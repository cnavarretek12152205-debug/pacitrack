<?php
$wfilter = (string)($_GET['wfilter'] ?? 'all');
$today_  = date('Y-m-d');
$limit   = date('Y-m-d', strtotime('+30 days'));

$rows = array_values(array_filter($equipment, fn($x) => !empty($x['warranty_expiry'])));
if ($wfilter === 'expiring') {
    $rows = array_values(array_filter($rows, fn($x) => $x['warranty_expiry'] >= $today_ && $x['warranty_expiry'] <= $limit));
} elseif ($wfilter === 'expired') {
    $rows = array_values(array_filter($rows, fn($x) => $x['warranty_expiry'] < $today_));
}

$tabs =
    '<a href="index.php?page=warranties&amp;wfilter=all"      class="filter-tab ' . ($wfilter === 'all'      ? 'active' : '') . '">All</a>' .
    '<a href="index.php?page=warranties&amp;wfilter=expiring" class="filter-tab ' . ($wfilter === 'expiring' ? 'active' : '') . '">Expiring soon</a>' .
    '<a href="index.php?page=warranties&amp;wfilter=expired"  class="filter-tab ' . ($wfilter === 'expired'  ? 'active' : '') . '">Expired</a>';

$tableRows     = $rows;
$tableTitle    = 'Warranties';
$tableSubtitle = 'Coverage dates across your equipment fleet';
$tableFilters  = $tabs;
$emptyMessage  = 'No warranties match this filter.';
require __DIR__ . '/../partials/equipment_table.php';