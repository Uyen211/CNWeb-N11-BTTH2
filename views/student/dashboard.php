<?php include 'views/layouts/header.php'; ?>
<?php include 'views/layouts/sidebar.php'; ?>
<div class="container py-5">
    <div class="row mb-5">
        <div class="col-lg-8">
            <div class="p-4 rounded-4 bg-primary text-white shadow d-flex align-items-center position-relative overflow-hidden welcome-banner">
                <div class="position-relative z-2">
                    <h2 class="fw-bold mb-2">Xin chào, <?php echo htmlspecialchars($_SESSION['fullname']); ?>! 👋</h2>
                    <p class="mb-0 opacity-75">"Học tập là hạt giống của kiến thức, kiến thức là hạt giống của hạnh phúc."</p>
                </div>
                <div class="position-absolute end-0 bottom-0 opacity-25" style="transform: rotate(-15deg); margin-right: -20px; margin-bottom: -20px;">
                    <i class="bi bi-book-half" style="font-size: 8rem;"></i>
                </div>
            </div>
        </div>
        
        <?php 
            $totalCourses = count($myCourses);
            $completedCourses = 0;
            foreach($myCourses as $c) { if($c['progress'] == 100) $completedCourses++; }
            $inProgress = $totalCourses - $completedCourses;
        ?>
        <div class="col-lg-4 mt-3 mt-lg-0">
            <div class="row h-100 g-3">
                <div class="col-6">
                    <div class="p-3 bg-white rounded-4 shadow-sm h-100 border border-light text-center">
                        <h3 class="fw-bold text-primary mb-0"><?php echo $totalCourses; ?></h3>
                        <small class="text-muted">Đã đăng ký</small>
                    </div>
                </div>
                <div class="col-6">
                    <div class="p-3 bg-white rounded-4 shadow-sm h-100 border border-light text-center">
                        <h3 class="fw-bold text-success mb-0"><?php echo $completedCourses; ?></h3>
                        <small class="text-muted">Hoàn thành</small>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="fw-bold text-dark border-start border-4 border-primary ps-3 mb-0">Khóa học của tôi</h4>
        <a href="index.php?controller=course&action=index" class="btn btn-outline-primary rounded-pill px-4">
            <i class="bi bi-search me-1"></i> Tìm khóa học mới
        </a>
    </div>

    <div class="row g-4">
        <?php if (empty($myCourses)): ?>
            <div class="col-12">
                <div class="text-center py-5 bg-light rounded-4 border border-dashed">
                    <div class="mb-3 text-muted">
                        <i class="bi bi-journal-x" style="font-size: 4rem;"></i>
                    </div>
                    <h5 class="fw-bold text-secondary">Bạn chưa đăng ký khóa học nào</h5>
                    <p class="text-muted mb-4">Hãy bắt đầu hành trình chinh phục kiến thức ngay hôm nay!</p>
                    <a href="index.php?controller=course&action=index" class="btn btn-primary px-4 py-2 rounded-pill">
                        Khám phá khóa học
                    </a>
                </div>
            </div>
        <?php else: ?>
            <?php foreach ($myCourses as $course): ?>
                <div class="col-md-6 col-lg-4">
                    <div class="card h-100 border-0 shadow-sm rounded-4 overflow-hidden course-card hover-lift">
                        <div class="position-relative card-img-wrapper">
                            <img src="assets/uploads/courses/<?php echo !empty($course['image']) ? $course['image'] : 'default_course.jpg'; ?>" 
                                 class="card-img-top object-fit-cover" 
                                 alt="<?php echo htmlspecialchars($course['title']); ?>"
                                 style="height: 180px; width: 100%;">
                            
                            <div class="card-img-overlay d-flex align-items-center justify-content-center bg-dark bg-opacity-50 opacity-0 hover-visible transition-all">
                                <a href="index.php?controller=lesson&action=view&course_id=<?php echo $course['course_id']; ?>" 
                                   class="btn btn-light rounded-circle p-3">
                                    <i class="bi bi-play-fill fs-4 text-primary"></i>
                                </a>
                            </div>

                            <?php 
                                $isCompleted = ($course['status'] == 'completed' || $course['progress'] == 100);
                                $badgeClass = $isCompleted ? 'bg-success' : 'bg-warning text-dark';
                                $badgeText = $isCompleted ? 'Đã xong' : 'Đang học';
                            ?>
                            <span class="position-absolute top-0 end-0 m-3 badge <?php echo $badgeClass; ?> rounded-pill shadow-sm">
                                <?php echo $badgeText; ?>
                            </span>
                        </div>

                        <div class="card-body d-flex flex-column p-4">
                            <h5 class="card-title fw-bold text-dark mb-2 text-truncate" title="<?php echo htmlspecialchars($course['title']); ?>">
                                <?php echo htmlspecialchars($course['title']); ?>
                            </h5>
                            
                            <p class="small text-muted mb-3">
                                <i class="bi bi-calendar3 me-1"></i> Đăng ký: <?php echo date('d/m/Y', strtotime($course['enrolled_date'])); ?>
                            </p>

                            <div class="mt-auto">
                                <div class="d-flex justify-content-between align-items-center mb-1">
                                    <small class="fw-bold text-secondary">Tiến độ</small>
                                    <small class="fw-bold text-primary"><?php echo $course['progress']; ?>%</small>
                                </div>
                                <div class="progress bg-light rounded-pill" style="height: 8px;">
                                    <div class="progress-bar rounded-pill <?php echo $isCompleted ? 'bg-success' : 'bg-primary'; ?>" 
                                         role="progressbar" 
                                         style="width: <?php echo $course['progress']; ?>%">
                                    </div>
                                </div>

                                <a href="index.php?controller=lesson&action=view&course_id=<?php echo $course['course_id']; ?>" 
                                   class="btn w-100 mt-3 rounded-pill fw-bold <?php echo $isCompleted ? 'btn-outline-success' : 'btn-primary'; ?>">
                                    <?php echo $isCompleted ? 'Xem lại' : 'Tiếp tục học'; ?> <i class="bi bi-arrow-right ms-1"></i>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</div>

<?php include 'views/layouts/footer.php'; ?>

<style>
    /* Hiệu ứng nền banner */
    .welcome-banner {
        background: linear-gradient(135deg, #4e54c8, #8f94fb);
    }

    /* Card hover effect */
    .hover-lift {
        transition: transform 0.3s ease, box-shadow 0.3s ease;
    }
    .hover-lift:hover {
        transform: translateY(-8px);
        box-shadow: 0 1rem 3rem rgba(0,0,0,.1) !important;
    }

    /* Image zoom effect */
    .card-img-wrapper {
        overflow: hidden;
    }
    .hover-lift:hover .card-img-top {
        transform: scale(1.05);
        transition: transform 0.5s ease;
    }

    /* Play button overlay */
    .hover-visible {
        transition: opacity 0.3s ease;
    }
    .card-img-wrapper:hover .hover-visible {
        opacity: 1 !important;
    }

    /* Progress bar animation */
    .progress-bar {
        transition: width 1s ease-in-out;
    }
</style>