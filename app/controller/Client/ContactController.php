<?php

// 1. Nhúng file MailService vào để sử dụng. 
require_once __DIR__ . '/../../../core/MailService.php'; // Lưu ý: Giữ lại đường dẫn đúng tới file MailService của bạn

// Điều hướng và xử lý hiển thị trang Liên hệ & Chăm sóc khách hàng phía Client
class ContactController extends Controller 
{
    /**
     * Hiển thị giao diện trang liên hệ
     * URL: ?url=contact
     */
    public function index() 
    {
        $this->render('client/contact');
    }

    /**
     * Xử lý dữ liệu khi khách hàng nhấn nút "GỬI THÔNG TIN"
     * URL: ?url=contact/submit
     */
    public function submit() 
    {
        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            // Lấy dữ liệu từ form dựa theo thuộc tính 'name' trong các thẻ HTML của view
            $name    = $_POST['name'] ?? '';
            $phone   = $_POST['phone'] ?? '';
            $email   = $_POST['email'] ?? '';
            $subject = $_POST['subject'] ?? 'Không xác định';
            $message = $_POST['message'] ?? '';

            // Chuyển đổi giá trị (value) của thẻ select sang tiếng Việt cho nội dung email đẹp hơn
            $subjectText = "Không xác định";
            if ($subject === 'appointment') {
                $subjectText = "Đặt lịch hẹn tư vấn mùi hương";
            } elseif ($subject === 'order') {
                $subjectText = "Hỗ trợ thông tin đơn hàng";
            } elseif ($subject === 'business') {
                $subjectText = "Hợp tác doanh nghiệp (B2B)";
            } elseif ($subject === 'other') {
                $subjectText = "Ý kiến đóng góp khác";
            }

            // ================================================================
            // BƯỚC 1: LƯU THÔNG TIN KHÁCH HÀNG VÀO DATABASE (BẢNG CONTACTS)
            // ================================================================
            
            // ĐÃ FIX LỖI: Nhúng trực tiếp file Model và gọi bằng từ khóa 'new' để không bị lỗi undefined method model()
            require_once __DIR__ . '/../../models/ContactModel.php'; 
            $contactModel = new ContactModel();
            
            $contactModel->insertContact($name, $phone, $email, $subjectText, $message);


            // ================================================================
            // BƯỚC 2: GỬI EMAIL THÔNG BÁO CHO ADMIN
            // ================================================================
            $isSent = MailService::sendContactNotification(
                $name, 
                $phone, 
                $email, 
                $subjectText,
                $message
            );

            if ($isSent) {
                // Nếu gửi mail thành công: Hiện thông báo và quay trở lại trang liên hệ
                echo "<script>
                        alert('Gửi yêu cầu thành công! KV Perfume sẽ sớm liên hệ lại với quý khách.');
                        window.location.href = '?url=contact';
                      </script>";
            } else {
                // Nếu gửi mail thất bại (nhưng vẫn lưu vào Database thành công)
                echo "<script>
                        alert('Có lỗi xảy ra khi gửi email tự động, nhưng chúng tôi đã ghi nhận yêu cầu của quý khách.');
                        window.location.href = '?url=contact';
                      </script>";
            }
        } else {
            // Nếu ai đó truy cập trực tiếp bằng phương thức GET vào đường dẫn này, tự động đá về trang liên hệ
            header('Location: ?url=contact');
            exit();
        }
    }
}
?>