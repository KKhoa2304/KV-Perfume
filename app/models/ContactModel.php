<?php

// Nhúng file XlData theo đúng chuẩn hệ thống của bạn
require_once __DIR__ . '/XLData.php';

class ContactModel extends XLData
{
    /**
     * Lưu thông tin form liên hệ vào Database
     */
    public function insertContact($name, $phone, $email, $subject, $message) 
    {
        $sql = "INSERT INTO contacts (name, phone, email, subject, message) 
                VALUES (:name, :phone, :email, :subject, :message)";
        
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            'name' => $name,
            'phone' => $phone,
            'email' => $email,
            'subject' => $subject,
            'message' => $message
        ]);
    }

    /**
     * Lấy danh sách tất cả liên hệ cho trang Admin
     */
    public function getAllContacts() 
    {
        // Dùng hàm readItem có sẵn của XlData giống y hệt OrderModel
        return $this->readItem("SELECT * FROM contacts ORDER BY created_at DESC");
    }
}
?>