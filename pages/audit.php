<?php $logRows = get_audit_logs($pdo, 50); ?>
<section class="panel table-panel">
    <div class="panel-head">
        <div><h2>Audit logs</h2><p>Recent security and equipment activity (last 50 events)</p></div>
    </div>
    <div class="table-scroll">
        <table>
            <thead>
                <tr><th>Timestamp</th><th>User</th><th>Action</th><th>Details</th><th>IP address</th></tr>
            </thead>
            <tbody>
            <?php foreach ($logRows as $log): ?>
                <tr>
                    <td><?= h($log['created_at']) ?></td>
                    <td><?= h($log['full_name'] ?? 'System') ?></td>
                    <td><strong><?= h($log['action']) ?></strong></td>
                    <td><?= h($log['details']) ?></td>
                    <td><?= h($log['ip_address']) ?></td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</section>