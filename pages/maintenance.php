<?php
$mfilter = (string)($_GET['mfilter'] ?? 'all');
$allowed = match ($mfilter) {
    'due'    => ['Maintenance due'],
    'repair' => ['Under repair'],
    default  => ['Maintenance due', 'Under repair'],
};
$rows = array_values(array_filter($equipment, fn($x) => in_array($x['status'], $allowed, true)));
$tabs =
    '<a href="index.php?page=maintenance&amp;mfilter=all"    class="filter-tab ' . ($mfilter === 'all'    ? 'active' : '') . '">All</a>' .
    '<a href="index.php?page=maintenance&amp;mfilter=due"    class="filter-tab ' . ($mfilter === 'due'    ? 'active' : '') . '">Maintenance due</a>' .
    '<a href="index.php?page=maintenance&amp;mfilter=repair" class="filter-tab ' . ($mfilter === 'repair' ? 'active' : '') . '">Under repair</a>';

$tableRows     = $rows;
$tableTitle    = 'Maintenance';
$tableSubtitle = 'Assets currently requiring service attention';
$tableFilters  = $tabs;
$emptyMessage  = 'No maintenance items in this view.';
require __DIR__ . '/../partials/equipment_table.php';