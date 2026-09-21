<div class="admin-page">
    <div class="admin-page-header">
        <div>
            <p class="admin-eyebrow">KV PERFUME ADMIN</p>
            <h1>Quản lý mã giảm giá</h1>
            <p>Voucher chỉ được áp dụng khi còn hạn, còn lượt dùng và đơn hàng đạt giá trị tối thiểu.</p>
        </div>

        <a class="admin-btn primary" href="?url=admin/voucher/create">+ Thêm voucher</a>
    </div>

    <?php if (!empty($message)): ?>
        <div class="admin-alert success"><?php echo htmlspecialchars($message); ?></div>
    <?php endif; ?>

    <?php if (!empty($error)): ?>
        <div class="admin-alert error"><?php echo htmlspecialchars($error); ?></div>
    <?php endif; ?>

    <div class="admin-toolbar">
        <a class="admin-filter <?php echo (($status ?? 'all') === 'all') ? 'active' : ''; ?>" href="?url=admin/voucher/index&status=all">Tất cả</a>
        <a class="admin-filter <?php echo (($status ?? '') === 'active') ? 'active' : ''; ?>" href="?url=admin/voucher/index&status=active">Đang hiệu lực</a>
        <a class="admin-filter <?php echo (($status ?? '') === 'locked') ? 'active' : ''; ?>" href="?url=admin/voucher/index&status=locked">Đã khóa</a>
        <a class="admin-filter <?php echo (($status ?? '') === 'expired') ? 'active' : ''; ?>" href="?url=admin/voucher/index&status=expired">Hết hạn</a>
        <a class="admin-filter <?php echo (($status ?? '') === 'sold_out') ? 'active' : ''; ?>" href="?url=admin/voucher/index&status=sold_out">Hết lượt</a>
    </div>

    <div class="admin-card">
        <div class="admin-table-wrap">
            <table class="admin-table">
                <thead>
                    <tr>
                        <th>Mã</th>
                        <th>Tên voucher</th>
                        <th>Giảm giá</th>
                        <th>Đơn tối thiểu</th>
                        <th>Số lượng</th>
                        <th>Thời gian</th>
                        <th>Trạng thái</th>
                        <th class="text-right">Thao tác</th>
                    </tr>
                </thead>

                <tbody>
                    <?php if (empty($vouchers)): ?>
                        <tr>
                            <td colspan="8" class="admin-empty">Không có voucher nào phù hợp.</td>
                        </tr>
                    <?php endif; ?>

                    <?php foreach ($vouchers as $voucher): ?>
                        <?php
                            $id = (int)($voucher['id'] ?? 0);
                            $isActive = (int)($voucher['status'] ?? 0) === 1;
                            $isExpired = strtotime($voucher['end_date']) < time();
                            $isSoldOut = (int)$voucher['quantity'] <= (int)$voucher['used_quantity'];
                        ?>

                        <tr>
                            <td><strong><?php echo htmlspecialchars($voucher['code']); ?></strong></td>

                            <td><?php echo htmlspecialchars($voucher['name']); ?></td>

                            <td>
                                <?php if ($voucher['discount_type'] === 'percent'): ?>
                                    <?php echo number_format((float)$voucher['discount_value'], 0, ',', '.'); ?>%
                                    <?php if (!empty($voucher['max_discount'])): ?>
                                        <span>Tối đa <?php echo number_format((float)$voucher['max_discount'], 0, ',', '.'); ?> VNĐ</span>
                                    <?php endif; ?>
                                <?php else: ?>
                                    <?php echo number_format((float)$voucher['discount_value'], 0, ',', '.'); ?> VNĐ
                                <?php endif; ?>
                            </td>

                            <td><?php echo number_format((float)$voucher['min_order_value'], 0, ',', '.'); ?> VNĐ</td>

                            <td>
                                <?php echo (int)$voucher['used_quantity']; ?> / <?php echo (int)$voucher['quantity']; ?>
                            </td>

                            <td>
                                <span><?php echo date('d/m/Y H:i', strtotime($voucher['start_date'])); ?></span>
                                <span><?php echo date('d/m/Y H:i', strtotime($voucher['end_date'])); ?></span>
                            </td>

                            <td>
                                <?php if (!$isActive): ?>
                                    <span class="admin-badge hidden">Đã khóa</span>
                                <?php elseif ($isExpired): ?>
                                    <span class="admin-badge danger">Hết hạn</span>
                                <?php elseif ($isSoldOut): ?>
                                    <span class="admin-badge warning">Hết lượt</span>
                                <?php else: ?>
                                    <span class="admin-badge active">Đang hiệu lực</span>
                                <?php endif; ?>
                            </td>

                            <td class="text-right">
                                <a class="admin-btn small" href="?url=admin/voucher/edit/<?php echo $id; ?>">Sửa</a>

                                <?php if ($isActive): ?>
                                    <form method="post" action="?url=admin/voucher/lock" class="inline-form"
                                          onsubmit="return confirm('Khóa voucher này? Khách hàng sẽ không áp dụng được nữa.');">
                                        <input type="hidden" name="id" value="<?php echo $id; ?>">
                                        <button class="admin-btn small danger" type="submit">Khóa</button>
                                    </form>
                                <?php else: ?>
                                    <form method="post" action="?url=admin/voucher/unlock" class="inline-form"
                                          onsubmit="return confirm('Mở lại voucher này? Hệ thống sẽ kiểm tra hạn dùng và số lượng còn lại.');">
                                        <input type="hidden" name="id" value="<?php echo $id; ?>">
                                        <button class="admin-btn small success" type="submit">Mở lại</button>
                                    </form>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>