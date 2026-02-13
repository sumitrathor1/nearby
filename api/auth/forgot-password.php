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
    $email = InputValidator::validateEmail($payload['email'] ?? '');
    
    if (!$email) {
        echo json_encode(['success' => false, 'message' => 'Enter a valid email address']);
        exit;
    }
    
} catch (InvalidArgumentException $e) {
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
    exit;
}

$conn = nearby_db_connect();
$emailLower = strtolower($email);

// Check if user exists
$sql = 'SELECT id FROM users WHERE college_email = ? LIMIT 1';
$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, 's', $emailLower);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$userExists = mysqli_fetch_assoc($result);
mysqli_stmt_close($stmt);

// In a real application, you would generate a unique token, save it in the database with an expiry,
// and send an email to the user with a link like: reset-password.php?token=...
// For this local demo, we will simulate success if the user exists.

if ($userExists) {
    echo json_encode([
        'success' => true,
        'message' => 'Instructions to reset your password have been sent to ' . htmlspecialchars($email) . '. (Simulated for demo)'
    ]);
} else {
    // Usually, for security, you'd show the same message even if the user doesn't exist 
    // to prevent email enumeration. But for a student project, a clearer error might be helpful.
    echo json_encode([
        'success' => false,
        'message' => 'No account found with that email address.'
    ]);
}
