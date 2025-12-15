<?php
// models/Enrollment.php

class Enrollment {
    private $conn;
    private $table = 'enrollments';

    public function __construct($db) {
        $this->conn = $db;
    }

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
    }
}
?>