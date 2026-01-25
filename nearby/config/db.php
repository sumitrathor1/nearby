<?php
require_once __DIR__ . '/config.php';

function nearby_db_connect(): mysqli
{
    static $connection;

    if ($connection instanceof mysqli) {
        return $connection;
    }

    // Validate DB credentials are loaded
    if (empty(DB_HOST) || empty(DB_NAME) || empty(DB_USER)) {
        header('Content-Type: application/json');
        http_response_code(500);
        die(json_encode([
            'success' => false,
            'message' => 'Database configuration missing',
            'debug' => IS_LOCAL ? [
                'host' => DB_HOST ?: 'EMPTY',
                'name' => DB_NAME ?: 'EMPTY',
                'user' => DB_USER ?: 'EMPTY',
                'env_file_exists' => file_exists(__DIR__ . '/.env') ? 'yes' : 'no'
            ] : null
        ]));
    }

    $connection = @mysqli_connect(DB_HOST, DB_USER, DB_PASS, DB_NAME, (int)DB_PORT);

    if (!$connection) {
        header('Content-Type: application/json');
        http_response_code(500);
        die(json_encode([
            'success' => false,
            'message' => 'Database connection failed',
            'error' => mysqli_connect_error(),
            'debug' => IS_LOCAL ? [
                'host' => DB_HOST,
                'port' => DB_PORT,
                'name' => DB_NAME,
                'user' => DB_USER
            ] : null
        ]));
    }

    if (!mysqli_set_charset($connection, DB_CHARSET)) {
        header('Content-Type: application/json');
        http_response_code(500);
        die(json_encode([
            'success' => false,
            'message' => 'Failed to set database charset',
            'error' => mysqli_error($connection),
        ]));
    }

    return $connection;
}

function nearby_db_close(): void
{
    $connection = nearby_db_connect();
    if ($connection instanceof mysqli) {
        mysqli_close($connection);
    }
}
