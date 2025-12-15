<?php 
// views/admin/dashboard.php 
?>

<div class="container-fluid px-4 py-5">
    <div class="text-center mb-5">
        <h1 class="fw-bold text-dark display-5">Trung Tâm Quản Trị</h1>
        <p class="text-muted fs-5">Xin chào, <?= htmlspecialchars($_SESSION['user']['fullname']) ?>! Bạn muốn làm gì hôm nay?</p>
    </div>

    <div class="row g-4 justify-content-center">
        
        <div class="col-xl-4 col-md-6">
            <a href="index.php?controller=admin&action=pending_courses" class="text-decoration-none">
                <div class="card admin-card bg-gradient-warning h-100 text-white shadow-lg border-0 transform-hover">
                    <div class="card-body d-flex flex-column justify-content-center align-items-center text-center p-5">
                        <div class="position-relative mb-3">
                            <i class="fas fa-clipboard-check fa-4x"></i>
                            <?php if(isset($pendingCount) && $pendingCount > 0): ?>
                                <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger border border-light fs-6">
                                    <?= $pendingCount ?>
                                    <span class="visually-hidden">yêu cầu mới</span>
                                </span>
                            <?php endif; ?>
                        </div>
                        <h3 class="fw-bold text-uppercase mt-2">Phê Duyệt Khóa Học</h3>
                        <p class="mb-0 opacity-75">Xem xét và duyệt các khóa học mới từ giảng viên</p>
                    </div>
                </div>
            </a>
        </div>

        <div class="col-xl-4 col-md-6">
            <a href="index.php?controller=admin&action=users" class="text-decoration-none">
                <div class="card admin-card bg-gradient-primary h-100 text-white shadow-lg border-0 transform-hover">
                    <div class="card-body d-flex flex-column justify-content-center align-items-center text-center p-5">
                        <i class="fas fa-users-cog fa-4x mb-3"></i>
                        <h3 class="fw-bold text-uppercase">Quản Lý Người Dùng</h3>
                        <p class="mb-0 opacity-75">Xem danh sách, kích hoạt hoặc khóa tài khoản</p>
                    </div>
                </div>
            </a>
        </div>

        <div class="col-xl-4 col-md-6">
            <a href="index.php?controller=admin&action=listCategory" class="text-decoration-none">
                <div class="card admin-card bg-gradient-primary h-100 text-white shadow-lg border-0 transform-hover">
                    <div class="card-body d-flex flex-column justify-content-center align-items-center text-center p-5">
                        <i class="fas fa-users-cog fa-4x mb-3"></i>
                        <h3 class="fw-bold text-uppercase">Quản Lý Danh mục</h3>
                        <p class="mb-0 opacity-75">Xem danh sách, sửa, xóa danh mục</p>
                    </div>
                </div>
            </a>
        </div>

        <div class="col-xl-4 col-md-6">
            <a href="index.php?controller=admin&action=statistics" class="text-decoration-none">
                <div class="card admin-card bg-gradient-success h-100 text-white shadow-lg border-0 transform-hover">
                    <div class="card-body d-flex flex-column justify-content-center align-items-center text-center p-5">
                        <i class="fas fa-chart-line fa-4x mb-3"></i>
                        <h3 class="fw-bold text-uppercase">Báo Cáo Thống Kê</h3>
                        <p class="mb-0 opacity-75">Xem biểu đồ doanh thu, tăng trưởng học viên</p>
                    </div>
                </div>
            </a>
        </div>

        <div class="col-xl-4 col-md-6">
            <a href="index.php?controller=admin&action=categories" class="text-decoration-none">
                <div class="card admin-card bg-gradient-danger h-100 text-white shadow-lg border-0 transform-hover">
                    <div class="card-body d-flex flex-column justify-content-center align-items-center text-center p-5">
                        <i class="fas fa-tags fa-4x mb-3"></i>
                        <h3 class="fw-bold text-uppercase">Quản Lý Danh Mục</h3>
                        <p class="mb-0 opacity-75">Thêm, sửa, xóa các danh mục khóa học</p>
                    </div>
                </div>
            </a>
        </div>

        <div class="col-xl-4 col-md-6">
            <a href="#" class="text-decoration-none">
                <div class="card admin-card bg-secondary h-100 text-white shadow-lg border-0 transform-hover">
                    <div class="card-body d-flex flex-column justify-content-center align-items-center text-center p-5">
                        <i class="fas fa-cogs fa-4x mb-3"></i>
                        <h3 class="fw-bold text-uppercase">Cấu Hình Hệ Thống</h3>
                        <p class="mb-0 opacity-75">Cài đặt chung, Banner, Footer (Coming soon)</p>
                    </div>
                </div>
            </a>
        </div>

    </div>
</div>

<style>
    /* CSS bổ sung để tạo hiệu ứng hover nổi lên cho các thẻ Card */
    .transform-hover {
        transition: transform 0.3s ease, box-shadow 0.3s ease;
    }
    .transform-hover:hover {
        transform: translateY(-10px);
        box-shadow: 0 1rem 3rem rgba(0,0,0,.175)!important;
        opacity: 0.95;
    }
    
    /* Đảm bảo các gradient class (lấy từ bài trước) có mặt */
    .bg-gradient-primary { background: linear-gradient(45deg, #4e73df, #224abe); }
    .bg-gradient-success { background: linear-gradient(45deg, #1cc88a, #13855c); }
    .bg-gradient-info    { background: linear-gradient(45deg, #36b9cc, #258391); }
    .bg-gradient-warning { background: linear-gradient(45deg, #f6c23e, #dda20a); }
    .bg-gradient-danger  { background: linear-gradient(45deg, #e74a3b, #be2617); }
</style>