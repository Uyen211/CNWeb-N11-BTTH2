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
    public $duration_weeks; // Thêm biến này (do code feature có dùng)
    public $level;          // Thêm biến này
    public $created_at;
    public $updated_at;

    public function __construct($db) {
        $this->conn = $db;
    }

    // --- 1. HÀM ĐA NĂNG: LẤY DANH SÁCH + TÌM KIẾM + LỌC (PUBLIC) ---
    public function getPublicCourses($keyword = null, $category_id = null) {
        $query = "SELECT c.*, u.fullname as instructor_name, cat.name as category_name 
                  FROM " . $this->table . " c
                  LEFT JOIN users u ON c.instructor_id = u.id
                  LEFT JOIN categories cat ON c.category_id = cat.id
                  WHERE 1=1"; 

        $params = [];

        if (!empty($keyword)) {
            $query .= " AND c.title LIKE :keyword";
            $params[':keyword'] = "%" . $keyword . "%";
        }

        if (!empty($category_id)) {
            $query .= " AND c.category_id = :category_id";
            $params[':category_id'] = $category_id;
        }

        $query .= " ORDER BY c.created_at DESC";

        $stmt = $this->conn->prepare($query);
        $stmt->execute($params);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // --- 2. LẤY CHI TIẾT KHÓA HỌC (PUBLIC) ---
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

    // --- 3. LẤY KHÓA HỌC MỚI NHẤT (PUBLIC - HOMEPAGE) ---
    public function getLatestCourses($limit = 8) {
        $query = "SELECT c.id, c.title, c.image, c.price, c.level, 
                         u.fullname as instructor_name, cat.name as category_name
                  FROM " . $this->table . " c
                  LEFT JOIN users u ON c.instructor_id = u.id
                  LEFT JOIN categories cat ON c.category_id = cat.id
                  ORDER BY c.created_at DESC 
                  LIMIT :limit";

        $stmt = $this->conn->prepare($query);
        // Fix lỗi conflict: Đóng hàm này lại đúng cách
        $stmt->bindValue(':limit', (int)$limit, PDO::PARAM_INT);
        $stmt->execute();
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // --- 4. TẠO KHÓA HỌC (INSTRUCTOR) ---
    public function create() {
        $query = "INSERT INTO " . $this->table . " 
                  SET title = :title, 
                      description = :description, 
                      instructor_id = :instructor_id, 
                      category_id = :category_id, 
                      price = :price, 
                      duration_weeks = :duration_weeks, 
                      level = :level, 
                      image = :image,
                      created_at = NOW()";

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

    // --- 5. CẬP NHẬT KHÓA HỌC (INSTRUCTOR) ---
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

    // --- 6. XÓA KHÓA HỌC (INSTRUCTOR) ---
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

    // --- 7. CÁC HÀM QUẢN TRỊ / ANALYTICS (INSTRUCTOR DASHBOARD) ---

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

    // Tính tiến độ trung bình
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

    // Kiểm tra có học viên đang học hay không (để chặn xóa)
    public function hasActiveEnrollments($courseId) {
        $query = "SELECT COUNT(*) FROM enrollments WHERE course_id = :course_id AND status = 'active'";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':course_id', $courseId);
        $stmt->execute();

        return $stmt->fetchColumn() > 0;
    }

    // Kiểm tra quyền sở hữu (Giảng viên A có sở hữu khóa học B không)
    public function checkInstructorOwnership($courseId, $instructorId) {
        $query = "SELECT COUNT(*) FROM " . $this->table . " 
                  WHERE id = :course_id AND instructor_id = :instructor_id";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':course_id', $courseId, PDO::PARAM_INT);
        $stmt->bindParam(':instructor_id', $instructorId, PDO::PARAM_INT);
        
        $stmt->execute();
        return $stmt->fetchColumn() > 0;
    }

    // Lấy danh sách khóa học phân trang cho Instructor
    public function getPaginated($limit, $offset, $instructorId, $keyword = "", $sort = 'created_at DESC') {
        $query = "SELECT * FROM " . $this->table . " WHERE instructor_id = :instructor_id";
        
        if (!empty($keyword)) {
            $query .= " AND title LIKE :keyword";
        }
        
        // Validate sort để tránh SQL Injection
        $allowedSorts = ['created_at DESC', 'created_at ASC', 'title ASC', 'price DESC'];
        if (!in_array($sort, $allowedSorts)) {
            $sort = 'created_at DESC';
        }
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

    // Đếm tổng số khóa học của Instructor (để phân trang)
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

    // Lấy Top khóa học (cho Chart/Thống kê)
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
        return $stmt; // Trả về stmt để fetch trong view/controller
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
    
    // Helper: Lấy danh sách Categories cho form Create/Edit
    public function getCategories() {
        $query = "SELECT * FROM categories ORDER BY name ASC";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
?>