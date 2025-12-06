<form action="/onlinecourse/index.php?controller=admin&action=updateCategory" method="POST" id="form-edit-category">
    <input type="hidden" name="id" id="edit-id"> <div class="mb-3">
        <label class="form-label">Tên danh mục</label>
        <input type="text" name="name" id="edit-name" class="form-control" required>
    </div>
    <div class="mb-3">
        <label class="form-label">Mô tả</label>
        <textarea name="description" id="edit-description" class="form-control" rows="4"></textarea>
    </div>
    <div class="text-end">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Đóng</button>
        <button type="submit" class="btn btn-primary">Lưu thay đổi</button>
    </div>
</form>