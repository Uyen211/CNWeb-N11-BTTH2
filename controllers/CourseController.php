<?php
require_once dirname(__DIR__) . '/models/Course.php';
require_once dirname(__DIR__) . '/models/Category.php';

class CourseController {
    private $courseModel;
    private $categoryModel;
    private $db;

    public function __construct($db) {
        $this->db = $db;
        // Khởi tạo Model ở đây
        $this->courseModel = new Course($db);
        
        if (class_exists('Category')) {
            $this->categoryModel = new Category($db);
        }
    }

    public function index() {
        // 1. Lấy tham số từ URL
        $keyword = isset($_GET['keyword']) ? trim($_GET['keyword']) : null;
        $category_id = isset($_GET['category']) ? $_GET['category'] : null;

        // 2. Gọi hàm đa năng từ Model (Course.php)
        $courses = $this->courseModel->getPublicCourses($keyword, $category_id);

        // 3. Lấy danh mục cho Sidebar
        $categories = [];
        if ($this->categoryModel) {
             $categories = $this->categoryModel->getAll();
        }

        // 4. Gọi View hiển thị
        require dirname(__DIR__) . '/views/courses/index.php';
    }

    public function detail() {
        if (isset($_GET['id'])) {
            $id = $_GET['id'];
            $course = $this->courseModel->getById($id);
            require dirname(__DIR__) . '/views/courses/detail.php';
        } else {
            header("Location: index.php?controller=course&action=index");
        }
    }
}
?>