<?php
require_once dirname(__DIR__) . '/config/Database.php';
require_once dirname(__DIR__) . '/models/User.php';
// require_once 'BaseController.php'; // Bỏ dòng này nếu không dùng

class AuthController {

    public function register() {
        $error = '';

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $database = new Database();
            $userModel = new User($database);

            $fullname = $_POST['fullname'] ?? '';
            $username = $_POST['username'] ?? '';
            $email = $_POST['email'] ?? '';
            $password = $_POST['password'] ?? '';
            $confirm_password = $_POST['confirm_password'] ?? '';
            $role = 0;

            if ($password !== $confirm_password) {
                $error = "Mật khẩu nhập lại không khớp!";
            } else {
                if ($userModel->register($username, $email, $password, $fullname, $role)) {
                    // --- QUAN TRỌNG: Thêm exit sau header ---
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
            $userModel = new User($database);

            $username = $_POST['username'] ?? '';
            $password = $_POST['password'] ?? '';

            $user = $userModel->getUserByUsername($username);

            if ($user && password_verify($password, $user['password'])) {
                $_SESSION['user'] = $user;
                
                $redirect = 'student';  // Mặc định là student (học viên)
                if ($user['role'] == 1) $redirect = 'instructor';
                if ($user['role'] == 2) $redirect = 'admin';
                
                // --- QUAN TRỌNG: Thêm exit sau header ---
                header("Location: index.php?controller=$redirect&action=dashboard");
                exit; 
            } else {
                $error = "Sai tên đăng nhập hoặc mật khẩu!";
            }
        }

        require dirname(__DIR__) . '/views/auth/login.php';
    }

    // controllers/AuthController.php

    public function logout() {
        // 1. Khởi động session nếu chưa có
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        // 2. Xóa sạch session
        session_unset();
        session_destroy();

        // 3. --- SỬA DÒNG NÀY ---
        // Chuyển hướng về trang Đăng nhập
        header('Location: index.php?controller=auth&action=login');
        exit;
    }
}
?>