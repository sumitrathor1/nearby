<?php
header('Content-Type: application/json');
error_reporting(0);
ini_set('display_errors', '0');

try {
    require_once __DIR__ . '/../config/config.php';
    
    $configInfo = [
        'success' => true,
        'message' => 'Configuration loaded successfully',
        'server_name' => $_SERVER['SERVER_NAME'] ?? 'unknown',
        'is_local' => defined('IS_LOCAL') ? IS_LOCAL : 'undefined',
        'env_file_exists' => file_exists(__DIR__ . '/../config/.env') ? 'yes' : 'no',
        'php_version' => PHP_VERSION,
        'constants' => [
            'DB_HOST' => defined('DB_HOST') ? (DB_HOST ?: 'EMPTY') : 'NOT_DEFINED',
            'DB_PORT' => defined('DB_PORT') ? DB_PORT : 'NOT_DEFINED',
            'DB_NAME' => defined('DB_NAME') ? (DB_NAME ?: 'EMPTY') : 'NOT_DEFINED',
            'DB_USER' => defined('DB_USER') ? (DB_USER ?: 'EMPTY') : 'NOT_DEFINED',
            'DB_PASS' => defined('DB_PASS') ? (empty(DB_PASS) ? 'EMPTY' : 'SET') : 'NOT_DEFINED',
        ]
    ];
    
    // Test database connection
    require_once __DIR__ . '/../config/db.php';
    $conn = nearby_db_connect();
    
    $configInfo['database'] = [
        'connected' => true,
        'server_info' => mysqli_get_server_info($conn),
        'charset' => mysqli_character_set_name($conn)
    ];
    
    echo json_encode($configInfo, JSON_PRETTY_PRINT);
    
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'error' => $e->getMessage(),
        'file' => $e->getFile(),
        'line' => $e->getLine()
    ], JSON_PRETTY_PRINT);
}
