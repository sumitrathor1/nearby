<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once __DIR__ . '/helpers/csrf.php';
$pageTitle = $pageTitle ?? 'NearBy Student Housing';
$currentUser = $_SESSION['user'] ?? null;
$pageStyles = $pageStyles ?? [];
$pageScripts = $pageScripts ?? [];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($pageTitle) ?></title>
    <?= csrfMetaTag() ?>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
   
    <link rel="icon" type="image/x-icon" href="assets/images/favicon_io/favicon.ico">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/style.css">
    <?php foreach ($pageStyles as $stylePath): ?>
        <link rel="stylesheet" href="<?= htmlspecialchars($stylePath) ?>">
    <?php endforeach; ?>
</head>
<body>
<div class="app-wrapper d-flex flex-column min-vh-100">
    <nav class="navbar navbar-expand-lg glass-nav fixed-top shadow-sm">
    <div class="container-fluid px-5">

        <a class="navbar-brand logo-text" href="index.php">
    NearBy
</a>

        <button class="navbar-toggler border-0" type="button"
            data-bs-toggle="collapse" data-bs-target="#primaryNav">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse justify-content-end" id="primaryNav">

            <ul class="navbar-nav align-items-center gap-lg-4 me-4">
                <li class="nav-item"><a class="nav-link text-white" href="index.php">Home</a></li>
                <li class="nav-item"><a class="nav-link text-white" href="search.php">Search</a></li>
                <li class="nav-item"><a class="nav-link text-white" href="guidance.php">Local Guidance</a></li>
                <li class="nav-item"><a class="nav-link text-white" href="second-hand-products.php">Second-Hand</a></li>
            </ul>

            <div class="d-flex align-items-center gap-2">

    <a class="btn btn-light btn-sm px-3 fw-semibold" href="login.php">Login</a>

    <a class="btn btn-sm px-3 fw-semibold register-btn" href="register.php">Register</a>

    <!-- Dark Mode Toggle -->
    <button id="dark-mode-toggle"
        class="btn btn-sm btn-outline-light ms-1"
        title="Toggle Dark Mode">
        <i id="dark-mode-icon" class="bi bi-moon-fill"></i>
    </button>

</div>

        </div>
    </div>
</nav>
<main class="flex-grow-1" style="margin-top: 60px;">
    <div class="container py-5">
