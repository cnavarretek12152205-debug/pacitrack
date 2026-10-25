<?php require __DIR__ . '/../partials/stats.php'; ?>

<?php
$isSearching     = $q !== '';
$tableRows       = $equipment;
$tableTitle      = 'Equipment';
$tableSubtitle   = $isSearching ? 'Search results for "'.$q.'"' : 'All registered equipment across your network';
$showAddButton   = !$isSearching;
$showClearSearch = $isSearching;
$showEditColumn  = is_admin();
$emptyMessage    = $isSearching ? 'No equipment matches "'.$q.'".' : 'No equipment registered yet.';
require __DIR__ . '/../partials/equipment_table.php';