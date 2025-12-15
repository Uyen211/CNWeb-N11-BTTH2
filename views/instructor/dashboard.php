<?php 
// views/instructor/dashboard.php 
?>

<div class="container-fluid px-4 py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="fw-bold text-dark">Xin chào, <?= htmlspecialchars($_SESSION['user']['fullname']) ?>! 👋</h2>
            <p class="text-muted">Đây là tổng quan tình hình giảng dạy của bạn hôm nay.</p>
        </div>
    </div>

    <div class="row mb-4">
        <div class="col-xl-4 col-md-6 mb-4">
            <div class="card border-0 shadow-sm h-100 py-2" style="border-left: 5px solid #34a853;">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">Tổng doanh thu</div>
                            <div class="h3 mb-0 font-weight-bold text-gray-800"><?= number_format($totalRevenue, 0, ',', '.') ?> đ</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-dollar-sign fa-2x text-gray-300 opacity-25"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-4 col-md-6 mb-4">
            <div class="card border-0 shadow-sm h-100 py-2" style="border-left: 5px solid #4285f4;">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Tổng lượt đăng ký</div>
                            <div class="h3 mb-0 font-weight-bold text-gray-800"><?= $totalStudents ?></div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-users fa-2x text-gray-300 opacity-25"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-4 col-md-6 mb-4">
            <div class="card border-0 shadow-sm h-100 py-2" style="border-left: 5px solid #fbbc04;">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">Khóa học của bạn</div>
                            <div class="h3 mb-0 font-weight-bold text-gray-800"><?= $totalCourses ?></div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-graduation-cap fa-2x text-gray-300 opacity-25"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-8 mb-4">
            <div class="card shadow-sm mb-4">
                <div class="card-header py-3 bg-white d-flex justify-content-between align-items-center">
                    <h6 class="m-0 font-weight-bold text-primary">Học viên mới đăng ký</h6>
                    <a href="#" class="btn btn-sm btn-link text-decoration-none">Xem tất cả</a>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="bg-light">
                                <tr>
                                    <th class="ps-4">Học viên</th>
                                    <th>Khóa học đăng ký</th>
                                    <th>Thời gian</th>
                                    <th class="text-end pe-4">Chi tiết</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if ($recentActivities->rowCount() > 0): ?>
                                    <?php while ($act = $recentActivities->fetch(PDO::FETCH_ASSOC)): ?>
                                        <tr>
                                            <td class="ps-4">
                                                <div class="fw-bold"><?= htmlspecialchars($act['student_name']) ?></div>
                                            </td>
                                            <td>
                                                <span class="badge bg-light text-dark border">
                                                    <?= htmlspecialchars($act['course_title']) ?>
                                                </span>
                                            </td>
                                            <td class="text-muted small">
                                                <?= date('d/m/Y H:i', strtotime($act['enrolled_date'])) ?>
                                            </td>
                                            <td class="text-end pe-4">
                                                <a href="/onlinecourse/index.php?controller=enrollment&action=index&course_id=<?= $act['course_id'] ?>" 
                                                   class="btn btn-sm btn-outline-primary rounded-pill">
                                                    Xem <i class="fas fa-arrow-right ms-1"></i>
                                                </a>
                                            </td>
                                        </tr>
                                    <?php endwhile; ?>
                                <?php else: ?>
                                    <tr><td colspan="4" class="text-center py-4 text-muted">Chưa có hoạt động mới nào.</td></tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-4 mb-4">
            <div class="card shadow-sm mb-4">
                <div class="card-header py-3 bg-white">
                    <h6 class="m-0 font-weight-bold text-dark">Khóa học nổi bật</h6>
                </div>
                <div class="card-body">
                    <?php if ($topCourses->rowCount() > 0): ?>
                        <?php while ($top = $topCourses->fetch(PDO::FETCH_ASSOC)): ?>
                            <div class="d-flex align-items-center mb-3 pb-3 border-bottom last-no-border">
                                <img src="assets/uploads/courses/<?= !empty($top['image']) ? $top['image'] : 'default.png' ?>" 
                                     class="rounded me-3" width="60" height="40" style="object-fit: cover;">
                                <div class="flex-grow-1 overflow-hidden">
                                    <h6 class="mb-0 text-truncate" title="<?= htmlspecialchars($top['title']) ?>">
                                        <?= htmlspecialchars($top['title']) ?>
                                    </h6>
                                    <small class="text-muted"><?= $top['student_count'] ?> học viên</small>
                                </div>
                                <a href="/onlinecourse/index.php?controller=course&action=manage&id=<?= $top['id'] ?>" 
                                   class="btn btn-sm btn-light text-primary" title="Vào trang quản lý">
                                    <i class="fas fa-cog"></i>
                                </a>
                            </div>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <p class="text-muted text-center py-3">Chưa có dữ liệu.</p>
                    <?php endif; ?>
                    
                    <div class="text-center mt-3">
                        <a href="/onlinecourse/index.php?controller=course&action=index" class="btn btn-block btn-outline-secondary w-100">
                            Xem tất cả khóa học
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .last-no-border:last-child { border-bottom: none !important; margin-bottom: 0 !important; padding-bottom: 0 !important; }
</style>