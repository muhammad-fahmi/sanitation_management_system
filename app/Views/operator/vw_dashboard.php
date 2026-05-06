<?= $this->extend('layouts/vw_master') ?>

<?= $this->section('content') ?>
<div class="container-fluid">
    <!-- User Profile Card -->
    <div class="card overflow-hidden">
        <div class="card-body p-0">
            <img src="<?= base_url('assets/images/backgrounds/profilebg.jpg') ?>" class="img-fluid">
            <div class="row align-items-center">
                <div class="col-lg-12 mt-n3 text-center">
                    <div class="mt-n5">
                        <div class="d-flex align-items-center justify-content-center mb-2">
                            <div class="d-flex align-items-center justify-content-center" style="width: 110px; height: 110px;">
                                <div class="border border-4 border-white d-flex align-items-center justify-content-center rounded-circle overflow-hidden" style="width: 100px; height: 100px;">
                                    <img src="<?= profile_image_url($user_info['name'] ?? null) ?>" class="w-100 h-100"
                                        alt="<?= esc($user_info['name']) ?>">
                                </div>
                            </div>
                        </div>
                        <div class="text-center">
                            <h4 class="fs-5 mb-1 fw-semibold text-uppercase"><?= esc($user_info['name']) ?> (<?= esc($user_info['user_role']) ?>)
                            </h4>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Rooms List Card -->
    <div class="card p-3">
        <div class="card-header pb-3">
            <h3 class="card-title text-center text-uppercase mb-3">Daftar Ruangan</h3>
            <input type="text" id="searchBox" placeholder="🔍 Cari ruangan..." class="form-control" />
        </div>
        <div class="card-body">
            <?php if (empty($rooms)): ?>
                <div class="alert alert-warning text-center">
                    <i class="fas fa-inbox"></i> Tidak ada ruangan tersedia
                </div>
            <?php else: ?>
                <div class="row justify-content-center" id="rooms_container">
                    <?php foreach ($rooms as $room): ?>
                                <div class='col-sm-12 col-md-4 col-lg-6 p-2 room_button' data-room-name='<?= strtolower($room['location_name']) ?>'>
                            <button class="btn btn-primary text-bg-primary p-3 text-uppercase position-relative"
                                style="width:100%;height:calc(100vh / 10);" onclick="visitRoom(<?= $room['location_id']; ?>)">
                                <?= esc($room['location_name']) ?>
                                <?php if (!empty($room['has_revision'])): ?>
                                    <span
                                        class="position-absolute top-0 start-100 translate-middle badge bg-warning text-dark rounded-pill">!</span>
                                <?php elseif (!empty($room['submit_count'])): ?>
                                    <span class="position-absolute top-0 start-100 translate-middle badge bg-success rounded-pill">
                                        <?= (int) $room['submit_count'] ?>
                                </span>
                                <?php endif; ?>
                            </button>
                        </div>
                        <?php endforeach; ?>
                        </div>
            <?php endif; ?>
        </div>
    </div>
</div>
<?= $this->endSection() ?>

<?= $this->section('script') ?>
<script>
    const searchBox = document.getElementById('searchBox');
    let currentQuery = '';

    // Filter rooms by search query
    function filterRooms() {
        const listItems = document.querySelectorAll('div.room_button');
        let visibleCount = 0;

        listItems.forEach(item => {
            const text = item.getAttribute('data-room-name');
            if (text.includes(currentQuery)) {
                item.style.display = '';
                visibleCount++;
            } else {
                item.style.display = 'none';
            }
        });

        // Show no results message
        const container = document.getElementById('rooms_container');
        if (visibleCount === 0 && currentQuery.length > 0) {
            if (!document.getElementById('no-results-msg')) {
                const msg = document.createElement('div');
                msg.id = 'no-results-msg';
                msg.className = 'alert alert-info mt-3';
                msg.textContent = 'Ruangan tidak ditemukan';
                container.parentElement.appendChild(msg);
            }
        } else {
            const msg = document.getElementById('no-results-msg');
            if (msg) msg.remove();
        }
    }

    // Event listener for search
    searchBox.addEventListener('input', (e) => {
        currentQuery = e.target.value.toLowerCase().trim();
        filterRooms();
    });

    // Visit room function
    function visitRoom(locationId) {
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
</script>
<?= $this->endSection() ?>
