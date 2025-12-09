<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
// Ưu tiên base_url ngắn gọn của bạn
$base_url = '/onlinecourse';
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $page_title ?? 'EduOnline - Hệ thống học trực tuyến' ?></title>
    
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;500;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    
    <link rel="stylesheet" href="<?= $base_url ?>/assets/css/header.css">
    <link rel="stylesheet" href="<?= $base_url ?>/assets/css/sidebar.css">
    <link rel="stylesheet" href="<?= $base_url ?>/assets/css/footer.css">
    <link rel="stylesheet" href="<?= $base_url ?>/assets/css/home.css">
    <link rel="stylesheet" href="<?= $base_url ?>/assets/css/auth.css">
</head>
<body class="d-flex flex-column min-vh-100">

    <nav class="navbar navbar-expand-lg navbar-google fixed-top">
        <div class="container-fluid px-4">
            
            <a class="navbar-brand me-5" href="index.php">
                <i class="fas fa-shapes text-primary me-2"></i>
                <span class="fw-medium">EduPlatform</span>
            </a>
            
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#mainNav">
                <span class="navbar-toggler-icon"></span>
            </button>

            <div class="collapse navbar-collapse" id="mainNav">
                
                <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                    <li class="nav-item">
                        <a class="nav-link nav-link-google active" href="index.php">Trang chủ</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link nav-link-google" href="index.php?controller=course&action=index">Khóa học</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link nav-link-google" href="#">Giảng viên</a>
                    </li>
                    <li class="nav-item">
                         <a class="nav-link nav-link-google" href="#">Blog</a>
                    </li>
                </ul>

                <form class="search-wrapper me-4 d-none d-lg-block position-relative" action="index.php" method="GET">
                    <input type="hidden" name="controller" value="course">
                    <input type="hidden" name="action" value="search">
                    <i class="fas fa-search search-icon"></i>
                    <input class="form-control search-input-google" type="search" name="q" placeholder="Tìm kiếm khóa học...">
                </form>

                <div class="d-flex align-items-center">
                    
                    <?php if (isset($_SESSION['user_id'])): ?>
                        <a href="#" class="text-secondary me-3 position-relative" style="font-size: 18px;">
                            <i class="fas fa-shopping-cart"></i>
                        </a>

                        <div class="dropdown">
                            <button class="avatar-btn dropdown-toggle hide-arrow d-flex align-items-center gap-2" type="button" id="userMenu" data-bs-toggle="dropdown" aria-expanded="false">
                                <div class="user-avatar">
                                    <?= strtoupper(substr($_SESSION['fullname'] ?? $_SESSION['username'] ?? 'U', 0, 1)) ?>
                                </div>
                            </button>
                            
                            <ul class="dropdown-menu dropdown-menu-end dropdown-menu-google shadow border-0 mt-2" aria-labelledby="userMenu">
                                <li class="dropdown-user-info">
                                    <div class="px-3 pb-2 pt-2">
                                        <div class="fw-bold text-dark">
                                            <?= htmlspecialchars($_SESSION['fullname'] ?? $_SESSION['username'] ?? 'User') ?>
                                        </div>
                                        <div class="small text-muted text-truncate" style="max-width: 200px;">
                                            <?= $_SESSION['email'] ?? '' ?>
                                        </div>
                                    </div>
                                </li>

                                <?php
                                    // Logic Dashboard Link theo Role
                                    $dashboardLink = '#';
                                    if (isset($_SESSION['role'])) {
                                        if ($_SESSION['role'] == 0) $dashboardLink = 'index.php?controller=student&action=dashboard';
                                        elseif ($_SESSION['role'] == 1) $dashboardLink = 'index.php?controller=instructor&action=dashboard';
                                        elseif ($_SESSION['role'] == 2) $dashboardLink = 'index.php?controller=admin&action=dashboard';
                                    }
                                ?>
                                
                                <li>
                                    <a class="dropdown-item dropdown-item-google" href="<?= $dashboardLink ?>">
                                        <i class="fas fa-tachometer-alt me-2 text-secondary w-20"></i> Bảng điều khiển
                                    </a>
                                </li>
                                <li>
                                    <a class="dropdown-item dropdown-item-google" href="index.php?controller=user&action=profile">
                                        <i class="fas fa-user me-2 text-secondary w-20"></i> Hồ sơ cá nhân
                                    </a>
                                </li>
                                <li>
                                    <a class="dropdown-item dropdown-item-google" href="index.php?controller=user&action=change_password">
                                        <i class="fas fa-key me-2 text-secondary w-20"></i> Đổi mật khẩu
                                    </a>
                                </li>
                                <li><hr class="dropdown-divider"></li>
                                <li>
                                    <a class="dropdown-item dropdown-item-google text-danger" href="index.php?controller=auth&action=logout">
                                        <i class="fas fa-sign-out-alt me-2 w-20"></i> Đăng xuất
                                    </a>
                                </li>
                            </ul>
                        </div>

                    <?php else: ?>
                        <a href="index.php?controller=auth&action=login" class="btn btn-google-text me-2">Đăng nhập</a>
                        <a href="index.php?controller=auth&action=register" class="btn btn-google-primary">Đăng ký</a>
                    <?php endif; ?>
                    
                </div>
            </div>
        </div>
    </nav>
    
    <main class="flex-grow-1">