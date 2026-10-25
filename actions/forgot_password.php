<?php
declare(strict_types=1);

verify_csrf();

$email    = strtolower(trim((string)($_POST['email'] ?? '')));
$msg      = 'If an account exists for that email, reset instructions have been created.';
$rawToken = null;

if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $user = find_user_by_email_any($pdo, $email);
    if ($user && (int)$user['is_active'] === 1) {
        $rawToken = bin2hex(random_bytes(32));
        create_password_reset($pdo, (int)$user['id'], $rawToken, 3600);
        audit($pdo, (int)$user['id'], 'Password reset requested', 'Token issued (1 hour TTL)');
    }
}

// No mail server in this stack — surface the token so you can test locally.
// In production: send a link index.php?page=reset_password&token=... via email.
$resetNotice = $msg;
$resetToken  = $rawToken; // null if email not found (never reveal which)