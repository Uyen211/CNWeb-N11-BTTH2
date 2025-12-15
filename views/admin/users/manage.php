<?php 
// views/admin/users/manage.php 
?>

<div class="container-fluid px-4 py-4">
    <?php if (isset($_SESSION['success'])): ?>
        <div class="alert alert-success alert-dismissible fade show"><?= $_SESSION['success']; ?><button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>
        <?php unset($_SESSION['success']); ?>
    <?php endif; ?>
    <?php if (isset($_SESSION['error'])): ?>
        <div class="alert alert-danger alert-dismissible fade show"><?= $_SESSION['error']; ?><button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>
        <?php unset($_SESSION['error']); ?>
    <?php endif; ?>

    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="fw-bold">Quản lý Người Dùng</h2>
    </div>

    <div class="card shadow-sm border-0 mb-4">
        <div class="card-body">
            <form action="index.php" method="GET" class="row g-2">
                <input type="hidden" name="controller" value="admin">
                <input type="hidden" name="action" value="users">
                <div class="col-md-4">
                    <div class="input-group">
                        <span class="input-group-text bg-white"><i class="fas fa-search"></i></span>
                        <input type="text" name="search" class="form-control" placeholder="Tìm theo tên hoặc email..." value="<?= htmlspecialchars($keyword) ?>">
                    </div>
                </div>
                <div class="col-md-2">
                    <button type="submit" class="btn btn-primary w-100">Tìm kiếm</button>
                </div>
            </form>
        </div>
    </div>

    <div class="card shadow-sm border-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle table-user mb-0">
                <thead class="bg-light">
                    <tr>
                        <th>ID</th>
                        <th>Thông tin</th>
                        <th>Vai trò</th>
                        <th>Ngày tham gia</th>
                        <th>Trạng thái</th>
                        <th class="text-end">Hành động</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($totalUsers > 0): ?>
                        <?php while ($row = $users->fetch(PDO::FETCH_ASSOC)): ?>
                            <tr>
                                <td>#<?= $row['id'] ?></td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="rounded-circle bg-secondary d-flex align-items-center justify-content-center text-white me-2" style="width: 32px; height: 32px;">
                                            <?= strtoupper(substr($row['fullname'], 0, 1)) ?>
                                        </div>
                                        <div>
                                            <div class="fw-bold"><?= htmlspecialchars($row['fullname']) ?></div>
                                            <div class="text-muted small"><?= htmlspecialchars($row['email']) ?></div>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <?php if($row['role'] == 2): ?>
                                        <span class="badge bg-danger">Admin</span>
                                    <?php elseif($row['role'] == 1): ?>
                                        <span class="badge bg-primary">Giảng viên</span>
                                    <?php else: ?>
                                        <span class="badge bg-secondary">Học viên</span>
                                    <?php endif; ?>
                                </td>
                                <td><?= date('d/m/Y', strtotime($row['created_at'])) ?></td>
                                <td>
                                    <?php if($row['is_active'] == 1): ?>
                                        <span class="badge bg-success bg-opacity-10 text-success px-3">Active</span>
                                    <?php else: ?>
                                        <span class="badge bg-danger bg-opacity-10 text-danger px-3">Inactive</span>
                                    <?php endif; ?>
                                </td>
                                <td class="text-end">
                                    <?php if($row['id'] != $_SESSION['user']['id']): ?>
                                        <form method="POST" action="index.php?controller=admin&action=toggle_status" class="d-inline">
                                            <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?? '' ?>">
                                            <input type="hidden" name="id" value="<?= $row['id'] ?>">
                                            
                                            <?php if($row['is_active'] == 1): ?>
                                                <input type="hidden" name="status" value="0">
                                                <button type="submit" class="btn btn-sm btn-outline-danger" title="Vô hiệu hóa" onclick="return confirm('Bạn có chắc muốn khóa tài khoản này?')">
                                                    <i class="fas fa-lock"></i>
                                                </button>
                                            <?php else: ?>
                                                <input type="hidden" name="status" value="1">
                                                <button type="submit" class="btn btn-sm btn-outline-success" title="Kích hoạt" onclick="return confirm('Kích hoạt lại tài khoản này?')">
                                                    <i class="fas fa-unlock"></i>
                                                </button>
                                            <?php endif; ?>
                                        </form>
                                    <?php else: ?>
                                        <small class="text-muted">Current</small>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr><td colspan="6" class="text-center py-4">Không tìm thấy người dùng.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
        
        <?php if ($totalPages > 1): ?>
        <div class="card-footer bg-white d-flex justify-content-end py-3">
            <nav>
                <ul class="pagination mb-0">
                    <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                        <li class="page-item <?= ($i == $page) ? 'active' : '' ?>">
                            <a class="page-link" href="index.php?controller=admin&action=users&page=<?= $i ?>&search=<?= urlencode($keyword) ?>">
                                <?= $i ?>
                            </a>
                        </li>
                    <?php endfor; ?>
                </ul>
            </nav>
        </div>
        <?php endif; ?>
    </div>
</div>