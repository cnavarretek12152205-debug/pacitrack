<header class="topbar">
    <button class="menu-button" onclick="toggleMenu(true)" aria-label="Open navigation"><?= icon('menu') ?></button>
    
    <form class="search" method="get" action="index.php">
        <input type="hidden" name="page" value="<?= h($page) ?>">
        <?= icon('search', 16) ?>
        <input name="q" value="<?= h($q) ?>" placeholder="Search equipment, clients, locations..." aria-label="Search records">
        <button type="submit" class="search-submit">Search</button>
        <div class="search-shortcut">/</div>
    </form>

    <?php $alertCount = $stats['maintenance'] + $stats['warranty_alerts']; ?>
    <a class="icon-button" href="index.php?page=maintenance" aria-label="View alerts" title="<?= $alertCount ?> alert(s)">
        <?= icon('bell', 18) ?>
        <?php if ($alertCount): ?><i><?= $alertCount > 9 ? '9+' : $alertCount ?></i><?php endif; ?>
    </a>

    <div class="profile-menu">
        <button class="profile-trigger" onclick="toggleProfile()" aria-haspopup="true" aria-expanded="false">
            <span><?= h($initials) ?></span>
            <div><strong><?= h($currentUser['name']) ?></strong><small><?= h($currentUser['role']) ?></small></div>
            <b><?= icon('chevron-down', 14) ?></b>
        </button>
        <button class="profile-scrim" id="profileScrim" onclick="toggleProfile()" hidden aria-label="Close menu"></button>
        <div class="profile-dropdown" id="profileDropdown" hidden>
            <div class="profile-summary">
                <span><?= h($initials) ?></span>
                <p><strong><?= h($currentUser['name']) ?></strong><small><?= h($currentUser['email']) ?></small></p>
            </div>
            <div class="profile-permission">
                <?= icon('shield') ?>
                <div>
                    <strong><?= h($currentUser['role']) ?> access</strong>
                    <small><?= is_admin() ? 'System oversight enabled' : 'Assigned workflows' ?></small>
                </div>
            </div>
            <button onclick="location.href='index.php?logout=1'"><?= icon('lock', 14) ?> Sign out securely</button>
        </div>
    </div>

    <?php if (is_admin()): ?>
        <button class="primary-button" onclick="openAdd()"><?= icon('plus', 16) ?><span>Add equipment</span></button>
    <?php endif; ?>
</header>