<?php
// 1. Logic lấy Role chuẩn (Ưu tiên session phẳng, fallback về mảng user)
$role = isset($_SESSION['role']) ? $_SESSION['role'] : (isset($_SESSION['user']['role']) ? $_SESSION['user']['role'] : -1);

// Nếu chưa đăng nhập -> Không hiện sidebar -> Chỉ mở thẻ main full màn hình để chứa nội dung Login/Register
if ($role == -1) {
    echo '<main class="container-fluid py-4" style="margin-top: 60px;">'; 
    return;
}

// 2. Hàm kiểm tra Active (Dựa trên Controller & Action trên URL)
function isAct($ctrl, $act = '') {
    $c = $_GET['controller'] ?? 'home';
    $a = $_GET['action'] ?? 'index';
    // Kiểm tra trùng Controller và (Action rỗng hoặc Action trùng)
    if ($c == $ctrl && ($act == '' || $a == $act)) {
        return 'active-item'; // Class riêng để style
    }
    return 'text-dark';
}
?>


<div class="container-fluid">
    <div class="row">
        <nav id="sidebarMenu" class="col-md-3 col-lg-2 d-md-block bg-white sidebar collapse position-fixed start-0">
            <div class="position-sticky">
                
                <?php if ($role == 0): // HỌC VIÊN ?>
                    <div class="sidebar-heading">Góc học tập</div>
                    
                    <a class="nav-link-custom <?= isAct('student', 'dashboard') ?>" href="index.php?controller=student&action=dashboard">
                        <i class="fas fa-home"></i> Tổng quan
                    </a>
                    
                    <a class="nav-link-custom <?= isAct('student', 'my-courses') ?>" href="index.php?controller=student&action=my-courses">
                        <i class="fas fa-book-open"></i> Khóa học của tôi
                    </a>

                    <a class="nav-link-custom <?= isAct('student', 'history') ?>" href="index.php?controller=student&action=history">
                        <i class="fas fa-history"></i> Lịch sử học tập
                    </a>

                    <div class="sidebar-heading mt-3">Khám phá</div>
                    <a class="nav-link-custom <?= isAct('course', 'index') ?>" href="index.php?controller=course&action=index">
                        <i class="fas fa-search"></i> Tìm khóa học mới
                    </a>
                
                <?php elseif ($role == 1): // GIẢNG VIÊN ?>
                    <div class="sidebar-heading">Quản lý giảng dạy</div>

                    <a class="nav-link-custom <?= isAct('instructor', 'dashboard') ?>" href="index.php?controller=instructor&action=dashboard">
                        <i class="fas fa-chart-line"></i> Bảng điều khiển
                    </a>

                    <a class="nav-link-custom <?= isAct('instructor', 'create') ?>" href="index.php?controller=instructor&action=create">
                        <i class="fas fa-plus-circle text-primary"></i> Tạo khóa học mới
                    </a>

                    <a class="nav-link-custom <?= isAct('instructor', 'manage') ?>" href="index.php?controller=instructor&action=manage">
                        <i class="fas fa-chalkboard-teacher"></i> Khóa học của tôi
                    </a>

                    <a class="nav-link-custom <?= isAct('instructor', 'students') ?>" href="index.php?controller=instructor&action=students">
                        <i class="fas fa-users"></i> Danh sách học viên
                    </a>

                <?php elseif ($role == 2): // ADMIN ?>
                    <div class="sidebar-heading">Hệ thống</div>

                    <a class="nav-link-custom <?= isAct('admin', 'dashboard') ?>" href="index.php?controller=admin&action=dashboard">
                        <i class="fas fa-tachometer-alt"></i> Thống kê chung
                    </a>

                    <a class="nav-link-custom <?= isAct('admin', 'users') ?>" href="index.php?controller=admin&action=users">
                        <i class="fas fa-user-shield"></i> Quản lý người dùng
                    </a>

                    <a class="nav-link-custom <?= isAct('admin', 'categories') ?>" href="index.php?controller=admin&action=categories">
                        <i class="fas fa-tags"></i> Danh mục khóa học
                    </a>
                <?php endif; ?>

                <div class="border-top my-3 mx-2"></div>
                <div class="sidebar-heading">Cá nhân</div>

                <a class="nav-link-custom <?= isAct('user', 'profile') ?>" href="index.php?controller=user&action=profile">
                    <i class="fas fa-user-circle"></i> Hồ sơ cá nhân
                </a>
            
                <a class="nav-link-custom text-danger mt-2" href="index.php?controller=auth&action=logout">
                    <i class="fas fa-sign-out-alt"></i> Đăng xuất
                </a>

            </div>
        </nav>

        <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4 py-4" style="margin-top: 50px;">