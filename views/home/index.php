<?php 
// 1. Include Header
include_once dirname(__DIR__) . '/layouts/header.php'; 
?>

<style>
    /* --- GOOGLE DEVELOPERS THEME VARIABLES --- */
    :root {
        --dev-blue: #1a73e8;           /* Màu xanh thương hiệu */
        --dev-blue-hover: #1967d2;     /* Màu xanh khi hover */
        --dev-text-head: #202124;      /* Màu tiêu đề (Đen xám) */
        --dev-text-body: #5f6368;      /* Màu nội dung (Xám) */
        --dev-border: #dadce0;         /* Màu viền (Xám nhạt) */
        --dev-bg-secondary: #f8f9fa;   /* Màu nền phụ */
    }

    body {
        font-family: 'Roboto', sans-serif;
        color: var(--dev-text-head);
        background-color: #fff;
    }

    /* --- HERO SECTION --- */
    .dev-hero {
        padding: 64px 0 48px;
        background-color: #fff;
    }

    .hero-eyebrow {
        color: var(--dev-text-body);
        font-weight: 500;
        text-transform: uppercase;
        font-size: 0.875rem;
        margin-bottom: 12px;
        letter-spacing: 0.5px;
    }

    .hero-title {
        font-size: 3.5rem; /* Google dùng chữ rất to */
        font-weight: 700;
        line-height: 1.2;
        letter-spacing: -0.5px;
        color: var(--dev-text-head);
        margin-bottom: 24px;
    }

    .hero-desc {
        font-size: 1.25rem;
        line-height: 1.6;
        color: var(--dev-text-body);
        font-weight: 300;
        max-width: 700px;
        margin-bottom: 32px;
    }

    /* Buttons chuẩn Google Dev */
    .btn-dev {
        font-family: 'Google Sans', 'Roboto', sans-serif;
        font-weight: 500;
        padding: 12px 24px;
        border-radius: 4px;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        transition: background-color 0.2s, box-shadow 0.2s;
    }

    .btn-dev-filled {
        background-color: var(--dev-blue);
        color: #fff;
        border: none;
    }

    .btn-dev-filled:hover {
        background-color: var(--dev-blue-hover);
        box-shadow: 0 1px 2px 0 rgba(60,64,67,0.3), 0 1px 3px 1px rgba(60,64,67,0.15);
        color: #fff;
    }

    .btn-dev-text {
        color: var(--dev-blue);
        background: transparent;
        margin-left: 16px;
    }

    .btn-dev-text:hover {
        background-color: rgba(26,115,232,0.04);
        color: var(--dev-blue-hover);
    }

    /* --- CARDS SECTION --- */
    .dev-section {
        padding: 48px 0;
    }

    .section-title {
        font-size: 2rem;
        font-weight: 500;
        margin-bottom: 40px;
        color: var(--dev-text-head);
    }

    /* Style cho Card giống Google */
    .dev-card {
        border: 1px solid var(--dev-border);
        border-radius: 8px;
        padding: 24px;
        height: 100%;
        background-color: #fff;
        transition: box-shadow 0.2s, border-color 0.2s;
        display: flex;
        flex-direction: column;
    }

    .dev-card:hover {
        border-color: #fff; /* Ẩn border, hiện shadow */
        box-shadow: 0 1px 2px 0 rgba(60,64,67,0.3), 0 2px 6px 2px rgba(60,64,67,0.15);
    }

    .card-icon-wrapper {
        width: 48px;
        height: 48px;
        margin-bottom: 20px;
        display: flex;
        align-items: center;
        color: var(--dev-blue);
        font-size: 2rem;
    }

    .card-title {
        font-size: 1.375rem;
        font-weight: 500;
        margin-bottom: 12px;
        color: var(--dev-text-head);
    }

    .card-text {
        color: var(--dev-text-body);
        font-size: 1rem;
        line-height: 1.5;
        margin-bottom: 24px;
        flex-grow: 1; /* Đẩy link xuống đáy */
    }

    .card-link {
        color: var(--dev-blue);
        font-weight: 500;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
    }

    .card-link:hover {
        text-decoration: none; /* Google Dev thường không gạch chân, chỉ đổi màu hoặc icon */
        color: var(--dev-blue-hover);
    }

    /* --- FEATURED / LEARNING PATHS --- */
    .path-section {
        background-color: var(--dev-bg-secondary);
        padding: 60px 0;
        border-top: 1px solid var(--dev-border);
    }
    
    .path-item {
        display: flex;
        gap: 20px;
        margin-bottom: 30px;
    }

    .path-icon {
        font-size: 1.5rem;
        color: var(--dev-text-body);
        margin-top: 4px;
    }

    /* Responsive */
    @media (max-width: 768px) {
        .hero-title { font-size: 2.5rem; }
        .btn-dev-text { margin-left: 0; margin-top: 10px; display: block; }
    }
</style>

<section class="dev-hero">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-lg-7">
                <div class="hero-eyebrow">EduPlatform for Students & Instructors</div>
                <h1 class="hero-title">Xây dựng kiến thức,<br>phát triển tương lai</h1>
                <p class="hero-desc">
                    Hệ thống quản lý học tập toàn diện. Truy cập tài liệu, tham gia khóa học và theo dõi lộ trình phát triển kỹ năng của bạn với các công cụ mới nhất.
                </p>
                <div class="d-flex flex-wrap align-items-center">
                    <?php if (!isset($_SESSION['user'])): ?>
                        <a href="/onlinecourse/views/auth/register.php" class="btn-dev btn-dev-filled">
                            Bắt đầu ngay
                        </a>
                        <a href="/onlinecourse/views/auth/login.php" class="btn-dev btn-dev-text">
                            Đăng nhập <i class="fas fa-arrow-right ms-2" style="font-size: 0.8rem"></i>
                        </a>
                    <?php else: ?>
                        <a href="/onlinecourse/views/student/dashboard.php" class="btn-dev btn-dev-filled">
                            Đến Dashboard
                        </a>
                    <?php endif; ?>
                </div>
            </div>
            
            <div class="col-lg-5 d-none d-lg-block text-center">
                 

[Image of data science charts and python logo]

                <div style="position: relative; height: 300px; width: 100%;">
                    <i class="fas fa-cubes" style="position: absolute; top: 20%; right: 10%; font-size: 8rem; color: #e8f0fe;"></i>
                    <i class="fas fa-code-branch" style="position: absolute; bottom: 20%; left: 10%; font-size: 6rem; color: #fce8e6;"></i>
                    <i class="fas fa-layer-group" style="position: absolute; top: 30%; left: 30%; font-size: 10rem; color: rgba(26,115,232,0.1);"></i>
                </div>
            </div>
        </div>
    </div>
</section>

<section class="dev-section">
    <div class="container">
        <h2 class="section-title">Khám phá theo vai trò</h2>
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