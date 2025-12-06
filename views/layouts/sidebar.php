<?php
// views/layouts/sidebar.php

// Đảm bảo user đã đăng nhập
if (!isset($_SESSION['user'])) return;

$role = $_SESSION['user']['role']; // 0: Student, 1: Instructor, 2: Admin

// Hàm kiểm tra link active (để tô màu xanh)
function isActive($path) {
    $current_uri = $_SERVER['REQUEST_URI'];
    // So sánh tương đối, nếu URL hiện tại chứa path thì trả về class active
    return (strpos($current_uri, $path) !== false) ? 'active' : '';
}
?>



<div class="sidebar-wrapper d-none d-md-block">
    
    <div class="sidebar-heading">Quản lý</div>
    
    <div class="nav flex-column">
        
        <?php if ($role == 0): ?>
            <a class="sidebar-link <?= isActive('/student/dashboard.php') ?>" href="/BTTH2/views/student/dashboard.php">
                <div class="sidebar-icon"><i class="fas fa-home"></i></div>
                <span>Tổng quan</span>
            </a>
            <a class="sidebar-link <?= isActive('/student/my_courses.php') ?>" href="/BTTH2/views/student/my_courses.php">
                <div class="sidebar-icon"><i class="fas fa-book-reader"></i></div>
                <span>Khóa học của tôi</span>
            </a>
            <a class="sidebar-link <?= isActive('/student/course_progress.php') ?>" href="/BTTH2/views/student/course_progress.php">
                <div class="sidebar-icon"><i class="fas fa-chart-line"></i></div>
                <span>Tiến độ học tập</span>
            </a>

        <?php elseif ($role == 1): ?>
            <a class="sidebar-link <?= isActive('/instructor/dashboard.php') ?>" href="/BTTH2/views/instructor/dashboard.php">
                <div class="sidebar-icon"><i class="fas fa-tachometer-alt"></i></div>
                <span>Dashboard</span>
            </a>
            <a class="sidebar-link <?= isActive('/instructor/course/manage.php') ?>" href="/BTTH2/views/instructor/course/manage.php">
                <div class="sidebar-icon"><i class="fas fa-chalkboard-teacher"></i></div>
                <span>Quản lý khóa học</span>
            </a>
            <a class="sidebar-link <?= isActive('/instructor/students/list.php') ?>" href="/BTTH2/views/instructor/students/list.php">
                <div class="sidebar-icon"><i class="fas fa-users"></i></div>
                <span>Học viên của tôi</span>
            </a>
            <a class="sidebar-link <?= isActive('/instructor/materials/upload.php') ?>" href="/BTTH2/views/instructor/materials/upload.php">
                <div class="sidebar-icon"><i class="fas fa-upload"></i></div>
                <span>Tài liệu</span>
            </a>

        <?php elseif ($role == 2): ?>
            <a class="sidebar-link <?= isActive('/admin/dashboard.php') ?>" href="/BTTH2/views/admin/dashboard.php">
                <div class="sidebar-icon"><i class="fas fa-cogs"></i></div>
                <span>Tổng quan hệ thống</span>
            </a>
            <a class="sidebar-link <?= isActive('/admin/users/manage.php') ?>" href="/BTTH2/views/admin/users/manage.php">
                <div class="sidebar-icon"><i class="fas fa-user-shield"></i></div>
                <span>Quản lý người dùng</span>
            </a>
            <a class="sidebar-link <?= isActive('/admin/categories/list.php') ?>" href="/BTTH2/views/admin/categories/list.php">
                <div class="sidebar-icon"><i class="fas fa-list"></i></div>
                <span>Danh mục khóa học</span>
            </a>
            <a class="sidebar-link <?= isActive('/admin/reports/statistics.php') ?>" href="/BTTH2/views/admin/reports/statistics.php">
                <div class="sidebar-icon"><i class="fas fa-chart-bar"></i></div>
                <span>Báo cáo thống kê</span>
            </a>
        <?php endif; ?>

        <hr style="margin: 12px 0; border-top: 1px solid #dadce0;">

        <a class="sidebar-link text-danger" href="/BTTH2/controllers/AuthController.php?action=logout">
            <div class="sidebar-icon"><i class="fas fa-sign-out-alt"></i></div>
            <span>Đăng xuất</span>
        </a>
    </div>
</div>