<?php
$pageTitle = 'Login | NearBy';
$pageScripts = ['assets/js/auth.js'];
require_once __DIR__ . '/includes/header.php';
?>
<div class="row justify-content-center" data-app-alerts>
    <div class="col-lg-6">
        <div class="glass-card p-4 p-md-5">
            <h1 class="h3 fw-semibold mb-4 text-center">Welcome Back</h1>
            <form id="loginForm" novalidate>
                <div class="mb-3">
                    <label for="loginEmail" class="form-label">College Email</label>
                    <input type="email" class="form-control" id="loginEmail" name="email" placeholder="you@mitsgwl.ac.in" required>
                    <div class="invalid-feedback">Use your verified college email</div>
                </div>
                <div class="mb-3">
                    <label for="loginPassword" class="form-label">Password</label>
                    <input type="password" class="form-control" id="loginPassword" name="password" required minlength="6">
                    <div class="invalid-feedback">Password must be at least 6 characters</div>
                </div>
                <div class="mb-4">
                    <label for="loginRole" class="form-label">I am a</label>
                    <select id="loginRole" class="form-select" name="role" required>
                        <option value="">Choose role</option>
                        <option value="junior">Junior Student</option>
                        <option value="senior">Senior Student</option>
                    </select>
                    <div class="invalid-feedback">Please select your role</div>
                </div>
                <button type="submit" class="btn btn-primary w-100">Login</button>
            </form>
            <p class="small text-muted text-center mt-4 mb-0">New here? <a href="register.php">Create an account</a></p>
        </div>
    </div>
    <div class="col-lg-5 mt-4 mt-lg-0">
        <div class="glass-card h-100 p-4 p-md-5">
            <h2 class="h4 fw-semibold mb-3">Need Assistance?</h2>
            <p class="text-muted mb-4">Reach out to the NearBy housing support desk for quick help with account access or platform issues.</p>
            <div class="d-flex flex-column gap-2">
                <div class="d-flex align-items-start gap-3">
                    <i class="bi bi-envelope text-primary fs-4"></i>
                    <div>
                        <div class="fw-semibold">Email</div>
                        <a class="text-decoration-none" href="mailto:24cd3dsu4@mitsgwl.ac.in">24cd3dsu4@mitsgwl.ac.in</a>
                    </div>
                </div>
                <div class="d-flex align-items-start gap-3">
                    <i class="bi bi-telephone text-primary fs-4"></i>
                    <div>
                        <div class="fw-semibold">Call</div>
                        <span class="text-muted">+91 7566868709 (Mon–Sat, 10 AM – 6 PM)</span>
                    </div>
                </div>
                <div class="d-flex align-items-start gap-3">
                    <i class="bi bi-geo-alt text-primary fs-4"></i>
                    <div>
                        <div class="fw-semibold">Campus Desk</div>
                        <span class="text-muted">Student Activity Centre, MITS Gwalior</span>
                    </div>
                </div>
            </div>
            <div class="alert alert-info bg-opacity-10 border-0 mt-4 mb-0">
                <i class="bi bi-info-circle me-2"></i>Use your official @mitsgwl.ac.in email to access NearBy services.
            </div>
        </div>
    </div>
</div>
<?php require_once __DIR__ . '/includes/footer.php'; ?>
