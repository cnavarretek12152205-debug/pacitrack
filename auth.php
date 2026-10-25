<?php
declare(strict_types=1);

function is_logged_in(): bool {
    return !empty($_SESSION['user_id']);
}

function current_user(): ?array {
    if (!is_logged_in()) return null;
    return [
        'id'    => (int)$_SESSION['user_id'],
        'name'  => (string)($_SESSION['name']  ?? 'User'),
        'email' => (string)($_SESSION['email'] ?? ''),
        'role'  => (string)($_SESSION['role']  ?? 'Operations User'),
    ];
}

function is_admin(): bool {
    return (current_user()['role'] ?? '') === 'Administrator';
}

function find_user_by_email(PDO $pdo, string $email): ?array {
    $stmt = $pdo->prepare('SELECT id, full_name, email, password_hash, role FROM users WHERE email = ? AND is_active = 1 LIMIT 1');
    $stmt->execute([$email]);
    $user = $stmt->fetch();
    return $user ?: null;
}

function login_user(PDO $pdo, array $user, bool $remember = false): void {
    session_regenerate_id(true);
    $_SESSION['user_id'] = (int)$user['id'];
    $_SESSION['name']    = $user['full_name'];
    $_SESSION['email']   = $user['email'];
    $_SESSION['role']    = $user['role'];
    $_SESSION['csrf']    = bin2hex(random_bytes(32));

    if ($remember) {
        $params = session_get_cookie_params();
        setcookie(session_name(), session_id(), [
            'expires'  => time() + 60 * 60 * 24 * 30,
            'path'     => $params['path'],
            'domain'   => $params['domain'],
            'secure'   => $params['secure'],
            'httponly' => true,
            'samesite' => 'Lax',
        ]);
    }

    audit($pdo, (int)$user['id'], 'Login', 'Successful sign-in');
}

function logout_user(PDO $pdo): void {
    if (is_logged_in()) {
        audit($pdo, (int)$_SESSION['user_id'], 'Logout', 'User signed out');
    }
    $_SESSION = [];
    if (ini_get('session.use_cookies')) {
        $p = session_get_cookie_params();
        setcookie(session_name(), '', time() - 42000, $p['path'], $p['domain'], $p['secure'], $p['httponly']);
    }
    session_destroy();
}

/* ---------- Registration / reset validators ---------- */

function validate_registration(string $name, string $email, string $password, string $confirm): array {
    $errors = [];
    if (trim($name) === '' || mb_strlen(trim($name)) > 120) $errors[] = 'Please enter your full name.';
    if (!filter_var($email, FILTER_VALIDATE_EMAIL))          $errors[] = 'Please enter a valid email address.';
    if (strlen($password) < 8)                               $errors[] = 'Password must be at least 8 characters.';
    if (!preg_match('/[A-Za-z]/', $password) || !preg_match('/\d/', $password)) {
        $errors[] = 'Password must contain at least one letter and one number.';
    }
    if ($password !== $confirm)                              $errors[] = 'Passwords do not match.';
    return $errors;
}

function validate_password_reset(string $password, string $confirm): array {
    $errors = [];
    if (strlen($password) < 8)                               $errors[] = 'Password must be at least 8 characters.';
    if (!preg_match('/[A-Za-z]/', $password) || !preg_match('/\d/', $password)) {
        $errors[] = 'Password must contain at least one letter and one number.';
    }
    if ($password !== $confirm)                              $errors[] = 'Passwords do not match.';
    return $errors;
}