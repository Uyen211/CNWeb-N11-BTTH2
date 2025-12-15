
<?php
// Đảm bảo user đã đăng nhập mới hiện sidebar này
if (!isset($_SESSION['user'])) {
    echo '<main class="col-12 ms-sm-auto px-md-4 py-4">';
    return;
}

$role = $_SESSION['user']['role']; // 0: Student, 1: Instructor, 2: Admin

// Hàm kiểm tra link active (để tô màu xanh)
function isActive($path) {
    $current_uri = $_SERVER['REQUEST_URI'];
    // So sánh tương đối, nếu URL hiện tại chứa path thì trả về class active
    return (strpos($current_uri, $path) !== false) ? 'active' : '';
}
?>


<nav id="sidebarMenu" class="col-md-3 col-lg-2 d-md-block bg-light sidebar collapse border-end">

<div class="sidebar-wrapper d-none d-md-block">
    
    <div class="sidebar-heading">Quản lý</div>
    
    <div class="nav flex-column">
        
        <?php if ($role == 0): ?>
            <a class="sidebar-link <?= isActive('/student/dashboard.php') ?>" href="/onlinecourse/views/student/dashboard.php">
                <div class="sidebar-icon"><i class="fas fa-home"></i></div>
                <span>Tổng quan</span>
            </a>
            <a class="sidebar-link <?= isActive('/student/my_courses.php') ?>" href="/onlinecourse/views/student/my_courses.php">
                <div class="sidebar-icon"><i class="fas fa-book-reader"></i></div>
                <span>Khóa học của tôi</span>
            </a>
            <a class="sidebar-link <?= isActive('/student/course_progress.php') ?>" href="/onlinecourse/views/student/course_progress.php">
                <div class="sidebar-icon"><i class="fas fa-chart-line"></i></div>
                <span>Tiến độ học tập</span>
            </a>

        <?php elseif ($role == 1): ?>
            <a class="sidebar-link <?= isActive('/instructor/dashboard.php') ?>" href="/onlinecourse/index.php?controller=instructor&action=dashboard">
                <div class="sidebar-icon"><i class="fas fa-tachometer-alt"></i></div>
                <span>Dashboard</span>
            </a>
            <a class="sidebar-link <?= isActive('/instructor/course/manage.php') ?>" href="/onlinecourse/index.php?controller=course&action=instructor_courses">
                <div class="sidebar-icon"><i class="fas fa-chalkboard-teacher"></i></div>
                <span>Quản lý khóa học</span>
            </a>
            

        <?php elseif ($role == 2): ?>
            <a class="sidebar-link <?= isActive('/admin/dashboard.php') ?>" href="/onlinecourse/index.php?controller=admin&action=dashboard">
                <div class="sidebar-icon"><i class="fas fa-cogs"></i></div>
                <span>Tổng quan hệ thống</span>
            </a>
            <a class="sidebar-link <?= isActive('/admin/users/manage.php') ?>" href="/onlinecourse/index.php?controller=admin&action=users">
                <div class="sidebar-icon"><i class="fas fa-user-shield"></i></div>
                <span>Quản lý người dùng</span>
            </a>
            <a class="sidebar-link <?= isActive('/admin/categories/list.php') ?>" href="/onlinecourse/index.php?controller=admin&action=listCategory">
                <div class="sidebar-icon"><i class="fas fa-list"></i></div>
                <span>Danh mục khóa học</span>
            </a>
            <a class="sidebar-link <?= isActive('/admin/reports/statistics.php') ?>" href="/onlinecourse/index.php?controller=admin&action=statistics">
                <div class="sidebar-icon"><i class="fas fa-chart-bar"></i></div>
                <span>Báo cáo thống kê</span>
            </a>
        <?php endif; ?>

        <hr style="margin: 12px 0; border-top: 1px solid #dadce0;">

        <a class="sidebar-link text-danger" href="/onlinecourse/controllers/AuthController.php?action=logout">
            <div class="sidebar-icon"><i class="fas fa-sign-out-alt"></i></div>
            <span>Đăng xuất</span>
        </a>
    </div>
</div>
</nav>

<main class="col-md-9 ms-sm-auto col-lg-10 px-md-4 py-4">
