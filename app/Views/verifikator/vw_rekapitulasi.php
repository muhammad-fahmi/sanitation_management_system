<?= $this->extend('layouts/vw_master') ?>

<?= $this->section('content') ?>
<div class="container-fluid">

    <div class="card mb-4">
        <div class="card-header text-bg-primary text-white">
            <h3 class="card-title text-white text-center">Rekapitulasi</h3>
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

</div>
<?= $this->endSection() ?>

<?= $this->section('script') ?>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
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
    });
</script>
<?= $this->endSection() ?>