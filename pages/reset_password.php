<?php
$rawToken    = (string)($_GET['token'] ?? ($_POST['token'] ?? ''));
$resetErrors = $resetErrors ?? [];
$validReset  = $rawToken !== '' ? find_valid_reset($pdo, $rawToken) : null;
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Set new password | <?= h($config['app']['name']) ?></title>
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
            <p class="story-eyebrow"><span></span> Secure reset</p>
            <h1>Choose a new password.<br>Get back to work.</h1>
            <p>Passwords are stored as one-way hashes and this token will be consumed once used.</p>
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
            <p>Secure reset</p>
            <h2>Set new password</h2>
            <span><?= $validReset ? 'Resetting password for ' . h($validReset['email']) : 'This link may be invalid or expired.' ?></span>
        </div>

        <?php if ($resetErrors): ?>
            <div class="notice error" role="alert">
                <?php foreach ($resetErrors as $err): ?><div><?= h($err) ?></div><?php endforeach; ?>
            </div>
        <?php endif; ?>

        <?php if ($validReset): ?>
            <form method="post" action="index.php?page=reset_password">
                <input type="hidden" name="action" value="reset_password">
                <input type="hidden" name="csrf"   value="<?= h(csrf_token()) ?>">
                <input type="hidden" name="token"  value="<?= h($rawToken) ?>">
                <label>
                    <span>New password</span>
                    <div class="field-wrap">
                        <?= icon('lock') ?>
                        <input type="password" name="password" id="password" required minlength="8" autocomplete="new-password">
                        <button type="button" onclick="togglePassword('password')">Show</button>
                    </div>
                </label>
                <label>
                    <span>Confirm new password</span>
                    <div class="field-wrap">
                        <?= icon('lock') ?>
                        <input type="password" name="confirm_password" id="confirm_password" required minlength="8" autocomplete="new-password">
                        <button type="button" onclick="togglePassword('confirm_password')">Show</button>
                    </div>
                </label>
                <button class="login-submit" type="submit">
                    <span>Update password</span><?= icon('check') ?>
                </button>
            </form>
        <?php else: ?>
            <div class="notice error" role="alert">This reset link is invalid or has expired.</div>
            <a class="login-submit" href="index.php?page=forgot_password" style="text-decoration:none;">
                <span>Request a new link</span><?= icon('arrow') ?>
            </a>
        <?php endif; ?>

        <div class="login-links">
            <a href="index.php">← Back to sign in</a>
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