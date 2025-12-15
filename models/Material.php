<?php
// models/Material.php

class Material {
    private $conn;
    private $table = 'materials';

    public $id;
    public $lessonId;
    public $filename;
    public $filePath;
    public $fileType;
    public $uploadedAt;

    public function __construct($db) {
        $this->conn = $db;
    }

    public function getByLessonId($lessonId) {
        $query = "SELECT * FROM " . $this->table . " WHERE lesson_id = :lesson_id ORDER BY uploaded_at DESC";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':lesson_id', $lessonId);
        $stmt->execute();
        return $stmt;
    }

    public function insert() {
        $query = "INSERT INTO " . $this->table . " 
                  (lesson_id, filename, file_path, file_type, uploaded_at) 
                  VALUES (:lesson_id, :filename, :file_path, :file_type, NOW())";
        
        $stmt = $this->conn->prepare($query);

        $this->filename = htmlspecialchars(strip_tags($this->filename));
        $this->filePath = htmlspecialchars(strip_tags($this->filePath));
        $this->fileType = htmlspecialchars(strip_tags($this->fileType));

        $stmt->bindParam(':lesson_id', $this->lessonId);
        $stmt->bindParam(':filename', $this->filename);
        $stmt->bindParam(':file_path', $this->filePath);
        $stmt->bindParam(':file_type', $this->fileType);

        if ($stmt->execute()) {
            return true;
        }
        return false;
    }
}