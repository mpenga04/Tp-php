<?php
declare(strict_types=1);

require_once dirname(__DIR__) . '/config/config.php';

function load_products(): array
{
    if (!file_exists(PRODUITS_FILE)) {
        return [];
    }
    $data = json_decode(file_get_contents(PRODUITS_FILE) ?: '[]', true);
    return is_array($data) ? $data : [];
}

function save_products(array $products): bool
{
    return file_put_contents(PRODUITS_FILE, json_encode(array_values($products), JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE)) !== false;
}

function find_product_by_barcode(string $barcode): ?array
{
    foreach (load_products() as $product) {
        if (($product['code_barre'] ?? '') === $barcode) {
            return $product;
        }
    }
    return null;
}

function validate_product_data(array $data): array
{
    $errors = [];
    if (trim((string) ($data['code_barre'] ?? '')) === '') {
        $errors[] = 'Code-barres obligatoire.';
    }
    if (trim((string) ($data['nom'] ?? '')) === '') {
        $errors[] = 'Nom obligatoire.';
    }
    if (!is_numeric($data['prix_unitaire_ht'] ?? null) || (float) $data['prix_unitaire_ht'] <= 0) {
        $errors[] = 'Prix unitaire invalide.';
    }
    if (!is_numeric($data['quantite_stock'] ?? null) || (int) $data['quantite_stock'] < 0) {
        $errors[] = 'Quantite en stock invalide.';
    }
    $date = DateTime::createFromFormat('Y-m-d', (string) ($data['date_expiration'] ?? ''));
    if (!$date || $date->format('Y-m-d') !== ($data['date_expiration'] ?? '')) {
        $errors[] = 'Date expiration invalide (format attendu YYYY-MM-DD).';
    }
    return $errors;
}
