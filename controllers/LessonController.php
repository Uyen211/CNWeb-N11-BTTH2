<?php
// controllers/LessonController.php
require_once 'config/Database.php';
require_once 'models/Lesson.php';
require_once 'models/Material.php';
require_once 'models/Course.php'; 

class LessonController {
    private $db;
    private $lessonModel;
    private $materialModel;
    private $courseModel;

    public function __construct() {
        $database = new Database();
        $this->db = $database->connect();
        $this->lessonModel = new Lesson($this->db);
        $this->materialModel = new Material($this->db);
        $this->courseModel = new Course($this->db);
        
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
    }

    // Helper: Kiểm tra quyền instructor và sở hữu khóa học
    private function checkAuthorization($courseId) {
        // Kiểm tra đăng nhập
        if (!isset($_SESSION['user']['id'])) {
            header("Location: /auth/login.php");
            exit;
        }
        
        // Kiểm tra Role = 1 (Giảng viên)
        // Nếu role khác 1 thì chỉ được xem (Logic xử lý ở view hoặc action riêng, ở đây ta chặn action sửa đổi)
        if ($_SESSION['user']['role'] != 1) {
            $_SESSION['error'] = "Bạn không có quyền thực hiện hành động này.";
            header("Location: /index.php"); // Redirect về trang chủ hoặc trang phù hợp
            exit;
        }

        $instructorId = $_SESSION['user']['id'];
        if (!$this->courseModel->checkInstructorOwnership($courseId, $instructorId)) {
            $_SESSION['error'] = "Bạn không có quyền truy cập khóa học này.";
            header("Location: /onlinecourse/views/instructor/dashboard.php");
            exit;
        }
    }

    private function generateCsrfToken() {
        if (empty($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }
        return $_SESSION['csrf_token'];
    }

    private function checkCsrf() {
        if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
            die("CSRF Token Verification Failed");
        }
    }
// controllers/LessonController.php

    public function index() {
        $courseId = isset($_GET['course_id']) ? intval($_GET['course_id']) : 0;
        
        // 1. Kiểm tra quyền sở hữu 
        $this->checkAuthorization($courseId);

        // Gọi hàm getById từ Model 
        $course = $this->courseModel->getById($courseId);
        
        // Kiểm tra nếu không tìm thấy khóa học
        if (!$course) {
             $_SESSION['error'] = "Khóa học không tồn tại.";
             header("Location: /onlinecourse/views/instructor/dashboard.php");
             exit;
        }

        // 2. Logic phân trang bài học 
        $limit = 10;
        $page = isset($_GET['page']) ? intval($_GET['page']) : 1;
        $offset = ($page - 1) * $limit;

        $lessonsStmt = $this->lessonModel->getPaginated($courseId, $limit, $offset);
        $totalLessons = $this->lessonModel->countAll($courseId);
        $totalPages = ceil($totalLessons / $limit);
        
        // 3. Load View 
        $css_files = ['course_hub.css', 'lesson.css'];
        
        require_once 'views/layouts/header.php';
        require_once 'views/layouts/sidebar.php';
        require_once 'views/instructor/lessons/manage.php'; 
        require_once 'views/layouts/footer.php';
    }

    // GET LESSON DETAIL (AJAX)
    public function detail() {
        $id = isset($_GET['id']) ? intval($_GET['id']) : 0;
        if($this->lessonModel->getById($id)) {
            $this->checkAuthorization($this->lessonModel->courseId);
            echo json_encode([
                'status' => 'success',
                'data' => [
                    'title' => $this->lessonModel->title,
                    'video_url' => $this->lessonModel->videoUrl,
                    'order' => $this->lessonModel->order,
                    'created_at' => $this->lessonModel->createdAt,
                    'content' => $this->lessonModel->content
                ]
            ]);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Not found']);
        }
        exit;
    }

    public function create() {
        $courseId = isset($_GET['course_id']) ? intval($_GET['course_id']) : 0;
        $this->checkAuthorization($courseId);
        $csrf_token = $this->generateCsrfToken();
        
        // Header/Footer required
        $css_files = ['lesson.css'];
        require_once 'views/layouts/header.php';
        require_once 'views/layouts/sidebar.php';
        require_once 'views/instructor/lessons/create.php';
        require_once 'views/layouts/footer.php';
    }

    public function store() {
        $this->checkCsrf();
        $courseId = isset($_POST['course_id']) ? intval($_POST['course_id']) : 0;
        $this->checkAuthorization($courseId);

        $this->lessonModel->courseId = $courseId;
        $this->lessonModel->title = $_POST['title'];
        $this->lessonModel->content = $_POST['content'];
        $this->lessonModel->videoUrl = $_POST['video_url'];
        $this->lessonModel->order = intval($_POST['order']);

        if($this->lessonModel->insert()) {
            $_SESSION['success'] = "Thêm bài học thành công!";
            header("Location: /onlinecourse/index.php?controller=lesson&action=index&course_id=" . $this->lessonModel->courseId);

        } else {
            $_SESSION['error'] = "Có lỗi xảy ra.";
            header("Location: " . $_SERVER['HTTP_REFERER']);
        }
    }

    public function edit() {
        $id = isset($_GET['id']) ? intval($_GET['id']) : 0;
        if($this->lessonModel->getById($id)) {
            $this->checkAuthorization($this->lessonModel->courseId);
            $lesson = $this->lessonModel;
            $csrf_token = $this->generateCsrfToken();

            $css_files = ['lesson.css'];
            require_once 'views/layouts/header.php';
            require_once 'views/layouts/sidebar.php';
            require_once 'views/instructor/lessons/edit.php';
            require_once 'views/layouts/footer.php';
        } else {
            $_SESSION['error'] = "Bài học không tồn tại.";
            header("Location: /instructor/dashboard.php");
        }
    }

    public function update() {
        $this->checkCsrf();
        $id = isset($_POST['id']) ? intval($_POST['id']) : 0;
        
        // Cần get lesson cũ để lấy courseId check quyền
        if($this->lessonModel->getById($id)) {
            $this->checkAuthorization($this->lessonModel->courseId);
            
            $this->lessonModel->id = $id;
            $this->lessonModel->title = $_POST['title'];
            $this->lessonModel->content = $_POST['content'];
            $this->lessonModel->videoUrl = $_POST['video_url'];
            $this->lessonModel->order = intval($_POST['order']);

            if($this->lessonModel->update()) {
                $_SESSION['success'] = "Cập nhật thành công!";
                 // Redirect về trang manage materials hoặc course hub
                 header("Location: /onlinecourse/index.php?controller=lesson&action=showMaterials&lesson_id=" . $id);
            } else {
                $_SESSION['error'] = "Lỗi cập nhật.";
                header("Location: " . $_SERVER['HTTP_REFERER']);
            }
        }
    }

    public function delete() {
        // Nên dùng POST/DELETE method, nhưng nếu dùng GET link cần confirm kỹ
        $id = isset($_POST['id']) ? intval($_POST['id']) : 0;
        if($this->lessonModel->getById($id)) {
            $this->checkAuthorization($this->lessonModel->courseId);
            if($this->lessonModel->delete()) {
                 $_SESSION['success'] = "Xóa bài học thành công!";
            } else {
                 $_SESSION['error'] = "Lỗi xóa bài học.";
            }
            // Quay về trang quản lý khóa học
             header("Location: /onlinecourse/index.php?controller=lesson&action=index&course_id=" . $this->lessonModel->courseId);
        }
    }

    // --- MATERIALS FUNCTIONS ---

    public function showMaterials() {
        $lessonId = isset($_GET['lesson_id']) ? intval($_GET['lesson_id']) : 0;
        
        if($this->lessonModel->getById($lessonId)) {
            $this->checkAuthorization($this->lessonModel->courseId);
            
            $lesson = $this->lessonModel;
            $materialsStmt = $this->materialModel->getByLessonId($lessonId);
            $csrf_token = $this->generateCsrfToken();

            $css_files = ['lesson.css'];
            require_once 'views/layouts/header.php';
            require_once 'views/layouts/sidebar.php';
            require_once 'views/instructor/materials/upload.php';
            require_once 'views/layouts/footer.php';
        } else {
            header("Location: /onlinecourse/instructor/dashboard.php");
        }
    }

    public function storeMaterial() {
        $this->checkCsrf();
        $lessonId = isset($_POST['lesson_id']) ? intval($_POST['lesson_id']) : 0;

        if($this->lessonModel->getById($lessonId)) {
            $this->checkAuthorization($this->lessonModel->courseId);

            if(isset($_FILES['material_file']) && $_FILES['material_file']['error'] == 0) {
                $allowed = ['pdf' => 'application/pdf', 'mp4' => 'video/mp4', 'doc' => 'application/msword', 'docx' => 'application/vnd.openxmlformats-officedocument.wordprocessingml.document'];
                $filename = $_FILES['material_file']['name'];
                $filetype = $_FILES['material_file']['type'];
                $filesize = $_FILES['material_file']['size'];
                $ext = pathinfo($filename, PATHINFO_EXTENSION);

                if(!array_key_exists($ext, $allowed)) {
                    $_SESSION['error'] = "Định dạng file không hỗ trợ.";
                    header("Location: " . $_SERVER['HTTP_REFERER']);
                    exit;
                }

                // Upload
                $uploadDir = 'assets/uploads/materials/';
                if (!file_exists($uploadDir)) mkdir($uploadDir, 0777, true);
                
                $newFilename = uniqid() . '_' . $filename;
                $destination = $uploadDir . $newFilename;

                if(move_uploaded_file($_FILES['material_file']['tmp_name'], $destination)) {
                    $this->materialModel->lessonId = $lessonId;
                    $this->materialModel->filename = $filename;
                    $this->materialModel->filePath = $destination;
                    $this->materialModel->fileType = $ext;
                    
                    if($this->materialModel->insert()) {
                        $_SESSION['success'] = "Upload tài liệu thành công!";
                    } else {
                        $_SESSION['error'] = "Lỗi lưu database.";
                    }
                } else {
                    $_SESSION['error'] = "Lỗi upload file.";
                }
            } else {
                $_SESSION['error'] = "Vui lòng chọn file.";
            }
            header("Location: /onlinecourse/index.php?controller=lesson&action=showMaterials&lesson_id=" . $lessonId);
        }
    }
}