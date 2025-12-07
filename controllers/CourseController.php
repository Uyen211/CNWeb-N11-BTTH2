<?php
// controllers/CourseController.php

require_once './config/Database.php';
require_once './models/Course.php';

class CourseController {
    private $db;
    private $courseModel;

    public function __construct() {
        $database = new Database();
        $this->db = $database->connect();
        $this->courseModel = new Course($this->db);
    }

    // Routes:
    // GET: index.php?controller=course&action=index (List/Manage)
    // GET: index.php?controller=course&action=create (Show Form)
    // POST: index.php?controller=course&action=create (Process Form)
    // GET: index.php?controller=course&action=edit&id=X (Show Form)
    // POST: index.php?controller=course&action=edit&id=X (Process Form)
    // POST: index.php?controller=course&action=delete (Process Delete)

    // --- MANAGE PAGE (INDEX) ---
    public function index() {
        
        // CSS cho trang này
        $css_files = ['assets/css/course.css'];

        $currentUser = $_SESSION['user'] ?? null; // Dùng null thay vì chuỗi rỗng để kiểm tra isset an toàn hơn

        // Kiểm tra xem $currentUser có tồn tại (không phải null) VÀ role có bằng 1 không
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
            
            // Render View: Gọi Header/Sidebar/Footer TẠI ĐÂY
            require_once 'views/layouts/header.php';
            require_once 'views/layouts/sidebar.php';
            require_once 'views/instructor/course/manage.php';
            require_once 'views/layouts/footer.php';
        } else {
             // Logic cho Student (chưa được phát triển, cần phát triển tiếp vào đây)
             header("Location: index.php");
        }
    }

    // --- CREATE ---
    public function create() {
        $this->requireInstructor();

        // Xử lý POST (Lưu dữ liệu)
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->validateCSRF();
            $data = $this->sanitizeInput($_POST);
            $errors = $this->validateCourseData($data, $_FILES['image']);

            if (empty($errors)) {
                $imagePath = $this->handleImageUpload($_FILES['image']);
                if ($imagePath) {
                    $this->courseModel->title = $data['title'];
                    $this->courseModel->description = $data['description'];
                    $this->courseModel->instructor_id = $_SESSION['user']['id'];
                    $this->courseModel->category_id = $data['category_id'];
                    $this->courseModel->price = $data['price'];
                    $this->courseModel->duration_weeks = $data['duration_weeks'];
                    $this->courseModel->level = $data['level'];
                    $this->courseModel->image = $imagePath;

                    if ($this->courseModel->create()) {
                        $_SESSION['success'] = "Tạo khóa học thành công! Đang chờ duyệt.";
                        header("Location: index.php?controller=course&action=index");
                        exit;
                    } else {
                        $_SESSION['error'] = "Lỗi hệ thống.";
                    }
                } else {
                     $_SESSION['error'] = "Lỗi upload ảnh.";
                }
            } else {
                $_SESSION['error'] = implode("<br>", $errors);
            }
            header("Location: index.php?controller=course&action=index"); // Quay lại trang list nếu lỗi
            exit;
        }

        // Xử lý GET (Hiển thị Form cho Popup)
        // Chỉ trả về nội dung file view, KHÔNG kèm header/footer
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
                header("Location: index.php?controller=course&action=index");
            } else {
                echo "Access Denied"; // Trả về text lỗi cho AJAX
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
            header("Location: index.php?controller=course&action=index");
            exit;
        }

        // Xử lý GET (Hiển thị Form cho Popup)
        // Chỉ trả về nội dung file view, KHÔNG kèm header/footer
        $categories = $this->courseModel->getCategories();
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
                header("Location: index.php?controller=course&action=index");
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
        header("Location: index.php?controller=course&action=index");
        exit;
    }

    public function detail() {
        
        $id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
        $course = $this->courseModel->getById($id);

        if (!$course) {
            echo "<p class='text-danger text-center'>Không tìm thấy khóa học.</p>";
            exit;
        }

        require 'views/instructor/course/detail.php';
    }

    // --- HELPERS ---
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

    // tạo ra và lưu trữ một mã thông báo bí mật (token).
    private function generateCSRF() {
        if (empty($_SESSION['csrf_token'])) $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        return $_SESSION['csrf_token'];
    }
    
    // kiểm tra mã thông báo CSRF từ biểu mẫu với mã đã lưu trong phiên làm việc.
    private function validateCSRF() {
        if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) die("CSRF Error");
    }

    // Hàm làm sạch dữ liệu đầu vào
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