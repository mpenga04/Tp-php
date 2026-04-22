<?php
declare(strict_types=1);

require_once dirname(__DIR__) . '/config/config.php';

function load_users(): array
{
    if (!file_exists(USERS_FILE)) {
        return [];
    }
    $raw = file_get_contents(USERS_FILE);
    $data = json_decode($raw ?: '[]', true);
    return is_array($data) ? $data : [];
}

function save_users(array $users): bool
{
    return file_put_contents(USERS_FILE, json_encode($users, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE)) !== false;
}

function find_user_by_identifiant(string $identifiant): ?array
{
    foreach (load_users() as $user) {
        if (($user['identifiant'] ?? '') === $identifiant) {
            return $user;
        }
    }
    return null;
}

function authenticate_user(string $identifiant, string $password): ?array
{
    $user = find_user_by_identifiant($identifiant);
    if (!$user || !($user['actif'] ?? false)) {
        return null;
    }
    return password_verify($password, (string) ($user['mot_de_passe'] ?? '')) ? $user : null;
}

function role_rank(string $role): int
{
    return match ($role) {
        'caissier' => 1,
        'manager' => 2,
        'super_admin' => 3,
        default => 0,
    };
}
