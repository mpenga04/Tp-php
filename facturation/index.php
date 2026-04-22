<?php
declare(strict_types=1);
require_once __DIR__ . '/config/config.php';
require_once __DIR__ . '/auth/session.php';
require_login();

include __DIR__ . '/includes/header.php';
?>
<nav class="menu">
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
    <h2>Accueil</h2>
    <p>Bienvenue dans le systeme de facturation.</p>
</section>
<?php include __DIR__ . '/includes/footer.php'; ?>
