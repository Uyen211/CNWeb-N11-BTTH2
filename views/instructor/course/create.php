<form action="/onlinecourse/index.php?controller=course&action=create" method="POST" enctype="multipart/form-data">
    <input type="hidden" name="csrf_token" value="<?= $csrf_token ?>">
    
    <div class="row mb-3">
        <div class="col-md-8">
            <label class="form-label">Tên khóa học <span class="text-danger">*</span></label>
            <input type="text" class="form-control" name="title" required>
        </div>
        <div class="col-md-4">
            <label class="form-label">Danh mục</label>
            <select class="form-select" name="category_id" required>
                <?php foreach($categories as $cat): ?>
                    <option value="<?= $cat['id'] ?>"><?= htmlspecialchars($cat['name']) ?></option>
                <?php endforeach; ?>
            </select>
        </div>
    </div>

    <div class="mb-3">
        <label class="form-label">Mô tả</label>
        <textarea class="form-control" name="description" rows="3"></textarea>
    </div>

    <div class="row mb-3">
        <div class="col-md-4">
            <label class="form-label">Học phí</label>
            <input type="number" class="form-control" name="price" value="0" min="0">
        </div>
        <div class="col-md-4">
            <label class="form-label">Thời lượng (tuần)</label>
            <input type="number" class="form-control" name="duration_weeks" value="4" min="0">
        </div>
        <div class="col-md-4">
            <label class="form-label">Level</label>
            <select class="form-select" name="level">
                <option value="Beginner">Beginner</option>
                <option value="Intermediate">Intermediate</option>
                <option value="Advanced">Advanced</option>
            </select>
        </div>
    </div>

    <div class="mb-3">
        <label class="form-label">Ảnh bìa <span class="text-danger">*</span></label>
        <input type="file" class="form-control" name="image" required accept="image/*">
    </div>

    <div class="text-end">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Đóng</button>
        <button type="submit" class="btn btn-primary">Lưu khóa học</button>
    </div>
</form>