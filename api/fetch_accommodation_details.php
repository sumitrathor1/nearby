<?php
header('Content-Type: application/json');
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../includes/helpers/authorization.php';

$id = $_GET['id'] ?? $_POST['id'] ?? null;
$id = validateId($id, 'accommodation ID');

$conn = nearby_db_connect();

// Verify accommodation exists
if (!accommodationExists($conn, $id)) {
    http_response_code(404);
    echo json_encode(['success' => false, 'message' => 'Accommodation not found']);
    mysqli_close($conn);
    exit;
}

$sql = 'SELECT a.id, a.title, a.type, a.allowed_for, a.rent, a.location, a.facilities, a.description, a.created_at,
               u.name AS owner_name, u.college_email AS owner_email
        FROM accommodations a
        INNER JOIN users u ON u.id = a.user_id
        WHERE a.id = ? LIMIT 1';

$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, 'i', $id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$accommodation = mysqli_fetch_assoc($result);
mysqli_stmt_close($stmt);

if (!$accommodation) {
    http_response_code(404);
    echo json_encode(['success' => false, 'message' => 'Accommodation not found']);
    exit;
}

$accommodation['rent'] = (int) $accommodation['rent'];
$accommodation['facilities'] = $accommodation['facilities'] !== null && $accommodation['facilities'] !== ''
    ? explode(',', $accommodation['facilities'])
    : [];

$accommodation['is_verified'] = true;
$accommodation['monthly_rent'] = $accommodation['rent'];

echo json_encode(['success' => true, 'data' => $accommodation]);
