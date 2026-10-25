<?php
declare(strict_types=1);

function h(mixed $value): string {
    return htmlspecialchars((string)$value, ENT_QUOTES, 'UTF-8');
}

function icon(string $name, int $size = 18): string {
    $paths = [
        'grid'         => '<rect x="3" y="3" width="7" height="7" rx="2"/><rect x="14" y="3" width="7" height="7" rx="2"/><rect x="3" y="14" width="7" height="7" rx="2"/><rect x="14" y="14" width="7" height="7" rx="2"/>',
        'box'          => '<path d="m21 8-9 5-9-5"/><path d="M3.3 7 12 2l8.7 5v10L12 22l-8.7-5Z"/><path d="M12 13v9"/>',
        'tool'         => '<path d="M14.7 6.3a4 4 0 0 0-5-5L12 3.6 8.4 7.2 6.1 4.9a4 4 0 0 0 5 5l-6.8 6.8a2.1 2.1 0 0 0 3 3l6.8-6.8a4 4 0 0 0 5-5l-2.3 2.3-3.6-3.6Z"/>',
        'shield'       => '<path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10"/><path d="m9 12 2 2 4-4"/>',
        'building'     => '<path d="M3 21h18M6 21V5l6-3 6 3v16"/><path d="M9 9h1m4 0h1M9 13h1m4 0h1M9 17h1m4 0h1"/>',
        'users'        => '<path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M22 21v-2a4 4 0 0 0-3-3.9M16 3.1a4 4 0 0 1 0 7.8"/>',
        'history'      => '<path d="M3 12a9 9 0 1 0 3-6.7L3 8"/><path d="M3 3v5h5M12 7v5l3 2"/>',
        'search'       => '<circle cx="11" cy="11" r="7"/><path d="m20 20-4-4"/>',
        'bell'         => '<path d="M18 8a6 6 0 0 0-12 0c0 7-3 7-3 9h18c0-2-3-2-3-9"/><path d="M10 21h4"/>',
        'plus'         => '<path d="M12 5v14M5 12h14"/>',
        'arrow'        => '<path d="m9 18 6-6-6-6"/>',
        'check'        => '<path d="m5 12 4 4L19 6"/>',
        'alert'        => '<path d="M10.3 2.9 1.8 17a2 2 0 0 0 1.7 3h17a2 2 0 0 0 1.7-3L13.7 2.9a2 2 0 0 0-3.4 0Z"/><path d="M12 9v4m0 4h.01"/>',
        'clock'        => '<circle cx="12" cy="12" r="9"/><path d="M12 7v5l3 2"/>',
        'lock'         => '<rect x="4" y="10" width="16" height="11" rx="2"/><path d="M8 10V7a4 4 0 0 1 8 0v3"/>',
        'menu'         => '<path d="M4 7h16M4 12h16M4 17h16"/>',
        'close'        => '<path d="m6 6 12 12M18 6 6 18"/>',
        'edit'         => '<path d="M12 20h9"/><path d="M16.5 3.5a2.1 2.1 0 0 1 3 3L7 19l-4 1 1-4Z"/>',
        'chevron-down' => '<path d="m6 9 6 6 6-6"/>',
    ];
    $body = $paths[$name] ?? $paths['box'];
    return '<svg width="' . $size . '" height="' . $size . '" viewBox="0 0 24 24" fill="none" '
        . 'stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" '
        . 'aria-hidden="true">' . $body . '</svg>';
}

function csrf_token(): string {
    if (empty($_SESSION['csrf'])) {
        $_SESSION['csrf'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf'];
}

function verify_csrf(): void {
    $token = (string)($_POST['csrf'] ?? '');
    if (empty($_SESSION['csrf']) || !hash_equals($_SESSION['csrf'], $token)) {
        http_response_code(419);
        exit('Session expired. Please reload the page and try again.');
    }
}

function redirect(string $url): never {
    header('Location: ' . $url);
    exit;
}

function flash(string $type, string $msg): void {
    $_SESSION['flash'] = ['type' => $type, 'msg' => $msg];
}

function get_flash(): ?array {
    if (empty($_SESSION['flash'])) {
        return null;
    }
    $f = $_SESSION['flash'];
    unset($_SESSION['flash']);
    return $f;
}

function audit(PDO $pdo, ?int $userId, string $action, string $details = ''): void {
    $stmt = $pdo->prepare('INSERT INTO audit_logs (user_id, action, details, ip_address) VALUES (?, ?, ?, ?)');
    $stmt->execute([$userId, $action, $details, $_SERVER['REMOTE_ADDR'] ?? '']);
}

function user_initials(string $name): string {
    $parts = array_slice(preg_split('/\s+/', trim($name)) ?: [], 0, 2);
    return implode('', array_map(fn($p) => strtoupper(substr($p, 0, 1)), $parts));
}

function status_class(string $status): string {
    return strtolower(str_replace(' ', '-', $status));
}

function warranty_class(?string $expiry): string {
    if (!$expiry) return '';
    $today = date('Y-m-d');
    if ($expiry < $today) return 'warranty-expired';
    if ($expiry <= date('Y-m-d', strtotime('+30 days'))) return 'warranty-soon';
    return '';
}