<div class="container mt-4">
    <div class="row justify-content-center">
        <div class="col-md-10">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                    <h5 class="mb-0 text-google-blue">Chỉnh sửa bài học</h5>
                    <a href="/onlinecourse/index.php?controller=lesson&action=showMaterials&lesson_id=<?= $lesson->id ?>" class="btn btn-sm btn-outline-secondary">
                        <i class="fas fa-folder-open"></i> Quản lý tài liệu
                    </a>
                </div>
                <div class="card-body">
                    <form action="/onlinecourse/index.php?controller=lesson&action=update" method="POST">
                        <input type="hidden" name="csrf_token" value="<?= $csrf_token ?>">
                        <input type="hidden" name="id" value="<?= $lesson->id ?>">
                        
                        <div class="mb-3">
                            <label class="form-label fw-bold">Tiêu đề bài học</label>
                            <input type="text" name="title" class="form-control" required value="<?= htmlspecialchars($lesson->title) ?>">
                        </div>

                        <div class="row">
                            <div class="col-md-8 mb-3">
                                <label class="form-label fw-bold">Video URL</label>
                                <input type="text" name="video_url" class="form-control" value="<?= htmlspecialchars($lesson->videoUrl) ?>">
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label fw-bold">Thứ tự</label>
                                <input type="number" name="order" class="form-control" value="<?= $lesson->order ?>" min="1">
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-bold">Nội dung</label>
                            <textarea name="content" class="form-control" rows="6"><?= htmlspecialchars($lesson->content) ?></textarea>
                        </div>

                        <div class="d-flex justify-content-end gap-2">
                            <a href="javascript:history.back()" class="btn btn-light">Quay lại</a>
                            <button type="submit" class="btn btn-primary">Cập nhật</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>