<?php

/**
 * Lớp Controller (Base Controller)
 * Đây là class cha mà tất cả các Controller khác (như ContactController, ProductController...) sẽ kế thừa.
 * Chịu trách nhiệm chính trong việc xử lý đường dẫn và nhúng (render) giao diện (View).
 */
class Controller
{
    /**
     * Lấy đường dẫn gốc của thư mục app/
     * (Viết thành hàm riêng để tái sử dụng, tuân thủ nguyên tắc DRY - Don't Repeat Yourself)
     */
    protected function getAppPath()
    {
        // dirname(__DIR__) trả về thư mục gốc của dự án (ví dụ: KV_Perfume-main)
        return dirname(__DIR__) . '/app';
    }

    /**
     * Render giao diện cho khu vực Khách hàng (Client)
     * * @param string $view Đường dẫn file view (VD: 'client/contact')
     * @param array $data Mảng dữ liệu truyền từ Controller sang View
     * @throws Exception Nếu không tìm thấy file View
     */
    protected function render($view, $data = [])
    {
        // Giải nén mảng thành các biến độc lập (VD: ['contacts' => $list] sẽ thành biến $contacts)
        extract($data);

        $appPath = $this->getAppPath();
        
        $viewPath   = $appPath . '/views/' . $view . '.php';
        $headerPath = $appPath . '/views/client/Layouts/Header.php';
        $footerPath = $appPath . '/views/client/Layouts/Footer.php';

        // Thay vì dùng die(), ném ra Exception sẽ giúp hệ thống bắt lỗi và log lại chuyên nghiệp hơn
        if (!file_exists($viewPath)) {
            throw new Exception("Lỗi hệ thống: Không tìm thấy giao diện Client tại " . $viewPath);
        }

        // Kiểm tra sự tồn tại của Header/Footer trước khi nhúng để tránh lỗi trắng trang nếu lỡ xóa nhầm file
        if (file_exists($headerPath)) require_once $headerPath;
        
        require_once $viewPath;
        
        if (file_exists($footerPath)) require_once $footerPath;
    }

    /**
     * Render giao diện cho khu vực Quản trị (Admin)
     * * @param string $view Đường dẫn file view (VD: 'admin/contact/index')
     * @param array $data Mảng dữ liệu truyền từ Controller sang View
     * @throws Exception Nếu không tìm thấy file View
     */
    protected function renderAdmin($view, $data = [])
    {
        extract($data);

        $appPath = $this->getAppPath();
        
        $viewPath = $appPath . '/views/' . $view . '.php';

        // Đường dẫn mặc định (Kiểu đặt tên PascalCase: 'Layouts')
        $headerPath = $appPath . '/views/admin/Layouts/Header.php';
        $footerPath = $appPath . '/views/admin/Layouts/Footer.php';

        // Fallback (Dự phòng): Nếu hệ điều hành (như Linux) phân biệt chữ hoa/thường 
        // và không tìm thấy thư mục 'Layouts', hệ thống sẽ tự động tìm thư mục 'layouts'
        if (!file_exists($headerPath)) {
            $headerPath = $appPath . '/views/admin/layouts/Header.php';
        }
        if (!file_exists($footerPath)) {
            $footerPath = $appPath . '/views/admin/layouts/Footer.php';
        }

        if (!file_exists($viewPath)) {
            throw new Exception("Lỗi hệ thống: Không tìm thấy giao diện Admin tại " . $viewPath);
        }

        // Nhúng tuần tự: Header -> Nội dung chính -> Footer
        if (file_exists($headerPath)) require_once $headerPath;
        
        require_once $viewPath;
        
        if (file_exists($footerPath)) require_once $footerPath;
    }
}
?>