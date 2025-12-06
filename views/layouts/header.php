
<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $page_title ?? 'Hệ thống Quản lý Khóa học Online' ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="/onlinecourse/assets/css/global.css">

    <?php 
    if (isset($css_files) && is_array($css_files)) {
        foreach ($css_files as $file) {
            echo '<link rel="stylesheet" href="/onlinecourse/assets/css/' . $file . '">';
        }
    }
    ?>
    <style>
        /* CSS fix chiều cao để sidebar chạy hết màn hình */
        .wrapper-row { min-height: calc(100vh - 56px); } /* 56px là chiều cao navbar */
    </style>
</head>
<body>

    <nav class="navbar navbar-expand-lg navbar-custom shadow-sm sticky-top">
        <div class="container-fluid">
            <a class="navbar-brand" href="/onlinecourse/index.php">
                <i class="fas fa-graduation-cap fa-lg me-2 text-warning"></i> EduOnline
            </a>
            
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#mainNav">
                <span class="navbar-toggler-icon"></span>
            </button>

            <div class="collapse navbar-collapse" id="mainNav">
                <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                    <li class="nav-item"><a class="nav-link" href="/onlinecourse/index.php">Trang chủ</a></li>
                    <li class="nav-item"><a class="nav-link" href="/onlinecourse/views/courses/index.php">Khóa học</a></li>
                </ul>

                <ul class="navbar-nav ms-auto">
                    <?php if (isset($_SESSION['user'])): ?>
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle" href="#" data-bs-toggle="dropdown">
                                <i class="fas fa-user-circle me-1"></i> <?= htmlspecialchars($_SESSION['user']['fullname']) ?>
                            </a>
                            <ul class="dropdown-menu dropdown-menu-end">
                                <li><a class="dropdown-item" href="#">Dashboard</a></li>
                                <li><hr class="dropdown-divider"></li>
                                <li><a class="dropdown-item text-danger" href="/onlinecourse/controllers/AuthController.php?action=logout">Đăng xuất</a></li>
                            </ul>
                        </li>
                    <?php else: ?>
                        <li class="nav-item"><a class="btn btn-primary btn-sm" href="/onlinecourse/views/auth/login.php">Đăng nhập</a></li>
                    <?php endif; ?>
                </ul>
            </div>
        </div>
    </nav>

    <div class="container-fluid">
        <div class="row wrapper-row">
