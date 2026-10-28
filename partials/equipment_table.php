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
    <div class="panel-head" style="padding-bottom: 12px;">
        <div>
            <h2 style="font-size: 15px; color: var(--navy); margin-bottom: 4px;"><?= h($tableTitle) ?></h2>
            <p style="font-size: 11px; color: #6e8083;"><?= h($tableSubtitle) ?></p>
        </div>
        <div style="display: flex; align-items: center; gap: 10px;">
            <?php if ($showClearSearch): ?>
                <a class="text-button" href="index.php?page=<?= h($page) ?>" style="background: #f4f7f9; padding: 6px 12px; border-radius: 6px; color: #5f7175; font-weight: 600;">Clear search <?= icon('close', 14) ?></a>
            <?php elseif ($showAddButton && is_admin()): ?>
                <button class="text-button" onclick="openAdd()" style="background: #ffffff; border: 1px solid var(--line); padding: 6px 12px; border-radius: 6px; color: var(--navy); font-weight: 600; box-shadow: 0 2px 5px rgba(0,0,0,0.02);"><?= icon('plus', 14) ?> Register equipment</button>
            <?php endif; ?>
        </div>
    </div>

    <?php if ($tableFilters): ?><div class="filter-tabs"><?= $tableFilters ?></div><?php endif; ?>

    <div class="table-scroll">
        <table>
            <thead>
                <tr>
                    <th>Equipment</th>
                    <th>Client &amp; Location</th>
                    <th>Category</th>
                    <th>Status</th>
                    <th>Next Service</th>
                    <th>Warranty Expiry</th>
                    <?php if ($showEditColumn): ?><th style="text-align: right; padding-right: 20px;">Actions</th><?php endif; ?>
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
                            <span><?= icon('box', 16) ?></span>
                            <div>
                                <strong><?= h($item['name']) ?></strong>
                                <small style="color: var(--teal); font-weight: 500;"><?= h($item['equipment_id']) ?></small>
                            </div>
                        </div>
                    </td>
                    <td>
                        <strong><?= h($item['client']) ?></strong>
                        <small style="display: flex; align-items: center; gap: 4px; margin-top: 4px;"><?= icon('building', 10) ?> <?= h($item['location']) ?></small>
                    </td>
                    <td><span style="background: #f4f7f9; padding: 4px 8px; border-radius: 4px; font-size: 9px; font-weight: 600; color: #5f7175;"><?= h($item['equipment_type']) ?></span></td>
                    <td><span class="status <?= h($statusCls) ?>"><i></i><?= h($item['status']) ?></span></td>
                    <td style="color: #5f7175; font-weight: 500;"><?= h($item['due_date']) ?></td>
                    <td class="<?= h($warrantyCls) ?>">
                        <?php if ($warrantyCls === 'warranty-expired'): ?><?= icon('alert', 12) ?> <?php endif; ?>
                        <?= h($item['warranty_expiry'] ?: '—') ?>
                    </td>
                    <?php if ($showEditColumn): ?>
                        <td class="row-action-cell" style="padding-right: 20px;">
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
                                title="Edit equipment"
                                style="border: 1px solid var(--line); background: white; padding: 6px; box-shadow: 0 1px 3px rgba(0,0,0,0.02);">
                                <?= icon('edit', 14) ?>
                            </button>
                        </td>
                    <?php endif; ?>
                </tr>
            <?php endforeach; else: ?>
                <tr><td colspan="<?= $colspan ?>" class="empty-cell" style="padding: 40px; text-align: center; color: #8a999c;">
                    <?= icon('box', 24) ?><br><br>
                    <strong style="font-size: 13px; color: var(--navy);">No records found</strong>
                    <p style="font-size: 11px; margin-top: 5px;"><?= h($emptyMessage) ?></p>
                </td></tr>
            <?php endif; ?>
            </tbody>
        </table>
    </div>

    <?php if ($showViewAll): ?>
        <div class="table-footer" style="background: #fafcfc;">
            <a class="text-button" href="index.php?page=equipment">
                View all <?= number_format($stats['total']) ?> assets <?= icon('arrow', 14) ?>
            </a>
        </div>
    <?php endif; ?>
</section>