<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
$base_url = '/onlinecourse'; // Đảm bảo đúng tên thư mục của bạn
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tài khoản - EduPlatform</title>
    
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;500;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    
    <link rel="stylesheet" href="<?= $base_url ?>/assets/css/auth.css">
    
    <style>
        .brand-logo-corner {
            position: absolute;
            top: 25px;
            left: 40px;
            z-index: 1000;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 10px;
            transition: transform 0.3s;
        }
        
        /* --- SỬA MÀU Ở ĐÂY: Dùng màu tối (#1f2937) hoặc xanh (#1a73e8) để nổi trên nền trời --- */
        .brand-logo-corner i { font-size: 2rem; color: #ffffffff; } 
        .brand-logo-corner span { 
            font-size: 1.5rem; font-weight: 700; color: #ffffffff; font-family: 'Roboto', sans-serif; 
        }
        
        .brand-logo-corner:hover { transform: translateY(-2px); }
        
        @media(max-width: 576px) {
            .brand-logo-corner { left: 20px; top: 20px; }
            .brand-logo-corner span { display: none; }
        }
    </style>
</head>
<body>

    <a href="#" class="brand-logo-corner">
        <i class="fas fa-shapes"></i>
        <span>EduPlatform</span>
    </a>