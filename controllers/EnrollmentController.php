<?php
require_once 'models/Enrollment.php';
require_once 'models/Course.php';

class EnrollmentController {
    private $conn;
    private $enrollmentModel;
    private $courseModel;

    public function __construct($db) {
        $this->conn = $db;
        $this->enrollmentModel = new Enrollment($db);
        $this->courseModel = new Course($db);
        if (session_status() === PHP_SESSION_NONE) session_start();
    }

    public function enroll() {
        // 1. Kiểm tra đăng nhập
        if (!isset($_SESSION['user_id'])) {
            header("Location: index.php?controller=auth&action=login&message=must_login");
            exit();
        }

        $student_id = $_SESSION['user_id'];
        $course_id = isset($_GET['course_id']) ? $_GET['course_id'] : null;

        if ($course_id) {
            // 2. Kiểm tra đã đăng ký chưa
            if ($this->enrollmentModel->isEnrolled($student_id, $course_id)) {
                // Nếu đã có rồi -> Chuyển về Dashboard kèm thông báo đã tham gia
                header("Location: index.php?controller=student&action=dashboard&msg=already_joined");
                exit;
            } 
            
            // 3. ĐĂNG KÝ THẲNG (Bỏ qua đoạn check giá tiền > 0)
            if ($this->enrollmentModel->registerCourse($student_id, $course_id)) {
                // Thành công -> Chuyển về Dashboard kèm thông báo thành công
                // Lưu ý: action=dashboard
                header("Location: index.php?controller=student&action=dashboard&msg=registered_success");
                exit; // Nên thêm exit sau header để dừng script
            } else {
                echo "Lỗi hệ thống: Không thể ghi dữ liệu.";
            }

        } else {
            header("Location: index.php");
        }
    }
    
    // Không cần hàm checkout() hay complete_order() nữa ở giai đoạn này
}
?>