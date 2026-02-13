<?php
$pageTitle = 'Forgot Password | NearBy';
$pageScripts = ['assets/js/forgot-password.js'];
require_once __DIR__ . '/includes/header.php';
?>
<div class="row justify-content-center" data-app-alerts>
    <div class="col-lg-6">
        <div class="glass-card p-4 p-md-5">
            <h1 class="h3 fw-semibold mb-4 text-center">Reset Password</h1>
            <p class="text-muted text-center mb-4">Enter your email address and we'll send you instructions to reset your password.</p>
            
            <form id="forgotPasswordForm" novalidate>
                <?= csrfField() ?>
                <div class="mb-4">
                    <label for="forgotEmail" class="form-label">Email Address</label>
                    <input type="email" class="form-control" id="forgotEmail" name="email" placeholder="you@example.com" required>
                    <div class="invalid-feedback">Please enter a valid email address</div>
                </div>
                
                <button type="submit" class="btn btn-primary w-100">Send Reset Link</button>
            </form>
            
            <p class="small text-muted text-center mt-4 mb-0">Remembered your password? <a href="login.php">Back to Login</a></p>
        </div>
    </div>
</div>
<?php require_once __DIR__ . '/includes/footer.php'; ?>
