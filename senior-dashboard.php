<?php
require_once __DIR__ . '/config/security.php';

startSecureSession();

if (!isSessionValid() || ($_SESSION['user']['role'] ?? null) !== 'senior') {
    header('Location: login.php');
    exit;
}

$pageTitle = 'Senior Dashboard | NearBy';
$pageScripts = ['assets/js/search.js', 'assets/js/message.js'];
$enableChatbot = true;
require_once __DIR__ . '/includes/header.php';
?>
<div data-app-alerts>
    <div class="row align-items-center mb-4">
        <div class="col-lg-8">
            <h1 class="h4 fw-semibold">Share rooms or services with the NearBy community</h1>
            <p class="text-muted small mb-0">Publish your room availability or trusted local services so students receive help straight from seniors and local partners.</p>
        </div>
        <div class="col-lg-4 text-lg-end mt-3 mt-lg-0">
            <div class="d-flex flex-column flex-lg-row justify-content-lg-end gap-2">
                <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#createPostModal">
                    <i class="bi bi-plus-circle me-2"></i>Create Post
                </button>
                <a class="btn btn-outline-light" href="search.php">
                    <i class="bi bi-search me-2"></i>Browse Community Posts
                </a>
            </div>
        </div>
    </div>

    <div class="glass-card p-4 mb-4">
        <h2 class="h5 fw-semibold mb-3">Before you publish</h2>
        <ul class="list-unstyled d-grid gap-3 mb-0">
            <li class="d-flex gap-3">
                <span class="facility-icon"><i class="bi bi-house-heart"></i></span>
                <div>
                    <p class="fw-semibold mb-1">Gather complete details</p>
                    <p class="small text-muted mb-0">Keep rent or pricing, facilities, and clear location handy so juniors can shortlist quickly.</p>
                </div>
            </li>
            <li class="d-flex gap-3">
                <span class="facility-icon"><i class="bi bi-telephone"></i></span>
                <div>
                    <p class="fw-semibold mb-1">Provide an active contact</p>
                    <p class="small text-muted mb-0">Use a number you can attend; only logged-in users can see it for safety.</p>
                </div>
            </li>
            <li class="d-flex gap-3">
                <span class="facility-icon"><i class="bi bi-clipboard-check"></i></span>
                <div>
                    <p class="fw-semibold mb-1">Update regularly</p>
                    <p class="small text-muted mb-0">Edit or repost when availability changes to keep the community database current.</p>
                </div>
            </li>
        </ul>
    </div>

    <div class="glass-card p-4">
        <h2 class="h5 fw-semibold mb-3">Posting checklist</h2>
        <div class="row g-4">
            <div class="col-md-4">
                <div class="h-100 border rounded-4 border-light-subtle p-3">
                    <h3 class="h6 fw-semibold mb-2"><i class="bi bi-geo-alt me-2 text-primary"></i>Location clarity</h3>
                    <p class="small text-muted mb-0">Mention nearby landmarks, floor details, and approach roads.</p>
                </div>
            </div>
            <div class="col-md-4">
                <div class="h-100 border rounded-4 border-light-subtle p-3">
                    <h3 class="h6 fw-semibold mb-2"><i class="bi bi-lightbulb me-2 text-primary"></i>Highlight benefits</h3>
                    <p class="small text-muted mb-0">Share power backup, meals, timings, or unique perks that make your listing stand out.</p>
                </div>
            </div>
            <div class="col-md-4">
                <div class="h-100 border rounded-4 border-light-subtle p-3">
                    <h3 class="h6 fw-semibold mb-2"><i class="bi bi-emoji-smile me-2 text-primary"></i>Be responsive</h3>
                    <p class="small text-muted mb-0">Students rely on quick replies—plan a convenient time to answer calls.</p>
                </div>
            </div>
        </div>
    </div>
</div>
<?php include __DIR__ . '/includes/create-post-modal.php'; ?>
<?php require_once __DIR__ . '/includes/footer.php'; ?>
