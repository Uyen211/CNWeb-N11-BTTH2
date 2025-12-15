<?php
session_start(); // Khởi tạo session ngay từ đầu

// 1. Import file cấu hình Database & User Model
require_once './config/Database.php';
// Kiểm tra file User tồn tại trước khi require (tránh lỗi)
if (file_exists('./models/User.php')) {
    require_once './models/User.php';
}

// --- KHỞI TẠO KẾT NỐI DATABASE (QUAN TRỌNG) ---
$database = new Database();
$db = $database->getConnection(); // Lấy biến kết nối PDO ($db)

// -----------------------------------------------------------
// ⚠️ CHÚ Ý: ĐOẠN CODE TEST NHANH (FAKE LOGIN)
// Đã comment lại để chức năng Đăng nhập thật hoạt động.
// Nếu muốn test nhanh quyền Giảng viên mà không cần login, hãy bỏ comment dòng dưới.
// -----------------------------------------------------------
/*
$_SESSION['user'] = [
    'id' => 2,
    'role' => 1,
    'fullname' => 'Nguyễn Văn A (Test Mode)'
];
*/
// -----------------------------------------------------------

// --- PHẦN XỬ LÝ ROUTING (ĐỊNH TUYẾN) ---

$controllerName = 'HomeController'; // Mặc định
$action = 'index';                  // Mặc định
$params = [];

// TRƯỜNG HỢP 1: Dùng URL thân thiện (ví dụ: /course/detail/1)
if (isset($_GET['url'])) {
    $url = rtrim($_GET['url'], '/');
    $url = explode('/', $url);

    if (isset($url[0]) && $url[0] != "") {
        $controllerName = ucfirst($url[0]) . 'Controller';
    }
    if (isset($url[1]) && $url[1] != "") {
        $action = $url[1];
    }
    if (count($url) > 2) {
        $params = array_values(array_slice($url, 2));
    }
} 
// TRƯỜNG HỢP 2: Dùng Query String (ví dụ: ?controller=course&action=detail&id=1)
elseif (isset($_GET['controller'])) {
    $controllerName = ucfirst($_GET['controller']) . 'Controller';
    if (isset($_GET['action'])) {
        $action = $_GET['action'];
    }
}

// --- PHẦN GỌI CONTROLLER ---

$controllerPath = "./controllers/" . $controllerName . ".php";

// Kiểm tra file controller có tồn tại không
if (file_exists($controllerPath)) {
    require_once $controllerPath;
    
    // Kiểm tra class có tồn tại không
    if (class_exists($controllerName)) {
        
        // --- KHỞI TẠO CONTROLLER VÀ TRUYỀN KẾT NỐI DB ---
        // Đây là chuẩn Dependency Injection mà bạn đang dùng
        $controller = new $controllerName($db); 
        // -------------------------------------------------

        // Kiểm tra method có tồn tại trong Controller không
        if (method_exists($controller, $action)) {
            // Gọi hành động
            call_user_func_array([$controller, $action], $params);
        } else {
            // Xử lý lỗi Action không tồn tại
            echo "Lỗi 404: Không tìm thấy Action '{$action}' trong Controller '{$controllerName}'";
        }
    } else {
        echo "Lỗi 500: Class '{$controllerName}' không tìm thấy trong file.";
    }
} else {
    // Xử lý lỗi Controller không tồn tại -> Về trang chủ hoặc báo lỗi
    // Nếu không tìm thấy controller (ví dụ gõ linh tinh), mặc định về Home
    if ($controllerName != 'HomeController') {
         echo "Lỗi 404: Không tìm thấy trang yêu cầu.";
    } else {
         // Nếu file HomeController không có thì chịu thua
         die("Lỗi Critical: Không tìm thấy HomeController. Hãy kiểm tra thư mục controllers.");
    }
}
?>