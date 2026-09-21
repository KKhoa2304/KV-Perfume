<?php

require_once __DIR__ . '/XLData.php';

class OrderModel extends XlData
{
    private array $statusFlow = [
        'pending'   => ['confirmed', 'cancelled'],
        'confirmed' => ['shipping', 'cancelled'],
        'shipping'  => ['delivered', 'cancelled'],
        'delivered' => ['completed'],
        'completed' => [],
        'cancelled' => []
    ];

    public function saveOrder($userId, $paymentMethod, $cartItems, $totalAmount, $voucher = null)
    {
        if (empty($cartItems)) {
            return false;
        }

        try {
            $this->db->beginTransaction();

            $voucherId = $voucher['id'] ?? null;
            $discountAmount = (float)($voucher['discount_amount'] ?? 0);
            $shippingFee = 0;
            $finalAmount = max(0, $totalAmount - $discountAmount + $shippingFee);
            $orderCode = 'KV' . date('YmdHis') . rand(100, 999);

            $receiverName    = $_POST['receiver_name'] ?? $_POST['fullname'] ?? $_SESSION['user']['name'] ?? 'Khách hàng';
            $receiverPhone   = $_POST['receiver_phone'] ?? $_POST['phone'] ?? $_SESSION['user']['phone'] ?? '';
            $receiverEmail   = $_SESSION['user']['email'] ?? '';
            $receiverAddress = $_POST['receiver_address'] ?? $_POST['address'] ?? '';
            $note            = $_POST['note'] ?? '';

            $paymentMethod = $paymentMethod === 'bank' ? 'bank_transfer' : $paymentMethod;
            if (!in_array($paymentMethod, ['cod', 'bank_transfer'], true)) {
                $paymentMethod = 'cod';
            }

            foreach ($cartItems as $item) {
                $productId = (int)($item['id'] ?? $item['product_id'] ?? 0);
                $quantity  = (int)($item['quantity'] ?? 1);

                if ($productId <= 0 || $quantity <= 0) {
                    throw new Exception("Số lượng sản phẩm không hợp lệ");
                }

                $stockStmt = $this->db->prepare(
                    "SELECT id, name, stock FROM products WHERE id = :id FOR UPDATE"
                );
                $stockStmt->execute(['id' => $productId]);
                $product = $stockStmt->fetch(PDO::FETCH_ASSOC);

                if (!$product) {
                    throw new Exception("Sản phẩm không tồn tại");
                }

                if ($quantity > (int)$product['stock']) {
                    throw new Exception(
                        "Sản phẩm " . $product['name'] . " chỉ còn " . $product['stock'] . " sản phẩm trong kho"
                    );
                }
            }

            $orderSql = "INSERT INTO orders (
                            user_id, voucher_id, order_code, receiver_name, receiver_phone,
                            receiver_email, receiver_address, note, total_amount, discount_amount,
                            shipping_fee, final_amount, payment_method, payment_status, status, created_at
                        ) VALUES (
                            :user_id, :voucher_id, :order_code, :receiver_name, :receiver_phone,
                            :receiver_email, :receiver_address, :note, :total_amount, :discount_amount,
                            :shipping_fee, :final_amount, :payment_method, :payment_status, :status, NOW()
                        )";

            $orderStmt = $this->db->prepare($orderSql);
            $orderStmt->execute([
                'user_id'          => (int)$userId,
                'voucher_id'       => $voucherId,
                'order_code'       => $orderCode,
                'receiver_name'    => $receiverName,
                'receiver_phone'   => $receiverPhone,
                'receiver_email'   => $receiverEmail,
                'receiver_address' => $receiverAddress,
                'note'             => $note,
                'total_amount'     => $totalAmount,
                'discount_amount'  => $discountAmount,
                'shipping_fee'     => $shippingFee,
                'final_amount'     => $finalAmount,
                'payment_method'   => $paymentMethod,
                'payment_status'   => 'unpaid',
                'status'           => 'pending'
            ]);

            $orderId = (int)$this->db->lastInsertId();

            foreach ($cartItems as $item) {
                $productId    = (int)($item['id'] ?? $item['product_id'] ?? 0);
                $quantity     = (int)($item['quantity'] ?? 1);
                $price        = (float)($item['price'] ?? 0);
                $productName  = $item['name'] ?? '';
                $productImage = $item['image'] ?? '';
                $subtotal     = $price * $quantity;

                $itemSql = "INSERT INTO order_items (
                                order_id, product_id, product_name, product_image, price, quantity, subtotal
                            ) VALUES (
                                :order_id, :product_id, :product_name, :product_image, :price, :quantity, :subtotal
                            )";

                $itemStmt = $this->db->prepare($itemSql);
                $itemStmt->execute([
                    'order_id'      => $orderId,
                    'product_id'    => $productId,
                    'product_name'  => $productName,
                    'product_image' => $productImage,
                    'price'         => $price,
                    'quantity'      => $quantity,
                    'subtotal'      => $subtotal
                ]);

                $productSql = "UPDATE products
                               SET stock = stock - :stock_quantity,
                                   sold_quantity = sold_quantity + :sold_quantity
                               WHERE id = :product_id
                               AND stock >= :check_quantity";

                $productStmt = $this->db->prepare($productSql);
                $productStmt->execute([
                    'stock_quantity' => $quantity,
                    'sold_quantity'  => $quantity,
                    'product_id'     => $productId,
                    'check_quantity' => $quantity
                ]);

                if ($productStmt->rowCount() === 0) {
                    throw new Exception(
                        "Sản phẩm " . $productName . " không đủ số lượng trong kho"
                    );
                }
            }

            $paymentSql = "INSERT INTO payments (
                               order_id, amount, method, status, created_at
                           ) VALUES (
                               :order_id, :amount, :method, :status, NOW()
                           )";

            $paymentStmt = $this->db->prepare($paymentSql);
            $paymentStmt->execute([
                'order_id' => $orderId,
                'amount'   => $finalAmount,
                'method'   => $paymentMethod,
                'status'   => 'unpaid'
            ]);

            $this->createOrderLog($orderId, 'pending', 'Khách hàng tạo đơn hàng', (int)$userId);

            if ($voucherId !== null) {
                $voucherSql = "UPDATE vouchers
                               SET used_quantity = used_quantity + 1
                               WHERE id = :id";
                $voucherStmt = $this->db->prepare($voucherSql);
                $voucherStmt->execute(['id' => $voucherId]);
            }

            $this->db->commit();
            return $orderId;

        } catch (Exception $e) {
            if ($this->db->inTransaction()) {
                $this->db->rollBack();
            }

            $_SESSION['checkout_error'] = $e->getMessage();
            return false;
        }
    }

    public function getOrdersByUser($userId)
    {
        return $this->readItem(
            "SELECT * FROM orders WHERE user_id = :user_id ORDER BY id DESC",
            ['user_id' => (int)$userId]
        );
    }

    public function getOrderItems($orderId)
    {
        return $this->readItem(
            "SELECT * FROM order_items WHERE order_id = :order_id ORDER BY id ASC",
            ['order_id' => (int)$orderId]
        );
    }

    public function getAllOrders($status = 'all')
    {
        $sql = "SELECT o.*, u.full_name, u.email
                FROM orders o
                LEFT JOIN users u ON o.user_id = u.id";

        $params = [];

        if ($status !== 'all') {
            $sql .= " WHERE o.status = :status";
            $params['status'] = $status;
        }

        $sql .= " ORDER BY o.id DESC";
        return $this->readItem($sql, $params);
    }

    public function getOrderById($orderId)
    {
        return $this->readOne(
            "SELECT o.*, u.full_name, u.email
             FROM orders o
             LEFT JOIN users u ON o.user_id = u.id
             WHERE o.id = :id",
            ['id' => (int)$orderId]
        );
    }

    public function getOrderLogs($orderId)
    {
        return $this->readItem(
            "SELECT l.*, u.full_name
             FROM order_status_logs l
             LEFT JOIN users u ON l.changed_by = u.id
             WHERE l.order_id = :order_id
             ORDER BY l.id DESC",
            ['order_id' => (int)$orderId]
        );
    }

    public function updateStatus($orderId, $newStatus, $changedBy = null)
    {
        $result = $this->updateOrderStatus(
            $orderId,
            $newStatus,
            $changedBy,
            'Admin cập nhật trạng thái đơn hàng'
        );

        return $result['success'] ?? false;
    }

    public function updateOrderStatus($orderId, $newStatus, $changedBy = null, $note = '')
    {
        $order = $this->getOrderById($orderId);

        if (!$order) {
            return ['success' => false, 'code' => 'not_found'];
        }

        $currentStatus = $order['status'];

        if (!isset($this->statusFlow[$currentStatus])) {
            return ['success' => false, 'code' => 'invalid_status'];
        }

        if (!in_array($newStatus, $this->statusFlow[$currentStatus], true)) {
            return ['success' => false, 'code' => 'invalid_flow'];
        }

        $updated = $this->executeItem(
            "UPDATE orders
             SET status = :status, updated_at = NOW()
             WHERE id = :id",
            [
                'status' => $newStatus,
                'id'     => (int)$orderId
            ]
        );

        if (!$updated) {
            return ['success' => false, 'code' => 'update_failed'];
        }

        $this->createOrderLog(
            $orderId,
            $newStatus,
            $note ?: 'Hệ thống cập nhật trạng thái',
            $changedBy
        );

        return ['success' => true];
    }

    public function cancelOrder($orderId, $changedBy = null, $reason = '', $isAdmin = false)
    {
        $order = $this->getOrderById($orderId);

        if (!$order) {
            return ['success' => false, 'code' => 'not_found'];
        }

        $currentStatus = trim(strtolower($order['status'] ?? ''));

        $allowedStatuses = $isAdmin
            ? ['pending', 'confirmed', 'shipping']
            : ['pending', 'confirmed'];

        if (!in_array($currentStatus, $allowedStatuses, true)) {
            return ['success' => false, 'code' => 'invalid_flow'];
        }

        try {
            $this->db->beginTransaction();

            $stmt = $this->db->prepare(
                "UPDATE orders
                 SET status = 'cancelled',
                     cancel_reason = :reason,
                     updated_at = NOW()
                 WHERE id = :id"
            );

            $stmt->execute([
                'reason' => $reason ?: ($isAdmin ? 'Admin hủy đơn hàng' : 'Khách hàng chủ động hủy đơn'),
                'id'     => (int)$orderId
            ]);

            $items = $this->getOrderItems($orderId);

            foreach ($items as $item) {
                $quantity  = (int)($item['quantity'] ?? 0);
                $productId = (int)($item['product_id'] ?? 0);

                if ($quantity <= 0 || $productId <= 0) {
                    continue;
                }

                $stmtProduct = $this->db->prepare(
                    "UPDATE products
                     SET stock = stock + :stock_quantity,
                         sold_quantity = GREATEST(sold_quantity - :sold_quantity, 0)
                     WHERE id = :product_id"
                );

                $stmtProduct->execute([
                    'stock_quantity' => $quantity,
                    'sold_quantity'  => $quantity,
                    'product_id'     => $productId
                ]);
            }

            if (!empty($order['voucher_id'])) {
                $stmtVoucher = $this->db->prepare(
                    "UPDATE vouchers
                     SET used_quantity = GREATEST(used_quantity - 1, 0)
                     WHERE id = :voucher_id"
                );

                $stmtVoucher->execute([
                    'voucher_id' => (int)$order['voucher_id']
                ]);
            }

            $this->db->commit();

            try {
                $this->createOrderLog(
                    $orderId,
                    'cancelled',
                    $reason ?: ($isAdmin ? 'Admin hủy đơn hàng' : 'Khách hàng hủy đơn'),
                    $changedBy
                );
            } catch (Exception $e) {}

            return ['success' => true];

        } catch (Exception $e) {
            if ($this->db->inTransaction()) {
                $this->db->rollBack();
            }

            return [
                'success' => false,
                'code'    => 'system_error',
                'message' => $e->getMessage()
            ];
        }
    }

    private function createOrderLog($orderId, $status, $note = '', $changedBy = null)
    {
        return $this->executeItem(
            "INSERT INTO order_status_logs (
                order_id, status, note, changed_by, created_at
            ) VALUES (
                :order_id, :status, :note, :changed_by, NOW()
            )",
            [
                'order_id'   => (int)$orderId,
                'status'     => $status,
                'note'       => $note,
                'changed_by' => $changedBy
            ]
        );
    }

    public function deleteOrder($id)
    {
        $order = $this->getOrderById($id);

        if (!$order) {
            return false;
        }

        if ($order['status'] !== 'pending') {
            return [
                'success' => false,
                'message' => 'Đơn đang xử lý hoặc đang giao không thể xóa'
            ];
        }

        $items = $this->getOrderItems($id);

        foreach ($items as $item) {
            $this->executeItem(
                "UPDATE products
                 SET stock = stock + :qty,
                     sold_quantity = GREATEST(sold_quantity - :qty, 0)
                 WHERE id = :id",
                [
                    'qty' => (int)$item['quantity'],
                    'id'  => (int)$item['product_id']
                ]
            );
        }

        return $this->executeItem(
            "UPDATE orders SET deleted_at = NOW() WHERE id = :id",
            ['id' => (int)$id]
        );
    }
}
?>