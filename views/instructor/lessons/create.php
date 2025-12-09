<div class="container mt-4">
    <div class="row justify-content-center">
        <div class="col-md-10">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white py-3">
                    <h5 class="mb-0 text-google-blue">Thêm bài học mới</h5>
                </div>
                <div class="card-body">
                    <form action="/onlinecourse/index.php?controller=lesson&action=store" method="POST">
                        <input type="hidden" name="csrf_token" value="<?= $csrf_token ?>">
                        <input type="hidden" name="course_id" value="<?= isset($_GET['course_id']) ? $_GET['course_id'] : '' ?>">
                        
                        <div class="mb-3">
                            <label class="form-label fw-bold">Tiêu đề bài học</label>
                            <input type="text" name="title" class="form-control" required placeholder="VD: Nhập môn lập trình...">
                        </div>

                        <div class="row">
                            <div class="col-md-8 mb-3">
                                <label class="form-label fw-bold">Video URL (Youtube/Vimeo)</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-white"><i class="fab fa-youtube text-danger"></i></span>
                                    <input type="url" name="video_url" class="form-control" placeholder="https://...">
                                </div>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label fw-bold">Thứ tự hiển thị</label>
                                <input type="number" name="order" class="form-control" value="1" min="1">
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-bold">Nội dung bài học</label>
                            <textarea name="content" class="form-control" rows="6" placeholder="Mô tả nội dung bài học..."></textarea>
                        </div>

                        <div class="d-flex justify-content-end gap-2">
                            <a href="<?= $_SERVER['HTTP_REFERER'] ?? '/instructor/dashboard.php' ?>" class="btn btn-light">Hủy</a>
                            <button type="submit" class="btn btn-primary">Lưu bài học</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>