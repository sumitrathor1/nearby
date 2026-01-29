<?php
/**
 * Secure Session Management Helper
 * Provides secure session configuration, timeout handling, and regeneration
 */

// Session configuration constants
define('SESSION_TIMEOUT', 3600); // 1 hour in seconds
define('SESSION_REGENERATE_INTERVAL', 300); // 5 minutes in seconds

/**
 * Initialize secure session with proper security settings
 * Call this instead of session_start() throughout the application
 * @return bool True if session started successfully
 */
function secureSessionStart() {
    // Don't start if already started
    if (session_status() === PHP_SESSION_ACTIVE) {
        return true;
    }
    
    // Configure secure session settings before starting
    configureSessionSecurity();
    
    // Start the session
    if (!session_start()) {
        return false;
    }
    
    // Validate and refresh session
    if (!validateSession()) {
        return false;
    }
    
    return true;
}

/**
 * Configure secure session parameters
 */
function configureSessionSecurity() {
    // Prevent JavaScript access to session cookie (XSS protection)
    ini_set('session.cookie_httponly', 1);
    
    // Only send cookie over HTTPS in production (uncomment for production)
    // ini_set('session.cookie_secure', 1);
    
    // Prevent session fixation attacks
    ini_set('session.use_strict_mode', 1);
    
    // Use cookies only (no URL-based session IDs)
    ini_set('session.use_only_cookies', 1);
    
    // Modern session ID hashing
    ini_set('session.sid_length', 48);
    ini_set('session.sid_bits_per_character', 6);
    
    // SameSite cookie attribute (CSRF protection)
    ini_set('session.cookie_samesite', 'Lax');
    
    // Session name (makes it less obvious)
    ini_set('session.name', 'NEARBY_SESSID');
    
    // Garbage collection
    ini_set('session.gc_maxlifetime', SESSION_TIMEOUT);
    ini_set('session.gc_probability', 1);
    ini_set('session.gc_divisor', 100);
}

/**
 * Validate current session for security issues
 * Checks timeout, hijacking attempts, and regenerates ID when needed
 * @return bool True if session is valid, false if session should be destroyed
 */
function validateSession() {
    // Initialize session tracking variables if not set
    if (!isset($_SESSION['created_at'])) {
        $_SESSION['created_at'] = time();
    }
    
    if (!isset($_SESSION['last_activity'])) {
        $_SESSION['last_activity'] = time();
    }
    
    if (!isset($_SESSION['last_regeneration'])) {
        $_SESSION['last_regeneration'] = time();
    }
    
    if (!isset($_SESSION['user_agent'])) {
        $_SESSION['user_agent'] = $_SERVER['HTTP_USER_AGENT'] ?? '';
    }
    
    if (!isset($_SESSION['ip_address'])) {
        $_SESSION['ip_address'] = getClientIP();
    }
    
    // Check for session timeout (absolute)
    if (time() - $_SESSION['created_at'] > SESSION_TIMEOUT * 2) {
        destroySession();
        return false;
    }
    
    // Check for inactivity timeout
    if (time() - $_SESSION['last_activity'] > SESSION_TIMEOUT) {
        destroySession();
        return false;
    }
    
    // Detect session hijacking attempts
    if (!validateSessionFingerprint()) {
        destroySession();
        return false;
    }
    
    // Update last activity time
    $_SESSION['last_activity'] = time();
    
    // Regenerate session ID periodically
    if (time() - $_SESSION['last_regeneration'] > SESSION_REGENERATE_INTERVAL) {
        regenerateSessionId();
    }
    
    return true;
}

/**
 * Validate session fingerprint to detect hijacking
 * @return bool True if fingerprint matches, false if potential hijacking
 */
function validateSessionFingerprint() {
    $currentUserAgent = $_SERVER['HTTP_USER_AGENT'] ?? '';
    $currentIP = getClientIP();
    
    // Check user agent (should not change during session)
    if (isset($_SESSION['user_agent']) && $_SESSION['user_agent'] !== $currentUserAgent) {
        // User agent changed - potential hijacking
        return false;
    }
    
    // Check IP address (allow some flexibility for mobile users)
    // Only check if IP was set and differs significantly
    if (isset($_SESSION['ip_address']) && $_SESSION['ip_address'] !== $currentIP) {
        // For stricter security, you could return false here
        // For now, we'll allow IP changes (mobile networks, VPNs)
        // But log it for monitoring
        error_log("Session IP changed from {$_SESSION['ip_address']} to {$currentIP}");
    }
    
    return true;
}

/**
 * Get client IP address (handles proxies)
 * @return string Client IP address
 */
function getClientIP() {
    if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
        return $_SERVER['HTTP_CLIENT_IP'];
    } elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
        // Get first IP if multiple are present (X-Forwarded-For can have a list)
        $ips = explode(',', $_SERVER['HTTP_X_FORWARDED_FOR']);
        return trim($ips[0]);
    } else {
        return $_SERVER['REMOTE_ADDR'] ?? '0.0.0.0';
    }
}

/**
 * Regenerate session ID to prevent fixation attacks
 * Should be called after login and periodically during the session
 * @param bool $deleteOldSession Whether to delete the old session file
 */
function regenerateSessionId($deleteOldSession = false) {
    if (session_status() === PHP_SESSION_ACTIVE) {
        session_regenerate_id($deleteOldSession);
        $_SESSION['last_regeneration'] = time();
    }
}

/**
 * Destroy session completely (logout)
 */
function destroySession() {
    if (session_status() === PHP_SESSION_ACTIVE) {
        // Clear session variables
        $_SESSION = array();
        
        // Delete session cookie
        if (isset($_COOKIE[session_name()])) {
            $params = session_get_cookie_params();
            setcookie(
                session_name(),
                '',
                time() - 42000,
                $params['path'],
                $params['domain'],
                $params['secure'],
                $params['httponly']
            );
        }
        
        // Destroy session
        session_destroy();
    }
}

/**
 * Set user session data after successful login
 * @param array $userData User data to store in session
 */
function setUserSession($userData) {
    // Regenerate session ID after login (prevent session fixation)
    regenerateSessionId(true);
    
    // Reset session timing
    $_SESSION['created_at'] = time();
    $_SESSION['last_activity'] = time();
    $_SESSION['last_regeneration'] = time();
    
    // Update fingerprint
    $_SESSION['user_agent'] = $_SERVER['HTTP_USER_AGENT'] ?? '';
    $_SESSION['ip_address'] = getClientIP();
    
    // Store user data
    $_SESSION['user'] = $userData;
    
    // Set authenticated flag
    $_SESSION['authenticated'] = true;
}

/**
 * Check if user is authenticated
 * @return bool True if user is logged in
 */
function isAuthenticated() {
    return isset($_SESSION['authenticated']) && $_SESSION['authenticated'] === true && isset($_SESSION['user']);
}

/**
 * Get session timeout remaining in seconds
 * @return int Seconds remaining before timeout
 */
function getSessionTimeoutRemaining() {
    if (!isset($_SESSION['last_activity'])) {
        return 0;
    }
    
    $elapsed = time() - $_SESSION['last_activity'];
    $remaining = SESSION_TIMEOUT - $elapsed;
    
    return max(0, $remaining);
}

/**
 * Get session info for debugging/monitoring
 * @return array Session information
 */
function getSessionInfo() {
    if (session_status() !== PHP_SESSION_ACTIVE) {
        return ['status' => 'not_started'];
    }
    
    return [
        'status' => 'active',
        'session_id' => session_id(),
        'created_at' => $_SESSION['created_at'] ?? null,
        'last_activity' => $_SESSION['last_activity'] ?? null,
        'last_regeneration' => $_SESSION['last_regeneration'] ?? null,
        'timeout_remaining' => getSessionTimeoutRemaining(),
        'authenticated' => isAuthenticated(),
        'user_id' => $_SESSION['user']['id'] ?? null,
        'ip_address' => $_SESSION['ip_address'] ?? null
    ];
}

/**
 * Refresh session activity (call on AJAX requests to prevent timeout)
 */
function refreshSessionActivity() {
    if (session_status() === PHP_SESSION_ACTIVE && isset($_SESSION['last_activity'])) {
        $_SESSION['last_activity'] = time();
    }
}

/**
 * Check if session will expire soon (within 5 minutes)
 * @return bool True if session is about to expire
 */
function isSessionExpiringSoon() {
    return getSessionTimeoutRemaining() < 300; // 5 minutes
}

/**
 * Get warning message if session is expiring soon
 * @return string|null Warning message or null if not expiring soon
 */
function getSessionExpiryWarning() {
    if (isSessionExpiringSoon()) {
        $remaining = getSessionTimeoutRemaining();
        $minutes = floor($remaining / 60);
        return "Your session will expire in {$minutes} minute(s). Save your work.";
    }
    return null;
}
