<?php
header('Content-Type: application/json');
require_once __DIR__ . '/../../config/db.php';
require_once __DIR__ . '/../../config/security.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$user = $_SESSION['user'] ?? null;

if (!$user) {
    http_response_code(401);
    echo json_encode(['success' => false, 'message' => 'Please login to contact the owner']);
    exit;
}

// Rate limiting for contact requests
$userId = $user['id'];
if (!checkRateLimit('contact_' . $userId, 10, 3600)) { // 10 contact requests per hour
    secureErrorResponse('Too many contact requests. Please wait before sending another request.', 429);
}

$input = json_decode(file_get_contents('php://input'), true);
if (!is_array($input) || empty($input)) {
    $input = $_POST;
}

// Validate accommodation ID
$accommodationIdValidation = validateNumeric($input['accommodation_id'] ?? '', 1);
if (!$accommodationIdValidation['valid']) {
    secureErrorResponse('Valid accommodation ID is required', 400);
}
$accommodationId = $accommodationIdValidation['value'];

// Validate and sanitize message
$message = '';
if (!empty($input['message'])) {
    $messageValidation = validateText($input['message'], 0, 500);
    if (!$messageValidation['valid']) {
        secureErrorResponse('Message: ' . $messageValidation['error'], 400);
    }
    $message = $messageValidation['text'];
}

$conn = nearby_db_connect();
$userId = (int) $user['id'];

$checkSql = 'SELECT id FROM accommodations WHERE id = ? LIMIT 1';
$checkStmt = mysqli_prepare($conn, $checkSql);

if ($checkStmt === false) {
    secureErrorResponse('Failed to process request. Please try again.', 500, 'Failed to prepare accommodation check: ' . mysqli_error($conn));
}

mysqli_stmt_bind_param($checkStmt, 'i', $accommodationId);
mysqli_stmt_execute($checkStmt);
$result = mysqli_stmt_get_result($checkStmt);
$exists = mysqli_fetch_assoc($result);
mysqli_stmt_close($checkStmt);

if (!$exists) {
    http_response_code(404);
    echo json_encode(['success' => false, 'message' => 'Accommodation not found']);
    exit;
}

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
