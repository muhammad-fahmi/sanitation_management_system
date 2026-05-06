<form id="form_add_action" action="<?= site_url('admin/manage/task/add/action') ?>" method="post">
    <?= csrf_field() ?>
    <style>
        .action-section {
            margin-bottom: 20px;
        }

        .section-title {
            font-weight: bold;
            margin-bottom: 10px;
            color: #333;
            font-size: 14px;
        }

        .todo-item {
            display: flex;
            align-items: center;
            margin-bottom: 10px;
            padding: 8px;
            border-radius: 4px;
            transition: background-color 0.2s ease;
        }

        .todo-item:hover {
            background-color: #f5f5f5;
        }

        .todo-item.disabled {
            opacity: 0.5;
            pointer-events: none;
        }

        .todo-checkbox {
            margin-right: 10px;
            transform: scale(1.2);
            cursor: pointer;
        }

        .todo-checkbox:disabled {
            cursor: not-allowed;
        }

        .todo-label {
            font-size: 16px;
            cursor: pointer;
            transition: all 0.3s ease;
            flex: 1;
            user-select: none;
        }

        .todo-checkbox:checked+.todo-label {
            text-decoration: line-through;
            color: #28a745;
            font-weight: bold;
        }

        .action-info {
            font-size: 12px;
            color: #999;
            margin-top: 5px;
            padding-left: 32px;
        }
    </style>

    <?php
    // Use default clean action passed from controller (item_id=999, action_name='sudah bersih')
    $cleanActionId = isset($clean_action_id) ? $clean_action_id : null;
    ?>

    <!-- Pilihan dari Administrator -->
    <div class="action-section">
        <div class="section-title">📋 Pilihan dari Administrator</div>
        <?php foreach ($actions as $action): ?>
            <div class="todo-item" id="action_item_<?= $action['action_id'] ?>">
                <input type="checkbox" id="<?= $action['action_id'] ?>" class="todo-checkbox admin-action"
                    data-action-id="<?= $action['action_id'] ?>" />
                <label for="<?= $action['action_id'] ?>" class="todo-label"><?= $action['action_name'] ?></label>
            </div>
        <?php endforeach; ?>
        <div class="action-info">Pilih salah satu atau lebih aksi dari administrator</div>
    </div>

    <hr class="my-3">

    <!-- Pilihan Sudah Bersih (berdasarkan m_actions) -->
    <div class="action-section">
        <div class="section-title">✓ Sudah Bersih</div>
        <?php if ($cleanActionId !== null): ?>
            <div class="todo-item" id="item_sudah_bersih">
                <input type="checkbox" id="sudah_bersih" class="todo-checkbox" data-action-id="<?= $cleanActionId ?>" />
                <label for="sudah_bersih" class="todo-label">Sudah bersih (aksi default)</label>
            </div>
            <div class="action-info">Gunakan jika item sudah dibersihkan tanpa aksi spesifik lain dari administrator</div>
        <?php else: ?>
            <div class="action-info">Aksi default "Sudah Bersih" belum dikonfigurasi di database.</div>
        <?php endif; ?>
    </div>

    <div class="d-flex justify-content-end gap-2 mt-4">
        <button type="button" id="btn_save_action" class="btn btn-primary d-flex align-items-center">
            <iconify-icon icon="fa7-solid:save" width="20" height="20"></iconify-icon>
            Simpan
        </button>
    </div>
</form>

<script>
    var obj = JSON.parse(localStorage.getItem('action_data')) || {};
    var modalLocationId = <?= $location_id ?>;
    var modalItemId = <?= $item_id ?>;
    var cleanActionId = <?= $cleanActionId !== null ? $cleanActionId : 'null' ?>;

    // Initialize function to update UI state
    function updateActionStates() {
        var selectedActions = (obj[modalLocationId] && obj[modalLocationId][modalItemId]) ? obj[modalLocationId][modalItemId] : {};
        var hasAdminAction = false;
        var hasSudahBersih = (cleanActionId !== null) ? !!selectedActions[cleanActionId] : false;

        // Check if any admin action is selected
        <?php foreach ($actions as $action): ?>
            if (selectedActions[<?= $action['action_id'] ?>]) {
                hasAdminAction = true;
            }
        <?php endforeach; ?>

        // Update admin action checkboxes
        <?php foreach ($actions as $action): ?>
            var checkbox = $('#<?= $action['action_id'] ?>');
            var item = $('#action_item_<?= $action['action_id'] ?>');

            if (hasSudahBersih) {
                checkbox.prop('disabled', true);
                checkbox.prop('checked', false);
                item.addClass('disabled');
            } else {
                checkbox.prop('disabled', false);
                item.removeClass('disabled');
            }

            if (selectedActions[<?= $action['action_id'] ?>]) {
                checkbox.prop('checked', true);
            }
        <?php endforeach; ?>

        // Update sudah_bersih checkbox
        var sudahBersihCheckbox = $('#sudah_bersih');
        var sudahBersihItem = $('#item_sudah_bersih');

        if (hasAdminAction) {
            sudahBersihCheckbox.prop('disabled', true);
            sudahBersihCheckbox.prop('checked', false);
            sudahBersihItem.addClass('disabled');
        } else {
            sudahBersihCheckbox.prop('disabled', false);
            sudahBersihItem.removeClass('disabled');
        }

        if (hasSudahBersih) {
            sudahBersihCheckbox.prop('checked', true);
        }

        // If previously stored legacy key exists, remove it
        if (selectedActions['sudah_bersih']) {
            delete selectedActions['sudah_bersih'];
            obj[modalLocationId][modalItemId] = selectedActions;
            localStorage.setItem('action_data', JSON.stringify(obj));
        }
    }

    // Load previously selected actions for this item
    updateActionStates();

    // Handle admin action checkboxes
    $('.admin-action').on('change', function () {
        var actionId = $(this).data('action-id');
        obj = JSON.parse(localStorage.getItem('action_data')) || {};

        if ($(this).is(':checked')) {
            if (!obj[modalLocationId]) obj[modalLocationId] = {};
            if (!obj[modalLocationId][modalItemId]) obj[modalLocationId][modalItemId] = {};

            // Remove clean action if any admin action is checked
            if (cleanActionId !== null && obj[modalLocationId][modalItemId][cleanActionId]) {
                delete obj[modalLocationId][modalItemId][cleanActionId];
            }

            obj[modalLocationId][modalItemId][actionId] = true;
        } else {
            if (obj[modalLocationId] && obj[modalLocationId][modalItemId]) {
                delete obj[modalLocationId][modalItemId][actionId];
                if (Object.keys(obj[modalLocationId][modalItemId]).length === 0) {
                    delete obj[modalLocationId][modalItemId];
                }
                if (Object.keys(obj[modalLocationId]).length === 0) {
                    delete obj[modalLocationId];
                }
            }
        }

        localStorage.setItem('action_data', JSON.stringify(obj));
        updateActionStates();
    });

    // Handle clean action checkbox
    $('#sudah_bersih').on('change', function () {
        obj = JSON.parse(localStorage.getItem('action_data')) || {};

        if (cleanActionId === null) {
            // No configured clean action in DB; prevent selection
            $(this).prop('checked', false);
            toastr.info('Aksi "Sudah Dibersihkan" belum dikonfigurasi untuk item ini.', 'Info');
            return;
        }

        if ($(this).is(':checked')) {
            if (!obj[modalLocationId]) obj[modalLocationId] = {};
            if (!obj[modalLocationId][modalItemId]) obj[modalLocationId][modalItemId] = {};

            // Remove all admin actions and set sudah_bersih
            <?php foreach ($actions as $action): ?>
                if (obj[modalLocationId][modalItemId][<?= (int) $action['action_id'] ?>]) {
                    delete obj[modalLocationId][modalItemId][<?= (int) $action['action_id'] ?>];
                }
            <?php endforeach; ?>

            obj[modalLocationId][modalItemId][cleanActionId] = true;
        } else {
            if (obj[modalLocationId] && obj[modalLocationId][modalItemId]) {
                if (cleanActionId !== null) {
                    delete obj[modalLocationId][modalItemId][cleanActionId];
                }
                if (Object.keys(obj[modalLocationId][modalItemId]).length === 0) {
                    delete obj[modalLocationId][modalItemId];
                }
                if (Object.keys(obj[modalLocationId]).length === 0) {
                    delete obj[modalLocationId];
                }
            }
        }

        localStorage.setItem('action_data', JSON.stringify(obj));
        updateActionStates();
    });

    $('#btn_save_action').on('click', function () {
        var modalEl = document.getElementById('bs_modal_md');
        var saveBtn = this;

        // Prevent accessibility warning when Bootstrap applies aria-hidden during hide
        if (saveBtn === document.activeElement) {
            saveBtn.blur();
        }

        var syncParentState = function () {
            if (typeof updateItemStatus === 'function') {
                updateItemStatus();
            }
        };

        if (!modalEl || typeof bootstrap === 'undefined' || !bootstrap.Modal) {
            syncParentState();
            return;
        }

        $(modalEl).one('hidden.bs.modal', function () {
            syncParentState();
        });

        var modalInstance = bootstrap.Modal.getInstance(modalEl) || new bootstrap.Modal(modalEl);
        modalInstance.hide();
    });
</script>