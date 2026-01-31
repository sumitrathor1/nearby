<?php
header('Content-Type: application/json');
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../includes/helpers/csrf.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Method not allowed']);
    exit;
}

// Validate CSRF token
requireCSRFToken();

$raw = file_get_contents('php://input');
$data = json_decode($raw, true);
if (!is_array($data) || empty($data)) {
    $data = $_POST;
}

$name = trim($data['name'] ?? '');
$email = trim($data['email'] ?? '');
$password = $data['password'] ?? '';
$role = $data['role'] ?? '';
$userCategory = trim($data['user_category'] ?? '');

// Validate name
$nameValidation = validateText($name, 2, 100);
if (!$nameValidation['valid']) {
    echo json_encode(['success' => false, 'message' => $nameValidation['error']]);
    exit;
}
$name = $nameValidation['text'];

// Validate email
$emailValidation = validateEmail($email);
if (!$emailValidation['valid']) {
    echo json_encode(['success' => false, 'message' => $emailValidation['error']]);
    exit;
}
$email = $emailValidation['email'];

// Validate password
$passwordValidation = validatePassword($password);
if (!$passwordValidation['valid']) {
    echo json_encode(['success' => false, 'message' => $passwordValidation['error']]);
    exit;
}
$password = $passwordValidation['password'];

// Validate user category
$allowedCategories = ['student', 'home_owner', 'room_owner', 'tiffin', 'gas', 'milk', 'sabji', 'other_service'];
$categoryValidation = validateEnum($userCategory, $allowedCategories, 'user category');
if (!$categoryValidation['valid']) {
    echo json_encode(['success' => false, 'message' => $categoryValidation['error']]);
    exit;
}
$userCategory = $categoryValidation['value'];

// Validate role
$allowedRoles = ['junior', 'senior'];
$roleValidation = validateEnum($role, $allowedRoles, 'role');
if (!$roleValidation['valid']) {
    echo json_encode(['success' => false, 'message' => $roleValidation['error']]);
    exit;
}
$role = $roleValidation['value'];

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
    echo json_encode(['success' => false, 'message' => 'Invalid user category']);
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

$conn = nearby_db_connect();

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
