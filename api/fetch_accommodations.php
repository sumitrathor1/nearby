<?php
header('Content-Type: application/json');
require_once __DIR__ . '/../config/db.php';

$conn = nearby_db_connect();

$params = [];
$types = '';
$conditions = [];

$query = trim($_GET['query'] ?? $_POST['query'] ?? '');
$type = trim($_GET['type'] ?? $_POST['type'] ?? '');
$allowedFor = trim($_GET['allowed_for'] ?? $_POST['allowed_for'] ?? '');
$maxRent = trim($_GET['max_rent'] ?? $_POST['max_rent'] ?? '');
$minRent = trim($_GET['min_rent'] ?? $_POST['min_rent'] ?? '');
$facilities = $_GET['facilities'] ?? $_POST['facilities'] ?? [];

$sql = 'SELECT a.id, a.type, a.allowed_for, a.rent, a.location, a.facilities, a.description, a.created_at, u.name AS owner_name
        FROM accommodations a
        INNER JOIN users u ON u.id = a.user_id';

if ($query !== '') {
    $conditions[] = '(a.location LIKE ? OR a.description LIKE ?)';
    $like = '%' . $query . '%';
    $params[] = $like;
    $params[] = $like;
    $types .= 'ss';
}

if ($type !== '') {
    $conditions[] = 'a.type = ?';
    $params[] = $type;
    $types .= 's';
}

if ($allowedFor !== '') {
    $conditions[] = 'a.allowed_for = ?';
    $params[] = $allowedFor;
    $types .= 's';
}

if ($minRent !== '' && is_numeric($minRent)) {
    $conditions[] = 'a.rent >= ?';
    $params[] = (int) $minRent;
    $types .= 'i';
}

if ($maxRent !== '' && is_numeric($maxRent)) {
    $conditions[] = 'a.rent <= ?';
    $params[] = (int) $maxRent;
    $types .= 'i';
}

if (!empty($facilities) && is_array($facilities)) {
    foreach ($facilities as $facility) {
        $conditions[] = 'FIND_IN_SET(?, a.facilities)';
        $params[] = $facility;
        $types .= 's';
    }
}

if ($conditions) {
    $sql .= ' WHERE ' . implode(' AND ', $conditions);
}

$sql .= ' ORDER BY a.created_at DESC LIMIT 100';

if ($params) {
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, $types, ...$params);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
} else {
    $result = mysqli_query($conn, $sql);
}

if (!$result) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Failed to fetch accommodations']);
    exit;
}

$listings = [];
while ($row = mysqli_fetch_assoc($result)) {
    $row['facilities'] = $row['facilities'] !== null && $row['facilities'] !== ''
        ? explode(',', $row['facilities'])
        : [];
    $row['rent'] = (int) $row['rent'];
    $row['monthly_rent'] = $row['rent'];
    $row['is_verified'] = true;
    $listings[] = $row;
}

if (isset($stmt)) {
    mysqli_stmt_close($stmt);
}

echo json_encode(['success' => true, 'data' => $listings]);
