<?php
declare(strict_types=1);

verify_csrf();

$name     = trim((string)($_POST['full_name'] ?? ''));
$email    = strtolower(trim((string)($_POST['email'] ?? '')));
$password = (string)($_POST['password'] ?? '');
$confirm  = (string)($_POST['confirm_password'] ?? '');

$errors = validate_registration($name, $email, $password, $confirm);

if (!$errors && find_user_by_email_any($pdo, $email)) {
    $errors[] = 'An account with this email already exists. Try signing in instead.';
}

if ($errors) {
    $registerErrors = $errors;
    $registerOld    = ['full_name' => $name, 'email' => $email];
    return;
}

try {
    $userId = create_user($pdo, $name, $email, password_hash($password, PASSWORD_DEFAULT), 'Operations User');
    audit($pdo, $userId, 'Register', 'Self-registered account ' . $email);
    $user = ['id' => $userId, 'full_name' => $name, 'email' => $email, 'role' => 'Operations User'];
    login_user($pdo, $user, false);
    flash('success', 'Welcome to PaciTrack! Your account is ready.');
    redirect('index.php');
} catch (PDOException $e) {
    $registerErrors = ['Could not create your account. Please try again.'];
    $registerOld    = ['full_name' => $name, 'email' => $email];
}