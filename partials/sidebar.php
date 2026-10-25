<aside class="sidebar" id="sidebar">
    <div class="brand">
        <div class="brand-mark"><?= icon('shield', 23) ?></div>
        <div><strong><?= h($config['app']['name']) ?></strong><span><?= h($config['app']['tagline']) ?></span></div>
    </div>
    <button class="mobile-close" onclick="toggleMenu(false)" aria-label="Close navigation"><?= icon('close') ?></button>

    <nav>
        <p class="nav-label">Workspace</p>
        <?php foreach ($navItems as $slug => $meta):
            if (!is_admin() && in_array($slug, $adminOnly, true)) continue; ?>
            <a class="nav-item <?= $page === $slug ? 'active' : '' ?>" href="index.php?page=<?= urlencode($slug) ?>">
                <?= icon($meta[0]) ?>
                <span><?= h($meta[1]) ?></span>
                <?php if ($slug === 'maintenance' && $stats['maintenance']): ?>
                    <b><?= number_format($stats['maintenance']) ?></b>
                <?php endif; ?>
            </a>
        <?php endforeach; ?>
    </nav>

    <div class="security-card">
        <div class="security-icon"><?= icon('lock') ?></div>
        <strong>Protected access</strong>
        <p>Role-based permissions and audit monitoring are enabled.</p>
        <span><i></i> Session authenticated</span>
    </div>
    <div class="user-card">
        <div class="avatar"><?= h($initials) ?></div>
        <div><strong><?= h($currentUser['name']) ?></strong><span><?= h($currentUser['role']) ?></span></div>
    </div>
    <a class="sign-out" href="index.php?logout=1"><?= icon('lock', 14) ?> Sign out securely</a>
</aside>
<button class="scrim" id="scrim" onclick="toggleMenu(false)" aria-label="Close navigation overlay" hidden></button>
<main>