<?php
/**
 * Expects:
 *   $tableRows        array  - equipment rows
 *   $tableTitle       string
 *   $tableSubtitle    string
 *   $tableFilters     string (optional HTML for filter tabs)
 *   $showEditColumn   bool   - default is_admin()
 *   $showAddButton    bool   - default false
 *   $showClearSearch  bool   - default false
 *   $showViewAll      bool   - default false
 *   $emptyMessage     string - default 'No records'
 */
$showEditColumn  = $showEditColumn  ?? is_admin();
$showAddButton   = $showAddButton   ?? false;
$showClearSearch = $showClearSearch ?? false;
$showViewAll     = $showViewAll     ?? false;
$tableFilters    = $tableFilters    ?? '';
$emptyMessage    = $emptyMessage    ?? 'No records in this view.';
$colspan         = $showEditColumn ? 7 : 6;
?>
<section class="panel table-panel">
    <div class="panel-head">
        <div>
            <h2><?= h($tableTitle) ?></h2>
            <p><?= h($tableSubtitle) ?></p>
        </div>
        <?php if ($showClearSearch): ?>
            <a class="text-button" href="index.php?page=<?= h($page) ?>">Clear search <?= icon('close', 14) ?></a>
        <?php elseif ($showAddButton && is_admin()): ?>
            <button class="text-button" onclick="openAdd()">Register equipment <?= icon('arrow', 15) ?></button>
        <?php endif; ?>
    </div>

    <?php if ($tableFilters): ?><div class="filter-tabs"><?= $tableFilters ?></div><?php endif; ?>

    <div class="table-scroll">
        <table>
            <thead>
                <tr>
                    <th>Equipment</th>
                    <th>Client &amp; location</th>
                    <th>Category</th>
                    <th>Status</th>
                    <th>Next service</th>
                    <th>Warranty expiry</th>
                    <?php if ($showEditColumn): ?><th></th><?php endif; ?>
                </tr>
            </thead>
            <tbody>
            <?php if ($tableRows): foreach ($tableRows as $item):
                $statusCls   = status_class($item['status']);
                $warrantyCls = warranty_class($item['warranty_expiry']);
            ?>
                <tr>
                    <td>
                        <div class="equipment-cell">
                            <span><?= icon('box') ?></span>
                            <div><strong><?= h($item['name']) ?></strong><small><?= h($item['equipment_id']) ?></small></div>
                        </div>
                    </td>
                    <td><strong><?= h($item['client']) ?></strong><small><?= h($item['location']) ?></small></td>
                    <td><?= h($item['equipment_type']) ?></td>
                    <td><span class="status <?= h($statusCls) ?>"><i></i><?= h($item['status']) ?></span></td>
                    <td><?= h($item['due_date']) ?></td>
                    <td class="<?= h($warrantyCls) ?>"><?= h($item['warranty_expiry'] ?: '—') ?></td>
                    <?php if ($showEditColumn): ?>
                        <td class="row-action-cell">
                            <button class="row-button"
                                data-id="<?= h($item['id']) ?>"
                                data-equipment-id="<?= h($item['equipment_id']) ?>"
                                data-name="<?= h($item['name']) ?>"
                                data-client="<?= h($item['client']) ?>"
                                data-location="<?= h($item['location']) ?>"
                                data-status="<?= h($item['status']) ?>"
                                data-due-date="<?= h($item['due_date']) ?>"
                                data-warranty-expiry="<?= h($item['warranty_expiry'] ?: '') ?>"
                                data-equipment-type="<?= h($item['equipment_type']) ?>"
                                onclick="openEdit(this)"
                                aria-label="Edit <?= h($item['equipment_id']) ?>"
                                title="Edit equipment"><?= icon('edit', 14) ?></button>
                        </td>
                    <?php endif; ?>
                </tr>
            <?php endforeach; else: ?>
                <tr><td colspan="<?= $colspan ?>" class="empty-cell"><?= h($emptyMessage) ?></td></tr>
            <?php endif; ?>
            </tbody>
        </table>
    </div>

    <?php if ($showViewAll): ?>
        <div class="table-footer">
            <a class="text-button" href="index.php?page=equipment">
                View all <?= number_format($stats['total']) ?> assets <?= icon('arrow', 14) ?>
            </a>
        </div>
    <?php endif; ?>
</section>