<?php include_once dirname(__DIR__) . '/layouts/header_auth.php'; ?>

<div class="auth-wrapper">
    <div class="auth-card">
        <div class="auth-icon-top"><i class="fas fa-user-plus"></i></div>
        <h2 class="auth-title">Tạo tài khoản</h2>
        <p class="auth-subtitle">Tham gia cộng đồng học tập miễn phí.</p>

        <?php if (!empty($error)): ?>
            <div class="alert alert-danger py-2 small text-start"><i class="fas fa-exclamation-circle me-1"></i> <?= $error ?></div>
        <?php endif; ?>

        <form action="<?= $base_url ?>/index.php?controller=auth&action=register" method="POST">
            <div class="input-group-custom">
                <i class="fas fa-user input-icon"></i>
                <input type="text" name="fullname" class="form-control-custom" placeholder="Họ và tên đầy đủ" required>
            </div>
            <div class="input-group-custom">
                <i class="fas fa-at input-icon"></i>
                <input type="text" name="username" class="form-control-custom" placeholder="Tên đăng nhập" required>
            </div>
            <div class="input-group-custom">
                <i class="fas fa-envelope input-icon"></i>
                <input type="email" name="email" class="form-control-custom" placeholder="Địa chỉ Email" required>
            </div>

            <div class="input-group-custom">
                <i class="fas fa-lock input-icon"></i>
                <input type="password" name="password" class="form-control-custom" placeholder="Mật khẩu" required id="regPass">
                <!-- <i class="fas fa-eye-slash toggle-password" onclick="togglePass('regPass', this)"></i> -->
            </div>

            <div class="input-group-custom">
                <i class="fas fa-check-circle input-icon"></i>
                <input type="password" name="confirm_password" class="form-control-custom" placeholder="Nhập lại mật khẩu" required id="regConfirmPass">
                <!-- <i class="fas fa-eye-slash toggle-password" onclick="togglePass('regConfirmPass', this)"></i> -->
            </div>

            <button type="submit" class="btn-auth-primary">Đăng ký tài khoản</button>
        </form>

        <div class="mt-4 text-muted small">
            Đã có tài khoản? <a href="<?= $base_url ?>/index.php?controller=auth&action=login" class="fw-bold text-dark text-decoration-none">Đăng nhập</a>
        </div>
    </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>