<?php

require_once __DIR__ . '/../../../core/Controller.php';
require_once __DIR__ . '/../../models/UserModel.php';

class AuthController extends Controller
{
    private UserModel $userModel;
    private const ACCOUNT_URL = '?url=account';

    public function __construct()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        $this->userModel = new UserModel();
    }

    public function index(): void
    {
        if (isset($_SESSION['user'])) {
            $role = $_SESSION['user']['role'] ?? 'user';

            if ($role === 'admin') {
                header('Location: ?url=admin/dashboard/index');
                exit;
            }

            if ($role === 'staff') {
                header('Location: ?url=admin/order/index');
                exit;
            }

            header('Location: ?url=account');
            exit;
        }

        $error = $_SESSION['login_error'] ?? '';
        unset($_SESSION['login_error']);

        $this->render('client/Login_Form', [
            'error' => $error
        ]);
    }

    public function login(): void
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirectTo(self::ACCOUNT_URL);
        }

        $email = trim(filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL) ?? '');
        $password = $_POST['password'] ?? '';

        if ($email === '' || $password === '') {
            $this->flashError('Vui lòng điền đầy đủ thông tin.');
            $this->redirectTo(self::ACCOUNT_URL);
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $this->flashError('Địa chỉ email không hợp lệ.');
            $this->redirectTo(self::ACCOUNT_URL);
        }

        $user = $this->userModel->checkLogin($email, $password);

        if (!$user) {
            $this->flashError('Sai email hoặc mật khẩu.');
            $this->redirectTo(self::ACCOUNT_URL);
        }

        session_regenerate_id(true);
        $_SESSION['user'] = $user;

        if ($user['role'] === 'admin') {
            unset($_SESSION['redirect_to']);
            $this->redirectTo('?url=admin/dashboard/index');
        }

        if ($user['role'] === 'staff') {
            unset($_SESSION['redirect_to']);
            $this->redirectTo('?url=admin/order/index');
        }

        $this->redirectTo($this->getSafeRedirect());
    }

    public function logout(): void
    {
        $_SESSION = [];

        if (ini_get('session.use_cookies')) {
            $p = session_get_cookie_params();

            setcookie(
                session_name(),
                '',
                time() - 42000,
                $p['path'],
                $p['domain'],
                $p['secure'],
                $p['httponly']
            );
        }

        session_destroy();

        $this->redirectTo('?url=home/index');
    }

    private function flashError(string $message): void
    {
        $_SESSION['login_error'] = $message;
    }

    private function redirectTo(string $url): never
    {
        header('Location: ' . $url);
        exit;
    }

    private function getSafeRedirect(): string
    {
        $url = $_SESSION['redirect_to'] ?? '';
        unset($_SESSION['redirect_to']);

        if (!empty($url) && (str_starts_with($url, '?') || str_starts_with($url, '/'))) {
            return $url;
        }

        return self::ACCOUNT_URL;
    }
}