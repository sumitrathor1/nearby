<?php
require_once __DIR__ . '/config/security.php';

startSecureSession();

if (!isSessionValid() || ($_SESSION['user']['role'] ?? null) !== 'junior') {
    header('Location: login.php');
    exit;
}

$pageTitle = 'Junior Dashboard | NearBy';
$pageScripts = ['assets/js/search.js', 'assets/js/message.js'];
$enableChatbot = true;
require_once __DIR__ . '/includes/header.php';
?>
<div data-app-alerts>
    <div class="row justify-content-between align-items-center mb-4">
        <div class="col-md-6">
            <h1 class="h4 fw-semibold">Hi <?= htmlspecialchars($_SESSION['user']['name']) ?>, find your next stay</h1>
            <p class="text-muted small">Filter based on location, type, rent, and must-have facilities.</p>
        </div>
        <div class="col-md-5">
            <div class="glass-card p-3">
                <p class="small text-muted mb-2">Quick tips</p>
                <div class="d-flex flex-wrap gap-2">
                    <span class="filter-pill">Verified owners</span>
                    <span class="filter-pill">Instant contact</span>
                    <span class="filter-pill">Budget friendly</span>
                </div>
            </div>
            <button type="button" class="btn btn-primary mt-3 w-100" data-bs-toggle="modal" data-bs-target="#createPostModal">
                <i class="bi bi-plus-circle me-2"></i>Create Post
            </button>
        </div>
    </div>

    <form id="dashboardSearch" class="glass-card p-4 mb-4">
        <input type="hidden" name="post_category" value="room">
        <div class="row g-3">
            <div class="col-md-4">
                <label class="form-label" for="dashLocation">Location / Keyword</label>
                <input type="text" class="form-control" id="dashLocation" name="location" placeholder="Civil Lines, DD Nagar...">
            </div>
            <div class="col-md-4">
                <label class="form-label" for="dashAccommodation">Accommodation Type</label>
                <select class="form-select" id="dashAccommodation" name="accommodation_type">
                    <option value="">Any</option>
                    <option value="PG">PG</option>
                    <option value="Flat">Flat</option>
                    <option value="Room">Room</option>
                    <option value="Hostel">Hostel</option>
                </select>
            </div>
            <div class="col-md-4">
                <label class="form-label" for="dashMaxPrice">Max Rent (₹)</label>
                <input type="number" class="form-control" id="dashMaxPrice" name="max_price" min="0" step="500" placeholder="15000">
            </div>
        </div>
        <div class="d-flex justify-content-end gap-3 mt-4">
            <button class="btn btn-outline-light" type="reset">Reset</button>
            <button class="btn btn-primary" type="submit">Search</button>
        </div>
    </form>

    <div id="searchResults" class="row g-4"></div>
</div>
<?php include __DIR__ . '/includes/create-post-modal.php'; ?>
<?php require_once __DIR__ . '/includes/footer.php'; ?>
