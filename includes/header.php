<?php
require_once __DIR__ . '/../config/security.php';

startSecureSession();

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
    <nav class="navbar navbar-expand-lg glass-nav navbar-dark fixed-top shadow-sm py-3">
        <div class="container">
            <a class="navbar-brand d-flex align-items-center" href="index.php">
                
                <img src="assets/images/nearby_image.png" class="brand-logo" alt="NearBy logo">
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#primaryNav" aria-controls="primaryNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="primaryNav">
                <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                    <li class="nav-item"><a class="nav-link" href="index.php">Home</a></li>
                    <li class="nav-item"><a class="nav-link" href="search.php">Search</a></li>
                    <li class="nav-item"><a class="nav-link" href="guidance.php">Local Guidance</a></li>
                    <li class="nav-item"><a class="nav-link" href="second-hand-products.php">Second-Hand</a></li>

                    <?php if ($currentUser && $currentUser['role'] === 'junior'): ?>
                        <li class="nav-item"><a class="nav-link" href="junior-dashboard.php">Junior Dashboard</a></li>
                    <?php elseif ($currentUser && $currentUser['role'] === 'senior'): ?>
                        <li class="nav-item"><a class="nav-link" href="senior-dashboard.php">Senior Dashboard</a></li>
                    <?php endif; ?>
                </ul>
                <div class="d-flex gap-2">
                    <?php if ($currentUser): ?>
                        <span class="navbar-text small text-white-50 me-2">Hi, <?= htmlspecialchars($currentUser['name']) ?></span>
                        <a class="btn btn-sm btn-outline-light" href="logout.php">Logout</a>
                    <?php else: ?>
                        <a class="btn btn-lg btn-light" href="login.php">Login</a>
                        <a class="btn btn-lg btn-primary" href="register.php">Register</a>
                    <!-- Dark Mode Toggle Button -->
<button id="dark-mode-toggle" class="btn btn-outline-secondary btn-lg ms-2" title="Toggle Dark Mode">
    <i id="dark-mode-icon" class="bi bi-moon-fill"></i>
</button>

                    <?php endif; ?>
                </div>
            </div>
        </div>
    </nav>
    <main class="flex-grow-1 pt-5 mt-4">
        <div class="container py-5">
