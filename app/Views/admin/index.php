<?= $this->extend('layouts/vw_master') ?>

<?= $this->section('page_title') ?>
Admin Page
<?= $this->endSection() ?>

<?= $this->section('style') ?>
<style>
    .stat-card-btn {
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
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="card-title mb-0">Kunjungan Ruangan</h5>
                    <div class="d-flex align-items-center gap-2">
                        <label for="visitDate" class="form-label mb-0 me-2">Tanggal:</label>
                        <input type="date" id="visitDate" class="form-control form-control-sm me-2"
                            style="width: 180px;" value="<?= date('Y-m-d') ?>" />
                        <button type="button" id="filterBtn" class="btn btn-primary btn-sm d-flex align-items-center me-2">
                            <iconify-icon icon="fa7-solid:filter" width="20" height="20" style="color: #fff"></iconify-icon>
                            Filter
                        </button>
                    </div>
                </div>
                <div class="card-body">
                    <div id="roomVisitsChart"></div>
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

        // Function to load room visits chart with date filter
        function loadRoomVisitsChart(date = null) {
            if (!date) {
                date = $('#visitDate').val();
            }

            $.ajax({
                url: '<?= base_url('admin/get_room_visits') ?>',
                type: 'GET',
                data: { date: date },
                dataType: 'json',
                success: function (res) {
                    if (res.status === 200) {
                        const data = res.data;
                        const categories = data.map(item => item.location_name);
                        const seriesData = data.map(item => item.visit_count);

                        const options = {
                            chart: {
                                type: 'bar',
                                height: 400
                            },
                            series: [{
                                name: 'Kunjungan',
                                data: seriesData
                            }],
                            xaxis: {
                                categories: categories
                            },
                            title: {
                                text: 'Jumlah Kunjungan per Ruangan (' + date + ')'
                            }
                        };

                        // Clear existing chart
                        const chartContainer = document.querySelector("#roomVisitsChart");
                        chartContainer.innerHTML = '';

                        window.roomVisitsChart = new ApexCharts(chartContainer, options);
                        window.roomVisitsChart.render();
                    }
                }
            });
        }

        // Load initial room visits chart
        loadRoomVisitsChart();

        // Filter button click event
        $('#filterBtn').click(function () {
            loadRoomVisitsChart();
        });

        // Enter key on date input
        $('#visitDate').keypress(function (e) {
            if (e.which == 13) {
                loadRoomVisitsChart();
            }
        });
    });
</script>
<?= $this->endSection() ?>