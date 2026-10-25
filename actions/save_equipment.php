<?php
declare(strict_types=1);

verify_csrf();

if (!is_admin()) {
    $notice = 'Only an Administrator can manage equipment.';
    return;
}

$action   = (string)($_POST['action'] ?? '');
$name     = trim((string)($_POST['name']            ?? ''));
$client   = trim((string)($_POST['client']          ?? ''));
$location = trim((string)($_POST['location']        ?? ''));
$type     = trim((string)($_POST['type']            ?? 'General'));
$status   =        (string)($_POST['status']        ?? 'In service');
$due      = trim((string)($_POST['due_date']        ?? ''));
$warranty = trim((string)($_POST['warranty_expiry'] ?? ''));
$warranty = $warranty !== '' ? $warranty : null;

$allowedStatuses = ['In service', 'Maintenance due', 'Under repair', 'In storage', 'Retired'];

if ($name === '' || $client === '' || $location === '' || $due === '' || !in_array($status, $allowedStatuses, true)) {
    $notice = 'Please complete all required fields with valid values.';
    return;
}

try {
    if ($action === 'add_equipment') {
        $equipmentId = strtoupper(trim((string)($_POST['equipment_id'] ?? '')));
        if ($equipmentId === '') {
            $notice = 'Equipment ID is required.';
            return;
        }
        $stmt = $pdo->prepare(
            'INSERT INTO equipment
                (equipment_id, name, client, location, status, due_date, equipment_type, warranty_expiry)
             VALUES (?, ?, ?, ?, ?, ?, ?, ?)'
        );
        $stmt->execute([$equipmentId, $name, $client, $location, $status, $due, $type, $warranty]);

        audit($pdo, current_user()['id'], 'Add equipment', 'Registered ' . $equipmentId . ' - ' . $name);
        flash('success', "Equipment \"{$equipmentId}\" was successfully registered.");
    } else {
        $dbId = (int)($_POST['id'] ?? 0);
        if ($dbId <= 0) {
            $notice = 'Invalid equipment record.';
            return;
        }
        $stmt = $pdo->prepare(
            'UPDATE equipment
             SET name = ?, client = ?, location = ?, status = ?, due_date = ?,
                 equipment_type = ?, warranty_expiry = ?
             WHERE id = ?'
        );
        $stmt->execute([$name, $client, $location, $status, $due, $type, $warranty, $dbId]);

        audit($pdo, current_user()['id'], 'Edit equipment', 'Updated equipment #' . $dbId . ' - ' . $name);
        flash('success', 'Equipment changes saved.');
    }
    redirect('index.php?page=equipment');
} catch (PDOException $e) {
    $notice = $action === 'add_equipment'
        ? 'Could not save equipment. Check that the equipment ID is unique.'
        : 'Could not save equipment changes.';
}