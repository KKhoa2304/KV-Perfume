<?php

require_once __DIR__ . '/../../../core/Controller.php';
require_once __DIR__ . '/../../../core/AuthMiddleware.php';
require_once __DIR__ . '/../../models/OrderModel.php';

class OrderController extends Controller
{
    private OrderModel $orderModel;

    public function __construct()
    {
        AuthMiddleware::requireRole('admin');
        $this->orderModel = new OrderModel();
    }

    public function index()
    {
        $status = $_GET['status'] ?? 'all';
        $orders = $this->orderModel->getAllOrders($status);

        $this->renderAdmin('admin/Order/index', [
            'title' => 'Quản lý đơn hàng',
            'orders' => $orders,
            'status' => $status,
            'message' => $this->getMessage($_GET['msg'] ?? ''),
            'error' => $this->getError($_GET['error'] ?? '')
        ]);
    }

    public function detail($id = null)
    {
        $id = (int)($id ?? $_GET['id'] ?? 0);

        if ($id <= 0) {
            header('Location: ?url=admin/order/index&error=invalid_id');
            exit;
        }

        $order = $this->orderModel->getOrderById($id);

        if (!$order) {
            header('Location: ?url=admin/order/index&error=not_found');
            exit;
        }

        $items = $this->orderModel->getOrderItems($id);
        $logs = $this->orderModel->getOrderLogs($id);

        $this->renderAdmin('admin/Order/detail', [
            'title' => 'Chi tiết đơn hàng',
            'order' => $order,
            'items' => $items,
            'logs' => $logs,
            'message' => $this->getMessage($_GET['msg'] ?? ''),
            'error' => $this->getError($_GET['error'] ?? '')
        ]);
    }

    public function updateStatus()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: ?url=admin/order/index&error=method_not_allowed');
            exit;
        }

        $orderId = (int)($_POST['order_id'] ?? 0);
        $newStatus = trim($_POST['status'] ?? '');
        $note = trim($_POST['note'] ?? '');
        $adminId = (int)($_SESSION['user']['id'] ?? $_SESSION['user_id'] ?? 0);

        if ($orderId <= 0) {
            header('Location: ?url=admin/order/index&error=invalid_id');
            exit;
        }

        $result = $this->orderModel->updateOrderStatus($orderId, $newStatus, $adminId, $note);

        if (!$result['success']) {
            header('Location: ?url=admin/order/detail/' . $orderId . '&error=' . urlencode($result['code']));
            exit;
        }

        header('Location: ?url=admin/order/detail/' . $orderId . '&msg=status_updated');
        exit;
    }

    public function cancel()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: ?url=admin/order/index&error=method_not_allowed');
            exit;
        }

        $orderId = (int)($_POST['order_id'] ?? 0);
        $reason = trim($_POST['cancel_reason'] ?? '');
        $adminId = (int)($_SESSION['user']['id'] ?? $_SESSION['user_id'] ?? 0);

        if ($orderId <= 0) {
            header('Location: ?url=admin/order/index&error=invalid_id');
            exit;
        }

        if ($reason === '') {
            header('Location: ?url=admin/order/detail/' . $orderId . '&error=missing_cancel_reason');
            exit;
        }

        $result = $this->orderModel->cancelOrder($orderId, $adminId, $reason);

        if (!$result['success']) {
            header('Location: ?url=admin/order/detail/' . $orderId . '&error=' . urlencode($result['code']));
            exit;
        }

        header('Location: ?url=admin/order/detail/' . $orderId . '&msg=cancelled');
        exit;
    }

    private function getMessage($key)
    {
        if ($key === '') {
            return '';
        }

        $messages = [
            'status_updated' => 'Cập nhật trạng thái đơn hàng thành công.',
            'cancelled' => 'Đã hủy đơn hàng thành công.'
        ];

        return $messages[$key] ?? '';
    }

    private function getError($key)
    {
        if ($key === '') {
            return '';
        }

        $errors = [
            'invalid_id' => 'Mã đơn hàng không hợp lệ.',
            'not_found' => 'Không tìm thấy đơn hàng.',
            'method_not_allowed' => 'Thao tác không hợp lệ.',
            'invalid_status' => 'Trạng thái đơn hàng không hợp lệ.',
            'invalid_flow' => 'Không thể chuyển trạng thái đơn hàng sai quy trình.',
            'completed_locked' => 'Đơn hàng đã hoàn tất, không thể thay đổi trạng thái.',
            'cancelled_locked' => 'Đơn hàng đã hủy, không thể thay đổi trạng thái.',
            'missing_cancel_reason' => 'Vui lòng nhập lý do hủy đơn hàng.',
            'update_failed' => 'Cập nhật đơn hàng thất bại.'
        ];

        return $errors[$key] ?? 'Có lỗi xảy ra. Vui lòng kiểm tra lại.';
    }
}