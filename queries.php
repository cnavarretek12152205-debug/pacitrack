<?php
declare(strict_types=1);

function get_stats(PDO $pdo): array {
    return [
        'total'           => (int)$pdo->query('SELECT COUNT(*) FROM equipment')->fetchColumn(),
        'maintenance'     => (int)$pdo->query("SELECT COUNT(*) FROM equipment WHERE status IN ('Maintenance due','Under repair')")->fetchColumn(),
        'warranty_alerts' => (int)$pdo->query("SELECT COUNT(*) FROM equipment WHERE warranty_expiry IS NOT NULL AND warranty_expiry <= DATE_ADD(CURDATE(), INTERVAL 30 DAY)")->fetchColumn(),
    ];
}

function get_status_counts(PDO $pdo): array {
    return $pdo->query('SELECT status, COUNT(*) AS total FROM equipment GROUP BY status')->fetchAll();
}

function get_all_equipment(PDO $pdo, string $q = ''): array {
    $sql    = 'SELECT * FROM equipment';
    $params = [];
    if ($q !== '') {
        $sql .= ' WHERE equipment_id LIKE ? OR name LIKE ? OR client LIKE ? OR location LIKE ?';
        $like = '%' . $q . '%';
        $params = [$like, $like, $like, $like];
    }
    $sql .= ' ORDER BY updated_at DESC, id DESC';
    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    return $stmt->fetchAll();
}

function get_maintenance_items(PDO $pdo, int $limit = 4): array {
    $stmt = $pdo->prepare(
        "SELECT equipment_id, name, client, due_date, status
         FROM equipment
         WHERE status IN ('Maintenance due','Under repair')
         ORDER BY due_date ASC
         LIMIT ?"
    );
    $stmt->bindValue(1, $limit, PDO::PARAM_INT);
    $stmt->execute();
    return $stmt->fetchAll();
}

function get_clients(PDO $pdo): array {
    return $pdo->query(
        'SELECT client, location, COUNT(*) AS assets
         FROM equipment
         GROUP BY client, location
         ORDER BY client'
    )->fetchAll();
}

function get_users(PDO $pdo): array {
    return $pdo->query(
        'SELECT id, full_name, email, role, is_active, created_at
         FROM users ORDER BY id'
    )->fetchAll();
}

function get_audit_logs(PDO $pdo, int $limit = 50): array {
    $stmt = $pdo->prepare(
        'SELECT a.*, u.full_name
         FROM audit_logs a
         LEFT JOIN users u ON u.id = a.user_id
         ORDER BY a.created_at DESC
         LIMIT ?'
    );
    $stmt->bindValue(1, $limit, PDO::PARAM_INT);
    $stmt->execute();
    return $stmt->fetchAll();
}

/* ---------- Auth helpers for register / reset ---------- */

function find_user_by_email_any(PDO $pdo, string $email): ?array {
    $stmt = $pdo->prepare('SELECT id, full_name, email, password_hash, role, is_active FROM users WHERE email = ? LIMIT 1');
    $stmt->execute([$email]);
    $u = $stmt->fetch();
    return $u ?: null;
}

function create_user(PDO $pdo, string $name, string $email, string $hash, string $role = 'Operations User'): int {
    $stmt = $pdo->prepare('INSERT INTO users (full_name, email, password_hash, role, is_active) VALUES (?, ?, ?, ?, 1)');
    $stmt->execute([$name, $email, $hash, $role]);
    return (int)$pdo->lastInsertId();
}

function create_password_reset(PDO $pdo, int $userId, string $rawToken, int $ttlSeconds = 3600): void {
    $stmt = $pdo->prepare('INSERT INTO password_resets (user_id, token_hash, expires_at) VALUES (?, ?, DATE_ADD(NOW(), INTERVAL ? SECOND))');
    $stmt->execute([$userId, hash('sha256', $rawToken), $ttlSeconds]);
}

function find_valid_reset(PDO $pdo, string $rawToken): ?array {
    $stmt = $pdo->prepare(
        'SELECT pr.id AS reset_id, pr.user_id, u.email, u.full_name
         FROM password_resets pr
         JOIN users u ON u.id = pr.user_id
         WHERE pr.token_hash = ? AND pr.used_at IS NULL AND pr.expires_at > NOW()
         LIMIT 1'
    );
    $stmt->execute([hash('sha256', $rawToken)]);
    $r = $stmt->fetch();
    return $r ?: null;
}

function consume_reset(PDO $pdo, int $resetId, int $userId, string $newHash): void {
    $pdo->beginTransaction();
    try {
        $pdo->prepare('UPDATE password_resets SET used_at = NOW() WHERE id = ?')->execute([$resetId]);
        $pdo->prepare('UPDATE users SET password_hash = ? WHERE id = ?')->execute([$newHash, $userId]);
        $pdo->commit();
    } catch (Throwable $e) {
        $pdo->rollBack();
        throw $e;
    }
}

function update_user_password(PDO $pdo, int $userId, string $newHash): void {
    $pdo->prepare('UPDATE users SET password_hash = ? WHERE id = ?')->execute([$newHash, $userId]);
}