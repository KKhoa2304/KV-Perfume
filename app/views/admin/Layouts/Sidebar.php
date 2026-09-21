<?php
$currentUrl = $_GET['url'] ?? '';

function adminActive($key, $currentUrl)
{
    return stripos($currentUrl, $key) !== false ? 'active' : '';
}
?>

<aside class="admin-sidebar">
    <div class="admin-logo">
        <strong>KV PERFUME</strong>
        <span>Luxury Admin Panel</span>
    </div>

    <nav class="admin-nav">
        <a class="<?php echo adminActive('admin/dashboard', $currentUrl); ?>"
           href="?url=admin/dashboard/index">
            Tổng quan
        </a>

        <a class="<?php echo adminActive('admin/product', $currentUrl); ?>"
           href="?url=admin/product/index">
            Quản lý sản phẩm
        </a>

        <a class="<?php echo adminActive('admin/order', $currentUrl); ?>"
           href="?url=admin/order/index">
            Quản lý đơn hàng
        </a>

        <a class="<?php echo adminActive('admin/user', $currentUrl); ?>"
           href="?url=admin/user/index">
            Quản lý tài khoản
        </a>

        <a class="<?php echo adminActive('admin/voucher', $currentUrl); ?>"
           href="?url=admin/voucher/index">
            Quản lý voucher
        </a>

        <a class="<?php echo adminActive('admin/contact', $currentUrl); ?>"
           href="?url=admin/contact/index">
            Quản lý liên hệ
        </a>

        <a href="?url=home/index" target="_blank">
            Xem website
        </a>

        <a href="?url=auth/logout">
            Đăng xuất
        </a>
    </nav>
</aside>