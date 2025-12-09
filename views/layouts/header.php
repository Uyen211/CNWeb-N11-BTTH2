
<?php
// Bắt đầu session nếu chưa có (đặt ở đầu file view hoặc index.php)
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
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;500;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="/onlinecourse/assets/css/header.css">
    <link rel="stylesheet" href="/onlinecourse/assets/css/footer.css">
    <link rel="stylesheet" href="/onlinecourse/assets/css/sidebar.css">


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

<body class="d-flex flex-column min-vh-100">

    <nav class="navbar navbar-expand-lg navbar-google fixed-top">
        <div class="container-fluid px-4">
            
            <a class="navbar-brand me-5" href="/onlinecourse/index.php">
                <i class="fas fa-shapes text-primary me-2"></i>
                <span class="fw-medium">EduPlatform</span>
            </a>
            
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#mainNav">
                <span class="navbar-toggler-icon"></span>
            </button>

            <div class="collapse navbar-collapse" id="mainNav">
                
                <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                    <li class="nav-item">
                        <a class="nav-link nav-link-google active" href="/onlinecourse/index.php">Trang chủ</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link nav-link-google" href="/onlinecourse/views/courses/index.php">Khóa học</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link nav-link-google" href="#">Giảng viên</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link nav-link-google" href="#">Blog</a>
                    </li>
                </ul>

                <form class="search-wrapper me-4 d-none d-lg-block" action="/onlinecourse/views/courses/search.php" method="GET">
                    <i class="fas fa-search search-icon"></i>
                    <input class="form-control search-input-google" type="search" name="q" placeholder="Tìm kiếm khóa học...">
                </form>

                <div class="d-flex align-items-center">
                    
                    <?php if (isset($_SESSION['user'])): ?>
                        <a href="#" class="text-secondary me-3 position-relative" style="font-size: 18px;">
                            <i class="fas fa-shopping-cart"></i>
                        </a>

                        <div class="dropdown">
                            <button class="avatar-btn dropdown-toggle hide-arrow" type="button" id="userMenu" data-bs-toggle="dropdown" aria-expanded="false">
                                <div class="user-avatar">
                                    <?= strtoupper(substr($_SESSION['user']['fullname'] ?? 'U', 0, 1)) ?>
                                </div>
                            </button>
                            
                            <ul class="dropdown-menu dropdown-menu-end dropdown-menu-google" aria-labelledby="userMenu">
                                <li class="dropdown-user-info">
                                    <div class="px-3 pb-2">
                                        <div class="fw-bold text-dark"><?= htmlspecialchars($_SESSION['user']['fullname']) ?></div>
                                        <div class="small text-muted text-truncate" style="max-width: 200px;">
                                            <?= $_SESSION['user']['email'] ?? 'user@example.com' ?>
                                        </div>
                                    </div>
                                </li>

                                <?php
                                    $dashboardLink = '#';
                                    if ($_SESSION['user']['role'] == 0) $dashboardLink = '/onlinecourse/views/student/dashboard.php';
                                    elseif ($_SESSION['user']['role'] == 1) $dashboardLink = '/onlinecourse/views/instructor/dashboard.php';
                                    elseif ($_SESSION['user']['role'] == 2) $dashboardLink = '/onlinecourse/views/admin/dashboard.php';
                                ?>
                                
                                <li>
                                    <a class="dropdown-item dropdown-item-google" href="<?= $dashboardLink ?>">
                                        <i class="fas fa-tachometer-alt me-2 text-secondary"></i> Bảng điều khiển
                                    </a>
                                </li>
                                <li>
                                    <a class="dropdown-item dropdown-item-google" href="#">
                                        <i class="fas fa-user me-2 text-secondary"></i> Hồ sơ cá nhân
                                    </a>
                                </li>
                                <li>
                                    <a class="dropdown-item dropdown-item-google" href="#">
                                        <i class="fas fa-cog me-2 text-secondary"></i> Cài đặt
                                    </a>
                                </li>
                                <li><hr class="dropdown-divider"></li>
                                <li>
                                    <a class="dropdown-item dropdown-item-google text-danger" href="/onlinecourse/controllers/AuthController.php?action=logout">
                                        <i class="fas fa-sign-out-alt me-2"></i> Đăng xuất
                                    </a>
                                </li>
                            </ul>
                        </div>

                    <?php else: ?>
                        <a href="/onlinecourse/views/auth/login.php" class="btn btn-google-text me-2">Đăng nhập</a>
                        <a href="/onlinecourse/views/auth/register.php" class="btn btn-google-primary">Đăng ký</a>
                    <?php endif; ?>
                    
                </div>
            </div>
        </div>
    </nav>

<div class="container-fluid flex-grow-1">
        <div class="row wrapper-row">