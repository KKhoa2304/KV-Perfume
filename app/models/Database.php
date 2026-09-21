<?php

// Lớp cơ sở xử lý kết nối Database bằng PDO
class Database
{
    // Cấu hình InfinityFree
    // Sau khi upload lên hosting, chỉ cần thay password vPanel/MySQL của mình ở dòng $password.
    private string $host = 'sql205.infinityfree.com';
    private string $dbname = 'if0_42257221_kv_perfume';
    private string $username = 'if0_42257221';
    private string $password = '0877766122';
    private string $charset = 'utf8mb4';

    private ?PDO $conn = null;

    public function connect(): PDO
    {
        if ($this->conn === null) {
            // Không đặt charset trong DSN để tránh lỗi Unknown character set trên một số node free host.
            $dsn = "mysql:host={$this->host};dbname={$this->dbname}";

            try {
                $this->conn = new PDO($dsn, $this->username, $this->password, [
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                    PDO::ATTR_EMULATE_PREPARES => false,
                    PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8mb4 COLLATE utf8mb4_unicode_ci"
                ]);

                // Ép kết nối dùng UTF-8 để tiếng Việt không bị lỗi dấu.
                $this->conn->exec("SET NAMES utf8mb4 COLLATE utf8mb4_unicode_ci");

            } catch (PDOException $e) {
                die('Kết nối database thất bại: ' . $e->getMessage());
            }
        }

        return $this->conn;
    }
}
