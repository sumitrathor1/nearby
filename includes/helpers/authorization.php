<?php
/**
 * Authorization Helper Functions
 * Provides resource ownership verification to prevent IDOR vulnerabilities
 */

require_once __DIR__ . '/session.php';

/**
 * Get current logged-in user ID
 * @return int|null User ID or null if not logged in
 */
function getCurrentUserId() {
    secureSessionStart();
    return isset($_SESSION['user']['id']) ? (int) $_SESSION['user']['id'] : null;
}

/**
 * Get current user's role
 * @return string|null User role or null if not logged in
 */
function getCurrentUserRole() {
    secureSessionStart();
    return $_SESSION['user']['role'] ?? null;
}

/**
 * Verify if current user owns a post
 * @param mysqli $conn Database connection
 * @param int $postId Post ID to check
 * @return bool True if user owns the post, false otherwise
 */
function verifyPostOwnership($conn, $postId) {
    $userId = getCurrentUserId();
    if (!$userId) {
        return false;
    }
    
    $sql = 'SELECT user_id FROM posts WHERE id = ? LIMIT 1';
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, 'i', $postId);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $post = mysqli_fetch_assoc($result);
    mysqli_stmt_close($stmt);
    
    if (!$post) {
        return false;
    }
    
    return (int) $post['user_id'] === $userId;
}

/**
 * Verify if current user owns an accommodation
 * @param mysqli $conn Database connection
 * @param int $accommodationId Accommodation ID to check
 * @return bool True if user owns the accommodation, false otherwise
 */
function verifyAccommodationOwnership($conn, $accommodationId) {
    $userId = getCurrentUserId();
    if (!$userId) {
        return false;
    }
    
    $sql = 'SELECT user_id FROM accommodations WHERE id = ? LIMIT 1';
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, 'i', $accommodationId);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $accommodation = mysqli_fetch_assoc($result);
    mysqli_stmt_close($stmt);
    
    if (!$accommodation) {
        return false;
    }
    
    return (int) $accommodation['user_id'] === $userId;
}

/**
 * Verify if current user owns a guidance tip
 * @param mysqli $conn Database connection
 * @param int $guidanceId Guidance ID to check
 * @return bool True if user owns the guidance, false otherwise
 */
function verifyGuidanceOwnership($conn, $guidanceId) {
    $userId = getCurrentUserId();
    if (!$userId) {
        return false;
    }
    
    $sql = 'SELECT user_id FROM guidance WHERE id = ? LIMIT 1';
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, 'i', $guidanceId);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $guidance = mysqli_fetch_assoc($result);
    mysqli_stmt_close($stmt);
    
    if (!$guidance) {
        return false;
    }
    
    return (int) $guidance['user_id'] === $userId;
}

/**
 * Verify if current user owns a contact request
 * @param mysqli $conn Database connection
 * @param int $requestId Contact request ID to check
 * @return bool True if user owns the request, false otherwise
 */
function verifyContactRequestOwnership($conn, $requestId) {
    $userId = getCurrentUserId();
    if (!$userId) {
        return false;
    }
    
    $sql = 'SELECT requester_id FROM contact_requests WHERE id = ? LIMIT 1';
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, 'i', $requestId);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $request = mysqli_fetch_assoc($result);
    mysqli_stmt_close($stmt);
    
    if (!$request) {
        return false;
    }
    
    return (int) $request['requester_id'] === $userId;
}

/**
 * Verify if accommodation exists (for viewing - no ownership required)
 * @param mysqli $conn Database connection
 * @param int $accommodationId Accommodation ID to check
 * @return bool True if accommodation exists, false otherwise
 */
function accommodationExists($conn, $accommodationId) {
    $sql = 'SELECT id FROM accommodations WHERE id = ? LIMIT 1';
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, 'i', $accommodationId);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $exists = mysqli_fetch_assoc($result);
    mysqli_stmt_close($stmt);
    
    return (bool) $exists;
}

/**
 * Verify if post exists (for viewing - no ownership required)
 * @param mysqli $conn Database connection
 * @param int $postId Post ID to check
 * @return bool True if post exists, false otherwise
 */
function postExists($conn, $postId) {
    $sql = 'SELECT id FROM posts WHERE id = ? LIMIT 1';
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, 'i', $postId);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $exists = mysqli_fetch_assoc($result);
    mysqli_stmt_close($stmt);
    
    return (bool) $exists;
}

/**
 * Require user to be logged in
 * Sends 401 response and exits if not logged in
 */
function requireLogin() {
    if (!getCurrentUserId()) {
        http_response_code(401);
        header('Content-Type: application/json');
        echo json_encode([
            'success' => false,
            'error' => 'Authentication required. Please log in to continue.'
        ]);
        exit;
    }
}

/**
 * Require user to have a specific role
 * @param string $requiredRole Required role (e.g., 'senior', 'junior')
 */
function requireRole($requiredRole) {
    requireLogin();
    $userRole = getCurrentUserRole();
    
    if ($userRole !== $requiredRole) {
        http_response_code(403);
        header('Content-Type: application/json');
        echo json_encode([
            'success' => false,
            'error' => 'Access denied. Insufficient permissions.'
        ]);
        exit;
    }
}

/**
 * Require ownership of a resource or send 403 response
 * @param bool $ownsResource Result from ownership verification function
 * @param string $resourceType Type of resource (for error message)
 */
function requireOwnership($ownsResource, $resourceType = 'resource') {
    if (!$ownsResource) {
        http_response_code(403);
        header('Content-Type: application/json');
        echo json_encode([
            'success' => false,
            'error' => "Access denied. You don't have permission to modify this {$resourceType}."
        ]);
        exit;
    }
}

/**
 * Verify resource exists or send 404 response
 * @param bool $exists Result from existence check function
 * @param string $resourceType Type of resource (for error message)
 */
function requireResourceExists($exists, $resourceType = 'resource') {
    if (!$exists) {
        http_response_code(404);
        header('Content-Type: application/json');
        echo json_encode([
            'success' => false,
            'error' => ucfirst($resourceType) . ' not found.'
        ]);
        exit;
    }
}

/**
 * Sanitize and validate numeric ID from input
 * @param mixed $id ID to validate
 * @param string $fieldName Field name for error message
 * @return int Validated ID
 */
function validateId($id, $fieldName = 'ID') {
    if (!$id || !is_numeric($id)) {
        http_response_code(400);
        header('Content-Type: application/json');
        echo json_encode([
            'success' => false,
            'error' => "Invalid {$fieldName}. Please provide a valid numeric identifier."
        ]);
        exit;
    }
    return (int) $id;
}

/**
 * Check if user can access accommodation (exists check for viewing)
 * @param mysqli $conn Database connection
 * @param int $accommodationId Accommodation ID
 * @return bool True if can access
 */
function canAccessAccommodation($conn, $accommodationId) {
    return accommodationExists($conn, $accommodationId);
}

/**
 * Check if user can modify accommodation (ownership check)
 * @param mysqli $conn Database connection
 * @param int $accommodationId Accommodation ID
 * @return bool True if can modify
 */
function canModifyAccommodation($conn, $accommodationId) {
    return verifyAccommodationOwnership($conn, $accommodationId);
}

/**
 * Check if user can access post (exists check for viewing)
 * @param mysqli $conn Database connection
 * @param int $postId Post ID
 * @return bool True if can access
 */
function canAccessPost($conn, $postId) {
    return postExists($conn, $postId);
}

/**
 * Check if user can modify post (ownership check)
 * @param mysqli $conn Database connection
 * @param int $postId Post ID
 * @return bool True if can modify
 */
function canModifyPost($conn, $postId) {
    return verifyPostOwnership($conn, $postId);
}
