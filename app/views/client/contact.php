<?php
/**
 * View: Liên hệ & Đặt lịch hẹn
 * * TODO (Backend Integration):
 * 1. Triển khai ContactController::submit() để nhận dữ liệu từ $_POST['name'], $_POST['phone']...
 * 2. Tích hợp MailService để gửi email yêu cầu về cho địa chỉ admin (ví dụ: contact@kvperfume.vn)
 */
?>
<div class="contact-page-wrapper" style="padding-top: 120px; padding-bottom: 100px; background-color: #ffffff;">
    
    <div style="text-align: center; margin-bottom: 70px;">
        <span style="font-family: var(--font-sans); font-size: 11px; font-weight: 600; letter-spacing: 4px; color: var(--accent-gold); text-transform: uppercase; display: block; margin-bottom: 15px;">
            Dịch vụ Chăm sóc Khách hàng
        </span>
        <h1 style="font-family: var(--font-serif); font-size: 32px; letter-spacing: 4px; color: var(--primary-black); text-transform: uppercase;">
            LIÊN HỆ & ĐẶT LỊCH HẸN
        </h1>
        <div style="width: 50px; height: 1px; background-color: var(--primary-black); margin: 30px auto 0 auto;"></div>
    </div>

    <div class="container" style="max-width: 1100px; margin: 0 auto; padding: 0 20px;">
        <div style="display: grid; grid-template-columns: 1fr 1.2fr; gap: 80px;">
            
            <div class="contact-info" style="font-family: var(--font-sans);">
                <h3 style="font-family: var(--font-serif); font-size: 20px; letter-spacing: 2px; text-transform: uppercase; margin-bottom: 30px; color: var(--primary-black);">
                    KV PERFUME BOUTIQUE
                </h3>
                
                <p style="font-size: 14px; color: #555; line-height: 1.8; margin-bottom: 30px;">
                    Không gian trưng bày xa xỉ của chúng tôi luôn sẵn sàng chào đón quý khách đến để trải nghiệm trực tiếp những kiệt tác mùi hương và nhận tư vấn chuyên sâu từ các chuyên gia khứu giác.
                </p>

                <div style="margin-bottom: 25px;">
                    <strong style="font-size: 12px; letter-spacing: 1px; text-transform: uppercase; color: var(--primary-black); display: block; margin-bottom: 8px;">📍 Địa chỉ Flagship Store:</strong>
                    <span style="font-size: 14px; color: #666;">613 Âu Cơ, Phường Phú Trung, Quận Tân Phú, TP. Hồ Chí Minh</span>
                </div>

                <div style="margin-bottom: 25px;">
                    <strong style="font-size: 12px; letter-spacing: 1px; text-transform: uppercase; color: var(--primary-black); display: block; margin-bottom: 8px;">📞 Hotline Đặc Quyền VIP:</strong>
                    <span style="font-size: 14px; color: #666;">090 123 4567</span>
                </div>

                <div style="margin-bottom: 25px;">
                    <strong style="font-size: 12px; letter-spacing: 1px; text-transform: uppercase; color: var(--primary-black); display: block; margin-bottom: 8px;">✉️ Email Khách Hàng:</strong>
                    <span style="font-size: 14px; color: #666;">contact@kvperfume.vn</span>
                </div>

                <div style="margin-bottom: 25px;">
                    <strong style="font-size: 12px; letter-spacing: 1px; text-transform: uppercase; color: var(--primary-black); display: block; margin-bottom: 8px;">🕒 Giờ Mở Cửa:</strong>
                    <span style="font-size: 14px; color: #666;">09:00 - 22:00 (Xuyên suốt các ngày trong tuần)</span>
                </div>
            </div>

            <div class="contact-form-wrapper" style="background-color: var(--luxury-gray-soft); padding: 40px; border: 1px solid var(--border-light);">
                <h3 style="font-family: var(--font-serif); font-size: 18px; letter-spacing: 2px; text-transform: uppercase; margin-bottom: 25px; color: var(--primary-black); text-align: center;">
                    Gửi Yêu Cầu Cho Chúng Tôi
                </h3>
                
                <form action="?url=contact/submit" method="POST" style="display: flex; flex-direction: column; gap: 20px;">
                    <div class="form-group">
                        <label class="form-label" style="font-size: 10px; color: var(--text-muted);">HỌ VÀ TÊN TRỌN VẸN *</label>
                        <input type="text" name="name" class="form-input" style="background: #ffffff; border: 1px solid #ddd;" placeholder="VD: Nguyễn Văn A" required />
                    </div>
                    
                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
                        <div class="form-group">
                            <label class="form-label" style="font-size: 10px; color: var(--text-muted);">SỐ ĐIỆN THOẠI *</label>
                            <input type="tel" name="phone" class="form-input" style="background: #ffffff; border: 1px solid #ddd;" placeholder="VD: 090xxxxxxx" required />
                        </div>
                        <div class="form-group">
                            <label class="form-label" style="font-size: 10px; color: var(--text-muted);">EMAIL *</label>
                            <input type="email" name="email" class="form-input" style="background: #ffffff; border: 1px solid #ddd;" placeholder="VD: email@domain.com" required />
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="form-label" style="font-size: 10px; color: var(--text-muted);">LOẠI YÊU CẦU</label>
                        <select name="subject" class="form-input" style="background: #ffffff; border: 1px solid #ddd; padding: 14px 16px; width: 100%; outline: none; font-family: inherit;">
                            <option value="appointment">Đặt lịch hẹn tư vấn mùi hương</option>
                            <option value="order">Hỗ trợ thông tin đơn hàng</option>
                            <option value="business">Hợp tác doanh nghiệp (B2B)</option>
                            <option value="other">Ý kiến đóng góp khác</option>
                        </select>
                    </div>
                    
                    <div class="form-group">
                        <label class="form-label" style="font-size: 10px; color: var(--text-muted);">NỘI DUNG CHI TIẾT *</label>
                        <textarea name="message" class="form-input" rows="5" style="background: #ffffff; border: 1px solid #ddd; resize: none; font-family: inherit;" placeholder="Quý khách vui lòng để lại lời nhắn..." required></textarea>
                    </div>
                    
                    <button type="submit" class="btn btn-primary" style="margin-top: 10px; width: 100%; padding: 16px; font-size: 12px;">
                        GỬI THÔNG TIN
                    </button>
                </form>
            </div>

        </div>
    </div>
</div>