<?php
header('Content-Type: application/json');
require_once __DIR__ . '/../config/db.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Method not allowed']);
    exit;
}

require_once __DIR__ . '/../config/security.php';
startSecureSession();

if (!isSessionValid() || ($_SESSION['user']['role'] ?? '') !== 'senior') {
    http_response_code(403);
    echo json_encode(['success' => false, 'message' => 'Only senior students can add guidance tips']);
    exit;
}

$raw = file_get_contents('php://input');
$data = json_decode($raw, true);
if (!is_array($data) || empty($data)) {
    $data = $_POST;
}

$category = trim($data['category'] ?? '');
$title = trim($data['title'] ?? '');
$description = trim($data['description'] ?? '');

if ($category === '' || $title === '' || $description === '') {
    echo json_encode(['success' => false, 'message' => 'All fields are required']);
    exit;
}

$conn = nearby_db_connect();
$sql = 'INSERT INTO guidance (category, title, description) VALUES (?, ?, ?)';
$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, 'sss', $category, $title, $description);

if (mysqli_stmt_execute($stmt)) {
    echo json_encode(['success' => true, 'message' => 'Guidance added successfully']);
} else {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Failed to add guidance']);
}

mysqli_stmt_close($stmt);
