<?php include_once dirname(__DIR__) . '/layouts/header_auth.php'; ?>

<div class="auth-wrapper">
    <div class="auth-card">
        <div class="auth-icon-top"><i class="fas fa-arrow-right"></i></div>
        <h2 class="auth-title">Đăng nhập</h2>
        <p class="auth-subtitle">Chào mừng trở lại! Vui lòng nhập thông tin.</p>

        <?php if (!empty($error)): ?>
            <div class="alert alert-danger py-2 small text-start"><i class="fas fa-exclamation-circle me-1"></i> <?= $error ?></div>
        <?php endif; ?>
        <?php if (isset($_GET['success'])): ?>
            <div class="alert alert-success py-2 small text-start"><i class="fas fa-check-circle me-1"></i> Đăng ký thành công!</div>
        <?php endif; ?>

        <form action="<?= $base_url ?>/index.php?controller=auth&action=login" method="POST">
            <div class="input-group-custom">
                <i class="fas fa-envelope input-icon"></i>
                <input type="text" name="username" class="form-control-custom" placeholder="Tên đăng nhập hoặc Email" required>
            </div>

            <div class="input-group-custom">
                <i class="fas fa-lock input-icon"></i>
                <input type="password" name="password" class="form-control-custom" placeholder="Mật khẩu" required id="loginPass">
                <!-- <i class="fas fa-eye-slash toggle-password" onclick="togglePass('loginPass', this)"></i> -->
            </div>

            <div class="d-flex justify-content-end mb-3">
                <a href="#" class="text-decoration-none text-muted small hover-primary">Quên mật khẩu?</a>
            </div>

            <button type="submit" class="btn-auth-primary">Đăng nhập</button>
        </form>

        <div class="social-buttons">
            <span class="social-label">Hoặc đăng nhập với</span>
            <a href="#" class="btn-social"><i class="fab fa-google text-danger"></i></a>
            <a href="#" class="btn-social"><i class="fab fa-facebook-f text-primary"></i></a>
        </div>

        <div class="mt-4 text-muted small">
            Chưa có tài khoản? <a href="<?= $base_url ?>/index.php?controller=auth&action=register" class="fw-bold text-dark text-decoration-none">Đăng ký ngay</a>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>