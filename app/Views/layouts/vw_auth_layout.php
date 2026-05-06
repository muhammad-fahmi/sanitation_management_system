<?php
if (isset($user_info) && $user_info == null) {
    header('Location: /auth/login');
    die();
} ?>

<!DOCTYPE html>
<html lang="en" dir="ltr" data-bs-theme="light" data-color-theme="Blue_Theme" data-layout="vertical">

<head>
    <!-- Required meta tags -->
    <meta charset="UTF-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />

    <!-- Favicon icon-->
    <link id="favicon" rel="shortcut icon" href="<?= base_url("logo_light.png") ?>" type="image/png">

    <!-- Core Css -->
    <link rel="stylesheet" href="<?= base_url('assets/css/styles.css'); ?>" />
    <script src="https://kit.fontawesome.com/601ba7fe41.js" crossorigin="anonymous"></script>
    <?= $this->renderSection('style') ?>
    <title><?= $page_title ?? "Page Title"; ?></title>
</head>

<body>
    <!-- <div class="toast toast-onload align-items-center text-bg-secondary border-0" role="alert" aria-live="assertive"
        aria-atomic="true">
        <div class="toast-body hstack align-items-start gap-6">
            <i class="ti ti-alert-circle fs-6"></i>
            <div>
                <h5 class="text-white fs-3 mb-1">Welcome to MaterialPro</h5>
                <h6 class="text-white fs-2 mb-0">Easy to costomize the Template!!!</h6>
            </div>
            <button type="button" class="btn-close btn-close-white fs-2 m-0 ms-auto shadow-none" data-bs-dismiss="toast"
                aria-label="Close"></button>
        </div>
    </div> -->
    <!-- Preloader -->
    <div class="preloader">
        <img src="<?= base_url("logo_dark.png") ?>" alt="loader" class="lds-ripple img-fluid" />
    </div>
    <div id="main-wrapper">
        <div
            class="position-relative overflow-hidden radial-gradient min-vh-100 w-100 d-flex align-items-center justify-content-center">
            <div class="d-flex align-items-center justify-content-center w-100">
                <div class="row justify-content-center w-100">
                    <div class="col-md-8 col-lg-6 col-xxl-3">
                        <div class="card mb-0">
                            <?= $this->renderSection('content') ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="dark-transparent sidebartoggler"></div>
    <!-- Import Js Files -->

    <script src="<?= base_url('assets/libs/jquery/dist/jquery.min.js'); ?>"></script>
    <script src="<?= base_url('assets/js/app.min.js'); ?>"></script>
    <script src="<?= base_url('assets/js/app.init.js'); ?>"></script>
    <script src="<?= base_url('assets/libs/bootstrap/dist/js/bootstrap.bundle.min.js'); ?>"></script>
    <script src="<?= base_url('assets/libs/simplebar/dist/simplebar.min.js'); ?>"></script>
    <script src="<?= base_url('assets/js/iconify-icon.min.js'); ?>"></script>
    <script src="<?= base_url('assets/js/sidebarmenu.js'); ?>"></script>
    <script src="<?= base_url('assets/js/theme.js'); ?>"></script>
    <script src="<?= base_url('assets/js/feather.min.js'); ?>"></script>
    <script src="<?= base_url('assets/libs/apexcharts/dist/apexcharts.min.js'); ?>"></script>
    <!-- Lodash JS -->
    <script src="<?= base_url('assets/js/lodash.js'); ?>"></script>
    <script>
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
    </script>
    <?= $this->renderSection('script') ?>
</body>

</html>