<?php $clientRows = get_clients($pdo); ?>
<section class="panel table-panel">
    <div class="panel-head">
        <div><h2>Clients</h2><p>Accounts with registered assets</p></div>
    </div>
    <div class="table-scroll">
        <table>
            <thead>
                <tr><th>Client</th><th>Location</th><th>Registered assets</th><th>Status</th></tr>
            </thead>
            <tbody>
            <?php if ($clientRows): foreach ($clientRows as $c): ?>
                <tr>
                    <td>
                        <div class="equipment-cell">
                            <span><?= icon('building') ?></span>
                            <div><strong><?= h($c['client']) ?></strong><small>Client account</small></div>
                        </div>
                    </td>
                    <td><?= h($c['location']) ?></td>
                    <td><strong><?= number_format((int)$c['assets']) ?> asset<?= ((int)$c['assets'] === 1) ? '' : 's' ?></strong></td>
                    <td><span class="status in-service"><i></i>Active</span></td>
                </tr>
            <?php endforeach; else: ?>
                <tr><td colspan="4" class="empty-cell">No clients on record yet.</td></tr>
            <?php endif; ?>
            </tbody>
        </table>
    </div>
</section>