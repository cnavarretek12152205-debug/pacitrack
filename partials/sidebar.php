<aside class="sidebar" id="sidebar">
    <div class="brand" style="align-items: flex-start;">
        <!-- Logo Container -->
        <div style="background: white; padding: 4px; border-radius: 8px; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
            <img src="assets/img/logo.jpg" alt="Pacific Surgical Logo" style="height: 32px; width: auto; object-fit: contain;">
        </div>
        
        <!-- Text Container -->
        <div style="margin-top: 2px;">
            <strong style="font-size: 17px;"><?= h($config['app']['name']) ?></strong>
            <span style="color: #9ab1b5; font-size: 9px; margin-top: 2px; display: block; line-height: 1.2;"><?= h($config['app']['tagline']) ?></span>
        </div>
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
        <div class="security-icon"><?= icon('shield') ?></div>
        <strong>Pacific Surgical Inc.</strong>
        <p>Internal asset management and audit monitoring are strictly enforced.</p>
        <span><i></i> Secure Connection</span>
    </div>
    <div class="user-card">
        <div class="avatar"><?= h($initials) ?></div>
        <div><strong><?= h($currentUser['name']) ?></strong><span><?= h($currentUser['role']) ?></span></div>
    </div>
    <a class="sign-out" href="index.php?logout=1"><?= icon('lock', 14) ?> Sign out securely</a>
</aside>
<button class="scrim" id="scrim" onclick="toggleMenu(false)" aria-label="Close navigation overlay" hidden></button>
<main>