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

$email = strtolower(trim($payload['email'] ?? ''));
$idToken = $payload['idToken'] ?? '';

if ($email === '' || $idToken === '') {
    echo json_encode(['success' => false, 'message' => 'Email and Google token are required']);
    exit;
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    echo json_encode(['success' => false, 'message' => 'Enter a valid email address']);
    exit;
}

$firebaseApiKey = 'AIzaSyAHdxzcFs9rFsMozKV8n5QMkD8Tul5HOo8';
$verifyUrl = 'https://identitytoolkit.googleapis.com/v1/accounts:lookup?key=' . urlencode($firebaseApiKey);
$verifyPayload = json_encode(['idToken' => $idToken]);

$context = stream_context_create([
    'http' => [
        'method'  => 'POST',
        'header'  => "Content-Type: application/json\r\n",
        'content' => $verifyPayload,
        'timeout' => 8,
    ],
]);

$verifyResponse = @file_get_contents($verifyUrl, false, $context);
if ($verifyResponse === false) {
    http_response_code(401);
    echo json_encode(['success' => false, 'message' => 'Unable to verify Google sign-in. Please try again.']);
    exit;
}

$verifyData = json_decode($verifyResponse, true);
if (!is_array($verifyData) || empty($verifyData['users'])) {
    http_response_code(401);
    echo json_encode(['success' => false, 'message' => 'Google verification failed.']);
    exit;
}

$emailVerified = false;
foreach ($verifyData['users'] as $verifiedUser) {
    $verifiedEmail = strtolower($verifiedUser['email'] ?? '');
    $isVerified = $verifiedUser['emailVerified'] ?? false;
    if ($verifiedEmail === $email && $isVerified) {
        $emailVerified = true;
        break;
    }
}

if (!$emailVerified) {
    http_response_code(403);
    echo json_encode(['success' => false, 'message' => 'Google account email is not verified or does not match.']);
    exit;
}

$conn = nearby_db_connect();
$sql = 'SELECT id, name, college_email, role, user_type FROM users WHERE college_email = ? LIMIT 1';
$stmt = mysqli_prepare($conn, $sql);
if ($stmt === false) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Failed to prepare user lookup']);
    exit;
}

mysqli_stmt_bind_param($stmt, 's', $email);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$user = mysqli_fetch_assoc($result);
mysqli_stmt_close($stmt);

if (!$user) {
    http_response_code(403);
    echo json_encode(['success' => false, 'message' => 'No NearBy account found for this Google email. Please register first.']);
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
