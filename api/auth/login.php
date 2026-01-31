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

if (session_status() === PHP_SESSION_NONE) {
	startSecureSession();
}

// Rate limiting for login attempts
$clientIP = $_SERVER['REMOTE_ADDR'] ?? 'unknown';
if (!checkRateLimit('login_' . $clientIP, 10, 300)) { // 10 login attempts per 5 minutes
	secureErrorResponse('Too many login attempts. Please try again later.', 429);
}

$payload = json_decode(file_get_contents('php://input'), true);
if (!is_array($payload) || empty($payload)) {
	$payload = $_POST;
}

try {
	$email = InputValidator::validateEmail($payload['email'] ?? '');
	$password = $payload['password'] ?? '';
	$role = InputValidator::validateRole($payload['role'] ?? '');
	
	if (!$email) {
		echo json_encode(['success' => false, 'message' => 'Enter a valid email address']);
		exit;
	}
	
	if (empty($password)) {
		echo json_encode(['success' => false, 'message' => 'Password is required']);
		exit;
	}
	
} catch (InvalidArgumentException $e) {
	echo json_encode(['success' => false, 'message' => $e->getMessage()]);
	exit;
}
$role = $roleValidation['value'];

$conn = nearby_db_connect();
$emailLower = strtolower($email);

$sql = 'SELECT id, name, college_email, password, role, user_type FROM users WHERE college_email = ? LIMIT 1';
$stmt = mysqli_prepare($conn, $sql);

if ($stmt === false) {
	secureErrorResponse('Login failed. Please try again.', 500, 'Failed to prepare login statement: ' . mysqli_error($conn));
}

mysqli_stmt_bind_param($stmt, 's', $emailLower);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$user = mysqli_fetch_assoc($result);
mysqli_stmt_close($stmt);

if (!$user || !password_verify($password, $user['password']) || $user['role'] !== $role) {
	// Use generic error message to prevent user enumeration
	secureErrorResponse('Invalid email, password, or role', 401);
}

// Regenerate session ID to prevent session fixation
regenerateSessionAfterLogin();

$_SESSION['user'] = [
	'id' => (int) $user['id'],
	'name' => sanitizeInput($user['name']),
	'email' => $user['college_email'],
	'role' => $user['role'],
	'user_type' => $user['user_type'] ?? 'student',
	'login_time' => time(),
];

// Generate CSRF token for future requests
$csrfToken = generateCSRFToken();

$redirect = 'search.php';
if ($user['role'] === 'junior') {
	$redirect = 'junior-dashboard.php';
} elseif ($user['role'] === 'senior') {
	$redirect = 'senior-dashboard.php';
}

echo json_encode([
	'success' => true,
	'message' => 'Login successful',
	'redirect' => $redirect,
	'csrf_token' => $csrfToken,
]);
