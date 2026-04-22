<?php
declare(strict_types=1);
require_once dirname(__DIR__) . '/config/config.php';
require_once dirname(__DIR__) . '/auth/session.php';
require_once dirname(__DIR__) . '/includes/fonctions-factures.php';
require_role('manager');

$date = $_GET['date'] ?? date('Y-m-d');
$invoices = array_values(array_filter(load_invoices(), static fn(array $i): bool => ($i['date'] ?? '') === $date));
$ca = array_sum(array_column($invoices, 'total_ttc'));

include dirname(__DIR__) . '/includes/header.php';
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
    <h2>Rapport </h2>
    <form method="get"><label>Date <input type="date" name="date" value="<?= htmlspecialchars($date) ?>"></label><button>Rechercher une vente</button></form>
    <p>Nombre de ventes: <?= count($invoices) ?></p>
    <p>Chiffre d affaires TTC: <?= round($ca, 2) ?> CDF</p>
</section>
<?php include dirname(__DIR__) . '/includes/footer.php'; ?>
