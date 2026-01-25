<?php
header('Content-Type: application/json');
require_once __DIR__ . '/../../config/db.php';
require_once __DIR__ . '/../../config/security.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Rate limiting for search requests
$clientIP = $_SERVER['REMOTE_ADDR'] ?? 'unknown';
if (!checkRateLimit('search_' . $clientIP, 60, 60)) { // 60 searches per minute
    secureErrorResponse('Too many search requests. Please wait before searching again.', 429);
}

$conn = nearby_db_connect();

$params = [];
$types = '';
$conditions = [];

// Validate and sanitize search parameters
$category = '';
if (!empty($_GET['post_category']) || !empty($_POST['post_category'])) {
    $categoryValidation = validateEnum($_GET['post_category'] ?? $_POST['post_category'], ['room', 'service'], 'post category');
    if ($categoryValidation['valid']) {
        $category = $categoryValidation['value'];
    }
}

$serviceType = '';
if (!empty($_GET['service_type']) || !empty($_POST['service_type'])) {
    $serviceValidation = validateEnum($_GET['service_type'] ?? $_POST['service_type'], ['tiffin', 'gas', 'milk', 'sabji', 'other'], 'service type');
    if ($serviceValidation['valid']) {
        $serviceType = $serviceValidation['value'];
    }
}

$location = '';
if (!empty($_GET['location']) || !empty($_POST['location'])) {
    $locationValidation = validateText($_GET['location'] ?? $_POST['location'], 0, 255);
    if ($locationValidation['valid']) {
        $location = $locationValidation['text'];
    }
}

$minPrice = 0;
if (!empty($_GET['min_price']) || !empty($_POST['min_price'])) {
    $minPriceValidation = validateNumeric($_GET['min_price'] ?? $_POST['min_price'], 0, 1000000);
    if ($minPriceValidation['valid']) {
        $minPrice = $minPriceValidation['value'];
    }
}

$maxPrice = 0;
if (!empty($_GET['max_price']) || !empty($_POST['max_price'])) {
    $maxPriceValidation = validateNumeric($_GET['max_price'] ?? $_POST['max_price'], 0, 1000000);
    if ($maxPriceValidation['valid']) {
        $maxPrice = $maxPriceValidation['value'];
    }
}

$allowedFor = '';
if (!empty($_GET['allowed_for']) || !empty($_POST['allowed_for'])) {
    $allowedValidation = validateEnum($_GET['allowed_for'] ?? $_POST['allowed_for'], ['Male', 'Female', 'Family'], 'allowed for');
    if ($allowedValidation['valid']) {
        $allowedFor = $allowedValidation['value'];
    }
}

$accommodationType = '';
if (!empty($_GET['accommodation_type']) || !empty($_POST['accommodation_type'])) {
    $accommodationValidation = validateEnum($_GET['accommodation_type'] ?? $_POST['accommodation_type'], ['PG', 'Flat', 'Room', 'Hostel'], 'accommodation type');
    if ($accommodationValidation['valid']) {
        $accommodationType = $accommodationValidation['value'];
    }
}

$sql = 'SELECT p.id, p.post_category, p.service_type, p.accommodation_type, p.allowed_for, p.rent_or_price,
               p.location, p.facilities, p.availability_time, p.description, p.contact_phone, p.created_at,
               u.name AS user_name, u.user_type
        FROM posts p
        INNER JOIN users u ON u.id = p.user_id';

if ($category !== '') {
    $conditions[] = 'p.post_category = ?';
    $params[] = $category;
    $types .= 's';
}

if ($serviceType !== '') {
    $conditions[] = 'p.service_type = ?';
    $params[] = $serviceType;
    $types .= 's';
}

if ($location !== '') {
    $conditions[] = '(p.location LIKE ? OR p.description LIKE ?)';
    $like = '%' . $location . '%';
    $params[] = $like;
    $params[] = $like;
    $types .= 'ss';
}

if ($allowedFor !== '') {
    $conditions[] = 'p.allowed_for = ?';
    $params[] = $allowedFor;
    $types .= 's';
}

if ($accommodationType !== '') {
    $conditions[] = 'p.accommodation_type = ?';
    $params[] = $accommodationType;
    $types .= 's';
}

if ($minPrice > 0) {
    $conditions[] = 'p.rent_or_price >= ?';
    $params[] = $minPrice;
    $types .= 'i';
}

if ($maxPrice > 0) {
    $conditions[] = 'p.rent_or_price <= ?';
    $params[] = $maxPrice;
    $types .= 'i';
}

if ($conditions) {
    $sql .= ' WHERE ' . implode(' AND ', $conditions);
}

$sql .= ' ORDER BY p.created_at DESC LIMIT 100';

if ($params) {
    $stmt = mysqli_prepare($conn, $sql);
    if ($stmt === false) {
        secureErrorResponse('Search failed. Please try again.', 500, 'Failed to prepare search query: ' . mysqli_error($conn));
    }
    mysqli_stmt_bind_param($stmt, $types, ...$params);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
} else {
    $result = mysqli_query($conn, $sql);
}

if (!$result) {
    secureErrorResponse('Search failed. Please try again.', 500, 'Failed to execute search query: ' . mysqli_error($conn));
}

$isLoggedIn = isset($_SESSION['user']['id']);
$posts = [];

while ($row = mysqli_fetch_assoc($result)) {
    // Sanitize output data
    $row['facilities'] = $row['facilities'] !== null && $row['facilities'] !== ''
        ? array_map('sanitizeInput', explode(',', $row['facilities']))
        : [];
    $row['rent_or_price'] = $row['rent_or_price'] !== null ? (int) $row['rent_or_price'] : null;
    $row['created_at'] = $row['created_at'];
    $row['can_view_contact'] = $isLoggedIn;
    
    // Sanitize text fields
    $row['location'] = sanitizeInput($row['location']);
    $row['description'] = sanitizeInput($row['description'], true); // Allow basic HTML
    $row['user_name'] = sanitizeInput($row['user_name']);
    
    if ($row['availability_time']) {
        $row['availability_time'] = sanitizeInput($row['availability_time']);
    }
    
    // Hide contact info for non-logged users
    if (!$isLoggedIn) {
        $row['contact_phone'] = null;
    } else {
        $row['contact_phone'] = sanitizeInput($row['contact_phone']);
    }
    
    $posts[] = $row;
}

if (isset($stmt)) {
    mysqli_stmt_close($stmt);
}

echo json_encode(['success' => true, 'data' => $posts]);
