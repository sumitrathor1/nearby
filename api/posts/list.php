<?php
header('Content-Type: application/json');
require_once __DIR__ . '/../../config/db.php';
require_once __DIR__ . '/../../includes/helpers/session.php';

secureSessionStart();

$conn = nearby_db_connect();

$params = [];
$types = '';
$conditions = [];

$category = strtolower(trim($_GET['post_category'] ?? $_POST['post_category'] ?? ''));
$serviceType = strtolower(trim($_GET['service_type'] ?? $_POST['service_type'] ?? ''));
$location = trim($_GET['location'] ?? $_POST['location'] ?? '');
$minPrice = trim($_GET['min_price'] ?? $_POST['min_price'] ?? '');
$maxPrice = trim($_GET['max_price'] ?? $_POST['max_price'] ?? '');
$allowedFor = trim($_GET['allowed_for'] ?? $_POST['allowed_for'] ?? '');
$accommodationType = trim($_GET['accommodation_type'] ?? $_POST['accommodation_type'] ?? '');

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

if ($minPrice !== '' && is_numeric($minPrice)) {
    $conditions[] = 'p.rent_or_price >= ?';
    $params[] = (int) $minPrice;
    $types .= 'i';
}

if ($maxPrice !== '' && is_numeric($maxPrice)) {
    $conditions[] = 'p.rent_or_price <= ?';
    $params[] = (int) $maxPrice;
    $types .= 'i';
}

if ($conditions) {
    $sql .= ' WHERE ' . implode(' AND ', $conditions);
}

$sql .= ' ORDER BY p.created_at DESC LIMIT 100';

if ($params) {
    $stmt = mysqli_prepare($conn, $sql);
    if ($stmt === false) {
        http_response_code(500);
        echo json_encode(['success' => false, 'message' => 'Failed to prepare query']);
        exit;
    }
    mysqli_stmt_bind_param($stmt, $types, ...$params);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
} else {
    $result = mysqli_query($conn, $sql);
}

if (!$result) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Failed to fetch posts']);
    exit;
}

$isLoggedIn = isset($_SESSION['user']['id']);
$posts = [];

while ($row = mysqli_fetch_assoc($result)) {
    $row['facilities'] = $row['facilities'] !== null && $row['facilities'] !== ''
        ? explode(',', $row['facilities'])
        : [];
    $row['rent_or_price'] = $row['rent_or_price'] !== null ? (int) $row['rent_or_price'] : null;
    $row['created_at'] = $row['created_at'];
    $row['can_view_contact'] = $isLoggedIn;
    if (!$isLoggedIn) {
        $row['contact_phone'] = null;
    }
    $posts[] = $row;
}

if (isset($stmt)) {
    mysqli_stmt_close($stmt);
}

echo json_encode(['success' => true, 'data' => $posts]);
