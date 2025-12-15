<?php include_once dirname(__DIR__) . '/layouts/header.php'; ?>

<div class="container py-5">
    <div class="row">
        <div class="col-lg-3 mb-4">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-white fw-bold text-uppercase py-3">
                    <i class="fas fa-filter me-2 text-primary"></i> Danh mục
                </div>
                <div class="list-group list-group-flush">
                    <a href="index.php?controller=course&action=index" 
                       class="list-group-item list-group-item-action <?= !isset($_GET['category']) ? 'active fw-bold' : '' ?>">
                        <i class="fas fa-th-large me-2"></i> Tất cả khóa học
                    </a>

                    <?php if (!empty($categories)): ?>
                        <?php foreach ($categories as $cat): ?>
                            <?php 
                                $isActive = (isset($_GET['category']) && $_GET['category'] == $cat['id']) ? 'active fw-bold' : '';
                            ?>
                            <a href="index.php?controller=course&action=index&category=<?= $cat['id'] ?>" 
                               class="list-group-item list-group-item-action <?= $isActive ?>">
                                <i class="fas fa-angle-right me-2 text-secondary"></i> <?= htmlspecialchars($cat['name']) ?>
                            </a>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <div class="col-lg-9">
            
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h2 class="h4 fw-bold mb-0">
                    <?php 
                        if (isset($_GET['keyword']) && !empty($_GET['keyword'])) {
                            echo 'Kết quả tìm kiếm: "<span class="text-primary">' . htmlspecialchars($_GET['keyword']) . '</span>"';
                        } else if (isset($_GET['category']) && !empty($categories)) {
                            // Tìm tên danh mục để hiển thị cho đẹp
                            $catName = "Danh mục";
                            foreach($categories as $c) { if($c['id'] == $_GET['category']) $catName = $c['name']; }
                            echo 'Danh mục: <span class="text-primary">' . htmlspecialchars($catName) . '</span>';
                        } else {
                            echo 'Tất cả khóa học';
                        }
                    ?>
                </h2>
                <span class="badge bg-secondary rounded-pill"><?= count($courses) ?> kết quả</span>
            </div>

            <?php if (empty($courses)): ?>
                
                <div class="text-center py-5 bg-light rounded shadow-sm border border-dashed">
                    <i class="fas fa-search fa-3x text-muted mb-3 opacity-50"></i>
                    <h5 class="fw-bold text-secondary">Không tìm thấy khóa học nào!</h5>
                    <p class="text-muted">Thử tìm với từ khóa khác hoặc quay lại danh sách chung.</p>
                    <a href="index.php?controller=course&action=index" class="btn btn-primary rounded-pill px-4 mt-2">
                        <i class="fas fa-sync-alt me-1"></i> Xem tất cả
                    </a>
                </div>

            <?php else: ?>

                <div class="row g-4">
                    <?php foreach ($courses as $course): ?>
                        <div class="col-md-6 col-lg-4">
                            <div class="card h-100 shadow-sm border-0 hover-card rounded-4 overflow-hidden">
                                
                                <div class="position-relative">
                                    <?php 
                                        $dbImage = 'assets/uploads/courses/' . $course['image'];
                                        $defaultImage = 'https://via.placeholder.com/600x400?text=EduPlatform'; 
                                        $imgSrc = !empty($course['image']) ? $dbImage : $defaultImage;
                                    ?>
                                    <a href="index.php?controller=course&action=detail&id=<?= $course['id'] ?>">
                                        <div class="ratio ratio-16x9">
                                            <img src="<?= $imgSrc ?>" 
                                                 class="card-img-top object-fit-cover" 
                                                 alt="<?= htmlspecialchars($course['title']) ?>"
                                                 onerror="this.onerror=null; this.src='<?= $defaultImage ?>';">
                                        </div>
                                    </a>
                                </div>
                                
                                <div class="card-body d-flex flex-column p-3">
                                    <div class="mb-2">
                                        <span class="badge bg-info bg-opacity-10 text-info border border-info border-opacity-25" style="font-size: 0.7rem;">
                                            <?= htmlspecialchars($course['category_name'] ?? 'General') ?>
                                        </span>
                                    </div>

                                    <h6 class="card-title fw-bold text-truncate mb-1" title="<?= htmlspecialchars($course['title']) ?>">
                                        <a href="index.php?controller=course&action=detail&id=<?= $course['id'] ?>" class="text-decoration-none text-dark stretched-link">
                                            <?= htmlspecialchars($course['title']) ?>
                                        </a>
                                    </h6>
                                    
                                    <small class="text-muted mb-3">
                                        <i class="fas fa-user-circle me-1"></i> <?= htmlspecialchars($course['instructor_name'] ?? 'Giảng viên') ?>
                                    </small>
                                    
                                    <div class="mt-auto pt-3 border-top d-flex justify-content-between align-items-center">
                                        <?php if($course['price'] == 0): ?>
                                            <span class="fw-bold text-success">Miễn phí</span>
                                        <?php else: ?>
                                            <span class="fw-bold text-primary"><?= number_format($course['price'], 0, ',', '.') ?> đ</span>
                                        <?php endif; ?>
                                        
                                        <span class="btn btn-sm btn-light rounded-circle text-primary">
                                            <i class="fas fa-arrow-right"></i>
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>

            <?php endif; ?>
        </div>
    </div>
</div>

<style>
    /* Hiệu ứng hover cho đẹp */
    .hover-card { transition: transform 0.2s ease, box-shadow 0.2s ease; }
    .hover-card:hover { transform: translateY(-5px); box-shadow: 0 10px 20px rgba(0,0,0,0.1) !important; }
</style>

<?php include_once dirname(__DIR__) . '/layouts/footer.php'; ?>