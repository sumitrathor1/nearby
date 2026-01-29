<?php
require_once __DIR__ . '/db.php';

try {
    $conn = nearby_db_connect();
} catch (Throwable $e) {
    die('Database connection failed');
}
