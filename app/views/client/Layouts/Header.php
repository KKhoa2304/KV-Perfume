<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$baseUrl = '';

$cartCount = 0;
if (!empty($_SESSION['cart'])) {
    foreach ($_SESSION['cart'] as $item) {
        $cartCount += (int)($item['quantity'] ?? 0);
    }
}

$currentUrl = $_GET['url'] ?? 'home';
$isHomePage = $currentUrl === 'home' || $currentUrl === '';
$isCartOpen = isset($_GET['open_cart']) && $_GET['open_cart'] == '1';

$user = $_SESSION['user'] ?? null;
$userName = $user['full_name'] ?? $user['name'] ?? 'Khách';
$isLoggedIn = !empty($user);
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>KV PERFUME - Luxury Fragrance Boutique</title>

    <link rel="stylesheet" href="<?= $baseUrl ?>/public/css/index.css">
    <link rel="stylesheet" href="<?= $baseUrl ?>/public/css/Home.css">
    <link rel="stylesheet" href="<?= $baseUrl ?>/public/css/Checkout.css">
    <link rel="stylesheet" href="<?= $baseUrl ?>/public/css/Productdetail.css">
</head>
<body>

<header class="site-header">
    <div class="header-top">

        <div class="header-search">
            <form action="index.php" method="GET">
                <input type="hidden" name="url" value="home">
                <input
                    type="text"
                    name="keyword"
                    value="<?= htmlspecialchars($_GET['keyword'] ?? '') ?>"
                    placeholder="Tìm kiếm sản phẩm..."
                >
                <button type="submit">⌕</button>
            </form>
        </div>

        <div class="header-brand">
            <a href="?url=home" class="brand-name">KV PERFUME</a>
            <p>Luxury Fragrance Boutique</p>
        </div>

        <div class="header-actions">
            <div class="account-box">
                <span>Xin chào, <?= htmlspecialchars($userName) ?></span>
                <div>
                    <?php if ($isLoggedIn): ?>
                        <a href="?url=account">Tài khoản</a>
                        <span>hoặc</span>
                        <a href="?url=account/logout">Đăng xuất</a>
                    <?php else: ?>
                        <a href="?url=account">Đăng nhập</a>
                        <span>hoặc</span>
                        <a href="?url=account/registerForm">Đăng ký</a>
                    <?php endif; ?>
                </div>
            </div>

            <a href="?url=account" class="heart-btn">♡</a>

            <button class="cart-top-btn" id="openCartBtn" type="button">
                🛒
                <span class="cart-badge"><?= $cartCount ?></span>
            </button>
        </div>
    </div>

    <nav class="header-nav">
        <a href="?url=home" class="<?= $isHomePage ? 'active' : '' ?>">TRANG CHỦ</a>
        <a href="?url=about" class="<?= $currentUrl === 'about' ? 'active' : '' ?>">GIỚI THIỆU</a>

        <div class="nav-item-dropdown">
            <span class="dropdown-trigger">THƯƠNG HIỆU ▾</span>
            <div class="dropdown-menu">
                <a href="?url=home&brand_id=1">Chanel</a>
                <a href="?url=home&brand_id=2">Dior</a>
                <a href="?url=home&brand_id=3">YSL</a>
                <a href="?url=home&brand_id=4">Gucci</a>
            </div>
        </div>

        <div class="nav-item-dropdown">
            <span class="dropdown-trigger">NƯỚC HOA ▾</span>
            <div class="dropdown-menu">
                <a href="?url=home&gender=female">Nước hoa nữ</a>
                <a href="?url=home&gender=male">Nước hoa nam</a>
                <a href="?url=home&gender=unisex">Nước hoa unisex</a>
                <a href="?url=home&category_id=4">Gift Set</a>
            </div>
        </div>

        <a href="?url=contact" class="<?= $currentUrl === 'contact' ? 'active' : '' ?>">LIÊN HỆ</a>
    </nav>
</header>

<div class="overlay <?= $isCartOpen ? 'active' : '' ?>" id="cartOverlay"></div>

<div id="cart-drawer" class="cart-drawer <?= $isCartOpen ? 'open' : '' ?>">

    <div class="cart-header">
        <h2>Giỏ hàng của bạn</h2>
        <button id="closeCartBtn" type="button">x</button>
    </div>

    <?php if (!empty($_SESSION['cart_error'])): ?>
        <div style="
            color:#b00020;
            background:#fff3f3;
            border:1px solid #ffcccc;
            padding:10px 12px;
            margin:12px 20px 10px;
            font-size:14px;
            font-weight:600;
            line-height:1.5;
        ">
            <?= htmlspecialchars($_SESSION['cart_error']); ?>
        </div>
        <?php unset($_SESSION['cart_error']); ?>
    <?php endif; ?>

    <div class="cart-items">
        <?php if (empty($_SESSION['cart'])): ?>
            <p>Giỏ hàng trống</p>
        <?php else: ?>
            <?php foreach ($_SESSION['cart'] as $item): ?>
                <div class="cart-item">

                    <img
                        class="cart-item-img"
                        src="<?= htmlspecialchars($item['image'] ?? '') ?>"
                        alt="<?= htmlspecialchars($item['name'] ?? '') ?>"
                    >

                    <div class="cart-item-info">
                        <h4><?= htmlspecialchars($item['name'] ?? '') ?></h4>

                        <p class="cart-item-price">
                            <?= number_format((float)($item['price'] ?? 0), 0, ',', '.') ?>đ
                        </p>

                        <div class="cart-item-quantity">

                            <a href="?url=cart/update&id=<?= $item['id'] ?>&amount=-1" class="btn-qty">-</a>

                            <form action="?url=cart/setQuantity" method="POST" style="display:inline;">
                                <input type="hidden" name="id" value="<?= $item['id'] ?>">
                                <input
                                    type="number"
                                    name="quantity"
                                    value="<?= (int)$item['quantity'] ?>"
                                    min="1"
                                    onchange="this.form.submit()"
                                    style="width:45px; text-align:center;"
                                >
                            </form>

                            <a href="?url=cart/update&id=<?= $item['id'] ?>&amount=1" class="btn-qty">+</a>

                            <a href="?url=cart/remove&id=<?= $item['id'] ?>" class="btn-remove">Xóa</a>

                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>

    <div class="cart-footer">
        <div class="cart-total">
            <span>Tổng cộng:</span>
            <span>
                <?php
                $total = 0;
                if (!empty($_SESSION['cart'])) {
                    foreach ($_SESSION['cart'] as $item) {
                        $total += ((float)$item['price']) * ((int)$item['quantity']);
                    }
                }
                echo number_format($total, 0, ',', '.');
                ?>đ
            </span>
        </div>

        <a href="?url=cart/checkout" class="checkout-btn">
            THANH TOÁN
        </a>
    </div>

</div>