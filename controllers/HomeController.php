<?php
// Load các models cần thiết
require_once 'models/Course.php';
require_once 'models/Category.php';

class HomeController {
    private $courseModel;
    private $categoryModel;
    private $db;

    public function __construct($db) {
        $this->db = $db;
        $this->courseModel = new Course($db);
        $this->categoryModel = new Category($db);
    }

    public function index() {
        // 1. ĐIỀU HƯỚNG NGƯỜI DÙNG ĐÃ ĐĂNG NHẬP (Tuỳ chọn)
        // Nếu là Admin hoặc Giảng viên thì vào thẳng Dashboard quản trị cho tiện
        if (isset($_SESSION['role'])) {
            if ($_SESSION['role'] == 2) { // Admin
                header("Location: index.php?controller=admin&action=dashboard");
                exit;
            }
            if ($_SESSION['role'] == 1) { // Instructor
                header("Location: index.php?controller=instructor&action=dashboard");
                exit;
            }
            // Nếu là Học viên (Role 0) thì vẫn cho xem trang chủ để mua thêm khóa học
        }

        // 2. LẤY DỮ LIỆU HIỂN THỊ
        // Lấy 8 khóa học mới nhất
        $latestCourses = $this->courseModel->getLatestCourses(8);
        
        // Lấy danh sách danh mục
        $categories = $this->categoryModel->getAll();

        // 3. GỌI VIEW
        require 'views/home/index.php';
    }

}
?>