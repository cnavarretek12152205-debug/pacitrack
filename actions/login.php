<?php
declare(strict_types=1);

verify_csrf();

$email    = strtolower(trim((string)($_POST['email']    ?? '')));
$password =              (string)($_POST['password'] ?? '');

$user = find_user_by_email($pdo, $email);
if (!$user || !password_verify($password, $user['password_hash'])) {
    $loginError = 'The email or password is incorrect.';
    return;
}

login_user($pdo, $user, !empty($_POST['remember']));
redirect('index.php');