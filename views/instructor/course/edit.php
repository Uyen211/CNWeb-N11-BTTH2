<form action="/onlinecourse/index.php?controller=course&action=edit&id=<?= $course['id'] ?>" method="POST" enctype="multipart/form-data">
    <input type="hidden" name="csrf_token" value="<?= $csrf_token ?>">
    
    <div class="row mb-3">
        <div class="col-md-8">
            <label class="form-label">Tên khóa học <span class="text-danger">*</span></label>
            <input type="text" class="form-control" name="title" required value="<?= htmlspecialchars($course['title']) ?>">
        </div>
        <div class="col-md-4">
            <label class="form-label">Danh mục</label>
            <select class="form-select" name="category_id" required>
                <?php foreach($categories as $cat): ?>
                    <option value="<?= $cat['id'] ?>" <?= ($cat['id'] == $course['category_id']) ? 'selected' : '' ?>>
                        <?= htmlspecialchars($cat['name']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
    </div>

    <div class="mb-3">
        <label class="form-label">Mô tả</label>
        <textarea class="form-control" name="description" rows="3"><?= htmlspecialchars($course['description']) ?></textarea>
    </div>

    <div class="row mb-3">
        <div class="col-md-4">
            <label class="form-label">Học phí</label>
            <input type="number" class="form-control" name="price" value="<?= $course['price'] ?>" min="0">
        </div>
        <div class="col-md-4">
            <label class="form-label">Thời lượng (tuần)</label>
            <input type="number" class="form-control" name="duration_weeks" value="<?= $course['duration_weeks'] ?>" min="0">
        </div>
        <div class="col-md-4">
            <label class="form-label">Level</label>
            <select class="form-select" name="level">
                <option value="Beginner" <?= ($course['level'] == 'Beginner') ? 'selected' : '' ?>>Beginner</option>
                <option value="Intermediate" <?= ($course['level'] == 'Intermediate') ? 'selected' : '' ?>>Intermediate</option>
                <option value="Advanced" <?= ($course['level'] == 'Advanced') ? 'selected' : '' ?>>Advanced</option>
            </select>
        </div>
    </div>

    <div class="mb-3">
        <label class="form-label">Ảnh bìa (Chọn nếu muốn đổi)</label>
        <input type="file" class="form-control" name="image" accept="image/*">
        <?php if(!empty($course['image'])): ?>
            <div class="mt-2">
                <img src="assets/uploads/courses/<?= $course['image'] ?>" width="80" class="rounded border">
            </div>
        <?php endif; ?>
    </div>

    <div class="text-end">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Đóng</button>
        <button type="submit" class="btn btn-warning">Cập nhật</button>
    </div>
</form>