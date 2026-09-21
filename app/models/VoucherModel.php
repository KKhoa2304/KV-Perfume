<?php

require_once __DIR__ . '/XLData.php';

class VoucherModel extends XLData
{
    public function getValidVoucher($code, $orderTotal)
    {
        $voucher = $this->readOne(
            "
            SELECT *
            FROM vouchers
            WHERE code = :code
              AND status = 1
              AND quantity > used_quantity
              AND start_date <= NOW()
              AND end_date >= NOW()
              AND min_order_value <= :total
            ",
            [
                'code' => strtoupper(trim($code)),
                'total' => $orderTotal
            ]
        );

        if (!$voucher) {
            return null;
        }

        if ($voucher['discount_type'] === 'percent') {
            $discount = $orderTotal * ((float)$voucher['discount_value'] / 100);
        } else {
            $discount = (float)$voucher['discount_value'];
        }

        if (!empty($voucher['max_discount'])) {
            $discount = min($discount, (float)$voucher['max_discount']);
        }

        $voucher['discount_amount'] = min($discount, $orderTotal);

        return $voucher;
    }

    public function getAllVouchers($status = 'all')
    {
        $sql = "SELECT * FROM vouchers WHERE 1 = 1";
        $params = [];

        if ($status === 'active') {
            $sql .= " AND status = 1 AND start_date <= NOW() AND end_date >= NOW() AND quantity > used_quantity";
        } elseif ($status === 'locked') {
            $sql .= " AND status = 0";
        } elseif ($status === 'expired') {
            $sql .= " AND end_date < NOW()";
        } elseif ($status === 'sold_out') {
            $sql .= " AND quantity <= used_quantity";
        }

        $sql .= " ORDER BY id DESC";

        return $this->readItem($sql, $params);
    }

    public function getAll()
    {
        return $this->getAllVouchers('all');
    }

    public function getVoucherById($id)
    {
        return $this->readOne(
            "SELECT * FROM vouchers WHERE id = :id",
            ['id' => (int)$id]
        );
    }

    public function getById($id)
    {
        return $this->getVoucherById($id);
    }

    public function createVoucher($data)
    {
        $data = $this->normalizeVoucherData($data);

        return $this->executeItem(
            "
            INSERT INTO vouchers (
                code, name, discount_type, discount_value,
                min_order_value, max_discount, quantity,
                used_quantity, start_date, end_date, status
            ) VALUES (
                :code, :name, :discount_type, :discount_value,
                :min_order_value, :max_discount, :quantity,
                :used_quantity, :start_date, :end_date, :status
            )
            ",
            $data
        );
    }

    public function updateVoucher($id, $data)
    {
        $data = $this->normalizeVoucherData($data);
        $data['id'] = (int)$id;

        return $this->executeItem(
            "
            UPDATE vouchers SET
                code = :code,
                name = :name,
                discount_type = :discount_type,
                discount_value = :discount_value,
                min_order_value = :min_order_value,
                max_discount = :max_discount,
                quantity = :quantity,
                used_quantity = :used_quantity,
                start_date = :start_date,
                end_date = :end_date,
                status = :status
            WHERE id = :id
            ",
            $data
        );
    }

    public function lockVoucher($id)
    {
        return $this->executeItem(
            "UPDATE vouchers SET status = 0 WHERE id = :id",
            ['id' => (int)$id]
        );
    }

    public function unlockVoucher($id)
    {
        $voucher = $this->getVoucherById($id);

        if (!$voucher) {
            return ['success' => false, 'code' => 'not_found'];
        }

        if (strtotime($voucher['end_date']) < time()) {
            return ['success' => false, 'code' => 'expired'];
        }

        if ((int)$voucher['quantity'] <= (int)$voucher['used_quantity']) {
            return ['success' => false, 'code' => 'quantity_invalid'];
        }

        $updated = $this->executeItem(
            "UPDATE vouchers SET status = 1 WHERE id = :id",
            ['id' => (int)$id]
        );

        return [
            'success' => (bool)$updated,
            'code' => $updated ? 'ok' : 'update_failed'
        ];
    }

    private function normalizeVoucherData($data)
    {
        $code = strtoupper(trim($data['code'] ?? ''));
        $name = trim($data['name'] ?? '');
        $discountType = trim($data['discount_type'] ?? 'percent');
        $discountValue = (float)($data['discount_value'] ?? 0);
        $minOrderValue = (float)($data['min_order_value'] ?? 0);
        $maxDiscount = ($data['max_discount'] ?? '') !== '' ? (float)$data['max_discount'] : null;
        $quantity = (int)($data['quantity'] ?? 0);
        $usedQuantity = (int)($data['used_quantity'] ?? 0);
        $startDate = trim($data['start_date'] ?? '');
        $endDate = trim($data['end_date'] ?? '');
        $status = (int)($data['status'] ?? 1);

        if ($code === '') {
            throw new Exception('Mã voucher không được để trống.');
        }

        if ($name === '') {
            throw new Exception('Tên voucher không được để trống.');
        }

        if (!in_array($discountType, ['percent', 'fixed'], true)) {
            throw new Exception('Loại giảm giá không hợp lệ.');
        }

        if ($discountValue <= 0) {
            throw new Exception('Giá trị giảm phải lớn hơn 0.');
        }

        if ($discountType === 'percent' && $discountValue > 100) {
            throw new Exception('Voucher phần trăm không được giảm quá 100%.');
        }

        if ($minOrderValue < 0) {
            throw new Exception('Giá trị đơn tối thiểu không được âm.');
        }

        if ($maxDiscount !== null && $maxDiscount < 0) {
            throw new Exception('Mức giảm tối đa không được âm.');
        }

        if ($quantity < 0) {
            throw new Exception('Số lượng voucher không được âm.');
        }

        if ($usedQuantity < 0) {
            throw new Exception('Số lượng đã dùng không được âm.');
        }

        if ($quantity < $usedQuantity) {
            throw new Exception('Số lượng voucher không được nhỏ hơn số lượng đã dùng.');
        }

        if ($startDate === '' || $endDate === '') {
            throw new Exception('Vui lòng nhập thời gian bắt đầu và kết thúc.');
        }

        if (strtotime($startDate) >= strtotime($endDate)) {
            throw new Exception('Ngày bắt đầu phải nhỏ hơn ngày kết thúc.');
        }

        return [
            'code' => $code,
            'name' => $name,
            'discount_type' => $discountType,
            'discount_value' => $discountValue,
            'min_order_value' => $minOrderValue,
            'max_discount' => $maxDiscount,
            'quantity' => $quantity,
            'used_quantity' => $usedQuantity,
            'start_date' => date('Y-m-d H:i:s', strtotime($startDate)),
            'end_date' => date('Y-m-d H:i:s', strtotime($endDate)),
            'status' => $status === 1 ? 1 : 0
        ];
    }
}