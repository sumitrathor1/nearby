<?php
require_once '../../config/db.php';
header('Content-Type: application/json');

$post_id = isset($_GET['post_id']) ? (int) $_GET['post_id'] : 0;

if ($post_id <= 0) {
    echo json_encode(["success" => false]);
    exit;
}

// 1️⃣ Get average rating + total count
$avgStmt = $conn->prepare("
    SELECT 
        ROUND(AVG(rating),1) as avg_rating, 
        COUNT(*) as total_reviews
    FROM reviews 
    WHERE post_id = ?
");
$avgStmt->bind_param("i", $post_id);
$avgStmt->execute();
$avgResult = $avgStmt->get_result()->fetch_assoc();

// 2️⃣ Get review list
$stmt = $conn->prepare("
    SELECT r.rating, r.review, r.created_at, u.name 
    FROM reviews r
    JOIN users u ON r.user_id = u.id
    WHERE r.post_id = ?
    ORDER BY r.created_at DESC
");
$stmt->bind_param("i", $post_id);
$stmt->execute();
$result = $stmt->get_result();

$reviews = [];
while ($row = $result->fetch_assoc()) {
    $reviews[] = $row;
}

// If no reviews, avoid null average
$average = $avgResult['avg_rating'] ?? 0;
$total   = $avgResult['total_reviews'] ?? 0;

echo json_encode([
    "success" => true,
    "average" => $average,
    "total" => $total,
    "reviews" => $reviews
]);
?>
