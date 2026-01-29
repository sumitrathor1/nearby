<?php
require_once __DIR__ . '/config/security.php';

startSecureSession();

require_once __DIR__ . '/config/db.php';

$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
if (!$id) {
    http_response_code(404);
    $error = 'We could not find that accommodation.';
} else {
    $record = null;
    $conn = nearby_db_connect();
    $sql = 'SELECT a.id, a.title, a.type, a.allowed_for, a.rent, a.location, a.facilities, a.description, a.created_at,
                   u.name AS owner_name, u.college_email AS owner_email
            FROM accommodations a
            INNER JOIN users u ON u.id = a.user_id
            WHERE a.id = ? LIMIT 1';
    $stmt = mysqli_prepare($conn, $sql);

    if ($stmt) {
        mysqli_stmt_bind_param($stmt, 'i', $id);
        if (mysqli_stmt_execute($stmt)) {
            $result = mysqli_stmt_get_result($stmt);
            $record = mysqli_fetch_assoc($result);
            mysqli_free_result($result);
        }
        mysqli_stmt_close($stmt);
    }

    if (empty($record)) {
        http_response_code(404);
        $error = 'Accommodation not found';
    } else {
        $record['facilities'] = $record['facilities'] !== null && $record['facilities'] !== ''
            ? explode(',', $record['facilities'])
            : [];
        $record['rent'] = (int) $record['rent'];
        $record['monthly_rent'] = $record['rent'];
        $record['is_verified'] = true;
        $accommodation = $record;
    }
}

$pageTitle = isset($accommodation)
    ? sprintf('%s | NearBy', htmlspecialchars($accommodation['title']))
    : 'Accommodation Not Found';
$pageScripts = isset($accommodation) ? ['assets/js/details.js'] : [];
require_once __DIR__ . '/includes/header.php';
?>
<div data-app-alerts>
    <?php if (!isset($accommodation)): ?>
        <div class="glass-card p-5 text-center">
            <h1 class="h4 fw-semibold mb-3">Not Available</h1>
            <p class="text-muted mb-4"><?= htmlspecialchars($error) ?></p>
            <a class="btn btn-primary" href="search.php">Back to search</a>
        </div>
    <?php else: ?>
        <div class="row g-4 align-items-start">
            <div class="col-lg-8">
                <article class="glass-card p-4">
                    <header class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-3">
                        <div>
                            <span class="badge bg-success bg-opacity-75 text-white mb-2">Allowed: <?= htmlspecialchars($accommodation['allowed_for']) ?></span>
                            <h1 class="h4 fw-semibold mb-1"><?= htmlspecialchars($accommodation['title']) ?></h1>
                            <p class="text-muted small mb-0"><i class="bi bi-geo-alt me-1"></i><?= htmlspecialchars($accommodation['location']) ?></p>
                        </div>
                        <div class="text-md-end">
                            <?php if ($accommodation['is_verified']): ?>
                                <span class="badge badge-verified"><i class="bi bi-patch-check-fill me-1"></i>Owner Verified</span>
                            <?php endif; ?>
                            <p class="fw-semibold text-success fs-5 mb-0">₹<?= number_format($accommodation['monthly_rent']) ?> / month</p>
                        </div>
                    </header>
                    <section class="mb-4">
                        <h2 class="h6 text-uppercase text-muted">Description</h2>
                        <p class="mb-0 text-muted"><?= nl2br(htmlspecialchars($accommodation['description'])) ?></p>
                    </section>
                    <section>
                        <h2 class="h6 text-uppercase text-muted mb-3">Facilities</h2>
                        <div class="d-flex flex-wrap gap-2">
                            <?php if (!empty($accommodation['facilities'])): ?>
                                <?php foreach ($accommodation['facilities'] as $facility): ?>
                                    <span class="filter-pill"><?= htmlspecialchars($facility) ?></span>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <span class="text-muted small">Owner has not listed facilities yet.</span>
                            <?php endif; ?>
                        </div>
                    </section>
                </article>
            </div>
            <div class="col-lg-4">
                <div class="glass-card p-4">
                    <h2 class="h5 fw-semibold mb-3">Owner Details</h2>
                    <p class="small text-muted mb-1">Posted by</p>
                    <p class="fw-semibold mb-3"><i class="bi bi-person-circle me-2"></i><?= htmlspecialchars($accommodation['owner_name']) ?></p>
                    <?php if (!empty($_SESSION['user'])): ?>
                        <div class="mb-4">
                            <p class="small text-muted mb-1">Contact email</p>
                            <a class="text-decoration-none" href="mailto:<?= htmlspecialchars($accommodation['owner_email']) ?>"><?= htmlspecialchars($accommodation['owner_email']) ?></a>
                        </div>
                    <?php else: ?>
                        <div class="alert alert-info alert-glass small" role="alert">
                            Login to view contact details instantly and send a request to the owner.
                        </div>
                    <?php endif; ?>
                    <button class="btn btn-primary w-100" data-contact-id="<?= (int)$accommodation['id'] ?>">Contact Owner</button>
                    <a class="btn btn-outline-light w-100 mt-3" href="search.php">Back to search</a>
                </div>
            </div>
        </div>
    <?php endif; ?>
</div>
<?php require_once __DIR__ . '/includes/footer.php'; ?>
