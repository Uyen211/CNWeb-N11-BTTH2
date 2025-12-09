<?php
// views/instructor/lessons/manage.php
// Biến $course và $lessonsStmt được truyền từ LessonController::index()
?>

<div class="course-hub-header container-fluid px-4">
    <div class="d-flex justify-content-between align-items-start">
        <div>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-2">
                    <li class="breadcrumb-item"><a href="/onlinecourse/index.php?controller=course&action=index" class="text-decoration-none">Khóa học của tôi</a></li>
                    <li class="breadcrumb-item"><a href="/onlinecourse/index.php?controller=course&action=manage&id=<?= $course['id'] ?>" class="text-decoration-none">Quản lý khóa học</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Bài học</li>
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
             <a href="/onlinecourse/index.php?controller=course&action=manage&id=<?= $course['id'] ?>" class="btn btn-outline-primary me-2">
                <i class="fas fa-arrow-left me-1"></i> Quay lại Tổng quan
            </a>
        </div>
    </div>

    <ul class="nav nav-tabs nav-tabs-google">
        <li class="nav-item">
            <a class="nav-link" href="/onlinecourse/index.php?controller=course&action=manage&id=<?= $course['id'] ?>">Tổng quan</a>
        </li>
        <li class="nav-item">
            <a class="nav-link active" href="#">Bài học (Lessons)</a>
        </li>
        <li class="nav-item">
            <a class="nav-link" href="/onlinecourse/index.php?controller=student&action=index&course_id=<?= $course['id'] ?>">Học viên (Students)</a>
        </li>
    </ul>
</div>
<div class="container-fluid px-4 py-4">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h5 class="text-google-text-dark">Danh sách bài học</h5>
        <a href="/onlinecourse/index.php?controller=lesson&action=create&course_id=<?= $course['id']; ?>" class="btn btn-primary btn-sm">
            <i class="fas fa-plus"></i> Thêm bài học
        </a>
    </div>


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


    <div class="card shadow-sm border-0">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="bg-light">
                        <tr>
                            <th width="5%">#</th>
                            <th width="50%">Tiêu đề</th>
                            <th width="15%" class="text-center">Thứ tự</th>
                            <th width="30%" class="text-end">Hành động</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if ($lessonsStmt->rowCount() > 0): ?>
                            <?php while ($row = $lessonsStmt->fetch(PDO::FETCH_ASSOC)): ?>
                                <tr>
                                    <td><?= htmlspecialchars($row['id']) ?></td>
                                    <td>
                                        <span class="fw-medium"><?= htmlspecialchars($row['title']) ?></span>
                                        <div id="meta-<?= $row['id'] ?>" class="small text-muted mt-1 d-none"></div>
                                    </td>
                                    <td class="text-center"><?= $row['order'] ?></td>
                                    <td class="text-end">
                                        <button class="btn btn-sm btn-outline-info me-1 btn-detail" data-id="<?= $row['id'] ?>">
                                            Chi tiết
                                        </button>
                                        <a href="/onlinecourse/index.php?controller=lesson&action=edit&id=<?= $row['id'] ?>" class="btn btn-sm btn-outline-warning me-1">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        
                                        <button class="btn btn-sm btn-outline-danger" 
                                                onclick="confirmDelete(<?= $row['id'] ?>, '<?= htmlspecialchars($row['title'], ENT_QUOTES) ?>')" title="Xóa">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                        <a href="/onlinecourse/index.php?controller=lesson&action=showMaterials&lesson_id=<?= $row['id'] ?>" class="btn btn-sm btn-secondary">
                                            <i class="fas fa-folder-open"></i> Tài liệu
                                        </a>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <tr><td colspan="4" class="text-center py-4">Chưa có bài học nào.</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <?php if ($totalPages > 1): ?>
    <nav class="mt-3">
        <ul class="pagination justify-content-center">
            <?php for($i=1; $i<=$totalPages; $i++): ?>
                <li class="page-item <?= ($page == $i) ? 'active' : '' ?>">
                    <a class="page-link" href="/onlinecourse/index.php?controller=lesson&action=index&course_id=<?= $course['id'] ?>&page=<?= $i ?>"><?= $i ?></a>
                </li>
            <?php endfor; ?>
        </ul>
    </nav>
    <?php endif; ?>
</div>


<div class="modal fade" id="deleteModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title text-danger">Xác nhận xóa</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                Bạn muốn xóa: <strong id="delName"></strong>?
            </div>
            <div class="modal-footer">
                <form method="POST" action="/onlinecourse/index.php?controller=lesson&action=delete">
                    <input type="hidden" name="id" id="delId">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
                    <button type="submit" class="btn btn-danger">Xóa ngay</button>
                </form>
            </div>
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

    function confirmDelete(id, title) {
        document.getElementById('delId').value = id;
        document.getElementById('delName').innerText = title;
        new bootstrap.Modal(document.getElementById('deleteModal')).show();
    }

document.querySelectorAll('.btn-detail').forEach(btn => {
    btn.addEventListener('click', function() {
        const id = this.getAttribute('data-id');
        const metaDiv = document.getElementById('meta-' + id);
        
        if (metaDiv.classList.contains('d-none')) {
            fetch('/onlinecourse/index.php?controller=lesson&action=detail&id=' + id)
                .then(response => response.json())
                .then(res => {
                    if(res.status === 'success') {
                        metaDiv.innerHTML = `
                            <div class="mt-2 p-2 bg-light rounded border">
                                <i class="fas fa-video me-1 text-danger"></i> URL: ${res.data.video_url || 'N/A'}<br>
                                <i class="fas fa-align-justify me-1 text-secondary"></i> Nội dung: ${res.data.content ? res.data.content.substring(0, 100) + (res.data.content.length > 100 ? '...' : '') : 'N/A'}<br>
                                <i class="far fa-clock me-1 text-primary"></i> Tạo: ${res.data.created_at}<br>
                                <i class="fas fa-edit me-1 text-warning"></i> Cập nhật: ${res.data.updated_at}
                            </div>
                        `;
                        metaDiv.classList.remove('d-none');
                        this.textContent = 'Ẩn';
                    }
                });
        } else {
            metaDiv.classList.add('d-none');
            this.textContent = 'Chi tiết';
        }
    });
});
</script>