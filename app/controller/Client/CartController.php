<?php

require_once __DIR__ . '/../../../core/Controller.php';
require_once __DIR__ . '/../../models/ProductModel.php';
require_once __DIR__ . '/../../models/OrderModel.php';
require_once __DIR__ . '/../../models/VoucherModel.php';
require_once __DIR__ . '/../../../core/MailService.php';

class CartController extends Controller
{
    private OrderModel $orderModel;
    private VoucherModel $voucherModel;

    public function __construct()
    {
        if (session_status() === PHP_SESSION_NONE) session_start();
        if (!isset($_SESSION['cart'])) $_SESSION['cart'] = [];

        $this->orderModel = new OrderModel();
        $this->voucherModel = new VoucherModel();
    }

    public function add()
    {
        $id = (int)($_GET['id'] ?? 0);
        if ($id <= 0) $this->redirectBackWithCart();

        $productModel = new ProductModel();
        $product = $productModel->getProductById($id);

        if (!$product) {
            $_SESSION['cart_error'] = 'Sản phẩm không tồn tại.';
            $this->redirectBackWithCart();
        }

        $stock = (int)($product['stock'] ?? 0);
        if ($stock <= 0) {
            $_SESSION['cart_error'] = 'Sản phẩm "' . $product['name'] . '" đã hết hàng.';
            $this->redirectBackWithCart();
        }

        $currentQuantity = isset($_SESSION['cart'][$id]) ? (int)$_SESSION['cart'][$id]['quantity'] : 0;

        if ($currentQuantity + 1 > $stock) {
            $_SESSION['cart_error'] = 'Sản phẩm "' . $product['name'] . '" chỉ còn ' . $stock . ' sản phẩm trong kho.';
            $_SESSION['cart'][$id]['quantity'] = $stock;
            $this->redirectBackWithCart();
        }

        if (isset($_SESSION['cart'][$id])) {
            $_SESSION['cart'][$id]['quantity']++;
            $_SESSION['cart'][$id]['image'] = $this->normalizeImagePath($product['main_image'] ?? '');
        } else {
            $price = !empty($product['sale_price']) ? $product['sale_price'] : $product['price'];

            $_SESSION['cart'][$id] = [
                'id' => (int)$product['id'],
                'name' => $product['name'],
                'price' => (float)$price,
                'image' => $this->normalizeImagePath($product['main_image'] ?? ''),
                'quantity' => 1
            ];
        }

        $this->redirectBackWithCart();
    }

    public function update()
    {
        $id = (int)($_GET['id'] ?? 0);
        $amount = (int)($_GET['amount'] ?? 0);

        if (isset($_SESSION['cart'][$id])) {
            $productModel = new ProductModel();
            $product = $productModel->getProductById($id);
            $stock = (int)($product['stock'] ?? 0);
            $productName = $product['name'] ?? ($_SESSION['cart'][$id]['name'] ?? 'Sản phẩm');

            $newQuantity = (int)$_SESSION['cart'][$id]['quantity'] + $amount;

            if ($newQuantity <= 0) {
                unset($_SESSION['cart'][$id]);
            } elseif ($newQuantity > $stock) {
                $_SESSION['cart_error'] = 'Sản phẩm "' . $productName . '" chỉ còn ' . $stock . ' sản phẩm trong kho.';
                $_SESSION['cart'][$id]['quantity'] = max(1, $stock);
            } else {
                $_SESSION['cart'][$id]['quantity'] = $newQuantity;
            }
        }

        $this->redirectBackWithCart();
    }

    public function setQuantity()
    {
        $id = (int)($_POST['id'] ?? 0);
        $quantity = (int)($_POST['quantity'] ?? 1);

        if (isset($_SESSION['cart'][$id])) {
            $productModel = new ProductModel();
            $product = $productModel->getProductById($id);
            $stock = (int)($product['stock'] ?? 0);
            $productName = $product['name'] ?? ($_SESSION['cart'][$id]['name'] ?? 'Sản phẩm');

            if ($quantity <= 0) {
                unset($_SESSION['cart'][$id]);
            } elseif ($quantity > $stock) {
                $_SESSION['cart_error'] = 'Sản phẩm "' . $productName . '" chỉ còn ' . $stock . ' sản phẩm trong kho.';
                $_SESSION['cart'][$id]['quantity'] = max(1, $stock);
            } else {
                $_SESSION['cart'][$id]['quantity'] = $quantity;
            }
        }

        $this->redirectBackWithCart();
    }

    public function remove()
    {
        $id = (int)($_GET['id'] ?? 0);
        unset($_SESSION['cart'][$id]);
        $this->redirectBackWithCart();
    }

    public function checkout()
    {
        if (empty($_SESSION['user'])) {
            $_SESSION['redirect_to'] = '?url=cart/checkout';
            $_SESSION['login_error'] = 'Vui lòng đăng nhập để thanh toán.';
            header('Location: ?url=account');
            exit();
        }

        if (empty($_SESSION['cart'])) {
            $this->render('client/Checkout', [
                'error' => 'Giỏ hàng của bạn đang trống.',
                'cart' => [],
                'total' => 0,
                'discount' => 0,
                'finalTotal' => 0,
                'step' => '1',
                'formData' => []
            ]);
            return;
        }

        $step = $_GET['step'] ?? '1';

        if (!isset($_SESSION['checkout_form'])) {
            $_SESSION['checkout_form'] = [
                'fullname' => $_SESSION['user']['name'] ?? '',
                'phone' => $_SESSION['user']['phone'] ?? '',
                'address' => '',
                'paymentMethod' => 'cod',
                'voucher_code' => ''
            ];
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $_SESSION['checkout_form']['fullname'] = trim($_POST['fullname'] ?? $_SESSION['checkout_form']['fullname']);
            $_SESSION['checkout_form']['phone'] = trim($_POST['phone'] ?? $_SESSION['checkout_form']['phone']);
            $_SESSION['checkout_form']['address'] = trim($_POST['address'] ?? $_SESSION['checkout_form']['address']);
            $_SESSION['checkout_form']['paymentMethod'] = $_POST['paymentMethod'] ?? $_SESSION['checkout_form']['paymentMethod'];
            $_SESSION['checkout_form']['voucher_code'] = trim($_POST['voucher_code'] ?? $_SESSION['checkout_form']['voucher_code']);

            $paymentMethod = $_SESSION['checkout_form']['paymentMethod'];

            if ($step === '1') {
                header('Location: ?url=cart/checkout&step=2');
                exit();
            }

            if ($step === '2') {
                if ($paymentMethod === 'bank' || $paymentMethod === 'bank_transfer') {
                    header('Location: ?url=cart/checkout&step=2.5');
                    exit();
                }

                header('Location: ?url=cart/checkout&step=3');
                exit();
            }

            if ($step === '2.5') {
                header('Location: ?url=cart/checkout&step=3');
                exit();
            }
        }

        $total = $this->calculateTotal();
        $voucher = null;
        $discount = 0;

        if (!empty($_SESSION['checkout_form']['voucher_code'])) {
            $voucher = $this->voucherModel->getValidVoucher($_SESSION['checkout_form']['voucher_code'], $total);
            $discount = (float)($voucher['discount_amount'] ?? 0);
        }

        $this->render('client/Checkout', [
            'step' => $step,
            'formData' => $_SESSION['checkout_form'],
            'cart' => $_SESSION['cart'],
            'total' => $total,
            'discount' => $discount,
            'finalTotal' => max(0, $total - $discount),
            'error' => ''
        ]);
    }

    public function submitOrder()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST' || empty($_SESSION['cart']) || empty($_SESSION['user'])) {
            header('Location: ?url=cart/checkout');
            exit();
        }

        $userId = $_SESSION['user']['id'] ?? null;

        if (!$userId) {
            die('Không tìm thấy ID người dùng. Vui lòng đăng nhập lại.');
        }

        $formData = $_SESSION['checkout_form'] ?? [];
        $formData['fullname'] = trim($_POST['fullname'] ?? $formData['fullname'] ?? '');
        $formData['phone'] = trim($_POST['phone'] ?? $formData['phone'] ?? '');
        $formData['address'] = trim($_POST['address'] ?? $formData['address'] ?? '');
        $formData['paymentMethod'] = $_POST['paymentMethod'] ?? ($formData['paymentMethod'] ?? 'cod');
        $formData['voucher_code'] = trim($_POST['voucher_code'] ?? ($formData['voucher_code'] ?? ''));

        if ($formData['fullname'] === '' || $formData['phone'] === '' || $formData['address'] === '') {
            $_SESSION['checkout_form'] = $formData;
            $_SESSION['checkout_error'] = 'Vui lòng nhập đầy đủ họ tên, số điện thoại và địa chỉ.';
            header('Location: ?url=cart/checkout&step=3');
            exit();
        }

        $_SESSION['checkout_form'] = $formData;

        if (!$this->validateCartStock('?url=cart/checkout&step=3')) exit();

        $_POST['receiver_name'] = $formData['fullname'];
        $_POST['receiver_phone'] = $formData['phone'];
        $_POST['receiver_address'] = $formData['address'];

        $total = $this->calculateTotal();
        $voucher = null;

        if (!empty($formData['voucher_code'])) {
            $voucher = $this->voucherModel->getValidVoucher($formData['voucher_code'], $total);
        }

        $orderResult = $this->orderModel->saveOrder(
            (int)$userId,
            $formData['paymentMethod'],
            $_SESSION['cart'],
            $total,
            $voucher
        );

        if ($orderResult) {
            $customerEmail = $_SESSION['user']['email'] ?? '';
            $customerName = $_POST['receiver_name'] ?? '';

            $orderCode = is_array($orderResult)
                ? $orderResult['code']
                : 'KV' . str_pad((string)$orderResult, 5, '0', STR_PAD_LEFT);

            if (!empty($customerEmail)) {
                MailService::sendOrderConfirmation($customerEmail, $customerName, $orderCode, $total);
            }

            MailService::sendAdminOrderNotification(
                $orderCode,
                $customerName,
                $formData['phone'],
                $formData['address'],
                $total
            );

            $_SESSION['cart'] = [];
            unset($_SESSION['checkout_form'], $_SESSION['checkout_error'], $_SESSION['cart_error']);

            header('Location: ?url=account&order_success=1');
            exit();
        }

        $_SESSION['checkout_error'] = $_SESSION['checkout_error'] ?? 'Tạo đơn hàng thất bại.';
        header('Location: ?url=cart/checkout&step=3');
        exit();
    }

    private function validateCartStock(string $redirectUrl): bool
    {
        $productModel = new ProductModel();

        foreach ($_SESSION['cart'] as $id => $item) {
            $productId = (int)($item['id'] ?? $item['product_id'] ?? $id);
            $quantity = (int)($item['quantity'] ?? 1);

            $product = $productModel->getProductById($productId);

            if (!$product) {
                unset($_SESSION['cart'][$id]);
                $_SESSION['checkout_error'] = 'Có sản phẩm không tồn tại trong giỏ hàng.';
                header('Location: ' . $redirectUrl);
                return false;
            }

            $stock = (int)($product['stock'] ?? 0);
            $productName = $product['name'] ?? ($item['name'] ?? 'Sản phẩm');

            if ($stock <= 0) {
                unset($_SESSION['cart'][$id]);
                $_SESSION['checkout_error'] = 'Sản phẩm "' . $productName . '" đã hết hàng và đã được xóa khỏi giỏ.';
                header('Location: ' . $redirectUrl);
                return false;
            }

            if ($quantity > $stock) {
                $_SESSION['cart'][$id]['quantity'] = $stock;
                $_SESSION['checkout_error'] = 'Sản phẩm "' . $productName . '" chỉ còn ' . $stock . ' sản phẩm trong kho. Hệ thống đã tự chỉnh số lượng về ' . $stock . '.';
                header('Location: ' . $redirectUrl);
                return false;
            }
        }

        return true;
    }

    private function calculateTotal(): float
    {
        $total = 0;
        foreach ($_SESSION['cart'] as $item) {
            $total += (float)$item['price'] * (int)$item['quantity'];
        }
        return $total;
    }

    private function normalizeImagePath($path): string
    {
        $path = trim((string)$path);

        if ($path === '') return '/public/upload/products/default.webp';

        $path = str_replace('\\', '/', $path);

        if (preg_match('#^https?://#i', $path)) return $path;

        $path = preg_replace('#/+#', '/', $path);
        $path = preg_replace('#^/+#', '', $path);

        $path = str_ireplace('CHANEL.VN-MAIN/', '', $path);
        $path = str_ireplace('KV_Perfume_InfinityFree/', '', $path);
        $path = str_ireplace('KV-PERFUME/', '', $path);

        while (strpos($path, 'public/upload/public/upload/') !== false) {
            $path = str_replace('public/upload/public/upload/', 'public/upload/', $path);
        }

        while (strpos($path, 'upload/public/upload/') !== false) {
            $path = str_replace('upload/public/upload/', 'public/upload/', $path);
        }

        if (strpos($path, 'public/upload/') !== false) {
            $parts = explode('public/upload/', $path);
            return '/public/upload/' . basename(end($parts));
        }

        return '/public/upload/' . basename($path);
    }

    private function redirectBackWithCart(): void
    {
        $url = $_SERVER['HTTP_REFERER'] ?? '?url=home';
        $separator = strpos($url, '?') !== false ? '&' : '?';

        if (strpos($url, 'open_cart=') === false) {
            $url .= $separator . 'open_cart=1';
        }

        header('Location: ' . $url);
        exit();
    }
}