<?php 
// 1. Include Header
include_once dirname(__DIR__) . '/layouts/header.php'; 
?>

<section class="dev-hero">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-lg-6 order-2 order-lg-1">
                <span class="hero-badge">EduPlatform Version 2.0</span>
                
                <h1 class="hero-title">
                    Xây dựng kiến thức,<br>
                    <span class="text-gradient">Phát triển tương lai</span>
                </h1>
                
                <p class="hero-desc">
                    Hệ thống quản lý học tập toàn diện. Truy cập tài liệu chuẩn quốc tế, thực hành dự án thực tế và kết nối với cộng đồng chuyên gia.
                </p>
                
                <div class="d-flex flex-wrap align-items-center">
                    <?php if (!isset($_SESSION['user'])): ?>
                        <a href="/onlinecourse/views/auth/register.php" class="btn-dev btn-dev-filled me-lg-3">
                            Bắt đầu miễn phí
                        </a>
                        <a href="/onlinecourse/views/auth/login.php" class="btn-dev btn-dev-text">
                            Đăng nhập <i class="fas fa-arrow-right ms-2"></i>
                        </a>
                    <?php else: ?>
                        <a href="/onlinecourse/views/student/dashboard.php" class="btn-dev btn-dev-filled">
                            Vào Dashboard
                        </a>
                    <?php endif; ?>
                </div>
            </div>
            
            <div class="col-lg-6 order-1 order-lg-2 mb-4 mb-lg-0">
                <div class="hero-image-container">
                    <img src="<?= $base_url ?? '' ?>/assets/imgs/homepage_first.png" 
                         alt="Học trực tuyến minh họa" 
                         class="hero-img img-fluid"
                         onerror="this.src='https://cdni.iconscout.com/illustration/premium/thumb/online-education-4388301-3655160.png'"> 
                         </div>
            </div>
        </div>
    </div>
</section>

<section class="dev-section">
    <div class="container">
        <div class="text-center mb-5">
            <h2 class="section-title mb-2">Khám phá theo vai trò</h2>
            <p class="text-muted">Chọn vai trò phù hợp để bắt đầu hành trình của bạn</p>
        </div>
        
        <div class="row g-4">
            <div class="col-md-4">
                <div class="dev-card">
                    <div class="card-icon-wrapper">
                        <i class="fas fa-graduation-cap"></i>
                    </div>
                    <h3 class="card-title">Dành cho Học viên</h3>
                    <p class="card-text">
                        Truy cập kho khóa học đa dạng, làm bài tập thực hành và nhận chứng chỉ hoàn thành. Theo dõi tiến độ học tập cá nhân hóa.
                    </p>
                    <a href="#" class="card-link">
                        Xem lộ trình học <i class="fas fa-arrow-right ms-2" style="font-size: 0.8rem"></i>
                    </a>
                </div>
            </div>

            <div class="col-md-4">
                <div class="dev-card">
                    <div class="card-icon-wrapper">
                        <i class="fas fa-chalkboard-teacher"></i>
                    </div>
                    <h3 class="card-title">Dành cho Giảng viên</h3>
                    <p class="card-text">
                        Công cụ quản lý lớp học mạnh mẽ. Tạo bài giảng, tải lên tài liệu và đánh giá sinh viên với hệ thống báo cáo chi tiết.
                    </p>
                    <a href="#" class="card-link">
                        Công cụ giảng dạy <i class="fas fa-arrow-right ms-2" style="font-size: 0.8rem"></i>
                    </a>
                </div>
            </div>

            <div class="col-md-4">
                <div class="dev-card">
                    <div class="card-icon-wrapper">
                        <i class="fas fa-book-open"></i>
                    </div>
                    <h3 class="card-title">Thư viện & Tài liệu</h3>
                    <p class="card-text">
                        Kho tài liệu tham khảo mở rộng, API docs và các bài viết chuyên sâu về công nghệ được cập nhật liên tục.
                    </p>
                    <a href="#" class="card-link">
                        Truy cập thư viện <i class="fas fa-arrow-right ms-2" style="font-size: 0.8rem"></i>
                    </a>
                </div>
            </div>
        </div>
    </div>
</section>

<section class="path-section">
    <div class="container">
        <div class="row">
            <div class="col-lg-4 mb-4">
                <h3 class="section-title mb-3">Tại sao chọn EduPlatform?</h3>
                <p style="color: var(--dev-text-body); font-size: 1.1rem; line-height: 1.6;">
                    Nền tảng được xây dựng với mục tiêu tối ưu hóa trải nghiệm học tập, tập trung vào tính thực tiễn và khả năng mở rộng.
                </p>
                <div class="mt-4">
                    <a href="#" class="btn-dev btn-dev-filled">Tìm hiểu thêm</a>
                </div>
            </div>
            
            <div class="col-lg-7 offset-lg-1">
                <div class="row">
                    <div class="col-md-6 mb-4">
                        <div class="path-item">
                            <i class="fas fa-project-diagram path-icon"></i>
                            <div>
                                <h5 class="fw-bold mb-2 text-dark">Lộ trình rõ ràng</h5>
                                <p class="text-secondary mb-0">Các khóa học được sắp xếp theo cấp độ từ cơ bản đến nâng cao.</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6 mb-4">
                        <div class="path-item">
                            <i class="fas fa-code path-icon"></i>
                            <div>
                                <h5 class="fw-bold mb-2 text-dark">Thực hành là chính</h5>
                                <p class="text-secondary mb-0">Hệ thống bài tập coding trực quan và dự án thực tế.</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6 mb-4">
                        <div class="path-item">
                            <i class="fas fa-users path-icon"></i>
                            <div>
                                <h5 class="fw-bold mb-2 text-dark">Cộng đồng hỗ trợ</h5>
                                <p class="text-secondary mb-0">Kết nối với hàng ngàn học viên và chuyên gia khác.</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6 mb-4">
                        <div class="path-item">
                            <i class="fas fa-mobile-alt path-icon"></i>
                            <div>
                                <h5 class="fw-bold mb-2 text-dark">Đa nền tảng</h5>
                                <p class="text-secondary mb-0">Học mọi lúc mọi nơi trên mọi thiết bị.</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<?php 
// 3. Include Footer
include_once dirname(__DIR__) . '/layouts/footer.php'; 
?>