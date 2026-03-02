<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$pageTitle = $pageTitle ?? "NearBy Student Housing";

require_once __DIR__ . '/header.php';
?>

<?= $content ?>

<?php require_once __DIR__ . '/footer.php'; ?>