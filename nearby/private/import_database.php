<?php
/**
 * import_database.php
 *
 * Robust SQL import script:
 * - Finds latest SQL file in ../database/backups (or use ?file=filename.sql)
 * - Disables FOREIGN_KEY_CHECKS before dropping/importing
 * - Optional drop of all existing tables (default: enabled)
 * - Parses SQL safely (supports DELIMITER changes)
 * - Executes statements one-by-one, logs errors and progress
 *
 * Usage:
 * - CLI or browser
 * - Optional GET params:
 *     file=your_sql_file.sql   -> import that file (must be in backups directory)
 *     drop=0                   -> skip dropping existing tables
 *
 * Make sure config/config.php defines DB_HOST, DB_USER, DB_PASS, DB_NAME, optionally DB_PORT
 */

set_time_limit(0);
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once __DIR__ . '/../config/config.php';

$backupDir = realpath(__DIR__ . '/../database/backups');
if ($backupDir === false || !is_dir($backupDir)) {
    http_response_code(500);
    echo "Error: backups directory not found: " . __DIR__ . '/../database/backups';
    exit(1);
}

// Get optional params
$requestedFile = isset($_GET['file']) ? basename($_GET['file']) : null;
$dropExisting = !(isset($_GET['drop']) && ($_GET['drop'] === '0' || strtolower($_GET['drop']) === 'false'));

// choose SQL file
$sqlFile = null;
if ($requestedFile) {
    $path = $backupDir . DIRECTORY_SEPARATOR . $requestedFile;
    if (!file_exists($path)) {
        http_response_code(400);
        echo "Error: requested file does not exist in backups: $requestedFile";
        exit(1);
    }
    $sqlFile = $path;
} else {
    // pick latest .sql file
    $files = glob($backupDir . DIRECTORY_SEPARATOR . '*.sql');
    if (!$files) {
        http_response_code(400);
        echo "Error: no .sql files found in backups directory: $backupDir";
        exit(1);
    }
    usort($files, function($a, $b) {
        return filemtime($b) - filemtime($a);
    });
    $sqlFile = $files[0];
}

// Logging
$logFile = $backupDir . DIRECTORY_SEPARATOR . 'import_log.txt';
$logHandle = fopen($logFile, 'a');
function logMsg($msg) {
    global $logHandle;
    $time = date('Y-m-d H:i:s');
    $line = "[$time] $msg\n";
    fwrite($logHandle, $line);
}

// Connect DB
$host = defined('DB_HOST') ? DB_HOST : '127.0.0.1';
$user = defined('DB_USER') ? DB_USER : 'root';
$pass = defined('DB_PASS') ? DB_PASS : '';
$dbname = defined('DB_NAME') ? DB_NAME : '';
$port = defined('DB_PORT') && DB_PORT ? DB_PORT : null;

$mysqli = mysqli_init();
if ($port) {
    $connected = $mysqli->real_connect($host, $user, $pass, $dbname, (int)$port);
} else {
    $connected = $mysqli->real_connect($host, $user, $pass, $dbname);
}

if (!$connected) {
    $err = mysqli_connect_error();
    logMsg("DB connect failed: $err");
    fclose($logHandle);
    http_response_code(500);
    echo "DB Connection failed: $err";
    exit(1);
}

$mysqli->set_charset('utf8mb4');
logMsg("Connected to DB {$host} / {$dbname} as {$user}");
logMsg("Import file: $sqlFile");
logMsg("Drop existing tables: " . ($dropExisting ? 'YES' : 'NO'));

try {
    // Turn off foreign key checks BEFORE dropping tables or altering schemas
    if (!$mysqli->query("SET FOREIGN_KEY_CHECKS = 0")) {
        throw new Exception("Failed to disable foreign key checks: " . $mysqli->error);
    }
    logMsg("Foreign key checks disabled.");

    // Optionally drop all existing tables
    if ($dropExisting) {
        $res = $mysqli->query("SHOW TABLES");
        if ($res === false) {
            throw new Exception("SHOW TABLES failed: " . $mysqli->error);
        }
        $tables = [];
        while ($row = $res->fetch_array(MYSQLI_NUM)) {
            $tables[] = $row[0];
        }
        // Drop in a safe loop
        foreach ($tables as $table) {
            $dropSql = "DROP TABLE IF EXISTS `$table`";
            if (!$mysqli->query($dropSql)) {
                // Log but continue (we already disabled FK checks)
                logMsg("Warning: DROP TABLE failed for $table: " . $mysqli->error);
            } else {
                logMsg("Dropped table: $table");
            }
        }
    }

    // Parse & execute SQL file (streaming, supporting DELIMITER)
    $handle = fopen($sqlFile, 'r');
    if ($handle === false) {
        throw new Exception("Cannot open SQL file: $sqlFile");
    }

    $delimiter = ';';
    $statement = '';
    $lineNumber = 0;
    $executedCount = 0;

    while (!feof($handle)) {
        $line = fgets($handle);
        if ($line === false) break;
        $lineNumber++;

        $trimmed = ltrim($line);
        // skip comments and empty lines (MySQL style)
        if ($trimmed === '' || strpos($trimmed, '-- ') === 0 || strpos($trimmed, '--\t') === 0 || strpos($trimmed, '#') === 0) {
            continue;
        }

        // Handle DELIMITER directive
        if (preg_match('/^\s*DELIMITER\s+(.+)$/i', $line, $m)) {
            $delimiter = rtrim($m[1], "\r\n");
            // If any pending statement, try execute it (rare)
            if (trim($statement) !== '') {
                $toExec = $statement;
                $statement = '';
                if (trim($toExec) !== '') {
                    if (!$mysqli->query($toExec)) {
                        $msg = "Error executing statement before delimiter change at line $lineNumber: " . $mysqli->error;
                        logMsg($msg);
                        throw new Exception($msg);
                    }
                    $executedCount++;
                }
            }
            continue;
        }

        $statement .= $line;

        // Check if current statement ends with delimiter
        $trimStmt = rtrim($statement);
        if ($delimiter === ';') {
            $ends = (substr($trimStmt, -1) === ';');
        } else {
            $ends = (substr($trimStmt, -strlen($delimiter)) === $delimiter);
        }

        if ($ends) {
            // Remove delimiter from statement
            if ($delimiter === ';') {
                $query = substr($statement, 0, -1);
            } else {
                $query = substr($statement, 0, -strlen($delimiter));
            }
            $statement = '';

            $query = trim($query);
            if ($query === '') {
                continue;
            }

            // Execute query
            if (!$mysqli->multi_query($query)) {
                // If multi_query fails, try single query (some statements may be non-multi).
                $err = $mysqli->error;
                logMsg("multi_query failed at approximately line $lineNumber: $err");
                // Try as single query
                if (!$mysqli->query($query)) {
                    $err2 = $mysqli->error;
                    $shortQuery = substr(preg_replace('/\s+/', ' ', $query), 0, 500);
                    $msg = "Import failed at line $lineNumber. Error: $err2. Query start: " . $shortQuery;
                    logMsg($msg);
                    throw new Exception($msg);
                } else {
                    // clear results if any
                    while ($mysqli->more_results() && $mysqli->next_result()) { /* flush */ }
                }
            } else {
                // flush results of multi_query
                do {
                    if ($res = $mysqli->store_result()) {
                        $res->free();
                    }
                } while ($mysqli->more_results() && $mysqli->next_result());
            }
            $executedCount++;
        }
    }

    // If anything left in buffer, try executing it
    if (trim($statement) !== '') {
        $query = trim($statement);
        if (!$mysqli->query($query)) {
            $err = $mysqli->error;
            $shortQuery = substr(preg_replace('/\s+/', ' ', $query), 0, 500);
            $msg = "Final statement execution failed. Error: $err. Query start: " . $shortQuery;
            logMsg($msg);
            throw new Exception($msg);
        }
        $executedCount++;
    }

    fclose($handle);

    // Re-enable foreign key checks
    if (!$mysqli->query("SET FOREIGN_KEY_CHECKS = 1")) {
        throw new Exception("Failed to re-enable foreign key checks: " . $mysqli->error);
    }
    logMsg("Foreign key checks re-enabled.");

    $msg = "Import completed successfully. Executed statements: $executedCount. File: " . basename($sqlFile);
    logMsg($msg);
    fclose($logHandle);

    // Output friendly success
    echo "✅ Import successful.\n";
    echo "File: " . basename($sqlFile) . "\n";
    echo "Statements executed: $executedCount\n";
    echo "Log: $logFile\n";
    exit(0);

} catch (Exception $e) {
    // Attempt to re-enable FK checks before exit, best-effort
    @$mysqli->query("SET FOREIGN_KEY_CHECKS = 1");
    $errMsg = $e->getMessage();
    logMsg("ERROR: " . $errMsg);
    fclose($logHandle);
    http_response_code(500);
    echo "❌ Import failed: " . $errMsg . "\n";
    echo "See log for details: $logFile\n";
    exit(1);
}
