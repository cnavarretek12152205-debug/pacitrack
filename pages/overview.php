<?php
$statusRows       = get_status_counts($pdo);
$maintenanceItems = get_maintenance_items($pdo, 4);
$isSearching      = $q !== '';
$recentEquipment  = $isSearching ? $equipment : array_slice($equipment, 0, 5);

$donutColors = [
    'In service'      => '#0c8f83',
    'Maintenance due' => '#e9a85f',
    'Under repair'    => '#4f80c5',
    'In storage'      => '#b9c2c4',
    'Retired'         => '#8a999c',
];
$donutParts = [];
$accum = 0.0;
$totalForChart = max(1, $stats['total']);
foreach ($statusRows as $sr) {
    $pct  = ($sr['total'] / $totalForChart) * 100;
    $clr  = $donutColors[$sr['status']] ?? '#b9c2c4';
    $donutParts[] = $clr . ' ' . $accum . '% ' . ($accum + $pct) . '%';
    $accum += $pct;
}
$donutGradient = 'conic-gradient(' . implode(', ', $donutParts) . ')';

require __DIR__ . '/../partials/stats.php';
?>
<section class="dashboard-grid">
    <article class="panel lifecycle-panel">
        <div class="panel-head">
            <div><h2>Equipment lifecycle</h2><p>Current status across registered assets</p></div>
        </div>
        <div class="chart-wrap">
            <div class="donut" style="background: <?= h($donutGradient) ?>">
                <div><strong><?= number_format($stats['total']) ?></strong><span>Total assets</span></div>
            </div>
            <div class="legend">
                <?php
                $dotClasses = ['In service'=>'teal','Maintenance due'=>'amber','Under repair'=>'blue','In storage'=>'gray','Retired'=>'gray'];
                foreach ($statusRows as $sr): ?>
                    <div>
                        <i class="dot <?= h($dotClasses[$sr['status']] ?? 'gray') ?>"></i>
                        <span><?= h($sr['status']) ?></span>
                        <strong><?= number_format((int)$sr['total']) ?></strong>
                        <em><?= $stats['total'] ? number_format(((int)$sr['total'] / $stats['total']) * 100, 1) . '%' : '0%' ?></em>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </article>

    <article class="panel alerts-panel">
        <div class="panel-head">
            <div><h2>Priority alerts</h2><p>Items that need your attention</p></div>
            <a class="text-button" href="index.php?page=maintenance">View all <?= icon('arrow', 15) ?></a>
        </div>
        <div class="alert-list">
            <div class="alert-row">
                <div class="alert-symbol red"><?= icon('alert') ?></div>
                <div>
                    <strong>Warranty expiring soon</strong>
                    <p><?= number_format($stats['warranty_alerts']) ?> items within the next 30 days or already expired</p>
                </div>
            </div>
            <?php if ($maintenanceItems): foreach ($maintenanceItems as $mr): ?>
                <div class="alert-row">
                    <div class="alert-symbol amber"><?= icon('tool') ?></div>
                    <div>
                        <strong><?= h($mr['name']) ?></strong>
                        <p><?= h($mr['equipment_id']) ?> · <?= h($mr['status']) ?> · due <?= h($mr['due_date']) ?></p>
                    </div>
                </div>
            <?php endforeach; else: ?>
                <div class="alert-row">
                    <div class="alert-symbol blue"><?= icon('check') ?></div>
                    <div><strong>No active maintenance alerts</strong><p>Equipment service status is up to date.</p></div>
                </div>
            <?php endif; ?>
        </div>
    </article>
</section>

<?php
$tableRows       = $recentEquipment;
$tableTitle      = 'Recently updated equipment';
$tableSubtitle   = $isSearching ? 'Search results for "'.$q.'"' : 'Latest records from your equipment network';
$showAddButton   = !$isSearching;
$showClearSearch = $isSearching;
$showViewAll     = !$isSearching && $stats['total'] > count($recentEquipment);
$emptyMessage    = $isSearching ? 'No equipment matches "'.$q.'".' : 'No equipment records yet.';
require __DIR__ . '/../partials/equipment_table.php';