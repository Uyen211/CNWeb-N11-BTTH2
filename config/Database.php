<?php

class Database {
    // Thông tin cấu hình Database
    private $host = 'localhost';
    private $db_name = 'CSDLonlinecourse';
    private $username = 'root'; 
    private $password = ''; 
    public $conn;

    // Phương thức kết nối
    public function connect() {
        $this->conn = null;

        try {
            // Chuỗi kết nối DSN cho MySQL
            // charset=utf8 để không bị lỗi font tiếng Việt
            $dsn = "mysql:host=" . $this->host . ";dbname=" . $this->db_name . ";charset=utf8";
            
            // Tùy chọn PDO
            $options = [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION, // Báo lỗi dạng Exception dễ bắt lỗi
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC, // Lấy dữ liệu dạng mảng kết hợp
                PDO::ATTR_EMULATE_PREPARES => false, // Tăng bảo mật chống SQL Injection
            ];

            $this->conn = new PDO($dsn, $this->username, $this->password, $options);
            
        } catch(PDOException $e) {
            echo "Lỗi kết nối Database: " . $e->getMessage();
        }

        return $this->conn;
    }
}
?>