<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

require 'assets/pages/_connection.php';

// 1️⃣ Delete all tables in the database
$result = $conn->query("SHOW TABLES");
if ($result) {
    while ($row = $result->fetch_array()) {
        $table = $row[0];
        $drop = $conn->query("DROP TABLE IF EXISTS `$table`");
        if ($drop) {
            echo "🗑️ Dropped table: $table\n";
        } else {
            echo "❌ Failed to drop table $table: " . $conn->error . "\n";
        }
    }
} else {
    die("❌ Failed to fetch tables: " . $conn->error);
}

// 2️⃣ Path to your SQL file
$sqlFile = __DIR__ . '/sql/jeeneettracker.sql';
if (!file_exists($sqlFile)) {
    die("❌ File $sqlFile not found.\n");
}

// 3️⃣ Execute jeeneettracker.sql
$sql = file_get_contents($sqlFile);

if ($conn->multi_query($sql)) {
    do {
        if ($result = $conn->store_result()) {
            $result->free();
        }
    } while ($conn->next_result());
    echo "✅ jeeneettracker.sql executed successfully.\n";
} else {
    echo "❌ Error executing jeeneettracker.sql: " . $conn->error . "\n";
}

$conn->close();
