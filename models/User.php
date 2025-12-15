<?php
// models/User.php

class User {
    private $conn;
    private $table = 'users';

    public $id;
    public $username;
    public $email;
    public $fullname;
    public $role;
    public $is_active;

    public function __construct($db) {
        $this->conn = $db;
    }

    // Lấy danh sách users có phân trang & tìm kiếm
    public function getAll($limit, $offset, $keyword = "") {
        $query = "SELECT * FROM " . $this->table . " WHERE 1=1";
        
        if (!empty($keyword)) {
            $query .= " AND (fullname LIKE :keyword OR email LIKE :keyword OR username LIKE :keyword)";
        }

        // Sắp xếp: Admin lên đầu, sau đó mới nhất
        $query .= " ORDER BY role DESC, created_at DESC LIMIT :limit OFFSET :offset";

        $stmt = $this->conn->prepare($query);

        if (!empty($keyword)) {
            $keyword = "%{$keyword}%";
            $stmt->bindParam(':keyword', $keyword);
        }

        $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
        $stmt->bindParam(':offset', $offset, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt;
    }

    // Đếm tổng users để phân trang
    public function countAll($keyword = "") {
        $query = "SELECT COUNT(*) as total FROM " . $this->table . " WHERE 1=1";
        if (!empty($keyword)) {
            $query .= " AND (fullname LIKE :keyword OR email LIKE :keyword)";
        }
        $stmt = $this->conn->prepare($query);
        if (!empty($keyword)) {
            $keyword = "%{$keyword}%";
            $stmt->bindParam(':keyword', $keyword);
        }
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row['total'];
    }

    // Toggle trạng thái (Kích hoạt / Vô hiệu hóa)
    public function toggleStatus($id, $status) {
        $query = "UPDATE " . $this->table . " SET is_active = :status WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':status', $status, PDO::PARAM_INT);
        $stmt->bindParam(':id', $id);
        return $stmt->execute();
    }

    // --- CÁC HÀM THỐNG KÊ DASHBOARD ---

    public function countByRole($role) {
        $query = "SELECT COUNT(*) as total FROM " . $this->table . " WHERE role = :role";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':role', $role);
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row['total'];
    }

    public function countTotalCourses() {
        $query = "SELECT COUNT(*) as total FROM courses";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row['total'];
    }

    public function countTotalEnrollments() {
        $query = "SELECT COUNT(*) as total FROM enrollments WHERE status = 'active'";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row['total'];
    }
}
?>