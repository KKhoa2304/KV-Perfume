<div class="admin-page">
    <div class="admin-page-header">
        <div>
            <p class="admin-eyebrow">KV PERFUME ADMIN</p>
            <h1>Quản lý sản phẩm</h1>
            <p>
                Sản phẩm đã phát sinh đơn hàng, giỏ hàng hoặc đánh giá sẽ không được xóa cứng.
                Khi cần ngưng bán, admin dùng chức năng <strong>Ngưng bán</strong>.
            </p>
        </div>

        <a class="admin-btn primary" href="?url=admin/product/create">+ Thêm sản phẩm</a>
    </div>

    <?php if (!empty($message)): ?>
        <div class="admin-alert success"><?= htmlspecialchars($message); ?></div>
    <?php endif; ?>

    <?php if (!empty($error)): ?>
        <div class="admin-alert error"><?= htmlspecialchars($error); ?></div>
    <?php endif; ?>

    <div class="admin-toolbar">
        <a class="admin-filter <?= (($type ?? 'all') === 'all') ? 'active' : ''; ?>" href="?url=admin/product/index&type=all">Tất cả</a>
        <a class="admin-filter <?= (($type ?? '') === 'active') ? 'active' : ''; ?>" href="?url=admin/product/index&type=active">Đang bán</a>
        <a class="admin-filter <?= (($type ?? '') === 'hidden') ? 'active' : ''; ?>" href="?url=admin/product/index&type=hidden">Ngưng bán</a>
        <a class="admin-filter <?= (($type ?? '') === 'out_of_stock') ? 'active' : ''; ?>" href="?url=admin/product/index&type=out_of_stock">Hết hàng</a>
    </div>

    <div class="admin-card">
        <div class="admin-table-wrap">
            <table class="admin-table">
                <thead>
                    <tr>
                        <th>Ảnh</th>
                        <th>Sản phẩm</th>
                        <th>Thương hiệu</th>
                        <th>Danh mục</th>
                        <th>Nhóm hương</th>
                        <th>Giá</th>
                        <th>Tồn kho</th>
                        <th>Trạng thái</th>
                        <th class="text-right">Thao tác</th>
                    </tr>
                </thead>

                <tbody>
                    <?php if (empty($products)): ?>
                        <tr>
                            <td colspan="9" class="admin-empty">Không có sản phẩm nào phù hợp.</td>
                        </tr>
                    <?php endif; ?>

                    <?php foreach ($products as $product): ?>
                        <?php
                            $id = (int)($product['id'] ?? 0);
                            $name = $product['name'] ?? '';
                            $image = trim($product['main_image'] ?? '');
                            $status = (int)($product['status'] ?? 0);
                            $stock = (int)($product['stock'] ?? 0);
                            $price = (float)($product['price'] ?? 0);
                            $salePrice = $product['sale_price'] ?? null;

                            $filename = basename(str_replace('\\', '/', $image));

                            if ($filename !== '') {
                                $image1 = '/public/upload/' . $filename;
                                $image2 = '/CHANEL.VN-MAIN/public/upload/' . $filename;
                                $image3 = '/upload/' . $filename;
                            } else {
                                $image1 = $image2 = $image3 = '';
                            }
                        ?>

                        <tr>
                            <td>
                                <?php if (!empty($filename)): ?>
                                    <img class="admin-product-img"
                                         src="<?= htmlspecialchars($image1); ?>"
                                         alt="<?= htmlspecialchars($name); ?>"
                                         onerror="
                                            if (!this.dataset.try1) {
                                                this.dataset.try1 = 1;
                                                this.src='<?= htmlspecialchars($image2); ?>';
                                            } else if (!this.dataset.try2) {
                                                this.dataset.try2 = 1;
                                                this.src='<?= htmlspecialchars($image3); ?>';
                                            } else {
                                                this.style.display='none';
                                            }
                                         ">
                                <?php else: ?>
                                    <div class="admin-product-img placeholder">No image</div>
                                <?php endif; ?>
                            </td>

                            <td>
                                <strong><?= htmlspecialchars($name); ?></strong>
                                <span><?= htmlspecialchars($product['slug'] ?? ''); ?></span>
                            </td>

                            <td><?= htmlspecialchars($product['brand_name'] ?? ''); ?></td>
                            <td><?= htmlspecialchars($product['category_name'] ?? ''); ?></td>
                            <td><?= htmlspecialchars($product['scent_group'] ?? ''); ?></td>

                            <td>
                                <?php if (!empty($salePrice)): ?>
                                    <strong><?= number_format((float)$salePrice, 0, ',', '.'); ?> VNĐ</strong>
                                    <span class="admin-old-price"><?= number_format($price, 0, ',', '.'); ?> VNĐ</span>
                                <?php else: ?>
                                    <strong><?= number_format($price, 0, ',', '.'); ?> VNĐ</strong>
                                <?php endif; ?>
                            </td>

                            <td>
                                <?php if ($stock <= 0): ?>
                                    <span class="admin-badge danger">Hết hàng</span>
                                <?php elseif ($stock <= 5): ?>
                                    <span class="admin-badge warning"><?= $stock; ?> còn lại</span>
                                <?php else: ?>
                                    <?= $stock; ?>
                                <?php endif; ?>
                            </td>

                            <td>
                                <?php if ($status === 1): ?>
                                    <span class="admin-badge active">Đang bán</span>
                                <?php else: ?>
                                    <span class="admin-badge hidden">Ngưng bán</span>
                                <?php endif; ?>
                            </td>

                            <td class="text-right">
                                <a class="admin-btn small" href="?url=admin/product/edit/<?= $id; ?>">Sửa</a>

                                <?php if ($status === 1): ?>
                                    <form method="post" action="?url=admin/product/lock" class="inline-form">
                                        <input type="hidden" name="id" value="<?= $id; ?>">
                                        <button class="admin-btn small warning" type="submit">Ngưng bán</button>
                                    </form>
                                <?php else: ?>
                                    <form method="post" action="?url=admin/product/unlock" class="inline-form">
                                        <input type="hidden" name="id" value="<?= $id; ?>">
                                        <button class="admin-btn small success" type="submit">Mở bán</button>
                                    </form>
                                <?php endif; ?>

                                <form method="post" action="?url=admin/product/delete" class="inline-form">
                                    <input type="hidden" name="id" value="<?= $id; ?>">
                                    <button class="admin-btn small danger" type="submit">Xóa</button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>