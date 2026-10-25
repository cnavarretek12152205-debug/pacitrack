<?php
declare(strict_types=1);
session_start();
date_default_timezone_set('Asia/Manila');

/*
 * PaciTrack PHP + MySQL
 * 1) Import database.sql in phpMyAdmin.
 * 2) Update the DB settings below if needed.
 * Demo password for both seeded accounts: PaciTrack2025!
 */
$dbHost = '127.0.0.1;port=3307';
$dbName = 'pacitrack';
$dbUser = 'root';
$dbPass = '';

function h(mixed $value): string {
    return htmlspecialchars((string)$value, ENT_QUOTES, 'UTF-8');
}
function icon(string $name, int $size = 18): string {
    $paths = [
        'grid'=>'<rect x="3" y="3" width="7" height="7" rx="2"/><rect x="14" y="3" width="7" height="7" rx="2"/><rect x="3" y="14" width="7" height="7" rx="2"/><rect x="14" y="14" width="7" height="7" rx="2"/>',
        'box'=>'<path d="m21 8-9 5-9-5"/><path d="M3.3 7 12 2l8.7 5v10L12 22l-8.7-5Z"/><path d="M12 13v9"/>',
        'tool'=>'<path d="M14.7 6.3a4 4 0 0 0-5-5L12 3.6 8.4 7.2 6.1 4.9a4 4 0 0 0 5 5l-6.8 6.8a2.1 2.1 0 0 0 3 3l6.8-6.8a4 4 0 0 0 5-5l-2.3 2.3-3.6-3.6Z"/>',
        'shield'=>'<path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10"/><path d="m9 12 2 2 4-4"/>',
        'building'=>'<path d="M3 21h18M6 21V5l6-3 6 3v16"/><path d="M9 9h1m4 0h1M9 13h1m4 0h1M9 17h1m4 0h1"/>',
        'users'=>'<path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M22 21v-2a4 4 0 0 0-3-3.9M16 3.1a4 4 0 0 1 0 7.8"/>',
        'history'=>'<path d="M3 12a9 9 0 1 0 3-6.7L3 8"/><path d="M3 3v5h5M12 7v5l3 2"/>',
        'search'=>'<circle cx="11" cy="11" r="7"/><path d="m20 20-4-4"/>',
        'bell'=>'<path d="M18 8a6 6 0 0 0-12 0c0 7-3 7-3 9h18c0-2-3-2-3-9"/><path d="M10 21h4"/>',
        'plus'=>'<path d="M12 5v14M5 12h14"/>','arrow'=>'<path d="m9 18 6-6-6-6"/>',
        'check'=>'<path d="m5 12 4 4L19 6"/>',
        'alert'=>'<path d="M10.3 2.9 1.8 17a2 2 0 0 0 1.7 3h17a2 2 0 0 0 1.7-3L13.7 2.9a2 2 0 0 0-3.4 0Z"/><path d="M12 9v4m0 4h.01"/>',
        'clock'=>'<circle cx="12" cy="12" r="9"/><path d="M12 7v5l3 2"/>','lock'=>'<rect x="4" y="10" width="16" height="11" rx="2"/><path d="M8 10V7a4 4 0 0 1 8 0v3"/>',
        'menu'=>'<path d="M4 7h16M4 12h16M4 17h16"/>','close'=>'<path d="m6 6 12 12M18 6 6 18"/>'
    ];
    return '<svg width="'.$size.'" height="'.$size.'" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">'.($paths[$name] ?? $paths['box']).'</svg>';
}
try {
    $pdo = new PDO("mysql:host=$dbHost;dbname=$dbName;charset=utf8mb4", $dbUser, $dbPass, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    ]);
} catch (PDOException $e) {
    http_response_code(500);
    exit('<h2>PaciTrack database connection failed</h2><p>Import <code>database.sql</code> in phpMyAdmin and check the database settings at the top of <code>index.php</code>.</p>');
}
function audit(PDO $pdo, ?int $userId, string $action, string $details = ''): void {
    $stmt = $pdo->prepare('INSERT INTO audit_logs (user_id, action, details, ip_address) VALUES (?, ?, ?, ?)');
    $stmt->execute([$userId, $action, $details, $_SERVER['REMOTE_ADDR'] ?? '']);
}
$navItems = [
    'Overview'=>['grid','overview'], 'Equipment'=>['box','equipment'],
    'Maintenance'=>['tool','maintenance'], 'Warranties'=>['shield','warranties'],
    'Clients'=>['building','clients'], 'User management'=>['users','users'],
    'Audit logs'=>['history','audit'],
];
$loginError = '';
if (isset($_GET['logout'])) {
    if (!empty($_SESSION['user_id'])) audit($pdo, (int)$_SESSION['user_id'], 'Logout', 'User signed out');
    $_SESSION = [];
    session_destroy();
    header('Location: index.php');
    exit;
}
if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['action'] ?? '') === 'login') {
    $email = strtolower(trim((string)($_POST['email'] ?? '')));
    $password = (string)($_POST['password'] ?? '');
    $stmt = $pdo->prepare('SELECT id, full_name, email, password_hash, role FROM users WHERE email = ? AND is_active = 1 LIMIT 1');
    $stmt->execute([$email]);
    $user = $stmt->fetch();
    if ($user && password_verify($password, $user['password_hash'])) {
        session_regenerate_id(true);
        $_SESSION['user_id'] = (int)$user['id'];
        $_SESSION['name'] = $user['full_name'];
        $_SESSION['email'] = $user['email'];
        $_SESSION['role'] = $user['role'];
        audit($pdo, (int)$user['id'], 'Login', 'Successful sign-in');
        header('Location: index.php');
        exit;
    }
    $loginError = 'The email or password is incorrect.';
}
if (empty($_SESSION['user_id'])):
?>
<!doctype html>
<html lang="en">
<head><meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1"><title>Sign in | PaciTrack</title><link rel="stylesheet" href="style.css"></head>
<body>
<div class="login-page">
  <section class="login-story">
    <div class="login-brand"><div class="brand-mark"><?=icon('shield',25)?></div><div><strong>PaciTrack</strong><span>Equipment intelligence</span></div></div>
    <div class="story-content">
      <p class="story-eyebrow"><span></span> Secure lifecycle management</p>
      <h1>Protecting every asset.<br>Supporting every procedure.</h1>
      <p>One secure source of truth for surgical equipment, warranties, maintenance, and regulatory records.</p>
      <div class="trust-grid">
        <div><?=icon('lock')?><span><strong>AES-256</strong>Encrypted records</span></div>
        <div><?=icon('shield')?><span><strong>RBAC</strong>Controlled access</span></div>
        <div><?=icon('history')?><span><strong>Auditable</strong>Complete traceability</span></div>
      </div>
    </div>
    <div class="story-footer"><span>Pacific Surgical Inc.</span><span>Data Privacy Act aligned</span></div>
    <div class="story-orbit orbit-one"></div><div class="story-orbit orbit-two"></div>
  </section>
  <main class="login-panel"><div class="login-card">
    <div class="mobile-login-brand"><div class="brand-mark"><?=icon('shield',22)?></div><strong>PaciTrack</strong></div>
    <div class="login-heading"><p>Authorized access only</p><h2>Welcome back</h2><span>Sign in to continue to your secure workspace.</span></div>
    <?php if ($loginError): ?><div class="notice error" role="alert"><?=h($loginError)?></div><?php endif; ?>
    <div class="login-role-select">
      <button type="button" class="selected" onclick="fillRole('admin@pacificsurgical.ph')"><?=icon('shield')?><span><strong>Administrator</strong><small>System-wide access</small></span></button>
      <button type="button" onclick="fillRole('operations@pacificsurgical.ph')"><?=icon('users')?><span><strong>Operations</strong><small>Assigned workflows</small></span></button>
    </div>
    <form method="post" action="index.php">
      <input type="hidden" name="action" value="login">
      <label><span>Work email</span><div class="field-wrap"><?=icon('users')?><input id="email" type="email" name="email" value="<?=h($_POST['email'] ?? 'admin@pacificsurgical.ph')?>" required autocomplete="username"></div></label>
      <label><span>Password</span><div class="field-wrap"><?=icon('lock')?><input id="password" type="password" name="password" required minlength="8" autocomplete="current-password"><button type="button" onclick="togglePassword()">Show</button></div></label>
      <div class="login-options"><label><input type="checkbox" name="remember" value="1"> <span>Keep me signed in</span></label><span>Secure login</span></div>
      <button class="login-submit" type="submit"><span>Sign in securely</span><?=icon('arrow')?></button>
    </form>
    <div class="login-notice"><?=icon('lock')?><p><strong>Protected session</strong><span>Sessions are managed by PHP. Passwords are stored as one-way hashes.</span></p></div>
    <p class="demo-note">Demo password: PaciTrack2025! — change demo credentials before deployment.</p>
  </div><footer class="login-footer"><span>© <?=date('Y')?> PaciTrack</span><span>Privacy</span><span>Security</span><span>Support</span></footer></main>
</div>
<script>
function fillRole(email){document.getElementById('email').value=email;document.getElementById('password').value='';document.querySelectorAll('.login-role-select button').forEach(b=>b.classList.remove('selected'));event.currentTarget.classList.add('selected');}
function togglePassword(){const p=document.getElementById('password');const b=p.nextElementSibling;p.type=p.type==='password'?'text':'password';b.textContent=p.type==='password'?'Show':'Hide';}
</script>
</body></html>
<?php
exit;
endif;

$role = $_SESSION['role'] ?? 'Operations User';
$isAdmin = $role === 'Administrator';
$active = (string)($_GET['page'] ?? 'Overview');
if (!isset($navItems[$active]) || (!$isAdmin && in_array($active, ['User management','Audit logs'], true))) $active = 'Overview';
$notice = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['action'] ?? '') === 'add_equipment') {
    if (!$isAdmin) {
        $notice = 'Only an Administrator can register equipment in this prototype.';
    } else {
        $id = strtoupper(trim((string)($_POST['equipment_id'] ?? '')));
        $name = trim((string)($_POST['name'] ?? ''));
        $client = trim((string)($_POST['client'] ?? ''));
        $location = trim((string)($_POST['location'] ?? ''));
        $type = trim((string)($_POST['type'] ?? 'General'));
        $status = (string)($_POST['status'] ?? 'In service');
        $due = (string)($_POST['due_date'] ?? '');
        if ($id === '' || $name === '' || $client === '' || $location === '' || $due === '') {
            $notice = 'Please complete all required equipment fields.';
        } else {
            try {
                $stmt = $pdo->prepare('INSERT INTO equipment (equipment_id, name, client, location, status, due_date, equipment_type, warranty_expiry) VALUES (?, ?, ?, ?, ?, ?, ?, ?)');
                $warranty = trim((string)($_POST['warranty_expiry'] ?? ''));
                $stmt->execute([$id,$name,$client,$location,$status,$due,$type,$warranty !== '' ? $warranty : null]);
                audit($pdo, (int)$_SESSION['user_id'], 'Add equipment', 'Registered '.$id.' - '.$name);
                header('Location: index.php?page=Equipment&saved=1'); exit;
            } catch (PDOException $e) { $notice = 'Could not save equipment. Check that the equipment ID is unique.'; }
        }
    }
}
if (isset($_GET['saved'])) $notice = 'Equipment was successfully registered.';
$q = trim((string)($_GET['q'] ?? ''));
$sql = 'SELECT * FROM equipment';
$params = [];
if ($q !== '') {
    $sql .= ' WHERE equipment_id LIKE ? OR name LIKE ? OR client LIKE ? OR location LIKE ?';
    $like = '%'.$q.'%'; $params = [$like,$like,$like,$like];
}
$sql .= ' ORDER BY created_at DESC, id DESC';
$stmt = $pdo->prepare($sql); $stmt->execute($params); $equipment = $stmt->fetchAll();
$totalEquipment = (int)$pdo->query('SELECT COUNT(*) FROM equipment')->fetchColumn();
$activeMaintenance = (int)$pdo->query("SELECT COUNT(*) FROM equipment WHERE status IN ('Maintenance due','Under repair')")->fetchColumn();
$warrantyAlerts = (int)$pdo->query("SELECT COUNT(*) FROM equipment WHERE warranty_expiry IS NOT NULL AND warranty_expiry BETWEEN CURDATE() AND DATE_ADD(CURDATE(), INTERVAL 30 DAY)")->fetchColumn();
$maintenanceRows = $pdo->query("SELECT equipment_id,name,client,due_date,status FROM equipment WHERE status IN ('Maintenance due','Under repair') ORDER BY due_date ASC LIMIT 4")->fetchAll();
$users = $pdo->query('SELECT id,full_name,email,role,is_active,created_at FROM users ORDER BY id')->fetchAll();
$logs = $pdo->query('SELECT a.*,u.full_name FROM audit_logs a LEFT JOIN users u ON u.id=a.user_id ORDER BY a.created_at DESC LIMIT 50')->fetchAll();
$name = $_SESSION['name'] ?? 'User';
$initials = implode('', array_map(fn($part)=>strtoupper(substr($part,0,1)), array_slice(explode(' ', $name),0,2)));
$today = date('l, F j, Y');
?>
<!doctype html>
<html lang="en">
<head><meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1"><title><?=h($active)?> | PaciTrack</title><link rel="stylesheet" href="style.css"></head>
<body>
<div class="app-shell">
<aside class="sidebar" id="sidebar">
  <div class="brand"><div class="brand-mark"><?=icon('shield',23)?></div><div><strong>PaciTrack</strong><span>Equipment intelligence</span></div></div>
  <button class="mobile-close" onclick="toggleMenu(false)" aria-label="Close navigation"><?=icon('close')?></button>
  <nav><p class="nav-label">Workspace</p>
  <?php foreach ($navItems as $label=>$meta): if (!$isAdmin && in_array($label,['User management','Audit logs'],true)) continue; ?>
    <a class="nav-item <?= $active===$label?'active':'' ?>" href="index.php?page=<?=urlencode($label)?>"><?=icon($meta[0])?><span><?=h($label)?></span><?php if($label==='Maintenance' && $activeMaintenance):?><b><?=number_format($activeMaintenance)?></b><?php endif;?></a>
  <?php endforeach; ?></nav>
  <div class="security-card"><div class="security-icon"><?=icon('lock')?></div><strong>Protected access</strong><p>Role-based permissions and audit monitoring are enabled.</p><span><i></i> Session authenticated</span></div>
  <div class="user-card"><div class="avatar"><?=h($initials)?></div><div><strong><?=h($name)?></strong><span><?=h($role)?></span></div></div>
  <a class="sign-out" href="index.php?logout=1"><?=icon('lock',14)?> Sign out securely</a>
</aside>
<button class="scrim" id="scrim" onclick="toggleMenu(false)" aria-label="Close navigation overlay" hidden></button>
<main>
<header class="topbar">
  <button class="menu-button" onclick="toggleMenu(true)" aria-label="Open navigation"><?=icon('menu')?></button>
  <form class="search" method="get" action="index.php"><input type="hidden" name="page" value="<?=h($active)?>"><?=icon('search')?><input name="q" value="<?=h($q)?>" placeholder="Search equipment, clients, records..." aria-label="Search records"><button type="submit" class="search-submit">Search</button></form>
  <button class="icon-button" type="button" aria-label="Notifications"><?=icon('bell')?><i></i></button>
  <div class="profile-menu"><div class="profile-trigger"><span><?=h($initials)?></span><div><strong><?=h($name)?></strong><small><?=h($role)?></small></div></div></div>
  <?php if ($isAdmin): ?><button class="primary-button" onclick="openModal()"><?=icon('plus')?><span>Add equipment</span></button><?php endif; ?>
</header>
<div class="content">
<?php if($notice): ?><div class="notice <?=isset($_GET['saved'])?'success':'error'?>"><?=h($notice)?></div><?php endif; ?>
<div class="access-banner"><div><?=icon($isAdmin?'shield':'lock')?></div><p><strong><?=h($role)?> access</strong><span><?= $isAdmin?'System oversight, equipment registration, user visibility, and audit records are enabled.':'Equipment and service workflows enabled. Administrative records are restricted.' ?></span></p><span class="session-status"><i></i> Secure session</span></div>
<section class="welcome"><div><p class="eyebrow">Operations dashboard</p><h1><?= $active==='Overview'?'Good day, '.h($name):h($active) ?></h1><p><?= $active==='Overview'?'Here is what is happening across your equipment network today.':'Manage and monitor '.h(strtolower($active)).' across Pacific Surgical locations.' ?></p></div><div class="date-chip"><?=icon('clock')?> <?=h($today)?></div></section>
<?php if($active==='Overview' || $active==='Equipment'): ?>
<section class="stats-grid">
  <article class="stat-card"><div class="stat-icon blue"><?=icon('box')?></div><span>Total equipment</span><strong><?=number_format($totalEquipment)?></strong><p>Registered assets</p></article>
  <article class="stat-card"><div class="stat-icon amber"><?=icon('tool')?></div><span>Active maintenance</span><strong><?=number_format($activeMaintenance)?></strong><p class="attention">Due or under repair</p></article>
  <article class="stat-card"><div class="stat-icon rose"><?=icon('alert')?></div><span>Warranty alerts</span><strong><?=number_format($warrantyAlerts)?></strong><p class="attention">Expiring within 30 days</p></article>
  <article class="stat-card"><div class="stat-icon green"><?=icon('shield')?></div><span>Compliance tracking</span><strong>Active</strong><p>Audit trail enabled</p></article>
</section>
<?php endif; ?>
<?php if($active==='Overview'): ?>
<section class="dashboard-grid">
<article class="panel lifecycle-panel"><div class="panel-head"><div><h2>Equipment lifecycle</h2><p>Current status across registered assets</p></div></div>
<div class="chart-wrap"><div class="donut"><div><strong><?=number_format($totalEquipment)?></strong><span>Total assets</span></div></div>
<div class="legend">
<?php
$statusRows = $pdo->query('SELECT status, COUNT(*) AS total FROM equipment GROUP BY status')->fetchAll();
$statusClasses = ['In service'=>'teal','Maintenance due'=>'amber','Under repair'=>'blue','In storage'=>'gray','Retired'=>'gray'];
foreach($statusRows as $sr): ?>
<div><i class="dot <?=h($statusClasses[$sr['status']]??'gray')?>"></i><span><?=h($sr['status'])?></span><strong><?=number_format((int)$sr['total'])?></strong><em><?= $totalEquipment ? number_format(((int)$sr['total']/$totalEquipment)*100,1).'%' : '0%' ?></em></div>
<?php endforeach; ?>
</div></div></article>
<article class="panel alerts-panel"><div class="panel-head"><div><h2>Priority alerts</h2><p>Items that need your attention</p></div><a class="text-button" href="index.php?page=Maintenance">View all <?=icon('arrow',15)?></a></div><div class="alert-list">
<div class="alert-row"><div class="alert-symbol red"><?=icon('alert')?></div><div><strong>Warranty expiring soon</strong><p><?=number_format($warrantyAlerts)?> items within the next 30 days</p></div></div>
<?php if($maintenanceRows): foreach($maintenanceRows as $mr): ?><div class="alert-row"><div class="alert-symbol amber"><?=icon('tool')?></div><div><strong><?=h($mr['name'])?></strong><p><?=h($mr['equipment_id'])?> · <?=h($mr['status'])?> · due <?=h($mr['due_date'])?></p></div></div><?php endforeach; else: ?><div class="alert-row"><div class="alert-symbol blue"><?=icon('check')?></div><div><strong>No active maintenance alerts</strong><p>Equipment service status is up to date.</p></div></div><?php endif; ?>
</div></article></section>
<?php endif; ?>
<?php if(in_array($active,['Overview','Equipment','Maintenance','Warranties','Clients'],true)): ?>
<section class="panel table-panel"><div class="panel-head"><div><h2><?= $active==='Overview'?'Recently updated equipment':h($active) ?></h2><p><?= $q!==''?'Search results for “'.h($q).'”':'Latest records from your equipment network' ?></p></div><?php if($isAdmin): ?><button class="text-button" onclick="openModal()">Register equipment <?=icon('arrow',15)?></button><?php endif; ?></div>
<div class="table-scroll"><table><thead><tr><th>Equipment</th><th>Client & location</th><th>Category</th><th>Status</th><th>Next service</th><th>Warranty expiry</th></tr></thead><tbody>
<?php
$rowsToShow = $equipment;
if($active==='Maintenance') $rowsToShow = array_values(array_filter($equipment,fn($x)=>in_array($x['status'],['Maintenance due','Under repair'],true)));
if($active==='Warranties') $rowsToShow = array_values(array_filter($equipment,fn($x)=>!empty($x['warranty_expiry'])));
if($active==='Clients') {
  $clientStmt=$pdo->query('SELECT client, location, COUNT(*) AS assets FROM equipment GROUP BY client, location ORDER BY client');
  $clientRows=$clientStmt->fetchAll();
}
if($active==='Clients'): foreach($clientRows as $c): ?>
<tr><td><div class="equipment-cell"><span><?=icon('building')?></span><div><strong><?=h($c['client'])?></strong><small><?=number_format((int)$c['assets'])?> registered asset(s)</small></div></div></td><td><?=h($c['client'])?><small><?=h($c['location'])?></small></td><td>Client account</td><td><span class="status"><i></i>Active</span></td><td>—</td><td>—</td></tr>
<?php endforeach; else: foreach($rowsToShow as $item): ?>
<tr><td><div class="equipment-cell"><span><?=icon('box')?></span><div><strong><?=h($item['name'])?></strong><small><?=h($item['equipment_id'])?></small></div></div></td><td><strong><?=h($item['client'])?></strong><small><?=h($item['location'])?></small></td><td><?=h($item['equipment_type'])?></td><td><span class="status <?=h(strtolower(str_replace(' ','-',$item['status'])))?>"><i></i><?=h($item['status'])?></span></td><td><?=h($item['due_date'])?></td><td><?=h($item['warranty_expiry'] ?: '—')?></td></tr>
<?php endforeach; endif; ?>
</tbody></table><?php if(!$rowsToShow && $active!=='Clients'): ?><div class="empty-state">No equipment records match this view.</div><?php endif; ?></div></section>
<?php elseif($active==='User management' && $isAdmin): ?>
<section class="panel table-panel"><div class="panel-head"><div><h2>User management</h2><p>Accounts and assigned roles</p></div></div><div class="table-scroll"><table><thead><tr><th>Name</th><th>Email</th><th>Role</th><th>Status</th><th>Created</th></tr></thead><tbody><?php foreach($users as $u): ?><tr><td><strong><?=h($u['full_name'])?></strong></td><td><?=h($u['email'])?></td><td><?=h($u['role'])?></td><td><?= $u['is_active']?'Active':'Disabled' ?></td><td><?=h($u['created_at'])?></td></tr><?php endforeach; ?></tbody></table></div></section>
<?php elseif($active==='Audit logs' && $isAdmin): ?>
<section class="panel table-panel"><div class="panel-head"><div><h2>Audit logs</h2><p>Recent security and equipment activity</p></div></div><div class="table-scroll"><table><thead><tr><th>Timestamp</th><th>User</th><th>Action</th><th>Details</th><th>IP address</th></tr></thead><tbody><?php foreach($logs as $log): ?><tr><td><?=h($log['created_at'])?></td><td><?=h($log['full_name']??'System')?></td><td><?=h($log['action'])?></td><td><?=h($log['details'])?></td><td><?=h($log['ip_address'])?></td></tr><?php endforeach; ?></tbody></table></div></section>
<?php endif; ?>
<footer><span>PaciTrack v1.0</span><span>Equipment lifecycle management</span><span><i></i> Database connected</span></footer>
</div>
</main>
</div>
<?php if($isAdmin): ?>
<div class="modal-backdrop" id="equipmentModal" hidden><div class="modal" role="dialog" aria-modal="true" aria-labelledby="modal-title"><div class="modal-icon"><?=icon('box')?></div><h2 id="modal-title">Add new equipment</h2><p>Register a surgical equipment asset in the inventory.</p>
<form method="post" action="index.php?page=Equipment"><input type="hidden" name="action" value="add_equipment">
<div class="form-grid">
<label>Equipment ID *<input name="equipment_id" placeholder="EQ-2049" required></label>
<label>Equipment name *<input name="name" placeholder="Surgical instrument set" required></label>
<label>Client / hospital *<input name="client" placeholder="Hospital name" required></label>
<label>Location *<input name="location" placeholder="Quezon City" required></label>
<label>Category<select name="type"><option>Capital / OR</option><option>Spine</option><option>Joints</option><option>General</option></select></label>
<label>Status<select name="status"><option>In service</option><option>Maintenance due</option><option>Under repair</option><option>In storage</option><option>Retired</option></select></label>
<label>Next service date *<input type="date" name="due_date" required></label>
<label>Warranty expiry<input type="date" name="warranty_expiry"></label>
</div><div class="modal-security"><?=icon('lock')?><span>Registration activity will be recorded in the audit log.</span></div><div class="modal-actions"><button type="button" class="cancel-button" onclick="closeModal()">Cancel</button><button type="submit" class="primary-button">Save equipment</button></div>
</form></div></div>
<?php endif; ?>
<script>
function toggleMenu(open){const s=document.getElementById('sidebar'), scrim=document.getElementById('scrim');s.classList.toggle('open',open);scrim.hidden=!open;}
function openModal(){const m=document.getElementById('equipmentModal');if(m)m.hidden=false;}
function closeModal(){const m=document.getElementById('equipmentModal');if(m)m.hidden=true;}
document.addEventListener('keydown',e=>{if(e.key==='Escape'){closeModal();toggleMenu(false);}});
document.getElementById('equipmentModal')?.addEventListener('click',e=>{if(e.target.id==='equipmentModal')closeModal();});
</script>
</body></html>