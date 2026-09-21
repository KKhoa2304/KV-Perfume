<?php

require_once __DIR__ . '/Database.php';

// Lớp trung gian thao tác dữ liệu (Data Access Object)
// Mọi Model trong hệ thống sẽ kế thừa lớp này để tái sử dụng các tác vụ CRUD cơ bản.
class XlData extends Database
{
    protected PDO $db;

    public function __construct()
    {
        // Tự động khởi tạo và kế thừa kết nối PDO từ lớp Database
        $this->db = $this->connect();
    }

    // Thực thi các truy vấn SELECT cần lấy danh sách nhiều bản ghi (trả về mảng 2 chiều)
    // BẢO MẬT: Bắt buộc truyền tham số qua mảng $params (Prepared Statement) để ngăn chặn tuyệt đối SQL Injection
    public function readItem(string $sql, array $params = []): array
    {
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);

        return $stmt->fetchAll();
    }

    // Thực thi các truy vấn SELECT chỉ lấy 1 bản ghi duy nhất (VD: getProductById, checkLogin)
    public function readOne(string $sql, array $params = []): ?array
    {
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);

        $result = $stmt->fetch();

        // Ép kiểu chặt chẽ: Trả về null nếu không có dữ liệu thay vì trả về mảng rỗng hoặc false
        return $result ?: null;
    }

    // Thực thi các lệnh thay đổi dữ liệu không trả về kết quả bảng (INSERT, UPDATE, DELETE)
    // Trả về true nếu thành công, false nếu thất bại
    public function executeItem(string $sql, array $params = []): bool
    {
        $stmt = $this->db->prepare($sql);

        return $stmt->execute($params);
    }
}