<?= $this->extend('layouts/vw_master') ?>

<?= $this->section('content') ?>
<div class="container-fluid">

    <div class="card overflow-hidden">
        <div class="card-body p-0">
            <img src="<?= base_url('assets/images/backgrounds/profilebg.jpg') ?>" alt="" class="img-fluid">
            <div class="row align-items-center">
                <div class="col-lg-12 mt-n3 text-center">
                    <div class="mt-n5">
                        <div class="d-flex align-items-center justify-content-center mb-2">
                            <div class=" d-flex align-items-center justify-content-center"
                                style="width: 110px; height: 110px;">
                                    <?php $displayName = display_name($user_info); ?>
                                    <?php $displayRole = display_role($user_info, 'Verifikator'); ?>
                                    <div class="border border-4 border-white d-flex align-items-center justify-content-center rounded-circle overflow-hidden"
                                    style="width: 100px; height: 100px;">
                                    <img src="<?= base_url('assets/profiles/') . esc($displayName) . '.jpg' ?>" alt=""
                                        class="w-100 h-100">
                                </div>
                            </div>
                        </div>
                        <div class="text-center">
                            <h5 class="fs-5 mb-0 fw-semibold"><?= esc($displayName) ?></h5>
                            <p class="mb-0 fs-4"><?= esc($displayRole) ?></p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="card p-0">
        <div class="card-header text-bg-primary text-white">
            <h3 class="card-title text-white text-center">
                Daftar Tugas
            </h3>
        </div>
        <div class="card-body">
            <div class="mb-3">
                <label for="filterLocation" class="form-label">Filter Lokasi</label>
                <select id="filterLocation" class="form-select">
                    <option value="0">Semua Lokasi</option>
                </select>
            </div>
            <div class="table-container overflow-x-scroll">
                <table class="table table-striped text-center" style="vertical-align: middle" id="taskTable">
                    <thead>
                        <tr>
                            <th class="text-center">#</th>
                            <th class="text-center">Tanggal</th>
                            <th class="text-center">Item</th>
                            <th class="text-center">Aksi</th>
                            <th class="text-center">Lokasi</th>
                            <th class="text-center">Status</th>
                            <th class="text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody id="list_pekerjaan"></tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Revise Modal -->
    <div class="modal fade" id="reviseModal" tabindex="-1" aria-labelledby="reviseModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="reviseModalLabel">Revisi Tugas</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div id="taskDetails"></div>
                    <div class="mb-3">
                        <label for="reviseMessage" class="form-label">Pesan Revisi</label>
                        <textarea class="form-control" id="reviseMessage" rows="3"
                            placeholder="Masukkan pesan revisi..."></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-danger d-flex align-items-center" data-bs-dismiss="modal">
                        <iconify-icon icon="fa7-solid:ban" width="24" height="24" class="me-1"></iconify-icon>
                        Batal
                    </button>
                    <button type="button" class="btn btn-warning text-white d-flex align-items-center" id="btnRevise">
                        <iconify-icon icon="fa7-solid:save" width="24" height="24" class="me-1"></iconify-icon>
                        <div>
                            <span id="btnReviseText">Revisi</span>
                        </div>

                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Detail Modal -->
    <div class="modal fade" id="detailModal" tabindex="-1" aria-labelledby="detailModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="detailModalLabel">Detail Tugas</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div id="taskDetailsReadOnly"></div>
                    <div class="mb-3">
                        <label for="reviseMessageReadOnly" class="form-label">Pesan Revisi</label>
                        <textarea class="form-control" id="reviseMessageReadOnly" rows="3" readonly></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

</div>
<?= $this->endSection() ?>

<?= $this->section('script') ?>
<script>
    $(document).ready(function () {
        const taskTable = $('#taskTable').DataTable({
            processing: true,
            serverSide: true,
            responsive: true,
            ajax: {
                url: '<?= base_url('verifikator/get_datatable'); ?>',
                type: 'POST',
                data: function (d) {
                    d.location_id = $('#filterLocation').val();
                    return d;
                }
            },
            columns: [
                { data: 'no', orderable: false },
                { data: 'date' },
                { data: 'item_name' },
                { data: 'action_name' },
                { data: 'location_name' },
                {
                    data: 'status',
                    orderable: false,
                    render: function (data) {
                        const status = (data || '').toLowerCase();
                        switch (status) {
                            case 'pending':
                                return '<span class="badge bg-warning">Pending</span>';
                            case 'verified':
                                return '<span class="badge bg-success">Verified</span>';
                            case 'selesai':
                                return '<span class="badge bg-success">Verified</span>';
                            case 'revised':
                            case 'revisi':
                                return '<span class="badge bg-info">Revisi</span>';
                            case 'rejected':
                                return '<span class="badge bg-danger">Rejected</span>';
                            default:
                                return '<span class="badge bg-secondary">' + (data || '-') + '</span>';
                        }
                    }
                },
                {
                    data: null,
                    orderable: false,
                    render: function (data, type, row) {
                        const id = row.task_submission_id || row.id;
                        if (!id) {
                            return '-';
                        }

                        const status = (row.status || '').toLowerCase();

                        if (status === 'pending') {
                            return `
                                <div class="d-flex gap-2">
                                    <button class="btn btn-sm btn-success btn-verify" data-id="${id}">
                                        <iconify-icon icon="solar:check-circle-bold"></iconify-icon> Verifikasi
                                    </button>
                                    <button class="btn btn-sm btn-warning btn-revise" data-id="${id}">
                                        <iconify-icon icon="solar:pen-bold"></iconify-icon> Revisi
                                    </button>
                                </div>
                            `;
                        }

                        if (status === 'revisi' || status === 'revised') {
                            return `
                                <div class="d-flex gap-2">
                                    <button class="btn btn-sm btn-warning btn-edit" data-id="${id}">
                                        <iconify-icon icon="solar:pen-bold"></iconify-icon> Edit Revisi
                                    </button>
                                    <button class="btn btn-sm btn-secondary btn-detail" data-id="${id}">
                                        <iconify-icon icon="solar:eye-bold"></iconify-icon> Detail
                                    </button>
                                </div>
                            `;
                        }

                        return `
                            <button class="btn btn-sm btn-secondary btn-detail" data-id="${id}">
                                <iconify-icon icon="solar:eye-bold"></iconify-icon> Detail
                            </button>
                        `;
                    }
                }
            ],
            language: {
                search: 'Cari:',
            },
            pageLength: 10,
            stateSave: true,
            stateDuration: 86400
        });

        const renderTaskDetails = (task) => `
            <p><strong>Tanggal:</strong> ${task.date}</p>
            <p><strong>Item:</strong> ${task.item_name}</p>
            <p><strong>Aksi:</strong> ${task.action_names || task.action_name || '-'}</p>
            <p><strong>Lokasi:</strong> ${task.location_name}</p>
            <p><strong>Status:</strong> ${task.status}</p>
        `;

        const loadTaskModal = (id, { readOnly = false, prefillMessage = '' } = {}) => {
            $.ajax({
                url: '<?= base_url('verifikator/modal'); ?>',
                type: 'POST',
                data: { id },
                success: function (response) {
                    if (!response.success) {
                        alert('Gagal memuat detail tugas: ' + response.message);
                        return;
                    }

                    const task = response.data;
                    const message = prefillMessage || task.revision_message || '';

                    if (readOnly) {
                        $('#taskDetailsReadOnly').html(renderTaskDetails(task));
                        $('#reviseMessageReadOnly').val(message);
                        $('#detailModal').modal('show');
                        return;
                    }

                    $('#taskDetails').html(renderTaskDetails(task));
                    $('#reviseMessage').val(message);
                    $('#reviseModal').data('task-id', id);
                    $('#reviseModalLabel').text(message ? 'Edit Revisi Tugas' : 'Revisi Tugas');
                    $('#btnReviseText').text(message ? 'Update' : 'Revisi');
                    $('#reviseModal').modal('show');
                },
                error: function () {
                    alert('Terjadi kesalahan saat memuat detail tugas.');
                }
            });
        };

        $(document).on('click', '.btn-verify', function (e) {
            e.preventDefault();
            const id = $(this).data('id');

            if (!id) {
                alert('ID tugas tidak ditemukan.');
                return;
            }

            if (!confirm('Apakah Anda yakin ingin memverifikasi tugas ini?')) {
                return;
            }

            $.ajax({
                url: '<?= base_url('verifikator/update'); ?>',
                type: 'POST',
                data: {
                    id: id,
                    action: 'verifikasi'
                },
                success: function (response) {
                    if (response.success) {
                        alert('Tugas berhasil diverifikasi.');
                        var currentPage = taskTable.page();
                        taskTable.ajax.reload(function () {
                            taskTable.page(currentPage).draw(false);
                        });
                        return;
                    }

                    alert('Gagal memverifikasi tugas: ' + response.message);
                },
                error: function () {
                    alert('Terjadi kesalahan saat memverifikasi tugas.');
                }
            });
        });

        $(document).on('click', '.btn-revise', function (e) {
            e.preventDefault();
            const id = $(this).data('id');
            if (!id) {
                alert('ID tugas tidak ditemukan.');
                return;
            }
            loadTaskModal(id);
        });

        $('#btnRevise').on('click', function () {
            const id = $('#reviseModal').data('task-id');
            const reviseMessage = $('#reviseMessage').val().trim();

            if (!id) {
                alert('ID tugas tidak ditemukan.');
                return;
            }

            if (!reviseMessage) {
                alert('Pesan revisi tidak boleh kosong.');
                return;
            }

            $.ajax({
                url: '<?= base_url('verifikator/update'); ?>',
                type: 'POST',
                data: {
                    id: id,
                    action: 'revisi',
                    revise_description: reviseMessage
                },
                success: function (response) {
                    if (response.success) {
                        alert('Tugas berhasil direvisi.');
                        $('#reviseModal').modal('hide');
                        var currentPage = taskTable.page();
                        taskTable.ajax.reload(function () {
                            taskTable.page(currentPage).draw(false);
                        });
                        return;
                    }

                    alert('Gagal merevisi tugas: ' + response.message);
                },
                error: function () {
                    alert('Terjadi kesalahan saat merevisi tugas.');
                }
            });
        });

        $(document).on('click', '.btn-edit', function (e) {
            e.preventDefault();
            const id = $(this).data('id');
            if (!id) {
                alert('ID tugas tidak ditemukan.');
                return;
            }
            const rowData = taskTable.row($(this).closest('tr')).data();
            loadTaskModal(id, { prefillMessage: rowData?.revision_message || '' });
        });

        $(document).on('click', '.btn-detail', function (e) {
            e.preventDefault();
            const id = $(this).data('id');
            if (!id) {
                alert('ID tugas tidak ditemukan.');
                return;
            }
            const rowData = taskTable.row($(this).closest('tr')).data();
            loadTaskModal(id, { readOnly: true, prefillMessage: rowData?.revision_message || '' });
        });

        // Handle location filter change
        $('#filterLocation').on('change', function () {
            taskTable.ajax.reload();
        });

        // Populate locations dropdown from table data
        let locationsAdded = new Set();
        taskTable.on('draw.dt', function () {
            const rows = taskTable.rows({ page: 'current' }).data();
            rows.each(function (row) {
                if (row.location_name && !locationsAdded.has(row.location_id)) {
                    $('#filterLocation').append(`<option value="${row.location_id}">${row.location_name}</option>`);
                    locationsAdded.add(row.location_id);
                }
            });
        });
    });
</script>
<?= $this->endSection() ?>