<?= $this->extend('layouts/vw_master') ?>

<?= $this->section('content') ?>
<div class="container-fluid">

    <!-- User Profile Card -->
    <div class="card overflow-hidden">
        <div class="card-body p-0">
            <img src="<?= base_url('assets/images/backgrounds/profilebg.jpg') ?>" alt="" class="img-fluid">
            <div class="row align-items-center">
                <div class="col-lg-12 mt-n3 text-center">
                    <div class="mt-n5">
                        <div class="d-flex align-items-center justify-content-center mb-2">
                            <div class="d-flex align-items-center justify-content-center"
                                style="width: 110px; height: 110px;">
                                <div class="border border-4 border-white d-flex align-items-center justify-content-center rounded-circle overflow-hidden"
                                    style="width: 100px; height: 100px;">
                                    <img src="<?= profile_image_url($user_info['name'] ?? null) ?>"
                                        alt="" class="w-100 h-100">
                                </div>
                            </div>
                        </div>
                        <div class="text-center">
                            <h5 class="fs-5 mb-0 fw-semibold text-capitalize"><?= esc($user_info['name']) ?></h5>
                            <p class="mb-0 fs-4"><?= esc($user_info['user_role']) ?></p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Revision Tasks Table -->
    <div class="card p-0">
        <div class="card-header text-bg-primary text-white">
            <div class="d-flex justify-content-between align-items-center">
                <h3 class="card-title text-white mb-0">Tugas yang Direvisi</h3>
                <?php if ($revision_room_count > 0): ?>
                    <span class="badge bg-danger fs-6"><?= $revision_room_count ?> Ruangan</span>
                <?php endif; ?>
            </div>
        </div>
        <div class="card-body">
            <?php if (empty($submissions)): ?>
                <div class="alert alert-info text-center mb-0">
                    <i class="fas fa-check-circle"></i> Tidak ada tugas yang perlu direvisi. Semua tugas sudah selesai!
                </div>
            <?php else: ?>
                <div class="table-container overflow-x-scroll">
                    <table class="table table-striped text-center" style="vertical-align: middle" id="revisiTable">
                        <thead>
                            <tr>
                                <th class="text-center">#</th>
                                <th class="text-center">Tanggal</th>
                                <th class="text-center">Item</th>
                                <th class="text-center">Aksi</th>
                                <th class="text-center">Lokasi</th>
                                <th class="text-center">Deskripsi Revisi</th>
                                <th class="text-center">Foto Bukti</th>
                                <th class="text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $no = 1;
                            ?>
                            <?php foreach ($submissions as $submission): ?>
                                <tr>
                                    <td><strong><?= $no++ ?></strong></td>
                                    <td><?= date('d M Y', strtotime($submission['date'])) ?></td>
                                    <td><?= esc($submission['item_name'] ?? 'Item Tidak Ditemukan') ?></td>
                                    <td><?= esc($submission['action_names'] ?? 'Aksi Tidak Ditemukan') ?></td>
                                    <td><?= esc($submission['location_name'] ?? 'Lokasi Tidak Ditemukan') ?></td>
                                    <td><small><?= esc($submission['revision_message'] ?? '-') ?></small></td>
                                    <td>
                                        <?php if (!empty($submission['revision_image_path'])): ?>
                                            <a href="<?= base_url($submission['revision_image_path']) ?>" target="_blank" rel="noopener noreferrer">
                                                <img src="<?= base_url($submission['revision_image_path']) ?>" alt="Bukti Revisi" class="img-fluid rounded border"
                                                    style="max-height: 70px; max-width: 120px; object-fit: cover;">
                                            </a>
                                        <?php else: ?>
                                            <small class="text-muted">-</small>
                                        <?php endif; ?>
                                        </td>
                                        <td>
                                        <button class="btn btn-sm btn-primary"
                                            onclick="goToRoom(<?= $submission['location_id'] ?>)">
                                            <iconify-icon icon="solar:arrow-right-bold"></iconify-icon> Revisi
                                        </button>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        </div>
    </div>

</div>
<?= $this->endSection() ?>

<?= $this->section('script') ?>
<script>
    function goToRoom(locationId) {
        const btn = event.target.closest('button');
        btn.disabled = true;
        btn.innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Loading...';

        $.ajax({
            url: '<?= base_url('operator/increment_visit/'); ?>' + locationId,
            type: 'POST',
            data: {
                '<?= csrf_token() ?>': '<?= csrf_hash() ?>'
            },
            success: function (response) {
                window.location.href = '<?= base_url('operator/scan/'); ?>' + locationId;
            },
            error: function (xhr) {
                btn.disabled = false;
                btn.innerHTML = btn.innerHTML.split('<span')[0];
                toastr.error('Gagal mengakses ruangan. Silahkan coba lagi.', 'Error');
            }
        });
    }

    $(document).ready(function () {
        // Initialize DataTable only if there are submissions
        <?php if (!empty($submissions)): ?>
            $('#revisiTable').DataTable({
                responsive: true,
                language: {
                    search: "Cari:",
                    info: "Menampilkan _START_ hingga _END_ dari _TOTAL_ tugas",
                    emptyTable: "Tidak ada data untuk ditampilkan"
                },
                pageLength: 10,
                order: [[1, 'desc']] // Sort by date descending
            });
        <?php endif; ?>
    });
</script>
<?= $this->endSection() ?>