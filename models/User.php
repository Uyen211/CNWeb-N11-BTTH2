<?php
class User {
    private $conn; // Thống nhất dùng tên biến này
    private $table = 'users';

    // Các thuộc tính public (Cần cho logic của nhóm)
    public $id;
    public $username;
    public $email;
    public $fullname;
    public $role;
    public $is_active;

    public function __construct($db) {
        $this->conn = $db;
    }

    // =================================================================
    // PHẦN 1: AUTHENTICATION (Đăng ký / Đăng nhập - Code của BẠN)
    // =================================================================

    // 1. Đăng ký tài khoản
    public function register($username, $email, $password, $fullname, $role = 0) {
        // Mã hóa mật khẩu
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);

        // Chuẩn bị câu SQL (Thêm is_active mặc định là 1)
        $sql = "INSERT INTO " . $this->table . " (username, email, password, fullname, role, is_active, created_at) 
                VALUES (:username, :email, :password, :fullname, :role, 1, NOW())";
        
        $stmt = $this->conn->prepare($sql);
        
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
        $sql = "SELECT * FROM " . $this->table . " WHERE username = :u1 OR email = :u2 LIMIT 1";
        
        $stmt = $this->conn->prepare($sql);
        
        // Truyền giá trị input vào cả 2 vị trí
        $stmt->execute([
            ':u1' => $input,
            ':u2' => $input
        ]);
        
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // =================================================================
    // PHẦN 2: ADMIN MANAGEMENT (Quản lý User - Code của NHÓM)
    // =================================================================

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

    // =================================================================
    // PHẦN 3: DASHBOARD STATISTICS (Thống kê - Code của NHÓM)
    // =================================================================

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