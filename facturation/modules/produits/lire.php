<?php
declare(strict_types=1);
require_once dirname(__DIR__, 2) . '/config/config.php';
require_once dirname(__DIR__, 2) . '/auth/session.php';
require_once dirname(__DIR__, 2) . '/includes/fonctions-produits.php';
require_role('caissier');

$barcode = trim((string) ($_GET['code_barre'] ?? ''));
$product = $barcode ? find_product_by_barcode($barcode) : null;

include dirname(__DIR__, 2) . '/includes/header.php';
?>
<section class="card">
    <h2>Lecture produit</h2>
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
    <form method="get">
        <label>Code-barres <input name="code_barre" value="<?= htmlspecialchars($barcode) ?>" required></label>
        <button type="submit">Rechercher</button>
    </form>
    <?php if ($barcode && !$product): ?>
        <p>Produit inconnu. Demandez au manager de l'enregistrer.</p>
    <?php elseif ($product): ?>
        <ul>
            <li>Nom: <?= htmlspecialchars((string) $product['nom']) ?></li>
            <li>Prix HT: <?= (float) $product['prix_unitaire_ht'] ?> CDF</li>
            <li>Stock: <?= (int) $product['quantite_stock'] ?></li>
        </ul>
    <?php endif; ?>
</section>
<?php include dirname(__DIR__, 2) . '/includes/footer.php'; ?>
