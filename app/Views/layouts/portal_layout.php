<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Portal</title>
    <!-- Favicon Icon-->
    <link id="favicon" rel="shortcut icon" href="<?= base_url("logo_light.png") ?>" type="image/png">

    <!-- Core CSS -->
    <link rel="stylesheet" href="<?= base_url('assets/css/styles.css') ?>" />
    <!-- Custom CSS -->
    <?= $this->renderSection('style') ?>
</head>

<body>
    <!-- Preloader -->
    <div class="preloader">
        <img src="<?= base_url("logo_dark.png") ?>" alt="loader" class="lds-ripple img-fluid" />
    </div>
    <!-- END Preloader -->

    <!-- Main -->
    <div id="main-wrapper">
        <div class="container p-5">
            <?= $this->renderSection('content') ?>
        </div>
        <?= $this->renderSection('footer') ?>
    </div>
    <!-- END Main -->
    <div class="dark-transparent sidebartoggler"></div>

    <!-- Import JS Files -->
    <!-- JQuery -->
    <script src="<?= base_url('assets/libs/jquery/dist/jquery.min.js'); ?>"></script>
    <script src="<?= base_url('assets/js/app.min.js'); ?>"></script>
    <script src="<?= base_url('assets/js/app.init.js'); ?>"></script>
    <!-- Bootstrap 5 -->
    <script src="<?= base_url('assets/libs/bootstrap/dist/js/bootstrap.bundle.min.js'); ?>"></script>
    <!-- Simplebar JS -->
    <script src="<?= base_url('assets/libs/simplebar/dist/simplebar.min.js'); ?>"></script>
    <!-- Iconify JS -->
    <script src="<?= base_url('assets/js/iconify-icon.min.js'); ?>"></script>
    <!-- Sidebarmenu JS -->
    <script src="<?= base_url('assets/js/sidebarmenu.js'); ?>"></script>
    <!-- Theme JS (App Interaction) -->
    <script src="<?= base_url('assets/js/theme.js'); ?>"></script>
    <!-- Feather Icons-->
    <script src="<?= base_url('assets/js/feather.min.js'); ?>"></script>
    <!-- Toastr JS -->
    <script src="<?= base_url('assets/js/plugins/toastr-init.js'); ?>"></script>
    <!-- SweetAlert2 -->
    <script src="<?= base_url('assets/libs/sweetalert2/dist/sweetalert2.all.min.js'); ?>"></script>
    <!-- Lodash JS -->
    <script src="<?= base_url('assets/js/lodash.js'); ?>"></script>
    <!-- Custom JS -->
    <script>
        feather.replace();
        // get icon id
        const faviconLink = document.getElementById('favicon');

        // function for changing icon color based on theme
        function setFavicon() {
            if (window.matchMedia && window.matchMedia('(prefers-color-scheme: dark)').matches) {
                // Dark mode
                faviconLink.href = '<?= base_url("logo_light.png") ?>';
            } else {
                // Light mode or no preference
                faviconLink.href = '<?= base_url("logo_dark.png") ?>';
            }
        }

        // Initial check
        setFavicon();

        // Listen for changes in the system theme
        window.matchMedia('(prefers-color-scheme: dark)').addEventListener('change', setFavicon);

        // Handle Color Theme
        function handleColorTheme(e) {
            $("html").attr("data-color-theme", e);
            $(e).prop("checked", !0);
        }

        // Under Development Alert
        function underDev() {
            toastr.warning("Masih dalam proses pengembangan", "Under Development", { timeOut: 2000, progressBar: true, closeButton: true, });
        }
    </script>
    <?= $this->renderSection('script') ?>
</body>

</html>