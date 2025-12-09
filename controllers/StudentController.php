<?php
// Sử dụng đường dẫn tuyệt đối để tránh lỗi không tìm thấy file
require_once dirname(__DIR__) . '/models/Course.php';
require_once dirname(__DIR__) . '/models/Enrollment.php';
require_once dirname(__DIR__) . '/config/Database.php';

class StudentController {
    private $db;

    public function __construct() {
        // 1. Kết nối Database
        $database = new Database();
        $this->db = $database->pdo; // Lấy biến pdo từ class Database

        // 2. Kiểm tra đăng nhập (BẮT BUỘC)
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        if (!isset($_SESSION['user_id'])) {
            header("Location: index.php?controller=auth&action=login");
            exit();
        }

        // 3. Kiểm tra Role
        // Chỉ cho phép Học viên (0) hoặc Admin (2) vào xem
        // Nếu là Giảng viên (1) thì đá về dashboard giảng viên
        if (isset($_SESSION['role']) && $_SESSION['role'] == 1) {
             header("Location: index.php?controller=instructor&action=dashboard");
             exit();
        }
    }

    // --- ACTION 1: TỔNG QUAN (Dashboard) ---
    public function dashboard() {
        $user_id = $_SESSION['user_id'];

        // Lấy danh sách khóa học để hiển thị tiến độ
        $enrollmentModel = new Enrollment($this->db);
        $myCourses = $enrollmentModel->getCoursesByStudentId($user_id);

        // Gọi View bằng đường dẫn tuyệt đối
        require dirname(__DIR__) . '/views/student/dashboard.php';
    }

    // --- ACTION 2: KHÓA HỌC CỦA TÔI (Khớp với Sidebar) ---
    // Trong sidebar ta để action=my-courses, nhưng trong PHP tên hàm không được có dấu gạch ngang
    // Router của bạn cần map 'my-courses' thành 'my_courses' hoặc bạn sửa link sidebar thành 'action=my_courses'
    public function my_courses() {
        // Về cơ bản logic giống hệt dashboard (hiển thị list khóa học)
        // Hoặc bạn có thể tạo view riêng views/student/my_courses.php nếu muốn giao diện khác
        $this->dashboard(); 
    }

    // --- ACTION 3: LỊCH SỬ HỌC TẬP (Khớp với Sidebar) ---
    public function history() {
        $user_id = $_SESSION['user_id'];
        
        // (Tạm thời hiển thị dashboard, sau này bạn tạo view history.php riêng)
        // require dirname(__DIR__) . '/views/student/history.php';
        echo "Chức năng lịch sử học tập đang phát triển...";
    }

    // --- ACTION 4: ĐĂNG KÝ KHÓA HỌC ---
    public function join() {
        if (isset($_GET['course_id'])) {
            $course_id = $_GET['course_id'];
            $user_id = $_SESSION['user_id'];

            $enrollmentModel = new Enrollment($this->db);
            
            // Kiểm tra đã đăng ký chưa
            if (!$enrollmentModel->isEnrolled($user_id, $course_id)) {
                if ($enrollmentModel->register($user_id, $course_id)) {
                    // Thành công -> Về dashboard
                    header("Location: index.php?controller=student&action=dashboard&msg=success");
                } else {
                    echo "Lỗi hệ thống, vui lòng thử lại.";
                }
            } else {
                // Đã đăng ký rồi -> Về dashboard
                header("Location: index.php?controller=student&action=dashboard&msg=already_joined");
            }
        } else {
            // Không có ID khóa học
            header("Location: index.php?controller=course&action=index");
        }
    }
}
?>