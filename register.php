<?php
$pageTitle = 'Register | NearBy';
$pageScripts = ['assets/js/auth.js'];
require_once __DIR__ . '/includes/header.php';
?>
<div class="row justify-content-center" data-app-alerts>
    <div class="col-lg-7">
        <div class="glass-card p-4 p-md-5">
            <h1 class="h3 fw-semibold mb-4 text-center">Create Your NearBy Account</h1>
            <form id="registerForm" novalidate>
                <div class="row g-3">
                    <div class="col-md-6">
                        <label for="regName" class="form-label">Full Name</label>
                        <input type="text" class="form-control" id="regName" name="name" required>
                        <div class="invalid-feedback">Please enter your name</div>
                    </div>
                    <div class="col-md-6">
                        <label for="regEmail" class="form-label">College Email</label>
                        <input type="email" class="form-control" id="regEmail" name="email" placeholder="you@mitsgwl.ac.in" required>
                        <div class="invalid-feedback">Please use your college email (@mitsgwl.ac.in)</div>
                    </div>
                </div>
                <div class="row g-3 mt-1">
                    <div class="col-md-6">
                        <label for="regPassword" class="form-label">Password</label>
                        <input type="password" class="form-control" id="regPassword" name="password" required minlength="6">
                        <div class="invalid-feedback">Password must be at least 6 characters</div>
                    </div>
                    <div class="col-md-6">
                        <label for="regRole" class="form-label">I am a</label>
                        <select id="regRole" class="form-select" name="role" required>
                            <option value="">Choose role</option>
                            <option value="junior">Junior Student</option>
                            <option value="senior">Senior Student</option>
                        </select>
                        <div class="invalid-feedback">Please select your role</div>
                    </div>
                </div>
                <button type="submit" class="btn btn-primary w-100 mt-4">Create Account</button>
            </form>
            <p class="small text-muted text-center mt-4 mb-0">Already a member? <a href="login.php">Login instead</a></p>
        </div>
    </div>
    <div class="col-lg-4 mt-4 mt-lg-0">
        <div class="glass-card h-100 p-4 p-md-5">
            <h2 class="h4 fw-semibold mb-3">Why Join NearBy?</h2>
            <ul class="list-unstyled text-muted mb-4">
                <li class="d-flex gap-3 mb-3">
                    <i class="bi bi-house-heart text-primary fs-4"></i>
                    <span>Verified accommodation listings curated for MITS students.</span>
                </li>
                <li class="d-flex gap-3 mb-3">
                    <i class="bi bi-people text-primary fs-4"></i>
                    <span>Connect with seniors for mentorship and housing guidance.</span>
                </li>
                <li class="d-flex gap-3 mb-0">
                    <i class="bi bi-shield-check text-primary fs-4"></i>
                    <span>Secure login with your official @mitsgwl.ac.in email.</span>
                </li>
            </ul>
            <div class="border-top border-light pt-4 mt-2">
                <h3 class="h5 fw-semibold mb-2">Need Help?</h3>
                <p class="text-muted mb-1">Email: <a href="mailto:24cd3dsu4@mitsgwl.ac.in">24cd3dsu4@mitsgwl.ac.in</a></p>
                <p class="text-muted mb-0">Phone: +91 7566868709</p>
            </div>
        </div>
    </div>
</div>
<?php require_once __DIR__ . '/includes/footer.php'; ?>
