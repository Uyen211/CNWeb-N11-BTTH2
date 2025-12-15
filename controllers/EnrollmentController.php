<?php
// controllers/StudentController.php
require_once 'config/Database.php';
require_once 'models/Course.php';
require_once 'models/Enrollment.php';

class EnrollmentController {
    private $db;
    private $courseModel;
    private $enrollmentModel;

    public function __construct() {
        $database = new Database();
        $this->db = $database->connect();
        $this->courseModel = new Course($this->db);
        $this->enrollmentModel = new Enrollment($this->db);
    }

    // Action: index (Hiển thị danh sách học viên trong Hub)
    public function instructor_enrollment() {
        $this->requireLogin();

        $courseId = isset($_GET['course_id']) ? intval($_GET['course_id']) : 0;

        // 1. Kiểm tra quyền sở hữu (Bảo mật: Chỉ giảng viên của khóa học mới được xem)
        $course = $this->courseModel->getById($courseId);
        
        if (!$course || $course['instructor_id'] != $_SESSION['user']['id']) {
            $_SESSION['error'] = "Bạn không có quyền truy cập khóa học này.";
            header("Location: /onlinecourse/index.php?controller=course&action=index");
            exit;
        }

        // 2. Phân trang
        $limit = 10;
        $page = isset($_GET['page']) ? intval($_GET['page']) : 1;
        $offset = ($page - 1) * $limit;

        // 3. Lấy dữ liệu
        $students = $this->enrollmentModel->getStudentsByCourse($courseId, $limit, $offset);
        $totalStudents = $this->enrollmentModel->countByCourse($courseId);
        $totalPages = ceil($totalStudents / $limit);

        // 4. Load View
        // Sử dụng chung css course_hub để đồng bộ giao diện
        $css_files = ['assets/css/course_hub.css']; 
        
        require_once 'views/layouts/header.php';
        require_once 'views/layouts/sidebar.php';
        require_once 'views/instructor/students/list.php';
        require_once 'views/layouts/footer.php';
    }

    private function requireLogin() {
        if (!isset($_SESSION['user']) || $_SESSION['user']['role'] != 1) {
            header("Location: /onlinecourse/index.php?controller=auth&action=login");
            exit;
        }
    }
}
?>