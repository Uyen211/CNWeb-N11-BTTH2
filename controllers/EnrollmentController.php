<?php
// Sử dụng đường dẫn tuyệt đối để tránh lỗi file not found
require_once dirname(__DIR__) . '/models/Enrollment.php';
require_once dirname(__DIR__) . '/models/Course.php';

class EnrollmentController {
    private $db;
    private $enrollmentModel;
    private $courseModel;

    // Sử dụng Constructor nhận $db từ index.php (Chuẩn MVC của bạn)
    public function __construct($db) {
        $this->db = $db;
        $this->enrollmentModel = new Enrollment($db);
        $this->courseModel = new Course($db);
        if (session_status() === PHP_SESSION_NONE) session_start();
    }

    // =================================================================
    // PHẦN 1: STUDENT - ĐĂNG KÝ HỌC (Code của BẠN)
    // =================================================================
    public function enroll() {
        // 1. Kiểm tra đăng nhập
        if (!isset($_SESSION['user_id']) && !isset($_SESSION['user'])) {
            header("Location: index.php?controller=auth&action=login&message=must_login");
            exit();
        }

        // Đồng bộ user_id dù dùng key nào
        $student_id = $_SESSION['user_id'] ?? $_SESSION['user']['id'];
        $course_id = isset($_GET['course_id']) ? $_GET['course_id'] : null;

        if ($course_id) {
            // 2. Kiểm tra đã đăng ký chưa
            if ($this->enrollmentModel->isEnrolled($student_id, $course_id)) {
                // Nếu đã có rồi -> Chuyển về Dashboard kèm thông báo
                header("Location: index.php?controller=student&action=dashboard&msg=already_joined");
                exit;
            } 
            
            // 3. ĐĂNG KÝ NGAY
            if ($this->enrollmentModel->registerCourse($student_id, $course_id)) {
                header("Location: index.php?controller=student&action=dashboard&msg=registered_success");
                exit;
            } else {
                echo "Lỗi hệ thống: Không thể ghi dữ liệu.";
            }

        } else {
            header("Location: index.php");
        }
    }

    // =================================================================
    // PHẦN 2: INSTRUCTOR - QUẢN LÝ HỌC VIÊN (Code của NHÓM)
    // =================================================================
    
    // Action: Xem danh sách học viên trong 1 khóa học
    public function instructor_enrollment() {
        $this->requireInstructorLogin();

        $courseId = isset($_GET['course_id']) ? intval($_GET['course_id']) : 0;

        // 1. Kiểm tra quyền sở hữu (Bảo mật)
        $course = $this->courseModel->getById($courseId);
        $currentUserId = $_SESSION['user']['id'] ?? $_SESSION['user_id'];
        
        if (!$course || $course['instructor_id'] != $currentUserId) {
            $_SESSION['error'] = "Bạn không có quyền truy cập khóa học này.";
            header("Location: index.php?controller=course&action=index");
            exit;
        }

        // 2. Phân trang
        $limit = 10;
        $page = isset($_GET['page']) ? intval($_GET['page']) : 1;
        $offset = ($page - 1) * $limit;

        // 3. Lấy dữ liệu (Đảm bảo Model Enrollment có các hàm này)
        $students = [];
        $totalStudents = 0;
        $totalPages = 0;

        if (method_exists($this->enrollmentModel, 'getStudentsByCourse')) {
            $students = $this->enrollmentModel->getStudentsByCourse($courseId, $limit, $offset);
            $totalStudents = $this->enrollmentModel->countByCourse($courseId);
            $totalPages = ceil($totalStudents / $limit);
        }

        // 4. Load View
        // Sử dụng dirname để path chính xác
        require_once dirname(__DIR__) . '/views/layouts/header.php';
        require_once dirname(__DIR__) . '/views/layouts/sidebar.php';
        // Kiểm tra xem file view có tồn tại không trước khi gọi
        if (file_exists(dirname(__DIR__) . '/views/instructor/students/list.php')) {
            require_once dirname(__DIR__) . '/views/instructor/students/list.php';
        } else {
            echo "Đang cập nhật giao diện danh sách học viên...";
        }
        require_once dirname(__DIR__) . '/views/layouts/footer.php';
    }

    // Helper kiểm tra quyền Giảng viên
    private function requireInstructorLogin() {
        // Kiểm tra user có tồn tại và role = 1 (Giảng viên) hay không
        $role = $_SESSION['user']['role'] ?? $_SESSION['role'] ?? -1;
        if ($role != 1) {
            header("Location: index.php?controller=auth&action=login");
            exit;
        }
    }
}
?>