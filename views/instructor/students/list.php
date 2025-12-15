<?php 
// views/instructor/students/list.php 
?>

<div class="course-hub-header container-fluid px-4">
    <div class="d-flex justify-content-between align-items-start">
        <div>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-2">
                    <li class="breadcrumb-item"><a href="/onlinecourse/index.php?controller=course&action=instructor_course" class="text-decoration-none">Khóa học của tôi</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Quản lý học viên</li>
                </ol>
            </nav>
            <h1 class="course-title mb-2">
                <?= htmlspecialchars($course['title']) ?> 
                <span class="badge bg-success fs-6 align-middle ms-2">Active</span>
            </h1>
            <div class="course-meta mb-3">
                <span class="me-3"><i class="far fa-clock me-1"></i> <?= $course['duration_weeks'] ?> Tuần</span>
                <span class="me-3"><i class="fas fa-layer-group me-1"></i> <?= $course['level'] ?></span>
            </div>
        </div>
        </div>

    <ul class="nav nav-tabs nav-tabs-google">
        <li class="nav-item">
            <a class="nav-link" href="/onlinecourse/index.php?controller=course&action=manage&id=<?= $course['id'] ?>">Tổng quan</a>
        </li>
        <li class="nav-item">
            <a class="nav-link" href="/onlinecourse/index.php?controller=lesson&action=instructor_lesson&course_id=<?= $course['id'] ?>">Bài học (Lessons)</a>
        </li>
        <li class="nav-item">
            <a class="nav-link active" href="/onlinecourse/index.php?controller=enrollment&action=instructor_enrollment&course_id=<?= $course['id'] ?>">Học viên (Students)</a>
        </li>
    </ul>
</div>
<div class="container-fluid px-4 py-4">
    
    <div class="d-flex justify-content-between mb-3">
        <h5 class="mb-0 align-self-center">Danh sách học viên (<?= $totalStudents ?>)</h5>
        
    </div>

    <div class="card shadow-sm border-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="bg-light">
                    <tr>
                        <th class="ps-4">Học viên</th>
                        <th>Ngày tham gia</th>
                        <th>Tiến độ</th>
                        <th>Trạng thái</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($totalStudents > 0): ?>
                        <?php while ($row = $students->fetch(PDO::FETCH_ASSOC)): ?>
                            <tr>
                                <td class="ps-4">
                                    <div class="d-flex align-items-center">
                                        <div class="rounded-circle bg-secondary d-flex align-items-center justify-content-center text-white me-3" style="width: 40px; height: 40px; font-size: 14px;">
                                            <?= strtoupper(substr($row['fullname'], 0, 1)) ?>
                                        </div>
                                        <div>
                                            <div class="fw-bold text-dark"><?= htmlspecialchars($row['fullname']) ?></div>
                                            <div class="text-muted small"><?= htmlspecialchars($row['email']) ?></div>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <span class="text-muted small"><i class="far fa-calendar-alt me-1"></i><?= date('d/m/Y', strtotime($row['enrolled_date'])) ?></span>
                                </td>
                                <td style="width: 20%;">
                                    <div class="d-flex align-items-center">
                                        <div class="progress flex-grow-1" style="height: 6px;">
                                            <div class="progress-bar bg-success" role="progressbar" 
                                                 style="width: <?= $row['progress'] ?>%" 
                                                 aria-valuenow="<?= $row['progress'] ?>" aria-valuemin="0" aria-valuemax="100">
                                            </div>
                                        </div>
                                        <span class="ms-2 small fw-bold text-success"><?= $row['progress'] ?>%</span>
                                    </div>
                                </td>
                                <td>
                                    <?php 
                                        $statusClass = 'bg-secondary';
                                        if ($row['status'] == 'active') $statusClass = 'bg-primary';
                                        elseif ($row['status'] == 'completed') $statusClass = 'bg-success';
                                        elseif ($row['status'] == 'dropped') $statusClass = 'bg-danger';
                                    ?>
                                    <span class="badge <?= $statusClass ?> rounded-pill fw-normal px-3">
                                        <?= ucfirst($row['status']) ?>
                                    </span>
                                </td>
                                
                            </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="5" class="text-center py-5 text-muted">
                                <img src="/onlinecourse/assets/images/empty_students.svg" alt="" style="width: 60px; opacity: 0.5;" class="mb-3 d-block mx-auto"> Chưa có học viên nào đăng ký khóa học này.
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

        <?php if ($totalPages > 1): ?>
        <div class="card-footer bg-white py-3">
            <nav aria-label="Page navigation">
                <ul class="pagination justify-content-end mb-0">
                    <li class="page-item <?= ($page <= 1) ? 'disabled' : '' ?>">
                        <a class="page-link border-0 text-muted" href="/onlinecourse/index.php?controller=student&action=index&course_id=<?= $course['id'] ?>&page=<?= $page - 1 ?>">
                            <i class="fas fa-chevron-left"></i>
                        </a>
                    </li>
                    <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                        <li class="page-item <?= ($page == $i) ? 'active' : '' ?>">
                            <a class="page-link border-0 rounded-circle mx-1 <?= ($page == $i) ? 'bg-primary text-white' : 'text-dark' ?>" 
                               href="/onlinecourse/index.php?controller=student&action=index&course_id=<?= $course['id'] ?>&page=<?= $i ?>">
                                <?= $i ?>
                            </a>
                        </li>
                    <?php endfor; ?>
                    <li class="page-item <?= ($page >= $totalPages) ? 'disabled' : '' ?>">
                        <a class="page-link border-0 text-muted" href="/onlinecourse/index.php?controller=student&action=index&course_id=<?= $course['id'] ?>&page=<?= $page + 1 ?>">
                            <i class="fas fa-chevron-right"></i>
                        </a>
                    </li>
                </ul>
            </nav>
        </div>
        <?php endif; ?>
    </div>
</div>