<div class="admin-page">
    <div class="admin-page-header">
        <div>
            <p class="admin-eyebrow">KV PERFUME ADMIN</p>
            <h1>Quản lý tài khoản</h1>
            <p>
                Admin có thể khóa/mở khóa tài khoản và phân quyền.
                Hệ thống không cho tự khóa mình hoặc khóa admin cuối cùng.
            </p>
        </div>
    </div>

    <?php if (!empty($message)): ?>
        <div class="admin-alert success">
            <?php echo htmlspecialchars($message); ?>
        </div>
    <?php endif; ?>

    <?php if (!empty($error)): ?>
        <div class="admin-alert error">
            <?php echo htmlspecialchars($error); ?>
        </div>
    <?php endif; ?>

    <div class="admin-toolbar">
        <a class="admin-filter <?php echo (($role ?? 'all') === 'all') ? 'active' : ''; ?>"
           href="?url=admin/user/index&role=all&status=<?php echo htmlspecialchars($status ?? 'all'); ?>">
            Tất cả quyền
        </a>

        <a class="admin-filter <?php echo (($role ?? '') === 'admin') ? 'active' : ''; ?>"
           href="?url=admin/user/index&role=admin&status=<?php echo htmlspecialchars($status ?? 'all'); ?>">
            Admin
        </a>

        <a class="admin-filter <?php echo (($role ?? '') === 'staff') ? 'active' : ''; ?>"
           href="?url=admin/user/index&role=staff&status=<?php echo htmlspecialchars($status ?? 'all'); ?>">
            Nhân viên
        </a>

        <a class="admin-filter <?php echo (($role ?? '') === 'user') ? 'active' : ''; ?>"
           href="?url=admin/user/index&role=user&status=<?php echo htmlspecialchars($status ?? 'all'); ?>">
            Khách hàng
        </a>

        <span class="admin-toolbar-divider"></span>

        <a class="admin-filter <?php echo (($status ?? 'all') === 'all') ? 'active' : ''; ?>"
           href="?url=admin/user/index&role=<?php echo htmlspecialchars($role ?? 'all'); ?>&status=all">
            Tất cả trạng thái
        </a>

        <a class="admin-filter <?php echo (($status ?? '') === 'active') ? 'active' : ''; ?>"
           href="?url=admin/user/index&role=<?php echo htmlspecialchars($role ?? 'all'); ?>&status=active">
            Hoạt động
        </a>

        <a class="admin-filter <?php echo (($status ?? '') === 'locked') ? 'active' : ''; ?>"
           href="?url=admin/user/index&role=<?php echo htmlspecialchars($role ?? 'all'); ?>&status=locked">
            Bị khóa
        </a>
    </div>

    <div class="admin-card">
        <div class="admin-table-wrap">
            <table class="admin-table">
                <thead>
                    <tr>
                        <th>Người dùng</th>
                        <th>Email</th>
                        <th>Số điện thoại</th>
                        <th>Quyền</th>
                        <th>Hạng</th>
                        <th>Đơn hàng</th>
                        <th>Tổng chi tiêu</th>
                        <th>Trạng thái</th>
                        <th class="text-right">Thao tác</th>
                    </tr>
                </thead>

                <tbody>
                    <?php if (empty($users)): ?>
                        <tr>
                            <td colspan="9" class="admin-empty">
                                Không có tài khoản nào phù hợp.
                            </td>
                        </tr>
                    <?php endif; ?>

                    <?php foreach ($users as $user): ?>
                        <?php
                            $id = (int)($user['id'] ?? 0);
                            $roleValue = $user['role'] ?? 'user';
                            $statusValue = $user['status'] ?? 'active';
                            $rank = $user['rank_level'] ?? 'silver';
                            $totalOrders = (int)($user['total_orders'] ?? 0);
                            $spent = (float)($user['completed_spent'] ?? $user['total_spent'] ?? 0);
                            $currentAdminId = (int)($_SESSION['user']['id'] ?? $_SESSION['user_id'] ?? 0);
                            $isSelf = $id === $currentAdminId;
                        ?>

                        <tr>
                            <td>
                                <strong><?php echo htmlspecialchars($user['full_name'] ?? ''); ?></strong>
                                <?php if ($isSelf): ?>
                                    <span class="admin-mini-note">Tài khoản đang đăng nhập</span>
                                <?php endif; ?>
                            </td>

                            <td><?php echo htmlspecialchars($user['email'] ?? ''); ?></td>

                            <td>
                                <?php echo !empty($user['phone']) ? htmlspecialchars($user['phone']) : '<span class="admin-muted">Chưa có</span>'; ?>
                            </td>

                            <td>
                                <?php if ($roleValue === 'admin'): ?>
                                    <span class="admin-badge danger">Admin</span>
                                <?php elseif ($roleValue === 'staff'): ?>
                                    <span class="admin-badge warning">Nhân viên</span>
                                <?php else: ?>
                                    <span class="admin-badge active">Khách hàng</span>
                                <?php endif; ?>
                            </td>

                            <td>
                                <?php if ($rank === 'diamond'): ?>
                                    <span class="admin-badge danger">Diamond</span>
                                <?php elseif ($rank === 'gold'): ?>
                                    <span class="admin-badge warning">Gold</span>
                                <?php else: ?>
                                    <span class="admin-badge hidden">Silver</span>
                                <?php endif; ?>
                            </td>

                            <td><?php echo $totalOrders; ?></td>

                            <td>
                                <?php echo number_format($spent, 0, ',', '.'); ?> VNĐ
                            </td>

                            <td>
                                <?php if ($statusValue === 'active'): ?>
                                    <span class="admin-badge active">Hoạt động</span>
                                <?php else: ?>
                                    <span class="admin-badge hidden">Bị khóa</span>
                                <?php endif; ?>
                            </td>

                            <td class="text-right">
                                <form method="post"
                                      action="?url=admin/user/changeRole"
                                      class="inline-form admin-role-form"
                                      onsubmit="return confirm('Cập nhật quyền tài khoản này?');">
                                    <input type="hidden" name="id" value="<?php echo $id; ?>">

                                    <select name="role" class="admin-small-select" <?php echo $isSelf ? 'disabled' : ''; ?>>
                                        <option value="user" <?php echo $roleValue === 'user' ? 'selected' : ''; ?>>
                                            User
                                        </option>
                                        <option value="staff" <?php echo $roleValue === 'staff' ? 'selected' : ''; ?>>
                                            Staff
                                        </option>
                                        <option value="admin" <?php echo $roleValue === 'admin' ? 'selected' : ''; ?>>
                                            Admin
                                        </option>
                                    </select>

                                    <?php if (!$isSelf): ?>
                                        <button class="admin-btn small" type="submit">
                                            Lưu quyền
                                        </button>
                                    <?php endif; ?>
                                </form>

                                <?php if ($statusValue === 'active'): ?>
                                    <form method="post"
                                          action="?url=admin/user/lock"
                                          class="inline-form"
                                          onsubmit="return confirm('Khóa tài khoản này? Người dùng sẽ không đăng nhập được nữa.');">
                                        <input type="hidden" name="id" value="<?php echo $id; ?>">
                                        <button class="admin-btn small danger" type="submit" <?php echo $isSelf ? 'disabled' : ''; ?>>
                                            Khóa
                                        </button>
                                    </form>
                                <?php else: ?>
                                    <form method="post"
                                          action="?url=admin/user/unlock"
                                          class="inline-form"
                                          onsubmit="return confirm('Mở khóa tài khoản này?');">
                                        <input type="hidden" name="id" value="<?php echo $id; ?>">
                                        <button class="admin-btn small success" type="submit">
                                            Mở khóa
                                        </button>
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