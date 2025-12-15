<?php
// controllers/InstructorController.php (Hoặc DashboardController)
require_once 'config/Database.php';
require_once 'models/Course.php';
require_once 'models/Enrollment.php';

class InstructorController { 
    private $db;
    private $courseModel;
    private $enrollmentModel;

    public function __construct() {
        $database = new Database();
        $this->db = $database->connect();
        $this->courseModel = new Course($this->db);
        $this->enrollmentModel = new Enrollment($this->db);
    }

    public function dashboard() {
        $this->requireInstructor();
        $instructorId = $_SESSION['user']['id'];

        // 1. Thống kê tổng quan (Cards)
        $totalCourses = $this->courseModel->countAll($instructorId); // Hàm này đã có ở bài trước
        $totalStudents = $this->enrollmentModel->countTotalEnrollments($instructorId);
        $totalRevenue = $this->courseModel->getTotalRevenue($instructorId);

        // 2. Danh sách khóa học tiêu biểu (Top Courses)
        $topCourses = $this->courseModel->getTopCourses($instructorId);

        // 3. Hoạt động gần đây (New Enrollments)
        $recentActivities = $this->enrollmentModel->getRecentActivity($instructorId);

        // Load View
        $css_files = ['course_hub.css']; // Tận dụng lại CSS của Hub vì style giống nhau
        
        require_once 'views/layouts/header.php';
        require_once 'views/layouts/sidebar.php';
        require_once 'views/instructor/dashboard.php';
        require_once 'views/layouts/footer.php';
    }

    private function requireInstructor() {
        if (!isset($_SESSION['user']) || $_SESSION['user']['role'] != 1) {
            header("Location: /onlinecourse/index.php?controller=auth&action=login");
            exit;
        }
    }
}
?>