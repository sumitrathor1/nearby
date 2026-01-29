<?php
header('Content-Type: application/json');
require_once __DIR__ . '/../../config/db.php';
require_once __DIR__ . '/../../includes/helpers/csrf.php';
require_once __DIR__ . '/../../includes/helpers/authorization.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Validate CSRF token
requireCSRFToken();

// Require user to be logged in
requireLogin();

// Rate limiting for contact requests
$userId = $user['id'];
if (!checkRateLimit('contact_' . $userId, 10, 3600)) { // 10 contact requests per hour
    secureErrorResponse('Too many contact requests. Please wait before sending another request.', 429);
}

$input = json_decode(file_get_contents('php://input'), true);
if (!is_array($input) || empty($input)) {
    $input = $_POST;
}

$accommodationId = $input['accommodation_id'] ?? null;
$accommodationId = validateId($accommodationId, 'accommodation ID');
$message = trim($input['message'] ?? '');

$conn = nearby_db_connect();

// Verify accommodation exists
requireResourceExists(accommodationExists($conn, $accommodationId), 'accommodation');

$userId = getCurrentUserId();

$insertSql = 'INSERT INTO contact_requests (accommodation_id, requester_id, message) VALUES (?, ?, ?)';
$insertStmt = mysqli_prepare($conn, $insertSql);

if ($insertStmt === false) {
    secureErrorResponse('Failed to send request. Please try again.', 500, 'Failed to prepare contact insert: ' . mysqli_error($conn));
}

mysqli_stmt_bind_param($insertStmt, 'iis', $accommodationId, $userId, $message);

if (mysqli_stmt_execute($insertStmt)) {
    echo json_encode(['success' => true, 'message' => 'Request sent to the owner']);
} else {
    secureErrorResponse('Unable to send request. Please try again.', 500, 'Failed to execute contact insert: ' . mysqli_stmt_error($insertStmt));
}

mysqli_stmt_close($insertStmt);
