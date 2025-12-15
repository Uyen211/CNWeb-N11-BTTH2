<?php 
// Include Header
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
                    <?php if (!isset($_SESSION['user_id'])): ?>
                        <a href="index.php?controller=auth&action=register" class="btn-dev btn-dev-filled me-lg-3">
                            Bắt đầu miễn phí
                        </a>
                        <a href="index.php?controller=auth&action=login" class="btn-dev btn-dev-text">
                            Đăng nhập <i class="fas fa-arrow-right ms-2"></i>
                        </a>
                    <?php else: ?>
                        <?php 
                            $dashboardLink = 'index.php?controller=student&action=dashboard';
                            if(isset($_SESSION['role']) && $_SESSION['role'] == 1) $dashboardLink = 'index.php?controller=instructor&action=dashboard';
                            if(isset($_SESSION['role']) && $_SESSION['role'] == 2) $dashboardLink = 'index.php?controller=admin&action=dashboard';
                        ?>
                        <a href="<?= $dashboardLink ?>" class="btn-dev btn-dev-filled">
                            Vào Dashboard
                        </a>
                    <?php endif; ?>
                </div>
            </div>
            
            <div class="col-lg-6 order-1 order-lg-2 mb-4 mb-lg-0">
                <div class="hero-image-container">
                    <img src="assets/imgs/homepage_first.png" 
                         alt="Học trực tuyến minh họa" 
                         class="hero-img img-fluid"
                         onerror="this.src='https://cdni.iconscout.com/illustration/premium/thumb/online-education-4388301-3655160.png'"> 
                </div>
            </div>
        </div>
    </div>
</section>

<section class="py-5 bg-light">
    <div class="container">
        <div class="text-center mb-5">
            <h2 class="section-title mb-2">Khóa học mới nhất</h2>
            <p class="text-muted">Cập nhật kiến thức mới nhất từ hệ thống</p>
        </div>

        <div class="row g-4">
            <?php if (!empty($latestCourses)): ?>
                <?php foreach ($latestCourses as $course): ?>
                    <div class="col-md-6 col-lg-3">
                        <div class="card h-100 border-0 shadow-sm rounded-4 overflow-hidden course-card hover-lift">
                            <div class="position-relative">
                                <?php $imgSrc = !empty($course['image']) ? 'assets/uploads/courses/'.$course['image'] : 'https://via.placeholder.com/400x225?text=Course'; ?>
                                <a href="index.php?controller=course&action=detail&id=<?= $course['id'] ?>">
                                    <img src="<?= $imgSrc ?>" class="card-img-top object-fit-cover" style="height: 180px; width: 100%;" alt="<?= htmlspecialchars($course['title']) ?>">
                                </a>
                                <span class="badge position-absolute top-0 end-0 m-3 shadow-sm <?= $course['price'] == 0 ? 'bg-success' : 'bg-primary' ?>">
                                    <?= $course['price'] == 0 ? 'Miễn phí' : number_format($course['price']) . ' đ' ?>
                                </span>
                            </div>

                            <div class="card-body d-flex flex-column p-3">
                                <h5 class="card-title fw-bold text-truncate mb-1">
                                    <a href="index.php?controller=course&action=detail&id=<?= $course['id'] ?>" class="text-dark text-decoration-none">
                                        <?= htmlspecialchars($course['title']) ?>
                                    </a>
                                </h5>
                                <small class="text-muted mb-3">
                                    <i class="fas fa-user-tie me-1"></i> <?= htmlspecialchars($course['instructor_name'] ?? 'Giảng viên') ?>
                                </small>
                                
                                <div class="mt-auto">
                                    <a href="index.php?controller=course&action=detail&id=<?= $course['id'] ?>" class="btn btn-sm btn-outline-primary rounded-pill w-100">
                                        Xem chi tiết
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="col-12 text-center">
                    <p>Chưa có khóa học nào. Hãy quay lại sau nhé!</p>
                </div>
            <?php endif; ?>
        </div>
        
        <div class="text-center mt-5">
            <a href="index.php?controller=course&action=index" class="btn btn-outline-dark rounded-pill px-4">Xem tất cả khóa học</a>
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
                <div class="dev-card h-100">
                    <div class="card-icon-wrapper">
                        <i class="fas fa-graduation-cap"></i>
                    </div>
                    <h3 class="card-title">Dành cho Học viên</h3>
                    <p class="card-text">
                        Truy cập kho khóa học đa dạng, làm bài tập thực hành và nhận chứng chỉ hoàn thành.
                    </p>
                    <a href="index.php?controller=auth&action=register" class="card-link mt-auto">
                        Đăng ký học ngay <i class="fas fa-arrow-right ms-2" style="font-size: 0.8rem"></i>
                    </a>
                </div>
            </div>

            <div class="col-md-4">
                <div class="dev-card h-100">
                    <div class="card-icon-wrapper">
                        <i class="fas fa-chalkboard-teacher"></i>
                    </div>
                    <h3 class="card-title">Dành cho Giảng viên</h3>
                    <p class="card-text">
                        Công cụ quản lý lớp học mạnh mẽ. Tạo bài giảng, tải lên tài liệu và đánh giá sinh viên.
                    </p>
                </div>
            </div>

            <div class="col-md-4">
                <div class="dev-card h-100">
                    <div class="card-icon-wrapper">
                        <i class="fas fa-book-open"></i>
                    </div>
                    <h3 class="card-title">Thư viện & Tài liệu</h3>
                    <p class="card-text">
                        Kho tài liệu tham khảo mở rộng được cập nhật liên tục.
                    </p>
                    <a href="index.php?controller=course&action=index" class="card-link mt-auto">
                        Truy cập thư viện <i class="fas fa-arrow-right ms-2" style="font-size: 0.8rem"></i>
                    </a>
                </div>
            </div>
        </div>
    </div>
</section>

<?php 
// Include Footer
include_once dirname(__DIR__) . '/layouts/footer.php'; 
?>
