<?php
// models/Lesson.php

class Lesson {
    private $conn;
    private $table = 'lessons';

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

    public function countAll($courseId) {
        $query = "SELECT COUNT(*) as total FROM " . $this->table . " WHERE course_id = :course_id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':course_id', $courseId);
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row['total'];
    }

    // Lấy danh sách lesson có phân trang và join tên khóa học
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
        return $stmt;
    }

    public function getAll($courseId) {
        $query = "SELECT * FROM " . $this->table . " WHERE course_id = :course_id ORDER BY `order` ASC";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':course_id', $courseId);
        $stmt->execute();
        return $stmt;
    }

    public function getById($id) {
        $query = "SELECT * FROM " . $this->table . " WHERE id = :id LIMIT 1";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($row) {
            $this->id = $row['id'];
            $this->courseId = $row['course_id'];
            $this->title = $row['title'];
            $this->content = $row['content'];
            $this->videoUrl = $row['video_url'];
            $this->order = $row['order'];
            $this->createdAt = $row['created_at'];
            return true;
        }
        return false;
    }

    public function insert() {
        $query = "INSERT INTO " . $this->table . " 
                  (course_id, title, content, video_url, `order`, created_at) 
                  VALUES (:course_id, :title, :content, :video_url, :order, NOW())";
        
        $stmt = $this->conn->prepare($query);

        // Clean data
        $this->title = htmlspecialchars(strip_tags($this->title));
        $this->videoUrl = htmlspecialchars(strip_tags($this->videoUrl));
        
        $stmt->bindParam(':course_id', $this->courseId);
        $stmt->bindParam(':title', $this->title);
        $stmt->bindParam(':content', $this->content); // Content might allow HTML (WYSIWYG)
        $stmt->bindParam(':video_url', $this->videoUrl);
        $stmt->bindParam(':order', $this->order);

        if ($stmt->execute()) {
            return true;
        }
        return false;
    }

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