<?php
// Sử dụng đường dẫn tuyệt đối chuẩn
require_once dirname(__DIR__) . '/config/Database.php';
require_once dirname(__DIR__) . '/models/Enrollment.php';
// require_once dirname(__DIR__) . '/models/Course.php'; // Nếu cần dùng Model Course thì mở dòng này

class StudentController {
    private $db;
    private $enrollmentModel; // 1. Khai báo thuộc tính này để dùng toàn cục trong class

    // 2. Nhận biến $db được truyền từ index.php vào
    public function __construct($db) {
        $this->db = $db;
        
        // 3. Khởi tạo Model ngay khi Controller chạy
        $this->enrollmentModel = new Enrollment($this->db);

        // Kiểm tra Session
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        if (!isset($_SESSION['user_id'])) {
            header("Location: index.php?controller=auth&action=login");
            exit();
        }

        // Kiểm tra Role (Chặn giảng viên vào khu vực học viên)
        if (isset($_SESSION['role']) && $_SESSION['role'] == 1) {
             header("Location: index.php?controller=instructor&action=dashboard");
             exit();
        }
    }

    // --- ACTION 1: TỔNG QUAN (Dashboard) ---
    public function dashboard() {
        $user_id = $_SESSION['user_id'];

        // 4. Gọi đúng tên hàm getMyCourses (đã thống nhất ở Model)
        // Dùng $this->enrollmentModel đã khởi tạo ở trên
        $myCourses = $this->enrollmentModel->getMyCourses($user_id);

        require dirname(__DIR__) . '/views/student/dashboard.php';
    }

    // --- ACTION 2: KHÓA HỌC CỦA TÔI ---
    public function my_courses() {
        $student_id = $_SESSION['user_id'];
        
        // Gọi Model
        $myCourses = $this->enrollmentModel->getMyCourses($student_id);
        
        // Gọi View
        require dirname(__DIR__) . '/views/student/my_courses.php';
    }

    // --- ACTION 3: LỊCH SỬ HỌC TẬP ---
    public function history() {
        echo "Chức năng lịch sử học tập đang phát triển...";
    }

    // --- ACTION 4: ĐĂNG KÝ KHÓA HỌC (Nếu bạn muốn xử lý ở đây thay vì EnrollmentController) ---
    public function join() {
        if (isset($_GET['course_id'])) {
            $course_id = $_GET['course_id'];
            $user_id = $_SESSION['user_id'];

            // Kiểm tra đã đăng ký chưa
            if (!$this->enrollmentModel->isEnrolled($user_id, $course_id)) {
                // 5. Gọi đúng tên hàm registerCourse (đã thống nhất ở Model)
                if ($this->enrollmentModel->registerCourse($user_id, $course_id)) {
                    header("Location: index.php?controller=student&action=dashboard&msg=success");
                } else {
                    echo "Lỗi hệ thống, vui lòng thử lại.";
                }
            } else {
                header("Location: index.php?controller=student&action=dashboard&msg=already_joined");
            }
        } else {
            header("Location: index.php?controller=course&action=index");
        }
    }
}
?>