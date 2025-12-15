<?php
class Lesson {
    private $conn;
    private $table = 'lessons';

    public function __construct($db) {
        $this->conn = $db;
    }

    // Lấy danh sách bài học theo khóa học
    public function getByCourseId($course_id) {
        $query = "SELECT * FROM " . $this->table . " WHERE course_id = :course_id ORDER BY `order` ASC";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':course_id', $course_id);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Lấy chi tiết 1 bài học
    public function getById($id) {
        $query = "SELECT * FROM " . $this->table . " WHERE id = :id LIMIT 0,1";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // Lấy tài liệu của bài học (Bảng materials)
    public function getMaterials($lesson_id) {
        $query = "SELECT * FROM materials WHERE lesson_id = :lesson_id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':lesson_id', $lesson_id);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
?>