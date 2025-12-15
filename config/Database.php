<?php
class Database {
    // Thông tin cấu hình Database
    private $host = 'localhost';
    private $db_name = 'CSDLonlinecourse'; // Đảm bảo tên DB này đúng
    private $username = 'root'; 
    private $password = ''; 
    
    // 1. Đổi tên biến $conn thành $pdo (để khớp với User.php)
    public $pdo;

    // 2. Đổi tên hàm connect() thành __construct()
    // Để khi gọi new Database() là nó tự động kết nối luôn
    public function __construct() {
        $this->pdo = null;

        try {
            // Chuỗi kết nối
            $dsn = "mysql:host=" . $this->host . ";dbname=" . $this->db_name . ";charset=utf8";
            
            // Tùy chọn PDO
            $options = [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false,
            ];

            // Kết nối và gán vào biến $pdo
            $this->pdo = new PDO($dsn, $this->username, $this->password, $options);
            
        } catch(PDOException $e) {
            echo "Lỗi kết nối Database: " . $e->getMessage();
            die(); // Dừng chương trình nếu lỗi
        }
    }

    public function getConnection() {
        return $this->pdo;
    }
}
?>