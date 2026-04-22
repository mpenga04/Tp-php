<?php
declare(strict_types=1);
require_once dirname(__DIR__, 2) . '/config/config.php';
require_once dirname(__DIR__, 2) . '/auth/session.php';
require_once dirname(__DIR__, 2) . '/includes/fonctions-produits.php';
require_role('caissier');

$products = load_products();
include dirname(__DIR__, 2) . '/includes/header.php';
?>

<nav class="menu">
    <a href="/Tp php/facturation/index.php">Acceuil</a>
    <a href="/Tp php/facturation/modules/facturation/nouvelle-facture.php">Nouvelle facture</a>
    <?php if ($user['role'] === 'caissier'): ?>    
    <?php else: ?>
    <a href="/Tp php/facturation/modules/produits/enregistrer.php">Enregistrer produit</a>
    <a href="/Tp php/facturation/rapports/rapport-journalier.php">Rapport journalier</a>
    <?php endif; ?>

    <?php if ($user['role'] === 'caissier' || $user['role'] === 'manager'): ?>    
    <?php else: ?>
    <a href="/Tp php/facturation/modules/admin/gestion-comptes.php">Gestion comptes</a>
    <?php endif; ?>

    <a href="/Tp php/facturation/modules/produits/liste.php">Liste produits</a>
</nav>
<section class="card">
    <h2>Liste des produits</h2>
    <table>
        <thead><tr><th>Code</th><th>Nom</th><th>Prix HT</th><th>Stock</th><th>Expiration</th></tr></thead>
        <tbody>
        <?php foreach ($products as $p): ?>
            <tr>
                <td><?= htmlspecialchars((string) $p['code_barre']) ?></td>
                <td><?= htmlspecialchars((string) $p['nom']) ?></td>
                <td><?= (float) $p['prix_unitaire_ht'] ?></td>
                <td><?= (int) $p['quantite_stock'] ?></td>
                <td><?= htmlspecialchars((string) $p['date_expiration']) ?></td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
</section>
<?php include dirname(__DIR__, 2) . '/includes/footer.php'; ?>
