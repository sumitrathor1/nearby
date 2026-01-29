<?php
/**
 * CSRF Protection Helper Functions
 * Provides token generation and validation for Cross-Site Request Forgery protection
 */

require_once __DIR__ . '/session.php';

/**
 * Generate a CSRF token and store it in the session
 * @return string The generated CSRF token
 */
function generateCSRFToken() {
    secureSessionStart();
    
    // Generate a random token
    $token = bin2hex(random_bytes(32));
    
    // Store in session
    $_SESSION['csrf_token'] = $token;
    $_SESSION['csrf_token_time'] = time();
    
    return $token;
}

/**
 * Get the current CSRF token from session or generate a new one
 * @return string The CSRF token
 */
function getCSRFToken() {
    secureSessionStart();
    
    // Check if token exists and is not expired (1 hour lifetime)
    if (isset($_SESSION['csrf_token']) && isset($_SESSION['csrf_token_time'])) {
        if (time() - $_SESSION['csrf_token_time'] < 3600) {
            return $_SESSION['csrf_token'];
        }
    }
    
    // Generate new token if expired or doesn't exist
    return generateCSRFToken();
}

/**
 * Validate CSRF token from request
 * @param string $token The token to validate (optional, will check POST/headers if not provided)
 * @return bool True if valid, false otherwise
 */
function validateCSRFToken($token = null) {
    secureSessionStart();
    
    // Get token from parameter, POST data, or headers
    if ($token === null) {
        $token = $_POST['csrf_token'] ?? $_SERVER['HTTP_X_CSRF_TOKEN'] ?? null;
    }
    
    // Check if token exists in request
    if (!$token) {
        return false;
    }
    
    // Check if session token exists
    if (!isset($_SESSION['csrf_token'])) {
        return false;
    }
    
    // Check if token has expired (1 hour lifetime)
    if (isset($_SESSION['csrf_token_time']) && (time() - $_SESSION['csrf_token_time'] > 3600)) {
        return false;
    }
    
    // Compare tokens using timing-safe comparison
    return hash_equals($_SESSION['csrf_token'], $token);
}

/**
 * Generate CSRF token input field HTML
 * @return string HTML input field with CSRF token
 */
function csrfField() {
    $token = getCSRFToken();
    return '<input type="hidden" name="csrf_token" value="' . htmlspecialchars($token, ENT_QUOTES, 'UTF-8') . '">';
}

/**
 * Get CSRF token meta tag for AJAX requests
 * @return string HTML meta tag with CSRF token
 */
function csrfMetaTag() {
    $token = getCSRFToken();
    return '<meta name="csrf-token" content="' . htmlspecialchars($token, ENT_QUOTES, 'UTF-8') . '">';
}

/**
 * Verify CSRF token or send error response
 * Call this at the beginning of POST endpoints
 */
function requireCSRFToken() {
    if (!validateCSRFToken()) {
        http_response_code(403);
        header('Content-Type: application/json');
        echo json_encode([
            'success' => false,
            'error' => 'Invalid or missing CSRF token. Please refresh the page and try again.'
        ]);
        exit;
    }
}
