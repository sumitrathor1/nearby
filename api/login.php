<?php
header('Content-Type: application/json');
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../includes/helpers/csrf.php';
require_once __DIR__ . '/../includes/helpers/session.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Method not allowed']);
    exit;
}

// Validate CSRF token
requireCSRFToken();

secureSessionStart();

$raw = file_get_contents('php://input');
$data = json_decode($raw, true);
if (!is_array($data) || empty($data)) {
    $data = $_POST;
}

$email = trim($data['email'] ?? '');
$password = $data['password'] ?? '';
$role = $data['role'] ?? '';

if ($email === '' || $password === '' || $role === '') {
    echo json_encode(['success' => false, 'message' => 'All fields are required']);
    exit;
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    echo json_encode(['success' => false, 'message' => 'Enter a valid email address']);
    exit;
}

if (!in_array($role, ['junior', 'senior'], true)) {
    echo json_encode(['success' => false, 'message' => 'Invalid role']);
    exit;
}

$conn = nearby_db_connect();
$emailLower = strtolower($email);

$sql = 'SELECT id, name, college_email, password, role, user_type FROM users WHERE college_email = ? LIMIT 1';
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

// Set secure session with regeneration
setUserSession([
    'id' => (int) $user['id'],
    'name' => $user['name'],
    'email' => $user['college_email'],
    'role' => $user['role'],
    'user_type' => $user['user_type'] ?? 'student',
]);

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
]);
