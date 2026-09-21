<?php
// Nhúng trực tiếp file Model vào đây (kiểm tra lại đường dẫn nếu cần)
require_once __DIR__ . '/../../models/ContactModel.php';

// Controller xử lý phần Quản lý liên hệ trong Admin
class ContactController extends Controller 
{
    public function index() 
    {
        // Khởi tạo trực tiếp đối tượng Model bằng chữ 'new'
        $contactModel = new ContactModel();
        
        $data['contacts'] = $contactModel->getAllContacts();

        // Truyền dữ liệu ra View (bảng danh sách)
        $this->render('admin/contact/index', $data);
    }
}
?>