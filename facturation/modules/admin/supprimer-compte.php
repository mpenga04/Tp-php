<?php
declare(strict_types=1);
require_once dirname(__DIR__, 2) . '/config/config.php';
require_once dirname(__DIR__, 2) . '/auth/session.php';
require_once dirname(__DIR__, 2) . '/includes/fonctions-auth.php';
require_role('super_admin');

$id = trim((string) ($_GET['identifiant'] ?? ''));
if ($id !== '') {
    $users = array_values(array_filter(load_users(), static function (array $u) use ($id): bool {
        return ($u['identifiant'] ?? '') !== $id || ($u['role'] ?? '') === 'super_admin';
    }));
    save_users($users);
    $_SESSION['flash_ok'] = 'Compte supprime.';
}
header('Location: /Tp php/facturation/modules/admin/gestion-comptes.php');
exit;
