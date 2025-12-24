<?php
header('Content-Type: application/json');
require_once __DIR__ . '/../../config/db.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
	http_response_code(405);
	echo json_encode(['success' => false, 'message' => 'Method not allowed']);
	exit;
}

$payload = json_decode(file_get_contents('php://input'), true);
if (!is_array($payload) || empty($payload)) {
	$payload = $_POST;
}

$name = trim($payload['name'] ?? '');
$email = trim($payload['email'] ?? '');
$password = $payload['password'] ?? '';
$role = $payload['role'] ?? '';

if ($name === '' || $email === '' || $password === '' || $role === '') {
	echo json_encode(['success' => false, 'message' => 'All fields are required']);
	exit;
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
	echo json_encode(['success' => false, 'message' => 'Enter a valid email']);
	exit;
}

if (!in_array($role, ['junior', 'senior'], true)) {
	echo json_encode(['success' => false, 'message' => 'Invalid role']);
	exit;
}

$conn = nearby_db_connect();
$emailLower = strtolower($email);

$checkSql = 'SELECT id FROM users WHERE college_email = ? LIMIT 1';
$checkStmt = mysqli_prepare($conn, $checkSql);
mysqli_stmt_bind_param($checkStmt, 's', $emailLower);
mysqli_stmt_execute($checkStmt);
mysqli_stmt_store_result($checkStmt);

if (mysqli_stmt_num_rows($checkStmt) > 0) {
	mysqli_stmt_close($checkStmt);
	echo json_encode(['success' => false, 'message' => 'Email already registered']);
	exit;
}

mysqli_stmt_close($checkStmt);

$hash = password_hash($password, PASSWORD_DEFAULT);
$insertSql = 'INSERT INTO users (name, college_email, password, role) VALUES (?, ?, ?, ?)';
$insertStmt = mysqli_prepare($conn, $insertSql);
mysqli_stmt_bind_param($insertStmt, 'ssss', $name, $emailLower, $hash, $role);

if (mysqli_stmt_execute($insertStmt)) {
	echo json_encode(['success' => true, 'message' => 'Registration successful. You can login now.']);
} else {
	http_response_code(500);
	echo json_encode(['success' => false, 'message' => 'Failed to register user']);
}

mysqli_stmt_close($insertStmt);
