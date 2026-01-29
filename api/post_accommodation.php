<?php
header('Content-Type: application/json');
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../config/security.php';

startSecureSession();

if (!isSessionValid() || ($_SESSION['user']['role'] ?? '') !== 'senior') {
    http_response_code(403);
    echo json_encode(['success' => false, 'message' => 'Only senior students can post accommodations']);
    exit;
}

// Additional authorization: Only owners can create accommodations
$userType = $_SESSION['user']['user_type'] ?? 'student';
$allowedOwnerTypes = ['home_owner', 'room_owner'];
if (!in_array($userType, $allowedOwnerTypes)) {
    http_response_code(403);
    echo json_encode(['success' => false, 'message' => 'Only property owners can create accommodation listings']);
    exit;
}

$raw = file_get_contents('php://input');
$data = json_decode($raw, true);
if (!is_array($data) || empty($data)) {
    $data = $_POST;
}

$title = trim($data['title'] ?? '');
$type = trim($data['type'] ?? '');
$allowedFor = trim($data['allowed_for'] ?? '');
$rent = $data['monthly_rent'] ?? $data['rent'] ?? '';
$location = trim($data['location'] ?? '');
$description = trim($data['description'] ?? '');
$facilities = $data['facilities'] ?? [];

if ($title === '' || $type === '' || $allowedFor === '' || $rent === '' || $location === '' || $description === '') {
    echo json_encode(['success' => false, 'message' => 'All fields are required']);
    exit;
}

if (!is_numeric($rent)) {
    echo json_encode(['success' => false, 'message' => 'Rent must be numeric']);
    exit;
}

$allowedTypes = ['PG', 'Flat', 'Room', 'Hostel'];
$allowedForOptions = ['Male', 'Female', 'Family'];

if (!in_array($type, $allowedTypes, true)) {
    echo json_encode(['success' => false, 'message' => 'Invalid accommodation type']);
    exit;
}

if (!in_array($allowedFor, $allowedForOptions, true)) {
    echo json_encode(['success' => false, 'message' => 'Invalid option for allowed for']);
    exit;
}

$conn = nearby_db_connect();
$ownerId = (int) $_SESSION['user']['id'];
$rentInt = (int) $rent;
$facilitiesStr = '';

if (!empty($facilities) && is_array($facilities)) {
    $sanitizedFacilities = array_map(static fn($fac) => trim($fac), $facilities);
    $facilitiesStr = implode(',', $sanitizedFacilities);
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
