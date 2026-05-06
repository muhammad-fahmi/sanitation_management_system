<?= $this->extend('layouts/vw_master') ?>

<?= $this->section('page_title') ?>
Admin Page
<?= $this->endSection() ?>

<?= $this->section('style') ?>
<style>
    .btn {
        width: auto;
        height: 100px;
    }

    .icon-size {
        font-size: 4vw;
    }
</style>
<script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="container mt-3">
    <!-- Statistics Cards -->
    <div class="row mb-4">
        <div class="col-md-3 mb-3">
            <div class="card border-start border-primary border-4 h-100">
                <div class="card-body d-flex align-items-center">
                    <div class="w-100 d-flex justify-content-around align-items-center">
                        <div>
                            <h6 class="text-muted mb-1">Total User</h6>
                            <h2 class="mb-0 fw-bold text-center" id="total_users">-</h2>
                        </div>
                        <div class="text-primary">
                            <iconify-icon icon="heroicons:user-group-solid"
                                class="opacity-50 mb-0 icon-size"></iconify-icon>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3 mb-3">
            <div class="card border-start border-danger border-4 h-100">
                <div class="card-body d-flex align-items-center">
                    <div class="w-100 d-flex justify-content-around align-items-center">
                        <div>
                            <h6 class="text-muted mb-1">Admin</h6>
                            <h2 class="mb-0 fw-bold text-center" id="admin_count">-</h2>
                        </div>
                        <div class="text-danger">
                            <iconify-icon icon="fa7-solid:user-shield" class="opacity-50 mb-0 icon-size"></iconify-icon>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3 mb-3">
            <div class="card border-start border-success border-4 h-100">
                <div class="card-body d-flex align-items-center">
                    <div class="w-100 d-flex justify-content-around align-items-center">
                        <div>
                            <h6 class="text-muted mb-1">Petugas</h6>
                            <h2 class="mb-0 fw-bold text-center" id="operator_count">-</h2>
                        </div>
                        <div class="text-success">
                            <iconify-icon icon="fa7-solid:user-gear" class="opacity-50 mb-0 icon-size"></iconify-icon>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3 mb-3">
            <div class="card border-start border-warning border-4 h-100">
                <div class="card-body d-flex align-items-center">
                    <div class="w-100 d-flex justify-content-around align-items-center">
                        <div>
                            <h6 class="text-muted mb-1">Verifikator</h6>
                            <h2 class="mb-0 fw-bold text-center" id="verifikator_count">-</h2>
                        </div>
                        <div class="text-warning">
                            <iconify-icon icon="fa7-solid:user-check" class="opacity-50 mb-0 icon-size"></iconify-icon>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Room Visits Chart -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title">Kunjungan Ruangan</h5>
                </div>
                <div class="card-body">
                    <div id="roomVisitsChart"></div>
                </div>
            </div>
        </div>
    </div>

    <!-- Item Clean Count Chart -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex align-items-center gap-3 flex-wrap">
                    <h5 class="card-title mb-0">Frekuensi Kebersihan Item</h5>
                    <select id="location_filter" class="form-select" style="min-width:220px">
                        <option value="">-- Pilih Lokasi --</option>
                    </select>
                </div>
                <div class="card-body">
                    <div id="itemCleanChart">
                        <p class="text-muted text-center mt-4">Pilih lokasi untuk menampilkan data.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>
<?= $this->section('script') ?>
<script>
    $(document).ready(function () {
        $.ajax({
            url: '<?= base_url('admin/get_stats') ?>',
            type: 'GET',
            dataType: 'json',
            success: function (res) {
                if (res.status === 200) {
                    $('#total_users').text(res.data.total_users);
                    $('#admin_count').text(res.data.admin_count);
                    $('#operator_count').text(res.data.operator_count);
                    $('#verifikator_count').text(res.data.verifikator_count);
                }
            }
        });

        $.ajax({
            url: '<?= base_url('admin/get_room_visits') ?>',
            type: 'GET',
            dataType: 'json',
            success: function (res) {
                if (res.status === 200) {
                    const data = res.data;
                    const categories = data.map(item => item.location_name);
                    const seriesData = data.map(item => item.visit_count);

                    const options = {
                        chart: {
                            type: 'bar',
                            height: 400,
                            toolbar: {
                                show: true,
                                tools: { download: true, selection: false, zoom: false, zoomin: false, zoomout: false, pan: false, reset: false },
                                export: { png: { filename: 'kunjungan-ruangan' }, svg: { filename: 'kunjungan-ruangan' } }
                            }
                        },
                        series: [{
                            name: 'Kunjungan',
                            data: seriesData
                        }],
                        xaxis: {
                            categories: categories
                        },
                        title: {
                            text: 'Jumlah Kunjungan per Ruangan'
                        }
                    };

                    const chart = new ApexCharts(document.querySelector("#roomVisitsChart"), options);
                    chart.render();
                }
            }
        });

        // Populate location dropdown
        $('#location_filter').select2({
            theme: 'bootstrap-5',
            placeholder: '-- Pilih Lokasi --',
            allowClear: true,
            width: 'resolve'
        });

        $.ajax({
            url: '<?= base_url('admin/get_locations') ?>',
            type: 'GET',
            dataType: 'json',
            success: function (res) {
                if (res.status === 200) {
                    res.data.forEach(function (loc) {
                        $('#location_filter').append(
                            $('<option>', { value: loc.location_id, text: loc.location_name })
                        );
                    });
                    $('#location_filter').trigger('change.select2');
                }
            }
        });

        let itemCleanChart = null;

        $('#location_filter').on('change', function () {
            const locationId = $(this).val();

            if (!locationId) {
                if (itemCleanChart) { itemCleanChart.destroy(); itemCleanChart = null; }
                $('#itemCleanChart').html('<p class="text-muted text-center mt-4">Pilih lokasi untuk menampilkan data.</p>');
                return;
            }

            $.ajax({
                url: '<?= base_url('admin/get_item_clean_count') ?>',
                type: 'GET',
                dataType: 'json',
                data: { location_id: locationId },
                success: function (res) {
                    if (res.status === 200) {
                        const labels   = res.data.map(d => d.item_name);
                        const counts   = res.data.map(d => parseInt(d.clean_count));
                        const locName  = $('#location_filter option:selected').text();

                        if (itemCleanChart) { itemCleanChart.destroy(); itemCleanChart = null; }
                        $('#itemCleanChart').html('');

                        if (labels.length === 0) {
                            $('#itemCleanChart').html('<p class="text-muted text-center mt-4">Tidak ada item untuk lokasi ini.</p>');
                            return;
                        }

                        const options = {
                            chart: { type: 'bar', height: 380,
                                toolbar: {
                                    show: true,
                                    tools: { download: true, selection: false, zoom: false, zoomin: false, zoomout: false, pan: false, reset: false },
                                    export: { png: { filename: 'item-kebersihan-' + locName }, svg: { filename: 'item-kebersihan-' + locName } }
                                }
                            },
                            plotOptions: {
                                bar: { horizontal: false, borderRadius: 4, dataLabels: { position: 'top' } }
                            },
                            dataLabels: { enabled: true, offsetY: -20, style: { fontSize: '12px' } },
                            series: [{ name: 'Jumlah Dibersihkan', data: counts }],
                            xaxis: {
                                categories: labels,
                                labels: { rotate: -35, trim: true }
                            },
                            yaxis: { title: { text: 'Kali Dibersihkan' }, min: 0, forceNiceScale: true },
                            title: { text: 'Item di "' + locName + '"', align: 'left' },
                            colors: ['#5d87ff'],
                            tooltip: { y: { formatter: val => val + ' kali' } }
                        };

                        itemCleanChart = new ApexCharts(document.querySelector('#itemCleanChart'), options);
                        itemCleanChart.render();
                    }
                }
            });
        });
    });
</script>
<?= $this->endSection() ?>