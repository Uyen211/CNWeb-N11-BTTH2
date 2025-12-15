<div class="container-fluid px-4 py-4">
    <h2 class="mb-4">Phê duyệt khóa học</h2>
    
    <?php if (isset($_SESSION['success'])): ?>
        <div class="alert alert-success"><?= $_SESSION['success']; unset($_SESSION['success']); ?></div>
    <?php endif; ?>

    <div class="card shadow-sm">
        <div class="card-body">
            <table class="table table-hover align-middle">
                <thead>
                    <tr>
                        <th>Khóa học</th>
                        <th>Giảng viên</th>
                        <th>Giá</th>
                        <th>Ngày tạo</th>
                        <th class="text-end">Hành động</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($pendingCourses->rowCount() > 0): ?>
                        <?php while ($row = $pendingCourses->fetch(PDO::FETCH_ASSOC)): ?>
                            <tr>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <img src="assets/uploads/courses/<?= $row['image'] ?>" width="50" class="me-2 rounded">
                                        <div>
                                            <strong><?= htmlspecialchars($row['title']) ?></strong><br>
                                            <small class="text-muted"><?= $row['category_name'] ?></small>
                                        </div>
                                    </div>
                                </td>
                                <td><?= htmlspecialchars($row['instructor_name']) ?></td>
                                <td><?= number_format($row['price']) ?> đ</td>
                                <td><?= date('d/m/Y', strtotime($row['created_at'])) ?></td>
                                <td class="text-end">
                                    <button class="btn btn-sm btn-info text-white me-1"><i class="fas fa-eye"></i></button>
                                    
                                    <form action="index.php?controller=admin&action=approve_course" method="POST" class="d-inline">
                                        <input type="hidden" name="id" value="<?= $row['id'] ?>">
                                        <button type="submit" name="action" value="approve" class="btn btn-sm btn-success" title="Duyệt">
                                            <i class="fas fa-check"></i>
                                        </button>
                                        <button type="submit" name="action" value="reject" class="btn btn-sm btn-danger" title="Từ chối" onclick="return confirm('Từ chối khóa học này?');">
                                            <i class="fas fa-times"></i>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr><td colspan="5" class="text-center">Không có yêu cầu nào đang chờ duyệt.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>