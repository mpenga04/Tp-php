<?php
declare(strict_types=1);
require_once dirname(__DIR__) . '/config/config.php';
require_once dirname(__DIR__) . '/auth/session.php';
require_once dirname(__DIR__) . '/includes/fonctions-factures.php';
require_role('manager');

$month = $_GET['mois'] ?? date('Y-m');
$invoices = array_values(array_filter(load_invoices(), static function (array $i) use ($month): bool {
    return str_starts_with((string) ($i['date'] ?? ''), $month);
}));
$ca = array_sum(array_column($invoices, 'total_ttc'));

include dirname(__DIR__) . '/includes/header.php';
?>
<section class="card">
    <h2>Rapport mensuel</h2>
    <form method="get"><label>Mois <input type="month" name="mois" value="<?= htmlspecialchars($month) ?>"></label><button>Filtrer</button></form>
    <p>Nombre de factures: <?= count($invoices) ?></p>
    <p>Chiffre d affaires TTC: <?= round($ca, 2) ?> CDF</p>
</section>
<?php include dirname(__DIR__) . '/includes/footer.php'; ?>
