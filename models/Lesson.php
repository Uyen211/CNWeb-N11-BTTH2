<?php
class Lesson {
    private $conn;
    private $table = 'lessons';

    // Khai báo thuộc tính (Cần thiết cho chức năng Create/Update của Instructor)
    public $id;
    public $courseId;
    public $title;
    public $content;
    public $videoUrl;
    public $order;
    public $createdAt;

    public function __construct($db) {
        $this->conn = $db;
    }

    // --- PHẦN 1: READ METHODS (Dùng chung & Student View) ---

    // Lấy danh sách bài học theo khóa học (Dùng cho Sidebar Student)
    public function getByCourseId($course_id) {
        $query = "SELECT * FROM " . $this->table . " WHERE course_id = :course_id ORDER BY `order` ASC";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':course_id', $course_id);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Lấy chi tiết 1 bài học (Hàm lai ghép quan trọng)
    public function getById($id) {
        $query = "SELECT * FROM " . $this->table . " WHERE id = :id LIMIT 0,1";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($row) {
            // 1. Gán vào thuộc tính object (Để phục vụ logic Edit/Delete của Instructor)
            $this->id = $row['id'];
            $this->courseId = $row['course_id'];
            $this->title = $row['title'];
            $this->content = $row['content'];
            $this->videoUrl = $row['video_url'];
            $this->order = $row['order'];
            $this->createdAt = $row['created_at'];

            // 2. Trả về mảng (Để phục vụ logic hiển thị View của Student)
            return $row;
        }
        
        return false;
    }

    // Lấy tài liệu của bài học (Helper cho Student View)
    public function getMaterials($lesson_id) {
        // Lưu ý: Nếu đã tách MaterialModel riêng thì nên dùng bên Controller,
        // nhưng giữ hàm này ở đây để tiện lấy nhanh cho View
        $query = "SELECT * FROM materials WHERE lesson_id = :lesson_id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':lesson_id', $lesson_id);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // --- PHẦN 2: INSTRUCTOR MANAGE METHODS (CRUD) ---

    // Đếm tổng số bài học (Phân trang)
    public function countAll($courseId) {
        $query = "SELECT COUNT(*) as total FROM " . $this->table . " WHERE course_id = :course_id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':course_id', $courseId);
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row['total'];
    }

    // Lấy danh sách lesson có phân trang (Instructor View)
    public function getPaginated($courseId, $limit, $offset) {
        $query = "SELECT l.*, c.title as course_title 
                  FROM " . $this->table . " l
                  JOIN courses c ON l.course_id = c.id
                  WHERE l.course_id = :course_id
                  ORDER BY l.order ASC, l.id ASC
                  LIMIT :limit OFFSET :offset";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':course_id', $courseId, PDO::PARAM_INT);
        $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
        $stmt->bindParam(':offset', $offset, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt; // Trả về statement để fetch trong vòng lặp
    }

    // Thêm bài học mới
    public function insert() {
        $query = "INSERT INTO " . $this->table . " 
                  (course_id, title, content, video_url, `order`, created_at) 
                  VALUES (:course_id, :title, :content, :video_url, :order, NOW())";
        
        $stmt = $this->conn->prepare($query);

        // Clean data
        $this->title = htmlspecialchars(strip_tags($this->title));
        $this->videoUrl = htmlspecialchars(strip_tags($this->videoUrl));
        
        // Bind data từ thuộc tính object
        $stmt->bindParam(':course_id', $this->courseId);
        $stmt->bindParam(':title', $this->title);
        $stmt->bindParam(':content', $this->content); // Content có thể chứa HTML (WYSIWYG)
        $stmt->bindParam(':video_url', $this->videoUrl);
        $stmt->bindParam(':order', $this->order);

        if ($stmt->execute()) {
            return true;
        }
        return false;
    }

    // Cập nhật bài học
    public function update() {
        $query = "UPDATE " . $this->table . " 
                  SET title = :title, 
                      content = :content, 
                      video_url = :video_url, 
                      `order` = :order 
                  WHERE id = :id";
        
        $stmt = $this->conn->prepare($query);

        $this->title = htmlspecialchars(strip_tags($this->title));
        $this->videoUrl = htmlspecialchars(strip_tags($this->videoUrl));
        
        $stmt->bindParam(':title', $this->title);
        $stmt->bindParam(':content', $this->content);
        $stmt->bindParam(':video_url', $this->videoUrl);
        $stmt->bindParam(':order', $this->order);
        $stmt->bindParam(':id', $this->id);

        if ($stmt->execute()) {
            return true;
        }
        return false;
    }

    // Xóa bài học
    public function delete() {
        $query = "DELETE FROM " . $this->table . " WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $this->id);
        if ($stmt->execute()) {
            return true;
        }
        return false;
    }
}
?>