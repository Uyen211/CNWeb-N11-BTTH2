<?php
// 1. Logic lấy Role (Ưu tiên session role phẳng, fallback sang mảng user)
$role = isset($_SESSION['role']) ? $_SESSION['role'] : (isset($_SESSION['user']['role']) ? $_SESSION['user']['role'] : -1);

// 2. Hàm kiểm tra Active (Dựa trên Controller & Action trên URL)
function isAct($ctrl, $act = '') {
    $c = $_GET['controller'] ?? 'home';
    $a = $_GET['action'] ?? 'index';
    
    // Nếu controller khớp VÀ (action rỗng HOẶC action khớp)
    if ($c == $ctrl && ($act == '' || $a == $act)) {
        return 'active-item text-primary fw-bold bg-light rounded'; // Class active đẹp hơn
    }
    return 'text-dark';
}

// Nếu chưa đăng nhập thì không hiện sidebar (hoặc xử lý khác tùy bạn)
if ($role == -1) {
    return;
}
?>

<nav id="sidebarMenu" class="col-md-3 col-lg-2 d-md-block bg-white sidebar collapse position-fixed start-0 shadow-sm" style="height: 100vh; top: 60px; overflow-y: auto; border-right: 1px solid #dee2e6;">
    <div class="position-sticky pt-4 px-3">
        
        <?php if ($role == 0): ?>
            <h6 class="sidebar-heading d-flex justify-content-between align-items-center px-3 mt-2 mb-3 text-muted text-uppercase">
                <span>Góc học tập</span>
            </h6>
            
            <ul class="nav flex-column mb-2">
                <li class="nav-item mb-2">
                    <a class="nav-link <?= isAct('student', 'dashboard') ?>" href="index.php?controller=student&action=dashboard">
                        <i class="fas fa-home me-2"></i> Tổng quan
                    </a>
                </li>
                <li class="nav-item mb-2">
                    <a class="nav-link <?= isAct('student', 'my_courses') ?>" href="index.php?controller=student&action=my_courses">
                        <i class="fas fa-book-reader me-2"></i> Khóa học của tôi
                    </a>
                </li>
                <li class="nav-item mb-2">
                    <a class="nav-link <?= isAct('course', 'index') ?>" href="index.php?controller=course&action=index">
                        <i class="fas fa-list-ul me-2"></i> Danh sách khóa học
                    </a>
                </li>
            </ul>
        
        <?php elseif ($role == 1): ?>
            <h6 class="sidebar-heading d-flex justify-content-between align-items-center px-3 mt-2 mb-3 text-muted text-uppercase">
                <span>Quản lý giảng dạy</span>
            </h6>

            <ul class="nav flex-column mb-2">
                <li class="nav-item mb-2">
                    <a class="nav-link <?= isAct('instructor', 'dashboard') ?>" href="index.php?controller=instructor&action=dashboard">
                        <i class="fas fa-chart-line me-2"></i> Bảng điều khiển
                    </a>
                </li>
                <li class="nav-item mb-2">
                    <a class="nav-link <?= isAct('instructor', 'create') ?>" href="index.php?controller=instructor&action=create">
                        <i class="fas fa-plus-circle me-2"></i> Tạo khóa học mới
                    </a>
                </li>
                <li class="nav-item mb-2">
                    <a class="nav-link <?= isAct('instructor', 'index') ?>" href="index.php?controller=instructor&action=index">
                        <i class="fas fa-chalkboard-teacher me-2"></i> Khóa học của tôi
                    </a>
                </li>
            </ul>

        <?php elseif ($role == 2): ?>
            <h6 class="sidebar-heading d-flex justify-content-between align-items-center px-3 mt-2 mb-3 text-muted text-uppercase">
                <span>Hệ thống</span>
            </h6>

            <ul class="nav flex-column mb-2">
                <li class="nav-item mb-2">
                    <a class="nav-link <?= isAct('admin', 'dashboard') ?>" href="index.php?controller=admin&action=dashboard">
                        <i class="fas fa-tachometer-alt me-2"></i> Thống kê
                    </a>
                </li>
                <li class="nav-item mb-2">
                    <a class="nav-link <?= isAct('admin', 'users') ?>" href="index.php?controller=admin&action=users">
                        <i class="fas fa-users-cog me-2"></i> Quản lý Users
                    </a>
                </li>
                <li class="nav-item mb-2">
                    <a class="nav-link <?= isAct('admin', 'categories') ?>" href="index.php?controller=admin&action=categories">
                        <i class="fas fa-layer-group me-2"></i> Quản lý Danh mục
                    </a>
                </li>
            </ul>
        <?php endif; ?>

        <hr class="my-3">
        <ul class="nav flex-column">
            <li class="nav-item">
                <a class="nav-link text-danger fw-bold" href="index.php?controller=auth&action=logout">
                    <i class="fas fa-sign-out-alt me-2"></i> Đăng xuất
                </a>
            </li>
        </ul>

    </div>
</nav>