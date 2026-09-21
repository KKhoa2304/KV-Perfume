<div class="admin-page">
    <div class="admin-page-header">
        <div>
            <p class="admin-eyebrow">KV PERFUME ADMIN</p>
            <h1>
                <?php echo ($mode ?? '') === 'create' ? 'Thêm sản phẩm' : 'Sửa sản phẩm'; ?>
            </h1>
            <p>Điền đầy đủ thông tin để sản phẩm hiển thị chính xác trên website.</p>
        </div>
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
        <form method="post">
            <div class="admin-form-grid">

                <div class="form-group">
                    <label>Tên sản phẩm</label>
                    <input type="text" name="name"
                           value="<?php echo htmlspecialchars($product['name'] ?? ''); ?>"
                           required>
                </div>

                <div class="form-group">
                    <label>Slug</label>
                    <input type="text" name="slug"
                           value="<?php echo htmlspecialchars($product['slug'] ?? ''); ?>"
                           placeholder="VD: gucci-flora-gift-set">
                </div>

                <div class="form-group">
                    <label>Thương hiệu</label>
                    <select name="brand_id" required>
                        <option value="">-- Chọn thương hiệu --</option>
                        <?php foreach (($brands ?? []) as $brand): ?>
                            <option value="<?php echo (int)$brand['id']; ?>"
                                <?php echo ((int)($product['brand_id'] ?? 0) === (int)$brand['id']) ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($brand['name'] ?? ''); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="form-group">
                    <label>Danh mục</label>
                    <select name="category_id" required>
                        <option value="">-- Chọn danh mục --</option>
                        <?php foreach (($categories ?? []) as $category): ?>
                            <option value="<?php echo (int)$category['id']; ?>"
                                <?php echo ((int)($product['category_id'] ?? 0) === (int)$category['id']) ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($category['name'] ?? ''); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="form-group">
                    <label>Giới tính</label>
                    <select name="gender">
                        <?php $gender = $product['gender'] ?? 'unisex'; ?>
                        <option value="female" <?php echo $gender === 'female' ? 'selected' : ''; ?>>Nữ</option>
                        <option value="male" <?php echo $gender === 'male' ? 'selected' : ''; ?>>Nam</option>
                        <option value="unisex" <?php echo $gender === 'unisex' ? 'selected' : ''; ?>>Unisex</option>
                    </select>
                </div>

                <div class="form-group">
                    <label>Nồng độ</label>
                    <input type="text" name="concentration"
                           value="<?php echo htmlspecialchars($product['concentration'] ?? ''); ?>"
                           placeholder="VD: EDP, EDT, Parfum">
                </div>

                <div class="form-group">
                    <label>Dung tích</label>
                    <input type="text" name="volume"
                           value="<?php echo htmlspecialchars($product['volume'] ?? ''); ?>"
                           placeholder="VD: 100ml">
                </div>

                <div class="form-group">
                    <label>Nhóm hương</label>
                    <input type="text" name="scent_group"
                           value="<?php echo htmlspecialchars($product['scent_group'] ?? ''); ?>"
                           placeholder="VD: Floral Fruity">
                </div>

                <div class="form-group">
                    <label>Giá</label>
                    <input type="number" name="price"
                           value="<?php echo htmlspecialchars($product['price'] ?? ''); ?>"
                           min="0"
                           required>
                </div>

                <div class="form-group">
                    <label>Giá khuyến mãi</label>
                    <input type="number" name="sale_price"
                           value="<?php echo htmlspecialchars($product['sale_price'] ?? ''); ?>"
                           min="0"
                           placeholder="Bỏ trống nếu không giảm giá">
                </div>

                <div class="form-group">
                    <label>Tồn kho</label>
                    <input type="number" name="stock"
                           value="<?php echo htmlspecialchars($product['stock'] ?? '0'); ?>"
                           min="0"
                           required>
                </div>

                <div class="form-group">
                    <label>Trạng thái</label>
                    <?php $status = (int)($product['status'] ?? 1); ?>
                    <select name="status">
                        <option value="1" <?php echo $status === 1 ? 'selected' : ''; ?>>Đang bán</option>
                        <option value="0" <?php echo $status === 0 ? 'selected' : ''; ?>>Ngưng bán</option>
                    </select>
                </div>

                <div class="form-group full-width">
                    <label>Link ảnh</label>
                    <input type="text" name="main_image"
                           value="<?php echo htmlspecialchars($product['main_image'] ?? ''); ?>"
                           placeholder="/CHANEL.VN-MAIN/public/upload/ten-anh.webp">
                </div>

                <div class="form-group full-width">
                    <label>Mô tả</label>
                    <textarea name="description"><?php echo htmlspecialchars($product['description'] ?? ''); ?></textarea>
                </div>

                <div class="form-group">
                    <label>Hương đầu</label>
                    <input type="text" name="top_note"
                           value="<?php echo htmlspecialchars($product['top_note'] ?? ''); ?>">
                </div>

                <div class="form-group">
                    <label>Hương giữa</label>
                    <input type="text" name="middle_note"
                           value="<?php echo htmlspecialchars($product['middle_note'] ?? ''); ?>">
                </div>

                <div class="form-group">
                    <label>Hương cuối</label>
                    <input type="text" name="base_note"
                           value="<?php echo htmlspecialchars($product['base_note'] ?? ''); ?>">
                </div>

                <div class="form-group">
                    <label>Độ lưu hương</label>
                    <input type="text" name="longevity"
                           value="<?php echo htmlspecialchars($product['longevity'] ?? ''); ?>">
                </div>

                <div class="form-group full-width">
                    <label>Câu chuyện mùi hương</label>
                    <textarea name="fragrance_story"><?php echo htmlspecialchars($product['fragrance_story'] ?? ''); ?></textarea>
                </div>

                <div class="form-group full-width">
                    <label>Dịp sử dụng</label>
                    <input type="text" name="occasion"
                           value="<?php echo htmlspecialchars($product['occasion'] ?? ''); ?>">
                </div>

            </div>

            <div class="admin-form-actions">
                <button type="submit" class="admin-btn primary">
                    Lưu sản phẩm
                </button>

                <a href="?url=admin/product/index" class="admin-btn">
                    Quay lại
                </a>
            </div>
        </form>
    </div>
</div>