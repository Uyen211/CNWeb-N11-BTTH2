<?php include 'views/layouts/header.php'; ?>
<?php include 'views/layouts/sidebar.php'; ?>

<div class="main-content" style="margin-left: 250px;">
    <div class="container-fluid p-4 position-relative">
        
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h2 class="fw-bold mb-0">Bảng quản trị</h2>
                <p class="text-muted">Quản lý nội dung và theo dõi học viên.</p>
            </div>
            <a href="index.php?controller=instructor&action=create" class="btn btn-primary btn-lg rounded-pill px-4 shadow-sm">
                <i class="bi bi-plus-lg me-2"></i> Tạo khóa học mới
            </a>
        </div>

        <div class="row g-4 mb-5">
            <div class="col-md-6 col-lg-3">
                <div class="p-3 bg-white rounded-4 shadow-sm d-flex align-items-center">
                    <div class="bg-primary bg-opacity-10 p-3 rounded-circle text-primary me-3">
                        <i class="bi bi-collection-play-fill fs-4"></i>
                    </div>
                    <div>
                        <h4 class="fw-bold mb-0"><?php echo count($courses); ?></h4>
                        <small class="text-muted">Tổng khóa học</small>
                    </div>
                </div>
            </div>
            </div>

        <div class="card border-0 shadow-sm rounded-4">
            <div class="card-header bg-white border-0 py-3 d-flex justify-content-between align-items-center">
                <h5 class="fw-bold mb-0">Khóa học của bạn</h5>
                <button class="btn btn-sm btn-light rounded-circle"><i class="bi bi-three-dots-vertical"></i></button>
            </div>
            <div class="card-body p-0">
                <?php if (empty($courses)): ?>
                    <div class="text-center py-5">
                        <p class="text-muted">Bạn chưa tạo khóa học nào.</p>
                    </div>
                <?php else: ?>
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="bg-light">
                                <tr>
                                    <th class="ps-4" style="width: 40%">Tên khóa học</th>
                                    <th>Giá</th>
                                    <th>Trạng thái</th>
                                    <th>Hành động</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($courses as $course): ?>
                                <tr>
                                    <td class="ps-4">
                                        <div class="d-flex align-items-center">
                                            <img src="assets/uploads/courses/<?php echo $course['image'] ?: 'default.jpg'; ?>" 
                                                 class="rounded-3 object-fit-cover me-3" width="60" height="40">
                                            <div>
                                                <div class="fw-bold text-dark"><?php echo $course['title']; ?></div>
                                                <small class="text-muted">ID: #<?php echo $course['id']; ?></small>
                                            </div>
                                        </div>
                                    </td>
                                    <td><?php echo number_format($course['price']); ?> đ</td>
                                    <td><span class="badge bg-success bg-opacity-10 text-success rounded-pill px-3">Hoạt động</span></td>
                                    <td>
                                        <div class="btn-group">
                                            <a href="index.php?controller=lesson&action=manage&course_id=<?php echo $course['id']; ?>" 
                                               class="btn btn-light btn-sm text-dark" title="Quản lý bài học">
                                                <i class="bi bi-layers-fill"></i>
                                            </a>
                                            <a href="index.php?controller=instructor&action=edit&id=<?php echo $course['id']; ?>" 
                                               class="btn btn-light btn-sm text-primary" title="Sửa">
                                                <i class="bi bi-pencil-square"></i>
                                            </a>
                                            <a href="index.php?controller=instructor&action=delete&id=<?php echo $course['id']; ?>" 
                                               class="btn btn-light btn-sm text-danger" onclick="return confirm('Xóa khóa này?')" title="Xóa">
                                                <i class="bi bi-trash"></i>
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>
<?php include 'views/layouts/footer.php'; ?>