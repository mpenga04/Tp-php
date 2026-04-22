<?php
declare(strict_types=1);
require_once dirname(__DIR__, 2) . '/config/config.php';
require_once dirname(__DIR__, 2) . '/auth/session.php';
require_role('caissier');

$invoice = $_SESSION['last_invoice'] ?? null;
include dirname(__DIR__, 2) . '/includes/header.php';
?>
<section class="card">
    <h2>Facture</h2>
    <?php if (!$invoice): ?>
        <p>Aucune facture recente.</p>
    <?php else: ?>
        <p>ID: <?= htmlspecialchars((string) $invoice['id_facture']) ?> | Date: <?= htmlspecialchars((string) $invoice['date']) ?> <?= htmlspecialchars((string) $invoice['heure']) ?></p>
        <table>
            <thead><tr><th>Designation</th><th>Prix unit. HT</th><th>Qte</th><th>Sous-total HT</th></tr></thead>
            <tbody>
            <?php foreach ($invoice['articles'] as $line): ?>
                <tr>
                    <td><?= htmlspecialchars((string) $line['nom']) ?></td>
                    <td><?= (float) $line['prix_unitaire_ht'] ?> CDF</td>
                    <td><?= (int) $line['quantite'] ?></td>
                    <td><?= (float) $line['sous_total_ht'] ?> CDF</td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
        <p>Total HT: <?= (float) $invoice['total_ht'] ?> CDF</p>
        <p>TVA: <?= (float) $invoice['tva'] ?> CDF</p>
        <p>Net a payer: <?= (float) $invoice['total_ttc'] ?> CDF</p>
    <?php endif; ?>
</section>
<?php include dirname(__DIR__, 2) . '/includes/footer.php'; ?>
