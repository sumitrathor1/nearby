<?php
header('Content-Type: application/json');
require_once __DIR__ . '/../../config/db.php';
require_once __DIR__ . '/../../config/security.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Method not allowed']);
    exit;
}

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['user']['id'])) {
    http_response_code(401);
    echo json_encode(['success' => false, 'message' => 'Login required to create a post']);
    exit;
}

// Rate limiting for post creation
$userId = $_SESSION['user']['id'];
if (!checkRateLimit('post_create_' . $userId, 5, 3600)) { // 5 posts per hour
    secureErrorResponse('Too many posts created. Please wait before creating another post.', 429);
}

$payload = json_decode(file_get_contents('php://input'), true);
if (!is_array($payload) || empty($payload)) {
    $payload = $_POST;
}

// Validate and sanitize inputs
$categoryValidation = validateEnum($payload['post_category'] ?? '', ['room', 'service'], 'post category');
if (!$categoryValidation['valid']) {
    secureErrorResponse($categoryValidation['error'], 400);
}
$postCategory = $categoryValidation['value'];

$locationValidation = validateText($payload['location'] ?? '', 3, 255);
if (!$locationValidation['valid']) {
    secureErrorResponse('Location: ' . $locationValidation['error'], 400);
}
$location = $locationValidation['text'];

$descriptionValidation = validateText($payload['description'] ?? '', 10, 2000, true);
if (!$descriptionValidation['valid']) {
    secureErrorResponse('Description: ' . $descriptionValidation['error'], 400);
}
$description = $descriptionValidation['text'];

$phoneValidation = validatePhone($payload['contact_phone'] ?? '');
if (!$phoneValidation['valid']) {
    secureErrorResponse('Contact phone: ' . $phoneValidation['error'], 400);
}
$contactPhone = $phoneValidation['phone'];

$facilitiesValidation = validateFacilities($payload['facilities'] ?? []);
if (!$facilitiesValidation['valid']) {
    secureErrorResponse('Facilities: ' . $facilitiesValidation['error'], 400);
}
$facilities = $facilitiesValidation['facilities'];

$availabilityTimeValidation = validateText($payload['availability_time'] ?? '', 0, 120);
$availabilityTime = $availabilityTimeValidation['text'];

$serviceType = null;
$accommodationType = null;
$allowedFor = null;
$rentValue = 0;
$facilitiesStr = null;

if ($postCategory === 'room') {
    $accommodationValidation = validateEnum($payload['accommodation_type'] ?? '', ['PG', 'Flat', 'Room', 'Hostel'], 'accommodation type');
    if (!$accommodationValidation['valid']) {
        secureErrorResponse($accommodationValidation['error'], 400);
    }
    $accommodationType = $accommodationValidation['value'];

    $allowedValidation = validateEnum($payload['allowed_for'] ?? '', ['Male', 'Female', 'Family'], 'allowed for');
    if (!$allowedValidation['valid']) {
        secureErrorResponse($allowedValidation['error'], 400);
    }
    $allowedFor = $allowedValidation['value'];

    $rentValidation = validateNumeric($payload['rent_or_price'] ?? '', 1, 100000);
    if (!$rentValidation['valid']) {
        secureErrorResponse('Rent: ' . $rentValidation['error'], 400);
    }
    $rentValue = $rentValidation['value'];

    if (!empty($facilities)) {
        $facilitiesStr = implode(',', $facilities);
    }
    $availabilityTime = null; // not applicable for room posts
} else {
    $serviceValidation = validateEnum($payload['service_type'] ?? '', ['tiffin', 'gas', 'milk', 'sabji', 'other'], 'service type');
    if (!$serviceValidation['valid']) {
        secureErrorResponse($serviceValidation['error'], 400);
    }
    $serviceType = $serviceValidation['value'];

    $rentOrPrice = trim((string) ($payload['rent_or_price'] ?? ''));
    if ($rentOrPrice !== '') {
        $priceValidation = validateNumeric($rentOrPrice, 0, 50000);
        if (!$priceValidation['valid']) {
            secureErrorResponse('Price: ' . $priceValidation['error'], 400);
        }
        $rentValue = $priceValidation['value'];
    }

    if (empty($availabilityTime)) {
        secureErrorResponse('Service availability time is required', 400);
    }
}

$conn = nearby_db_connect();
$userId = (int) $_SESSION['user']['id'];

$sql = 'INSERT INTO posts (user_id, post_category, service_type, accommodation_type, allowed_for, rent_or_price, location, facilities, availability_time, description, contact_phone)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)';
$stmt = mysqli_prepare($conn, $sql);

if ($stmt === false) {
    secureErrorResponse('Failed to create post. Please try again.', 500, 'Failed to prepare post insert statement: ' . mysqli_error($conn));
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
    $postId = mysqli_insert_id($conn);
    echo json_encode([
        'success' => true, 
        'message' => 'Post created successfully',
        'post_id' => $postId
    ]);
} else {
    secureErrorResponse('Unable to save post. Please try again.', 500, 'Failed to execute post insert: ' . mysqli_stmt_error($stmt));
}

mysqli_stmt_close($stmt);
