<?php
// Load các Model cần thiết cho cả Student và Instructor
require_once dirname(__DIR__) . '/models/Lesson.php';
require_once dirname(__DIR__) . '/models/Enrollment.php';

// Kiểm tra và load thêm Model của Instructor nếu chưa có
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

        // Khởi tạo Model cho Instructor (Kiểm tra class tồn tại để tránh lỗi nếu chưa merge file Model)
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
    // PHẦN 1: STUDENT VIEW (Code của BẠN)
    // =================================================================

    public function view() {
        // Kiểm tra đăng nhập
        if (!isset($_SESSION['user']) && !isset($_SESSION['user_id'])) {
            header("Location: index.php?controller=auth&action=login");
            exit;
        }
        
        // Lấy ID user (tương thích cả 2 code session)
        $userId = $_SESSION['user']['id'] ?? $_SESSION['user_id'];

        $course_id = $_GET['course_id'] ?? null;
        $lesson_id = $_GET['id'] ?? null; // ID bài học đang chọn (nếu có)

        // Kiểm tra quyền: Phải đăng ký khóa học rồi mới được xem
        if (!$this->enrollmentModel->isEnrolled($userId, $course_id)) {
            die("Bạn chưa đăng ký khóa học này!");
        }

        // 1. Lấy danh sách tất cả bài học trong khóa (để làm sidebar bên phải)
        $lessons = $this->lessonModel->getByCourseId($course_id);

        // 2. Xác định bài học hiện tại
        if ($lesson_id) {
            $currentLesson = $this->lessonModel->getById($lesson_id);
        } else {
            // Nếu không chọn bài nào, mặc định lấy bài đầu tiên
            $currentLesson = $lessons[0] ?? null;
        }

        // 3. Lấy tài liệu của bài học hiện tại
        $materials = [];
        if ($currentLesson) {
            // Kiểm tra hàm getMaterials tồn tại ở Model nào để gọi cho đúng
            if (method_exists($this->lessonModel, 'getMaterials')) {
                $materials = $this->lessonModel->getMaterials($currentLesson['id']);
            } elseif ($this->materialModel) {
                $materials = $this->materialModel->getByLessonId($currentLesson['id']);
            }
        }

        require dirname(__DIR__) . '/views/lessons/view.php';
    }

    // =================================================================
    // PHẦN 2: INSTRUCTOR MANAGE (Code của BẠN BÈ)
    // =================================================================

    // Trang danh sách bài học (Quản lý)
    public function index() {
        $courseId = isset($_GET['course_id']) ? intval($_GET['course_id']) : 0;
        
        // 1. Kiểm tra quyền sở hữu 
        $this->checkAuthorization($courseId);

        // Gọi hàm getById từ Model 
        $course = $this->courseModel->getById($courseId);
        
        // Kiểm tra nếu không tìm thấy khóa học
        if (!$course) {
             $_SESSION['error'] = "Khóa học không tồn tại.";
             header("Location: index.php?controller=course&action=instructor_courses");
             exit;
        }

        // 2. Logic phân trang bài học 
        $limit = 10;
        $page = isset($_GET['page']) ? intval($_GET['page']) : 1;
        $offset = ($page - 1) * $limit;

        // Lưu ý: Đảm bảo Model Lesson có hàm getPaginated
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

    // GET LESSON DETAIL (AJAX - Dùng cho popup sửa nhanh nếu có)
    public function detail() {
        $id = isset($_GET['id']) ? intval($_GET['id']) : 0;
        $lesson = $this->lessonModel->getById($id);
        
        if($lesson) {
            // Lấy courseId từ kết quả trả về (vì model có thể trả về array hoặc object tùy implementation)
            $cId = is_array($lesson) ? $lesson['course_id'] : $this->lessonModel->courseId;
            $this->checkAuthorization($cId);
            
            echo json_encode([
                'status' => 'success',
                'data' => $lesson
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

        // Gán dữ liệu vào Model
        $this->lessonModel->courseId = $courseId;
        $this->lessonModel->title = $_POST['title'];
        $this->lessonModel->content = $_POST['content'];
        $this->lessonModel->videoUrl = $_POST['video_url'];
        $this->lessonModel->order = intval($_POST['order']);

        if($this->lessonModel->insert()) {
            $_SESSION['success'] = "Thêm bài học thành công!";
            header("Location: index.php?controller=lesson&action=index&course_id=" . $courseId);
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
            
            // Nếu getById trả về mảng, cần gán vào property object để view dùng (tùy view viết ntn)
            // Ở đây giả định view dùng $this->lessonModel hoặc biến $lesson truyền vào
            
            $csrf_token = $this->generateCsrfToken();
            $css_files = ['lesson.css'];
            
            require_once 'views/layouts/header.php';
            require_once 'views/layouts/sidebar.php';
            require_once 'views/instructor/lessons/edit.php';
            require_once 'views/layouts/footer.php';
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
                 // Redirect về trang upload materials hoặc danh sách bài học
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
            header("Location: index.php?controller=lesson&action=index&course_id=" . $cId);
        }
    }

    // --- MATERIALS FUNCTIONS (Quản lý tài liệu) ---

    public function showMaterials() {
        $lessonId = isset($_GET['lesson_id']) ? intval($_GET['lesson_id']) : 0;
        $lesson = $this->lessonModel->getById($lessonId);

        if($lesson) {
            $cId = is_array($lesson) ? $lesson['course_id'] : $this->lessonModel->courseId;
            $this->checkAuthorization($cId);
            
            $materialsStmt = $this->materialModel->getByLessonId($lessonId);
            $csrf_token = $this->generateCsrfToken();

            $css_files = ['lesson.css'];
            require_once 'views/layouts/header.php';
            require_once 'views/layouts/sidebar.php';
            require_once 'views/instructor/materials/upload.php';
            require_once 'views/layouts/footer.php';
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
                // $filetype = $_FILES['material_file']['type']; // Ít dùng
                // $filesize = $_FILES['material_file']['size']; // Có thể check size ở đây
                $ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));

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
            header("Location: index.php?controller=lesson&action=showMaterials&lesson_id=" . $lessonId);
        }
    }

    // =================================================================
    // HELPER METHODS
    // =================================================================

    // Helper: Kiểm tra quyền instructor và sở hữu khóa học
    private function checkAuthorization($courseId) {
        // Kiểm tra đăng nhập
        if (!isset($_SESSION['user']) && !isset($_SESSION['user_id'])) {
            header("Location: index.php?controller=auth&action=login");
            exit;
        }
        
        $userRole = $_SESSION['user']['role'] ?? 0;
        $userId = $_SESSION['user']['id'] ?? $_SESSION['user_id'];

        // Kiểm tra Role = 1 (Giảng viên)
        if ($userRole != 1) {
            $_SESSION['error'] = "Bạn không có quyền thực hiện hành động này.";
            header("Location: index.php"); 
            exit;
        }

        if (!$this->courseModel->checkInstructorOwnership($courseId, $userId)) {
            $_SESSION['error'] = "Bạn không có quyền truy cập khóa học này.";
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