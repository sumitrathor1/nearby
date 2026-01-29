<?php
// Secure environment configuration
error_reporting(E_ALL);
ini_set('display_errors', '0');
ini_set('log_errors', '1');

// Environment detection
$serverName = $_SERVER['SERVER_NAME'] ?? 'localhost';
$isLocal = in_array($serverName, ['localhost', '127.0.0.1'], true);
define('IS_LOCAL', $isLocal);

// Load .env securely
$envPath = __DIR__ . '/.env';
if (file_exists($envPath)) {
    foreach (file($envPath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES) as $line) {
        $line = trim($line);
        if ($line === '' || str_starts_with($line, '#')) continue;
        
        $parts = explode('=', $line, 2);
        if (count($parts) !== 2) continue;
        
        [$key, $value] = $parts;
        $key = trim($key);
        $value = trim($value);
        
        // Remove quotes
        if (($value[0] ?? '') === '"' && ($value[-1] ?? '') === '"') {
            $value = substr($value, 1, -1);
        } elseif (($value[0] ?? '') === "'" && ($value[-1] ?? '') === "'") {
            $value = substr($value, 1, -1);
        }
        
        $_ENV[$key] = $value;
        putenv($key . '=' . $value);
    }
}

function getEnvValue($key, $default = '') {
    return $_ENV[$key] ?? getenv($key) ?: $default;
}

// Database configuration
if ($isLocal) {
    define('DB_HOST', getEnvValue('LOCAL_DB_HOST', '127.0.0.1'));
    define('DB_PORT', getEnvValue('LOCAL_DB_PORT', '3306'));
    define('DB_NAME', getEnvValue('LOCAL_DB_NAME', 'nearby'));
    define('DB_USER', getEnvValue('LOCAL_DB_USER', 'root'));
    define('DB_PASS', getEnvValue('LOCAL_DB_PASS', ''));
    define('BASE_URL', getEnvValue('LOCAL_BASE_URL', 'http://localhost/nearby/'));
} else {
    define('DB_HOST', getEnvValue('LIVE_DB_HOST'));
    define('DB_PORT', getEnvValue('LIVE_DB_PORT', '3306'));
    define('DB_NAME', getEnvValue('LIVE_DB_NAME'));
    define('DB_USER', getEnvValue('LIVE_DB_USER'));
    define('DB_PASS', getEnvValue('LIVE_DB_PASS'));
    define('BASE_URL', getEnvValue('LIVE_BASE_URL'));
}

define('DB_CHARSET', 'utf8mb4');

// Secure API key handling
function getSecureApiKey() {
    $key = getEnvValue('GEMINI_API_KEY');
    // Validate API key format
    if (!$key || !preg_match('/^AIza[0-9A-Za-z_-]{35}$/', $key)) {
        return null;
    }
    return $key;
}

define('GEMINI_API_KEY', getSecureApiKey());
define('GEMINI_MODEL', getEnvValue('GEMINI_MODEL', 'models/gemini-2.0-flash-exp'));

// Security headers
if (!$isLocal) {
    header('X-Content-Type-Options: nosniff');
    header('X-Frame-Options: DENY');
    header('X-XSS-Protection: 1; mode=block');
}