<?php
header('Content-Type: application/json');
require_once __DIR__ . '/../../config/db.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$user = $_SESSION['user'] ?? null;

if (!$user) {
    http_response_code(401);
    echo json_encode(['success' => false, 'message' => 'Please login to contact the owner']);
    exit;
}

$input = json_decode(file_get_contents('php://input'), true);
if (!is_array($input) || empty($input)) {
    $input = $_POST;
}

$accommodationId = $input['accommodation_id'] ?? null;
$message = trim($input['message'] ?? '');

if (!$accommodationId || !is_numeric($accommodationId)) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Missing accommodation reference']);
    exit;
}

$conn = nearby_db_connect();
$accommodationId = (int) $accommodationId;
$userId = (int) $user['id'];

$checkSql = 'SELECT id FROM accommodations WHERE id = ? LIMIT 1';
$checkStmt = mysqli_prepare($conn, $checkSql);
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
mysqli_stmt_bind_param($insertStmt, 'iis', $accommodationId, $userId, $message);

if (mysqli_stmt_execute($insertStmt)) {
    echo json_encode(['success' => true, 'message' => 'Request sent to the owner']);
} else {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Unable to send request']);
}

mysqli_stmt_close($insertStmt);
