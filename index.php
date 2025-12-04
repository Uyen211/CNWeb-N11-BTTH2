<?php
session_start(); // Khởi tạo session ngay từ đầu

// Import file cấu hình Database
require_once './config/Database.php';

$url = isset($_GET['url']) ? $_GET['url'] : null;

if ($url != null) {
    $url = rtrim($url, '/'); // Cắt dấu / thừa ở cuối
    $url = explode('/', $url); // Tách chuỗi thành mảng
} else {
    // Nếu không có url thì mặc định vào trang chủ
    $url = ['home', 'index'];
}

// 1. Xác định Controller (Phần tử đầu tiên của mảng)
// Quy ước: Tên controller viết hoa chữ cái đầu + "Controller"
// Ví dụ: courses -> CourseController
$controllerName = isset($url[0]) ? ucfirst($url[0]) . 'Controller' : 'HomeController';

// Đường dẫn file controller
$controllerPath = "./controllers/" . $controllerName . ".php";

// Kiểm tra xem file controller có tồn tại không
if (file_exists($controllerPath)) {
    require_once $controllerPath;
    
    // Khởi tạo Controller
    $controller = new $controllerName();

    // 2. Xác định Action (Hàm trong controller - Phần tử thứ 2)
    $action = isset($url[1]) ? $url[1] : 'index';

    // 3. Xác định tham số (Phần tử thứ 3 trở đi)
    $params = array_values(array_slice($url, 2));

    // Kiểm tra xem method có tồn tại trong Controller không
    if (method_exists($controller, $action)) {
        // Gọi hàm và truyền tham số
        call_user_func_array([$controller, $action], $params);
    } else {
        echo "Lỗi 404: Không tìm thấy Action '{$action}' trong '{$controllerName}'";
    }
} else {
    echo "Lỗi 404: Không tìm thấy Controller '{$controllerName}'";
}
?>