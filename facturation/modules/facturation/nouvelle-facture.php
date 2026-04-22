<?php
declare(strict_types=1);
require_once dirname(__DIR__, 2) . '/config/config.php';
require_once dirname(__DIR__, 2) . '/auth/session.php';
require_once dirname(__DIR__, 2) . '/includes/fonctions-produits.php';
require_once dirname(__DIR__, 2) . '/includes/fonctions-factures.php';
require_role('caissier');

if (!isset($_SESSION['panier'])) {
    $_SESSION['panier'] = [];
}
$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    if ($action === 'ajouter') {
        $barcode = trim((string) ($_POST['code_barre'] ?? ''));
        $qty = (int) ($_POST['quantite'] ?? 0);
        $product = find_product_by_barcode($barcode);
        if (!$product) {
            $error = 'Produit inconnu. Le manager doit d abord l enregistrer.';
        } elseif ($qty <= 0) {
            $error = 'Quantite invalide.';
        } elseif ($qty > (int) $product['quantite_stock']) {
            $error = 'Stock insuffisant.';
        } else {
            $_SESSION['panier'][] = [
                'code_barre' => $product['code_barre'],
                'nom' => $product['nom'],
                'prix_unitaire_ht' => (float) $product['prix_unitaire_ht'],
                'quantite' => $qty,
                'sous_total_ht' => (float) $product['prix_unitaire_ht'] * $qty,
            ];
        }
    }
    if ($action === 'valider') {
        $lines = $_SESSION['panier'];
        if (!$lines) {
            $error = 'Le panier est vide.';
        } else {
            $products = load_products();
            foreach ($lines as $line) {
                foreach ($products as &$product) {
                    if ($product['code_barre'] === $line['code_barre']) {
                        if ((int) $product['quantite_stock'] < (int) $line['quantite']) {
                            $error = 'Stock devenu insuffisant pour: ' . $product['nom'];
                        } else {
                            $product['quantite_stock'] -= (int) $line['quantite'];
                        }
                        break;
                    }
                }
                unset($product);
                if ($error) {
                    break;
                }
            }
            if (!$error) {
                save_products($products);
                $invoice = create_invoice($lines, (string) ($_SESSION['user']['identifiant'] ?? 'caissier'));
                $invoices = load_invoices();
                $invoices[] = $invoice;
                save_invoices($invoices);
                $_SESSION['last_invoice'] = $invoice;
                $_SESSION['panier'] = [];
                header('Location: ' . BASE_URL . '/modules/facturation/afficher-facture.php');
                exit;
            }
        }
    }
    if ($action === 'vider') {
        $_SESSION['panier'] = [];
    }
}

$panier = $_SESSION['panier'];
$totalHt = array_sum(array_column($panier, 'sous_total_ht'));
$tva = $totalHt * TVA_RATE;
$totalTtc = $totalHt + $tva;

include dirname(__DIR__, 2) . '/includes/header.php';
?>
<nav class="menu">
    <a href="<?= BASE_URL ?>/modules/facturation/nouvelle-facture.php">Acceuil</a>
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
    <h2>Nouvelle facture</h2>
    <?php if ($error): ?><div class="alert error"><?= htmlspecialchars($error) ?></div><?php endif; ?>
    <div class="card">
        <h3>Scanner code-barres</h3>
        <button type="button" id="start-scan-btn">Demarrer scan</button>
        <button type="button" id="stop-scan-btn">Arreter scan</button>
        <button type="button" id="refresh-cameras-btn">Actualiser cameras</button>
        <label>Camera
            <select id="scanner-device"></select>
        </label>
        <p id="scanner-status">Scanner inactif.</p>
        <video id="scanner-video" autoplay playsinline muted style="width:100%;max-width:420px;border:1px solid #ccc;"></video>
    </div>
    <form method="post">
        <input type="hidden" name="action" value="ajouter">
        <label>Code-barres <input name="code_barre" required></label>
        <label>Quantite <input type="number" min="1" name="quantite" required></label>
        <button type="submit">Ajouter</button>
    </form>
    <table>
        <thead><tr><th>Designation</th><th>Prix HT</th><th>Qte</th><th>Sous-total HT</th></tr></thead>
        <tbody>
        <?php foreach ($panier as $line): ?>
            <tr>
                <td><?= htmlspecialchars((string) $line['nom']) ?></td>
                <td><?= (float) $line['prix_unitaire_ht'] ?></td>
                <td><?= (int) $line['quantite'] ?></td>
                <td><?= (float) $line['sous_total_ht'] ?></td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
    <p>Total HT: <?= round($totalHt, 2) ?> CDF</p>
    <p>TVA (<?= TVA_RATE * 100 ?>%): <?= round($tva, 2) ?> CDF</p>
    <p>Net a payer: <?= round($totalTtc, 2) ?> CDF</p>
    <form method="post"><input type="hidden" name="action" value="valider"><button type="submit">Valider facture</button></form>
    <form method="post"><input type="hidden" name="action" value="vider"><button type="submit">Vider</button></form>
</section>
<?php include dirname(__DIR__, 2) . '/includes/footer.php'; ?>
