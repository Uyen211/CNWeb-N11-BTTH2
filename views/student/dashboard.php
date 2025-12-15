<?php 
// 1. Include Header
include_once dirname(__DIR__, 2) . '/views/layouts/header.php'; 
?>

<div class="container-fluid">
    <div class="row">
        
        <?php include_once dirname(__DIR__, 2) . '/views/layouts/sidebar.php';  ?>

        <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4 py-4 bg-light">
            
            <?php if (isset($_GET['msg'])): ?>
                <div class="mb-4">
                    <?php if ($_GET['msg'] == 'registered_success'): ?>
                        <div class="alert alert-success alert-dismissible fade show shadow-sm border-0" role="alert">
                            <i class="fas fa-check-circle me-2"></i> 
                            <strong>Thành công!</strong> Bạn đã đăng ký khóa học mới. Hãy bắt đầu học ngay!
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    <?php elseif ($_GET['msg'] == 'already_joined'): ?>
                        <div class="alert alert-info alert-dismissible fade show shadow-sm border-0" role="alert">
                            <i class="fas fa-info-circle me-2"></i> 
                            <strong>Thông báo:</strong> Bạn đã tham gia khóa học này rồi.
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    <?php endif; ?>
                </div>
            <?php endif; ?>

            <div class="row mb-5">
                <div class="col-lg-8 mb-3 mb-lg-0">
                    <div class="p-4 rounded-4 bg-primary text-white shadow d-flex align-items-center position-relative overflow-hidden h-100">
                        <div class="position-relative z-2">
                            <h2 class="fw-bold mb-2">Xin chào, <?php echo htmlspecialchars($_SESSION['fullname'] ?? $_SESSION['username'] ?? 'Học viên'); ?>! 👋</h2>
                            <p class="mb-0 opacity-75">"Học tập là hạt giống của kiến thức, kiến thức là hạt giống của hạnh phúc."</p>
                        </div>
                        <div class="position-absolute end-0 bottom-0 opacity-25" style="transform: rotate(-15deg); margin-right: -20px; margin-bottom: -20px;">
                            <i class="fas fa-book-open" style="font-size: 8rem;"></i>
                        </div>
                    </div>
                </div>
                
                <?php 
                    $myCourses = $myCourses ?? []; 
                    $totalCourses = count($myCourses);
                    $completedCourses = 0;
                    foreach($myCourses as $c) { 
                        if(isset($c['progress']) && $c['progress'] == 100) $completedCourses++; 
                    }
                ?>
                <div class="col-lg-4">
                    <div class="row h-100 g-3">
                        <div class="col-6">
                            <div class="p-3 bg-white rounded-4 shadow-sm h-100 border-0 text-center d-flex flex-column justify-content-center">
                                <h3 class="fw-bold text-primary mb-0"><?php echo $totalCourses; ?></h3>
                                <small class="text-muted fw-bold">Đã đăng ký</small>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="p-3 bg-white rounded-4 shadow-sm h-100 border-0 text-center d-flex flex-column justify-content-center">
                                <h3 class="fw-bold text-success mb-0"><?php echo $completedCourses; ?></h3>
                                <small class="text-muted fw-bold">Hoàn thành</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="d-flex justify-content-between align-items-center mb-4">
                <h4 class="fw-bold text-dark border-start border-4 border-primary ps-3 mb-0">Tiến độ học tập</h4>
                <a href="index.php?controller=course&action=index" class="btn btn-outline-primary rounded-pill px-4 fw-bold">
                    <i class="fas fa-search me-1"></i> Tìm khóa học mới
                </a>
            </div>

            <div class="row g-4 pb-5"> 
                <?php if (empty($myCourses)): ?>
                    <div class="col-12">
                        <div class="text-center py-5 bg-white rounded-4 shadow-sm">
                            <div class="mb-3 text-muted">
                                <i class="fas fa-folder-open" style="font-size: 4rem; opacity: 0.3;"></i>
                            </div>
                            <h5 class="fw-bold text-secondary">Bạn chưa đăng ký khóa học nào</h5>
                            <p class="text-muted">Hãy khám phá các khóa học thú vị của chúng tôi ngay hôm nay.</p>
                            <a href="index.php?controller=course&action=index" class="btn btn-primary px-4 py-2 rounded-pill mt-2">
                                Khám phá ngay
                            </a>
                        </div>
                    </div>
                <?php else: ?>
                    <?php foreach ($myCourses as $course): ?>
                        <div class="col-md-6 col-lg-4">
                            <div class="card h-100 border-0 shadow-sm rounded-4 overflow-hidden course-card hover-lift">
                                <div class="position-relative">
                                    <?php 
                                        $imgSrc = !empty($course['image']) ? 'assets/uploads/courses/'.$course['image'] : 'https://via.placeholder.com/400x225?text=No+Image'; 
                                    ?>
                                    <img src="<?php echo $imgSrc; ?>" class="card-img-top object-fit-cover" style="height: 180px; width: 100%;" alt="Course Image">
                                    
                                    <?php 
                                        $percent = $course['progress'] ?? 0;
                                        $isCompleted = ($percent == 100);
                                    ?>
                                    <span class="position-absolute top-0 end-0 m-3 badge <?php echo $isCompleted ? 'bg-success' : 'bg-warning text-dark'; ?> rounded-pill shadow-sm">
                                        <?php echo $isCompleted ? 'Đã xong' : 'Đang học'; ?>
                                    </span>
                                </div>

                                <div class="card-body d-flex flex-column p-4">
                                    <h5 class="card-title fw-bold text-dark mb-3 text-truncate" title="<?php echo htmlspecialchars($course['title']); ?>">
                                        <?php echo htmlspecialchars($course['title']); ?>
                                    </h5>
                                    
                                    <div class="mt-auto">
                                        <div class="d-flex justify-content-between align-items-center mb-1">
                                            <small class="fw-bold text-secondary">Tiến độ</small>
                                            <small class="fw-bold text-primary"><?php echo $percent; ?>%</small>
                                        </div>
                                        <div class="progress bg-light rounded-pill mb-3" style="height: 6px;">
                                            <div class="progress-bar rounded-pill <?php echo $isCompleted ? 'bg-success' : 'bg-primary'; ?>" 
                                                 role="progressbar" 
                                                 style="width: <?php echo $percent; ?>%" 
                                                 aria-valuenow="<?php echo $percent; ?>" 
                                                 aria-valuemin="0" 
                                                 aria-valuemax="100">
                                            </div>
                                        </div>

                                        <a href="index.php?controller=lesson&action=view&course_id=<?php echo $course['id']; ?>" 
                                           class="btn w-100 rounded-pill fw-bold <?php echo $isCompleted ? 'btn-outline-success' : 'btn-primary'; ?>">
                                            <?php echo $isCompleted ? 'Xem lại bài học' : 'Tiếp tục học'; ?> 
                                            <i class="fas fa-arrow-right ms-1"></i>
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>

        </main>
    </div> 
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
