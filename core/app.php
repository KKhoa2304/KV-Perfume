<?php

/**
 * Lớp App (Core Router / Front Controller)
 * Mục đích: Phân tích URL và điều hướng request tới đúng Controller và Action tương ứng.
 * Kiến trúc: Đóng vai trò là "trạm kiểm soát trung tâm" định tuyến mọi yêu cầu từ người dùng.
 */
class App
{
    protected $controller = 'HomeController';
    protected $action = 'index';
    protected $params = [];

    public function __construct()
    {
        $url = $this->UrlProcess();
        $isAdmin = false;

        // ==========================================================
        // 1. KIỂM TRA ĐỊNH TUYẾN ADMIN
        // Cắt phần tử đầu tiên để kiểm tra xem người dùng đang truy cập khu vực Admin hay Client
        // ==========================================================
        if (isset($url[0]) && strtolower($url[0]) === 'admin') {
            $isAdmin = true;
            unset($url[0]);
            $url = array_values($url); // Reset lại chỉ số mảng sau khi unset
        }

        // ==========================================================
        // 2. XÁC ĐỊNH VÀ KHỞI TẠO CONTROLLER
        // ==========================================================
        if (isset($url[0]) && $url[0] !== '') {
            // Viết hoa chữ cái đầu để khớp với chuẩn đặt tên class (VD: product -> ProductController)
            $controllerName = ucfirst($url[0]) . 'Controller';

            // Phân luồng thư mục dựa vào flag $isAdmin
            $controllerPath = $isAdmin
                ? './app/controller/admin/' . $controllerName . '.php'
                : './app/controller/Client/' . $controllerName . '.php';

            if (file_exists($controllerPath)) {
                require_once $controllerPath;
                $this->controller = $controllerName;
                unset($url[0]);
            } else {
                // TODO: Trong môi trường thực tế, nên render một View 404 Not Found thay vì dùng die() để UX tốt hơn.
                die('Không tìm thấy controller: ' . $controllerPath);
            }
        } else {
            // Load Controller mặc định nếu URL trống
            $controllerPath = './app/controller/Client/' . $this->controller . '.php';

            if (file_exists($controllerPath)) {
                require_once $controllerPath;
            }
        }

        // Khởi tạo đối tượng Controller tương ứng
        $this->controller = new $this->controller();

        // ==========================================================
        // 3. XÁC ĐỊNH ACTION (PHƯƠNG THỨC TRONG CONTROLLER)
        // ==========================================================
        if (isset($url[1])) {
            if (method_exists($this->controller, $url[1])) {
                $this->action = $url[1];
                unset($url[1]);
            }
        }

        // ==========================================================
        // 4. XÁC ĐỊNH THAM SỐ (PARAMETERS) VÀ THỰC THI
        // ==========================================================
        $this->params = $url ? array_values($url) : [];

        // Gọi hàm của Controller đã được khởi tạo và truyền mảng tham số vào
        call_user_func_array([$this->controller, $this->action], $this->params);
    }

    /**
     * Tiện ích xử lý URL: Lấy chuỗi query 'url', làm sạch (sanitize) và cắt thành mảng
     * VD: ?url=product/detail/1  ->  ['product', 'detail', '1']
     */
    protected function UrlProcess()
    {
        if (isset($_GET['url'])) {
            return explode('/', filter_var(trim($_GET['url'], '/'), FILTER_SANITIZE_URL));
        }

        return [];
    }
}