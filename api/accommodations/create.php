<?php
header('Content-Type: application/json');
require_once __DIR__ . '/../../config/db.php';
require_once __DIR__ . '/../../config/security.php';

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

// Validate title
$titleValidation = validateText($title, 5, 200);
if (!$titleValidation['valid']) {
    echo json_encode(['success' => false, 'message' => $titleValidation['error']]);
    exit;
}
$title = $titleValidation['text'];

// Validate accommodation type
$validTypes = ['PG', 'Flat', 'Room', 'Hostel'];
$typeValidation = validateEnum($type, $validTypes, 'accommodation type');
if (!$typeValidation['valid']) {
    echo json_encode(['success' => false, 'message' => $typeValidation['error']]);
    exit;
}
$type = $typeValidation['value'];

// Validate allowed for
$validAllowedFor = ['Male', 'Female', 'Family'];
$allowedForValidation = validateEnum($allowedFor, $validAllowedFor, 'allowed for option');
if (!$allowedForValidation['valid']) {
    echo json_encode(['success' => false, 'message' => $allowedForValidation['error']]);
    exit;
}
$allowedFor = $allowedForValidation['value'];

// Validate rent
$rentValidation = validateNumeric($rent, 500, 50000);
if (!$rentValidation['valid']) {
    echo json_encode(['success' => false, 'message' => $rentValidation['error']]);
    exit;
}
$rentInt = $rentValidation['value'];

// Validate location
$locationValidation = validateText($location, 5, 200);
if (!$locationValidation['valid']) {
    echo json_encode(['success' => false, 'message' => $locationValidation['error']]);
    exit;
}
$location = $locationValidation['text'];

// Validate description
$descriptionValidation = validateText($description, 10, 1000, true);
if (!$descriptionValidation['valid']) {
    echo json_encode(['success' => false, 'message' => $descriptionValidation['error']]);
    exit;
}
$description = $descriptionValidation['text'];

// Validate facilities
$facilitiesValidation = validateFacilities($facilities);
if (!$facilitiesValidation['valid']) {
    echo json_encode(['success' => false, 'message' => $facilitiesValidation['error']]);
    exit;
}
$facilitiesArray = $facilitiesValidation['facilities'];

$conn = nearby_db_connect();
$ownerId = (int) $_SESSION['user']['id'];
$facilitiesStr = '';

if (!empty($facilitiesArray)) {
    $facilitiesStr = implode(',', $facilitiesArray);
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
