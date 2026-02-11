<?= $this->extend('layouts/vw_master') ?>

<?= $this->section('content') ?>
<div class="container-fluid">

    <div class="card overflow-hidden">
        <div class="card-body p-0">
            <img src="<?= base_url('assets/images/backgrounds/profilebg.jpg') ?>" alt="" class="img-fluid">
            <div class="row align-items-center">
                <div class="col-lg-12 mt-n3 text-center">
                    <div class="mt-n5">
                        <?php $displayName = display_name($user_info); ?>
                        <?php $displayRole = display_role($user_info, 'Verifikator'); ?>
                        <div class="d-flex align-items-center justify-content-center mb-2">
                            <div class=" d-flex align-items-center justify-content-center"
                                style="width: 110px; height: 110px;">
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
        <div class="card-header text-bg-success text-white">
            <h3 class="card-title text-white text-center">
                Daftar Tugas Terverifikasi
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
                            <th class="text-center">Verifikator</th>
                            <th class="text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody id="list_pekerjaan"></tbody>
                </table>
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
                url: '<?= base_url('verifikator/get_verified_datatable'); ?>',
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
                            case 'verified':
                            case 'selesai':
                                return '<span class="badge bg-success">Verified</span>';
                            default:
                                return '<span class="badge bg-secondary">' + (data || '-') + '</span>';
                        }
                    }
                },
                {
                    data: 'verified_by_name',
                    orderable: false,
                    render: function (data) {
                        return data || '-';
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

        const loadTaskModal = (id) => {
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

                    $('#taskDetailsReadOnly').html(renderTaskDetails(task));
                    $('#detailModal').modal('show');
                },
                error: function () {
                    alert('Terjadi kesalahan saat memuat detail tugas.');
                }
            });
        };

        $(document).on('click', '.btn-detail', function (e) {
            e.preventDefault();
            const id = $(this).data('id');
            if (!id) {
                alert('ID tugas tidak ditemukan.');
                return;
            }
            loadTaskModal(id);
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