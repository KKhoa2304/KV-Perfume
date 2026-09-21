<?php

require_once __DIR__ . '/XLData.php';

/**
 * Lớp UserModel
 * Quản lý toàn bộ nghiệp vụ liên quan đến Người dùng:
 * Đăng nhập, đăng ký, cập nhật hồ sơ, đổi mật khẩu và quản trị thành viên (Admin)
 */
class UserModel extends XLData
{
    /**
     * Kiểm tra thông tin đăng nhập
     */
    public function checkLogin($email, $password)
    {
        $sql = "SELECT * FROM users WHERE email = :email AND status = 'active'";
        $user = $this->readOne($sql, ['email' => $email]);

        // Sử dụng password_verify để so sánh mật khẩu gốc với mã hash trong DB
        if ($user && password_verify($password, $user['password'])) {
            return [
                'id'         => $user['id'],
                'email'      => $user['email'],
                'name'       => $user['full_name'],
                'phone'      => $user['phone'],
                'isVIP'      => !empty($user['phone']),
                'role'       => $user['role'],
                'rank_level' => $user['rank_level'],
                'created_at' => $user['created_at'] ?? ''
            ];
        }

        return null;
    }

    /**
     * Xử lý đăng ký tài khoản mới (Mặc định role = user, rank = silver)
     */
    public function registerUser($fullName, $email, $phone, $password)
    {
        try {
            // Chặn triệt để chuỗi khoảng trắng
            if (empty(trim($fullName)) || empty(trim($email)) || empty($password)) {
                return ['status' => false, 'message' => 'Vui lòng điền đầy đủ thông tin bắt buộc.'];
            }

            // Kiểm tra trùng Email
            if ($this->readOne("SELECT id FROM users WHERE email = :email", ['email' => $email])) {
                return ['status' => false, 'message' => 'Email này đã được sử dụng.'];
            }

            // Kiểm tra trùng Số điện thoại (nếu có nhập)
            if (!empty(trim($phone)) && $this->readOne("SELECT id FROM users WHERE phone = :phone", ['phone' => $phone])) {
                return ['status' => false, 'message' => 'Số điện thoại này đã được đăng ký.'];
            }

            $sql = "INSERT INTO users (full_name, email, phone, password, role, status, rank_level)
                    VALUES (:full_name, :email, :phone, :password, 'user', 'active', 'silver')";

            $isInserted = $this->executeItem($sql, [
                'full_name' => trim($fullName),
                'email'     => trim($email),
                'phone'     => empty(trim($phone)) ? null : trim($phone),
                'password'  => password_hash($password, PASSWORD_DEFAULT) // Mã hóa mật khẩu an toàn
            ]);

            return $isInserted
                ? ['status' => true,  'message' => 'Tạo tài khoản thành công. Vui lòng đăng nhập.']
                : ['status' => false, 'message' => 'Không thể tạo tài khoản lúc này.'];
        } catch (Exception $e) {
            return ['status' => false, 'message' => 'Có lỗi xảy ra khi tạo tài khoản.'];
        }
    }

    /**
     * Lấy thông tin chi tiết một user theo ID
     */
    public function getUserById($id)
    {
        return $this->readOne(
            "SELECT * FROM users WHERE id = :id",
            ['id' => (int)$id]
        );
    }

    /**
     * Cập nhật thông tin hồ sơ cá nhân
     */
    public function updateProfile($id, $data)
    {
        if (empty(trim($data['full_name'] ?? ''))) {
            return ['status' => false, 'message' => 'Họ tên không được để trống.'];
        }

        // Kiểm tra số điện thoại có bị trùng với người khác không
        if (!empty(trim($data['phone'] ?? ''))) {
            $exists = $this->readOne(
                "SELECT id FROM users WHERE phone = :phone AND id != :id",
                [
                    'phone' => trim($data['phone']),
                    'id'    => (int)$id
                ]
            );

            if ($exists) {
                return ['status' => false, 'message' => 'Số điện thoại đã được sử dụng.'];
            }
        }

        // Cập nhật thông tin (Dùng Null Coalescing ?? để tránh lỗi Undefined Key)
        $updated = $this->executeItem(
            "UPDATE users 
             SET full_name = :full_name, phone = :phone, gender = :gender,
                 birthday = :birthday, address = :address, city = :city, updated_at = NOW()
             WHERE id = :id",
            [
                'full_name' => trim($data['full_name']),
                'phone'     => !empty(trim($data['phone'] ?? '')) ? trim($data['phone']) : null,
                'gender'    => $data['gender'] ?? null,
                'birthday'  => $data['birthday'] ?? null,
                'address'   => $data['address'] ?? null,
                'city'      => $data['city'] ?? null,
                'id'        => (int)$id
            ]
        );

        return [
            'status'  => (bool)$updated,
            'message' => 'Cập nhật thông tin thành công.'
        ];
    }

    /**
     * Cập nhật Email (Yêu cầu Email chưa được ai sử dụng)
     */
    public function changeEmail($id, $newEmail)
    {
        $newEmail = trim($newEmail);
        
        if (!filter_var($newEmail, FILTER_VALIDATE_EMAIL)) {
            return ['status' => false, 'message' => 'Email không hợp lệ.'];
        }

        $exists = $this->readOne(
            "SELECT id FROM users WHERE email = :email AND id != :id",
            ['email' => $newEmail, 'id' => (int)$id]
        );

        if ($exists) {
            return ['status' => false, 'message' => 'Email này đã được tài khoản khác sử dụng.'];
        }

        $updated = $this->executeItem(
            "UPDATE users SET email = :email, updated_at = NOW() WHERE id = :id",
            ['email' => $newEmail, 'id' => (int)$id]
        );

        return $updated
            ? ['status' => true,  'message' => 'Cập nhật email thành công.']
            : ['status' => false, 'message' => 'Không thể cập nhật email lúc này.'];
    }

    /**
     * Đổi mật khẩu (Yêu cầu nhập đúng mật khẩu cũ)
     */
    public function changePassword($id, $currentPassword, $newPassword, $confirmPassword)
    {
        $user = $this->getUserById($id);

        if (!$user || !password_verify($currentPassword, $user['password'])) {
            return ['status' => false, 'message' => 'Mật khẩu hiện tại không chính xác.'];
        }

        if (strlen($newPassword) < 6) {
            return ['status' => false, 'message' => 'Mật khẩu mới phải có ít nhất 6 ký tự.'];
        }

        if ($newPassword !== $confirmPassword) {
            return ['status' => false, 'message' => 'Xác nhận mật khẩu không khớp.'];
        }

        $updated = $this->executeItem(
            "UPDATE users SET password = :password, updated_at = NOW() WHERE id = :id",
            ['password' => password_hash($newPassword, PASSWORD_DEFAULT), 'id' => (int)$id]
        );

        return $updated
            ? ['status' => true,  'message' => 'Đổi mật khẩu thành công.']
            : ['status' => false, 'message' => 'Không thể đổi mật khẩu lúc này.'];
    }

    /**
     * Lấy danh sách mã giảm giá (Voucher) còn hạn và khả dụng
     */
    public function getAvailableCoupons()
    {
        return $this->readItem(
            "SELECT * FROM vouchers
             WHERE status = 1
               AND quantity > used_quantity
               AND start_date <= NOW()
               AND end_date >= NOW()
             ORDER BY id DESC"
        );
    }

    /**
     * Lấy danh sách sản phẩm khách hàng đã mua và đủ điều kiện đánh giá
     */
    public function getReviewableItems($userId)
    {
        return $this->readItem(
            "SELECT oi.product_id, oi.product_name, oi.product_image,
                    o.order_code, o.id AS order_id, o.status, o.created_at
             FROM order_items oi
             INNER JOIN orders o ON oi.order_id = o.id
             WHERE o.user_id = :user_id
               AND o.status IN ('delivered', 'completed')
               AND NOT EXISTS (
                   SELECT 1 FROM reviews r 
                   WHERE r.user_id = o.user_id 
                     AND r.product_id = oi.product_id
               )
             ORDER BY o.id DESC",
            ['user_id' => (int)$userId]
        );
    }

    /**
     * Lấy lịch sử đánh giá của khách hàng
     */
    public function getUserReviews($userId)
    {
        return $this->readItem(
            "SELECT r.*, p.name AS product_name, p.main_image AS product_image
             FROM reviews r
             LEFT JOIN products p ON r.product_id = p.id
             WHERE r.user_id = :user_id
             ORDER BY r.id DESC",
            ['user_id' => (int)$userId]
        );
    }

    /**
     * Gửi đánh giá sản phẩm mới (Mặc định ở trạng thái chờ duyệt)
     */
    public function submitReview($userId, $productId, $rating, $comment)
    {
        $rating = max(1, min(5, (int)$rating));

        if ($productId <= 0) {
            return ['status' => false, 'message' => 'Sản phẩm không hợp lệ.'];
        }

        $alreadyReviewed = $this->readOne(
            "SELECT id FROM reviews 
             WHERE user_id = :user_id AND product_id = :product_id LIMIT 1",
            ['user_id' => (int)$userId, 'product_id' => (int)$productId]
        );

        if ($alreadyReviewed) {
            return ['status' => false, 'message' => 'Bạn đã đánh giá sản phẩm này rồi.'];
        }

        try {
            $sql = "INSERT INTO reviews (user_id, product_id, rating, comment, status, created_at)
                    VALUES (:user_id, :product_id, :rating, :comment, 'pending', NOW())";

            $ok = $this->executeItem($sql, [
                'user_id'    => (int)$userId,
                'product_id' => (int)$productId,
                'rating'     => $rating,
                'comment'    => trim($comment)
            ]);

            return $ok
                ? ['status' => true,  'message' => 'Cảm ơn! Đánh giá của bạn đã được gửi và đang chờ duyệt.']
                : ['status' => false, 'message' => 'Không thể gửi đánh giá lúc này.'];
        } catch (Exception $e) {
            return ['status' => false, 'message' => 'Không thể gửi đánh giá. Vui lòng thử lại sau.'];
        }
    }

    /**
     * ==========================================
     * CÁC HÀM DÀNH RIÊNG CHO QUẢN TRỊ VIÊN (ADMIN)
     * ==========================================
     */

    /**
     * Lấy danh sách toàn bộ người dùng (Có kèm thống kê chi tiêu)
     */
    public function getAllUsers($role = 'all', $status = 'all')
    {
        $sql = "SELECT u.*,
                       COUNT(o.id) AS total_orders,
                       COALESCE(SUM(CASE WHEN o.status = 'completed' THEN o.final_amount ELSE 0 END), 0) AS completed_spent
                FROM users u
                LEFT JOIN orders o ON u.id = o.user_id
                WHERE 1 = 1";

        $params = [];

        if ($role !== 'all') {
            $sql .= " AND u.role = :role";
            $params['role'] = $role;
        }

        if ($status !== 'all') {
            $sql .= " AND u.status = :status";
            $params['status'] = $status;
        }

        $sql .= " GROUP BY u.id ORDER BY u.id DESC";

        return $this->readItem($sql, $params);
    }

    /**
     * Khóa tài khoản người dùng
     */
    public function lockUser($id)
    {
        $user = $this->getUserById($id);

        if (!$user) {
            return ['success' => false, 'code' => 'not_found'];
        }

        // Chống tự khóa Admin cuối cùng của hệ thống
        if ($user['role'] === 'admin' && $this->countActiveAdmins() <= 1) {
            return ['success' => false, 'code' => 'last_admin'];
        }

        $updated = $this->executeItem(
            "UPDATE users SET status = 'locked', updated_at = NOW() WHERE id = :id",
            ['id' => (int)$id]
        );

        return [
            'success' => (bool)$updated,
            'code'    => $updated ? 'ok' : 'update_failed'
        ];
    }

    /**
     * Mở khóa tài khoản người dùng
     */
    public function unlockUser($id)
    {
        $updated = $this->executeItem(
            "UPDATE users SET status = 'active', updated_at = NOW() WHERE id = :id",
            ['id' => (int)$id]
        );

        return [
            'success' => (bool)$updated,
            'code'    => $updated ? 'ok' : 'update_failed'
        ];
    }

    /**
     * Thay đổi quyền hạn (Role) của người dùng
     */
    public function changeRole($id, $role)
    {
        $validRoles = ['user', 'staff', 'admin'];

        if (!in_array($role, $validRoles, true)) {
            return ['success' => false, 'code' => 'invalid_role'];
        }

        $updated = $this->executeItem(
            "UPDATE users SET role = :role, updated_at = NOW() WHERE id = :id",
            ['id' => (int)$id, 'role' => $role]
        );

        return [
            'success' => (bool)$updated,
            'code'    => $updated ? 'ok' : 'update_failed'
        ];
    }

    /**
     * Đếm số lượng Admin đang hoạt động (Dùng để chặn xóa/khóa admin cuối cùng)
     */
    public function countActiveAdmins()
    {
        $row = $this->readOne(
            "SELECT COUNT(*) AS total FROM users WHERE role = 'admin' AND status = 'active'"
        );
        return (int)($row['total'] ?? 0);
    }
}
?>