<?php

require_once __DIR__ . '/../../../core/Controller.php';
require_once __DIR__ . '/../../../core/AuthMiddleware.php';
require_once __DIR__ . '/../../models/VoucherModel.php';

class VoucherController extends Controller
{
    private VoucherModel $voucherModel;

    public function __construct()
    {
        AuthMiddleware::requireRole('admin');
        $this->voucherModel = new VoucherModel();
    }

    public function index()
    {
        $status = $_GET['status'] ?? 'all';
        $vouchers = $this->voucherModel->getAllVouchers($status);

        $this->renderAdmin('admin/voucher/index', [
            'title' => 'Quản lý mã giảm giá',
            'vouchers' => $vouchers,
            'status' => $status,
            'message' => $this->getMessage($_GET['msg'] ?? ''),
            'error' => $this->getError($_GET['error'] ?? '')
        ]);
    }

    public function create()
    {
        $voucher = [
            'discount_type' => 'percent',
            'status' => 1
        ];

        $errors = [];

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $voucher = $this->cleanInput();
            $errors = $this->validateVoucher($voucher);

            if (empty($errors)) {
                try {
                    $this->voucherModel->createVoucher($voucher);
                    header('Location: ?url=admin/voucher/index&msg=created');
                    exit;
                } catch (Exception $e) {
                    $errors[] = $this->friendlyException($e->getMessage());
                }
            }
        }

        $this->renderAdmin('admin/voucher/form', [
            'title' => 'Thêm mã giảm giá',
            'mode' => 'create',
            'voucher' => $voucher,
            'errors' => $errors
        ]);
    }

    public function edit($id = null)
    {
        $id = (int)($id ?? $_GET['id'] ?? 0);

        if ($id <= 0) {
            header('Location: ?url=admin/voucher/index&error=invalid_id');
            exit;
        }

        $voucher = $this->voucherModel->getVoucherById($id);

        if (!$voucher) {
            header('Location: ?url=admin/voucher/index&error=not_found');
            exit;
        }

        $errors = [];

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $voucher = array_merge($voucher, $this->cleanInput());
            $errors = $this->validateVoucher($voucher);

            if (empty($errors)) {
                try {
                    $this->voucherModel->updateVoucher($id, $voucher);
                    header('Location: ?url=admin/voucher/index&msg=updated');
                    exit;
                } catch (Exception $e) {
                    $errors[] = $this->friendlyException($e->getMessage());
                }
            }
        }

        $this->renderAdmin('admin/voucher/form', [
            'title' => 'Sửa mã giảm giá',
            'mode' => 'edit',
            'voucher' => $voucher,
            'errors' => $errors
        ]);
    }

    public function lock()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: ?url=admin/voucher/index&error=method_not_allowed');
            exit;
        }

        $id = (int)($_POST['id'] ?? 0);

        if ($id <= 0) {
            header('Location: ?url=admin/voucher/index&error=invalid_id');
            exit;
        }

        $this->voucherModel->lockVoucher($id);

        header('Location: ?url=admin/voucher/index&msg=locked');
        exit;
    }

    public function unlock()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: ?url=admin/voucher/index&error=method_not_allowed');
            exit;
        }

        $id = (int)($_POST['id'] ?? 0);

        if ($id <= 0) {
            header('Location: ?url=admin/voucher/index&error=invalid_id');
            exit;
        }

        $result = $this->voucherModel->unlockVoucher($id);

        if (!$result['success']) {
            header('Location: ?url=admin/voucher/index&error=' . urlencode($result['code']));
            exit;
        }

        header('Location: ?url=admin/voucher/index&msg=unlocked');
        exit;
    }

    private function cleanInput()
    {
        return [
            'code' => strtoupper(trim($_POST['code'] ?? '')),
            'name' => trim($_POST['name'] ?? ''),
            'discount_type' => trim($_POST['discount_type'] ?? 'percent'),
            'discount_value' => (float)($_POST['discount_value'] ?? 0),
            'min_order_value' => (float)($_POST['min_order_value'] ?? 0),
            'max_discount' => ($_POST['max_discount'] ?? '') !== '' ? (float)$_POST['max_discount'] : null,
            'quantity' => (int)($_POST['quantity'] ?? 0),
            'used_quantity' => (int)($_POST['used_quantity'] ?? 0),
            'start_date' => trim($_POST['start_date'] ?? ''),
            'end_date' => trim($_POST['end_date'] ?? ''),
            'status' => (int)($_POST['status'] ?? 1)
        ];
    }

    private function validateVoucher($data)
    {
        $errors = [];

        if (($data['code'] ?? '') === '') {
            $errors[] = 'Mã voucher không được để trống.';
        }

        if (($data['name'] ?? '') === '') {
            $errors[] = 'Tên voucher không được để trống.';
        }

        if (!in_array(($data['discount_type'] ?? ''), ['percent', 'fixed'], true)) {
            $errors[] = 'Loại giảm giá không hợp lệ.';
        }

        if (($data['discount_value'] ?? 0) <= 0) {
            $errors[] = 'Giá trị giảm phải lớn hơn 0.';
        }

        if (($data['discount_type'] ?? '') === 'percent' && $data['discount_value'] > 100) {
            $errors[] = 'Voucher phần trăm không được giảm quá 100%.';
        }

        if (($data['min_order_value'] ?? 0) < 0) {
            $errors[] = 'Giá trị đơn tối thiểu không được âm.';
        }

        if (($data['max_discount'] ?? null) !== null && $data['max_discount'] < 0) {
            $errors[] = 'Mức giảm tối đa không được âm.';
        }

        if (($data['quantity'] ?? 0) < 0) {
            $errors[] = 'Số lượng voucher không được âm.';
        }

        if (($data['used_quantity'] ?? 0) < 0) {
            $errors[] = 'Số lượng đã dùng không được âm.';
        }

        if (($data['quantity'] ?? 0) < ($data['used_quantity'] ?? 0)) {
            $errors[] = 'Số lượng voucher không được nhỏ hơn số lượng đã dùng.';
        }

        if (empty($data['start_date'])) {
            $errors[] = 'Vui lòng chọn ngày bắt đầu.';
        }

        if (empty($data['end_date'])) {
            $errors[] = 'Vui lòng chọn ngày kết thúc.';
        }

        if (!empty($data['start_date']) && !empty($data['end_date'])) {
            if (strtotime($data['start_date']) >= strtotime($data['end_date'])) {
                $errors[] = 'Ngày bắt đầu phải nhỏ hơn ngày kết thúc.';
            }
        }

        if (!in_array((int)($data['status'] ?? 1), [0, 1], true)) {
            $errors[] = 'Trạng thái voucher không hợp lệ.';
        }

        return $errors;
    }

    private function getMessage($key)
    {
        if ($key === '') {
            return '';
        }

        $messages = [
            'created' => 'Thêm voucher thành công.',
            'updated' => 'Cập nhật voucher thành công.',
            'locked' => 'Đã khóa voucher.',
            'unlocked' => 'Đã mở lại voucher.'
        ];

        return $messages[$key] ?? '';
    }

    private function getError($key)
    {
        if ($key === '') {
            return '';
        }

        $errors = [
            'invalid_id' => 'Mã voucher không hợp lệ.',
            'not_found' => 'Không tìm thấy voucher.',
            'method_not_allowed' => 'Thao tác không hợp lệ.',
            'expired' => 'Không thể mở voucher đã hết hạn.',
            'quantity_invalid' => 'Không thể mở voucher vì số lượng đã dùng lớn hơn hoặc bằng số lượng phát hành.',
            'update_failed' => 'Cập nhật voucher thất bại.'
        ];

        return $errors[$key] ?? 'Có lỗi xảy ra. Vui lòng kiểm tra lại.';
    }

    private function friendlyException($message)
    {
        if (stripos($message, 'Duplicate') !== false) {
            return 'Mã voucher đã tồn tại. Vui lòng dùng mã khác.';
        }

        return $message;
    }
}