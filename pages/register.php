<?php
$registerErrors = $registerErrors ?? [];
$registerOld    = $registerOld    ?? ['full_name' => '', 'email' => ''];
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Create account | <?= h($config['app']['name']) ?></title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
<div class="login-page">
    <section class="login-story">
        <div class="login-brand">
            <div class="brand-mark"><?= icon('shield', 25) ?></div>
            <div><strong><?= h($config['app']['name']) ?></strong><span><?= h($config['app']['tagline']) ?></span></div>
        </div>
        <div class="story-content">
            <p class="story-eyebrow"><span></span> Join the workspace</p>
            <h1>One secure source<br>of equipment truth.</h1>
            <p>Create an Operations account to track equipment, warranties, and service history across Pacific Surgical locations.</p>
            <div class="trust-grid">
                <div><?= icon('lock') ?><span><strong>Hashed</strong>Password storage</span></div>
                <div><?= icon('shield') ?><span><strong>RBAC</strong>Controlled access</span></div>
                <div><?= icon('history') ?><span><strong>Auditable</strong>Complete traceability</span></div>
            </div>
        </div>
        <div class="story-footer"><span>Pacific Surgical Inc.</span><span>Data Privacy Act aligned</span></div>
        <div class="story-orbit orbit-one"></div>
        <div class="story-orbit orbit-two"></div>
    </section>

    <main class="login-panel"><div class="login-card">
        <div class="mobile-login-brand">
            <div class="brand-mark"><?= icon('shield', 22) ?></div>
            <strong><?= h($config['app']['name']) ?></strong>
        </div>
        <div class="login-heading">
            <p>Create your account</p>
            <h2>Get started</h2>
            <span>New accounts are created with Operations access. Administrators can elevate roles later.</span>
        </div>

        <?php if ($registerErrors): ?>
            <div class="notice error" role="alert">
                <?php foreach ($registerErrors as $err): ?>
                    <div><?= h($err) ?></div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>

        <form method="post" action="index.php?page=register" autocomplete="on">
            <input type="hidden" name="action" value="register">
            <input type="hidden" name="csrf"   value="<?= h(csrf_token()) ?>">

            <label>
                <span>Full name</span>
                <div class="field-wrap">
                    <?= icon('users') ?>
                    <input type="text" name="full_name" value="<?= h($registerOld['full_name']) ?>" required maxlength="120" autocomplete="name">
                </div>
            </label>
            <label>
                <span>Work email</span>
                <div class="field-wrap">
                    <?= icon('users') ?>
                    <input type="email" name="email" value="<?= h($registerOld['email']) ?>" required autocomplete="email">
                </div>
            </label>
            <label>
                <span>Password</span>
                <div class="field-wrap">
                    <?= icon('lock') ?>
                    <input type="password" name="password" id="password" required minlength="8" autocomplete="new-password">
                    <button type="button" onclick="togglePassword('password')">Show</button>
                </div>
            </label>
            <label>
                <span>Confirm password</span>
                <div class="field-wrap">
                    <?= icon('lock') ?>
                    <input type="password" name="confirm_password" id="confirm_password" required minlength="8" autocomplete="new-password">
                    <button type="button" onclick="togglePassword('confirm_password')">Show</button>
                </div>
            </label>

            <div class="login-options">
                <span class="hint-text">At least 8 characters, including a letter and a number.</span>
            </div>

            <button class="login-submit" type="submit">
                <span>Create account</span><?= icon('arrow') ?>
            </button>
        </form>

        <div class="login-links">
            <span>Already have an account?</span>
            <a href="index.php">Sign in</a>
        </div>
    </div>
    <footer class="login-footer">
        <span>© <?= date('Y') ?> <?= h($config['app']['name']) ?></span>
        <span>Privacy</span><span>Security</span><span>Support</span>
    </footer>
    </main>
</div>
<script>
function togglePassword(id) {
    const p = document.getElementById(id);
    const b = p.parentNode.querySelector('button');
    p.type = p.type === 'password' ? 'text' : 'password';
    b.textContent = p.type === 'password' ? 'Show' : 'Hide';
}
</script>
</body>
</html>