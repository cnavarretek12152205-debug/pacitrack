<?php
$statusRows       = get_status_counts($pdo);
$maintenanceItems = get_maintenance_items($pdo, 4);
$isSearching      = $q !== '';
$recentEquipment  = $isSearching ? $equipment : array_slice($equipment, 0, 5);

$donutColors = [
    'In service'      => '#008080', // Teal
    'Maintenance due' => '#e9a85f', // Amber
    'Under repair'    => '#4f80c5', // Blue
    'In storage'      => '#b9c2c4', // Gray
    'Retired'         => '#8a999c', // Dark Gray
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

<!-- ENTERPRISE SECURITY BANNER -->
<div class="enterprise-banner">
    <div class="eb-item">
        <?= icon('lock', 16) ?> <span><strong>AES-256</strong> Data at Rest</span>
    </div>
    <div class="eb-divider"></div>
    <div class="eb-item">
        <?= icon('shield', 16) ?> <span><strong>RBAC</strong> Enforced</span>
    </div>
    <div class="eb-divider"></div>
    <div class="eb-item">
        <?= icon('history', 16) ?> <span><strong>Audit Logs</strong> Active</span>
    </div>
    <div class="eb-divider"></div>
    <div class="eb-item">
        <?= icon('clock', 16) ?> <span><strong>Auto-Backup</strong> 2 mins ago</span>
    </div>
</div>

<section class="dashboard-grid">
    <article class="panel lifecycle-panel">
        <div class="panel-head">
            <div><h2>Asset Lifecycle Distribution</h2><p>Real-time status across registered surgical equipment</p></div>
        </div>
        <div class="chart-wrap">
            <div class="donut" style="background: <?= h($donutGradient) ?>">
                <div><strong><?= number_format($stats['total']) ?></strong><span>Total Assets</span></div>
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
            <div><h2>Critical Workflows</h2><p>Actionable items requiring immediate attention</p></div>
            <a class="text-button" href="index.php?page=maintenance">View all <?= icon('arrow', 15) ?></a>
        </div>
        <div class="alert-list">
            <div class="alert-row">
                <div class="alert-symbol red"><?= icon('alert') ?></div>
                <div>
                    <strong>Warranty Expiration Alerts</strong>
                    <p><?= number_format($stats['warranty_alerts']) ?> assets require warranty review within 30 days.</p>
                </div>
            </div>
            <?php if ($maintenanceItems): foreach ($maintenanceItems as $mr): ?>
                <div class="alert-row">
                    <div class="alert-symbol amber"><?= icon('tool') ?></div>
                    <div>
                        <strong><?= h($mr['name']) ?></strong>
                        <p>ID: <?= h($mr['equipment_id']) ?> &middot; <?= h($mr['status']) ?> &middot; Target: <?= h($mr['due_date']) ?></p>
                    </div>
                </div>
            <?php endforeach; else: ?>
                <div class="alert-row">
                    <div class="alert-symbol blue"><?= icon('check') ?></div>
                    <div><strong>No pending maintenance</strong><p>All equipment service schedules are up to date.</p></div>
                </div>
            <?php endif; ?>
        </div>
    </article>
</section>

<?php
$tableRows       = $recentEquipment;
$tableTitle      = 'Equipment Registry (Recent Updates)';
$tableSubtitle   = $isSearching ? 'Search results for "'.$q.'"' : 'Latest synchronized records from the central database';
$showAddButton   = !$isSearching;
$showClearSearch = $isSearching;
$showViewAll     = !$isSearching && $stats['total'] > count($recentEquipment);
$emptyMessage    = $isSearching ? 'No equipment matches "'.$q.'".' : 'No equipment records yet.';
require __DIR__ . '/../partials/equipment_table.php';