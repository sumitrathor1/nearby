<?php
header('Content-Type: application/json');
require_once __DIR__ . '/../../config/db.php';
require_once __DIR__ . '/../../includes/helpers/csrf.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Method not allowed']);
    exit;
}

// Validate CSRF token
requireCSRFToken();

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['user']['id'])) {
    http_response_code(401);
    echo json_encode(['success' => false, 'message' => 'Login required to create a post']);
    exit;
}

$payload = json_decode(file_get_contents('php://input'), true);
if (!is_array($payload) || empty($payload)) {
    $payload = $_POST;
}

$postCategory = strtolower(trim($payload['post_category'] ?? ''));
$location = trim($payload['location'] ?? '');
$description = trim($payload['description'] ?? '');
$contactPhone = trim(preg_replace('/[^0-9+\-\s]/', '', $payload['contact_phone'] ?? ''));
$rentOrPrice = trim((string) ($payload['rent_or_price'] ?? ''));
$facilities = $payload['facilities'] ?? [];
$availabilityTime = trim((string) ($payload['availability_time'] ?? ''));

if ($postCategory === '' || $location === '' || $description === '' || $contactPhone === '') {
    echo json_encode(['success' => false, 'message' => 'Post type, location, description, and contact number are required']);
    exit;
}

if (!preg_match('/^[0-9+][0-9\-\s]{6,}$/', $contactPhone)) {
    echo json_encode(['success' => false, 'message' => 'Enter a valid contact number']);
    exit;
}

$validCategories = ['room', 'service'];
if (!in_array($postCategory, $validCategories, true)) {
    echo json_encode(['success' => false, 'message' => 'Invalid post type']);
    exit;
}

$serviceType = null;
$accommodationType = null;
$allowedFor = null;
$rentValue = 0;
$facilitiesStr = null;

if ($postCategory === 'room') {
    $accommodationType = trim($payload['accommodation_type'] ?? '');
    $allowedFor = trim($payload['allowed_for'] ?? '');

    $validAccommodation = ['PG', 'Flat', 'Room', 'Hostel'];
    $validAllowed = ['Male', 'Female', 'Family'];

    if (!in_array($accommodationType, $validAccommodation, true)) {
        echo json_encode(['success' => false, 'message' => 'Select a valid accommodation type']);
        exit;
    }

    if (!in_array($allowedFor, $validAllowed, true)) {
        echo json_encode(['success' => false, 'message' => 'Select who can stay']);
        exit;
    }

    if ($rentOrPrice === '' || !is_numeric($rentOrPrice)) {
        echo json_encode(['success' => false, 'message' => 'Provide a numeric rent amount']);
        exit;
    }
    $rentValue = (int) $rentOrPrice;
    if ($rentValue <= 0) {
        echo json_encode(['success' => false, 'message' => 'Rent must be greater than zero']);
        exit;
    }

    if (!empty($facilities) && is_array($facilities)) {
        $cleanFacilities = array_filter(array_map(static fn($fac) => trim((string) $fac), $facilities));
        if ($cleanFacilities) {
            $facilitiesStr = implode(',', $cleanFacilities);
        }
    }
    $availabilityTime = null; // not applicable for room posts
} else {
    $serviceType = strtolower(trim($payload['service_type'] ?? ''));
    $validServices = ['tiffin', 'gas', 'milk', 'sabji', 'other'];

    if (!in_array($serviceType, $validServices, true)) {
        echo json_encode(['success' => false, 'message' => 'Select a valid service type']);
        exit;
    }

    if ($rentOrPrice !== '') {
        if (!is_numeric($rentOrPrice)) {
            echo json_encode(['success' => false, 'message' => 'Price must be numeric']);
            exit;
        }
        $rentValue = max(0, (int) $rentOrPrice);
    } else {
        $rentValue = 0;
    }

    if ($availabilityTime === '') {
        echo json_encode(['success' => false, 'message' => 'Provide your service availability']);
        exit;
    }
}

$conn = nearby_db_connect();
$userId = (int) $_SESSION['user']['id'];

$sql = 'INSERT INTO posts (user_id, post_category, service_type, accommodation_type, allowed_for, rent_or_price, location, facilities, availability_time, description, contact_phone)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)';
$stmt = mysqli_prepare($conn, $sql);

if ($stmt === false) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Failed to prepare statement']);
    exit;
}

$rentBind = $rentValue;
$facilitiesBind = $facilitiesStr;

mysqli_stmt_bind_param(
    $stmt,
    'issssisssss',
    $userId,
    $postCategory,
    $serviceType,
    $accommodationType,
    $allowedFor,
    $rentBind,
    $location,
    $facilitiesBind,
    $availabilityTime,
    $description,
    $contactPhone
);

if (mysqli_stmt_execute($stmt)) {
    echo json_encode(['success' => true, 'message' => 'Post created successfully']);
} else {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Unable to save post']);
}

mysqli_stmt_close($stmt);
