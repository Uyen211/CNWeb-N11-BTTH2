<?php

class Category
{
    private $conn;
    private $table = 'categories';


    public function __construct($db)
    {
        $this->conn = $db;
    }

    // Hàm đếm tổng số bản ghi (Để tính số trang)
    public function countAll() {
        $query = "SELECT COUNT(*) as total FROM " . $this->table;
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row['total'];
    }

    // Hàm lấy danh sách có Phân trang & Sắp xếp
    public function getPaginated($limit, $offset) {
        // Sắp xếp: Mới nhất lên đầu (DESC) hoặc Cũ nhất lên đầu (ASC)
        $query = "SELECT * FROM " . $this->table . " 
                  ORDER BY id ASC 
                  LIMIT :limit OFFSET :offset";
        
        $stmt = $this->conn->prepare($query);
        
        // Bind Param (Lưu ý: PDO LIMIT/OFFSET cần kiểu INT)
        $stmt->bindValue(':limit', (int) $limit, PDO::PARAM_INT);
        $stmt->bindValue(':offset', (int) $offset, PDO::PARAM_INT);
        
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }


    // Lấy tất cả danh mục
    public function getAll()
    {
        $query = "SELECT * FROM " . $this->table . " ORDER BY created_at DESC";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }


    // Lấy chi tiết 1 danh mục theo ID
    public function getById($id)
    {
        $query = "SELECT * FROM " . $this->table . " WHERE id = :id LIMIT 1";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC); //quy định kết quả trả về là mảng kết hợp
    }


    // Tạo mới danh mục
    public function create($name, $description)
    {
        $query = "INSERT INTO " . $this->table . " (name, description, created_at) VALUES (:name, :description, NOW())";
        $stmt = $this->conn->prepare($query);


        // Sanitize & Bind
        $name = htmlspecialchars(strip_tags($name));
        $description = htmlspecialchars(strip_tags($description));


        $stmt->bindParam(':name', $name);
        $stmt->bindParam(':description', $description);


        if ($stmt->execute()) {
            return true;
        }
        return false;
    }


    // Cập nhật danh mục
    public function update($id, $name, $description)
    {
        $query = "UPDATE " . $this->table . " SET name = :name, description = :description WHERE id = :id";
        $stmt = $this->conn->prepare($query);


        //strip_tags: loại bỏ tất cả các thẻ HTML, XML và PHP
        //htmlspecialchars: chuyển đổi các ký tự đặc biệt thành thực thể HTML
        $name = htmlspecialchars(strip_tags($name));
        $description = htmlspecialchars(strip_tags($description));


        $stmt->bindParam(':name', $name);
        $stmt->bindParam(':description', $description);
        $stmt->bindParam(':id', $id);


        if ($stmt->execute()) {
            return true;
        }
        return false;
    }


    // Kiểm tra danh mục có đang được sử dụng bởi khóa học nào không
    public function hasCourses($id)
    {
        $query = "SELECT COUNT(*) as total FROM courses WHERE category_id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row['total'] > 0;
    }


    // Xóa danh mục
    public function delete($id)
    {
        $query = "DELETE FROM " . $this->table . " WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id);


        if ($stmt->execute()) {
            return true;
        }
        return false;
    }
}
