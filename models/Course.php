<?php
class Course {
    private $conn;
    private $table = 'courses';

    public $id;
    public $title;
    public $description;
    public $instructor_id;
    public $category_id;
    public $price;
    public $image;
    public $created_at;

    public function __construct($db) {
        $this->conn = $db;
    }

    // --- 1. HÀM ĐA NĂNG: LẤY DANH SÁCH + TÌM KIẾM (CHỈ TITLE) + LỌC ---
    public function getPublicCourses($keyword = null, $category_id = null) {
        // Khởi tạo câu truy vấn cơ bản
        $query = "SELECT c.*, u.fullname as instructor_name, cat.name as category_name 
                  FROM " . $this->table . " c
                  LEFT JOIN users u ON c.instructor_id = u.id
                  LEFT JOIN categories cat ON c.category_id = cat.id
                  WHERE 1=1"; 

        // Mảng chứa tham số (Đây là chìa khóa để sửa lỗi)
        $params = [];

        // 1. Logic Tìm kiếm
        if (!empty($keyword)) {
            // YÊU CẦU CỦA BẠN: Chỉ tìm trong title (Bỏ description)
            $query .= " AND c.title LIKE :keyword";
            
            // Thêm dấu % vào đây để tìm kiếm tương đối (Ví dụ: "py" ra "python")
            $params[':keyword'] = "%" . $keyword . "%";
        }

        // 2. Logic Lọc danh mục
        if (!empty($category_id)) {
            $query .= " AND c.category_id = :category_id";
            $params[':category_id'] = $category_id;
        }

        // 3. Sắp xếp: Mặc định khóa mới nhất lên đầu
        $query .= " ORDER BY c.created_at DESC";

        // Chuẩn bị statement
        $stmt = $this->conn->prepare($query);

        // THỰC THI VỚI MẢNG PARAM (Sửa triệt để lỗi Invalid parameter number)
        $stmt->execute($params);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // --- 2. LẤY CHI TIẾT KHÓA HỌC THEO ID ---
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

    // --- 3. LẤY KHÓA HỌC MỚI NHẤT (Cho trang chủ) ---
    public function getLatestCourses($limit = 8) {
        $query = "SELECT c.id, c.title, c.image, c.price, c.level, 
                         u.fullname as instructor_name, cat.name as category_name
                  FROM " . $this->table . " c
                  LEFT JOIN users u ON c.instructor_id = u.id
                  LEFT JOIN categories cat ON c.category_id = cat.id
                  ORDER BY c.created_at DESC 
                  LIMIT :limit";

        $stmt = $this->conn->prepare($query);
        $stmt->bindValue(':limit', (int)$limit, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
?>