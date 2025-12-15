<div class="main-content container-fluid p-4">
    
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

    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="page-title mb-0">Quản lý Khóa học</h1>
        <button type="button" class="btn btn-primary" onclick="openCreateModal()">
            <i class="fas fa-plus me-1"></i> Tạo khóa học
        </button>
    </div>

    <div class="card shadow-sm">
        <div class="table-responsive">
            <table class="table table-hover mb-0 align-middle">
                <thead class="bg-light text-nowrap">
                    <tr>
                        <th style="width: 5%;">ID</th>
                        <th style="width: 10%;">Hình ảnh</th>
                        <th style="width: 20%;">Tên khóa học</th>
                        <th style="width: 15%;">Danh mục</th> <th style="width: 25%;">Mô tả</th>    <th style="width: 10%;">Giá</th>
                        <th style="width: 15%;" class="text-end">Hành động</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (isset($courses) && $totalCourses > 0): ?>
                        <?php while ($row = $courses->fetch(PDO::FETCH_ASSOC)): ?>
                            <tr style="cursor: pointer;">
                                <td><?= $row['id'] ?></td>
                                <td>
                                    <img src="assets/uploads/courses/<?= !empty($row['image']) ? $row['image'] : 'default.png' ?>" 
                                         class="rounded border" width="60" height="40" style="object-fit: cover;">
                                </td>
                                
                                <td>
                                    <a href="#" onclick="openDetailModal(<?= $row['id'] ?>); return false;" class="fw-bold text-decoration-none text-dark">
                                        <?= htmlspecialchars($row['title']) ?>
                                    </a>
                                </td>

                                <td><span class="badge bg-info text-dark"><?= htmlspecialchars($row['category_name']) ?></span></td>
                                
                                <td>
                                    <small class="text-muted">
                                        <?= mb_strimwidth(htmlspecialchars(strip_tags($row['description'])), 0, 50, "...") ?>
                                    </small>
                                </td>

                                <td class="fw-bold text-primary"><?= number_format($row['price'], 0, ',', '.') ?> đ</td>
                                
                                <td class="text-end text-nowrap">
                               
                                    <button class="btn btn-sm btn-outline-warning me-1" 
                                            onclick="openEditModal(<?= $row['id'] ?>)" title="Sửa">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    
                                    <button class="btn btn-sm btn-outline-danger" 
                                            onclick="confirmDelete(<?= $row['id'] ?>, '<?= htmlspecialchars($row['title'], ENT_QUOTES) ?>')" title="Xóa">
                                        <i class="fas fa-trash"></i>
                                    </button>

                                    <a href="/onlinecourse/index.php?controller=course&action=manage&id=<?= $row['id'] ?>" 
                                    class="btn btn-sm btn-outline-primary me-1" 
                                    title="Quản lý chi tiết">
                                        <i class="fas fa-cog"></i> Quản lý
                                    </a>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr><td colspan="7" class="text-center py-4">Chưa có khóa học nào.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
        </div>
    </div>
</div>

<div class="modal fade" id="courseModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="courseModalLabel">Thông tin khóa học</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body" id="courseModalBody">
                <div class="text-center py-4"><i class="fas fa-spinner fa-spin fa-2x"></i></div>
            </div>
        </div>
    </div>
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
                <form method="POST" action="/onlinecourse/index.php?controller=course&action=delete">
                    <input type="hidden" name="id" id="delId">
                    <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?? '' ?>">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
                    <button type="submit" class="btn btn-danger">Xóa ngay</button>
                </form>
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

    // 2. Hàm mở Popup Create
    function openCreateModal() {
        var modalEl = document.getElementById('courseModal');
        var modal = new bootstrap.Modal(modalEl);
        document.getElementById('courseModalLabel').innerText = "Tạo Khóa Học Mới";
        
        // Gọi AJAX lấy nội dung form create
        fetch('/onlinecourse/index.php?controller=course&action=create')
            .then(response => response.text())
            .then(html => {
                document.getElementById('courseModalBody').innerHTML = html;
                modal.show();
            })
            .catch(err => console.error('Lỗi load form:', err));
    }

    // 3. Hàm mở Popup Edit
    function openEditModal(id) {
        var modalEl = document.getElementById('courseModal');
        var modal = new bootstrap.Modal(modalEl);
        document.getElementById('courseModalLabel').innerText = "Chỉnh sửa Khóa Học";
        
        // Gọi AJAX lấy nội dung form edit (kèm ID)
        fetch('/onlinecourse/index.php?controller=course&action=edit&id=' + id)
            .then(response => response.text())
            .then(html => {
                document.getElementById('courseModalBody').innerHTML = html;
                modal.show();
            })
            .catch(err => console.error('Lỗi load form:', err));
    }

    // 4. Hàm mở Popup Delete
    function confirmDelete(id, title) {
        document.getElementById('delId').value = id;
        document.getElementById('delName').innerText = title;
        new bootstrap.Modal(document.getElementById('deleteModal')).show();
    }
</script>