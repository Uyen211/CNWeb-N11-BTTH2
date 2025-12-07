<?php
session_start(); // Khởi tạo session ngay từ đầu

// Import file cấu hình Database
require_once './config/Database.php';

$_SESSION['user'] = [
    'id' => 2,
    'role' => 1
]; // Giả lập user đã đăng nhập với role Instructor

// --- PHẦN XỬ LÝ ROUTING (ĐỊNH TUYẾN) ---

$controllerName = 'HomeController'; // Mặc định
$action = 'index';                  // Mặc định
$params = [];

// TRƯỜNG HỢP 1: Dùng URL thân thiện (VD: /admin/manageCategories)
if (isset($_GET['url'])) {
    $url = rtrim($_GET['url'], '/');
    $url = explode('/', $url);

    // Lấy Controller
    if (isset($url[0]) && $url[0] != "") {
        $controllerName = ucfirst($url[0]) . 'Controller';
    }

    // Lấy Action
    if (isset($url[1]) && $url[1] != "") {
        $action = $url[1];
    }

    // Lấy tham số
    if (count($url) > 2) {
        $params = array_values(array_slice($url, 2));
    }
} 
// TRƯỜNG HỢP 2: Dùng Query String (VD: index.php?controller=admin&action=manageCategories)
elseif (isset($_GET['controller'])) {
    $controllerName = ucfirst($_GET['controller']) . 'Controller';
    
    if (isset($_GET['action'])) {
        $action = $_GET['action'];
    }
    
    // Nếu có id truyền vào URL thì đưa vào params (VD: &id=1)
    // Lưu ý: Các hàm trong controller cần viết kiểu function deleteCategory() { $id = $_GET['id']; ... } 
    // thay vì nhận tham số truyền vào nếu dùng cách này.
    // code controller Admin dùng $_GET['id'] bên trong hàm nên không ảnh hưởng.
}

// --- PHẦN GỌI CONTROLLER ---

$controllerPath = "./controllers/" . $controllerName . ".php";

// Kiểm tra file controller có tồn tại không
if (file_exists($controllerPath)) {
    require_once $controllerPath;
    
    // Kiểm tra class có tồn tại không
    if (class_exists($controllerName)) {
        $controller = new $controllerName();

        // Kiểm tra method có tồn tại trong Controller không
        if (method_exists($controller, $action)) {
            // Gọi hành động
            // Lưu ý: Nếu dùng params từ URL rewrite thì truyền vào, nếu không thì gọi hàm không tham số
            call_user_func_array([$controller, $action], $params);
        } else {
            // Xử lý lỗi Action không tồn tại
            die("Lỗi 404: Không tìm thấy Action '{$action}' trong Controller '{$controllerName}'");
        }
    } else {
        die("Lỗi 500: Class '{$controllerName}' không tìm thấy trong file.");
    }
} else {
    // Xử lý lỗi Controller không tồn tại
    die("Lỗi 404: Không tìm thấy Controller '{$controllerName}' (Path: {$controllerPath})");
}
?>