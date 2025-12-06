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
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-sRIl4kxILFvY47J16cr9ZwB07vP4J8+LH7qKQnuqkuIAvNWLzeN8tE5YBujZqJLB" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="/onlinecourse/assets/css/style.css">
</head>
<body class="d-flex flex-column min-vh-100">


    <nav class="navbar navbar-expand-lg navbar-custom shadow-sm sticky-top">
        <div class="container">
            <a class="navbar-brand" href="/onlinecourse/index.php">
                <i class="fas fa-graduation-cap fa-lg me-2 text-accent"></i> EduOnline
            </a>
           
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#mainNav">
                <span class="navbar-toggler-icon"></span>
            </button>


            <div class="collapse navbar-collapse" id="mainNav">
                <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                    <li class="nav-item">
                        <a class="nav-link" href="/onlinecourse/index.php">Trang chủ</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="/onlinecourse/views/courses/index.php">Khóa học</a>
                    </li>
                </ul>


                <form class="d-flex me-3" action="/onlinecourse/views/courses/search.php" method="GET">
                    <input class="form-control me-2" type="search" name="q" placeholder="Tìm khóa học..." aria-label="Search">
                    <button class="btn btn-accent btn-sm" type="submit"><i class="fas fa-search"></i></button>
                </form>


                <ul class="navbar-nav">
                    <?php if (isset($_SESSION['user'])): ?>
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle" href="#" id="userDropdown" role="button" data-bs-toggle="dropdown">
                                <i class="fas fa-user-circle me-1"></i>
                                <?= htmlspecialchars($_SESSION['user']['fullname']) ?>
                            </a>
                            <ul class="dropdown-menu dropdown-menu-end">
                                <?php
                                    // Điều hướng Dashboard dựa trên Role
                                    $dashboardLink = '#';
                                    if ($_SESSION['user']['role'] == 0) $dashboardLink = '/onlinecourse/views/student/dashboard.php';
                                    elseif ($_SESSION['user']['role'] == 1) $dashboardLink = '/onlinecourse/views/instructor/dashboard.php';
                                    elseif ($_SESSION['user']['role'] == 2) $dashboardLink = '/onlinecourse/views/admin/dashboard.php';
                                ?>
                                <li><a class="dropdown-item" href="<?= $dashboardLink ?>">Bảng điều khiển</a></li>
                                <li><a class="dropdown-item" href="#">Hồ sơ cá nhân</a></li>
                                <li><hr class="dropdown-divider"></li>
                                <li><a class="dropdown-item text-danger" href="/onlinecourse/controllers/AuthController.php?action=logout">Đăng xuất</a></li>
                            </ul>
                        </li>
                    <?php else: ?>
                        <li class="nav-item me-2">
                            <a class="btn btn-outline-light btn-sm" href="/onlinecourse/views/auth/login.php">Đăng nhập</a>
                        </li>
                        <li class="nav-item">
                            <a class="btn btn-accent btn-sm" href="/onlinecourse/views/auth/register.php">Đăng ký</a>
                        </li>
                    <?php endif; ?>
                </ul>
            </div>
        </div>
    </nav>
   
    <main class="flex-grow-1">
