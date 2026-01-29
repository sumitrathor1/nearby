<?php
/**
 * Security Utility Functions for NearBy Platform
 * Provides comprehensive input validation, sanitization, and XSS protection
 */

/**
 * Sanitize string input to prevent XSS attacks
 * @param string $input Raw user input
 * @param bool $allowHtml Whether to allow basic HTML tags
 * @return string Sanitized string
 */
function sanitizeInput($input, $allowHtml = false) {
    if (!is_string($input)) {
        return '';
    }
    
    // Remove null bytes and control characters
    $input = str_replace(["\0", "\x0B"], '', $input);
    
    // Trim whitespace
    $input = trim($input);
    
    if ($allowHtml) {
        // Allow only safe HTML tags
        $allowedTags = '<p><br><strong><em><u><a><ul><ol><li>';
        $input = strip_tags($input, $allowedTags);
    } else {
        // Remove all HTML tags
        $input = strip_tags($input);
    }
    
    // Convert special characters to HTML entities
    return htmlspecialchars($input, ENT_QUOTES | ENT_HTML5, 'UTF-8');
}

/**
 * Validate and sanitize email address
 * @param string $email Email to validate
 * @return array ['valid' => bool, 'email' => string, 'error' => string]
 */
function validateEmail($email) {
    $email = trim($email);
    $email = strtolower($email);
    
    if (empty($email)) {
        return ['valid' => false, 'email' => '', 'error' => 'Email is required'];
    }
    
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        return ['valid' => false, 'email' => '', 'error' => 'Invalid email format'];
    }
    
    // Check for dangerous characters
    if (preg_match('/[<>"\'\\\]/', $email)) {
        return ['valid' => false, 'email' => '', 'error' => 'Email contains invalid characters'];
    }
    
    return ['valid' => true, 'email' => $email, 'error' => ''];
}

/**
 * Validate and sanitize phone number
 * @param string $phone Phone number to validate
 * @return array ['valid' => bool, 'phone' => string, 'error' => string]
 */
function validatePhone($phone) {
    $phone = trim($phone);
    
    if (empty($phone)) {
        return ['valid' => false, 'phone' => '', 'error' => 'Phone number is required'];
    }
    
    // Remove all non-numeric characters except + and -
    $cleanPhone = preg_replace('/[^0-9+\-\s]/', '', $phone);
    
    // Check if it matches a valid phone pattern
    if (!preg_match('/^[0-9+][0-9\-\s]{6,}$/', $cleanPhone)) {
        return ['valid' => false, 'phone' => '', 'error' => 'Invalid phone number format'];
    }
    
    return ['valid' => true, 'phone' => $cleanPhone, 'error' => ''];
}

/**
 * Validate numeric input (price, rent, etc.)
 * @param mixed $value Value to validate
 * @param int $min Minimum allowed value
 * @param int $max Maximum allowed value
 * @return array ['valid' => bool, 'value' => int, 'error' => string]
 */
function validateNumeric($value, $min = 0, $max = PHP_INT_MAX) {
    if ($value === '' || $value === null) {
        return ['valid' => false, 'value' => 0, 'error' => 'Numeric value is required'];
    }
    
    if (!is_numeric($value)) {
        return ['valid' => false, 'value' => 0, 'error' => 'Value must be numeric'];
    }
    
    $numValue = (int) $value;
    
    if ($numValue < $min) {
        return ['valid' => false, 'value' => 0, 'error' => "Value must be at least {$min}"];
    }
    
    if ($numValue > $max) {
        return ['valid' => false, 'value' => 0, 'error' => "Value cannot exceed {$max}"];
    }
    
    return ['valid' => true, 'value' => $numValue, 'error' => ''];
}

/**
 * Validate text length and content
 * @param string $text Text to validate
 * @param int $minLength Minimum length
 * @param int $maxLength Maximum length
 * @param bool $allowHtml Whether to allow HTML
 * @return array ['valid' => bool, 'text' => string, 'error' => string]
 */
function validateText($text, $minLength = 1, $maxLength = 1000, $allowHtml = false) {
    if (!is_string($text)) {
        return ['valid' => false, 'text' => '', 'error' => 'Text must be a string'];
    }
    
    $sanitized = sanitizeInput($text, $allowHtml);
    $length = mb_strlen($sanitized);
    
    if ($length < $minLength) {
        return ['valid' => false, 'text' => '', 'error' => "Text must be at least {$minLength} characters"];
    }
    
    if ($length > $maxLength) {
        return ['valid' => false, 'text' => '', 'error' => "Text cannot exceed {$maxLength} characters"];
    }
    
    return ['valid' => true, 'text' => $sanitized, 'error' => ''];
}

/**
 * Validate enum values (role, category, etc.)
 * @param string $value Value to validate
 * @param array $allowedValues Array of allowed values
 * @param string $fieldName Field name for error messages
 * @return array ['valid' => bool, 'value' => string, 'error' => string]
 */
function validateEnum($value, $allowedValues, $fieldName = 'field') {
    $value = trim($value);
    
    if (empty($value)) {
        return ['valid' => false, 'value' => '', 'error' => ucfirst($fieldName) . ' is required'];
    }
    
    if (!in_array($value, $allowedValues, true)) {
        $allowed = implode(', ', $allowedValues);
        return ['valid' => false, 'value' => '', 'error' => "Invalid {$fieldName}. Allowed values: {$allowed}"];
    }
    
    return ['valid' => true, 'value' => $value, 'error' => ''];
}

/**
 * Validate array of facilities/tags
 * @param mixed $facilities Facilities array or string
 * @return array ['valid' => bool, 'facilities' => array, 'error' => string]
 */
function validateFacilities($facilities) {
    if (empty($facilities)) {
        return ['valid' => true, 'facilities' => [], 'error' => ''];
    }
    
    if (is_string($facilities)) {
        $facilities = explode(',', $facilities);
    }
    
    if (!is_array($facilities)) {
        return ['valid' => false, 'facilities' => [], 'error' => 'Facilities must be an array'];
    }
    
    $cleanFacilities = [];
    foreach ($facilities as $facility) {
        $clean = sanitizeInput($facility);
        if (!empty($clean) && mb_strlen($clean) <= 50) {
            $cleanFacilities[] = $clean;
        }
    }
    
    if (count($cleanFacilities) > 20) {
        return ['valid' => false, 'facilities' => [], 'error' => 'Too many facilities (max 20)'];
    }
    
    return ['valid' => true, 'facilities' => $cleanFacilities, 'error' => ''];
}

/**
 * Validate password strength
 * @param string $password Password to validate
 * @return array ['valid' => bool, 'password' => string, 'error' => string]
 */
function validatePassword($password) {
    if (!is_string($password)) {
        return ['valid' => false, 'password' => '', 'error' => 'Password must be a string'];
    }
    
    $password = trim($password);
    
    if (empty($password)) {
        return ['valid' => false, 'password' => '', 'error' => 'Password is required'];
    }
    
    if (mb_strlen($password) < 8) {
        return ['valid' => false, 'password' => '', 'error' => 'Password must be at least 8 characters long'];
    }
    
    if (mb_strlen($password) > 128) {
        return ['valid' => false, 'password' => '', 'error' => 'Password cannot exceed 128 characters'];
    }
    
    // Check for at least one uppercase letter
    if (!preg_match('/[A-Z]/', $password)) {
        return ['valid' => false, 'password' => '', 'error' => 'Password must contain at least one uppercase letter'];
    }
    
    // Check for at least one lowercase letter
    if (!preg_match('/[a-z]/', $password)) {
        return ['valid' => false, 'password' => '', 'error' => 'Password must contain at least one lowercase letter'];
    }
    
    // Check for at least one digit
    if (!preg_match('/[0-9]/', $password)) {
        return ['valid' => false, 'password' => '', 'error' => 'Password must contain at least one number'];
    }
    
    // Check for at least one special character
    if (!preg_match('/[!@#$%^&*()_+\-=\[\]{};\':"\\|,.<>\/?]/', $password)) {
        return ['valid' => false, 'password' => '', 'error' => 'Password must contain at least one special character'];
    }
    
    // Check for common weak passwords
    $weakPasswords = ['password', '12345678', 'qwerty', 'abc123', 'password123', 'admin', 'letmein'];
    if (in_array(strtolower($password), $weakPasswords)) {
        return ['valid' => false, 'password' => '', 'error' => 'Password is too common. Please choose a stronger password'];
    }
    
    return ['valid' => true, 'password' => $password, 'error' => ''];
}

/**
 * Generate secure error response without exposing sensitive information
 * @param string $message User-friendly error message
 * @param int $httpCode HTTP status code
 * @param string $logMessage Internal error message for logging
 * @return void
 */
function secureErrorResponse($message, $httpCode = 400, $logMessage = null) {
    http_response_code($httpCode);
    
    // Log internal error details if provided
    if ($logMessage) {
        error_log("[NearBy Security] {$logMessage}");
    }
    
    // Return only user-friendly message
    echo json_encode([
        'success' => false,
        'message' => $message,
        'timestamp' => date('c')
    ]);
    exit;
}

/**
 * Rate limiting check (basic implementation)
 * @param string $identifier User identifier (IP, user ID, etc.)
 * @param int $maxRequests Maximum requests allowed
 * @param int $timeWindow Time window in seconds
 * @return bool True if request is allowed
 */
function checkRateLimit($identifier, $maxRequests = 60, $timeWindow = 60) {
    $cacheFile = sys_get_temp_dir() . '/nearby_rate_limit_' . md5($identifier);
    $now = time();
    
    if (!file_exists($cacheFile)) {
        file_put_contents($cacheFile, json_encode(['count' => 1, 'reset' => $now + $timeWindow]));
        return true;
    }
    
    $data = json_decode(file_get_contents($cacheFile), true);
    
    if ($now > $data['reset']) {
        // Reset the counter
        file_put_contents($cacheFile, json_encode(['count' => 1, 'reset' => $now + $timeWindow]));
        return true;
    }
    
    if ($data['count'] >= $maxRequests) {
        return false;
    }
    
    // Increment counter
    $data['count']++;
    file_put_contents($cacheFile, json_encode($data));
    return true;
}

/**
 * Validate CSRF token (if implemented)
 * @param string $token Token to validate
 * @return bool True if valid
 */
function validateCSRFToken($token) {
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    
    $sessionToken = $_SESSION['csrf_token'] ?? '';
    return !empty($sessionToken) && hash_equals($sessionToken, $token);
}

/**
 * Initialize secure session configuration
 * Must be called before session_start()
 */
function initSecureSession() {
    // Set secure session configuration
    ini_set('session.use_only_cookies', 1);
    ini_set('session.use_strict_mode', 1);
    ini_set('session.cookie_httponly', 1);
    ini_set('session.cookie_secure', isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on');
    ini_set('session.cookie_samesite', 'Lax');
    ini_set('session.gc_maxlifetime', 3600); // 1 hour
    ini_set('session.cookie_lifetime', 0); // Session cookie

    // Set session save path to a secure location if possible
    if (function_exists('session_save_path')) {
        $sessionPath = session_save_path();
        if (empty($sessionPath) || !is_writable($sessionPath)) {
            // Try to set a custom path
            $customPath = __DIR__ . '/../private/sessions';
            if (!is_dir($customPath)) {
                mkdir($customPath, 0700, true);
            }
            if (is_writable($customPath)) {
                session_save_path($customPath);
            }
        }
    }

    // Set session name
    session_name('NEARBY_SESS');
}

/**
 * Start secure session with timeout and regeneration
 */
function startSecureSession() {
    if (session_status() === PHP_SESSION_NONE) {
        initSecureSession();
        session_start();

        // Check session timeout (30 minutes of inactivity)
        if (isset($_SESSION['last_activity']) && (time() - $_SESSION['last_activity'] > 1800)) {
            session_unset();
            session_destroy();
            session_start();
        }
        $_SESSION['last_activity'] = time();

        // Regenerate session ID periodically (every 10 minutes)
        if (!isset($_SESSION['created'])) {
            $_SESSION['created'] = time();
        } else if (time() - $_SESSION['created'] > 600) {
            session_regenerate_id(true);
            $_SESSION['created'] = time();
        }
    }
}

/**
 * Regenerate session ID after successful login
 */
function regenerateSessionAfterLogin() {
    session_regenerate_id(true);
    $_SESSION['created'] = time();
    $_SESSION['last_activity'] = time();
}

/**
 * Check if user session is valid and not expired
 * @return bool
 */
function isSessionValid() {
    if (session_status() === PHP_SESSION_NONE) {
        return false;
    }

    // Check if session has required data
    if (!isset($_SESSION['user']) || !isset($_SESSION['created'])) {
        return false;
    }

    // Check session timeout
    if (isset($_SESSION['last_activity']) && (time() - $_SESSION['last_activity'] > 1800)) {
        return false;
    }

    return true;
}

/**
 * Secure logout function
 */
function secureLogout() {
    // Clear all session data
    $_SESSION = [];

    // Delete session cookie
    if (ini_get('session.use_cookies')) {
        $params = session_get_cookie_params();
        setcookie(session_name(), '', time() - 42000,
            $params['path'], $params['domain'],
            $params['secure'], $params['httponly']
        );
    }

    // Destroy session
    session_destroy();
}

/**
 * Get CSRF token for forms
 * @return string CSRF token
 */
function generateCSRFToken() {
    if (session_status() === PHP_SESSION_NONE) {
        startSecureSession();
    }

    if (!isset($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

/**
 * Validate CSRF token
 * @param string $token Token to validate
 * @return bool
 */
function validateCSRFToken($token) {
    if (session_status() === PHP_SESSION_NONE) {
        return false;
    }

    return isset($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $token);
}