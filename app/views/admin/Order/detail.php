<div class="admin-page">
    <div class="admin-page-header">
        <div>
            <p class="admin-eyebrow">KV PERFUME ADMIN</p>
            <h1>Chi tiết đơn hàng #<?php echo htmlspecialchars($order['order_code'] ?? ''); ?></h1>
            <p>Chỉ được cập nhật trạng thái theo đúng quy trình, không được nhảy bước.</p>
        </div>

        <a class="admin-btn" href="?url=admin/order/index">← Quay lại</a>
    </div>

    <?php if (!empty($message)): ?>
        <div class="admin-alert success"><?php echo htmlspecialchars($message); ?></div>
    <?php endif; ?>

    <?php if (!empty($error)): ?>
        <div class="admin-alert error"><?php echo htmlspecialchars($error); ?></div>
    <?php endif; ?>

    <?php
        $statusText = [
            'pending' => 'Chờ xác nhận',
            'confirmed' => 'Đã xác nhận',
            'shipping' => 'Đang giao',
            'delivered' => 'Đã giao',
            'completed' => 'Hoàn tất',
            'cancelled' => 'Đã hủy'
        ];

        $statusClass = [
            'pending' => 'warning',
            'confirmed' => 'active',
            'shipping' => 'active',
            'delivered' => 'active',
            'completed' => 'success',
            'cancelled' => 'danger'
        ];

        $nextStatus = [
            'pending' => 'confirmed',
            'confirmed' => 'shipping',
            'shipping' => 'delivered',
            'delivered' => 'completed'
        ];

        $nextLabel = [
            'confirmed' => 'Xác nhận đơn',
            'shipping' => 'Chuyển sang đang giao',
            'delivered' => 'Xác nhận đã giao',
            'completed' => 'Hoàn tất đơn hàng'
        ];

        $currentStatus = $order['status'] ?? 'pending';
        $canCancel = in_array($currentStatus, ['pending', 'confirmed']);
        $canNext = isset($nextStatus[$currentStatus]);
    ?>

    <div class="admin-detail-grid">
        <div class="admin-card">
            <h2>Thông tin đơn hàng</h2>

            <div class="admin-info-list">
                <p><strong>Mã đơn:</strong> <?php echo htmlspecialchars($order['order_code'] ?? ''); ?></p>
                <p><strong>Trạng thái:</strong>
                    <span class="admin-badge <?php echo $statusClass[$currentStatus] ?? 'hidden'; ?>">
                        <?php echo $statusText[$currentStatus] ?? htmlspecialchars($currentStatus); ?>
                    </span>
                </p>
                <p><strong>Ngày tạo:</strong> <?php echo date('d/m/Y H:i', strtotime($order['created_at'])); ?></p>
                <p><strong>Phương thức thanh toán:</strong>
                    <?php echo (($order['payment_method'] ?? '') === 'bank_transfer') ? 'Chuyển khoản' : 'COD'; ?>
                </p>
                <p><strong>Trạng thái thanh toán:</strong> <?php echo htmlspecialchars($order['payment_status'] ?? ''); ?></p>
            </div>
        </div>

        <div class="admin-card">
            <h2>Thông tin người nhận</h2>

            <div class="admin-info-list">
                <p><strong>Họ tên:</strong> <?php echo htmlspecialchars($order['receiver_name'] ?? ''); ?></p>
                <p><strong>Số điện thoại:</strong> <?php echo htmlspecialchars($order['receiver_phone'] ?? ''); ?></p>
                <p><strong>Email:</strong> <?php echo htmlspecialchars($order['receiver_email'] ?? ''); ?></p>
                <p><strong>Địa chỉ:</strong> <?php echo htmlspecialchars($order['receiver_address'] ?? ''); ?></p>
                <p><strong>Ghi chú:</strong> <?php echo htmlspecialchars($order['note'] ?? 'Không có'); ?></p>
            </div>
        </div>
    </div>

    <div class="admin-card">
        <h2>Sản phẩm trong đơn</h2>

        <div class="admin-table-wrap">
            <table class="admin-table">
                <thead>
                    <tr>
                        <th>Sản phẩm</th>
                        <th>Ảnh</th>
                        <th>Đơn giá</th>
                        <th>Số lượng</th>
                        <th>Tạm tính</th>
                    </tr>
                </thead>

                <tbody>
                    <?php foreach ($items as $item): ?>
                        <tr>
                            <td><strong><?php echo htmlspecialchars($item['product_name'] ?? ''); ?></strong></td>
                            <td>
                                <?php if (!empty($item['product_image'])): ?>
                                    <img class="admin-product-img"
                                         src="<?php echo htmlspecialchars($item['product_image']); ?>"
                                         alt="<?php echo htmlspecialchars($item['product_name']); ?>">
                                <?php endif; ?>
                            </td>
                            <td><?php echo number_format((float)$item['price'], 0, ',', '.'); ?> VNĐ</td>
                            <td><?php echo (int)$item['quantity']; ?></td>
                            <td><strong><?php echo number_format((float)$item['subtotal'], 0, ',', '.'); ?> VNĐ</strong></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>

        <div class="admin-order-total">
            <p>Tổng tiền hàng: <strong><?php echo number_format((float)$order['total_amount'], 0, ',', '.'); ?> VNĐ</strong></p>
            <p>Giảm giá: <strong><?php echo number_format((float)$order['discount_amount'], 0, ',', '.'); ?> VNĐ</strong></p>
            <p>Phí vận chuyển: <strong><?php echo number_format((float)$order['shipping_fee'], 0, ',', '.'); ?> VNĐ</strong></p>
            <h3>Thành tiền: <?php echo number_format((float)$order['final_amount'], 0, ',', '.'); ?> VNĐ</h3>
        </div>
    </div>

    <div class="admin-detail-grid">
        <div class="admin-card">
            <h2>Cập nhật trạng thái</h2>

            <?php if ($canNext): ?>
                <form method="post" action="?url=admin/order/updateStatus" class="admin-form">
                    <input type="hidden" name="order_id" value="<?php echo (int)$order['id']; ?>">
                    <input type="hidden" name="status" value="<?php echo htmlspecialchars($nextStatus[$currentStatus]); ?>">

                    <div class="form-group">
                        <label>Ghi chú xử lý</label>
                        <textarea name="note" placeholder="VD: Admin xác nhận đơn hàng..."></textarea>
                    </div>

                    <button type="submit" class="admin-btn primary"
                            onclick="return confirm('Cập nhật trạng thái đơn hàng sang bước tiếp theo?');">
                        <?php echo $nextLabel[$nextStatus[$currentStatus]]; ?>
                    </button>
                </form>
            <?php else: ?>
                <div class="admin-alert">
                    Đơn hàng đang ở trạng thái cuối, không thể cập nhật tiếp.
                </div>
            <?php endif; ?>

            <?php if ($canCancel): ?>
                <hr>

                <form method="post" action="?url=admin/order/cancel" class="admin-form">
                    <input type="hidden" name="order_id" value="<?php echo (int)$order['id']; ?>">

                    <div class="form-group">
                        <label>Lý do hủy đơn</label>
                        <textarea name="cancel_reason" required placeholder="Nhập lý do hủy đơn..."></textarea>
                    </div>

                    <button type="submit" class="admin-btn danger"
                            onclick="return confirm('Hủy đơn hàng này? Thao tác này không thể chuyển lại trạng thái trước đó.');">
                        Hủy đơn hàng
                    </button>
                </form>
            <?php endif; ?>
        </div>

        <div class="admin-card">
            <h2>Lịch sử trạng thái</h2>

            <div class="admin-timeline">
                <?php if (empty($logs)): ?>
                    <p class="admin-muted">Chưa có lịch sử trạng thái.</p>
                <?php endif; ?>

                <?php foreach ($logs as $log): ?>
                    <div class="admin-timeline-item">
                        <strong>
                            <?php echo $statusText[$log['status']] ?? htmlspecialchars($log['status']); ?>
                        </strong>
                        <span><?php echo date('d/m/Y H:i', strtotime($log['created_at'])); ?></span>
                        <p><?php echo htmlspecialchars($log['note'] ?? ''); ?></p>
                        <small>
                            Người cập nhật:
                            <?php echo htmlspecialchars($log['full_name'] ?? 'Hệ thống'); ?>
                        </small>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
</div>