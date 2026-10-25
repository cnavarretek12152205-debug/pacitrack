<?php
declare(strict_types=1);

verify_csrf();

$rawToken = (string)($_POST['token'] ?? '');
$password = (string)($_POST['password'] ?? '');
$confirm  = (string)($_POST['confirm_password'] ?? '');

$reset = find_valid_reset($pdo, $rawToken);

if (!$reset) {
    $resetErrors = ['This reset link is invalid or has expired. Please request a new one.'];
    return;
}

$errors = validate_password_reset($password, $confirm);
if ($errors) {
    $resetErrors = $errors;
    return;
}

try {
    consume_reset($pdo, (int)$reset['reset_id'], (int)$reset['user_id'], password_hash($password, PASSWORD_DEFAULT));
    audit($pdo, (int)$reset['user_id'], 'Password reset', 'Password changed via reset token');
    flash('success', 'Your password has been updated. Please sign in.');
    redirect('index.php');
} catch (Throwable $e) {
    $resetErrors = ['Could not update your password. Please try again.'];
}