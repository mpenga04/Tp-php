<?php
declare(strict_types=1);

require_once dirname(__DIR__) . '/includes/fonctions-auth.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

function current_user(): ?array
{
    return $_SESSION['user'] ?? null;
}

function require_login(): void
{
    if (!current_user()) {
        header('Location: ' . BASE_URL . '/auth/login.php');
        exit;
    }
}

function require_role(string $minimumRole): void
{
    require_login();
    $user = current_user();
    if (!$user || role_rank((string) $user['role']) < role_rank($minimumRole)) {
        $_SESSION['flash_error'] = 'Acces non autorise.';
        header('Location: ' . BASE_URL . '/index.php');
        exit;
    }
}
