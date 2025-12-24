<?php
require_once __DIR__ . '/config.php';

function nearby_db_connect(): mysqli
{
    static $connection;

    if ($connection instanceof mysqli) {
        return $connection;
    }

    $connection = mysqli_connect(DB_HOST, DB_USER, DB_PASS, DB_NAME, DB_PORT);

    if (!$connection) {
        http_response_code(500);
        die(json_encode([
            'success' => false,
            'message' => 'Database connection failed',
            'error' => mysqli_connect_error(),
        ]));
    }

    if (!mysqli_set_charset($connection, DB_CHARSET)) {
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
