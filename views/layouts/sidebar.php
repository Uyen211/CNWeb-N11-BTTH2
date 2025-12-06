<?php
// Đảm bảo user đã đăng nhập mới hiện sidebar này
if (!isset($_SESSION['user'])) return;


$role = $_SESSION['user']['role']; // 0: Student, 1: Instructor, 2: Admin
?>


<div class="sidebar p-3 d-none d-md-block h-100">
    <h5 class="text-primary mb-4 text-center">Menu Quản Lý</h5>
    <ul class="nav flex-column">
       
        <?php if ($role == 0): ?>
            <li class="nav-item">
                <a class="nav-link" href="/BTTH2/views/student/dashboard.php">
                    <i class="fas fa-home me-2"></i> Tổng quan
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="/BTTH2/views/student/my_courses.php">
                    <i class="fas fa-book-reader me-2"></i> Khóa học của tôi
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="/BTTH2/views/student/course_progress.php">
                    <i class="fas fa-chart-line me-2"></i> Tiến độ học tập
                </a>
            </li>


        <?php elseif ($role == 1): ?>
            <li class="nav-item">
                <a class="nav-link" href="/BTTH2/views/instructor/dashboard.php">
                    <i class="fas fa-tachometer-alt me-2"></i> Dashboard
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="/BTTH2/views/instructor/course/manage.php">
                    <i class="fas fa-chalkboard-teacher me-2"></i> Quản lý khóa học
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="/BTTH2/views/instructor/students/list.php">
                    <i class="fas fa-users me-2"></i> Học viên của tôi
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="/BTTH2/views/instructor/materials/upload.php">
                    <i class="fas fa-upload me-2"></i> Tài liệu
                </a>
            </li>


        <?php elseif ($role == 2): ?>
            <li class="nav-item">
                <a class="nav-link" href="/BTTH2/views/admin/dashboard.php">
                    <i class="fas fa-cogs me-2"></i> Tổng quan hệ thống
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="/BTTH2/views/admin/users/manage.php">
                    <i class="fas fa-user-shield me-2"></i> Quản lý người dùng
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="/BTTH2/views/admin/categories/list.php">
                    <i class="fas fa-list me-2"></i> Danh mục khóa học
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="/BTTH2/views/admin/reports/statistics.php">
                    <i class="fas fa-chart-bar me-2"></i> Báo cáo thống kê
                </a>
            </li>
        <?php endif; ?>


        <li class="nav-item mt-3">
             <a class="nav-link text-danger" href="/BTTH2/controllers/AuthController.php?action=logout">
                <i class="fas fa-sign-out-alt me-2"></i> Đăng xuất
            </a>
        </li>
    </ul>
</div>
