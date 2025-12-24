<?php
$serverName = $_SERVER['SERVER_NAME'] ?? 'localhost';
$isLocal = in_array($serverName, ['localhost', '127.0.0.1'], true);

if ($isLocal) {
	define('DB_HOST', '127.0.0.1');
	define('DB_PORT', 3306);
	define('DB_NAME', 'nearby');
	define('DB_USER', 'root');
	define('DB_PASS', '');
	define('BASE_URL', 'http://localhost/nearby/');
} else {
	define('DB_HOST', 'sql113.infinityfree.com');
	define('DB_PORT', 3306);
	define('DB_NAME', 'if0_35933629_nearby');
	define('DB_USER', 'if0_35933629');
	define('DB_PASS', 'vCp0BAOfzLxQV');
	define('BASE_URL', 'https://' . $serverName . '/');
}

define('DB_CHARSET', 'utf8mb4');
