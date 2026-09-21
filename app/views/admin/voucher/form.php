<div class="admin-page">
    <div class="admin-page-header">
        <div>
            <p class="admin-eyebrow">KV PERFUME ADMIN</p>
            <h1><?php echo ($mode === 'create') ? 'Thêm voucher' : 'Chỉnh sửa voucher'; ?></h1>
            <p>Thiết lập mã giảm giá có ràng buộc rõ ràng về thời gian, số lượng và giá trị đơn hàng.</p>
        </div>

        <a class="admin-btn" href="?url=admin/voucher/index">← Quay lại</a>
    </div>

    <?php if (!empty($errors)): ?>
        <div class="admin-alert error">
            <ul>
                <?php foreach ($errors as $error): ?>
                    <li><?php echo htmlspecialchars($error); ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>

    <div class="admin-card">
        <form method="post" class="admin-form">
            <div class="admin-form-grid">
                <div class="form-group">
                    <label>Mã voucher</label>
                    <input type="text" name="code" required
                           placeholder="VD: WELCOME10"
                           value="<?php echo htmlspecialchars($voucher['code'] ?? ''); ?>">
                </div>

                <div class="form-group">
                    <label>Tên voucher</label>
                    <input type="text" name="name" required
                           placeholder="VD: Giảm 10% cho khách mới"
                           value="<?php echo htmlspecialchars($voucher['name'] ?? ''); ?>">
                </div>

                <div class="form-group">
                    <label>Loại giảm giá</label>
                    <select name="discount_type" required>
                        <option value="percent" <?php echo (($voucher['discount_type'] ?? '') === 'percent') ? 'selected' : ''; ?>>
                            Giảm theo phần trăm
                        </option>
                        <option value="fixed" <?php echo (($voucher['discount_type'] ?? '') === 'fixed') ? 'selected' : ''; ?>>
                            Giảm số tiền cố định
                        </option>
                    </select>
                </div>

                <div class="form-group">
                    <label>Giá trị giảm</label>
                    <input type="number" name="discount_value" min="1" required
                           value="<?php echo htmlspecialchars($voucher['discount_value'] ?? ''); ?>">
                </div>

                <div class="form-group">
                    <label>Giá trị đơn tối thiểu</label>
                    <input type="number" name="min_order_value" min="0"
                           value="<?php echo htmlspecialchars($voucher['min_order_value'] ?? 0); ?>">
                </div>

                <div class="form-group">
                    <label>Mức giảm tối đa</label>
                    <input type="number" name="max_discount" min="0"
                           placeholder="Áp dụng tốt cho voucher %"
                           value="<?php echo htmlspecialchars($voucher['max_discount'] ?? ''); ?>">
                </div>

                <div class="form-group">
                    <label>Số lượng phát hành</label>
                    <input type="number" name="quantity" min="0" required
                           value="<?php echo htmlspecialchars($voucher['quantity'] ?? 0); ?>">
                </div>

                <div class="form-group">
                    <label>Số lượng đã dùng</label>
                    <input type="number" name="used_quantity" min="0"
                           value="<?php echo htmlspecialchars($voucher['used_quantity'] ?? 0); ?>">
                </div>

                <div class="form-group">
                    <label>Ngày bắt đầu</label>
                    <input type="datetime-local" name="start_date" required
                           value="<?php echo !empty($voucher['start_date']) ? date('Y-m-d\TH:i', strtotime($voucher['start_date'])) : ''; ?>">
                </div>

                <div class="form-group">
                    <label>Ngày kết thúc</label>
                    <input type="datetime-local" name="end_date" required
                           value="<?php echo !empty($voucher['end_date']) ? date('Y-m-d\TH:i', strtotime($voucher['end_date'])) : ''; ?>">
                </div>

                <div class="form-group">
                    <label>Trạng thái</label>
                    <select name="status">
                        <option value="1" <?php echo (($voucher['status'] ?? 1) == 1) ? 'selected' : ''; ?>>
                            Kích hoạt
                        </option>
                        <option value="0" <?php echo (($voucher['status'] ?? 1) == 0) ? 'selected' : ''; ?>>
                            Khóa
                        </option>
                    </select>
                </div>
            </div>

            <div class="admin-form-actions">
                <button type="submit" class="admin-btn primary">
                    <?php echo ($mode === 'create') ? 'Thêm voucher' : 'Cập nhật voucher'; ?>
                </button>

                <a href="?url=admin/voucher/index" class="admin-btn">Hủy</a>
            </div>
        </form>
    </div>
</div>