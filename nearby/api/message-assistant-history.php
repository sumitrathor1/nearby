<?php
header('Content-Type: application/json');
require_once __DIR__ . '/../config/db.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (empty($_SESSION['user']['id'])) {
    http_response_code(401);
    echo json_encode(['success' => false, 'message' => 'Please login to use the chat assistant.']);
    exit;
}

$conn = nearby_db_connect();
$userId = (int) $_SESSION['user']['id'];

$sql = 'SELECT sender, message, created_at FROM chatbot_messages WHERE user_id = ? ORDER BY created_at ASC';
$stmt = mysqli_prepare($conn, $sql);
if ($stmt === false) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Failed to prepare chat history lookup.']);
    exit;
}

mysqli_stmt_bind_param($stmt, 'i', $userId);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

$messages = [];
while ($row = mysqli_fetch_assoc($result)) {
    $messages[] = [
        'sender' => $row['sender'],
        'message' => trim(preg_replace('/\s+/u', ' ', strip_tags($row['message'] ?? ''))),
        'created_at' => $row['created_at'],
    ];
}

mysqli_stmt_close($stmt);

echo json_encode([
    'success' => true,
    'messages' => $messages,
]);
