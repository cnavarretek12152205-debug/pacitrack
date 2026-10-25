</main>
</div><!-- /app-shell -->

<?php if (is_admin()):
    $reopen   = $notice !== '';
    $posted   = $reopen ? $_POST : [];
    $editMode = ($posted['action'] ?? '') === 'edit_equipment';
?>
<div class="modal-backdrop" id="equipmentModal" <?= $reopen ? '' : 'hidden' ?>>
    <div class="modal" role="dialog" aria-modal="true" aria-labelledby="modal-title">
        <div class="modal-icon"><?= icon('box') ?></div>
        <h2 id="modal-title"><?= $editMode ? 'Edit equipment' : 'Add new equipment' ?></h2>
        <p id="modal-desc">
            <?= $editMode
                ? 'Update the details for ' . h($posted['equipment_id'] ?? '') . '.'
                : 'Register a surgical equipment asset in the inventory.' ?>
        </p>
        <form method="post" action="index.php?page=equipment" id="equipmentForm">
            <input type="hidden" name="action" id="formAction" value="<?= $editMode ? 'edit_equipment' : 'add_equipment' ?>">
            <input type="hidden" name="id"     id="equipmentDbId" value="<?= h($posted['id'] ?? '') ?>">
            <input type="hidden" name="csrf"   value="<?= h(csrf_token()) ?>">
            <div class="form-grid">
                <label>Equipment ID *
                    <input name="equipment_id" id="f_equipment_id"
                           value="<?= h($posted['equipment_id'] ?? '') ?>"
                           placeholder="EQ-2049" <?= $editMode ? 'readonly' : 'required' ?>>
                </label>
                <label>Equipment name *
                    <input name="name" id="f_name" value="<?= h($posted['name'] ?? '') ?>"
                           placeholder="Surgical instrument set" required>
                </label>
                <label>Client / hospital *
                    <input name="client" id="f_client" value="<?= h($posted['client'] ?? '') ?>"
                           placeholder="Hospital name" required>
                </label>
                <label>Location *
                    <input name="location" id="f_location" value="<?= h($posted['location'] ?? '') ?>"
                           placeholder="Quezon City" required>
                </label>
                <label>Category
                    <select name="type" id="f_type">
                        <?php foreach (['Capital / OR','Spine','Joints','General'] as $opt): ?>
                            <option <?= ($posted['type'] ?? 'General') === $opt ? 'selected' : '' ?>><?= h($opt) ?></option>
                        <?php endforeach; ?>
                    </select>
                </label>
                <label>Status
                    <select name="status" id="f_status">
                        <?php foreach (['In service','Maintenance due','Under repair','In storage','Retired'] as $opt): ?>
                            <option <?= ($posted['status'] ?? 'In service') === $opt ? 'selected' : '' ?>><?= h($opt) ?></option>
                        <?php endforeach; ?>
                    </select>
                </label>
                <label>Next service date *
                    <input type="date" name="due_date" id="f_due_date" value="<?= h($posted['due_date'] ?? '') ?>" required>
                </label>
                <label>Warranty expiry
                    <input type="date" name="warranty_expiry" id="f_warranty" value="<?= h($posted['warranty_expiry'] ?? '') ?>">
                </label>
            </div>
            <div class="modal-security"><?= icon('lock') ?><span>This activity will be recorded in the audit log.</span></div>
            <div class="modal-actions">
                <button type="button" class="cancel-button" onclick="closeModal()">Cancel</button>
                <button type="submit" class="primary-button">Save equipment</button>
            </div>
        </form>
    </div>
</div>
<?php endif; ?>

<script>
function toggleMenu(open) {
    const s = document.getElementById('sidebar'), scrim = document.getElementById('scrim');
    s.classList.toggle('open', open);
    scrim.hidden = !open;
}
function toggleProfile() {
    const d = document.getElementById('profileDropdown');
    const s = document.getElementById('profileScrim');
    d.hidden = !d.hidden;
    s.hidden = d.hidden;
    document.querySelector('.profile-trigger').setAttribute('aria-expanded', String(!d.hidden));
}
function openModal()  { const m = document.getElementById('equipmentModal'); if (m) m.hidden = false; }
function closeModal() { const m = document.getElementById('equipmentModal'); if (m) m.hidden = true; }

function openAdd() {
    const f = document.getElementById('equipmentForm'); if (!f) return;
    f.reset();
    document.getElementById('formAction').value   = 'add_equipment';
    document.getElementById('equipmentDbId').value = '';
    document.getElementById('modal-title').textContent = 'Add new equipment';
    document.getElementById('modal-desc').textContent  = 'Register a surgical equipment asset in the inventory.';
    document.getElementById('f_equipment_id').readOnly = false;
    openModal();
}

function openEdit(btn) {
    const d = btn.dataset;
    document.getElementById('formAction').value    = 'edit_equipment';
    document.getElementById('equipmentDbId').value = d.id;
    document.getElementById('f_equipment_id').value = d.equipmentId;
    document.getElementById('f_equipment_id').readOnly = true;
    document.getElementById('f_name').value        = d.name;
    document.getElementById('f_client').value      = d.client;
    document.getElementById('f_location').value    = d.location;
    document.getElementById('f_type').value        = d.equipmentType;
    document.getElementById('f_status').value      = d.status;
    document.getElementById('f_due_date').value    = d.dueDate;
    document.getElementById('f_warranty').value    = d.warrantyExpiry || '';
    document.getElementById('modal-title').textContent = 'Edit equipment';
    document.getElementById('modal-desc').textContent  = 'Update the details for ' + d.equipmentId + '.';
    openModal();
}

document.addEventListener('keydown', e => {
    if (e.key === 'Escape') {
        closeModal();
        toggleMenu(false);
        const pd = document.getElementById('profileDropdown');
        if (pd && !pd.hidden) toggleProfile();
    }
});
document.getElementById('equipmentModal')?.addEventListener('click', e => {
    if (e.target.id === 'equipmentModal') closeModal();
});
</script>
</body>
</html>