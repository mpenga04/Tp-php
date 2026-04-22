<?php
declare(strict_types=1);

require_once dirname(__DIR__) . '/config/config.php';
require_once dirname(__DIR__) . '/includes/fonctions-produits.php';

function load_invoices(): array
{
    if (!file_exists(FACTURES_FILE)) {
        return [];
    }
    $data = json_decode(file_get_contents(FACTURES_FILE) ?: '[]', true);
    return is_array($data) ? $data : [];
}

function save_invoices(array $invoices): bool
{
    return file_put_contents(FACTURES_FILE, json_encode(array_values($invoices), JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE)) !== false;
}

function generate_invoice_id(): string
{
    $today = date('Ymd');
    $invoices = load_invoices();
    $count = 0;
    foreach ($invoices as $inv) {
        if (str_contains((string) ($inv['id_facture'] ?? ''), $today)) {
            $count++;
        }
    }
    return 'FAC-' . $today . '-' . str_pad((string) ($count + 1), 3, '0', STR_PAD_LEFT);
}

function create_invoice(array $lines, string $caissier): array
{
    $totalHt = array_sum(array_column($lines, 'sous_total_ht'));
    $tva = (float) round($totalHt * TVA_RATE, 2);
    $totalTtc = (float) round($totalHt + $tva, 2);

    return [
        'id_facture' => generate_invoice_id(),
        'date' => date('Y-m-d'),
        'heure' => date('H:i:s'),
        'caissier' => $caissier,
        'articles' => $lines,
        'total_ht' => $totalHt,
        'tva' => $tva,
        'total_ttc' => $totalTtc,
    ];
}
