<?php
// 1. KHỞI TẠO VÀ KIỂM TRA DỮ LIỆU ĐẦU VÀO
$user = $user ?? [];
$orders = $orders ?? [];
$coupons = $coupons ?? [];
$reviewableItems = $reviewableItems ?? [];
$myReviews = $myReviews ?? [];

// 2. GÁN BIẾN THÔNG TIN NGƯỜI DÙNG
$userName = $user['full_name'] ?? $user['name'] ?? 'Khách hàng';
$userEmail = $user['email'] ?? '';
$userPhone = $user['phone'] ?? '';
$userRank = $user['rank_level'] ?? 'silver';
$userCreatedAt = $user['created_at'] ?? '';
$userGender = $user['gender'] ?? '';
$userBirthday = $user['birthday'] ?? '';
$userAddress = $user['address'] ?? '';
$userCity = $user['city'] ?? '';

$genderLabel = [
    'male' => 'Nam',
    'female' => 'Nữ',
    'other' => 'Khác'
][$userGender] ?? 'Chưa cập nhật';

// 3. CẤU HÌNH HIỂN THỊ TRẠNG THÁI ĐƠN HÀNG
$statusLabels = [
    'pending' => 'Chờ xác nhận',
    'confirmed' => 'Đã xác nhận',
    'shipping' => 'Đang giao',
    'delivered' => 'Đã giao',
    'completed' => 'Hoàn tất',
    'cancelled' => 'Đã hủy'
];

$statusColors = [
    'pending' => ['bg' => '#fff3cd', 'color' => '#856404'],
    'confirmed' => ['bg' => '#dbeafe', 'color' => '#0c63e7'],
    'shipping' => ['bg' => '#f3e8ff', 'color' => '#8e44ad'],
    'delivered' => ['bg' => '#d1e7dd', 'color' => '#198754'],
    'completed' => ['bg' => '#d1e7dd', 'color' => '#146c43'],
    'cancelled' => ['bg' => '#fde2e2', 'color' => '#dc3545']
];
?>

<div class="account-container">

    <?php if (!empty($orderSuccess)): ?>
        <div class="alert alert-success">
            <?php echo htmlspecialchars($orderSuccess); ?>
        </div>
    <?php endif; ?>

    <?php if (!empty($success)): ?>
        <div class="alert alert-success">
            <?php echo htmlspecialchars($success); ?>
        </div>
    <?php endif; ?>

    <?php if (!empty($error)): ?>
        <div class="alert alert-danger">
            <?php echo htmlspecialchars($error); ?>
        </div>
    <?php endif; ?>

    <div class="account-header">
        <div class="account-profile-info">
            <div class="account-avatar">👤</div>
            <div>
                <h2 class="account-name"><?php echo htmlspecialchars($userName); ?></h2>
                <p class="account-email"><?php echo htmlspecialchars($userEmail); ?></p>
            </div>
        </div>
        <a href="?url=account/logout" class="btn btn-outline" style="text-decoration:none;">ĐĂNG XUẤT</a>
    </div>

    <div class="account-stats-grid">
        <div class="stat-card">
            <div class="stat-label">Hạng thành viên</div>
            <strong class="stat-value text-gold"><?php echo htmlspecialchars($userRank); ?></strong>
        </div>
        <div class="stat-card">
            <div class="stat-label">Số điện thoại</div>
            <strong class="stat-value"><?php echo htmlspecialchars($userPhone ?: 'Chưa cập nhật'); ?></strong>
        </div>
        <div class="stat-card">
            <div class="stat-label">Tổng đơn</div>
            <strong class="stat-value"><?php echo count($orders); ?></strong>
        </div>
        <div class="stat-card">
            <div class="stat-label">Ngày tham gia</div>
            <strong class="stat-value">
                <?php echo !empty($userCreatedAt) ? date('d/m/Y', strtotime($userCreatedAt)) : '---'; ?>
            </strong>
        </div>
    </div>

    <div class="account-forms-grid">
        <div class="form-card">
            <h3 class="section-title">Thông tin cá nhân</h3>
            
            <div class="info-summary-grid">
                <div class="info-box">
                    <span class="info-label">GIỚI TÍNH</span>
                    <strong class="info-value"><?php echo htmlspecialchars($genderLabel); ?></strong>
                </div>
                <div class="info-box">
                    <span class="info-label">NGÀY SINH</span>
                    <strong class="info-value">
                        <?php echo !empty($userBirthday) ? date('d/m/Y', strtotime($userBirthday)) : 'Chưa cập nhật'; ?>
                    </strong>
                </div>
                <div class="info-box">
                    <span class="info-label">THÀNH PHỐ</span>
                    <strong class="info-value"><?php echo htmlspecialchars($userCity ?: 'Chưa cập nhật'); ?></strong>
                </div>
                <div class="info-box">
                    <span class="info-label">TRẠNG THÁI</span>
                    <strong class="info-value">Đang hoạt động</strong>
                </div>
            </div>

            <form method="POST" action="?url=account/updateProfile">
                <label class="input-label">HỌ TÊN</label>
                <input class="input-field" name="full_name" value="<?php echo htmlspecialchars($userName); ?>">

                <label class="input-label">SỐ ĐIỆN THOẠI</label>
                <input class="input-field" name="phone" value="<?php echo htmlspecialchars($userPhone); ?>">

                <label class="input-label">GIỚI TÍNH</label>
                <select class="input-field" name="gender">
                    <option value="">Chưa cập nhật</option>
                    <option value="male" <?php echo $userGender === 'male' ? 'selected' : ''; ?>>Nam</option>
                    <option value="female" <?php echo $userGender === 'female' ? 'selected' : ''; ?>>Nữ</option>
                    <option value="other" <?php echo $userGender === 'other' ? 'selected' : ''; ?>>Khác</option>
                </select>

                <label class="input-label">NGÀY SINH</label>
                <input class="input-field" type="date" name="birthday" value="<?php echo htmlspecialchars($userBirthday); ?>">

                <label class="input-label">ĐỊA CHỈ</label>
                <input class="input-field" name="address" value="<?php echo htmlspecialchars($userAddress); ?>" placeholder="Số nhà, đường, phường/xã...">

                <label class="input-label">THÀNH PHỐ</label>
                <input class="input-field" name="city" value="<?php echo htmlspecialchars($userCity); ?>" placeholder="VD: TP. Hồ Chí Minh">

                <button class="btn btn-primary w-100" type="submit">CẬP NHẬT THÔNG TIN</button>
            </form>
        </div>

        <div class="form-card">
            <h3 class="section-title">Tài khoản & bảo mật</h3>

            <form method="POST" action="?url=account/changeEmail" class="mb-24">
                <label class="input-label">EMAIL</label>
                <input class="input-field" name="email" value="<?php echo htmlspecialchars($userEmail); ?>">
                <button class="btn btn-outline w-100" type="submit">ĐỔI EMAIL</button>
            </form>

            <form method="POST" action="?url=account/changePassword">
                <input class="input-field mb-12" type="password" name="current_password" placeholder="Mật khẩu hiện tại">
                <input class="input-field mb-12" type="password" name="new_password" placeholder="Mật khẩu mới">
                <input class="input-field mb-14" type="password" name="confirm_password" placeholder="Xác nhận mật khẩu mới">
                <button class="btn btn-primary w-100" type="submit">ĐỔI MẬT KHẨU</button>
            </form>
        </div>
    </div>

    <h3 class="section-title mb-22">🎟️ Coupon khả dụng</h3>
    <div class="coupon-grid">
        <?php if (empty($coupons)): ?>
            <p class="text-muted">Hiện chưa có coupon khả dụng.</p>
        <?php else: ?>
            <?php foreach ($coupons as $coupon): ?>
                <div class="coupon-card">
                    <strong class="coupon-code"><?php echo htmlspecialchars($coupon['code'] ?? ''); ?></strong>
                    <p class="coupon-desc"><?php echo htmlspecialchars($coupon['name'] ?? 'Mã giảm giá'); ?></p>
                    <span class="coupon-date">
                        HSD: <?php echo !empty($coupon['end_date']) ? date('d/m/Y', strtotime($coupon['end_date'])) : '---'; ?>
                    </span>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>

    <h3 id="reviews" class="section-title mb-22">⭐ Đánh giá sản phẩm</h3>
    <div class="mb-45">
        <?php if (empty($reviewableItems)): ?>
            <p class="empty-state-box">Bạn chưa có sản phẩm nào đủ điều kiện đánh giá.</p>
        <?php else: ?>
            <?php foreach ($reviewableItems as $item): ?>
                <form method="POST" action="?url=account/submitReview" class="review-form-card">
                    <input type="hidden" name="product_id" value="<?php echo (int)$item['product_id']; ?>">
                    <strong><?php echo htmlspecialchars($item['product_name']); ?></strong>
                    <span class="review-order-code">#<?php echo htmlspecialchars($item['order_code']); ?></span>

                    <div class="review-inputs-grid">
                        <select name="rating" class="input-field m-0">
                            <option value="5">5 sao</option>
                            <option value="4">4 sao</option>
                            <option value="3">3 sao</option>
                            <option value="2">2 sao</option>
                            <option value="1">1 sao</option>
                        </select>
                        <input name="comment" class="input-field m-0" placeholder="Cảm nhận của bạn về sản phẩm...">
                        <button class="btn btn-primary" type="submit">GỬI</button>
                    </div>
                </form>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>

    <h3 class="section-title mb-22">✍️ Đánh giá của tôi</h3>
    <div class="mb-45">
        <?php if (empty($myReviews)): ?>
            <p class="empty-state-box">Bạn chưa viết đánh giá nào.</p>
        <?php else: ?>
            <?php foreach ($myReviews as $review): ?>
                <div class="my-review-card">
                    <strong><?php echo htmlspecialchars($review['product_name'] ?? 'Sản phẩm'); ?></strong>
                    <div class="stars-container">
                        <?php echo str_repeat('★', (int)($review['rating'] ?? 5)); ?>
                        <?php echo str_repeat('☆', 5 - (int)($review['rating'] ?? 5)); ?>
                    </div>
                    <p class="review-comment">
                        <?php echo htmlspecialchars($review['comment'] ?? ''); ?>
                    </p>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>

    <h3 class="section-title mb-22">📋 Lịch sử đơn hàng</h3>
    <?php if (empty($orders)): ?>
        <p class="empty-state-box text-center">Bạn chưa có đơn hàng nào.</p>
    <?php else: ?>
        <div class="orders-container">
            <?php foreach ($orders as $order): ?>
                <?php
                    $status = $order['status'] ?? 'pending';
                    $statusText = $statusLabels[$status] ?? $status;
                    $statusStyle = $statusColors[$status] ?? ['bg' => '#f5f5f5', 'color' => '#666'];
                    $canCancel = in_array($status, ['pending', 'confirmed'], true);
                ?>

                <div class="order-card">
                    <div class="order-card-inner">
                        <div class="order-details">
                            <strong class="order-code">#<?php echo htmlspecialchars($order['order_code'] ?? $order['id']); ?></strong>
                            <span class="order-date">
                                <?php echo !empty($order['created_at']) ? date('d/m/Y H:i', strtotime($order['created_at'])) : ''; ?>
                            </span>
                            <span class="order-status-badge" style="background:<?php echo $statusStyle['bg']; ?>; color:<?php echo $statusStyle['color']; ?>;">
                                <?php echo htmlspecialchars($statusText); ?>
                            </span>

                            <?php if (!empty($order['cancel_reason']) && $status === 'cancelled'): ?>
                                <div class="order-cancel-reason">
                                    Lý do hủy: <?php echo htmlspecialchars($order['cancel_reason']); ?>
                                </div>
                            <?php endif; ?>

                            <?php if (!empty($order['items'])): ?>
                                <div class="order-items-list">
                                    <?php foreach ($order['items'] as $item): ?>
                                        <div>
                                            • <?php echo htmlspecialchars($item['product_name'] ?? 'Sản phẩm'); ?>
                                            <strong>x<?php echo (int)$item['quantity']; ?></strong>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                            <?php endif; ?>
                        </div>

                        <div class="order-actions">
                            <div class="order-total-label">Tổng thành tiền</div>
                            <div class="order-total-value">
                                <?php echo number_format((float)($order['final_amount'] ?? $order['total_amount'] ?? 0), 0, ',', '.'); ?> đ
                            </div>

                            <?php if ($canCancel): ?>
                                <form method="POST" action="?url=account/cancelOrder" class="mt-15" onsubmit="return confirm('Bạn chắc chắn muốn hủy đơn hàng này?');">
                                    <input type="hidden" name="order_id" value="<?php echo (int)$order['id']; ?>">
                                    <input type="hidden" name="cancel_reason" value="Khách hàng chủ động hủy đơn">
                                    <button type="submit" class="btn-cancel-order">Hủy đơn</button>
                                </form>
                            <?php else: ?>
                                <?php if (!in_array($status, ['cancelled', 'completed', 'delivered'], true)): ?>
                                    <div class="order-no-cancel-msg">Đơn đã chuyển sang giao hàng nên không thể hủy.</div>
                                <?php endif; ?>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>

</div>

<style>
    /* Cấu trúc chung */
    .account-container { padding-top: 160px; max-width: 1120px; margin: 0 auto; padding-bottom: 90px; font-family: var(--font-sans); }
    .text-gold { color: var(--accent-gold); }
    .text-muted { color: #666; }
    .text-center { text-align: center; }
    .w-100 { width: 100%; }
    .mb-12 { margin-bottom: 12px; }
    .mb-14 { margin-bottom: 14px; }
    .mb-22 { margin-bottom: 22px; }
    .mb-24 { margin-bottom: 24px; }
    .mb-45 { margin-bottom: 45px; }
    .mt-15 { margin-top: 15px; }
    .m-0 { margin: 0 !important; }

    /* Alerts (Thông báo) */
    .alert { padding: 14px 18px; margin-bottom: 22px; border: 1px solid transparent; }
    .alert-success { border-color: #d8ead8; background: #f4fbf4; color: #236b23; }
    .alert-danger { border-color: #f0b7b7; background: #fff5f5; color: #b42318; }

    /* Header Tài khoản */
    .account-header { display: flex; justify-content: space-between; align-items: center; border-bottom: 1px solid var(--border-light); padding-bottom: 24px; margin-bottom: 42px; }
    .account-profile-info { display: flex; align-items: center; gap: 22px; }
    .account-avatar { font-size: 38px; background: #f7f7f7; width: 84px; height: 84px; display: flex; align-items: center; justify-content: center; border-radius: 50%; border: 1px solid var(--border-light); }
    .account-name { font-family: var(--font-serif); font-size: 28px; letter-spacing: 3px; text-transform: uppercase; margin: 0 0 6px; }
    .account-email { color: #666; margin: 0; }

    /* Thống kê */
    .account-stats-grid { display: grid; grid-template-columns: repeat(4, 1fr); gap: 18px; margin-bottom: 38px; }
    .stat-card { border: 1px solid var(--border-light); padding: 22px; background: #fff; }
    .stat-label { font-size: 11px; letter-spacing: 2px; color: #888; text-transform: uppercase; }
    .stat-value { display: block; margin-top: 10px; text-transform: uppercase; }

    /* Form Cập nhật & Bảo mật */
    .account-forms-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 26px; margin-bottom: 45px; }
    .form-card { border: 1px solid var(--border-light); padding: 28px; background: #fff; }
    .section-title { font-family: var(--font-serif); font-size: 22px; letter-spacing: 3px; text-transform: uppercase; margin-top: 0; }
    .info-summary-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 14px; margin-bottom: 24px; }
    .info-box { background: #fafafa; padding: 14px; border: 1px solid var(--border-light); }
    .info-label { font-size: 10px; color: #888; letter-spacing: 1.5px; text-transform: uppercase; }
    .info-value { display: block; margin-top: 6px; }

    /* Form Inputs */
    .input-label { font-size: 11px; letter-spacing: 1.5px; text-transform: uppercase; display: block; }
    .input-field { width: 100%; padding: 14px; margin: 8px 0 18px; border: 1px solid var(--border-light); box-sizing: border-box; background: #fff; }
    
    /* Coupons */
    .coupon-grid { display: grid; grid-template-columns: repeat(2, 1fr); gap: 18px; margin-bottom: 45px; }
    .coupon-card { border: 1px solid var(--border-light); padding: 22px; background: #fff; }
    .coupon-code { font-size: 18px; letter-spacing: 2px; }
    .coupon-desc { color: #666; margin-bottom: 6px; }
    .coupon-date { font-size: 13px; color: #888; }

    /* Empty States (Khi không có dữ liệu) */
    .empty-state-box { color: #666; padding: 24px; border: 1px dashed var(--border-light); background: #fafafa; }

    /* Đánh giá */
    .review-form-card, .my-review-card { border: 1px solid var(--border-light); background: #fff; padding: 22px; margin-bottom: 16px; }
    .review-order-code { color: #888; margin-left: 10px; }
    .review-inputs-grid { display: grid; grid-template-columns: 140px 1fr auto; gap: 12px; margin-top: 14px; }
    .stars-container { color: #b89b5e; margin: 8px 0; }
    .review-comment { color: #555; line-height: 1.7; margin: 0; }

    /* Lịch sử Đơn hàng */
    .orders-container { display: flex; flex-direction: column; gap: 20px; }
    .order-card { border: 1px solid var(--border-light); padding: 25px; background: #fff; }
    .order-card-inner { display: flex; justify-content: space-between; gap: 30px; }
    .order-details { flex: 1; }
    .order-code { font-size: 16px; letter-spacing: 1px; }
    .order-date { color: #888; font-size: 13px; margin-left: 15px; }
    .order-status-badge { display: inline-block; padding: 4px 12px; margin-left: 12px; font-size: 11px; font-weight: 600; text-transform: uppercase; }
    .order-cancel-reason { margin-top: 10px; color: #b42318; font-size: 13px; }
    .order-items-list { margin-top: 14px; font-size: 13px; color: #555; }
    
    .order-actions { text-align: right; min-width: 220px; }
    .order-total-label { font-size: 11px; color: #888; text-transform: uppercase; letter-spacing: 1px; }
    .order-total-value { font-size: 18px; font-weight: 600; color: var(--accent-gold); margin-top: 6px; }
    .btn-cancel-order { width: 100%; padding: 10px; border: none; background: #b42318; color: #fff; cursor: pointer; font-size: 12px; font-weight: 700; letter-spacing: 1px; text-transform: uppercase; }
    .order-no-cancel-msg { margin-top: 15px; font-size: 12px; color: #888; }
</style>