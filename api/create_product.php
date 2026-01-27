<?php
/**
 * Create Second-Hand Product API
 * Allows authenticated users to post products
 */

require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../config/security.php';

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Content-Type');

startSecureSession();

// Check if user is logged in
if (!isSessionValid() || !isset($_SESSION['user']['id'])) {
    http_response_code(401);
    echo json_encode([
        'success' => false,
        'error' => 'Authentication required'
    ]);
    exit;
}

$userId = $_SESSION['user']['id'];

// Get database connection
$conn = nearby_db_connect();

// Get POST data
$input = json_decode(file_get_contents('php://input'), true);

if (!$input) {
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'error' => 'Invalid JSON input'
    ]);
    exit;
}

// Validate required fields and inputs
$titleValidation = validateText($input['title'] ?? '', 3, 100);
if (!$titleValidation['valid']) {
    http_response_code(400);
    echo json_encode(['success' => false, 'error' => 'Title: ' . $titleValidation['error']]);
    exit;
}
$title = $titleValidation['text'];

$validCategories = ['fridge', 'cooler', 'almirah', 'washing-machine', 'furniture', 'other'];
$categoryValidation = validateEnum($input['category'] ?? '', $validCategories, 'category');
if (!$categoryValidation['valid']) {
    http_response_code(400);
    echo json_encode(['success' => false, 'error' => $categoryValidation['error']]);
    exit;
}
$category = $categoryValidation['value'];

$priceValidation = validateNumeric($input['price'] ?? '', 1, 100000);
if (!$priceValidation['valid']) {
    http_response_code(400);
    echo json_encode(['success' => false, 'error' => 'Price: ' . $priceValidation['error']]);
    exit;
}
$price = $priceValidation['value'];

$validConditions = ['new-like', 'good', 'old'];
$conditionValidation = validateEnum($input['condition'] ?? '', $validConditions, 'condition');
if (!$conditionValidation['valid']) {
    http_response_code(400);
    echo json_encode(['success' => false, 'error' => $conditionValidation['error']]);
    exit;
}
$condition = $conditionValidation['value'];

$locationValidation = validateText($input['location'] ?? '', 3, 100);
if (!$locationValidation['valid']) {
    http_response_code(400);
    echo json_encode(['success' => false, 'error' => 'Location: ' . $locationValidation['error']]);
    exit;
}
$location = $locationValidation['text'];

$descriptionValidation = validateText($input['description'] ?? '', 10, 500, true);
if (!$descriptionValidation['valid']) {
    http_response_code(400);
    echo json_encode(['success' => false, 'error' => 'Description: ' . $descriptionValidation['error']]);
    exit;
}
$description = $descriptionValidation['text'];

$phoneValidation = validatePhone($input['contact_phone'] ?? '');
if (!$phoneValidation['valid']) {
    http_response_code(400);
    echo json_encode(['success' => false, 'error' => 'Contact phone: ' . $phoneValidation['error']]);
    exit;
}
$contactPhone = $phoneValidation['phone'];

$imageUrl = null;
if (!empty($input['image_url'])) {
    // Basic URL validation for image URLs
    $imageUrlValidation = validateText($input['image_url'], 10, 500);
    if ($imageUrlValidation['valid']) {
        $imageUrl = $imageUrlValidation['text'];
    }
}

try {
    // Insert product
    $query = "INSERT INTO products (user_id, title, category, price, condition_status, location, description, contact_phone, image_url) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
    $stmt = mysqli_prepare($conn, $query);
    mysqli_stmt_bind_param($stmt, "ississsss", $userId, $title, $category, $price, $condition, $location, $description, $contactPhone, $imageUrl);

    if (mysqli_stmt_execute($stmt)) {
        $productId = mysqli_stmt_insert_id($stmt);
        echo json_encode([
            'success' => true,
            'product_id' => $productId,
            'message' => 'Product posted successfully'
        ]);
    } else {
        throw new Exception('Failed to insert product');
    }

    mysqli_stmt_close($stmt);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'error' => 'Database error occurred'
    ]);
}

mysqli_close($conn);
?>