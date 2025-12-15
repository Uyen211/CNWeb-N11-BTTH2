<?php
session_start(); // Khởi tạo session ngay từ đầu

// 1. Import file cấu hình Database & User Model
require_once './config/Database.php';
require_once './models/User.php'; // Load User model để dùng cho AuthController

// --- KHỞI TẠO KẾT NỐI DATABASE (QUAN TRỌNG) ---
$database = new Database();
$db = $database->getConnection(); // Lấy biến kết nối PDO ($db)

// -----------------------------------------------------------
// ⚠️ XÓA ĐOẠN CODE SAU ĐỂ CHỨC NĂNG LOGIN HOẠT ĐỘNG THẬT:
// $_SESSION['user'] = [ 'id' => 2, 'role' => 1 ]; 
// -----------------------------------------------------------


// --- PHẦN XỬ LÝ ROUTING (ĐỊNH TUYẾN) ---

$controllerName = 'HomeController'; // Mặc định
$action = 'index';                  // Mặc định
$params = [];

// TRƯỜNG HỢP 1: Dùng URL thân thiện
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
// TRƯỜNG HỢP 2: Dùng Query String
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
        
        // --- SỬA LỖI TẠI ĐÂY: TRUYỀN $db VÀO CONTROLLER ---
        $controller = new $controllerName($db); 
        // -------------------------------------------------

        // Kiểm tra method có tồn tại trong Controller không
        if (method_exists($controller, $action)) {
            // Gọi hành động
            call_user_func_array([$controller, $action], $params);
        } else {
            die("Lỗi 404: Không tìm thấy Action '{$action}' trong Controller '{$controllerName}'");
        }
    } else {
        die("Lỗi 500: Class '{$controllerName}' không tìm thấy trong file.");
    }
} else {
    // Xử lý lỗi Controller không tồn tại
    // Chuyển về trang chủ hoặc báo lỗi 404 đẹp hơn
    die("Lỗi 404: Không tìm thấy Controller '{$controllerName}'");
}
?>