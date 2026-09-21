<?php
$product = $product ?? null;
$reviews = $reviews ?? [];
$reviewSummary = $reviewSummary ?? [
    'total_reviews' => 0,
    'average_rating' => 0
];

if (!function_exists('kvDetailImageSet')) {
    function kvDetailImageSet(array $product): array
    {
        $image = $product['main_image'] ?? $product['image'] ?? $product['image_path'] ?? $product['image_url'] ?? '';
        $image = trim((string)$image);
        $image = str_replace('\\', '/', $image);

        if ($image === '') {
            return [
                'primary' => '/public/upload/products/default.webp',
                'fallback1' => '/public/upload/default.webp',
                'fallback2' => '/upload/default.webp'
            ];
        }

        if (preg_match('/^https?:\/\//i', $image)) {
            return [
                'primary' => $image,
                'fallback1' => $image,
                'fallback2' => $image
            ];
        }

        $filename = basename($image);

        return [
            'primary' => '/public/upload/' . $filename,
            'fallback1' => '/public/upload/products/' . $filename,
            'fallback2' => '/CHANEL.VN-MAIN/public/upload/' . $filename
        ];
    }
}
?>

<div class="product-detail-page" style="padding-top: 170px; padding-bottom: 90px;">
    <?php if (!$product): ?>
        <div style="max-width: 900px; margin: 0 auto; text-align: center; padding: 80px 20px;">
            <h2 style="font-family: var(--font-serif); letter-spacing: 2px; text-transform: uppercase;">
                Không tìm thấy sản phẩm
            </h2>
            <a href="?url=home" class="btn-outline" style="margin-top: 24px; display: inline-block; text-decoration: none;">
                Quay lại trang chủ
            </a>
        </div>
    <?php else: ?>
        <?php
            $id = (int)($product['id'] ?? 0);
            $name = $product['name'] ?? 'Sản phẩm';
            $imageSet = kvDetailImageSet($product);

            $brand = $product['brand_name'] ?? '';
            $category = $product['scent_group'] ?? $product['category_name'] ?? 'Luxury Fragrance';
            $concentration = $product['concentration'] ?? '';
            $volume = $product['volume'] ?? '';

            $price = !empty($product['sale_price']) ? $product['sale_price'] : ($product['price'] ?? 0);
            $oldPrice = !empty($product['sale_price']) ? ($product['price'] ?? 0) : 0;

            $description = !empty($product['description']) ? $product['description'] : 'Đang cập nhật mô tả sản phẩm.';
            $topNote = !empty($product['top_note']) ? $product['top_note'] : 'Đang cập nhật...';
            $middleNote = !empty($product['middle_note']) ? $product['middle_note'] : 'Đang cập nhật...';
            $baseNote = !empty($product['base_note']) ? $product['base_note'] : 'Đang cập nhật...';
            $fragranceStory = !empty($product['fragrance_story']) ? $product['fragrance_story'] : $description;
            $longevity = !empty($product['longevity']) ? $product['longevity'] : 'Đang cập nhật';
            $occasion = !empty($product['occasion']) ? $product['occasion'] : 'Đang cập nhật';

            $totalReviews = (int)($reviewSummary['total_reviews'] ?? 0);
            $averageRating = round((float)($reviewSummary['average_rating'] ?? 0), 1);
        ?>

        <div style="max-width: 1180px; margin: 0 auto; padding: 0 24px;">
            <div style="display: grid; grid-template-columns: minmax(0, 1fr) minmax(360px, 0.9fr); gap: 70px; align-items: center;">
                
                <div style="background: #f8f7f5; min-height: 560px; display: flex; align-items: center; justify-content: center; padding: 50px;">
                    <img 
                        src="<?= htmlspecialchars($imageSet['primary']); ?>"
                        alt="<?= htmlspecialchars($name); ?>"
                        style="max-width: 100%; max-height: 520px; object-fit: contain; display: block;"
                        onerror="
                            if (!this.dataset.try1) {
                                this.dataset.try1 = 1;
                                this.src='<?= htmlspecialchars($imageSet['fallback1']); ?>';
                            } else if (!this.dataset.try2) {
                                this.dataset.try2 = 1;
                                this.src='<?= htmlspecialchars($imageSet['fallback2']); ?>';
                            } else {
                                this.style.display='none';
                            }
                        "
                    >
                </div>

                <div>
                    <p style="font-size: 12px; color: #777; font-weight: 600; letter-spacing: 3px; text-transform: uppercase; margin-bottom: 18px;">
                        <?= htmlspecialchars($category); ?>
                    </p>

                    <h1 style="font-family: var(--font-serif); font-size: 42px; font-weight: 500; letter-spacing: 3px; text-transform: uppercase; line-height: 1.15; margin: 0 0 24px; padding-bottom: 22px; border-bottom: 1px solid #111;">
                        <?= htmlspecialchars($name); ?>
                    </h1>

                    <div style="display: flex; align-items: center; gap: 12px; margin-bottom: 22px;">
                        <div style="color: #b89b5e; letter-spacing: 2px; font-size: 16px;">
                            <?php
                                $roundedRating = (int)round($averageRating);
                                echo str_repeat('★', max(0, min(5, $roundedRating)));
                                echo str_repeat('☆', max(0, 5 - $roundedRating));
                            ?>
                        </div>
                        <span style="font-size: 13px; color: #777;">
                            <?= $averageRating; ?>/5 · <?= $totalReviews; ?> đánh giá
                        </span>
                    </div>

                    <div style="display: flex; flex-wrap: wrap; gap: 10px; margin-bottom: 24px;">
                        <?php if (!empty($brand)): ?>
                            <span style="border: 1px solid #dedbd6; padding: 8px 14px; font-size: 11px; letter-spacing: 1.5px; text-transform: uppercase;">
                                <?= htmlspecialchars($brand); ?>
                            </span>
                        <?php endif; ?>

                        <?php if (!empty($concentration)): ?>
                            <span style="border: 1px solid #dedbd6; padding: 8px 14px; font-size: 11px; letter-spacing: 1.5px; text-transform: uppercase;">
                                <?= htmlspecialchars($concentration); ?>
                            </span>
                        <?php endif; ?>

                        <?php if (!empty($volume)): ?>
                            <span style="border: 1px solid #dedbd6; padding: 8px 14px; font-size: 11px; letter-spacing: 1.5px; text-transform: uppercase;">
                                <?= htmlspecialchars($volume); ?>
                            </span>
                        <?php endif; ?>
                    </div>

                    <div style="margin-bottom: 30px;">
                        <p style="font-size: 22px; font-weight: 700; margin: 0; color: #111;">
                            <?= number_format((float)$price, 0, ',', '.'); ?> VNĐ
                        </p>

                        <?php if (!empty($oldPrice)): ?>
                            <p style="font-size: 14px; color: #999; text-decoration: line-through; margin-top: 6px;">
                                <?= number_format((float)$oldPrice, 0, ',', '.'); ?> VNĐ
                            </p>
                        <?php endif; ?>
                    </div>

                    <p style="font-size: 15px; line-height: 1.9; color: #555; margin-bottom: 34px;">
                        <?= htmlspecialchars($description); ?>
                    </p>

                    <div style="background: #faf9f7; border: 1px solid #eee9e2; padding: 24px; margin-bottom: 34px;">
                        <h3 style="font-family: var(--font-serif); font-size: 22px; letter-spacing: 2px; text-transform: uppercase; margin: 0 0 14px;">
                            Câu chuyện mùi hương
                        </h3>
                        <p style="font-size: 14px; line-height: 1.9; color: #555; margin: 0;">
                            <?= htmlspecialchars($fragranceStory); ?>
                        </p>
                    </div>

                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 14px; margin-bottom: 34px;">
                        <div style="border: 1px solid #eee9e2; padding: 18px;">
                            <span style="display: block; font-size: 11px; color: #777; font-weight: 700; letter-spacing: 2px; text-transform: uppercase; margin-bottom: 8px;">
                                Độ lưu hương
                            </span>
                            <strong style="font-size: 14px; color: #111;">
                                <?= htmlspecialchars($longevity); ?>
                            </strong>
                        </div>

                        <div style="border: 1px solid #eee9e2; padding: 18px;">
                            <span style="display: block; font-size: 11px; color: #777; font-weight: 700; letter-spacing: 2px; text-transform: uppercase; margin-bottom: 8px;">
                                Phù hợp
                            </span>
                            <strong style="font-size: 14px; color: #111;">
                                <?= htmlspecialchars($occasion); ?>
                            </strong>
                        </div>
                    </div>

                    <div style="margin-bottom: 42px;">
                        <div style="border-bottom: 1px solid #eee; padding: 16px 0;">
                            <span style="font-size: 11px; font-weight: 700; color: #777; letter-spacing: 2px; text-transform: uppercase;">
                                Hương đầu
                            </span>
                            <p style="margin-top: 8px; font-size: 14px; color: #333; line-height: 1.6;">
                                <?= htmlspecialchars($topNote); ?>
                            </p>
                        </div>

                        <div style="border-bottom: 1px solid #eee; padding: 16px 0;">
                            <span style="font-size: 11px; font-weight: 700; color: #777; letter-spacing: 2px; text-transform: uppercase;">
                                Hương giữa
                            </span>
                            <p style="margin-top: 8px; font-size: 14px; color: #333; line-height: 1.6;">
                                <?= htmlspecialchars($middleNote); ?>
                            </p>
                        </div>

                        <div style="border-bottom: 1px solid #eee; padding: 16px 0;">
                            <span style="font-size: 11px; font-weight: 700; color: #777; letter-spacing: 2px; text-transform: uppercase;">
                                Hương cuối
                            </span>
                            <p style="margin-top: 8px; font-size: 14px; color: #333; line-height: 1.6;">
                                <?= htmlspecialchars($baseNote); ?>
                            </p>
                        </div>
                    </div>

                    <a 
                        href="?url=cart/add&id=<?= $id; ?>" 
                        class="btn-primary" 
                        style="width: 100%; padding: 18px; font-weight: 700; letter-spacing: 2px; text-transform: uppercase; text-align: center; text-decoration: none; display: block;"
                    >
                        Thêm vào túi mua hàng
                    </a>
                </div>
            </div>

            <section style="margin-top: 80px; padding-top: 48px; border-top: 1px solid #eee9e2;">
                <div style="display: flex; justify-content: space-between; align-items: flex-end; gap: 30px; margin-bottom: 34px;">
                    <div>
                        <p style="font-size: 11px; letter-spacing: 4px; color: #9a7a3f; text-transform: uppercase; margin-bottom: 12px;">
                            KV PERFUME REVIEWS
                        </p>

                        <h2 style="font-family: var(--font-serif); font-size: 30px; letter-spacing: 4px; text-transform: uppercase; margin: 0;">
                            Đánh giá khách hàng
                        </h2>
                    </div>

                    <div style="text-align: right;">
                        <div style="font-size: 28px; font-weight: 600;">
                            <?= $averageRating; ?>/5
                        </div>

                        <div style="color: #777; font-size: 13px;">
                            <?= $totalReviews; ?> đánh giá
                        </div>
                    </div>
                </div>

                <?php if (empty($reviews)): ?>
                    <div style="padding: 34px; border: 1px dashed #dedbd6; color: #666; text-align: center;">
                        Sản phẩm này chưa có đánh giá nào.
                    </div>
                <?php else: ?>
                    <div style="display: flex; flex-direction: column; gap: 18px;">
                        <?php foreach ($reviews as $review): ?>
                            <?php
                                $customerName = $review['customer_name'] ?? 'Khách hàng';
                                $rating = (int)($review['rating'] ?? 5);
                                $rating = max(1, min(5, $rating));
                                $comment = $review['comment'] ?? '';
                                $createdAt = $review['created_at'] ?? '';
                            ?>

                            <div style="border: 1px solid #eee9e2; background: #fff; padding: 24px;">
                                <div style="display: flex; justify-content: space-between; align-items: flex-start; gap: 20px; margin-bottom: 12px;">
                                    <div>
                                        <strong style="display: block; font-size: 15px; letter-spacing: 1px; text-transform: uppercase;">
                                            <?= htmlspecialchars($customerName); ?>
                                        </strong>

                                        <span style="color: #888; font-size: 13px;">
                                            <?= !empty($createdAt) ? date('d/m/Y H:i', strtotime($createdAt)) : ''; ?>
                                        </span>
                                    </div>

                                    <div style="color: #b89b5e; letter-spacing: 2px;">
                                        <?= str_repeat('★', $rating); ?>
                                        <?= str_repeat('☆', 5 - $rating); ?>
                                    </div>
                                </div>

                                <p style="margin: 0; color: #555; line-height: 1.8;">
                                    <?= !empty($comment) ? nl2br(htmlspecialchars($comment)) : 'Khách hàng chưa để lại bình luận.'; ?>
                                </p>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </section>
        </div>
    <?php endif; ?>
</div>