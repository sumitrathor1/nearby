<?php
// Ensure errors don't break JSON responses
error_reporting(E_ALL);
ini_set('display_errors', '0');
ini_set('log_errors', '1');

// Load .env manually (simple way)
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
		
		// Remove quotes if present
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

$serverName = $_SERVER['SERVER_NAME'] ?? 'localhost';
$isLocal = in_array($serverName, ['localhost', '127.0.0.1'], true);

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
define('IS_LOCAL', $isLocal);

define('GEMINI_API_KEY', getEnvValue('GEMINI_API_KEY'));
define('GEMINI_MODEL', getEnvValue('GEMINI_MODEL', 'models/gemini-2.0-flash-exp'));
