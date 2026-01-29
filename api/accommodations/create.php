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

// Require senior role
requireRole('senior');

$payload = json_decode(file_get_contents('php://input'), true);
if (!is_array($payload) || empty($payload)) {
    $payload = $_POST;
}

$title = trim($payload['title'] ?? '');
$type = trim($payload['type'] ?? '');
$allowedFor = trim($payload['allowed_for'] ?? '');
$rent = $payload['monthly_rent'] ?? $payload['rent'] ?? '';
$location = trim($payload['location'] ?? '');
$description = trim($payload['description'] ?? '');
$facilities = $payload['facilities'] ?? [];

if ($title === '' || $type === '' || $allowedFor === '' || $rent === '' || $location === '' || $description === '') {
    echo json_encode(['success' => false, 'message' => 'All fields are required']);
    exit;
}

if (!is_numeric($rent)) {
    echo json_encode(['success' => false, 'message' => 'Rent must be numeric']);
    exit;
}

$validTypes = ['PG', 'Flat', 'Room', 'Hostel'];
$validAllowedFor = ['Male', 'Female', 'Family'];

if (!in_array($type, $validTypes, true)) {
    echo json_encode(['success' => false, 'message' => 'Invalid accommodation type']);
    exit;
}

if (!in_array($allowedFor, $validAllowedFor, true)) {
    echo json_encode(['success' => false, 'message' => 'Invalid allowed for option']);
    exit;
}

$conn = nearby_db_connect();
$ownerId = getCurrentUserId();
$rentInt = (int) $rent;
$facilitiesStr = '';

if (!empty($facilities) && is_array($facilities)) {
    $cleanFacilities = array_map(static fn($fac) => trim($fac), $facilities);
    $facilitiesStr = implode(',', $cleanFacilities);
}

$sql = 'INSERT INTO accommodations (user_id, title, type, allowed_for, rent, location, facilities, description)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?)';
$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, 'isssisss', $ownerId, $title, $type, $allowedFor, $rentInt, $location, $facilitiesStr, $description);

if (mysqli_stmt_execute($stmt)) {
    echo json_encode(['success' => true, 'message' => 'Accommodation posted successfully']);
} else {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Unable to save accommodation']);
}

mysqli_stmt_close($stmt);
