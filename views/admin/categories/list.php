<?php if (isset($_SESSION['success'])): ?>
    <div class="alert alert-success alert-dismissible fade show" role="alert" id="alert-success">
        <?= $_SESSION['success']; ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    <!-- Sau khi hiển thị sẽ xóa session đó luôn -->
    <?php unset($_SESSION['success']); ?>
<?php endif; ?>

<?php if (isset($_SESSION['error'])): ?>
    <div class="alert alert-danger alert-dismissible fade show" role="alert" id="alert-error">
        <?= $_SESSION['error']; ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    <?php unset($_SESSION['error']); ?>
<?php endif; ?>

<script>
    // DOMContentLoaded để đảm bảo mã chạy sau khi DOM được tải
    document.addEventListener('DOMContentLoaded', function () {
        // Chọn tất cả các phần tử có class 'alert'
        var alerts = document.querySelectorAll('.alert');

        alerts.forEach(function (alert) {
            setTimeout(function () {
                // Sử dụng Bootstrap API để đóng alert
                var bsAlert = new bootstrap.Alert(alert);
                bsAlert.close();
            }, 2000); 
        });
    });
</script>

<div class="d-flex justify-content-between mb-3">
    <h3>Quản lý Danh mục</h3>
    <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#createModal">
        Thêm danh mục
    </button>
</div>
<div class="table-responsive">
        
    <table class="table table-bordered table-hover">
        <thead>
            <tr>
                <th>#</th>
                <th>Tên danh mục</th>
                <th>Mô tả</th>
                <th>Hành động</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($categories as $cat): ?>
            <tr>
                <td><?= $cat['id'] ?></td>
                <td><?= htmlspecialchars($cat['name']) ?></td>
                <td>
                    <div class="desc-truncate" 
                        onclick="showDetail(<?= htmlspecialchars(json_encode($cat['name'])) ?>, <?= htmlspecialchars(json_encode($cat['description'])) ?>)">
                        <?= htmlspecialchars($cat['description']) ?>
                    </div>
                </td>
                <td>
                    <button class="btn btn-warning btn-sm" 
                            onclick="openEditModal(<?= $cat['id'] ?>, '<?= htmlspecialchars($cat['name']) ?>', '<?= htmlspecialchars($cat['description']) ?>')">
                        <i class="fas fa-edit"></i>
                    </button>
                    
                    <button class="btn btn-danger btn-sm" 
                            onclick="openDeleteModal(<?= $cat['id'] ?>, '<?= htmlspecialchars($cat['name']) ?>')">
                        <i class="fas fa-trash"></i>
                    </button>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<?php if ($total_pages > 1): ?>
<nav aria-label="Page navigation">
    <ul class="pagination justify-content-center mt-4">
        
        <li class="page-item <?= ($page <= 1) ? 'disabled' : '' ?>">
            <a class="page-link" href="/onlinecourse/index.php?controller=admin&action=index&page=<?= $page - 1 ?>">Trước</a>
        </li>

        <?php for ($i = 1; $i <= $total_pages; $i++): ?>
            <li class="page-item <?= ($page == $i) ? 'active' : '' ?>">
                <a class="page-link" href="/onlinecourse/index.php?controller=admin&action=index&page=<?= $i ?>"><?= $i ?></a>
            </li>
        <?php endfor; ?>

        <li class="page-item <?= ($page >= $total_pages) ? 'disabled' : '' ?>">
            <a class="page-link" href="/onlinecourse/index.php?controller=admin&action=index&page=<?= $page + 1 ?>">Sau</a>
        </li>
    </ul>
</nav>
<?php endif; ?>

<div class="modal fade" id="createModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Thêm danh mục mới</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <?php include 'views/admin/categories/create.php'; ?>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="editModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Cập nhật danh mục</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <?php include 'views/admin/categories/edit.php'; ?>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="deleteModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Xác nhận xóa</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p>Bạn có chắc chắn muốn xóa danh mục: <strong id="delete-name-display"></strong>?</p>
            </div>
            <div class="modal-footer">
                <form action="/onlinecourse/index.php?controller=admin&action=deleteCategory" method="POST">
                    <input type="hidden" name="id" id="delete-id-input">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
                    <button type="submit" class="btn btn-danger">Xóa</button>
                </form>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="detailModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered modal-lg"> <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="detail-title-display"></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p id="detail-content-display" style="white-space: pre-wrap;"></p>
            </div>
        </div>
    </div>
</div>

<script>
    // Hàm mở Modal Sửa và điền dữ liệu cũ
    function openEditModal(id, name, description) {
        document.getElementById('edit-id').value = id;
        document.getElementById('edit-name').value = name;
        document.getElementById('edit-description').value = description;
        
        var myModal = new bootstrap.Modal(document.getElementById('editModal'));
        myModal.show();
    }

    // Hàm mở Modal Xóa
    function openDeleteModal(id, name) {
        document.getElementById('delete-id-input').value = id;
        document.getElementById('delete-name-display').innerText = name;
        
        var myModal = new bootstrap.Modal(document.getElementById('deleteModal'));
        myModal.show();
    }

    // Hàm mở Modal Xem chi tiết (khi click vào mô tả)
    function showDetail(name, description) {
        document.getElementById('detail-title-display').innerText = name;
        document.getElementById('detail-content-display').innerText = description;
        
        var myModal = new bootstrap.Modal(document.getElementById('detailModal'));
        myModal.show();
    }
</script>