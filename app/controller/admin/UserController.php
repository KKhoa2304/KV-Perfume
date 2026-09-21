<?php

require_once __DIR__ . '/../../../core/Controller.php';
require_once __DIR__ . '/../../../core/AuthMiddleware.php';
require_once __DIR__ . '/../../models/UserModel.php';

class UserController extends Controller
{
    private UserModel $userModel;

    public function __construct()
    {
        AuthMiddleware::requireRole('admin');
        $this->userModel = new UserModel();
    }

    public function index()
    {
        $role = $_GET['role'] ?? 'all';
        $status = $_GET['status'] ?? 'all';

        $users = $this->userModel->getAllUsers($role, $status);

        $this->renderAdmin('admin/User/index', [
            'title' => 'Quản lý tài khoản',
            'users' => $users,
            'role' => $role,
            'status' => $status,
            'message' => $this->getMessage($_GET['msg'] ?? ''),
            'error' => $this->getError($_GET['error'] ?? '')
        ]);
    }

    public function lock()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: ?url=admin/user/index&error=method_not_allowed');
            exit;
        }

        $id = (int)($_POST['id'] ?? 0);
        $currentAdminId = (int)($_SESSION['user']['id'] ?? $_SESSION['user_id'] ?? 0);

        if ($id <= 0) {
            header('Location: ?url=admin/user/index&error=invalid_id');
            exit;
        }

        if ($id === $currentAdminId) {
            header('Location: ?url=admin/user/index&error=cannot_lock_self');
            exit;
        }

        $result = $this->userModel->lockUser($id);

        if (!$result['success']) {
            header('Location: ?url=admin/user/index&error=' . urlencode($result['code']));
            exit;
        }

        header('Location: ?url=admin/user/index&msg=locked');
        exit;
    }

    public function unlock()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: ?url=admin/user/index&error=method_not_allowed');
            exit;
        }

        $id = (int)($_POST['id'] ?? 0);

        if ($id <= 0) {
            header('Location: ?url=admin/user/index&error=invalid_id');
            exit;
        }

        $result = $this->userModel->unlockUser($id);

        if (!$result['success']) {
            header('Location: ?url=admin/user/index&error=' . urlencode($result['code']));
            exit;
        }

        header('Location: ?url=admin/user/index&msg=unlocked');
        exit;
    }

    public function changeRole()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: ?url=admin/user/index&error=method_not_allowed');
            exit;
        }

        $id = (int)($_POST['id'] ?? 0);
        $role = trim($_POST['role'] ?? '');
        $currentAdminId = (int)($_SESSION['user']['id'] ?? $_SESSION['user_id'] ?? 0);

        if ($id <= 0) {
            header('Location: ?url=admin/user/index&error=invalid_id');
            exit;
        }

        if ($id === $currentAdminId && $role !== 'admin') {
            header('Location: ?url=admin/user/index&error=cannot_downgrade_self');
            exit;
        }

        $result = $this->userModel->changeRole($id, $role);

        if (!$result['success']) {
            header('Location: ?url=admin/user/index&error=' . urlencode($result['code']));
            exit;
        }

        header('Location: ?url=admin/user/index&msg=role_updated');
        exit;
    }

    private function getMessage($key)
    {
        if ($key === '') {
            return '';
        }

        $messages = [
            'locked' => 'Đã khóa tài khoản thành công.',
            'unlocked' => 'Đã mở khóa tài khoản thành công.',
            'role_updated' => 'Cập nhật quyền tài khoản thành công.'
        ];

        return $messages[$key] ?? '';
    }

    private function getError($key)
    {
        if ($key === '') {
            return '';
        }

        $errors = [
            'invalid_id' => 'Mã tài khoản không hợp lệ.',
            'not_found' => 'Không tìm thấy tài khoản.',
            'method_not_allowed' => 'Thao tác không hợp lệ.',
            'invalid_role' => 'Quyền tài khoản không hợp lệ.',
            'cannot_lock_self' => 'Bạn không thể tự khóa tài khoản đang đăng nhập.',
            'cannot_downgrade_self' => 'Bạn không thể tự hạ quyền tài khoản admin đang đăng nhập.',
            'last_admin' => 'Không thể thực hiện vì hệ thống phải còn ít nhất một tài khoản admin hoạt động.',
            'update_failed' => 'Cập nhật tài khoản thất bại.'
        ];

        return $errors[$key] ?? 'Có lỗi xảy ra. Vui lòng kiểm tra lại.';
    }
}