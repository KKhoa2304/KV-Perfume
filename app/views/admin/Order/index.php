<div class="admin-page">
    <div class="admin-page-header">
        <div>
            <p class="admin-eyebrow">KV PERFUME ADMIN</p>
            <h1>Quản lý đơn hàng</h1>
            <p>Quản lý trạng thái đơn hàng theo đúng quy trình xử lý.</p>
        </div>
    </div>

    <?php if (!empty($message)): ?>
        <div class="admin-alert success"><?php echo htmlspecialchars($message); ?></div>
    <?php endif; ?>

    <?php if (!empty($error)): ?>
        <div class="admin-alert error"><?php echo htmlspecialchars($error); ?></div>
    <?php endif; ?>

    <div class="admin-toolbar">
        <?php
        $filters = [
            'all' => 'Tất cả',
            'pending' => 'Chờ xác nhận',
            'confirmed' => 'Đã xác nhận',
            'shipping' => 'Đang giao',
            'delivered' => 'Đã giao',
            'completed' => 'Hoàn tất',
            'cancelled' => 'Đã hủy'
        ];
        ?>

        <?php foreach ($filters as $key => $label): ?>
            <a class="admin-filter <?php echo (($status ?? 'all') === $key) ? 'active' : ''; ?>"
               href="?url=admin/order/index&status=<?php echo $key; ?>">
                <?php echo $label; ?>
            </a>
        <?php endforeach; ?>
    </div>

    <div class="admin-card">
        <div class="admin-table-wrap">
            <table class="admin-table">
                <thead>
                    <tr>
                        <th>Mã đơn</th>
                        <th>Khách hàng</th>
                        <th>SĐT</th>
                        <th>Tổng tiền</th>
                        <th>Thanh toán</th>
                        <th>Trạng thái</th>
                        <th>Ngày tạo</th>
                        <th class="text-right">Thao tác</th>
                    </tr>
                </thead>

                <tbody>
                    <?php if (empty($orders)): ?>
                        <tr>
                            <td colspan="8" class="admin-empty">Chưa có đơn hàng nào.</td>
                        </tr>
                    <?php endif; ?>

                    <?php foreach ($orders as $order): ?>
                        <tr>
                            <td><strong><?php echo htmlspecialchars($order['order_code'] ?? ''); ?></strong></td>

                            <td>
                                <strong><?php echo htmlspecialchars($order['receiver_name'] ?? $order['full_name'] ?? ''); ?></strong>
                                <span><?php echo htmlspecialchars($order['receiver_email'] ?? $order['email'] ?? ''); ?></span>
                            </td>

                            <td><?php echo htmlspecialchars($order['receiver_phone'] ?? ''); ?></td>

                            <td>
                                <strong><?php echo number_format((float)($order['final_amount'] ?? 0), 0, ',', '.'); ?> VNĐ</strong>
                            </td>

                            <td>
                                <?php echo htmlspecialchars($order['payment_method'] ?? 'cod'); ?>
                                <span><?php echo htmlspecialchars($order['payment_status'] ?? 'unpaid'); ?></span>
                            </td>

                            <td>
                                <span class="admin-badge active">
                                    <?php echo htmlspecialchars($order['status'] ?? 'pending'); ?>
                                </span>
                            </td>

                            <td>
                                <?php echo !empty($order['created_at']) ? date('d/m/Y H:i', strtotime($order['created_at'])) : ''; ?>
                            </td>

                            <td class="text-right">
                                <a class="admin-btn small primary"
                                   href="?url=admin/order/detail/<?php echo (int)$order['id']; ?>">
                                    Chi tiết
                                </a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>