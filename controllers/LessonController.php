<?php
// Load Models cơ bản
require_once dirname(__DIR__) . '/models/Lesson.php';
require_once dirname(__DIR__) . '/models/Enrollment.php';

// Load thêm Model của Instructor (Kiểm tra file tồn tại để tránh lỗi)
if (file_exists(dirname(__DIR__) . '/models/Material.php')) {
    require_once dirname(__DIR__) . '/models/Material.php';
}
if (file_exists(dirname(__DIR__) . '/models/Course.php')) {
    require_once dirname(__DIR__) . '/models/Course.php';
}

class LessonController {
    private $db;
    private $lessonModel;
    private $enrollmentModel; // Dành cho Student
    private $materialModel;   // Dành cho Instructor
    private $courseModel;     // Dành cho Instructor

    public function __construct($db) {
        $this->db = $db;
        
        // Khởi tạo Model chung
        $this->lessonModel = new Lesson($db);
        
        // Khởi tạo Model cho Student
        $this->enrollmentModel = new Enrollment($db);

        // Khởi tạo Model cho Instructor (Nếu class tồn tại)
        if (class_exists('Material')) {
            $this->materialModel = new Material($db);
        }
        if (class_exists('Course')) {
            $this->courseModel = new Course($db);
        }

        // Đảm bảo session đã start
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
    }

    // =================================================================
    // PHẦN 1: STUDENT VIEW (HỌC VIÊN XEM BÀI)
    // =================================================================

    public function view() {
        // 1. Kiểm tra đăng nhập (Hỗ trợ cả 2 kiểu lưu session)
        $userId = $_SESSION['user']['id'] ?? $_SESSION['user_id'] ?? null;
        
        if (!$userId) {
            header("Location: index.php?controller=auth&action=login");
            exit;
        }

        $course_id = $_GET['course_id'] ?? null;
        $lesson_id = $_GET['id'] ?? null; // ID bài học đang chọn (nếu có)

        // 2. Kiểm tra quyền: Phải đăng ký khóa học rồi mới được xem
        if (!$this->enrollmentModel->isEnrolled($userId, $course_id)) {
            // Có thể redirect về trang chi tiết khóa học để mua
            echo "<script>alert('Bạn chưa đăng ký khóa học này!'); window.location.href='index.php?controller=course&action=detail&id=$course_id';</script>";
            exit;
        }

        // 3. Lấy danh sách tất cả bài học trong khóa (Sidebar bên phải)
        $lessons = $this->lessonModel->getByCourseId($course_id);

        // 4. Xác định bài học hiện tại
        $currentLesson = null;
        if ($lesson_id) {
            $currentLesson = $this->lessonModel->getById($lesson_id);
        } elseif (!empty($lessons)) {
            // Nếu không chọn bài nào, mặc định lấy bài đầu tiên
            $currentLesson = $lessons[0];
        }

        // 5. Lấy tài liệu của bài học hiện tại
        $materials = [];
        if ($currentLesson) {
            // Kiểm tra xem hàm getMaterials nằm ở Model nào
            if (method_exists($this->lessonModel, 'getMaterials')) {
                $materials = $this->lessonModel->getMaterials($currentLesson['id']);
            } elseif ($this->materialModel) {
                $materials = $this->materialModel->getByLessonId($currentLesson['id']);
            }
        }

        // Load View (Dùng đường dẫn tuyệt đối cho an toàn)
        require dirname(__DIR__) . '/views/lessons/view.php';
    }

    // =================================================================
    // PHẦN 2: INSTRUCTOR MANAGE (GIẢNG VIÊN QUẢN LÝ)
    // =================================================================

    // Trang danh sách bài học (Quản lý)
    public function instructor_lesson() {
        $courseId = isset($_GET['course_id']) ? intval($_GET['course_id']) : 0;
        
        // 1. Kiểm tra quyền sở hữu 
        $this->checkAuthorization($courseId);

        // Gọi hàm getById từ Model 
        $course = $this->courseModel->getById($courseId);
        
        if (!$course) {
             $_SESSION['error'] = "Khóa học không tồn tại.";
             header("Location: index.php?controller=course&action=instructor_courses");
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
        require_once dirname(__DIR__) . '/views/layouts/header.php';
        require_once dirname(__DIR__) . '/views/layouts/sidebar.php';
        require_once dirname(__DIR__) . '/views/instructor/lessons/manage.php'; 
        require_once dirname(__DIR__) . '/views/layouts/footer.php';
    }

    // GET LESSON DETAIL (AJAX)
    public function detail() {
        $id = isset($_GET['id']) ? intval($_GET['id']) : 0;
        $lesson = $this->lessonModel->getById($id);
        
        if($lesson) {
            $cId = is_array($lesson) ? $lesson['course_id'] : $this->lessonModel->courseId;
            $this->checkAuthorization($cId);
            
            echo json_encode(['status' => 'success', 'data' => $lesson]);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Not found']);
        }
        exit;
    }

    public function create() {
        $courseId = isset($_GET['course_id']) ? intval($_GET['course_id']) : 0;
        $this->checkAuthorization($courseId);
        $csrf_token = $this->generateCsrfToken();
        
        require_once dirname(__DIR__) . '/views/layouts/header.php';
        require_once dirname(__DIR__) . '/views/layouts/sidebar.php';
        require_once dirname(__DIR__) . '/views/instructor/lessons/create.php';
        require_once dirname(__DIR__) . '/views/layouts/footer.php';
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
            header("Location: index.php?controller=lesson&action=instructor_lesson&course_id=" . $courseId);
        } else {
            $_SESSION['error'] = "Có lỗi xảy ra.";
            header("Location: " . $_SERVER['HTTP_REFERER']);
        }
    }

    public function edit() {
        $id = isset($_GET['id']) ? intval($_GET['id']) : 0;
        $lesson = $this->lessonModel->getById($id);

        if($lesson) {
            $cId = is_array($lesson) ? $lesson['course_id'] : $this->lessonModel->courseId;
            $this->checkAuthorization($cId);
            
            $csrf_token = $this->generateCsrfToken();
            
            require_once dirname(__DIR__) . '/views/layouts/header.php';
            require_once dirname(__DIR__) . '/views/layouts/sidebar.php';
            require_once dirname(__DIR__) . '/views/instructor/lessons/edit.php';
            require_once dirname(__DIR__) . '/views/layouts/footer.php';
        } else {
            $_SESSION['error'] = "Bài học không tồn tại.";
            header("Location: index.php?controller=course&action=instructor_courses");
        }
    }

    public function update() {
        $this->checkCsrf();
        $id = isset($_POST['id']) ? intval($_POST['id']) : 0;
        
        $lesson = $this->lessonModel->getById($id);
        if($lesson) {
            $cId = is_array($lesson) ? $lesson['course_id'] : $this->lessonModel->courseId;
            $this->checkAuthorization($cId);
            
            $this->lessonModel->id = $id;
            $this->lessonModel->title = $_POST['title'];
            $this->lessonModel->content = $_POST['content'];
            $this->lessonModel->videoUrl = $_POST['video_url'];
            $this->lessonModel->order = intval($_POST['order']);

            if($this->lessonModel->update()) {
                $_SESSION['success'] = "Cập nhật thành công!";
                header("Location: index.php?controller=lesson&action=showMaterials&lesson_id=" . $id);
            } else {
                $_SESSION['error'] = "Lỗi cập nhật.";
                header("Location: " . $_SERVER['HTTP_REFERER']);
            }
        }
    }

    public function delete() {
        $id = isset($_POST['id']) ? intval($_POST['id']) : 0;
        $lesson = $this->lessonModel->getById($id);
        
        if($lesson) {
            $cId = is_array($lesson) ? $lesson['course_id'] : $this->lessonModel->courseId;
            $this->checkAuthorization($cId);
            
            $this->lessonModel->id = $id;
            if($this->lessonModel->delete()) {
                 $_SESSION['success'] = "Xóa bài học thành công!";
            } else {
                 $_SESSION['error'] = "Lỗi xóa bài học.";
            }
            header("Location: index.php?controller=lesson&action=instructor_lesson&course_id=" . $cId);
        }
    }

    // --- MATERIALS FUNCTIONS (Quản lý tài liệu) ---

    public function showMaterials() {
        $lessonId = isset($_GET['lesson_id']) ? intval($_GET['lesson_id']) : 0;
        $lesson = $this->lessonModel->getById($lessonId);

        if($lesson) {
            $cId = is_array($lesson) ? $lesson['course_id'] : $this->lessonModel->courseId;
            $this->checkAuthorization($cId);
            
            // Lấy danh sách tài liệu
            $materialsStmt = [];
            if ($this->materialModel) {
                $materialsStmt = $this->materialModel->getByLessonId($lessonId);
            }
            
            $csrf_token = $this->generateCsrfToken();

            require_once dirname(__DIR__) . '/views/layouts/header.php';
            require_once dirname(__DIR__) . '/views/layouts/sidebar.php';
            require_once dirname(__DIR__) . '/views/instructor/materials/upload.php';
            require_once dirname(__DIR__) . '/views/layouts/footer.php';
        } else {
            header("Location: index.php?controller=course&action=instructor_courses");
        }
    }

    public function storeMaterial() {
        $this->checkCsrf();
        $lessonId = isset($_POST['lesson_id']) ? intval($_POST['lesson_id']) : 0;
        $lesson = $this->lessonModel->getById($lessonId);

        if($lesson) {
            $cId = is_array($lesson) ? $lesson['course_id'] : $this->lessonModel->courseId;
            $this->checkAuthorization($cId);

            if(isset($_FILES['material_file']) && $_FILES['material_file']['error'] == 0) {
                $allowed = ['pdf' => 'application/pdf', 'mp4' => 'video/mp4', 'doc' => 'application/msword', 'docx' => 'application/vnd.openxmlformats-officedocument.wordprocessingml.document'];
                $filename = $_FILES['material_file']['name'];
                $ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));

                if(!array_key_exists($ext, $allowed)) {
                    $_SESSION['error'] = "Định dạng file không hỗ trợ.";
                    header("Location: " . $_SERVER['HTTP_REFERER']);
                    exit;
                }

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
            header("Location: index.php?controller=lesson&action=showMaterials&lesson_id=" . $lessonId);
        }
    }

    // =================================================================
    // HELPER METHODS
    // =================================================================

    private function checkAuthorization($courseId) {
        $userId = $_SESSION['user']['id'] ?? $_SESSION['user_id'] ?? null;
        $userRole = $_SESSION['user']['role'] ?? $_SESSION['role'] ?? 0;

        if (!$userId || $userRole != 1) {
            $_SESSION['error'] = "Bạn không có quyền truy cập.";
            header("Location: index.php"); 
            exit;
        }

        if (!$this->courseModel->checkInstructorOwnership($courseId, $userId)) {
            $_SESSION['error'] = "Bạn không sở hữu khóa học này.";
            header("Location: index.php?controller=course&action=instructor_courses");
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
}
?>