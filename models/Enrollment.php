<?php
class Enrollment {
    private $conn;
    private $table = 'enrollments';

    public function __construct($db) {
        $this->conn = $db;
    }

    // 1. Kiểm tra xem user đã đăng ký khóa học này chưa
    public function isEnrolled($student_id, $course_id) {
        $query = "SELECT id FROM " . $this->table . " 
                  WHERE student_id = :student_id AND course_id = :course_id LIMIT 1";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':student_id', $student_id);
        $stmt->bindParam(':course_id', $course_id);
        $stmt->execute();

        if ($stmt->rowCount() > 0) {
            return true; // Đã đăng ký
        }
        return false; // Chưa đăng ký
    }

    // 2. Thực hiện đăng ký khóa học mới
    public function registerCourse($student_id, $course_id) {
        $query = "INSERT INTO " . $this->table . " 
                  (student_id, course_id, status, progress, enrolled_date) 
                  VALUES (:student_id, :course_id, 'active', 0, NOW())";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':student_id', $student_id);
        $stmt->bindParam(':course_id', $course_id);

        if ($stmt->execute()) {
            return true;
        }
        return false;
    }

    public function getMyCourses($student_id) {
        // JOIN bảng enrollments với courses để lấy thông tin hiển thị
        $query = "SELECT c.id, c.title, c.image, c.price,
                        e.progress, e.enrolled_date, e.status, e.course_id
                FROM enrollments e
                JOIN courses c ON e.course_id = c.id
                WHERE e.student_id = :student_id
                ORDER BY e.enrolled_date DESC";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':student_id', $student_id);
        $stmt->execute();
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
?>