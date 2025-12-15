

<div class="container-fluid px-4 py-4">
    <h2 class="fw-bold mb-4">Quản Trị</h2>

    <div class="row g-4">
        <div class="col-md-3">
            <div class="card stat-card-admin h-100 p-3">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="text-muted mb-1">Tổng Học Viên</h6>
                        <h3 class="mb-0 fw-bold"><?= $stats['students'] ?></h3>
                    </div>
                    <div class="icon-box bg-light-primary">
                        <i class="fas fa-user-graduate"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card stat-card-admin h-100 p-3">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="text-muted mb-1">Tổng Giảng Viên</h6>
                        <h3 class="mb-0 fw-bold"><?= $stats['instructors'] ?></h3>
                    </div>
                    <div class="icon-box bg-light-success">
                        <i class="fas fa-chalkboard-teacher"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card stat-card-admin h-100 p-3">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="text-muted mb-1">Tổng Khóa Học</h6>
                        <h3 class="mb-0 fw-bold"><?= $stats['courses'] ?></h3>
                    </div>
                    <div class="icon-box bg-light-warning">
                        <i class="fas fa-book-open"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card stat-card-admin h-100 p-3">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="text-muted mb-1">Lượt Đăng Ký Active</h6>
                        <h3 class="mb-0 fw-bold"><?= $stats['enrollments'] ?></h3>
                    </div>
                    <div class="icon-box bg-light-danger">
                        <i class="fas fa-file-signature"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row mt-5">
        <div class="col-md-6">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-white py-3">
                    <h5 class="mb-0">Hành động nhanh</h5>
                </div>
                <div class="card-body">
                    <a href="index.php?controller=admin&action=users" class="btn btn-outline-primary mb-2 me-2">
                        <i class="fas fa-users-cog me-2"></i>Quản lý người dùng
                    </a>
                    <a href="#" class="btn btn-outline-success mb-2 me-2">
                        <i class="fas fa-check-double me-2"></i>Phê duyệt khóa học 
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>