<?php 
// Đảm bảo đường dẫn file này nằm ở views/courses/detail.php
// dirname(__DIR__, 1) -> views/courses
// dirname(__DIR__, 2) -> views
include_once dirname(__DIR__, 2) . '/views/layouts/header.php'; 
?>

<div class="container mt-5">
    <div class="row">
        <div class="col-md-8">
            <h1 class="mb-3"><?= htmlspecialchars($course['title']) ?></h1>
            
            <?php 
                $imgSrc = !empty($course['image']) ? 'assets/uploads/courses/' . $course['image'] : 'assets/images/no-image.jpg';
            ?>
            <img src="<?= $imgSrc ?>" class="img-fluid rounded mb-4 shadow-sm" style="width:100%; height: 400px; object-fit:cover;">
            
            <h3 class="text-primary border-bottom pb-2">Mô tả khóa học</h3>
            <div class="mt-3">
                <?= nl2br(htmlspecialchars($course['description'])) ?>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card shadow border-0 rounded-3">
                <div class="card-body p-4">
                    <h3 class="text-danger fw-bold text-center mb-4">
                        <?= number_format($course['price'], 0, ',', '.') ?> VNĐ
                    </h3>
                    
                    <div class="d-grid gap-2">
                        <?php if (!isset($_SESSION['user_id'])): ?>
                            <a href="index.php?controller=auth&action=login&redirect=enroll&course_id=<?= $course['id'] ?>" 
                               class="btn btn-warning btn-lg fw-bold text-white">
                                <i class="fas fa-sign-in-alt me-2"></i> Đăng nhập để đăng ký
                            </a>

                        <?php elseif (isset($isEnrolled) && $isEnrolled): ?>
                            <div class="alert alert-success text-center">
                                <i class="fas fa-check-circle"></i> Bạn đã sở hữu khóa học này
                            </div>
                            <a href="index.php?controller=lesson&action=view&course_id=<?= $course['id'] ?>" 
                               class="btn btn-success btn-lg fw-bold">
                                <i class="fas fa-play-circle me-2"></i> Vào học ngay
                            </a>

                        <?php else: ?>
                            <a href="index.php?controller=enrollment&action=enroll&course_id=<?= $course['id'] ?>" 
                               class="btn btn-primary btn-lg fw-bold">
                                <i class="fas fa-cart-plus me-2"></i> Đăng ký học ngay
                            </a>
                        <?php endif; ?>
                    </div>
                    
                    <ul class="list-group list-group-flush mt-4">
                        <li class="list-group-item"><i class="fas fa-clock me-2 text-muted"></i> Thời lượng: <?= $course['duration_weeks'] ?? 'Unknown' ?> tuần</li>
                        <li class="list-group-item"><i class="fas fa-signal me-2 text-muted"></i> Trình độ: <?= $course['level'] ?></li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include_once dirname(__DIR__, 2) . '/views/layouts/footer.php'; ?>