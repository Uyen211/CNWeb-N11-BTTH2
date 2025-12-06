<?php
// AdminController.php

// Import các file cần thiết
require_once 'config/Database.php';
require_once 'models/Category.php';

class AdminController {
    private $categoryModel;
    private $db;

    public function __construct() {
        // Khởi động session nếu chưa có (để dùng Flash Message và Auth)
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        // --- AUTHENTICATION CHECK (Optional) ---
        // Bạn có thể bỏ comment đoạn này để bảo mật
        if (!isset($_SESSION['user']) || $_SESSION['user']['role'] != 1) { 
            header("Location: index.php?controller=auth&action=login");
            exit();
        }

        // --- DEPENDENCY INJECTION ---
        // Khởi tạo kết nối DB và Model theo chuẩn OOP
        $database = new Database();
        $this->db = $database->connect();
        $this->categoryModel = new Category($this->db);
    }

    /**
     * Hiển thị danh sách danh mục (Giao diện chính chứa Modal)
     */
    public function index() {
        // Cấu hình View
        $page_title = "Quản lý Danh mục";
        $css_files = ['admin.css'];

        // --- LOGIC PHÂN TRANG ---
        $limit = 5; // Số dòng trên 1 trang 
        
        // Lấy trang hiện tại từ URL, mặc định là 1
        $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
        if ($page < 1) $page = 1;

        // Tính Offset (Vị trí bắt đầu lấy dữ liệu)
        $offset = ($page - 1) * $limit;

        // Lấy tổng số bản ghi
        $total_records = $this->categoryModel->countAll();

        // Tính tổng số trang (làm tròn lên)
        $total_pages = ceil($total_records / $limit);

        // Lấy dữ liệu theo trang
        $categories = $this->categoryModel->getPaginated($limit, $offset);
        // ------------------------

        // Load Views
        require_once 'views/layouts/header.php';
        require_once 'views/layouts/sidebar.php';
        require_once 'views/admin/categories/list.php';
        require_once 'views/layouts/footer.php';
    }

    /**
     * Xử lý Thêm mới (Logic cho Modal Add)
     */
    public function storeCategory() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Sanitize input
            $name = trim($_POST['name'] ?? '');
            $description = trim($_POST['description'] ?? '');

            // Validation cơ bản
            if (empty($name)) {
                $_SESSION['error'] = "Tên danh mục không được để trống!";
                header('Location: index.php?controller=admin&action=index');
                exit();
            }

            // Gọi Model xử lý
            if ($this->categoryModel->create($name, $description)) {
                $_SESSION['success'] = "Thêm danh mục thành công!";
            } else {
                $_SESSION['error'] = "Lỗi hệ thống: Không thể thêm danh mục.";
            }

            // Redirect về trang index để đóng modal và hiện thông báo
            header('Location: index.php?controller=admin&action=index');
            exit();
        }
    }

    /**
     * Xử lý Cập nhật (Logic cho Modal Edit)
     */
    public function updateCategory() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $id = $_POST['id'] ?? null;
            $name = trim($_POST['name'] ?? '');
            $description = trim($_POST['description'] ?? '');

            if (!$id || empty($name)) {
                $_SESSION['error'] = "Dữ liệu không hợp lệ!";
                header('Location: index.php?controller=admin&action=index');
                exit();
            }

            if ($this->categoryModel->update($id, $name, $description)) {
                $_SESSION['success'] = "Cập nhật danh mục thành công!";
            } else {
                $_SESSION['error'] = "Lỗi cập nhật danh mục.";
            }

            header('Location: index.php?controller=admin&action=index');
            exit();
        }
    }

    /**
     * Xử lý Xóa (Có kiểm tra ràng buộc)
     */
    public function deleteCategory() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $id = $_POST['id'] ?? null;

            if ($id) {
                // LOGIC THÔNG MINH: Kiểm tra ràng buộc trước khi xóa
                // Nếu Model cũ của bạn có hàm hasCourses(), hãy dùng nó
                // Nếu chưa có, bạn nên thêm vào Model để tránh xóa danh mục đang có khóa học
                if (method_exists($this->categoryModel, 'hasCourses') && $this->categoryModel->hasCourses($id)) {
                    $_SESSION['error'] = "Không thể xóa! Danh mục này đang chứa khóa học.";
                } else {
                    if ($this->categoryModel->delete($id)) {
                        $_SESSION['success'] = "Xóa danh mục thành công!";
                    } else {
                        $_SESSION['error'] = "Lỗi khi xóa danh mục.";
                    }
                }
            } else {
                $_SESSION['error'] = "ID danh mục không tồn tại.";
            }

            header('Location: index.php?controller=admin&action=index');
            exit();
        }
    }
}
?>