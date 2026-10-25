<section class="stats-grid">
    <article class="stat-card">
        <div class="stat-icon blue"><?= icon('box') ?></div>
        <span>Total equipment</span>
        <strong><?= number_format($stats['total']) ?></strong>
        <p>Registered assets</p>
    </article>
    <article class="stat-card">
        <div class="stat-icon amber"><?= icon('tool') ?></div>
        <span>Active maintenance</span>
        <strong><?= number_format($stats['maintenance']) ?></strong>
        <p class="attention">Due or under repair</p>
    </article>
    <article class="stat-card">
        <div class="stat-icon rose"><?= icon('alert') ?></div>
        <span>Warranty alerts</span>
        <strong><?= number_format($stats['warranty_alerts']) ?></strong>
        <p class="attention">Expiring or expired</p>
    </article>
    <article class="stat-card">
        <div class="stat-icon green"><?= icon('shield') ?></div>
        <span>Compliance tracking</span>
        <strong>Active</strong>
        <p>Audit trail enabled</p>
    </article>
</section>