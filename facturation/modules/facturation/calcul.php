<?php
declare(strict_types=1);

function calculer_totaux_facture(array $articles, float $tvaRate): array
{
    $totalHt = array_sum(array_column($articles, 'sous_total_ht'));
    $tva = round($totalHt * $tvaRate, 2);
    return [
        'total_ht' => round($totalHt, 2),
        'tva' => $tva,
        'total_ttc' => round($totalHt + $tva, 2),
    ];
}
