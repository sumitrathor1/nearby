<?php
header('Content-Type: application/json');
require_once __DIR__ . '/../config/db.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Method not allowed']);
    exit;
}

$raw = file_get_contents('php://input');
$data = json_decode($raw, true);
if (!is_array($data) || empty($data)) {
    $data = $_POST;
}

$conn = nearby_db_connect();

$name = trim($data['name'] ?? '');
$email = trim($data['email'] ?? '');
$password = $data['password'] ?? '';
$role = $data['role'] ?? '';
$userCategory = trim($data['user_category'] ?? '');

if ($name === '' || $email === '' || $password === '' || $role === '' || $userCategory === '') {
    echo json_encode(['success' => false, 'message' => 'All fields are required']);
    exit;
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL) || !str_contains($email, '@')) {
    echo json_encode(['success' => false, 'message' => 'Enter a valid email address']);
    exit;
}

$categoryMap = [
    'student' => 'student',
    'home_owner' => 'owner',
    'room_owner' => 'owner',
    'tiffin' => 'service_provider',
    'gas' => 'service_provider',
    'milk' => 'service_provider',
    'sabji' => 'service_provider',
    'other_service' => 'service_provider',
];

$userType = $categoryMap[strtolower($userCategory)] ?? null;
if ($userType === null) {
    echo json_encode(['success' => false, 'message' => 'Select a valid user category']);
    exit;
}

$allowedDomain = '@mitsgwl.ac.in';
$emailLower = strtolower($email);
if ($userType === 'student' && !str_ends_with($emailLower, $allowedDomain)) {
    echo json_encode(['success' => false, 'message' => 'Students must use their @mitsgwl.ac.in email']);
    exit;
}

if ($userType !== 'student') {
    $role = 'senior';
}

if (!in_array($role, ['junior', 'senior'], true)) {
    echo json_encode(['success' => false, 'message' => 'Invalid role selected']);
    exit;
}

$emailSafe = mysqli_real_escape_string($conn, $emailLower);

$checkSql = 'SELECT id FROM users WHERE college_email = ? LIMIT 1';
$checkStmt = mysqli_prepare($conn, $checkSql);
mysqli_stmt_bind_param($checkStmt, 's', $emailSafe);
mysqli_stmt_execute($checkStmt);
mysqli_stmt_store_result($checkStmt);
if (mysqli_stmt_num_rows($checkStmt) > 0) {
    mysqli_stmt_close($checkStmt);
    echo json_encode(['success' => false, 'message' => 'Email already registered']);
    exit;
}
mysqli_stmt_close($checkStmt);

$hash = password_hash($password, PASSWORD_DEFAULT);
$insertSql = 'INSERT INTO users (name, college_email, password, role, user_type) VALUES (?, ?, ?, ?, ?)';
$insertStmt = mysqli_prepare($conn, $insertSql);
mysqli_stmt_bind_param($insertStmt, 'sssss', $name, $emailSafe, $hash, $role, $userType);

if (mysqli_stmt_execute($insertStmt)) {
    echo json_encode(['success' => true, 'message' => 'Registration successful. You can login now.']);
} else {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Failed to register user']);
}

mysqli_stmt_close($insertStmt);
