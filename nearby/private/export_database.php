<?php
require_once __DIR__ . '/../config/config.php';

$host = DB_HOST;
$user = DB_USER;
$pass = DB_PASS; // XAMPP default keeps empty string locally
$dbname = DB_NAME;

$backupDir = dirname(__DIR__) . DIRECTORY_SEPARATOR . 'database' . DIRECTORY_SEPARATOR . 'backups';
if (!is_dir($backupDir) && !mkdir($backupDir, 0775, true)) {
    die('Error: Unable to create backup directory: ' . $backupDir);
}

$backupFile = $backupDir . DIRECTORY_SEPARATOR . 'nearby_' . date('Ymd_His') . '.sql';

// Ensure directory exists and is writable
if (!is_dir(dirname($backupFile))) {
    die("Error: Directory does not exist: " . dirname($backupFile));
}
if (!is_writable(dirname($backupFile))) {
    die("Error: Directory not writable: " . dirname($backupFile));
}

$mysqldumpPath = "C:\\xampp\\mysql\\bin\\mysqldump.exe";
if (!file_exists($mysqldumpPath)) {
    // try fallback to mysqldump in PATH
    $mysqldumpPath = 'mysqldump';
}

// Build command safely. For empty password omit the --password option so mysqldump uses no password.
$parts = [];
$parts[] = escapeshellarg($mysqldumpPath);
$parts[] = '--user=' . escapeshellarg($user);
if ($pass !== null && $pass !== '') {
    $parts[] = '--password=' . escapeshellarg($pass);
}
$parts[] = '--host=' . escapeshellarg($host);
if (defined('DB_PORT') && DB_PORT) {
    $parts[] = '--port=' . escapeshellarg((string) DB_PORT);
}
$parts[] = escapeshellarg($dbname);
$parts[] = '--add-drop-table';
$parts[] = '--complete-insert';
$parts[] = '--create-options';
$parts[] = '--default-character-set=utf8mb4';
$parts[] = '--skip-triggers';
$parts[] = '--skip-comments';
$parts[] = '--skip-set-charset';

// We'll capture stdout/stderr and write to file from PHP to avoid shell redirection problems on Windows.
$cmd = implode(' ', $parts) . ' 2>&1';

// Execute
exec($cmd, $output, $returnVar);

// If command succeeded, write collected output to backup file
if ($returnVar === 0) {
    $dump = implode(PHP_EOL, $output);
    if (file_put_contents($backupFile, $dump) === false) {
        echo "❌ Export command succeeded but writing to file failed: $backupFile";
        exit(1);
    }
    echo "✅ Database export successful: $backupFile";
    exit(0);
} else {
    // Show helpful debug info
    echo "❌ Error exporting database. Return code: $returnVar\n";
    echo "Command: $cmd\n";
    echo "Output:\n" . implode("\n", $output);
    exit($returnVar);
}
?>
