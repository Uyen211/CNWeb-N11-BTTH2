<?php
// models/Course.php

class Course {
    private $conn;
    private $table = 'courses';

    public $id;
    public $title;
    public $description;
    public $instructor_id;
    public $category_id;
    public $price;
    public $duration_weeks;
    public $level;
    public $image;
    public $created_at;
    public $updated_at;

    public function __construct($db) {
        $this->conn = $db;
    }

    // Đếm tổng số bản ghi (có hỗ trợ lọc theo giảng viên và từ khóa)
    public function countAll($instructorId = null, $keyword = "") {
        $query = "SELECT COUNT(*) as total FROM " . $this->table . " WHERE 1=1";
        
        if ($instructorId) {
            $query .= " AND instructor_id = :instructor_id";
        }
        if (!empty($keyword)) {
            $query .= " AND title LIKE :keyword";
        }

        $stmt = $this->conn->prepare($query);

        if ($instructorId) {
            $stmt->bindParam(':instructor_id', $instructorId);
        }
        if (!empty($keyword)) {
            $keyword = "%{$keyword}%";
            $stmt->bindParam(':keyword', $keyword);
        }

        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row['total'];
    }

    // Lấy danh sách phân trang
    public function getPaginated($limit, $offset, $instructorId = null, $keyword = "", $sort = 'created_at DESC') {
        // Whitelist sort fields to prevent SQL Injection
        $allowedSorts = ['created_at DESC', 'created_at ASC', 'title ASC', 'price ASC', 'price DESC'];
        if (!in_array($sort, $allowedSorts)) {
            $sort = 'created_at DESC';
        }

        $query = "SELECT c.*, u.fullname as instructor_name, cat.name as category_name 
                  FROM " . $this->table . " c
                  LEFT JOIN users u ON c.instructor_id = u.id
                  LEFT JOIN categories cat ON c.category_id = cat.id
                  WHERE 1=1";

        if ($instructorId) {
            $query .= " AND c.instructor_id = :instructor_id";
        }
        if (!empty($keyword)) {
            $query .= " AND c.title LIKE :keyword";
        }

        $query .= " ORDER BY c." . $sort . " LIMIT :limit OFFSET :offset";

        $stmt = $this->conn->prepare($query);

        if ($instructorId) {
            $stmt->bindParam(':instructor_id', $instructorId);
        }
        if (!empty($keyword)) {
            $keyword = "%{$keyword}%";
            $stmt->bindParam(':keyword', $keyword);
        }

        $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
        $stmt->bindParam(':offset', $offset, PDO::PARAM_INT);

        $stmt->execute();
        return $stmt;
    }

    // Lấy chi tiết khóa học
    public function getById($id) {
        $query = "SELECT c.*, cat.name as category_name 
                  FROM " . $this->table . " c
                  LEFT JOIN categories cat ON c.category_id = cat.id
                  WHERE c.id = :id LIMIT 0,1";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id);
        $stmt->execute();

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function create() {
        $query = "INSERT INTO " . $this->table . " 
                  (title, description, instructor_id, category_id, price, duration_weeks, level, image, created_at, updated_at) 
                  VALUES (:title, :description, :instructor_id, :category_id, :price, :duration_weeks, :level, :image, NOW(), NOW())";

        $stmt = $this->conn->prepare($query);

        // Sanitize
        $this->title = htmlspecialchars(strip_tags($this->title));
        $this->description = htmlspecialchars(strip_tags($this->description));
        $this->level = htmlspecialchars(strip_tags($this->level));

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

        // Sanitize
        $this->title = htmlspecialchars(strip_tags($this->title));
        $this->description = htmlspecialchars(strip_tags($this->description));
        
        // Bind
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

    // Đếm số lượng học viên đã đăng ký (trừ trạng thái dropped nếu cần)
    public function countStudents($courseId) {
        // Chỉ đếm những học viên active hoặc completed
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

    // Tính trung bình tiến độ 
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

    // Kiểm tra học viên active
    public function hasActiveEnrollments($courseId) {
        $query = "SELECT COUNT(*) as count FROM enrollments WHERE course_id = :course_id AND status = 'active'";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':course_id', $courseId);
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row['count'] > 0;
    }

    // Helper: Lấy danh sách Categories để đổ vào Select Option
    public function getCategories() {
        $query = "SELECT id, name FROM categories ORDER BY name ASC";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function checkInstructorOwnership($courseId, $instructorId) {
        $query = "SELECT COUNT(*) FROM " . $this->table . " 
                  WHERE id = :course_id AND instructor_id = :instructor_id";
        
        $stmt = $this->conn->prepare($query);
        
        // Gán giá trị và đảm bảo kiểu dữ liệu an toàn
        $stmt->bindParam(':course_id', $courseId, PDO::PARAM_INT);
        $stmt->bindParam(':instructor_id', $instructorId, PDO::PARAM_INT);
        
        $stmt->execute();
        
        // Nếu số lượng bản ghi > 0, nghĩa là instructorId này sở hữu khóa học đó.
        return $stmt->fetchColumn() > 0;
    }

        // Lấy Top 5 khóa học có nhiều học viên nhất của giảng viên
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

    // Tính tổng doanh thu của TẤT CẢ khóa học thuộc giảng viên
    public function getTotalRevenue($instructorId) {
        // Giả định doanh thu = giá khóa học * số lượt enroll (không tính dropped)
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
}
?>