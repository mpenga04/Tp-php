<?php
declare(strict_types=1);
require_once dirname(__DIR__, 2) . '/config/config.php';
require_once dirname(__DIR__, 2) . '/auth/session.php';
require_once dirname(__DIR__, 2) . '/includes/fonctions-auth.php';
require_role('super_admin');

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $identifiant = trim((string) ($_POST['identifiant'] ?? ''));
    $nom = trim((string) ($_POST['nom_complet'] ?? ''));
    $role = (string) ($_POST['role'] ?? 'caissier');
    $password = (string) ($_POST['mot_de_passe'] ?? '');

    if ($identifiant === '' || $nom === '' || $password === '') {
        $error = 'Tous les champs sont obligatoires.';
    } elseif (!in_array($role, ['caissier', 'manager'], true)) {
        $error = 'Role invalide.';
    } elseif (find_user_by_identifiant($identifiant)) {
        $error = 'Identifiant deja utilise.';
    } else {
        $users = load_users();
        $users[] = [
            'identifiant' => $identifiant,
            'mot_de_passe' => password_hash($password, PASSWORD_DEFAULT),
            'role' => $role,
            'nom_complet' => $nom,
            'date_creation' => date('Y-m-d'),
            'actif' => true,
        ];
        save_users($users);
        $_SESSION['flash_ok'] = 'Compte cree.';
        header('Location: ' . BASE_URL . '/modules/admin/gestion-comptes.php');
        exit;
    }
}

include dirname(__DIR__, 2) . '/includes/header.php';
?>
<section class="card">
    <h2>Ajouter un compte</h2>
    <?php if ($error): ?><div class="alert error"><?= htmlspecialchars($error) ?></div><?php endif; ?>
    <form method="post">
        <label>Identifiant <input name="identifiant" required></label>
        <label>Nom complet <input name="nom_complet" required></label>
        <label>Mot de passe <input type="password" name="mot_de_passe" required></label>
        <label>Role
            <select name="role">
                <option value="caissier">Caissier</option>
                <option value="manager">Manager</option>
            </select>
        </label>
        <button type="submit">Creer</button>
    </form>
</section>
<?php include dirname(__DIR__, 2) . '/includes/footer.php'; ?>
