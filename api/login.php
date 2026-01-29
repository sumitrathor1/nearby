<?php
header('Content-Type: application/json');
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../config/security.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Method not allowed']);
    exit;
}

startSecureSession();

$raw = file_get_contents('php://input');
$data = json_decode($raw, true);
if (!is_array($data) || empty($data)) {
    $data = $_POST;
}

$email = trim($data['email'] ?? '');
$password = $data['password'] ?? '';
$role = $data['role'] ?? '';

// Validate email
$emailValidation = validateEmail($email);
if (!$emailValidation['valid']) {
    echo json_encode(['success' => false, 'message' => $emailValidation['error']]);
    exit;
}
$email = $emailValidation['email'];

// Validate password (basic check for login)
if (empty($password)) {
    echo json_encode(['success' => false, 'message' => 'Password is required']);
    exit;
}

// Validate role
$allowedRoles = ['junior', 'senior'];
$roleValidation = validateEnum($role, $allowedRoles, 'role');
if (!$roleValidation['valid']) {
    echo json_encode(['success' => false, 'message' => $roleValidation['error']]);
    exit;
}
$role = $roleValidation['value'];

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

$_SESSION['user'] = [
    'id' => (int) $user['id'],
    'name' => $user['name'],
    'email' => $user['college_email'],
    'role' => $user['role'],
    'user_type' => $user['user_type'] ?? 'student',
];

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
