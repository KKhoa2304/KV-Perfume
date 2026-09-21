<?php

require_once __DIR__ . '/../../../core/Controller.php';
require_once __DIR__ . '/../../models/UserModel.php';
require_once __DIR__ . '/../../models/OrderModel.php';

class AccountController extends Controller
{
    private UserModel $userModel;
    private OrderModel $orderModel;

    public function __construct()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        $this->userModel = new UserModel();
        $this->orderModel = new OrderModel();
    }

    public function index()
    {
        if (!isset($_SESSION['user'])) {
            $error = $_SESSION['login_error'] ?? '';
            $success = $_SESSION['register_success'] ?? '';

            unset($_SESSION['login_error'], $_SESSION['register_success']);

            $this->render('client/Login_Form', [
                'error' => $error,
                'success' => $success
            ]);
            return;
        }

        $role = $_SESSION['user']['role'] ?? 'user';

        if ($role === 'admin') {
            header('Location: ?url=admin/dashboard/index');
            exit;
        }

        if ($role === 'staff') {
            header('Location: ?url=admin/order/index');
            exit;
        }

        $userId = (int)$_SESSION['user']['id'];

        $user = $this->userModel->getUserById($userId);
        $orders = $this->orderModel->getOrdersByUser($userId);

        foreach ($orders as &$order) {
            $order['items'] = $this->orderModel->getOrderItems((int)$order['id']);
        }
        unset($order);

        $this->render('client/Account_Info', [
            'user' => $user ?: $_SESSION['user'],
            'orders' => $orders,
            'coupons' => $this->userModel->getAvailableCoupons(),
            'reviewableItems' => $this->userModel->getReviewableItems($userId),
            'myReviews' => $this->userModel->getUserReviews($userId),
            'orderSuccess' => isset($_GET['order_success']) ? 'Đặt hàng thành công!' : '',
            'success' => $_SESSION['account_success'] ?? '',
            'error' => $_SESSION['account_error'] ?? ''
        ]);

        unset($_SESSION['account_success'], $_SESSION['account_error']);
    }

    public function updateProfile()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST' || empty($_SESSION['user'])) {
            header('Location: ?url=account');
            exit;
        }

        $userId = (int)$_SESSION['user']['id'];

        $data = [
            'full_name' => trim($_POST['full_name'] ?? ''),
            'phone' => trim($_POST['phone'] ?? ''),
            'gender' => trim($_POST['gender'] ?? ''),
            'birthday' => trim($_POST['birthday'] ?? ''),
            'address' => trim($_POST['address'] ?? ''),
            'city' => trim($_POST['city'] ?? '')
        ];

        $result = $this->userModel->updateProfile($userId, $data);

        if ($result['status']) {
            $_SESSION['account_success'] = $result['message'];

            $_SESSION['user']['name'] = $data['full_name'];
            $_SESSION['user']['phone'] = $data['phone'];
            $_SESSION['user']['gender'] = $data['gender'];
            $_SESSION['user']['birthday'] = $data['birthday'];
            $_SESSION['user']['address'] = $data['address'];
            $_SESSION['user']['city'] = $data['city'];
        } else {
            $_SESSION['account_error'] = $result['message'];
        }

        header('Location: ?url=account');
        exit;
    }

    public function changeEmail()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST' || empty($_SESSION['user'])) {
            header('Location: ?url=account');
            exit;
        }

        $userId = (int)$_SESSION['user']['id'];
        $email = trim($_POST['email'] ?? '');

        $result = $this->userModel->changeEmail($userId, $email);

        if ($result['status']) {
            $_SESSION['user']['email'] = $email;
            $_SESSION['account_success'] = $result['message'];
        } else {
            $_SESSION['account_error'] = $result['message'];
        }

        header('Location: ?url=account');
        exit;
    }

    public function changePassword()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST' || empty($_SESSION['user'])) {
            header('Location: ?url=account');
            exit;
        }

        $result = $this->userModel->changePassword(
            (int)$_SESSION['user']['id'],
            $_POST['current_password'] ?? '',
            $_POST['new_password'] ?? '',
            $_POST['confirm_password'] ?? ''
        );

        if ($result['status']) {
            $_SESSION['account_success'] = $result['message'];
        } else {
            $_SESSION['account_error'] = $result['message'];
        }

        header('Location: ?url=account');
        exit;
    }

    public function submitReview()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST' || empty($_SESSION['user'])) {
            header('Location: ?url=account');
            exit;
        }

        $result = $this->userModel->submitReview(
            (int)$_SESSION['user']['id'],
            (int)($_POST['product_id'] ?? 0),
            (int)($_POST['rating'] ?? 5),
            trim($_POST['comment'] ?? '')
        );

        if ($result['status']) {
            $_SESSION['account_success'] = $result['message'];
        } else {
            $_SESSION['account_error'] = $result['message'];
        }

        header('Location: ?url=account#reviews');
        exit;
    }

    public function cancelOrder()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST' || empty($_SESSION['user'])) {
            header('Location: ?url=account');
            exit;
        }

        $orderId = (int)($_POST['order_id'] ?? 0);
        $reason = trim($_POST['cancel_reason'] ?? 'Khách hàng chủ động hủy đơn');

        if ($orderId <= 0) {
            $_SESSION['account_error'] = 'Đơn hàng không hợp lệ.';
            header('Location: ?url=account');
            exit;
        }

        $result = $this->orderModel->cancelOrder(
            $orderId,
            (int)$_SESSION['user']['id'],
            $reason
        );

        if (!empty($result['success'])) {
            $_SESSION['account_success'] = 'Hủy đơn hàng thành công.';
        } else {
            $code = $result['code'] ?? '';

            if ($code === 'invalid_flow') {
                $_SESSION['account_error'] = 'Đơn hàng đang giao hoặc đã hoàn tất nên không thể hủy.';
            } elseif ($code === 'not_found') {
                $_SESSION['account_error'] = 'Không tìm thấy đơn hàng.';
            } else {
                $_SESSION['account_error'] = 'Không thể hủy đơn hàng lúc này. Lỗi: ' . ($result['message'] ?? $code);
            }
        }

        header('Location: ?url=account');
        exit;
    }

    public function registerForm()
    {
        if (isset($_SESSION['user'])) {
            header('Location: ?url=account');
            exit;
        }

        $error = $_SESSION['register_error'] ?? '';
        unset($_SESSION['register_error']);

        $this->render('client/register_form', [
            'error' => $error
        ]);
    }

    public function login()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: ?url=account');
            exit;
        }

        $email = trim($_POST['email'] ?? '');
        $password = trim($_POST['password'] ?? '');

        if ($email === '' || $password === '') {
            $_SESSION['login_error'] = 'Vui lòng điền đầy đủ thông tin đăng nhập.';
            header('Location: ?url=account');
            exit;
        }

        $user = $this->userModel->checkLogin($email, $password);

        if (!$user) {
            $_SESSION['login_error'] = 'Sai email hoặc mật khẩu.';
            header('Location: ?url=account');
            exit;
        }

        $_SESSION['user'] = $user;

        if ($user['role'] === 'admin') {
            header('Location: ?url=admin/dashboard/index');
            exit;
        }

        if ($user['role'] === 'staff') {
            header('Location: ?url=admin/order/index');
            exit;
        }

        $redirect = $_SESSION['redirect_to'] ?? '?url=account';
        unset($_SESSION['redirect_to']);

        header('Location: ' . $redirect);
        exit;
    }

    public function register()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: ?url=account/registerForm');
            exit;
        }

        $result = $this->userModel->registerUser(
            trim($_POST['fullname'] ?? ''),
            trim($_POST['email'] ?? ''),
            trim($_POST['phone'] ?? ''),
            trim($_POST['password'] ?? '')
        );

        if ($result['status']) {
            $_SESSION['register_success'] = $result['message'];
            header('Location: ?url=account');
            exit;
        }

        $_SESSION['register_error'] = $result['message'];
        header('Location: ?url=account/registerForm');
        exit;
    }

    public function logout()
    {
        unset($_SESSION['user'], $_SESSION['redirect_to']);
        header('Location: ?url=home');
        exit;
    }
}