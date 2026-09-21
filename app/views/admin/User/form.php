<div class="admin-page">
    <div class="admin-page-header">
        <div>
            <p class="admin-eyebrow">KV PERFUME ADMIN</p>
            <h1><?php echo isset($user) ? 'Chỉnh sửa tài khoản' : 'Thêm tài khoản'; ?></h1>
            <p>Quản lý thông tin tài khoản và phân quyền người dùng.</p>
        </div>
    </div>

    <div class="admin-card">
        <form action="" method="POST" class="admin-form-grid">

            <div class="form-group">
                <label>Họ tên</label>
                <input type="text" name="full_name"
                       value="<?php echo htmlspecialchars($user['full_name'] ?? ''); ?>"
                       required>
            </div>

            <div class="form-group">
                <label>Email</label>
                <input type="email" name="email"
                       value="<?php echo htmlspecialchars($user['email'] ?? ''); ?>"
                       required>
            </div>

            <div class="form-group">
                <label>Mật khẩu</label>
                <input type="password" name="password"
                       <?php echo isset($user) ? '' : 'required'; ?>>
            </div>

            <div class="form-group">
                <label>Vai trò</label>
                <select name="role" required>
                    <option value="user"
                        <?php echo (($user['role'] ?? '') === 'user') ? 'selected' : ''; ?>>
                        User
                    </option>
                    <option value="staff"
                        <?php echo (($user['role'] ?? '') === 'staff') ? 'selected' : ''; ?>>
                        Staff
                    </option>
                    <option value="admin"
                        <?php echo (($user['role'] ?? '') === 'admin') ? 'selected' : ''; ?>>
                        Admin
                    </option>
                </select>
            </div>

            <div class="form-group full-width">
                <label>Trạng thái</label>
                <select name="status">
                    <option value="active"
                        <?php echo (($user['status'] ?? '') === 'active') ? 'selected' : ''; ?>>
                        Hoạt động
                    </option>
                    <option value="blocked"
                        <?php echo (($user['status'] ?? '') === 'blocked') ? 'selected' : ''; ?>>
                        Bị khóa
                    </option>
                </select>
            </div>

            <div class="admin-form-actions">
                <button type="submit" class="admin-btn primary">
                    <?php echo isset($user) ? 'Cập nhật' : 'Tạo mới'; ?>
                </button>

                <a href="?url=admin/user/index" class="admin-btn">
                    Quay lại
                </a>
            </div>

        </form>
    </div>
</div>