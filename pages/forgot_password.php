<?php
$resetNotice = $resetNotice ?? '';
$resetToken  = $resetToken  ?? null;
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Reset password | <?= h($config['app']['name']) ?></title>
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
            <p class="story-eyebrow"><span></span> Account recovery</p>
            <h1>Forgot your password?<br>We can help.</h1>
            <p>Enter the email on your PaciTrack account and we'll create a one-time reset token valid for one hour.</p>
            <div class="trust-grid">
                <div><?= icon('lock') ?><span><strong>Hashed</strong>One-time tokens</span></div>
                <div><?= icon('shield') ?><span><strong>RBAC</strong>Controlled access</span></div>
                <div><?= icon('history') ?><span><strong>Auditable</strong>Every reset logged</span></div>
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
            <p>Account recovery</p>
            <h2>Forgot password</h2>
            <span>We'll create a one-time reset token for your account.</span>
        </div>

        <?php if ($resetNotice): ?>
            <div class="notice success" role="status"><?= h($resetNotice) ?></div>
        <?php endif; ?>

        <?php if ($resetToken): ?>
            <div class="token-box">
                <p><strong>Demo mode — no email server configured.</strong></p>
                <p>Use this one-time link within 1 hour:</p>
                <a href="index.php?page=reset_password&amp;token=<?= h($resetToken) ?>">
                    Continue to reset password →
                </a>
                <code><?= h($resetToken) ?></code>
            </div>
        <?php else: ?>
            <form method="post" action="index.php?page=forgot_password">
                <input type="hidden" name="action" value="forgot_password">
                <input type="hidden" name="csrf"   value="<?= h(csrf_token()) ?>">
                <label>
                    <span>Work email</span>
                    <div class="field-wrap">
                        <?= icon('users') ?>
                        <input type="email" name="email" required autocomplete="email">
                    </div>
                </label>
                <button class="login-submit" type="submit">
                    <span>Create reset token</span><?= icon('arrow') ?>
                </button>
            </form>
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
</body>
</html>