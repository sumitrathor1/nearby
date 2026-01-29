<?php
require_once __DIR__ . '/config/db.php';
require_once __DIR__ . '/config/security.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: contact.php');
    exit;
}

$name = sanitizeInput($_POST['name'] ?? '');
$emailValidation = validateEmail($_POST['email'] ?? '');
if (!$emailValidation['valid']) {
    header('Location: contact.php?error=1');
    exit;
}
$email = $emailValidation['email'];
$subject = sanitizeInput($_POST['subject'] ?? '');
$messageValidation = validateText($_POST['message'] ?? '', 1, 1000);
if (!$messageValidation['valid']) {
    header('Location: contact.php?error=1');
    exit;
}
$message = $messageValidation['text'];

$conn = nearby_db_connect();
$sql = "INSERT INTO contact (name, email, subject, message) VALUES (?, ?, ?, ?)";
$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, 'ssss', $name, $email, $subject, $message);

if (mysqli_stmt_execute($stmt)) {
    header('Location: contact.php?success=1');
} else {
    header('Location: contact.php?error=1');
}
exit;