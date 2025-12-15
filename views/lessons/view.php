<?php include_once dirname(__DIR__) . '/layouts/header.php'; ?>

<div class="container-fluid py-4">
    <div class="row">
        <div class="col-lg-8">
            <?php if ($currentLesson): ?>
                <div class="ratio ratio-16x9 bg-dark rounded shadow-sm mb-4">
                    <iframe src="<?= htmlspecialchars($currentLesson['video_url']) ?>" allowfullscreen></iframe>
                </div>

                <h3 class="fw-bold"><?= htmlspecialchars($currentLesson['title']) ?></h3>
                <hr>

                <div class="card mt-4">
                    <div class="card-header bg-light fw-bold">
                        <i class="fas fa-paperclip me-2"></i> Tài liệu học tập
                    </div>
                    <ul class="list-group list-group-flush">
                        <?php if (!empty($materials)): ?>
                            <?php foreach ($materials as $file): ?>
                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                    <span>
                                        <i class="fas fa-file-pdf text-danger me-2"></i> 
                                        <?= htmlspecialchars($file['filename']) ?>
                                    </span>
                                    <a href="<?= htmlspecialchars($file['file_path']) ?>" class="btn btn-sm btn-outline-primary" download>
                                        <i class="fas fa-download"></i> Tải về
                                    </a>
                                </li>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <li class="list-group-item text-muted">Không có tài liệu đính kèm.</li>
                        <?php endif; ?>
                    </ul>
                </div>
            <?php else: ?>
                <div class="alert alert-warning">Khóa học này chưa có bài học nào.</div>
            <?php endif; ?>
        </div>

        <div class="col-lg-4">
            <div class="card shadow-sm h-100">
                <div class="card-header bg-primary text-white fw-bold">
                    Nội dung khóa học
                </div>
                <div class="list-group list-group-flush" style="max-height: 600px; overflow-y: auto;">
                    <?php foreach ($lessons as $index => $lesson): ?>
                        <?php $isActive = ($currentLesson && $lesson['id'] == $currentLesson['id']) ? 'active' : ''; ?>
                        
                        <a href="index.php?controller=lesson&action=view&course_id=<?= $course_id ?>&id=<?= $lesson['id'] ?>" 
                           class="list-group-item list-group-item-action <?= $isActive ?>">
                            <div class="d-flex w-100 justify-content-between">
                                <h6 class="mb-1">Bài <?= $index + 1 ?>: <?= htmlspecialchars($lesson['title']) ?></h6>
                            </div>
                            <small class="<?= $isActive ? 'text-white-50' : 'text-muted' ?>">
                                <i class="fas fa-play-circle me-1"></i> Video bài giảng
                            </small>
                        </a>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include_once dirname(__DIR__) . '/layouts/footer.php'; ?>