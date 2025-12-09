<?php
class Enrollment {
    private $conn;
    private $table = 'enrollments';

    // Các thuộc tính (tùy chọn, dùng khi cần xử lý object cụ thể)
    public $id;
    public $course_id;
    public $student_id;
    public $enrolled_date;
    public $status;
    public $progress;

    // Constructor nhận kết nối DB
    public function __construct($db) {
        $this->conn = $db;
    }

    // 1. Đăng ký khóa học mới
    public function register($student_id, $course_id) {
        // Câu lệnh SQL insert
        $query = "INSERT INTO " . $this->table . " 
                  (student_id, course_id, enrolled_date, status, progress) 
                  VALUES (:student_id, :course_id, NOW(), 'active', 0)";

        $stmt = $this->conn->prepare($query);

        // Làm sạch dữ liệu (Security)
        $student_id = htmlspecialchars(strip_tags($student_id));
        $course_id = htmlspecialchars(strip_tags($course_id));

        // Gán dữ liệu vào tham số
        $stmt->bindParam(':student_id', $student_id);
        $stmt->bindParam(':course_id', $course_id);

        // Thực thi
        if ($stmt->execute()) {
            return true;
        }
        return false;
    }

    // 2. Kiểm tra xem sinh viên đã đăng ký khóa này chưa
    // (Tránh việc một người đăng ký 2 lần 1 khóa)
    public function isEnrolled($student_id, $course_id) {
        $query = "SELECT id FROM " . $this->table . " 
                  WHERE student_id = :student_id AND course_id = :course_id 
                  LIMIT 1";

        $stmt = $this->conn->prepare($query);

        $stmt->bindParam(':student_id', $student_id);
        $stmt->bindParam(':course_id', $course_id);

        $stmt->execute();

        // Nếu số dòng tìm thấy > 0 nghĩa là đã đăng ký rồi
        if ($stmt->rowCount() > 0) {
            return true;
        }
        return false;
    }

    // 3. Lấy danh sách khóa học của một sinh viên (Dùng cho Dashboard Học Viên)
    // Cần JOIN với bảng 'courses' để lấy tên và ảnh khóa học
    public function getCoursesByStudentId($student_id) {
        $query = "SELECT 
                    c.id as course_id,
                    c.title,
                    c.image,
                    c.instructor_id,
                    e.enrolled_date,
                    e.status,
                    e.progress
                  FROM " . $this->table . " e
                  JOIN courses c ON e.course_id = c.id
                  WHERE e.student_id = :student_id
                  ORDER BY e.enrolled_date DESC";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':student_id', $student_id);
        $stmt->execute();

        // Trả về mảng danh sách các khóa học
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // 4. Lấy danh sách học viên của một khóa học (Dùng cho Giảng viên xem ai đang học khóa mình)
    // Cần JOIN với bảng 'users' để lấy tên sinh viên
    public function getStudentsByCourseId($course_id) {
        $query = "SELECT 
                    u.fullname,
                    u.email,
                    e.enrolled_date,
                    e.progress
                  FROM " . $this->table . " e
                  JOIN users u ON e.student_id = u.id
                  WHERE e.course_id = :course_id";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':course_id', $course_id);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // 5. Cập nhật tiến độ học tập (Ví dụ: Khi học xong 1 bài)
    public function updateProgress($student_id, $course_id, $progress) {
        $query = "UPDATE " . $this->table . " 
                  SET progress = :progress 
                  WHERE student_id = :student_id AND course_id = :course_id";

        $stmt = $this->conn->prepare($query);

        $stmt->bindParam(':progress', $progress);
        $stmt->bindParam(':student_id', $student_id);
        $stmt->bindParam(':course_id', $course_id);

        if ($stmt->execute()) {
            return true;
        }
        return false;
    }
}
?>