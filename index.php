<?php
declare(strict_types=1);
require __DIR__ . '/bootstrap.php';

/* ---------- 1. Actions ---------- */
$loginError = '';
$notice     = '';

if (isset($_GET['logout'])) {
    require __DIR__ . '/actions/logout.php';
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $postAction = (string)($_POST['action'] ?? '');
    if ($postAction === 'login') {
        require __DIR__ . '/actions/login.php';
    } elseif (in_array($postAction, ['add_equipment', 'edit_equipment'], true)) {
        require __DIR__ . '/actions/save_equipment.php';
    }
}

/* ---------- 1b. Public auth actions (register / forgot / reset) ---------- */
$publicPage  = strtolower((string)($_GET['page'] ?? ''));
$publicPages = ['register', 'forgot_password', 'reset_password'];

// Handle their POSTs before the auth gate so non-logged-in users can submit
if ($_SERVER['REQUEST_METHOD'] === 'POST' && in_array($publicPage, $publicPages, true)) {
    $postAction = (string)($_POST['action'] ?? '');
    if ($postAction === 'register') {
        require __DIR__ . '/actions/register.php';
        if (empty($registerErrors)) { /* redirect() already called */ }
    } elseif ($postAction === 'forgot_password') {
        require __DIR__ . '/actions/forgot_password.php';
    } elseif ($postAction === 'reset_password') {
        require __DIR__ . '/actions/reset_password.php';
        if (empty($resetErrors)) { /* redirect() already called */ }
    }
}

/* ---------- 2. Public auth pages (no login required) ---------- */
if (!is_logged_in() && in_array($publicPage, $publicPages, true)) {
    require __DIR__ . '/pages/' . $publicPage . '.php';
    exit;
}

/* ---------- 3. Auth gate ---------- */
if (!is_logged_in()) {
    require __DIR__ . '/pages/login.php';
    exit;
}

/* ---------- 4. Route resolution ---------- */
$navItems = [
    'overview'    => ['grid',     'Overview'],
    'equipment'   => ['box',      'Equipment'],
    'maintenance' => ['tool',     'Maintenance'],
    'warranties'  => ['shield',   'Warranties'],
    'clients'     => ['building', 'Clients'],
    'users'       => ['users',    'User management'],
    'audit'       => ['history',  'Audit logs'],
];
$adminOnly = ['users', 'audit'];

$page = strtolower((string)($_GET['page'] ?? 'overview')) ?: 'overview';
if (!isset($navItems[$page]))                         $page = 'overview';
if (in_array($page, $adminOnly, true) && !is_admin()) $page = 'overview';

/* ---------- 5. Shared data for the layout ---------- */
$currentUser = current_user();
$initials    = user_initials($currentUser['name']);
$pageLabel   = $navItems[$page][1];
$pageHeading = $page === 'overview' ? 'Good day, ' . $currentUser['name'] : $pageLabel;
$pageSubhead = $page === 'overview'
    ? 'Here is what is happening across your equipment network today.'
    : 'Manage and monitor ' . strtolower($pageLabel) . ' across Pacific Surgical locations.';
$today       = date('l, F j, Y');
$flash       = get_flash();
$q           = trim((string)($_GET['q'] ?? ''));

$stats       = get_stats($pdo);
$equipment   = get_all_equipment($pdo, $q);

/* ---------- 6. Render ---------- */
require __DIR__ . '/partials/head.php';
require __DIR__ . '/partials/sidebar.php';
require __DIR__ . '/partials/topbar.php';
?>
<div class="content">

<?php if ($flash): ?>
    <div class="notice <?= h($flash['type']) ?>"><?= h($flash['msg']) ?></div>
<?php endif; ?>
<?php if ($notice !== ''): ?>
    <div class="notice error"><?= h($notice) ?></div>
<?php endif; ?>

<div class="access-banner">
    <div><?= icon(is_admin() ? 'shield' : 'lock') ?></div>
    <p>
        <strong><?= h($currentUser['role']) ?> access</strong>
        <span><?= is_admin()
            ? 'System oversight, equipment registration, user visibility, and audit records are enabled.'
            : 'Equipment and service workflows enabled. Administrative records are restricted.' ?></span>
    </p>
    <span class="session-status"><i></i> Secure session</span>
</div>

<section class="welcome">
    <div>
        <p class="eyebrow">Operations dashboard</p>
        <h1><?= h($pageHeading) ?></h1>
        <p><?= h($pageSubhead) ?></p>
    </div>
    <div class="date-chip"><?= icon('clock') ?> <?= h($today) ?></div>
</section>

<?php require __DIR__ . '/pages/' . $page . '.php'; ?>

<footer>
    <span>PaciTrack v1.3</span>
    <span>Equipment lifecycle management</span>
    <span><i></i> Database connected</span>
</footer>
</div><!-- /content -->

<?php require __DIR__ . '/partials/footer.php'; ?>