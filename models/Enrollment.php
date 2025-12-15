<?php
<<<<<<< HEAD
=======
// models/Enrollment.php

>>>>>>> feature/instructor-student-dashboard
class Enrollment {
    private $conn;
    private $table = 'enrollments';

    public function __construct($db) {
        $this->conn = $db;
    }

<<<<<<< HEAD
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
=======
    // Lấy danh sách học viên theo course_id (có phân trang)
    public function getStudentsByCourse($courseId, $limit, $offset) {
        $query = "SELECT e.*, u.fullname, u.email, u.username 
                  FROM " . $this->table . " e
                  JOIN users u ON e.student_id = u.id
                  WHERE e.course_id = :course_id
                  ORDER BY e.enrolled_date DESC
                  LIMIT :limit OFFSET :offset";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':course_id', $courseId);
        $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
        $stmt->bindParam(':offset', $offset, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt;
    }

    // Đếm tổng số học viên của khóa học (để phân trang)
    public function countByCourse($courseId) {
        $query = "SELECT COUNT(*) as total FROM " . $this->table . " WHERE course_id = :course_id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':course_id', $courseId);
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row['total'];
    }

    // Lấy danh sách học viên vừa đăng ký MỚI NHẤT (của bất kỳ khóa học nào thuộc giảng viên)
    public function getRecentActivity($instructorId, $limit = 5) {
        $query = "SELECT e.*, u.fullname as student_name, c.title as course_title, c.id as course_id
                FROM enrollments e
                JOIN users u ON e.student_id = u.id
                JOIN courses c ON e.course_id = c.id
                WHERE c.instructor_id = :instructor_id
                ORDER BY e.enrolled_date DESC
                LIMIT :limit";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':instructor_id', $instructorId);
        $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt;
    }

    // Đếm tổng số học viên của giảng viên (Distinct student - 1 người học 2 khóa tính là 1 hoặc tùy logic, ở đây mình đếm tổng lượt enroll)
    public function countTotalEnrollments($instructorId) {
        $query = "SELECT COUNT(*) as total
                FROM enrollments e
                JOIN courses c ON e.course_id = c.id
                WHERE c.instructor_id = :instructor_id AND e.status != 'dropped'";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':instructor_id', $instructorId);
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row['total'];
>>>>>>> feature/instructor-student-dashboard
    }
}
?>