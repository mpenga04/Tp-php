<?php
declare(strict_types=1);
require_once dirname(__DIR__, 2) . '/config/config.php';
require_once dirname(__DIR__, 2) . '/auth/session.php';
require_once dirname(__DIR__, 2) . '/includes/fonctions-produits.php';
require_role('manager');

$form = [
    'code_barre' => (string) ($_GET['code_barre'] ?? ''),
    'nom' => '',
    'prix_unitaire_ht' => '',
    'date_expiration' => '',
    'quantite_stock' => '',
];
$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $form = [
        'code_barre' => trim((string) ($_POST['code_barre'] ?? '')),
        'nom' => trim((string) ($_POST['nom'] ?? '')),
        'prix_unitaire_ht' => (string) ($_POST['prix_unitaire_ht'] ?? ''),
        'date_expiration' => (string) ($_POST['date_expiration'] ?? ''),
        'quantite_stock' => (string) ($_POST['quantite_stock'] ?? ''),
    ];
    if (find_product_by_barcode($form['code_barre'])) {
        $errors[] = 'Ce code-barres existe deja.';
    }
    $errors = array_merge($errors, validate_product_data($form));
    if (!$errors) {
        $products = load_products();
        $products[] = [
            'code_barre' => $form['code_barre'],
            'nom' => $form['nom'],
            'prix_unitaire_ht' => (float) $form['prix_unitaire_ht'],
            'date_expiration' => $form['date_expiration'],
            'quantite_stock' => (int) $form['quantite_stock'],
            'date_enregistrement' => date('Y-m-d'),
        ];
        save_products($products);
        $_SESSION['flash_ok'] = 'Produit enregistre avec succes.';
        header('Location: ' . BASE_URL . '/modules/produits/liste.php');
        exit;
    }
}

include dirname(__DIR__, 2) . '/includes/header.php';
?>
<nav class="menu">
    <a href="<?= BASE_URL ?>/index.php">Acceuil</a>
    <a href="<?= BASE_URL ?>/modules/facturation/nouvelle-facture.php">Nouvelle facture</a>
    <?php if ($user['role'] === 'caissier'): ?>    
    <?php else: ?>
    <a href="<?= BASE_URL ?>/rapports/rapport-journalier.php">Rapport journalier</a>
    <?php endif; ?>

    <?php if ($user['role'] === 'caissier' || $user['role'] === 'manager'): ?>    
    <?php else: ?>
    <a href="<?= BASE_URL ?>/modules/admin/gestion-comptes.php">Gestion comptes</a>
    <?php endif; ?>

    <a href="<?= BASE_URL ?>/modules/produits/liste.php">Liste produits</a>
</nav>
<section class="card">
    <h2>Enregistrer un produit</h2>
    <?php foreach ($errors as $error): ?><div class="alert error"><?= htmlspecialchars($error) ?></div><?php endforeach; ?>
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
        <label>Code-barres <input name="code_barre" value="<?= htmlspecialchars($form['code_barre']) ?>" required></label>
        <label>Nom <input name="nom" value="<?= htmlspecialchars($form['nom']) ?>" required></label>
        <label>Prix unitaire HT (CDF) <input type="number" step="0.01" name="prix_unitaire_ht" value="<?= htmlspecialchars($form['prix_unitaire_ht']) ?>" required></label>
        <label>Date expiration <input type="date" name="date_expiration" value="<?= htmlspecialchars($form['date_expiration']) ?>" required></label>
        <label>Quantite stock <input type="number" min="0" name="quantite_stock" value="<?= htmlspecialchars($form['quantite_stock']) ?>" required></label>
        <button type="submit">Enregistrer</button>
    </form>
</section>
<?php include dirname(__DIR__, 2) . '/includes/footer.php'; ?>
