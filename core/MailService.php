<?php
/**
 * Lớp MailService (Dịch vụ gửi Email)
 * Mục đích: Xử lý logic gửi email thông báo (Xác nhận đơn hàng cho khách và Thông báo có đơn mới cho Admin).
 * Sử dụng thư viện: PHPMailer
 */

require_once __DIR__ . '/PHPMailer/Exception.php';
require_once __DIR__ . '/PHPMailer/PHPMailer.php';
require_once __DIR__ . '/PHPMailer/SMTP.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

class MailService {
    
    /**
     * Gửi email xác nhận đơn hàng cho Khách hàng (Giao diện chuẩn Couture)
     */
    public static function sendOrderConfirmation($toEmail, $customerName, $orderCode, $totalAmount) {
        $mail = new PHPMailer(true);

        try {
            // Cấu hình SMTP
            $mail->isSMTP();
            $mail->Host       = 'smtp.gmail.com';
            $mail->SMTPAuth   = true;
            $mail->Username   = 'kvperfume.shop@gmail.com'; // TODO: Đưa vào file config/.env
            $mail->Password   = 'jrohsvnriehwrurk';         // TODO: Đưa vào file config/.env
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
            $mail->Port       = 465;
            $mail->CharSet    = 'UTF-8';

            // Cấu hình người gửi & nhận
            $mail->setFrom('kvperfume.shop@gmail.com', 'KV PERFUME BOUTIQUE');
            $mail->addAddress($toEmail, $customerName);

            // Cấu hình nội dung Email
            $mail->isHTML(true);
            $mail->Subject = 'KV PERFUME - Xác nhận đơn hàng #' . $orderCode;
            
            $formattedTotal = number_format($totalAmount, 0, ',', '.') . ' VNĐ';

            $mail->Body = "
            <div style='font-family: Arial, sans-serif; max-width: 600px; margin: 0 auto; border: 1px solid #ece8e2; background-color: #ffffff;'>
                <div style='background-color: #111111; padding: 30px; text-align: center;'>
                    <h1 style='color: #ffffff; margin: 0; font-size: 28px; letter-spacing: 4px; font-weight: normal;'>KV PERFUME</h1>
                    <p style='color: #a98a52; margin: 10px 0 0 0; font-size: 12px; letter-spacing: 2px; text-transform: uppercase;'>Luxury Fragrance Boutique</p>
                </div>
                
                <div style='padding: 40px 30px; color: #333333; line-height: 1.6;'>
                    <h2 style='font-size: 18px; color: #111111; border-bottom: 1px solid #ece8e2; padding-bottom: 15px; text-transform: uppercase; letter-spacing: 1px;'>Xác nhận đơn hàng</h2>
                    <p>Kính chào quý khách <strong>$customerName</strong>,</p>
                    <p>Cảm ơn quý khách đã tin tưởng và lựa chọn những tuyệt tác mùi hương tại KV PERFUME. Đơn hàng của quý khách đã được hệ thống ghi nhận thành công.</p>
                    
                    <div style='background-color: #fbfbfa; border: 1px solid #ece8e2; padding: 20px; margin: 30px 0;'>
                        <p style='margin: 0 0 10px 0;'><strong>Mã đơn hàng:</strong> <span style='color: #a98a52;'>#$orderCode</span></p>
                        <p style='margin: 0;'><strong>Tổng thanh toán:</strong> <span style='font-size: 18px; font-weight: bold; color: #111111;'>$formattedTotal</span></p>
                    </div>
                    
                    <p>Chúng tôi sẽ tiến hành đóng gói tiêu chuẩn couture và sớm liên hệ với quý khách để giao hàng.</p>
                    <p>Trân trọng,<br><strong>Đội ngũ KV PERFUME</strong></p>
                </div>
                
                <div style='background-color: #f6f5f3; padding: 20px; text-align: center; border-top: 1px solid #ece8e2;'>
                    <p style='color: #777777; font-size: 11px; margin: 0; letter-spacing: 1px;'>613 Âu Cơ, Phường Phú Trung, Quận Tân Phú, TP. Hồ Chí Minh</p>
                    <p style='color: #777777; font-size: 11px; margin: 5px 0 0 0; letter-spacing: 1px;'>Hotline: 0869 058 393</p>
                </div>
            </div>";

            $mail->send();
            return true;
        } catch (Exception $e) {
            return false;
        }
    }

    /**
     * Gửi email thông báo "Nổ đơn" cho Admin (Giao diện tối giản, tập trung dữ liệu)
     */
    public static function sendAdminOrderNotification($orderCode, $customerName, $customerPhone, $customerAddress, $totalAmount) {
        $mail = new PHPMailer(true);

        try {
            // Cấu hình SMTP
            $mail->isSMTP();
            $mail->Host       = 'smtp.gmail.com';
            $mail->SMTPAuth   = true;
            $mail->Username   = 'kvperfume.shop@gmail.com'; 
            $mail->Password   = 'jrohsvnriehwrurk'; 
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
            $mail->Port       = 465;
            $mail->CharSet    = 'UTF-8';

            // Cấu hình người gửi & nhận
            $mail->setFrom('kvperfume.shop@gmail.com', 'KV Perfume System');
            $mail->addAddress('kvperfume.shop@gmail.com', 'Chủ Shop'); // Có thể đổi thành email cá nhân của Admin

            // Cấu hình nội dung Email
            $mail->isHTML(true);
            $mail->Subject = '🎉 [ĐƠN HÀNG MỚI] Đơn hàng #' . $orderCode;
            
            $formattedTotal = number_format($totalAmount, 0, ',', '.') . ' VNĐ';

            $mail->Body = "
            <div style='font-family: Arial, sans-serif; max-width: 600px; margin: 0 auto; border: 1px solid #ddd; padding: 20px;'>
                <h2 style='color: #2e7d32; text-align: center;'>🎉 CHÚC MỪNG BẠN VỪA NỔ ĐƠN!</h2>
                <hr style='border: 0; border-top: 1px solid #eee; margin: 20px 0;'>
                
                <p><strong>Mã đơn hàng:</strong> <span style='color: #a98a52;'>#$orderCode</span></p>
                <p><strong>Tổng giá trị:</strong> <span style='font-size: 18px; font-weight: bold; color: #b71c1c;'>$formattedTotal</span></p>
                
                <h3 style='background: #f4f4f4; padding: 10px; margin-top: 30px;'>THÔNG TIN GIAO HÀNG</h3>
                <ul style='line-height: 1.8;'>
                    <li><strong>Khách hàng:</strong> $customerName</li>
                    <li><strong>Số điện thoại:</strong> $customerPhone</li>
                    <li><strong>Địa chỉ:</strong> $customerAddress</li>
                </ul>
                <p style='text-align: center; margin-top: 30px; font-size: 12px; color: #777;'>
                    Hãy đăng nhập vào trang quản trị (Admin) để xử lý đơn hàng này nhé!
                </p>
            </div>";

            $mail->send();
            return true;
        } catch (Exception $e) {
            return false;
        }
    }

    /**
     * Gửi email thông báo cho Admin khi khách hàng điền form Liên hệ
     */
    /**
     * Gửi email thông báo cho Admin khi khách hàng điền form Liên hệ
     */
    public static function sendContactNotification($customerName, $customerPhone, $customerEmail, $requestType, $messageContent) {
        $mail = new PHPMailer(true);

        try {
            // Cấu hình SMTP
            $mail->isSMTP();
            $mail->Host       = 'smtp.gmail.com';
            $mail->SMTPAuth   = true;
            $mail->Username   = 'kvperfume.shop@gmail.com'; 
            $mail->Password   = 'jrohsvnriehwrurk'; 
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
            $mail->Port       = 465;
            $mail->CharSet    = 'UTF-8';

            // Cấu hình người gửi & nhận
            $mail->setFrom('kvperfume.shop@gmail.com', 'KV Perfume System');
            $mail->addAddress('kvperfume.shop@gmail.com', 'Admin KV Perfume'); 
            
            // Thêm Reply-To để Admin có thể nhấn "Reply" (Trả lời) và gửi thẳng lại cho email của khách
            $mail->addReplyTo($customerEmail, $customerName);

            // Cấu hình nội dung Email
            $mail->isHTML(true);
            $mail->Subject = '📬 [LIÊN HỆ MỚI] Yêu cầu từ: ' . $customerName;

            // Xử lý xuống dòng cho nội dung tin nhắn
            $formattedMessage = nl2br(htmlspecialchars($messageContent));
            
            // Lấy thời gian hiện tại
            date_default_timezone_set('Asia/Ho_Chi_Minh');
            $currentTime = date('H:i - d/m/Y');

            $mail->Body = "
            <div style='font-family: Arial, sans-serif; max-width: 600px; margin: 0 auto; border: 1px solid #e0e0e0; border-radius: 8px; overflow: hidden;'>
                <div style='background-color: #111; padding: 20px; text-align: center;'>
                    <h2 style='color: #a98a52; margin: 0; text-transform: uppercase; letter-spacing: 2px; font-size: 20px;'>YÊU CẦU LIÊN HỆ MỚI</h2>
                </div>
                
                <div style='padding: 30px; background-color: #fafafa;'>
                    <div style='background-color: #fff; padding: 20px; border-radius: 6px; border: 1px solid #eee; margin-bottom: 20px;'>
                        <h3 style='margin-top: 0; color: #333; font-size: 16px; border-bottom: 1px solid #eee; padding-bottom: 10px;'>THÔNG TIN KHÁCH HÀNG</h3>
                        <p style='margin: 10px 0; color: #555;'><strong>👤 Họ và tên:</strong> $customerName</p>
                        <p style='margin: 10px 0; color: #555;'><strong>📞 Số điện thoại:</strong> <a href='tel:$customerPhone' style='color: #a98a52; text-decoration: none;'>$customerPhone</a></p>
                        <p style='margin: 10px 0; color: #555;'><strong>✉️ Email:</strong> <a href='mailto:$customerEmail' style='color: #a98a52; text-decoration: none;'>$customerEmail</a></p>
                        <p style='margin: 10px 0; color: #555;'><strong>🕒 Thời gian gửi:</strong> $currentTime</p>
                    </div>

                    <div style='background-color: #fff; padding: 20px; border-radius: 6px; border: 1px solid #eee;'>
                        <h3 style='margin-top: 0; color: #333; font-size: 16px; border-bottom: 1px solid #eee; padding-bottom: 10px;'>NỘI DUNG YÊU CẦU</h3>
                        <p style='margin: 10px 0; color: #555;'><strong>🏷️ Phân loại:</strong> <span style='display: inline-block; background: #eef2f5; padding: 4px 8px; border-radius: 4px; font-size: 13px; font-weight: bold; color: #111;'>$requestType</span></p>
                        <p style='margin: 15px 0 5px 0; color: #555;'><strong>📝 Chi tiết lời nhắn:</strong></p>
                        <div style='background-color: #f8f9fa; padding: 15px; border-left: 4px solid #a98a52; font-style: italic; color: #444; line-height: 1.6;'>
                            $formattedMessage
                        </div>
                    </div>
                    
                    <div style='text-align: center; margin-top: 30px;'>
                        <a href='http://localhost/KV_Perfume-main/?url=admin/contact' style='display: inline-block; background-color: #111; color: #a98a52; text-decoration: none; padding: 12px 25px; font-weight: bold; letter-spacing: 1px; border-radius: 4px; font-size: 14px; border: 1px solid #111;'>
                            XEM TRONG ADMIN
                        </a>
                    </div>
                </div>
                
                <div style='background-color: #eee; padding: 15px; text-align: center; font-size: 12px; color: #777;'>
                    Email này được gửi tự động từ hệ thống KV Perfume Boutique.
                </div>
            </div>";

            $mail->send();
            return true;
        } catch (Exception $e) {
            return false;
        }
    }
}
?>