<?php
header('Content-Type: application/json');
require_once __DIR__ . '/../../config/db.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
	http_response_code(405);
	echo json_encode(['success' => false, 'message' => 'Method not allowed']);
	exit;
}

if (session_status() === PHP_SESSION_NONE) {
	session_start();
}

$payload = json_decode(file_get_contents('php://input'), true);
if (!is_array($payload) || empty($payload)) {
	$payload = $_POST;
}

$email = trim($payload['email'] ?? '');
$password = $payload['password'] ?? '';
$role = $payload['role'] ?? '';

if ($email === '' || $password === '' || $role === '') {
	echo json_encode(['success' => false, 'message' => 'Email, password, and role are required']);
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

$sql = 'SELECT id, name, college_email, password, role FROM users WHERE college_email = ? LIMIT 1';
$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, 's', $emailLower);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$user = mysqli_fetch_assoc($result);
mysqli_stmt_close($stmt);

if (!$user || !password_verify($password, $user['password']) || $user['role'] !== $role) {
	echo json_encode(['success' => false, 'message' => 'Invalid credentials']);
	exit;
}

$_SESSION['user'] = [
	'id' => (int) $user['id'],
	'name' => $user['name'],
	'email' => $user['college_email'],
	'role' => $user['role'],
];

echo json_encode([
	'success' => true,
	'message' => 'Login successful',
	'redirect' => $role === 'junior' ? 'junior-dashboard.php' : 'senior-dashboard.php',
]);
