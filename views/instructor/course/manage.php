<?php
// views/instructor/course/manage.php
?>

<div class="course-hub-header container-fluid px-4">
    <div class="d-flex justify-content-between align-items-start">
        <div>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-2">
                    <li class="breadcrumb-item"><a href="/onlinecourse/index.php?controller=course&action=index" class="text-decoration-none">Khóa học của tôi</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Quản lý khóa học</li>
                </ol>
            </nav>
            <h1 class="course-title mb-2">
                <?= htmlspecialchars($course['title']) ?> 
                <span class="badge bg-success fs-6 align-middle ms-2">Active</span>
            </h1>
            <div class="course-meta mb-3">
                <span class="me-3"><i class="far fa-clock me-1"></i> <?= $course['duration_weeks'] ?> Tuần</span>
                <span class="me-3"><i class="fas fa-layer-group me-1"></i> <?= $course['level'] ?></span>
                <span class="me-3"><i class="fas fa-tag me-1"></i> <?= number_format($course['price']) ?> đ</span>
            </div>
        </div>
        <div class="d-flex mt-4">
            <button class="btn btn-primary me-2" onclick="openEditModal(<?= $course['id'] ?>)">
                <i class="fas fa-edit me-1"></i> Chỉnh sửa
            </button>
        </div>
    </div>

    <ul class="nav nav-tabs nav-tabs-google">
        <li class="nav-item">
            <a class="nav-link active" href="/onlinecourse/index.php?controller=course&action=manage&id=<?= $course['id'] ?>">Tổng quan</a>
        </li>
        <li class="nav-item">
            <a class="nav-link" href="/onlinecourse/index.php?controller=lesson&action=index&course_id=<?= $course['id'] ?>">Bài học (Lessons)</a>
        </li>
        <li class="nav-item">
            <a class="nav-link" href="/onlinecourse/index.php?controller=enrollment&action=index&course_id=<?= $course['id'] ?>">Học viên (Students)</a>
        </li>
    </ul>
</div>

<div class="container-fluid px-4 py-4">
    <div class="row">
        <div class="col-lg-8">
            <div class="row mb-4">
                <?php if (isset($_SESSION['success'])): ?>
                <div class="alert alert-success alert-dismissible fade show" role="alert" id="alert-success">
                    <?= $_SESSION['success']; ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
                <?php unset($_SESSION['success']); ?>
                <?php endif; ?>

                <?php if (isset($_SESSION['error'])): ?>
                <div class="alert alert-danger alert-dismissible fade show" role="alert" id="alert-error">
                    <?= $_SESSION['error']; ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
                <?php unset($_SESSION['error']); ?>
                <?php endif; ?>
                <div class="col-md-4">
                    <div class="card shadow-sm stat-card h-100">
                        <div class="card-body">
                            <h6 class="text-muted text-uppercase small fw-bold">Tổng học viên</h6>
                            <h3 class="mb-0 text-primary"><i class="fas fa-users me-2 opacity-50"></i><?= $totalStudents ?? 0 ?></h3>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card shadow-sm stat-card h-100" style="border-left-color: #34a853;">
                        <div class="card-body">
                            <h6 class="text-muted text-uppercase small fw-bold">Doanh thu</h6>
                            <h3 class="mb-0 text-success"><?= number_format($totalRevenue ?? 0, 0, ',', '.') ?> đ</h3>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card shadow-sm stat-card h-100" style="border-left-color: #fbbc04;">
                        <div class="card-body">
                            <h6 class="text-muted text-uppercase small fw-bold">Tiến độ TB</h6>
                            <h3 class="mb-0 text-warning"><?= $avgProgress ?? 0 ?>%</h3>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card shadow-sm mb-4">
                <div class="card-header bg-white py-3"><h5 class="mb-0 text-dark">Mô tả khóa học</h5></div>
                <div class="card-body">
                    <div class="text-muted" style="white-space: pre-line;">
                        <?= htmlspecialchars_decode($course['description']) ?>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card shadow-sm mb-4">
                <div class="card-body p-3">
                    <img src="assets/uploads/courses/<?= !empty($course['image']) ? $course['image'] : 'default.png' ?>" class="course-thumbnail-hub mb-3" alt="Course Image">
                    <div class="mb-3"><div class="info-label">Danh mục</div><div class="info-value"><?= htmlspecialchars($course['category_name'] ?? 'Chưa phân loại') ?></div></div>
                    <div class="mb-3"><div class="info-label">Ngày tạo</div><div class="info-value"><?= date('d/m/Y', strtotime($course['created_at'])) ?></div></div>
                    <div class="mb-3"><div class="info-label">Cập nhật</div><div class="info-value"><?= date('d/m/Y', strtotime($course['updated_at'])) ?></div></div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="courseModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="courseModalLabel">Thông tin khóa học</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body" id="courseModalBody"><div class="text-center py-4"><i class="fas fa-spinner fa-spin fa-2x"></i></div></div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        var alerts = document.querySelectorAll('.alert');
        alerts.forEach(function (alert) {
            setTimeout(function () {
                var bsAlert = new bootstrap.Alert(alert);
                bsAlert.close();
            }, 2000); 
        });
    });

    function openEditModal(id) {
        var modal = new bootstrap.Modal(document.getElementById('courseModal'));
        document.getElementById('courseModalLabel').innerText = "Chỉnh sửa Khóa Học";
        fetch('/onlinecourse/index.php?controller=course&action=edit&id=' + id)
            .then(r => r.text())
            .then(h => { document.getElementById('courseModalBody').innerHTML = h; modal.show(); });
    }
</script>