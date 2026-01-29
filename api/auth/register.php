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

<?php
header('Content-Type: application/json');
require_once __DIR__ . '/../../config/db.php';
require_once __DIR__ . '/../../includes/helpers/validation.php';
require_once __DIR__ . '/../../includes/helpers/csrf.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
	http_response_code(405);
	echo json_encode(['success' => false, 'message' => 'Method not allowed']);
	exit;
}

// Validate CSRF token
requireCSRFToken();

$payload = json_decode(file_get_contents('php://input'), true);
if (!is_array($payload) || empty($payload)) {
	$payload = $_POST;
}

try {
	$name = InputValidator::validateName($payload['name'] ?? '');
	$email = InputValidator::validateEmail($payload['email'] ?? '');
	$password = InputValidator::validatePassword($payload['password'] ?? '');
	$role = InputValidator::validateRole($payload['role'] ?? '');
	$userCategory = InputValidator::validateUserCategory($payload['user_category'] ?? '');
	
	if (!$email) {
		echo json_encode(['success' => false, 'message' => 'Enter a valid email address']);
		exit;
	}
	
} catch (InvalidArgumentException $e) {
	echo json_encode(['success' => false, 'message' => $e->getMessage()]);
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

$conn = nearby_db_connect();

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
$insertSql = 'INSERT INTO users (name, college_email, password, role, user_type) VALUES (?, ?, ?, ?, ?)';
$insertStmt = mysqli_prepare($conn, $insertSql);
mysqli_stmt_bind_param($insertStmt, 'sssss', $name, $emailLower, $hash, $role, $userType);

if (mysqli_stmt_execute($insertStmt)) {
	echo json_encode(['success' => true, 'message' => 'Registration successful. You can login now.']);
} else {
	http_response_code(500);
	echo json_encode(['success' => false, 'message' => 'Failed to register user']);
}

mysqli_stmt_close($insertStmt);
