<?php

require_once './config/Database.php';
require_once './models/Course.php';
// Kiểm tra nếu cần load thêm Model Category
if (file_exists('./models/Category.php')) {
    require_once './models/Category.php';
}

class CourseController {
    private $courseModel;
    private $categoryModel;
    private $db;

    public function __construct($db) {
        $this->db = $db;
        // Khởi tạo Course Model
        $this->courseModel = new Course($db);
        
        // Khởi tạo Category Model nếu class tồn tại (Logic của bạn)
        if (class_exists('Category')) {
            $this->categoryModel = new Category($db);
        }
    }

    // =================================================================
    // PHẦN 1: PUBLIC / STUDENT VIEW 
    // =================================================================

    // Trang chủ hiển thị danh sách khóa học (Dành cho Student/Guest)
    public function index() {
        // 1. Lấy tham số từ URL
        $keyword = isset($_GET['keyword']) ? trim($_GET['keyword']) : null;
        $category_id = isset($_GET['category']) ? $_GET['category'] : null;

        // CSS cho trang này (Kết hợp ý tưởng load CSS của bạn bè nếu cần)
        $css_files = ['course.css'];

        // 2. Gọi hàm đa năng từ Model (Course.php)
        // Lưu ý: Đảm bảo method getPublicCourses tồn tại trong Model của bạn
        $courses = $this->courseModel->getPublicCourses($keyword, $category_id);

        // 3. Lấy danh mục cho Sidebar
        $categories = [];
        if ($this->categoryModel) {
             $categories = $this->categoryModel->getAll();
        } elseif (method_exists($this->courseModel, 'getCategories')) {
             // Fallback: Nếu không có CategoryModel riêng, dùng hàm trong CourseModel của bạn bè
             $categories = $this->courseModel->getCategories();
        }

        // 4. Gọi View hiển thị
        // Đường dẫn view nên thống nhất, ở đây tôi giữ nguyên logic của bạn
        require dirname(__DIR__) . '/views/courses/index.php';
    }

    // Trang chi tiết khóa học (Public)
    public function detail() {
        if (isset($_GET['id'])) {
            $id = $_GET['id'];
            $course = $this->courseModel->getById($id);
            require dirname(__DIR__) . '/views/courses/detail.php';
        } else {
            header("Location: index.php?controller=course&action=index");
        }
    }

    // =================================================================
    // PHẦN 2: INSTRUCTOR DASHBOARD 
    // =================================================================

    // Đã đổi tên từ index() của bạn bè thành instructor_courses() để không trùng lặp
    public function instructor_courses() {
        $this->requireInstructor(); // Yêu cầu phải là Instructor mới vào được
        
        $currentUser = $_SESSION['user'] ?? null;
        $isInstructor = ($currentUser && $currentUser['role'] == 1); 

        // Pagination & Sort
        $limit = isset($_GET['limit']) ? (int)$_GET['limit'] : 10;
        $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
        $offset = ($page - 1) * $limit;
        $keyword = isset($_GET['search']) ? trim($_GET['search']) : "";
        $sort = isset($_GET['sort']) ? $_GET['sort'] : 'created_at DESC';

        if ($isInstructor) {
            $totalCourses = $this->courseModel->countAll($currentUser['id'], $keyword);
            $courses = $this->courseModel->getPaginated($limit, $offset, $currentUser['id'], $keyword, $sort);
            $totalPages = ceil($totalCourses / $limit);
            
            // Render View: Gọi Header/Sidebar/Footer
            require_once 'views/layouts/header.php';
            require_once 'views/layouts/sidebar.php';
            require_once 'views/instructor/my_courses.php'; // View quản lý của giảng viên
            require_once 'views/layouts/footer.php';
        } else {
             header("Location: index.php");
        }
    }

    // --- MANAGE HUB (Quản trị chi tiết 1 khóa học) ---
    public function manage() {
        $this->requireInstructor();

        $id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
        $course = $this->courseModel->getById($id);

        if (!$course || $course['instructor_id'] != $_SESSION['user']['id']) {
            $_SESSION['error'] = "Khóa học không tồn tại hoặc không có quyền truy cập.";
            header("Location: index.php?controller=course&action=instructor_courses");
            exit;
        }
        
        // Lấy tổng số học viên
        $totalStudents = $this->courseModel->countStudents($id);

        // Tính doanh thu ước tính
        $totalRevenue = $totalStudents * $course['price'];

        // Lấy tiến độ trung bình
        $avgProgress = $this->courseModel->getAverageProgress($id);

        $css_files = ['course_hub.css']; 
        
        require_once 'views/layouts/header.php';
        require_once 'views/layouts/sidebar.php';
        require_once 'views/instructor/course/manage.php';
        require_once 'views/layouts/footer.php';
    }
    // controllers/CourseController.php

    public function create() {
        $this->requireInstructor();

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->validateCSRF();
            $data = $this->sanitizeInput($_POST);
            $errors = $this->validateCourseData($data, $_FILES['image']);

            if (empty($errors)) {
                $imagePath = $this->handleImageUpload($_FILES['image']);
                if ($imagePath) {
                    // Set data
                    $this->courseModel->title = $data['title'];
                    $this->courseModel->description = $data['description'];
                    $this->courseModel->instructor_id = $_SESSION['user']['id'];
                    $this->courseModel->category_id = $data['category_id'];
                    $this->courseModel->price = $data['price'];
                    $this->courseModel->duration_weeks = $data['duration_weeks'];
                    $this->courseModel->level = $data['level'];
                    $this->courseModel->image = $imagePath;
                    

                    if ($this->courseModel->create()) {
                        // --- SỬA MESSAGE ---
                        $_SESSION['success'] = "Gửi yêu cầu tạo khóa học thành công! Vui lòng chờ Admin phê duyệt.";
                        header("Location: index.php?controller=course&action=instructor_courses");
                        exit;
                    } else {
                        $_SESSION['error'] = "Lỗi hệ thống không thể tạo khóa học.";
                    }
                } else {
                    $_SESSION['error'] = "Lỗi upload ảnh.";
                }
            } else {
                $_SESSION['error'] = implode("<br>", $errors);
            }
            // Nếu lỗi thì redirect lại form (hoặc giữ nguyên trang để hiện lỗi - tùy logic view)
            // Ở đây redirect về list theo code cũ của bạn
            header("Location: index.php?controller=course&action=instructor_courses");
            exit;
        }

        // Phần GET hiển thị form giữ nguyên
        $categories = $this->courseModel->getCategories(); 
        $csrf_token = $this->generateCSRF();
        require 'views/instructor/course/create.php';
    }

    // --- EDIT ---
    public function edit() {
        $this->requireInstructor();
        $id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
        
        // Kiểm tra quyền sở hữu
        $course = $this->courseModel->getById($id);
        if (!$course || $course['instructor_id'] != $_SESSION['user']['id']) {
            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                $_SESSION['error'] = "Không có quyền chỉnh sửa.";
                header("Location: index.php?controller=course&action=instructor_courses");
            } else {
                echo "Access Denied"; 
            }
            exit;
        }

        // Xử lý POST (Cập nhật)
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->validateCSRF();
            $data = $this->sanitizeInput($_POST);
            $errors = $this->validateCourseData($data, $_FILES['image'], true);

            if (empty($errors)) {
                $imagePath = $course['image'];
                if (!empty($_FILES['image']['name'])) {
                    $uploaded = $this->handleImageUpload($_FILES['image']);
                    if ($uploaded) $imagePath = $uploaded;
                }

                $this->courseModel->id = $id;
                $this->courseModel->instructor_id = $_SESSION['user']['id'];
                $this->courseModel->title = $data['title'];
                $this->courseModel->description = $data['description'];
                $this->courseModel->category_id = $data['category_id'];
                $this->courseModel->price = $data['price'];
                $this->courseModel->duration_weeks = $data['duration_weeks'];
                $this->courseModel->level = $data['level'];
                $this->courseModel->image = $imagePath;

                if ($this->courseModel->update()) {
                    $_SESSION['success'] = "Cập nhật khóa học thành công.";
                } else {
                    $_SESSION['error'] = "Lỗi cập nhật.";
                }
            } else {
                $_SESSION['error'] = implode("<br>", $errors);
            }
            header("Location: index.php?controller=course&action=manage&id=" . $id);
            exit;
        }

        // Xử lý GET
        $categories = method_exists($this->courseModel, 'getCategories') 
                    ? $this->courseModel->getCategories() 
                    : ($this->categoryModel ? $this->categoryModel->getAll() : []);
        $csrf_token = $this->generateCSRF();
        require 'views/instructor/course/edit.php';
    }

    // --- DELETE ---
    public function delete() {
        $this->requireInstructor();
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->validateCSRF();
            $id = isset($_POST['id']) ? (int)$_POST['id'] : 0;

            $course = $this->courseModel->getById($id);
            if (!$course || $course['instructor_id'] != $_SESSION['user']['id']) {
                $_SESSION['error'] = "Không có quyền xóa.";
                header("Location: index.php?controller=course&action=instructor_courses");
                exit;
            }

            if ($this->courseModel->hasActiveEnrollments($id)) {
                $_SESSION['error'] = "Không thể xóa khóa học đang hoạt động.";
            } else {
                $this->courseModel->id = $id;
                $this->courseModel->instructor_id = $_SESSION['user']['id'];
                if ($this->courseModel->delete()) {
                    $_SESSION['success'] = "Đã xóa khóa học.";
                } else {
                    $_SESSION['error'] = "Lỗi xóa.";
                }
            }
        }
        header("Location: index.php?controller=course&action=instructor_courses");
        exit;
    }

    // =================================================================
    // HELPER METHODS
    // =================================================================

    private function requireLogin() {
        if (!isset($_SESSION['user'])) {
            header("Location: index.php?controller=auth&action=login");
            exit;
        }
    }

    private function requireInstructor() {
        $this->requireLogin();
        if ($_SESSION['user']['role'] != 1) {
            header("Location: index.php"); exit;
        }
    }

    private function generateCSRF() {
        if (empty($_SESSION['csrf_token'])) $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        return $_SESSION['csrf_token'];
    }
    
    private function validateCSRF() {
        if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) die("CSRF Error");
    }

    private function sanitizeInput($input) { 
        return array_map('trim', $input); 
    }

    private function validateCourseData($data, $file, $isUpdate = false) {
        $errors = [];
        if (empty($data['title'])) $errors[] = "Thiếu tên khóa học.";
        if (!$isUpdate && empty($file['name'])) $errors[] = "Thiếu ảnh bìa.";
        return $errors;
    }

    private function handleImageUpload($file) {
        $targetDir = "assets/uploads/courses/";
        if (!file_exists($targetDir)) mkdir($targetDir, 0777, true);
        $fileName = uniqid() . "." . pathinfo($file["name"], PATHINFO_EXTENSION);
        if (move_uploaded_file($file["tmp_name"], $targetDir . $fileName)) return $fileName;
        return false;
    }
}
?>