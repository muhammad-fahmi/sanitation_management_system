<?= $this->extend('layouts/vw_master') ?>

<?= $this->section('content') ?>
<div class="container-fluid">

    <div class="card mb-4">
        <div class="card-header text-bg-primary text-white d-flex align-items-center justify-content-between flex-wrap gap-2">
            <h3 class="card-title text-white text-center mb-0">Rekapitulasi</h3>
            <div class="d-flex gap-2">
                <button class="btn btn-sm btn-light" onclick="exportChart('rekapChart','rekapitulasi','png')">
                    <iconify-icon icon="solar:download-linear" class="me-1"></iconify-icon>PNG
                </button>
                <button class="btn btn-sm btn-light" onclick="exportChart('rekapChart','rekapitulasi','jpg')">
                    <iconify-icon icon="solar:download-linear" class="me-1"></iconify-icon>JPG
                </button>
            </div>
        </div>
        <div class="card-body">
            <div class="row g-3 mb-3">
                <div class="col-md-4">
                    <div class="border rounded p-3 bg-warning-subtle text-center">
                        <h6 class="mb-1">Menunggu</h6>
                        <h3 class="mb-0" id="pendingTotal">0</h3>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="border rounded p-3 bg-info-subtle text-center">
                        <h6 class="mb-1">Revisi</h6>
                        <h3 class="mb-0" id="revisiTotal">0</h3>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="border rounded p-3 bg-success-subtle text-center">
                        <h6 class="mb-1">Terverifikasi</h6>
                        <h3 class="mb-0" id="verifiedTotal">0</h3>
                    </div>
                </div>
            </div>

            <div class="position-relative" style="height: 360px;">
                <canvas id="rekapChart"></canvas>
            </div>
        </div>
    </div>

    <!-- Room Visits Chart -->
    <div class="card mb-4">
        <div class="card-header d-flex align-items-center justify-content-between flex-wrap gap-2">
            <h5 class="card-title mb-0">Kunjungan Ruangan</h5>
            <div class="d-flex align-items-center gap-2 flex-wrap ms-auto">
                <input type="date" id="room_visits_date_filter" class="form-control" style="min-width:180px;" />
                <button class="btn btn-sm btn-outline-secondary" onclick="exportChart('roomVisitsChart','kunjungan-ruangan','png')">
                    <iconify-icon icon="solar:download-linear" class="me-1"></iconify-icon>PNG
                </button>
                <button class="btn btn-sm btn-outline-secondary" onclick="exportChart('roomVisitsChart','kunjungan-ruangan','jpg')">
                    <iconify-icon icon="solar:download-linear" class="me-1"></iconify-icon>JPG
                </button>
            </div>
        </div>
        <div class="card-body">
            <div class="position-relative" style="height: 360px;">
                <canvas id="roomVisitsChart"></canvas>
            </div>
        </div>
    </div>

    <!-- Item Clean Count Chart -->
    <div class="card mb-4">
        <div class="card-header d-flex align-items-center gap-3 flex-wrap">
            <h5 class="card-title mb-0">Frekuensi Kebersihan Item</h5>
            <div class="d-flex align-items-center gap-2 flex-wrap">
                <select id="location_filter" class="form-select" style="min-width:220px;" data-placeholder="-- Pilih Lokasi --">
                    <option value="">-- Pilih Lokasi --</option>
                </select>
                <input type="date" id="item_date_filter" class="form-control" style="min-width:180px;" />
            </div>
            <div class="ms-auto d-flex gap-2" id="itemChartExportBtns" style="display:none !important;">
                <button class="btn btn-sm btn-outline-secondary" onclick="exportChart('itemCleanChart','item-kebersihan','png')">
                    <iconify-icon icon="solar:download-linear" class="me-1"></iconify-icon>PNG
                </button>
                <button class="btn btn-sm btn-outline-secondary" onclick="exportChart('itemCleanChart','item-kebersihan','jpg')">
                    <iconify-icon icon="solar:download-linear" class="me-1"></iconify-icon>JPG
                </button>
            </div>
        </div>
        <div class="card-body">
            <div id="itemCleanChartWrap">
                <p class="text-muted text-center mt-4">Pilih lokasi untuk menampilkan data.</p>
            </div>
            <div class="position-relative" style="height: 380px; display:none;" id="itemCleanChartContainer">
                <canvas id="itemCleanChart"></canvas>
            </div>
        </div>
    </div>

</div>
<?= $this->endSection() ?>

<?= $this->section('script') ?>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    function exportChart(canvasId, filename, format) {
        const canvas = document.getElementById(canvasId);
        if (!canvas) return;

        const mimeType = format === 'jpg' ? 'image/jpeg' : 'image/png';
        const ext      = format === 'jpg' ? 'jpg' : 'png';

        // For JPG we need a white background (canvas default is transparent)
        let dataUrl;
        if (format === 'jpg') {
            const tmpCanvas = document.createElement('canvas');
            tmpCanvas.width  = canvas.width;
            tmpCanvas.height = canvas.height;
            const ctx = tmpCanvas.getContext('2d');
            ctx.fillStyle = '#ffffff';
            ctx.fillRect(0, 0, tmpCanvas.width, tmpCanvas.height);
            ctx.drawImage(canvas, 0, 0);
            dataUrl = tmpCanvas.toDataURL(mimeType, 0.95);
        } else {
            dataUrl = canvas.toDataURL(mimeType);
        }

        const a = document.createElement('a');
        a.href     = dataUrl;
        a.download = filename + '.' + ext;
        a.click();
    }

    $(document).ready(function () {
        const endpoint = '<?= base_url('verifikator/laporan/rekapitulasi/summary'); ?>';
        let chartInstance = null;

        function renderChart(pending, revisi, verified) {
            const ctx = document.getElementById('rekapChart').getContext('2d');

            if (chartInstance) {
                chartInstance.destroy();
            }

            chartInstance = new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: ['Menunggu', 'Revisi', 'Terverifikasi'],
                    datasets: [{
                        label: 'Total Tugas',
                        data: [pending, revisi, verified],
                        backgroundColor: [
                            'rgba(255, 193, 7, 0.7)',
                            'rgba(13, 202, 240, 0.7)',
                            'rgba(25, 135, 84, 0.7)'
                        ],
                        borderColor: [
                            'rgba(255, 193, 7, 1)',
                            'rgba(13, 202, 240, 1)',
                            'rgba(25, 135, 84, 1)'
                        ],
                        borderWidth: 1,
                        borderRadius: 8
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            display: false
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: {
                                precision: 0
                            }
                        }
                    }
                }
            });
        }

        function loadSummary() {
            $.ajax({
                url: endpoint,
                type: 'GET',
                success: function (response) {
                    if (!response.success) {
                        toastr.error('Gagal memuat data rekapitulasi.');
                        return;
                    }

                    const pending = parseInt(response.data.pending || 0, 10);
                    const revisi = parseInt(response.data.revisi || 0, 10);
                    const verified = parseInt(response.data.verified || 0, 10);

                    $('#pendingTotal').text(pending);
                    $('#revisiTotal').text(revisi);
                    $('#verifiedTotal').text(verified);

                    renderChart(pending, revisi, verified);
                },
                error: function () {
                    toastr.error('Terjadi kesalahan saat memuat rekapitulasi.');
                }
            });
        }

        loadSummary();

        // Room Visits Chart
        let roomVisitsChartInstance = null;

        function loadRoomVisitsChart() {
            const date = $('#room_visits_date_filter').val();

            $.ajax({
                url: '<?= base_url('admin/get_room_visits') ?>',
                type: 'GET',
                dataType: 'json',
                data: { date: date },
                success: function (res) {
                    if (res.status !== 200) return;

                    const labels = res.data.map(d => d.location_name);
                    const counts = res.data.map(d => parseInt(d.visit_count));

                    if (roomVisitsChartInstance) {
                        roomVisitsChartInstance.destroy();
                        roomVisitsChartInstance = null;
                    }

                    if (!labels.length) {
                        return;
                    }

                    roomVisitsChartInstance = new Chart(document.getElementById('roomVisitsChart').getContext('2d'), {
                        type: 'bar',
                        data: {
                            labels: labels,
                            datasets: [{
                                label: 'Kunjungan',
                                data: counts,
                                backgroundColor: 'rgba(93, 135, 255, 0.7)',
                                borderColor: 'rgba(93, 135, 255, 1)',
                                borderWidth: 1,
                                borderRadius: 6
                            }]
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            plugins: { legend: { display: false } },
                            scales: { y: { beginAtZero: true, ticks: { precision: 0 } } }
                        }
                    });
                }
            });
        }

        loadRoomVisitsChart();

        $('#room_visits_date_filter').on('change', function () {
            loadRoomVisitsChart();
        });

        function initLocationSelect2() {
            const $locationFilter = $('#location_filter');

            if (!$locationFilter.length || !$.fn.select2) {
                return;
            }

            if ($locationFilter.hasClass('select2-hidden-accessible')) {
                $locationFilter.select2('destroy');
            }

            $locationFilter.select2({
                theme: 'bootstrap-5',
                placeholder: $locationFilter.data('placeholder') || '-- Pilih Lokasi --',
                allowClear: true,
                width: '100%'
            });
        }

        initLocationSelect2();

        // Populate location dropdown
        $.ajax({
            url: '<?= base_url('admin/get_locations') ?>',
            type: 'GET',
            dataType: 'json',
            success: function (res) {
                if (res.status !== 200) return;
                res.data.forEach(function (loc) {
                    $('#location_filter').append(
                        $('<option>', { value: loc.location_id, text: loc.location_name })
                    );
                });

                initLocationSelect2();
            }
        });

        let itemCleanChartInstance = null;

        function formatDateLabel(dateValue) {
            if (!dateValue) {
                return 'Semua Tanggal';
            }
            const d = new Date(dateValue + 'T00:00:00');
            if (Number.isNaN(d.getTime())) {
                return dateValue;
            }
            return d.toLocaleDateString('id-ID', {
                day: '2-digit',
                month: 'short',
                year: 'numeric'
            });
        }

        function loadItemCleanChart() {
            const locationId = $('#location_filter').val();
            const selectedDate = $('#item_date_filter').val();

            if (!locationId) {
                if (itemCleanChartInstance) { itemCleanChartInstance.destroy(); itemCleanChartInstance = null; }
                $('#itemCleanChartContainer').hide();
                $('#itemChartExportBtns').hide();
                $('#itemCleanChartWrap').show().html('<p class="text-muted text-center mt-4">Pilih lokasi untuk menampilkan data.</p>');
                return;
            }

            $.ajax({
                url: '<?= base_url('admin/get_item_clean_count') ?>',
                type: 'GET',
                dataType: 'json',
                data: {
                    location_id: locationId,
                    date: selectedDate
                },
                success: function (res) {
                    if (res.status !== 200) return;

                    const labels = res.data.map(d => d.item_name);
                    const counts = res.data.map(d => parseInt(d.clean_count));
                    const locName = $('#location_filter option:selected').text();
                    const dateLabel = formatDateLabel(selectedDate);

                    if (itemCleanChartInstance) { itemCleanChartInstance.destroy(); itemCleanChartInstance = null; }

                    if (!labels.length) {
                        $('#itemCleanChartContainer').hide();
                        $('#itemChartExportBtns').hide();
                        $('#itemCleanChartWrap').show().html('<p class="text-muted text-center mt-4">Tidak ada item untuk lokasi dan tanggal ini.</p>');
                        return;
                    }

                    $('#itemCleanChartWrap').hide();
                    $('#itemCleanChartContainer').show();
                    $('#itemChartExportBtns').show();

                    itemCleanChartInstance = new Chart(document.getElementById('itemCleanChart').getContext('2d'), {
                        type: 'bar',
                        data: {
                            labels: labels,
                            datasets: [{
                                label: 'Jumlah Dibersihkan',
                                data: counts,
                                backgroundColor: 'rgba(93, 135, 255, 0.7)',
                                borderColor: 'rgba(93, 135, 255, 1)',
                                borderWidth: 1,
                                borderRadius: 6
                            }]
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            plugins: {
                                legend: { display: false },
                                title: { display: true, text: 'Item di "' + locName + '" (' + dateLabel + ')' }
                            },
                            scales: {
                                y: { beginAtZero: true, ticks: { precision: 0 }, title: { display: true, text: 'Kali Dibersihkan' } }
                            }
                        }
                    });
                }
            });
        }

        $('#location_filter').on('change', function () {
            loadItemCleanChart();
        });

        $('#item_date_filter').on('change', function () {
            loadItemCleanChart();
        });
    });
</script>
<?= $this->endSection() ?>