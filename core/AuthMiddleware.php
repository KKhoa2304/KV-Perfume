<?php
/**
 * Lớp AuthMiddleware (Bảo mật & Phân quyền)
 * Mục đích: Đóng vai trò là "người gác cổng", kiểm tra trạng thái đăng nhập và quyền hạn trước khi cho phép truy cập vào các Controller/Action.
 */
class AuthMiddleware {
    
    // ==========================================================
    // 1. KIỂM TRA TRẠNG THÁI ĐĂNG NHẬP (AUTHENTICATION)
    // ==========================================================
    public static function requireLogin() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        
        // Block nếu người dùng chưa có session định danh
        if (!isset($_SESSION['user'])) {
            // UX Helper: Lưu lại URL hiện tại để tự động chuyển hướng về đúng trang (giỏ hàng, thanh toán...) sau khi đăng nhập thành công
            $_SESSION['redirect_to'] = '?url=' . ($_GET['url'] ?? 'home');
            $_SESSION['login_error'] = 'Vui lòng đăng nhập tài khoản để tiếp tục thao tác.';
            
            header('Location: ?url=account');
            exit();
        }
    }

    // ==========================================================
    // 2. KIỂM TRA QUYỀN HẠN (AUTHORIZATION)
    // ==========================================================
    public static function requireRole($requiredRole) {
        // Luôn phải đảm bảo user đã đăng nhập trước khi check quyền
        self::requireLogin(); 
        
        // Trích xuất role từ session (Hỗ trợ linh hoạt cả 2 key 'role' và 'role_id')
        $userRole = $_SESSION['user']['role'] ?? $_SESSION['user']['role_id'] ?? null;
        $hasPermission = false;

        // Chuẩn hóa role về chữ thường (nếu là chuỗi) để so sánh không bị phân biệt hoa/thường
        $normalizedRole = is_string($userRole) ? strtolower($userRole) : $userRole;

        // Phân luồng kiểm tra logic phân quyền
        if ($requiredRole === 'admin') {
            if ($normalizedRole === 1 || $normalizedRole === '1' || $normalizedRole === 'admin') {
                $hasPermission = true;
            }
        } 
        elseif ($requiredRole === 'user') {
            if ($normalizedRole === 0 || $normalizedRole === '0' || $normalizedRole === 'user') {
                $hasPermission = true;
            }
        } 
        elseif ($normalizedRole === $requiredRole) {
            // Khớp chính xác với các custom role khác nếu có
            $hasPermission = true;
        }

        // Từ chối truy cập nếu không thỏa mãn bất kỳ điều kiện nào ở trên
        if (!$hasPermission) {
            // TODO: Ở môi trường Production, nên điều hướng người dùng về trang giao diện Lỗi 403 (Forbidden) thay vì dùng die() để giữ trải nghiệm liền mạch.
            die("Thao tác thất bại: Bạn không có quyền truy cập vào khu vực này.");
        }
    }
}