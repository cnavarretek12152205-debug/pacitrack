<?php $userRows = get_users($pdo); ?>
<section class="panel table-panel">
    <div class="panel-head">
        <div><h2>User management</h2><p>Accounts and assigned roles</p></div>
    </div>
    <div class="table-scroll">
        <table>
            <thead>
                <tr><th>Name</th><th>Email</th><th>Role</th><th>Status</th><th>Created</th></tr>
            </thead>
            <tbody>
            <?php foreach ($userRows as $u): ?>
                <tr>
                    <td><strong><?= h($u['full_name']) ?></strong></td>
                    <td><?= h($u['email']) ?></td>
                    <td><?= h($u['role']) ?></td>
                    <td><span class="status <?= $u['is_active'] ? 'in-service' : 'retired' ?>"><i></i><?= $u['is_active'] ? 'Active' : 'Disabled' ?></span></td>
                    <td><?= h($u['created_at']) ?></td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</section>