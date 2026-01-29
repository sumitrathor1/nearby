<?php
/**
 * Fetch Second-Hand Products API
 * Returns filtered list of products based on query parameters
 */

require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../config/security.php';

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET');
header('Access-Control-Allow-Headers: Content-Type');

// Get database connection
$db = getDBConnection();

// Get and sanitize query parameters
$category = isset($_GET['category']) ? sanitizeInput($_GET['category']) : null;
$maxPrice = isset($_GET['max_price']) ? (int)$_GET['max_price'] : null;
$condition = isset($_GET['condition']) ? sanitizeInput($_GET['condition']) : null;
$search = isset($_GET['search']) ? sanitizeInput($_GET['search']) : null;
$limit = isset($_GET['limit']) ? (int)$_GET['limit'] : 20;
$offset = isset($_GET['offset']) ? (int)$_GET['offset'] : 0;

// Build query
$query = "SELECT p.*, u.name as seller_name FROM products p JOIN users u ON p.user_id = u.id WHERE 1=1";
$params = [];
$types = "";

// Add filters
if ($category) {
    $query .= " AND p.category = ?";
    $params[] = $category;
    $types .= "s";
}

if ($maxPrice) {
    $query .= " AND p.price <= ?";
    $params[] = $maxPrice;
    $types .= "i";
}

if ($condition) {
    $query .= " AND p.condition_status = ?";
    $params[] = $condition;
    $types .= "s";
}

if ($search) {
    $query .= " AND (p.title LIKE ? OR p.description LIKE ? OR p.location LIKE ?)";
    $searchTerm = "%$search%";
    $params[] = $searchTerm;
    $params[] = $searchTerm;
    $params[] = $searchTerm;
    $types .= "sss";
}

// Add ordering and pagination
$query .= " ORDER BY p.created_at DESC LIMIT ? OFFSET ?";
$params[] = $limit;
$params[] = $offset;
$types .= "ii";

try {
    $stmt = $db->prepare($query);
    if (!empty($params)) {
        $stmt->bind_param($types, ...$params);
    }
    $stmt->execute();
    $result = $stmt->get_result();

    $products = [];
    while ($row = $result->fetch_assoc()) {
        $products[] = [
            'id' => $row['id'],
            'title' => $row['title'],
            'category' => $row['category'],
            'price' => $row['price'],
            'condition' => $row['condition_status'],
            'location' => $row['location'],
            'description' => $row['description'],
            'contact_phone' => $row['contact_phone'],
            'image_url' => $row['image_url'],
            'seller_name' => $row['seller_name'],
            'created_at' => $row['created_at']
        ];
    }

    // Get total count for pagination
    $countQuery = "SELECT COUNT(*) as total FROM products p WHERE 1=1";
    $countParams = array_slice($params, 0, -2); // Remove limit and offset
    $countTypes = substr($types, 0, -2);

    $countStmt = $db->prepare($countQuery);
    if (!empty($countParams)) {
        $countStmt->bind_param($countTypes, ...$countParams);
    }
    $countStmt->execute();
    $countResult = $countStmt->get_result();
    $total = $countResult->fetch_assoc()['total'];

    echo json_encode([
        'success' => true,
        'products' => $products,
        'total' => $total,
        'limit' => $limit,
        'offset' => $offset
    ]);

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'error' => 'Database error: ' . $e->getMessage()
    ]);
}

$db->close();
?>