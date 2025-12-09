<div class="container mt-4">
    
    <nav aria-label="breadcrumb" class="mb-4">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="#">Khóa học</a></li>
            <li class="breadcrumb-item"><a href="/onlinecourse/index.php?controller=course&action=manage&id=<?= $lesson->courseId ?>">Quản lý</a></li>
            <li class="breadcrumb-item active" aria-current="page"><?= htmlspecialchars($lesson->title) ?></li>
        </ol>
    </nav>

    <div class="row">
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-body text-center bg-black rounded p-0 overflow-hidden" style="min-height: 400px; display: flex; align-items: center; justify-content: center;">
                    <?php if (!empty($lesson->videoUrl)): ?>
                        <?php 
                            $embedUrl = str_replace("watch?v=", "embed/", $lesson->videoUrl); 
                        ?>
                        <iframe width="100%" height="450" src="<?= $embedUrl ?>" frameborder="0" allowfullscreen></iframe>
                    <?php else: ?>
                        <div class="text-white">
                            <i class="fas fa-video-slash fa-3x mb-3"></i><br>
                            Không có video
                        </div>
                    <?php endif; ?>
                </div>
                <div class="card-body">
                    <h4 class="card-title"><?= htmlspecialchars($lesson->title) ?></h4>
                    <p class="card-text text-muted"><?= nl2br(htmlspecialchars($lesson->content)) ?></p>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white fw-bold text-google-blue">
                    <i class="fas fa-cloud-upload-alt me-2"></i> Upload tài liệu
                </div>
                <div class="card-body">
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

                    <form action="/onlinecourse/index.php?controller=lesson&action=storeMaterial" method="POST" enctype="multipart/form-data">
                        <input type="hidden" name="csrf_token" value="<?= $csrf_token ?>">
                        <input type="hidden" name="lesson_id" value="<?= $lesson->id ?>">
                        
                        <div class="mb-3">
                            <label class="form-label small text-muted">Chọn file (PDF, DOC, MP4)</label>
                            <input class="form-control" type="file" name="material_file" required>
                        </div>
                        <button type="submit" class="btn btn-primary w-100">Tải lên</button>
                    </form>
                </div>
            </div>

            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white fw-bold">
                    <i class="fas fa-file-alt me-2"></i> Danh sách tài liệu
                </div>
                <ul class="list-group list-group-flush">
                    <?php if ($materialsStmt->rowCount() > 0): ?>
                        <?php while ($mat = $materialsStmt->fetch(PDO::FETCH_ASSOC)): ?>
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                <div class="text-truncate" style="max-width: 200px;">
                                    <?php 
                                        $icon = 'fa-file';
                                        if($mat['file_type'] == 'pdf') $icon = 'fa-file-pdf text-danger';
                                        elseif(strpos($mat['file_type'], 'word') !== false) $icon = 'fa-file-word text-primary';
                                        elseif(strpos($mat['file_type'], 'video') !== false) $icon = 'fa-file-video text-info';
                                    ?>
                                    <i class="fas <?= $icon ?> me-2"></i>
                                    <a href="/onlinecourse/<?= $mat['file_path'] ?>" target="_blank" class="text-decoration-none text-dark" title="<?= $mat['filename'] ?>">
                                        <?= htmlspecialchars($mat['filename']) ?>
                                    </a>
                                </div>
                                <span class="badge bg-light text-dark rounded-pill">
                                    <i class="fas fa-download text-muted"></i>
                                </span>
                            </li>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <li class="list-group-item text-center text-muted small">Chưa có tài liệu nào.</li>
                    <?php endif; ?>
                </ul>
            </div>
        </div>
    </div>
</div>


<script>
    // 1. Script ẩn Alert sau 2s (như bạn yêu cầu)
    document.addEventListener('DOMContentLoaded', function () {
        var alerts = document.querySelectorAll('.alert');
        alerts.forEach(function (alert) {
            setTimeout(function () {
                var bsAlert = new bootstrap.Alert(alert);
                bsAlert.close();
            }, 2000); 
        });
    });
</script>