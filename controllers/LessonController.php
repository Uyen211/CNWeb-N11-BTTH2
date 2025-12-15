<?php
require_once dirname(__DIR__) . '/models/Lesson.php';
require_once dirname(__DIR__) . '/models/Enrollment.php';

class LessonController {
    private $db;
    private $lessonModel;
    private $enrollmentModel;

    public function __construct($db) {
        $this->db = $db;
        $this->lessonModel = new Lesson($db);
        $this->enrollmentModel = new Enrollment($db);
    }

    public function view() {
        // Kiểm tra đăng nhập
        if (!isset($_SESSION['user_id'])) {
            header("Location: index.php?controller=auth&action=login");
            exit;
        }

        $course_id = $_GET['course_id'] ?? null;
        $lesson_id = $_GET['id'] ?? null; // ID bài học đang chọn (nếu có)

        // Kiểm tra quyền: Phải đăng ký khóa học rồi mới được xem
        if (!$this->enrollmentModel->isEnrolled($_SESSION['user_id'], $course_id)) {
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
            $materials = $this->lessonModel->getMaterials($currentLesson['id']);
        }

        require dirname(__DIR__) . '/views/lessons/view.php';
    }
}
?>