<?php
require_once dirname(__DIR__) . '/config/Database.php';
require_once dirname(__DIR__) . '/models/User.php';

class AuthController {

    public function register() {
        $error = '';
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $database = new Database();
            $db = $database->pdo; 
            $userModel = new User($db);

            $fullname = $_POST['fullname'] ?? '';
            $username = $_POST['username'] ?? '';
            $email = $_POST['email'] ?? '';
            $password = $_POST['password'] ?? '';
            $confirm_password = $_POST['confirm_password'] ?? '';
            $role = 0; // Mặc định là học viên

            if ($password !== $confirm_password) {
                $error = "Mật khẩu nhập lại không khớp!";
            } else {
                if ($userModel->register($username, $email, $password, $fullname, $role)) {
                    header('Location: index.php?controller=auth&action=login&success=1');
                    exit; 
                } else {
                    $error = "Tên đăng nhập hoặc Email đã tồn tại!";
                }
            }
        }
        require dirname(__DIR__) . '/views/auth/register.php';
    }

    public function login() {
        $error = '';
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $database = new Database();
            $db = $database->pdo;
            $userModel = new User($db);

            $username = $_POST['username'] ?? ''; 
            $password = $_POST['password'] ?? '';

            $user = $userModel->getUserByUsername($username);

            if ($user && password_verify($password, $user['password'])) {
                
                // --- ĐÂY LÀ PHẦN QUAN TRỌNG NHẤT ---
                if (session_status() === PHP_SESSION_NONE) session_start();

                $_SESSION['user_id'] = $user['id'];
                $_SESSION['username'] = $user['username'];
                $_SESSION['email'] = $user['email'];
                $_SESSION['role'] = $user['role'];
                
                // Dòng này giúp Header hiển thị đúng tên:
                $_SESSION['fullname'] = $user['fullname'] ?? $user['full_name'] ?? $user['username'];

                $redirect = 'student';
                if ($user['role'] == 1) $redirect = 'instructor';
                if ($user['role'] == 2) $redirect = 'admin';
                
                header("Location: index.php?controller=$redirect&action=dashboard");
                exit; 
            } else {
                $error = "Sai tên đăng nhập hoặc mật khẩu!";
            }
        }
        require dirname(__DIR__) . '/views/auth/login.php';
    }

    public function logout() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        session_unset();
        session_destroy();
        header('Location: index.php?controller=auth&action=login');
        exit;
    }
}
?>