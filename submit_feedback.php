<?php
require_once __DIR__ . '/config/db.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: feedback.php');
    exit;
}

// Step 1: get database connection
$conn = nearby_db_connect();

// Step 2: read form values safely
$name = $_POST['name'] ?? '';
$email = $_POST['email'] ?? '';
$feedback_type = $_POST['feedback_type'] ?? '';
$message = $_POST['message'] ?? '';

// Step 3: basic validation
if (empty($feedback_type) || empty($message)) {
    header('Location: feedback.php?error=1');
    exit;
}

// Step 4: prepare SQL query
$sql = "INSERT INTO feedback (name, email, feedback_type, message)
        VALUES (?, ?, ?, ?)";

$stmt = mysqli_prepare($conn, $sql);

// Step 5: bind values
mysqli_stmt_bind_param(
    $stmt,
    "ssss",
    $name,
    $email,
    $feedback_type,
    $message
);

// Step 6: execute query
if (mysqli_stmt_execute($stmt)) {
    header('Location: feedback.php?success=1');
} else {
    header('Location: feedback.php?error=1');
}

exit;
