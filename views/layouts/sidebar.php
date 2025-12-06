
<?php
// LOGIC: Nếu không có user, ta không hiện sidebar,
// NHƯNG ta vẫn phải mở thẻ <main> full màn hình (col-12) để nội dung không bị vỡ layout.
if (!isset($_SESSION['user'])) {
    echo '<main class="col-12 ms-sm-auto px-md-4 py-4">';
    return;
}

$role = $_SESSION['user']['role']; 
?>

<nav id="sidebarMenu" class="col-md-3 col-lg-2 d-md-block bg-light sidebar collapse border-end">
    <div class="position-sticky pt-3">
        <h5 class="text-primary mb-4 text-center px-2">Menu Quản Lý</h5>
        <ul class="nav flex-column">
            
            <?php if ($role == 0): ?>
                <li class="nav-item"><a class="nav-link" href="/onlinecourse/views/student/dashboard.php"><i class="fas fa-home me-2"></i> Tổng quan</a></li>
                <li class="nav-item"><a class="nav-link" href="/onlinecourse/views/student/my_courses.php"><i class="fas fa-book-reader me-2"></i> Khóa học của tôi</a></li>
            
            <?php elseif ($role == 1): ?>
                <li class="nav-item"><a class="nav-link" href="/onlinecourse/views/instructor/dashboard.php"><i class="fas fa-tachometer-alt me-2"></i> Dashboard</a></li>
                <li class="nav-item"><a class="nav-link" href="/onlinecourse/views/instructor/course/manage.php"><i class="fas fa-chalkboard-teacher me-2"></i> Quản lý khóa học</a></li>

            <?php elseif ($role == 2): ?>
                <li class="nav-item"><a class="nav-link" href="/onlinecourse/views/admin/dashboard.php"><i class="fas fa-cogs me-2"></i> Tổng quan</a></li>
                <li class="nav-item"><a class="nav-link" href="index.php?controller=admin&action=index"><i class="fas fa-list me-2"></i> Danh mục khóa học</a></li>
                <li class="nav-item"><a class="nav-link" href="/onlinecourse/views/admin/users/manage.php"><i class="fas fa-user-shield me-2"></i> Người dùng</a></li>
            <?php endif; ?>

            <li class="nav-item mt-3 border-top pt-2">
                 <a class="nav-link text-danger" href="/onlinecourse/controllers/AuthController.php?action=logout"><i class="fas fa-sign-out-alt me-2"></i> Đăng xuất</a>
            </li>
        </ul>
    </div>
</nav>

<main class="col-md-9 ms-sm-auto col-lg-10 px-md-4 py-4">
