<?php
class User {
    private $db; // Biến lưu kết nối PDO

    // Constructor nhận kết nối PDO từ bên ngoài (AuthController truyền vào)
    public function __construct($db) {
        // --- SỬA LỖI Ở ĐÂY ---
        // Phải gán vào $this->db thì các hàm bên dưới mới dùng được
        $this->db = $db; 
    }

    // 1. Đăng ký tài khoản
    public function register($username, $email, $password, $fullname, $role = 0) {
        // Mã hóa mật khẩu
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);

        // Chuẩn bị câu SQL
        $sql = "INSERT INTO users (username, email, password, fullname, role, created_at) 
                VALUES (:username, :email, :password, :fullname, :role, NOW())";
        
        $stmt = $this->db->prepare($sql);
        
        try {
            return $stmt->execute([
                ':username' => $username,
                ':email'    => $email,
                ':password' => $hashed_password,
                ':fullname' => $fullname,
                ':role'     => $role
            ]);
        } catch (PDOException $e) {
            // Xử lý lỗi trùng lặp (Duplicate entry - Mã lỗi 23000)
            if ($e->getCode() == 23000) {
                return false; // Username hoặc Email đã tồn tại
            }
            throw $e; // Nếu lỗi khác thì ném ra để debug
        }
    }

    // 2. Lấy thông tin user bằng Username HOẶC Email (để đăng nhập)
    public function getUserByUsername($input) {
        // Logic: Cho phép nhập username hoặc email đều tìm được
        $sql = "SELECT * FROM users WHERE username = :u1 OR email = :u2 LIMIT 1";
        
        $stmt = $this->db->prepare($sql);
        
        // Truyền giá trị input vào cả 2 vị trí (so khớp cả 2 cột)
        $stmt->execute([
            ':u1' => $input,
            ':u2' => $input
        ]);
        
        // Trả về mảng dữ liệu user (hoặc false nếu không tìm thấy)
        return $stmt->fetch();
    }
}
?>