<?php
// models/User.php

class User {
    private $db;

    // Dependency Injection: Nhận kết nối DB từ bên ngoài
    public function __construct(Database $database) {
        $this->db = $database->pdo;
    }

    // 1. Đăng ký tài khoản
    public function register($username, $email, $password, $fullname, $role = 0) {
        // Mã hóa mật khẩu
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);

        $sql = "INSERT INTO users (username, email, password, fullname, role) VALUES (:username, :email, :password, :fullname, :role)";
        $stmt = $this->db->prepare($sql);
        
        try {
            return $stmt->execute([
                ':username' => $username,
                ':email' => $email,
                ':password' => $hashed_password,
                ':fullname' => $fullname,
                ':role' => $role
            ]);
        } catch (PDOException $e) {
            // Xử lý lỗi trùng lặp (Duplicate entry)
            if ($e->getCode() == 23000) {
                return false; // Username hoặc Email đã tồn tại
            }
            throw $e;
        }
    }

    // 2. Lấy thông tin user bằng Username (để đăng nhập)
    public function getUserByUsername($username) {
    // Sửa câu SQL: Dùng 2 tên tham số khác nhau (:u1 và :u2)
        $sql = "SELECT * FROM users WHERE username = :u1 OR email = :u2";
        
        $stmt = $this->db->prepare($sql);
        
        // Truyền giá trị $username vào cả 2 vị trí
        $stmt->execute([
            ':u1' => $username,
            ':u2' => $username
        ]);
        
        return $stmt->fetch();
    }
}
?>