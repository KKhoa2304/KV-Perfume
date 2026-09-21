<div class="admin-page">
    <div class="admin-page-header">
        <div>
            <p class="admin-eyebrow">KV PERFUME ADMIN</p>
            <h1>Tổng quan hệ thống</h1>
            <p>Quản lý toàn bộ hoạt động website KV PERFUME.</p>
        </div>
    </div>

    <!-- KPI -->
    <div class="admin-stats-grid">

        <div class="admin-stat-card">
            <h3>Tổng đơn hàng</h3>
            <strong><?php echo (int)($totalOrders ?? 0); ?></strong>
            <span>Đơn đã tạo</span>
        </div>

        <div class="admin-stat-card">
            <h3>Doanh thu</h3>
            <strong>
                <?php echo number_format((float)($totalRevenue ?? 0), 0, ',', '.'); ?> VNĐ
            </strong>
            <span>Tổng doanh thu</span>
        </div>

        <div class="admin-stat-card">
            <h3>Đơn chờ xử lý</h3>
            <strong><?php echo (int)($pendingOrders ?? 0); ?></strong>
            <span>Cần xác nhận</span>
        </div>

    </div>

    <!-- Modules -->
    <div class="admin-detail-grid">

        <div class="admin-card">
            <h2>Quản lý sản phẩm</h2>
            <p>Thêm, sửa, cập nhật tồn kho, ngưng bán hoặc mở bán sản phẩm trực tiếp trên website.</p>
            <a class="admin-btn primary" href="?url=admin/product/index">
                Vào sản phẩm
            </a>
        </div>

        <div class="admin-card">
            <h2>Quản lý đơn hàng</h2>
            <p>Xác nhận đơn, cập nhật trạng thái giao hàng và theo dõi tiến trình xử lý.</p>
            <a class="admin-btn primary" href="?url=admin/order/index">
                Vào đơn hàng
            </a>
        </div>

        <div class="admin-card">
            <h2>Quản lý tài khoản</h2>
            <p>Khóa/mở khóa tài khoản khách hàng, phân quyền admin và nhân viên.</p>
            <a class="admin-btn primary" href="?url=admin/user/index">
                Vào tài khoản
            </a>
        </div>

        <div class="admin-card">
            <h2>Quản lý voucher</h2>
            <p>Tạo mã giảm giá, giới hạn số lượng, thời gian áp dụng và điều kiện sử dụng.</p>
            <a class="admin-btn primary" href="?url=admin/voucher/index">
                Vào voucher
            </a>
        </div>

    </div>
</div>  