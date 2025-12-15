<?php
class Course {
    private $conn;
    private $table = 'courses';

    // Khai báo đầy đủ thuộc tính cho cả 2 luồng chức năng
    public $id;
    public $title;
    public $description;
    public $instructor_id;
    public $category_id;
    public $price;
    public $image;
    public $duration_weeks; // Quan trọng cho tính năng mới
    public $level;          // Quan trọng cho tính năng mới
    public $created_at;
    public $updated_at;
    public $status;

    public function __construct($db) {
        $this->conn = $db;
    }

    // =================================================================
    // PHẦN 1: PUBLIC FUNCTIONS (Dành cho Student/Guest)
    // =================================================================

    // --- HÀM ĐA NĂNG: LẤY DANH SÁCH + TÌM KIẾM + LỌC ---
    public function getPublicCourses($keyword = null, $category_id = null) {
        $query = "SELECT c.*, u.fullname as instructor_name, cat.name as category_name 
                  FROM " . $this->table . " c
                  LEFT JOIN users u ON c.instructor_id = u.id
                  LEFT JOIN categories cat ON c.category_id = cat.id
                  WHERE c.status = 'approved'"; // Chỉ lấy khóa học đã duyệt (nếu có cột status)

        // Nếu bảng chưa có cột status, hãy bỏ dòng WHERE trên hoặc sửa thành WHERE 1=1

        // Mảng chứa tham số để bind (Tránh lỗi Invalid parameter number)
        $params = [];

        // 1. Logic Tìm kiếm (Theo Title)
        if (!empty($keyword)) {
            $query .= " AND c.title LIKE :keyword";
            $params[':keyword'] = "%" . $keyword . "%";
        }

        // 2. Logic Lọc danh mục
        if (!empty($category_id)) {
            $query .= " AND c.category_id = :category_id";
            $params[':category_id'] = $category_id;
        }

        // 3. Sắp xếp: Mặc định khóa mới nhất lên đầu
        $query .= " ORDER BY c.created_at DESC";

        $stmt = $this->conn->prepare($query);
        $stmt->execute($params);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // --- LẤY CHI TIẾT KHÓA HỌC ---
    public function getById($id) {
        $query = "SELECT c.*, cat.name as category_name, u.fullname as instructor_name
                  FROM " . $this->table . " c
                  LEFT JOIN categories cat ON c.category_id = cat.id
                  LEFT JOIN users u ON c.instructor_id = u.id
                  WHERE c.id = :id LIMIT 0,1";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id);
        $stmt->execute();

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // --- LẤY KHÓA HỌC MỚI NHẤT (Cho Home) ---
    public function getLatestCourses($limit = 8) {
        $query = "SELECT c.id, c.title, c.image, c.price, c.level, 
                         u.fullname as instructor_name, cat.name as category_name
                  FROM " . $this->table . " c
                  LEFT JOIN users u ON c.instructor_id = u.id
                  LEFT JOIN categories cat ON c.category_id = cat.id
                  ORDER BY c.created_at DESC 
                  LIMIT :limit";

        $stmt = $this->conn->prepare($query);
        $stmt->bindValue(':limit', (int)$limit, PDO::PARAM_INT);
        $stmt->execute();
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // =================================================================
    // PHẦN 2: INSTRUCTOR FUNCTIONS (Quản lý khóa học)
    // =================================================================

    // --- TẠO KHÓA HỌC ---
    public function create() {
        $query = "INSERT INTO " . $this->table . " 
                  (title, description, instructor_id, category_id, price, duration_weeks, level, image, status, created_at, updated_at) 
                  VALUES (:title, :description, :instructor_id, :category_id, :price, :duration_weeks, :level, :image, 'pending', NOW(), NOW())";

        $stmt = $this->conn->prepare($query);

        // Sanitize
        $this->title = htmlspecialchars(strip_tags($this->title));
        $this->description = htmlspecialchars(strip_tags($this->description));
        
        // Bind
        $stmt->bindParam(':title', $this->title);
        $stmt->bindParam(':description', $this->description);
        $stmt->bindParam(':instructor_id', $this->instructor_id);
        $stmt->bindParam(':category_id', $this->category_id);
        $stmt->bindParam(':price', $this->price);
        $stmt->bindParam(':duration_weeks', $this->duration_weeks);
        $stmt->bindParam(':level', $this->level);
        $stmt->bindParam(':image', $this->image);

        if ($stmt->execute()) {
            return true;
        }
        return false;
    }

    // --- CẬP NHẬT KHÓA HỌC ---
    public function update() {
        $query = "UPDATE " . $this->table . " 
                  SET title = :title, 
                      description = :description, 
                      category_id = :category_id, 
                      price = :price, 
                      duration_weeks = :duration_weeks, 
                      level = :level, 
                      image = :image, 
                      updated_at = NOW() 
                  WHERE id = :id AND instructor_id = :instructor_id";

        $stmt = $this->conn->prepare($query);

        $this->title = htmlspecialchars(strip_tags($this->title));
        $this->description = htmlspecialchars(strip_tags($this->description));
        
        $stmt->bindParam(':title', $this->title);
        $stmt->bindParam(':description', $this->description);
        $stmt->bindParam(':category_id', $this->category_id);
        $stmt->bindParam(':price', $this->price);
        $stmt->bindParam(':duration_weeks', $this->duration_weeks);
        $stmt->bindParam(':level', $this->level);
        $stmt->bindParam(':image', $this->image);
        $stmt->bindParam(':id', $this->id);
        $stmt->bindParam(':instructor_id', $this->instructor_id);

        if ($stmt->execute()) {
            return true;
        }
        return false;
    }

    // --- XÓA KHÓA HỌC ---
    public function delete() {
        $query = "DELETE FROM " . $this->table . " WHERE id = :id AND instructor_id = :instructor_id";
        $stmt = $this->conn->prepare($query);

        $stmt->bindParam(':id', $this->id);
        $stmt->bindParam(':instructor_id', $this->instructor_id);

        if ($stmt->execute()) {
            return true;
        }
        return false;
    }

    // =================================================================
    // PHẦN 3: ADMIN & ANALYTICS (Dành cho Dashboard/Stats)
    // =================================================================

    // Lấy danh sách chờ duyệt (Admin)
    public function getPendingCourses() {
        $query = "SELECT c.*, u.fullname as instructor_name, cat.name as category_name 
                  FROM " . $this->table . " c
                  JOIN users u ON c.instructor_id = u.id
                  LEFT JOIN categories cat ON c.category_id = cat.id
                  WHERE c.status = 'pending'
                  ORDER BY c.created_at ASC";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt;
    }

    // Duyệt/Từ chối (Admin)
    public function updateStatus($id, $status) {
        $query = "UPDATE " . $this->table . " SET status = :status WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':status', $status);
        $stmt->bindParam(':id', $id);
        return $stmt->execute();
    }

    // Đếm số lượng học viên
    public function countStudents($courseId) {
        $query = "SELECT COUNT(*) as total 
                  FROM enrollments 
                  WHERE course_id = :course_id 
                  AND status IN ('active', 'completed')";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':course_id', $courseId);
        $stmt->execute();
        
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row['total'];
    }

    // Tính tiến độ trung bình của lớp
    public function getAverageProgress($courseId) {
        $query = "SELECT AVG(progress) as avg_progress 
                  FROM enrollments 
                  WHERE course_id = :course_id";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':course_id', $courseId);
        $stmt->execute();
        
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row['avg_progress'] ? round($row['avg_progress'], 1) : 0;
    }

    // Kiểm tra có học viên đang học (để chặn xóa)
    public function hasActiveEnrollments($courseId) {
        $query = "SELECT COUNT(*) FROM enrollments WHERE course_id = :course_id AND status = 'active'";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':course_id', $courseId);
        $stmt->execute();

        return $stmt->fetchColumn() > 0;
    }

    // Kiểm tra quyền sở hữu (Bảo mật)
    public function checkInstructorOwnership($courseId, $instructorId) {
        $query = "SELECT COUNT(*) FROM " . $this->table . " 
                  WHERE id = :course_id AND instructor_id = :instructor_id";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':course_id', $courseId, PDO::PARAM_INT);
        $stmt->bindParam(':instructor_id', $instructorId, PDO::PARAM_INT);
        
        $stmt->execute();
        return $stmt->fetchColumn() > 0;
    }

    // Phân trang cho Instructor Dashboard
    public function getPaginated($limit, $offset, $instructorId, $keyword = "", $sort = 'created_at DESC') {
        $query = "SELECT * FROM " . $this->table . " WHERE instructor_id = :instructor_id";
        
        if (!empty($keyword)) {
            $query .= " AND title LIKE :keyword";
        }
        
        $allowedSorts = ['created_at DESC', 'created_at ASC', 'title ASC', 'price DESC'];
        if (!in_array($sort, $allowedSorts)) $sort = 'created_at DESC';
        
        $query .= " ORDER BY " . $sort;
        $query .= " LIMIT :limit OFFSET :offset";

        $stmt = $this->conn->prepare($query);
        
        $stmt->bindValue(':instructor_id', $instructorId, PDO::PARAM_INT);
        $stmt->bindValue(':limit', (int)$limit, PDO::PARAM_INT);
        $stmt->bindValue(':offset', (int)$offset, PDO::PARAM_INT);
        
        if (!empty($keyword)) {
            $stmt->bindValue(':keyword', "%$keyword%");
        }

        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Đếm tổng số khóa học của Instructor
    public function countAll($instructorId, $keyword = "") {
        $query = "SELECT COUNT(*) FROM " . $this->table . " WHERE instructor_id = :instructor_id";
        if (!empty($keyword)) {
            $query .= " AND title LIKE :keyword";
        }
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindValue(':instructor_id', $instructorId);
        if (!empty($keyword)) {
            $stmt->bindValue(':keyword', "%$keyword%");
        }
        $stmt->execute();
        return $stmt->fetchColumn();
    }

    // Lấy Top khóa học (Thống kê)
    public function getTopCourses($instructorId, $limit = 5) {
        $query = "SELECT c.id, c.title, c.image, c.price, COUNT(e.id) as student_count 
                  FROM courses c
                  LEFT JOIN enrollments e ON c.id = e.course_id
                  WHERE c.instructor_id = :instructor_id
                  GROUP BY c.id
                  ORDER BY student_count DESC
                  LIMIT :limit";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':instructor_id', $instructorId);
        $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt;
    }

    // Tổng doanh thu
    public function getTotalRevenue($instructorId) {
        $query = "SELECT SUM(c.price) as total_revenue
                  FROM enrollments e
                  JOIN courses c ON e.course_id = c.id
                  WHERE c.instructor_id = :instructor_id AND e.status != 'dropped'";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':instructor_id', $instructorId);
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row['total_revenue'] ?? 0;
    }
    
    // Helper: Lấy danh sách Categories
    public function getCategories() {
        $query = "SELECT * FROM categories ORDER BY name ASC";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
?>