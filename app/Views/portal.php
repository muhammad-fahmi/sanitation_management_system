<?= $this->extend('layouts/portal_layout') ?>

<?= $this->section('style') ?>
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="row p-3">
    <div class="col w-100 text-center">
        <h3>Selamat Datang Di Portal PT. Bionic Natura</h3>
        <p>Silahkan pilih aplikasi yang ingin diakses</p>
    </div>
</div>
<div class="row p-3">
    <div class="col">
        <div class="card h-100">
            <a class="btn btn-primary h-100 d-flex justify-content-center align-items-center"
                href="<?= base_url('auth/login'); ?>">Cleaning Tracker</a>
        </div>
    </div>
    <div class="col">
        <div class="card h-100">
            <a class="btn btn-primary h-100 d-flex justify-content-center align-items-center"
                href="http://localhost:8000">Project Tracker</a>
        </div>
    </div>
    <div class="col">
        <div class="card h-100">
            <a class="btn btn-primary h-100 d-flex justify-content-center align-items-center"
                onclick="underDev()">Maintenance Web</a>
        </div>
    </div>
</div>
<?= $this->endSection() ?>

<?= $this->section('script') ?>
<script>
    // Under Development Alert
    function underDev() {
        toastr.warning("Masih dalam proses pengembangan", "Under Development", { timeOut: 2000, progressBar: true, closeButton: true, });
    }
</script>
<?= $this->endSection() ?>