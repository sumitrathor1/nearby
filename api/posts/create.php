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

if (!isSessionValid() || !isset($_SESSION['user']['id'])) {
    http_response_code(401);
    echo json_encode(['success' => false, 'message' => 'Login required to create a post']);
    exit;
}

$userId = $_SESSION['user']['id'];
$userType = $_SESSION['user']['user_type'] ?? 'student';

// Authorization check: Verify user has permission to create this type of post
if ($payload['post_category'] === 'room') {
    // Only owners can create room posts
    $allowedOwnerTypes = ['home_owner', 'room_owner'];
    if (!in_array($userType, $allowedOwnerTypes)) {
        http_response_code(403);
        echo json_encode(['success' => false, 'message' => 'Only property owners can create room listings']);
        exit;
    }
} elseif ($payload['post_category'] === 'service') {
    // Only service providers can create service posts
    $allowedServiceTypes = ['tiffin', 'gas', 'milk', 'sabji', 'other_service'];
    if (!in_array($userType, $allowedServiceTypes)) {
        http_response_code(403);
        echo json_encode(['success' => false, 'message' => 'Only service providers can create service listings']);
        exit;
    }

    // Additional check: Service type must match user's service type
    $serviceType = $payload['service_type'] ?? '';
    if ($serviceType && $userType !== 'other_service' && $userType !== $serviceType) {
        http_response_code(403);
        echo json_encode(['success' => false, 'message' => 'You can only create listings for your service type']);
        exit;
    }
}

// Rate limiting for post creation
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
