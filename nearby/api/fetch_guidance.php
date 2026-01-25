<?php
header('Content-Type: application/json');
require_once __DIR__ . '/../config/db.php';

$conn = nearby_db_connect();
$category = trim($_GET['category'] ?? $_POST['category'] ?? '');

$sql = 'SELECT id, category, title, description FROM guidance';
$params = [];
$types = '';

if ($category !== '') {
    $sql .= ' WHERE category = ?';
    $params[] = $category;
    $types .= 's';
}

$sql .= ' ORDER BY id DESC LIMIT 50';

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
    echo json_encode(['success' => false, 'message' => 'Failed to load guidance']);
    exit;
}

$items = [];
while ($row = mysqli_fetch_assoc($result)) {
    $items[] = $row;
}

if (isset($stmt)) {
    mysqli_stmt_close($stmt);
}

echo json_encode(['success' => true, 'data' => $items]);
