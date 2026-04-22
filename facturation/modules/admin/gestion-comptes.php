<?php
declare(strict_types=1);
require_once dirname(__DIR__, 2) . '/config/config.php';
require_once dirname(__DIR__, 2) . '/auth/session.php';
require_once dirname(__DIR__, 2) . '/includes/fonctions-auth.php';
require_role('super_admin');

$users = load_users();
include dirname(__DIR__, 2) . '/includes/header.php';
?>
<nav class="menu">
    <a href="<?= BASE_URL ?>/index.php">Acceuil</a>
    <a href="<?= BASE_URL ?>/modules/facturation/nouvelle-facture.php">Nouvelle facture</a>
    <?php if ($user['role'] === 'caissier'): ?>    
    <?php else: ?>
    <a href="<?= BASE_URL ?>/modules/produits/enregistrer.php">Enregistrer produit</a>
    <a href="<?= BASE_URL ?>/rapports/rapport-journalier.php">Rapport journalier</a>
    <?php endif; ?>

    <?php if ($user['role'] === 'caissier' || $user['role'] === 'manager'): ?>    
    <?php else: ?>
    <a href="<?= BASE_URL ?>/modules/admin/gestion-comptes.php">Gestion comptes</a>
    <?php endif; ?>

    <a href="<?= BASE_URL ?>/modules/produits/liste.php">Liste produits</a>
</nav>
<section class="card">
    <h2>Gestion des comptes</h2>
    <p><a href="<?= BASE_URL ?>/modules/admin/ajouter-compte.php">Ajouter un compte</a></p>
    <table>
        <thead><tr><th>Identifiant</th><th>Nom</th><th>Role</th><th>Actif</th><th>Action</th></tr></thead>
        <tbody>
        <?php foreach ($users as $u): ?>
            <tr>
                <td><?= htmlspecialchars((string) $u['identifiant']) ?></td>
                <td><?= htmlspecialchars((string) $u['nom_complet']) ?></td>
                <td><?= htmlspecialchars((string) $u['role']) ?></td>
                <td><?= !empty($u['actif']) ? 'Oui' : 'Non' ?></td>
                <td>
                    <?php if ($u['role'] !== 'super_admin'): ?>
                        <a href="<?= BASE_URL ?>/modules/admin/supprimer-compte.php?identifiant=<?= urlencode((string) $u['identifiant']) ?>">Supprimer</a>
                    <?php endif; ?>
                </td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
</section>
<?php include dirname(__DIR__, 2) . '/includes/footer.php'; ?>
