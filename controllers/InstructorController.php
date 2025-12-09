<?php
require_once 'models/Course.php';
require_once 'models/Category.php';

class InstructorController {
    private $db;

    public function __construct() {
        $database = new Database();
        $this->db = $database->getConnection();

        // CHECK QUYỀN: Phải là Giảng viên (role = 1)
        if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 1) {
            header("Location: index.php?controller=auth&action=login");
            exit();
        }
    }

    // Dashboard: Hiển thị danh sách khóa học do giảng viên này tạo
    public function dashboard() {
        $instructor_id = $_SESSION['user_id'];

        $courseModel = new Course($this->db);
        $courses = $courseModel->getByInstructorId($instructor_id);

        require 'views/instructor/dashboard.php';
    }

    // Hiển thị form tạo khóa học mới (GET)
    public function create() {
        // Cần lấy danh sách Category để hiển thị trong select box
        $categoryModel = new Category($this->db);
        $categories = $categoryModel->getAll();

        require 'views/instructor/course/create.php';
    }

    // Xử lý lưu khóa học mới vào CSDL (POST)
    public function store() {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $courseModel = new Course($this->db);
            
            // Lấy dữ liệu từ form
            $title = $_POST['title'];
            $description = $_POST['description'];
            $price = $_POST['price'];
            $category_id = $_POST['category_id'];
            $instructor_id = $_SESSION['user_id']; // Tự động lấy ID người đang login

            // (Lưu ý: Phần này cần thêm logic Upload ảnh, ở đây mình viết gọn)
            $image = "default.jpg"; 

            if ($courseModel->create($title, $description, $instructor_id, $category_id, $price, $image)) {
                header("Location: index.php?controller=instructor&action=dashboard");
            } else {
                echo "Lỗi khi tạo khóa học.";
            }
        }
    }

    // Xóa khóa học
    public function delete() {
        if (isset($_GET['id'])) {
            $course_id = $_GET['id'];
            $courseModel = new Course($this->db);
            
            // Cần kiểm tra xem khóa học này có đúng là của giảng viên này không trước khi xóa
            if ($courseModel->isOwner($_SESSION['user_id'], $course_id)) {
                $courseModel->delete($course_id);
            }
            
            header("Location: index.php?controller=instructor&action=dashboard");
        }
    }
}
?>