<?php
$user = $_SESSION['user'] ?? null;
?>
<!doctype html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= APP_NAME ?></title>
    <link rel="stylesheet" href="/Tp php/facturation/assets/css/style.css">
    <script src="https://unpkg.com/@zxing/library@latest"></script>
    <script src="/Tp php/facturation/assets/js/scanner.js" defer></script>
</head>
<body>
<header class="topbar">
    <h1><?= APP_NAME ?></h1>
    <?php if ($user): ?>
        <div>
            <span>Bienvenue <?= htmlspecialchars((string) $user['nom_complet']) ?> (<?= htmlspecialchars((string) $user['role']) ?>)</span>
            <a href="/Tp php/facturation/auth/logout.php">Deconnexion</a>
        </div>
    <?php endif; ?>
</header>
<main class="container">
    <?php if (!empty($_SESSION['flash_error'])): ?>
        <div class="alert error"><?= htmlspecialchars((string) $_SESSION['flash_error']) ?></div>
        <?php unset($_SESSION['flash_error']); ?>
    <?php endif; ?>
    <?php if (!empty($_SESSION['flash_ok'])): ?>
        <div class="alert ok"><?= htmlspecialchars((string) $_SESSION['flash_ok']) ?></div>
        <?php unset($_SESSION['flash_ok']); ?>
    <?php endif; ?>
