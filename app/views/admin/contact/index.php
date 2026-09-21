<div class="admin-content">
    <div class="page-header">
        <h2>QUẢN LÝ YÊU CẦU LIÊN HỆ</h2>
    </div>

    <div class="table-container">
        <table class="luxury-table">
            <thead>
                <tr>
                    <th style="width: 5%;">ID</th>
                    <th style="width: 15%;">Khách hàng</th>
                    <th style="width: 20%;">Thông tin liên lạc</th>
                    <th style="width: 15%;">Loại yêu cầu</th>
                    <th style="width: 25%;">Nội dung (Rút gọn)</th>
                    <th style="width: 10%;">Ngày gửi</th>
                    <th style="width: 10%; text-align: center;">Thao tác</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($data['contacts'])) : ?>
                    <?php foreach ($data['contacts'] as $contact) : ?>
                        <tr>
                            <td>#<?php echo $contact['id']; ?></td>
                            <td class="font-bold"><?php echo htmlspecialchars($contact['name']); ?></td>
                            <td>
                                <div class="mb-5">📞 <?php echo htmlspecialchars($contact['phone']); ?></div>
                                <div>✉️ <a href="mailto:<?php echo htmlspecialchars($contact['email']); ?>" class="text-gold"><?php echo htmlspecialchars($contact['email']); ?></a></div>
                            </td>
                            <td>
                                <span class="badge-subject">
                                    <?php echo htmlspecialchars($contact['subject']); ?>
                                </span>
                            </td>
                            <td class="text-muted line-height-15">
                                <?php 
                                    // Cắt chuỗi 50 ký tự để giao diện bảng không bị vỡ nếu lời nhắn quá dài
                                    $shortMessage = mb_substr($contact['message'], 0, 50, 'UTF-8');
                                    echo htmlspecialchars($shortMessage) . (mb_strlen($contact['message'], 'UTF-8') > 50 ? '...' : ''); 
                                ?>
                            </td>
                            <td class="text-small text-muted">
                                <?php echo date('d/m/Y H:i', strtotime($contact['created_at'])); ?>
                            </td>
                            <td style="text-align: center;">
                                <button class="btn-luxury" onclick="openContactModal(this)"
                                    data-id="<?php echo $contact['id']; ?>"
                                    data-name="<?php echo htmlspecialchars($contact['name']); ?>"
                                    data-phone="<?php echo htmlspecialchars($contact['phone']); ?>"
                                    data-email="<?php echo htmlspecialchars($contact['email']); ?>"
                                    data-subject="<?php echo htmlspecialchars($contact['subject']); ?>"
                                    data-message="<?php echo htmlspecialchars($contact['message']); ?>"
                                    data-date="<?php echo date('d/m/Y H:i', strtotime($contact['created_at'])); ?>">
                                    Xem chi tiết
                                </button>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else : ?>
                    <tr>
                        <td colspan="7" class="empty-state">
                            Hiện tại chưa có yêu cầu liên hệ nào từ khách hàng.
                        </td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<div id="contactDetailModal" class="contact-modal-overlay">
    <div class="contact-modal-box">
        <div class="modal-header">
            <h3>CHI TIẾT LIÊN HỆ #<span id="modal-id"></span></h3>
            <button class="btn-close" onclick="closeContactModal()">&times;</button>
        </div>
        <div class="modal-body">
            <p><strong>👤 Khách hàng:</strong> <span id="modal-name"></span></p>
            <p><strong>📞 Số điện thoại:</strong> <span id="modal-phone"></span></p>
            <p><strong>✉️ Email:</strong> <span id="modal-email"></span></p>
            <p><strong>🏷️ Phân loại:</strong> <span id="modal-subject" class="badge-subject"></span></p>
            <p><strong>🕒 Ngày gửi:</strong> <span id="modal-date"></span></p>
            
            <h4 class="msg-title">Nội dung chi tiết:</h4>
            <div id="modal-message" class="modal-msg-box"></div>
        </div>
    </div>
</div>

<style>
    /* CSS Cấu trúc cơ bản */
    .page-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; }
    .page-header h2 { font-family: var(--font-serif); letter-spacing: 2px; text-transform: uppercase; margin: 0; }
    .table-container { background: #fff; padding: 20px; border: 1px solid #ddd; border-radius: 4px; }
    
    /* CSS Bảng dữ liệu */
    .luxury-table { width: 100%; border-collapse: collapse; font-family: var(--font-sans); font-size: 14px; }
    .luxury-table th { padding: 12px; background-color: #f8f9fa; border-bottom: 2px solid #ddd; text-align: left; }
    .luxury-table td { padding: 12px; border-bottom: 1px solid #eee; vertical-align: middle; }
    
    /* Các class tiện ích (Utilities) */
    .font-bold { font-weight: bold; }
    .mb-5 { margin-bottom: 5px; }
    .text-gold { color: #a98a52; text-decoration: none; transition: 0.2s; }
    .text-gold:hover { text-decoration: underline; }
    .text-muted { color: #555; }
    .text-small { font-size: 13px; color: #888; }
    .line-height-15 { line-height: 1.5; }
    .empty-state { padding: 30px; text-align: center; color: #888; font-style: italic; }
    .badge-subject { background: #eef2f5; padding: 4px 8px; border-radius: 4px; font-size: 12px; font-weight: bold; color: #333; display: inline-block; }

    /* Nút Xem chi tiết (Ghost Button phong cách Luxury) */
    .btn-luxury {
        background-color: transparent;
        color: #111;
        border: 1px solid #111;
        padding: 6px 14px;
        font-size: 13px;
        font-weight: 600;
        border-radius: 3px;
        cursor: pointer;
        transition: all 0.3s ease;
    }
    .btn-luxury:hover {
        background-color: #111;
        color: #a98a52;
        box-shadow: 0 4px 10px rgba(0, 0, 0, 0.15);
    }

    /* CSS Modal (Popup) */
    .contact-modal-overlay {
        display: none; position: fixed; z-index: 9999; left: 0; top: 0; width: 100%; height: 100%;
        background-color: rgba(0, 0, 0, 0.6); align-items: center; justify-content: center;
    }
    .contact-modal-box {
        background-color: #fff; width: 600px; max-width: 90%; border-radius: 8px; 
        box-shadow: 0 5px 20px rgba(0,0,0,0.3); overflow: hidden; position: relative;
        animation: modalFadeIn 0.3s ease;
    }
    @keyframes modalFadeIn { from { transform: translateY(-30px); opacity: 0; } to { transform: translateY(0); opacity: 1; } }
    
    .modal-header { background: #111; padding: 15px 20px; color: #a98a52; display: flex; justify-content: space-between; align-items: center; }
    .modal-header h3 { margin: 0; font-size: 18px; letter-spacing: 1px; text-transform: uppercase; }
    .btn-close { color: #fff; font-size: 24px; cursor: pointer; background: transparent; border: none; line-height: 1; transition: 0.2s; }
    .btn-close:hover { color: #a98a52; transform: scale(1.1); }
    
    .modal-body { padding: 20px; font-size: 15px; color: #333; line-height: 1.6; }
    .modal-body p { margin: 8px 0; }
    .msg-title { margin: 20px 0 5px 0; border-top: 1px solid #eee; padding-top: 15px; font-size: 16px; }
    .modal-msg-box { background: #f9f9f9; padding: 15px; border-left: 4px solid #a98a52; margin-top: 10px; white-space: pre-wrap; color: #444; min-height: 80px; }
</style>

<script>
    /**
     * Lấy dữ liệu từ các thuộc tính data-* của nút được click
     * và truyền vào các thẻ HTML tương ứng trong Modal
     */
    function openContactModal(button) {
        document.getElementById('modal-id').innerText = button.getAttribute('data-id');
        document.getElementById('modal-name').innerText = button.getAttribute('data-name');
        document.getElementById('modal-phone').innerText = button.getAttribute('data-phone');
        document.getElementById('modal-email').innerText = button.getAttribute('data-email');
        document.getElementById('modal-subject').innerText = button.getAttribute('data-subject');
        document.getElementById('modal-date').innerText = button.getAttribute('data-date');
        document.getElementById('modal-message').innerText = button.getAttribute('data-message');
        
        // Hiển thị modal (chuyển display từ none sang flex để canh giữa)
        document.getElementById('contactDetailModal').style.display = 'flex';
    }

    /**
     * Đóng Modal khi bấm nút X
     */
    function closeContactModal() {
        document.getElementById('contactDetailModal').style.display = 'none';
    }

    /**
     * Lắng nghe sự kiện click: Nếu user bấm ra ngoài vùng trắng của Modal (vào phần nền đen)
     * thì tự động đóng Modal lại cho thân thiện với UX.
     */
    window.onclick = function(event) {
        let modal = document.getElementById('contactDetailModal');
        if (event.target == modal) {
            modal.style.display = "none";
        }
    }
</script>