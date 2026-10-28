<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Sign in | <?= h($config['app']['name']) ?></title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
<div class="login-page">
    <section class="login-story">
        <div class="login-brand">
            <!-- Pinalitan ang shield icon ng Logo -->
            <div style="background: white; padding: 4px; border-radius: 8px; display: flex; align-items: center; justify-content: center;">
                <img src="assets/img/logo.jpg" alt="Pacific Surgical Logo" style="height: 35px; width: auto; object-fit: contain;">
            </div>
            <div><strong><?= h($config['app']['name']) ?></strong><span><?= h($config['app']['tagline']) ?></span></div>
        </div>
        <div class="story-content">
            <p class="story-eyebrow"><span></span> Internal Asset Management</p>
            <h1>Streamlining surgical equipment.<br>Empowering healthcare.</h1>
            <p>One secure source of truth for surgical equipment, warranties, maintenance, and regulatory records across Pacific Surgical Inc.</p>
            <div class="trust-grid">
                <div><?= icon('lock') ?><span><strong>Company</strong>Authorized Access</span></div>
                <div><?= icon('shield') ?><span><strong>Secure</strong>Data Protection</span></div>
                <div><?= icon('history') ?><span><strong>Compliant</strong>Audit Ready</span></div>
            </div>
        </div>
        <div class="story-footer"><span>Pacific Surgical Inc.</span><span>Data Privacy Act aligned</span></div>
        <div class="story-orbit orbit-one"></div>
        <div class="story-orbit orbit-two"></div>
    </section>

    <main class="login-panel"><div class="login-card">
        <div class="mobile-login-brand">
            <!-- Logo din sa mobile view -->
            <div style="background: white; padding: 2px 6px; border-radius: 6px; display: inline-flex; align-items: center;">
                <img src="assets/img/logo.jpg" alt="Logo" style="height: 24px;">
            </div>
            <strong><?= h($config['app']['name']) ?></strong>
        </div>
        <div class="login-heading">
            <p>Pacific Surgical Inc.</p>
            <h2>PaciTrack Portal</h2>
            <span>Sign in to continue to your secure workspace.</span>
        </div>
        <?php if (!empty($loginError)): ?>
            <div class="notice error" role="alert"><?= h($loginError) ?></div>
        <?php endif; ?>

        <div class="login-role-select">
            <button type="button" class="selected" onclick="fillRole(this, 'admin@pacificsurgical.ph')">
                <?= icon('shield') ?><span><strong>Administrator</strong><small>System-wide access</small></span>
            </button>
            <button type="button" onclick="fillRole(this, 'operations@pacificsurgical.ph')">
                <?= icon('users') ?><span><strong>Operations</strong><small>Assigned workflows</small></span>
            </button>
        </div>

        <form method="post" action="index.php">
            <input type="hidden" name="action" value="login">
            <input type="hidden" name="csrf"   value="<?= h(csrf_token()) ?>">
            <label>
                <span>Work email</span>
                <div class="field-wrap">
                    <?= icon('users') ?>
                    <input id="email" type="email" name="email"
                           value="<?= h($_POST['email'] ?? 'admin@pacificsurgical.ph') ?>"
                           required autocomplete="username">
                </div>
            </label>
            <label>
                <span>Password</span>
                <div class="field-wrap">
                    <?= icon('lock') ?>
                    <input id="password" type="password" name="password" required minlength="8" autocomplete="current-password">
                    <button type="button" onclick="togglePassword()">Show</button>
                </div>
            </label>
            <div class="login-options">
                <label><input type="checkbox" name="remember" value="1"> <span>Keep me signed in</span></label>
                <a class="inline-link" href="index.php?page=forgot_password">Forgot password?</a>
            </div>
            <button class="login-submit" type="submit">
                <span>Sign in securely</span><?= icon('arrow') ?>
            </button>
        </form>

        <div class="login-links">
            <span>New to PaciTrack?</span>
            <a href="index.php?page=register">Create an account</a>
        </div>

        <div class="login-notice">
            <?= icon('lock') ?>
            <p><strong>Protected session</strong><span>Sessions are managed by PHP. Passwords are stored as one-way hashes.</span></p>
        </div>
        <p class="demo-note">Demo password: <?= h($config['app']['demo_password_hint']) ?> — change demo credentials before deployment.</p>
    </div>
    <footer class="login-footer">
        <span>© <?= date('Y') ?> <?= h($config['app']['tagline']) ?></span>
        <span>Internal Use Only</span><span>Privacy</span><span>Support</span>
    </footer>
    </main>
</div>
<script>
function fillRole(btn, email) {
    document.getElementById('email').value = email;
    document.getElementById('password').value = '';
    document.querySelectorAll('.login-role-select button').forEach(b => b.classList.remove('selected'));
    btn.classList.add('selected');
    document.getElementById('password').focus();
}
function togglePassword() {
    const p = document.getElementById('password');
    const b = p.parentNode.querySelector('button');
    p.type = p.type === 'password' ? 'text' : 'password';
    b.textContent = p.type === 'password' ? 'Show' : 'Hide';
}
</script>
</body>
</html>