<?php
if (isset($user_info) && $user_info == null) {
    header('Location: /auth/login');
    die();
}
?>

<!DOCTYPE html>
<html lang="en" dir="ltr" data-bs-theme="light" data-color-theme="Blue_Theme">

<head>
    <!-- Required meta tags -->
    <meta charset="UTF-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />

    <!-- Favicon Icon-->
    <link id="favicon" rel="shortcut icon" href="<?= base_url("logo_light.png") ?>" type="image/png">

    <!-- Core CSS -->
    <link rel="stylesheet" href="<?= base_url('assets/css/styles.css') ?>" />
    <!-- Datatable CSS -->
    <link rel="stylesheet" href="<?= base_url('assets/css/new_datatable/datatables.min.css') ?>">
    <!-- Sweet Alert 2 -->
    <link rel="stylesheet" href="<?= base_url('assets/libs/sweetalert2/dist/sweetalert2.min.css') ?>">
    <!-- Select 2 -->
    <link rel="stylesheet" href="<?= base_url('assets/libs/select2/dist/css/select2.min.css') ?>">
    <link rel="stylesheet"
        href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css" />
    <!-- Custom CSS -->
    <?= $this->renderSection('style') ?>
    <title><?= esc($page_title) ?? "Page Title"; ?></title>
</head>

<body>
    <!-- Preloader -->
    <div class="preloader">
        <img src="<?= base_url("logo_dark.png") ?>" alt="loader" class="lds-ripple img-fluid" />
    </div>
    <!-- END Preloader -->

    <!-- Main -->
    <div id="main-wrapper">
        <!-- Sidebar Start -->
        <aside class="left-sidebar with-vertical">
            <!-- Vertical Layout Sidebar -->
            <div>
                <!-- Sidebar scroll-->
                <nav class="sidebar-nav scroll-sidebar" data-simplebar>
                    <!-----------Profile------------------>
                    <div class="user-profile position-relative"
                        style="background: url(<?= base_url('assets/images/backgrounds/user-info.jpg') ?>) no-repeat;">
                        <!-- User profile image -->
                        <div class="profile-img">
                            <img src="<?= profile_image_url($user_info['name'] ?? null) ?>"
                                alt="user" class="w-100 rounded-circle overflow-hidden" />
                        </div>
                        <!-- END User profile image -->

                        <!-- User profile text-->
                        <div class="profile-text hide-menu pt-1 dropdown">
                            <!-- Identity Dropdown Button -->
                            <a href="#" class="dropdown-toggle u-dropdown w-100 text-white d-block position-relative"
                                id="dropdownMenuLink" data-bs-toggle="dropdown" aria-expanded="false">
                                <?= esc($user_info['name']) . "&nbsp;(" . esc($user_info['user_role']) . ")" ?? "Markarn Doe" ?>
                            </a>
                            <!-- END Identity Dropdown Button -->
                            <!-- Dropdown Menu Flip -->
                            <div class="dropdown-menu animated flipInY" aria-labelledby="dropdownMenuLink"
                                data-popper-placement="bottom-start"
                                style="position: absolute; inset: 0px auto auto 0px; margin: 0px; transform: translate(0px, 41px);">
                                <a class="dropdown-item" href="#">
                                    <i data-feather="user" width="24" height="24" stroke-width="2"
                                        class="feather-sm text-info me-1 ms-1" stroke-linecap="round"
                                        stroke-linejoin="round"></i>
                                    </svg>
                                    My Profile</a>
                                <a class="dropdown-item" href="#">
                                    <i data-feather="credit-card" width="24" height="24" stroke-width="2"
                                        class="feather-sm text-info me-1 ms-1" stroke-linecap="round"
                                        stroke-linejoin="round"></i>
                                    My Balance</a>
                                <a class="dropdown-item" href="#">
                                    <i data-feather="mail" width="24" height="24" stroke-width="2"
                                        class="feather-sm text-success me-1 ms-1" stroke-linecap="round"
                                        stroke-linejoin="round"></i>
                                    Inbox</a>
                                <div class="dropdown-divider"></div>
                                <a class="dropdown-item" href="#">
                                    <i data-feather="settings" width="24" height="24" stroke-width="2"
                                        class="feather-sm text-warning me-1 ms-1" stroke-linecap="round"
                                        stroke-linejoin="round"></i>
                                    Account Setting</a>
                                <div class="dropdown-divider"></div>
                                <a class="dropdown-item" href="<?= base_url('auth/logout') ?>">
                                    <i data-feather="log-out" width="24" height="24" stroke-width="2"
                                        class="feather-sm text-danger me-1 ms-1" stroke-linecap="round"
                                        stroke-linejoin="round"></i>
                                    Logout</a>
                            </div>
                            <!-- END Dropdown Menu Flip -->
                        </div>
                        <!-- END User profile text-->
                    </div>
                    <!-----------Profile End------------------>

                    <ul id="sidebarnav">
                        <!-- ---------------------------------- -->
                        <!-- Home -->
                        <!-- ---------------------------------- -->
                        <!-- Administrator -->
                        <?php if (esc($user_info['user_role']) == 'administrator'): ?>
                            <!-- Group Name -->
                            <li class="nav-small-cap">
                                <iconify-icon icon="solar:menu-dots-bold" class="nav-small-cap-icon fs-4"></iconify-icon>
                                <span class="hide-menu">DASHBOARD</span>
                            </li>
                            <!-- END Group Name -->
                            <!-- Group Content -->
                            <li class="sidebar-item">
                                <a class="sidebar-link waves-effect waves-dark sidebar-link <?= (url_is('admin')) ? 'active' : '' ?>"
                                    href="<?= base_url('admin') ?>" aria-expanded="false">
                                    <iconify-icon icon="solar:widget-outline"></iconify-icon>
                                    <span class="hide-menu">Dashboard</span>
                                </a>
                            </li>
                            <!-- Group Content -->

                            <li class="nav-small-cap">
                                <iconify-icon icon="solar:menu-dots-bold" class="nav-small-cap-icon fs-4"></iconify-icon>
                                <span class="hide-menu">MANAJEMEN</span>
                            </li>
                            <li class="sidebar-item">
                                <a class="sidebar-link waves-effect waves-dark sidebar-link"
                                    href="<?= base_url('admin/manage/user') ?>" aria-expanded="false">
                                    <iconify-icon icon="solar:user-outline"></iconify-icon>
                                    <span class="hide-menu">Manajemen User</span>
                                </a>
                            </li>
                            <li class="sidebar-item">
                                <a class="sidebar-link waves-effect waves-dark sidebar-link"
                                    href="<?= base_url('admin/manage/task') ?>" aria-expanded="false">
                                    <iconify-icon icon="solar:home-2-outline"></iconify-icon>
                                    <span class="hide-menu">Manajemen Tugas</span>
                                </a>
                            </li>
                        <?php endif ?>
                        <!-- Verifikator -->
                        <?php if ($user_info['user_role'] == 'verifikator'): ?>
                            <li class="nav-small-cap">
                                <iconify-icon icon="solar:menu-dots-bold" class="nav-small-cap-icon fs-4"></iconify-icon>
                                <span class="hide-menu">TUGAS</span>
                            </li>
                            <li class="sidebar-item">
                                <a class="sidebar-link waves-effect waves-dark sidebar-link"
                                    href="<?= base_url(relativePath: 'verifikator'); ?>" aria-expanded="false"><iconify-icon
                                        icon="solar:widget-outline"></iconify-icon><span class="hide-menu">Dashboard</span></a>
                            </li>
                            <li class="nav-small-cap">
                                <iconify-icon icon="solar:menu-dots-bold" class="nav-small-cap-icon fs-4"></iconify-icon>
                                <span class="hide-menu">LAPORAN</span>
                            </li>
                            <li class="sidebar-item">
                                <a class="sidebar-link waves-effect waves-dark sidebar-link <?= (url_is('verifikator/laporan/rekapitulasi')) ? 'active' : '' ?>"
                                    href="<?= base_url('verifikator/laporan/rekapitulasi'); ?>" aria-expanded="false">
                                    <iconify-icon icon="solar:document-text-outline"></iconify-icon>
                                    <span class="hide-menu">Rekapitulasi</span>
                                </a>
                            </li>
                        <?php endif ?>
                        <!-- END Verifikator -->
                        <!-- Petugas -->
                        <?php if ($user_info['user_role'] == 'operator'): ?>
                            <li class="nav-small-cap">
                                <iconify-icon icon="solar:menu-dots-bold" class="nav-small-cap-icon fs-4"></iconify-icon>
                                <span class="hide-menu">TUGAS</span>
                            </li>
                            <li class="sidebar-item">
                                <a class="sidebar-link waves-effect waves-dark sidebar-link"
                                    href="<?= base_url('operator') ?>" aria-expanded="false"><iconify-icon
                                        icon="solar:widget-outline"></iconify-icon><span hpp class="hide-menu">Tugas
                                        Utama</span></a>
                            </li>

                            <li class="nav-small-cap">
                                <iconify-icon icon="solar:menu-dots-bold" class="nav-small-cap-icon fs-4"></iconify-icon>
                                <span class="hide-menu">REVISI TUGAS</span>
                            </li>
                            <li class="sidebar-item">
                                <a class="sidebar-link" href="<?= base_url('operator/revisi') ?>" aria-expanded="false">
                                    <span class="d-flex">
                                        <iconify-icon icon="solar:link-square-linear" class="fs-6"></iconify-icon>
                                    </span>
                                    <span class="hide-menu">Revisi Tugas
                                        <?php $revCount = session()->get('revision_room_count') ?? (isset($revision_room_count) ? $revision_room_count : 0); ?>
                                        <?php if ($revCount > 0): ?>
                                            <span class="badge bg-danger ms-1"><?= $revCount ?></span>
                                        <?php endif; ?>
                                    </span>
                                </a>
                            </li>
                        <?php endif ?>
                        <!-- END Petugas -->
                    </ul>
                </nav>
                <!-- End Sidebar scroll-->
            </div>
            <!-- END Vertical Layout Sidebar -->
        </aside>
        <!--  END Sidebar Start -->
        <div class="page-wrapper">
            <!--  Header Start -->
            <header class="topbar rounded-0 border-0 bg-primary">
                <div class="with-vertical">
                    <!-- ---------------------------------- -->
                    <!-- Start Vertical Layout Header -->
                    <!-- ---------------------------------- -->
                    <nav class="navbar navbar-expand-lg px-lg-0 px-3 py-0">
                        <!-- Logo & Title -->
                        <div class="d-none d-lg-block">
                            <div class="brand-logo d-flex align-items-center">
                                <a href="<?= base_url(); ?>" class="text-nowrap logo-img d-flex align-items-center">
                                    <b class="logo-icon">
                                        <img src="<?= base_url("logo_light.png") ?>" class="dark-logo me-2"
                                            style="width: 24px" />
                                        <img src="<?= base_url("logo_light.png") ?>" class="light-logo me-2"
                                            style="width: 24px" />
                                    </b>
                                    <span class="logo-text mt-2 pt-1">
                                        <h4 class="dark-logo ps-2 text-white">BIONIC NATURA</h4>
                                        <h4 class="light-logo ps-2 text-white">BIONIC NATURA</h4>
                                    </span>
                                </a>
                            </div>
                        </div>
                        <!-- END Logo & Title -->

                        <!-- Sidebar Menu Toggler Button -->
                        <ul class="navbar-nav">
                            <li class="nav-item">
                                <a class="nav-link nav-icon-hover sidebartoggler" id="headerCollapse"
                                    href="javascript:void(0)">
                                    <iconify-icon icon="solar:list-bold"></iconify-icon>
                                </a>
                            </li>
                        </ul>
                        <!-- END Sidebar Menu Toggler Button -->

                        <!-- XS,SM,MD Logo & Title -->
                        <div class="d-block d-lg-none">
                            <div class="brand-logo d-flex align-items-center justify-content-between">
                                <a href="<?= base_url(); ?>" class="text-nowrap logo-img d-flex align-items-center">
                                    <b class="logo-icon">
                                        <img src="<?= base_url(" logo_light.png") ?>" class="dark-logo me-2"
                                            style="width: 24px" />
                                        <img src="<?= base_url(" logo_light.png") ?>" class="light-logo me-2"
                                            style="width: 24px" />
                                    </b>
                                    <span class="logo-text mt-2 pt-1">
                                        <h4 class="dark-logo ps-2 text-white">BIONIC NATURA</h4>
                                        <h4 class="light-logo ps-2 text-white">BIONIC NATURA</h4>
                                    </span>
                                </a>
                            </div>
                        </div>
                        <!-- END XS,SM,MD Logo & Title -->


                        <!-- Additional Menu Toggler For Mobile Device -->
                        <ul class="navbar-nav flex-row  align-items-center justify-content-center d-flex d-lg-none">
                            <li class="nav-item dropdown">
                                <a class="navbar-toggler nav-link text-white nav-icon-hover border-0"
                                    href="javascript:void(0)" data-bs-toggle="collapse" data-bs-target="#navbarNav"
                                    aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                                    <span class="">
                                        <i class="ti ti-dots fs-7"></i>
                                    </span>
                                </a>
                            </li>
                        </ul>
                        <!-- END Additional Menu Toggler For Mobile Device -->


                        <!-- Additional Menu For Mobile Device -->
                        <div class="collapse navbar-collapse justify-content-end" id="navbarNav">
                            <div class="d-flex align-items-center justify-content-between">
                                <!-- Notification & Inbox -->
                                <ul
                                    class="navbar-nav flex-row  align-items-center justify-content-center d-flex d-lg-none">
                                    <!-- <li class="nav-item dropdown">
                                        <a href="javascript:void(0)"
                                            class="nav-link d-flex d-lg-none align-items-center justify-content-center"
                                            type="button" data-bs-toggle="offcanvas" data-bs-target="#mobilenavbar"
                                            aria-controls="offcanvasWithBothOptions">
                                            <iconify-icon icon="solar:menu-dots-circle-linear"></iconify-icon>
                                        </a>
                                    </li> -->
                                    <li class="nav-item hover-dd dropdown">
                                        <!-- Bell Icon -->
                                        <a class="nav-link nav-icon-hover waves-effect waves-dark"
                                            href="javascript:void(0)" id="drop2" aria-expanded="false">
                                            <iconify-icon icon="solar:bell-bing-line-duotone"></iconify-icon>
                                            <!-- Heartbit Icon -->
                                            <!-- <div class="notify">
                                                <span class="heartbit"></span> <span class="point"></span>
                                            </div> -->
                                        </a>
                                        <!-- END Bell Icon -->

                                        <!-- Expanded Menu For Notification -->
                                        <div class="dropdown-menu py-0 content-dd  dropdown-menu-animate-up overflow-hidden"
                                            aria-labelledby="drop2">
                                            <div class="py-3 px-4 bg-primary">
                                                <div class="mb-0 fs-6 fw-medium text-white">Notifications</div>
                                                <div class="mb-0 fs-2 fw-medium text-white">You have 4 Notifications
                                                </div>
                                            </div>
                                            <div class="message-body" data-simplebar>
                                                <a href="javascript:void(0)"
                                                    class="p-3 d-flex align-items-center dropdown-item gap-3 border-bottom">
                                                    <span
                                                        class="flex-shrink-0 bg-primary-subtle rounded-circle round-40 d-flex align-items-center justify-content-center fs-6 text-primary">
                                                        <iconify-icon icon="solar:widget-3-line-duotone"></iconify-icon>
                                                    </span>
                                                    <div class="w-80 d-inline-block v-middle">
                                                        <div class="d-flex align-items-center justify-content-between">
                                                            <h6 class="mb-1">Luanch Admin</h6>
                                                            <span class="fs-2 text-nowrap d-block text-muted ">9:30
                                                                AM</span>
                                                        </div>
                                                        <span class="fs-2 d-block text-truncate text-muted">Just see the
                                                            my new admin!</span>
                                                    </div>
                                                </a>
                                                <a href="javascript:void(0)"
                                                    class="p-3 d-flex align-items-center dropdown-item gap-3  border-bottom">
                                                    <span
                                                        class="flex-shrink-0 bg-secondary-subtle rounded-circle round-40 d-flex align-items-center justify-content-center fs-6 text-secondary">
                                                        <iconify-icon
                                                            icon="solar:calendar-mark-line-duotone"></iconify-icon>
                                                    </span>
                                                    <div class="w-80 d-inline-block v-middle">
                                                        <div class="d-flex align-items-center justify-content-between">
                                                            <h6 class="mb-1">Event today</h6>
                                                            <span class="fs-2 text-nowrap d-block text-muted ">9:10
                                                                AM</span>
                                                        </div>

                                                        <span class="fs-2 d-block text-truncate text-muted">Just a
                                                            reminder that you have event</span>
                                                    </div>
                                                </a>
                                                <a href="javascript:void(0)"
                                                    class="p-3 d-flex align-items-center dropdown-item gap-3  border-bottom">
                                                    <span
                                                        class="flex-shrink-0 bg-danger-subtle rounded-circle round-40 d-flex align-items-center justify-content-center fs-6 text-danger">
                                                        <iconify-icon
                                                            icon="solar:settings-minimalistic-line-duotone"></iconify-icon>
                                                    </span>
                                                    <div class="w-80 d-inline-block v-middle">
                                                        <div class="d-flex align-items-center justify-content-between">
                                                            <h6 class="mb-1">Settings</h6>
                                                            <span class="fs-2 text-nowrap d-block text-muted ">9:08
                                                                AM</span>
                                                        </div>
                                                        <span class="fs-2 d-block text-truncate text-muted">You can
                                                            customize this template as you want</span>
                                                    </div>
                                                </a>
                                                <a href="javascript:void(0)"
                                                    class="p-3 d-flex align-items-center dropdown-item gap-3  border-bottom">
                                                    <span
                                                        class="flex-shrink-0 bg-warning-subtle rounded-circle round-40 d-flex align-items-center justify-content-center fs-6 text-warning">
                                                        <iconify-icon
                                                            icon="solar:link-circle-line-duotone"></iconify-icon>
                                                    </span>
                                                    <div class="w-80 d-inline-block v-middle">
                                                        <div class="d-flex align-items-center justify-content-between">
                                                            <h6 class="mb-1">Luanch Admin</h6>
                                                            <span class="fs-2 text-nowrap d-block text-muted ">9:30
                                                                AM</span>
                                                        </div>
                                                        <span class="fs-2 d-block text-truncate text-muted">Just see the
                                                            my new admin!</span>
                                                    </div>
                                                </a>
                                                <a href="javascript:void(0)"
                                                    class="p-3 d-flex align-items-center dropdown-item gap-3  border-bottom">
                                                    <span
                                                        class="flex-shrink-0 bg-success-subtle rounded-circle round-40 d-flex align-items-center justify-content-center">
                                                        <i data-feather="calendar"
                                                            class="feather-sm fill-white text-success"></i>
                                                    </span>
                                                    <div class="w-80 d-inline-block v-middle">
                                                        <div class="d-flex align-items-center justify-content-between">
                                                            <h6 class="mb-1">Event today</h6>
                                                            <span class="fs-2 text-nowrap d-block text-muted ">9:10
                                                                AM</span>
                                                        </div>
                                                        <span class="fs-2 d-block text-truncate text-muted">Just a
                                                            reminder that you have event</span>
                                                    </div>
                                                </a>
                                                <a href="javascript:void(0)"
                                                    class="p-3 d-flex align-items-center dropdown-item gap-3  border-bottom">
                                                    <span
                                                        class="flex-shrink-0 bg-info-subtle rounded-circle round-40 d-flex align-items-center justify-content-center">
                                                        <i data-feather="settings"
                                                            class="feather-sm fill-white text-info"></i>
                                                    </span>
                                                    <div class="w-80 d-inline-block v-middle">
                                                        <div class="d-flex align-items-center justify-content-between">
                                                            <h6 class="mb-1">Settings</h6>
                                                            <span class="fs-2 text-nowrap d-block text-muted ">9:08
                                                                AM</span>
                                                        </div>
                                                        <span class="fs-2 d-block text-truncate text-muted">You can
                                                            customize this template as you want</span>
                                                    </div>
                                                </a>
                                            </div>
                                            <div class="p-3">
                                                <a class="d-flex btn btn-primary rounded align-items-center justify-content-center gap-2"
                                                    href="javascript:void(0);">
                                                    <span>Check all Notifications</span>
                                                    <iconify-icon icon="solar:alt-arrow-right-outline"
                                                        class="iconify-sm"></iconify-icon>
                                                </a>
                                            </div>
                                        </div>
                                        <!-- END Expanded Menu For Notification -->
                                    </li>
                                    <li class="nav-item hover-dd dropdown">
                                        <!-- Inbox Icon -->
                                        <a class="nav-link nav-icon-hover" href="javascript:void(0)" id="drop2"
                                            aria-expanded="false">
                                            <iconify-icon icon="solar:inbox-line-line-duotone"></iconify-icon>
                                            <div class="notify">
                                                <span class="heartbit"></span> <span class="point"></span>
                                            </div>
                                        </a>
                                        <!-- END Inbox Icon -->

                                        <!-- Inbox Message Extended Menu -->
                                        <div class="dropdown-menu py-0 content-dd dropdown-menu-animate-up overflow-hidden"
                                            aria-labelledby="drop2">
                                            <div class="py-3 px-4 bg-secondary">
                                                <div class="mb-0 fs-6 fw-medium text-white">Messages</div>
                                                <div class="mb-0 fs-2 fw-medium text-white">You have 5 new messages
                                                </div>
                                            </div>
                                            <div class="message-body" data-simplebar>
                                                <a href="javascript:void(0)"
                                                    class="p-3 d-flex align-items-center dropdown-item gap-3 border-bottom">
                                                    <span class="user-img position-relative d-inline-block">
                                                        <img src="<?= base_url('assets/images/profile/user-1.jpg') ?>"
                                                            alt="user" class="rounded-circle w-100 round-40" />
                                                        <span
                                                            class="profile-status bg-success position-absolute rounded-circle"></span>
                                                    </span>
                                                    <div class="w-80 d-inline-block v-middle">
                                                        <div class="d-flex align-items-center justify-content-between">
                                                            <h6 class="mb-1">Mathew Anderson</h6>
                                                            <span class="fs-2 text-nowrap d-block text-muted">9:30
                                                                AM</span>
                                                        </div>
                                                        <span class="fs-2 d-block text-truncate text-muted">Just see the
                                                            my new admin!</span>
                                                    </div>
                                                </a>
                                                <a href="javascript:void(0)"
                                                    class="p-3 d-flex align-items-center dropdown-item gap-3 border-bottom">
                                                    <span class="user-img position-relative d-inline-block">
                                                        <img src="<?= base_url('assets/images/profile/user-2.jpg') ?>"
                                                            alt="user" class="rounded-circle w-100 round-40" />
                                                        <span
                                                            class="profile-status bg-success position-absolute rounded-circle"></span>
                                                    </span>
                                                    <div class="w-80 d-inline-block v-middle">
                                                        <div class="d-flex align-items-center justify-content-between">
                                                            <h6 class="mb-1">Bianca Anderson</h6>
                                                            <span class="fs-2 text-nowrap d-block text-muted">9:10
                                                                AM</span>
                                                        </div>

                                                        <span class="fs-2 d-block text-truncate text-muted">Just a
                                                            reminder that you have event</span>
                                                    </div>
                                                </a>
                                                <a href="javascript:void(0)"
                                                    class="p-3 d-flex align-items-center dropdown-item gap-3 border-bottom">
                                                    <span class="user-img position-relative d-inline-block">
                                                        <img src="<?= base_url('assets/images/profile/user-3.jpg') ?>"
                                                            alt="user" class="rounded-circle w-100 round-40" />
                                                        <span
                                                            class="profile-status bg-success position-absolute rounded-circle"></span>
                                                    </span>
                                                    <div class="w-80 d-inline-block v-middle">
                                                        <div class="d-flex align-items-center justify-content-between">
                                                            <h6 class="mb-1">Andrew Johnson</h6>
                                                            <span class="fs-2 text-nowrap d-block text-muted">9:08
                                                                AM</span>
                                                        </div>
                                                        <span class="fs-2 d-block text-truncate text-muted">You can
                                                            customize this template as you want</span>
                                                    </div>
                                                </a>
                                                <a href="javascript:void(0)"
                                                    class="p-3 d-flex align-items-center dropdown-item gap-3 border-bottom">
                                                    <span class="user-img position-relative d-inline-block">
                                                        <img src="<?= base_url('assets/images/profile/user-4.jpg') ?>"
                                                            alt="user" class="rounded-circle w-100 round-40" />
                                                        <span
                                                            class="profile-status bg-success position-absolute rounded-circle"></span>
                                                    </span>
                                                    <div class="w-80 d-inline-block v-middle">
                                                        <div class="d-flex align-items-center justify-content-between">
                                                            <h6 class="mb-1">Mark Strokes</h6>
                                                            <span class="fs-2 text-nowrap d-block text-muted">9:30
                                                                AM</span>
                                                        </div>
                                                        <span class="fs-2 d-block text-truncate text-muted">Just see the
                                                            my new admin!</span>
                                                    </div>
                                                </a>
                                                <a href="javascript:void(0)"
                                                    class="p-3 d-flex align-items-center dropdown-item gap-3 border-bottom">
                                                    <span class="user-img position-relative d-inline-block">
                                                        <img src="<?= base_url('assets/images/profile/user-5.jpg') ?>"
                                                            alt="user" class="rounded-circle w-100 round-40" />
                                                        <span
                                                            class="profile-status bg-success position-absolute rounded-circle"></span>
                                                    </span>
                                                    <div class="w-80 d-inline-block v-middle">
                                                        <div class="d-flex align-items-center justify-content-between">
                                                            <h6 class="mb-1">Mark, Stoinus & Rishvi..</h6>
                                                            <span class="fs-2 text-nowrap d-block text-muted">9:10
                                                                AM</span>
                                                        </div>
                                                        <span class="fs-2 d-block text-truncate text-muted">Just a
                                                            reminder that you have event</span>
                                                    </div>
                                                </a>
                                                <a href="javascript:void(0)"
                                                    class="p-3 d-flex align-items-center dropdown-item gap-3 border-bottom">
                                                    <span class="user-img position-relative d-inline-block">
                                                        <img src="<?= base_url('assets/images/profile/user-6.jpg') ?>"
                                                            alt="user" class="rounded-circle w-100 round-40" />
                                                        <span
                                                            class="profile-status bg-success position-absolute rounded-circle"></span>
                                                    </span>
                                                    <div class="w-80 d-inline-block v-middle">
                                                        <div class="d-flex align-items-center justify-content-between">
                                                            <h6 class="mb-1">Eliga Rush</h6>
                                                            <span class="fs-2 text-nowrap d-block text-muted">9:08
                                                                AM</span>
                                                        </div>
                                                        <span class="fs-2 d-block text-truncate text-muted">You can
                                                            customize this template as you want</span>
                                                    </div>
                                                </a>
                                            </div>
                                            <div class="p-3">
                                                <a class="d-flex btn btn-secondary rounded align-items-center justify-content-center gap-2"
                                                    href="javascript:void(0);">
                                                    <span>Check all Messages</span>
                                                    <iconify-icon icon="solar:alt-arrow-right-outline"
                                                        class="iconify-sm"></iconify-icon>
                                                </a>
                                            </div>
                                        </div>
                                        <!-- END Inbox Message Extended Menu -->
                                    </li>
                                </ul>
                                <!-- END Notification & Inbox -->

                                <!-- Additional Menu For Desktop Device -->
                                <ul class="navbar-nav flex-row ms-auto align-items-center justify-content-center">
                                    <!-- Theme Toggler -->
                                    <li class="nav-item">
                                        <a class="nav-link nav-icon-hover moon dark-layout" href="javascript:void(0)">
                                            <iconify-icon icon="solar:moon-line-duotone" class="moon"></iconify-icon>
                                        </a>
                                        <a class="nav-link nav-icon-hover sun light-layout" href="javascript:void(0)"
                                            style="display: none;">
                                            <iconify-icon icon="solar:sun-2-line-duotone" class="sun"></iconify-icon>
                                        </a>
                                    </li>
                                    <!-- END Theme Toggler -->

                                    <!-- Notification & Messages -->
                                    <!-- Notification -->
                                    <li class="nav-item hover-dd dropdown  d-none d-lg-block">
                                        <a class="nav-link nav-icon-hover waves-effect waves-dark"
                                            href="javascript:void(0)" id="drop2" aria-expanded="false">
                                            <iconify-icon icon="solar:bell-bing-line-duotone"></iconify-icon>
                                            <!-- Heartbit Icon -->
                                            <!-- <div class="notify">
                                                <span class="heartbit"></span> <span class="point"></span>
                                            </div> -->
                                        </a>
                                        <div class="dropdown-menu py-0 content-dd  dropdown-menu-animate-up overflow-hidden dropdown-menu-end"
                                            aria-labelledby="drop2">
                                            <div class="py-3 px-4 bg-primary">
                                                <div class="mb-0 fs-6 fw-medium text-white">Notifications</div>
                                                <div class="mb-0 fs-2 fw-medium text-white">You have 4 Notifications
                                                </div>
                                            </div>
                                            <div class="message-body" data-simplebar>
                                                <a href="javascript:void(0)"
                                                    class="p-3 d-flex align-items-center  dropdown-item gap-3   border-bottom">
                                                    <span
                                                        class="flex-shrink-0 bg-primary-subtle rounded-circle round-40 d-flex align-items-center justify-content-center fs-6 text-primary">
                                                        <iconify-icon icon="solar:widget-3-line-duotone"></iconify-icon>
                                                    </span>
                                                    <div class="w-80 d-inline-block v-middle">
                                                        <div class="d-flex align-items-center justify-content-between">
                                                            <h6 class="mb-1">Launch Admin</h6>
                                                            <span class="fs-2 text-nowrap d-block text-muted ">9:30
                                                                AM</span>
                                                        </div>
                                                        <span class="fs-2 d-block text-truncate text-muted">Just see the
                                                            my new admin!</span>
                                                    </div>
                                                </a>
                                                <a href="javascript:void(0)"
                                                    class="p-3 d-flex align-items-center dropdown-item gap-3  border-bottom">
                                                    <span
                                                        class="flex-shrink-0 bg-secondary-subtle rounded-circle round-40 d-flex align-items-center justify-content-center fs-6 text-secondary">
                                                        <iconify-icon
                                                            icon="solar:calendar-mark-line-duotone"></iconify-icon>
                                                    </span>
                                                    <div class="w-80 d-inline-block v-middle">
                                                        <div class="d-flex align-items-center justify-content-between">
                                                            <h6 class="mb-1">Event today</h6>
                                                            <span class="fs-2 text-nowrap d-block text-muted ">9:10
                                                                AM</span>
                                                        </div>

                                                        <span class="fs-2 d-block text-truncate text-muted">Just a
                                                            reminder that you have event</span>
                                                    </div>
                                                </a>
                                                <a href="javascript:void(0)"
                                                    class="p-3 d-flex align-items-center dropdown-item gap-3  border-bottom">
                                                    <span
                                                        class="flex-shrink-0 bg-danger-subtle rounded-circle round-40 d-flex align-items-center justify-content-center fs-6 text-danger">
                                                        <iconify-icon
                                                            icon="solar:settings-minimalistic-line-duotone"></iconify-icon>
                                                    </span>
                                                    <div class="w-80 d-inline-block v-middle">
                                                        <div class="d-flex align-items-center justify-content-between">
                                                            <h6 class="mb-1">Settings</h6>
                                                            <span class="fs-2 text-nowrap d-block text-muted ">9:08
                                                                AM</span>
                                                        </div>
                                                        <span class="fs-2 d-block text-truncate text-muted">You can
                                                            customize this template as you want</span>
                                                    </div>
                                                </a>
                                                <a href="javascript:void(0)"
                                                    class="p-3 d-flex align-items-center dropdown-item gap-3  border-bottom">
                                                    <span
                                                        class="flex-shrink-0 bg-warning-subtle rounded-circle round-40 d-flex align-items-center justify-content-center fs-6 text-warning">
                                                        <iconify-icon
                                                            icon="solar:link-circle-line-duotone"></iconify-icon>
                                                    </span>
                                                    <div class="w-80 d-inline-block v-middle">
                                                        <div class="d-flex align-items-center justify-content-between">
                                                            <h6 class="mb-1">Launch Admin</h6>
                                                            <span class="fs-2 text-nowrap d-block text-muted ">9:30
                                                                AM</span>
                                                        </div>
                                                        <span class="fs-2 d-block text-truncate text-muted">Just see the
                                                            my new admin!</span>
                                                    </div>
                                                </a>
                                                <a href="javascript:void(0)"
                                                    class="p-3 d-flex align-items-center dropdown-item gap-3  border-bottom">
                                                    <span
                                                        class="flex-shrink-0 bg-success-subtle rounded-circle round-40 d-flex align-items-center justify-content-center">
                                                        <i data-feather="calendar"
                                                            class="feather-sm fill-white text-success"></i>
                                                    </span>
                                                    <div class="w-80 d-inline-block v-middle">
                                                        <div class="d-flex align-items-center justify-content-between">
                                                            <h6 class="mb-1">Event today</h6>
                                                            <span class="fs-2 text-nowrap d-block text-muted ">9:10
                                                                AM</span>
                                                        </div>
                                                        <span class="fs-2 d-block text-truncate text-muted">Just a
                                                            reminder that you have event</span>
                                                    </div>
                                                </a>
                                                <a href="javascript:void(0)"
                                                    class="p-3 d-flex align-items-center dropdown-item gap-3  border-bottom">
                                                    <span
                                                        class="flex-shrink-0 bg-info-subtle rounded-circle round-40 d-flex align-items-center justify-content-center">
                                                        <i data-feather="settings"
                                                            class="feather-sm fill-white text-info"></i>
                                                    </span>
                                                    <div class="w-80 d-inline-block v-middle">
                                                        <div class="d-flex align-items-center justify-content-between">
                                                            <h6 class="mb-1">Settings</h6>
                                                            <span class="fs-2 text-nowrap d-block text-muted ">9:08
                                                                AM</span>
                                                        </div>
                                                        <span class="fs-2 d-block text-truncate text-muted">You can
                                                            customize this template as you want</span>
                                                    </div>
                                                </a>
                                            </div>
                                            <div class="p-3">
                                                <a class="d-flex btn btn-primary rounded align-items-center justify-content-center gap-2"
                                                    href="javascript:void(0);">
                                                    <span>Check all Notifications</span>
                                                    <iconify-icon icon="solar:alt-arrow-right-outline"
                                                        class="iconify-sm"></iconify-icon>
                                                </a>
                                            </div>
                                        </div>
                                    </li>
                                    <!-- END Notification -->

                                    <!-- Messages -->
                                    <li class="nav-item hover-dd dropdown  d-none d-lg-block">
                                        <a class="nav-link nav-icon-hover" href="javascript:void(0)" id="drop2"
                                            aria-expanded="false">
                                            <iconify-icon icon="solar:inbox-line-line-duotone"></iconify-icon>
                                            <!-- Heartbit Icon -->
                                            <!-- <div class="notify">
                                                <span class="heartbit"></span> <span class="point"></span>
                                            </div> -->
                                        </a>
                                        <div class="dropdown-menu py-0 content-dd dropdown-menu-animate-up dropdown-menu-end overflow-hidden"
                                            aria-labelledby="drop2">
                                            <div class="py-3 px-4 bg-secondary">
                                                <div class="mb-0 fs-6 fw-medium text-white">Messages</div>
                                                <div class="mb-0 fs-2 fw-medium text-white">You have 5 new messages
                                                </div>
                                            </div>
                                            <div class="message-body" data-simplebar>
                                                <a href="javascript:void(0)"
                                                    class="p-3 d-flex align-items-center dropdown-item gap-3 border-bottom">
                                                    <span class="user-img position-relative d-inline-block">
                                                        <img src="<?= base_url('assets/images/profile/user-1.jpg') ?>"
                                                            alt="user" class="rounded-circle w-100 round-40" />
                                                        <span
                                                            class="profile-status bg-success position-absolute rounded-circle"></span>
                                                    </span>
                                                    <div class="w-80 d-inline-block v-middle">
                                                        <div class="d-flex align-items-center justify-content-between">
                                                            <h6 class="mb-1">Mathew Anderson</h6>
                                                            <span class="fs-2 text-nowrap d-block text-muted">9:30
                                                                AM</span>
                                                        </div>
                                                        <span class="fs-2 d-block text-truncate text-muted">Just see the
                                                            my new admin!</span>
                                                    </div>
                                                </a>
                                                <a href="javascript:void(0)"
                                                    class="p-3 d-flex align-items-center dropdown-item gap-3 border-bottom">
                                                    <span class="user-img position-relative d-inline-block">
                                                        <img src="<?= base_url('assets/images/profile/user-2.jpg') ?>"
                                                            alt="user" class="rounded-circle w-100 round-40" />
                                                        <span
                                                            class="profile-status bg-success position-absolute rounded-circle"></span>
                                                    </span>
                                                    <div class="w-80 d-inline-block v-middle">
                                                        <div class="d-flex align-items-center justify-content-between">
                                                            <h6 class="mb-1">Bianca Anderson</h6>
                                                            <span class="fs-2 text-nowrap d-block text-muted">9:10
                                                                AM</span>
                                                        </div>

                                                        <span class="fs-2 d-block text-truncate text-muted">Just a
                                                            reminder that you have event</span>
                                                    </div>
                                                </a>
                                                <a href="javascript:void(0)"
                                                    class="p-3 d-flex align-items-center dropdown-item gap-3 border-bottom">
                                                    <span class="user-img position-relative d-inline-block">
                                                        <img src="<?= base_url('assets/images/profile/user-3.jpg') ?>"
                                                            alt="user" class="rounded-circle w-100 round-40" />
                                                        <span
                                                            class="profile-status bg-success position-absolute rounded-circle"></span>
                                                    </span>
                                                    <div class="w-80 d-inline-block v-middle">
                                                        <div class="d-flex align-items-center justify-content-between">
                                                            <h6 class="mb-1">Andrew Johnson</h6>
                                                            <span class="fs-2 text-nowrap d-block text-muted">9:08
                                                                AM</span>
                                                        </div>
                                                        <span class="fs-2 d-block text-truncate text-muted">You can
                                                            customize this template as you want</span>
                                                    </div>
                                                </a>
                                                <a href="javascript:void(0)"
                                                    class="p-3 d-flex align-items-center dropdown-item gap-3 border-bottom">
                                                    <span class="user-img position-relative d-inline-block">
                                                        <img src="<?= base_url('assets/images/profile/user-4.jpg') ?>"
                                                            alt="user" class="rounded-circle w-100 round-40" />
                                                        <span
                                                            class="profile-status bg-success position-absolute rounded-circle"></span>
                                                    </span>
                                                    <div class="w-80 d-inline-block v-middle">
                                                        <div class="d-flex align-items-center justify-content-between">
                                                            <h6 class="mb-1">Mark Strokes</h6>
                                                            <span class="fs-2 text-nowrap d-block text-muted">9:30
                                                                AM</span>
                                                        </div>
                                                        <span class="fs-2 d-block text-truncate text-muted">Just see the
                                                            my new admin!</span>
                                                    </div>
                                                </a>
                                                <a href="javascript:void(0)"
                                                    class="p-3 d-flex align-items-center dropdown-item gap-3 border-bottom">
                                                    <span class="user-img position-relative d-inline-block">
                                                        <img src="<?= base_url('assets/images/profile/user-5.jpg') ?>"
                                                            alt="user" class="rounded-circle w-100 round-40" />
                                                        <span
                                                            class="profile-status bg-success position-absolute rounded-circle"></span>
                                                    </span>
                                                    <div class="w-80 d-inline-block v-middle">
                                                        <div class="d-flex align-items-center justify-content-between">
                                                            <h6 class="mb-1">Mark, Stoinus & Rishvi..</h6>
                                                            <span class="fs-2 text-nowrap d-block text-muted">9:10
                                                                AM</span>
                                                        </div>
                                                        <span class="fs-2 d-block text-truncate text-muted">Just a
                                                            reminder that you have event</span>
                                                    </div>
                                                </a>
                                                <a href="javascript:void(0)"
                                                    class="p-3 d-flex align-items-center dropdown-item gap-3 border-bottom">
                                                    <span class="user-img position-relative d-inline-block">
                                                        <img src="<?= base_url('assets/images/profile/user-6.jpg') ?>"
                                                            alt="user" class="rounded-circle w-100 round-40" />
                                                        <span
                                                            class="profile-status bg-success position-absolute rounded-circle"></span>
                                                    </span>
                                                    <div class="w-80 d-inline-block v-middle">
                                                        <div class="d-flex align-items-center justify-content-between">
                                                            <h6 class="mb-1">Eliga Rush</h6>
                                                            <span class="fs-2 text-nowrap d-block text-muted">9:08
                                                                AM</span>
                                                        </div>
                                                        <span class="fs-2 d-block text-truncate text-muted">You can
                                                            customize this template as you want</span>
                                                    </div>
                                                </a>
                                            </div>
                                            <div class="p-3">
                                                <a class="d-flex btn btn-secondary rounded align-items-center justify-content-center gap-2"
                                                    href="javascript:void(0);">
                                                    <span>Check all Messages</span>
                                                    <iconify-icon icon="solar:alt-arrow-right-outline"
                                                        class="iconify-sm"></iconify-icon>
                                                </a>
                                            </div>
                                        </div>
                                    </li>
                                    <!-- END Messages -->
                                    <!-- End Notification & Messages -->

                                    <!-- ------------------------------- -->
                                    <!-- start profile Dropdown -->
                                    <!-- ------------------------------- -->
                                    <li class="nav-item hover-dd dropdown">
                                        <!-- Profile Icon -->
                                        <a class="nav-link nav-icon-hover" href="javascript:void(0)" id="drop2"
                                            aria-expanded="false">
                                            <img src="<?= base_url('assets/images/profile/user-1.jpg') ?>" alt="user"
                                                class="profile-pic rounded-circle round-30" />
                                        </a>
                                        <!-- END Profile Icon -->

                                        <div class="dropdown-menu pt-0 content-dd overflow-hidden pt-0 dropdown-menu-end user-dd"
                                            aria-labelledby="drop2">
                                            <div class="profile-dropdown position-relative" data-simplebar>
                                                <div class=" py-3 border-bottom">
                                                    <!-- Identity Description -->
                                                    <div class="d-flex align-items-center px-3">
                                                        <img src="<?= base_url('assets/images/profile/user-1.jpg') ?>"
                                                            class="rounded-circle round-50" alt="" />
                                                        <div class="ms-3">
                                                            <h5 class="mb-1 fs-4">
                                                                <?= esc($user_info['name']) ?? 'Markarn Doe' ?>
                                                            </h5>
                                                            <p class="mb-0 fs-2 d-flex align-items-center">
                                                                <?= esc($user_info['user_role']) ?>
                                                            </p>
                                                        </div>
                                                    </div>
                                                    <!-- END Identity Description -->
                                                </div>
                                                <!-- Profile Dropdown -->
                                                <div class="message-body pb-3">
                                                    <!-- Top Area Profile Dropdown -->
                                                    <div class="px-3 pt-3">
                                                        <!-- My Profile -->
                                                        <!-- <div class="h6 mb-0 dropdown-item py-8 px-3 rounded-2 link">
                                                            <a href="#" class=" d-flex  align-items-center ">
                                                                My Profile
                                                            </a>
                                                        </div> -->
                                                        <!-- END My Profile -->

                                                        <!-- My Projects -->
                                                        <!-- <div class="h6 mb-0 dropdown-item py-8 px-3 rounded-2 link">
                                                            <a href="#" class=" d-flex  align-items-center ">
                                                                My Projects
                                                            </a>
                                                        </div> -->
                                                        <!-- END My Projects -->

                                                        <!-- Inbox -->
                                                        <div class="h6 mb-0 dropdown-item py-8 px-3 rounded-2 link">
                                                            <a href="#" class=" d-flex  align-items-center ">
                                                                Inbox
                                                            </a>
                                                        </div>
                                                        <!-- END Inbox -->
                                                    </div>
                                                    <!-- END Top Area Profile Dropdown -->
                                                    <hr>
                                                    <!-- Bottom Area Profile Dropdown -->
                                                    <div class="px-3">
                                                        <div
                                                            class="py-8 px-3 d-flex justify-content-between dropdown-item align-items-center h6 mb-0  rounded-2 link">
                                                            <a href="#" class="">
                                                                Mode
                                                            </a>
                                                            <!-- Theme Toggler -->
                                                            <div>
                                                                <a class="moon dark-layout" href="javascript:void(0)">
                                                                    <iconify-icon icon="solar:moon-line-duotone"
                                                                        class="moon"></iconify-icon>
                                                                </a>
                                                                <a class="sun light-layout" href="javascript:void(0)"
                                                                    style="display: none;">
                                                                    <iconify-icon icon="solar:sun-2-line-duotone"
                                                                        class="sun"></iconify-icon>
                                                                </a>
                                                            </div>
                                                            <!-- END Theme Toggler -->
                                                        </div>
                                                        <!-- Account Settings -->
                                                        <!-- <div class="h6 mb-0 dropdown-item py-8 px-3 rounded-2 link">
                                                            <a href="#" class=" d-flex  align-items-center  ">
                                                                Account Settings
                                                            </a>
                                                        </div> -->
                                                        <!-- END Account Settings -->
                                                        <!-- Sign Out -->
                                                        <div class="h6 mb-0 dropdown-item py-8 px-3 rounded-2 link">
                                                            <a href="<?= base_url('auth/logout/'); ?>"
                                                                class=" d-flex  align-items-center ">
                                                                Sign Out
                                                            </a>
                                                        </div>
                                                        <!-- END Sign Out -->
                                                    </div>
                                                    <!-- END Bottom Area Profile Dropdown -->
                                                </div>
                                                <!-- END Profile Dropdown -->
                                            </div>
                                        </div>
                                    </li>
                                    <!-- ------------------------------- -->
                                    <!-- end profile Dropdown -->
                                    <!-- ------------------------------- -->
                                </ul>
                                <!-- END Additional Menu For Desktop Device -->
                            </div>
                        </div>
                        <!-- END Additional Menu For Mobile Device -->
                    </nav>
                    <!-- ---------------------------------- -->
                    <!-- End Vertical Layout Header -->
                    <!-- ---------------------------------- -->

                    <!-- ------------------------------- -->
                    <!-- apps Dropdown in Small screen -->
                    <!-- ------------------------------- -->
                    <!--  Mobile Offcanvas Menu-->
                    <!-- <div class="offcanvas offcanvas-start" data-bs-scroll="true" tabindex="-1" id="mobilenavbar">
                        <nav class="sidebar-nav scroll-sidebar">
                            <div class="offcanvas-header justify-content-between">
                                <a href="<.?= base_url(); ?>" class="text-nowrap logo-img d-block">
                                    <b class="logo-icon">
                                        <img src="<.?= base_url('assets/images/logos/logo-icon.svg'); ?>" alt="homepage">
                                    </b>
                                    <span class="logo-text">
                                        <img src="<.?= base_url('assets/images/logos/logo-text.svg'); ?>" alt="homepage"
                                            class="dark-logo ps-2">
                                        <img src="<.?= base_url('assets/images/logos/logo-light-text.svg'); ?>"
                                            class="light-logo ps-2" alt="homepage">
                                    </span>
                                </a>
                                <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Close"></button>
                            </div>
                            <div class="offcanvas-body" data-simplebar="" data-simplebar
                                style="height: calc(100vh - 80px)">
                                <ul id="sidebarnav">
                                    <li class="sidebar-item">
                                        <a class="sidebar-link has-arrow px-1" href="javascript:void(0)"
                                            aria-expanded="false">
                                            <span class="d-flex">
                                                <iconify-icon icon="solar:shield-plus-outline"
                                                    class="fs-6"></iconify-icon>
                                            </span>
                                            <span class="hide-menu">Apps</span>
                                        </a>
                                        <ul aria-expanded="false" class="collapse first-level my-3">
                                            <li class="sidebar-item py-2">
                                                <a href="#" class="d-flex align-items-center position-relative ">
                                                    <div
                                                        class="bg-primary-subtle rounded-circle round-40 me-3 p-6 d-flex align-items-center justify-content-center">
                                                        <iconify-icon icon="solar:chat-line-linear"
                                                            class="text-primary fs-5"></iconify-icon>
                                                    </div>
                                                    <div class="d-inline-block ">
                                                        <h6 class="mb-0 ">Chat Application</h6>
                                                        <span class="fs-3 d-block text-muted">New messages
                                                            arrived</span>
                                                    </div>
                                                </a>
                                            </li>
                                            <li class="sidebar-item py-2">
                                                <a href="#" class="d-flex align-items-center position-relative">
                                                    <div
                                                        class="bg-secondary-subtle rounded-circle round-40 me-3 p-6 d-flex align-items-center justify-content-center">
                                                        <iconify-icon icon="solar:bill-list-linear"
                                                            class="text-secondary fs-5"></iconify-icon>
                                                    </div>
                                                    <div class="d-inline-block">
                                                        <h6 class="mb-0">Invoice App</h6>
                                                        <span class="fs-3 d-block text-muted">Get latest invoice</span>
                                                    </div>
                                                </a>
                                            </li>
                                            <li class="sidebar-item py-2">
                                                <a href="#" class="d-flex align-items-center position-relative">
                                                    <div
                                                        class="bg-success-subtle rounded-circle round-40 me-3 p-6 d-flex align-items-center justify-content-center">
                                                        <iconify-icon icon="solar:bedside-table-2-linear"
                                                            class="text-success fs-5"></iconify-icon>
                                                    </div>
                                                    <div class="d-inline-block">
                                                        <h6 class="mb-0">Contact Application</h6>
                                                        <span class="fs-3 d-block text-muted">2 Unsaved Contacts</span>
                                                    </div>
                                                </a>
                                            </li>
                                            <li class="sidebar-item py-2">
                                                <a href="#" class="d-flex align-items-center position-relative">
                                                    <div
                                                        class="bg-warning-subtle rounded-circle round-40 me-3 p-6 d-flex align-items-center justify-content-center">
                                                        <iconify-icon icon="solar:letter-unread-linear"
                                                            class="text-warning fs-5"></iconify-icon>
                                                    </div>
                                                    <div class="d-inline-block">
                                                        <h6 class="mb-0">Email App</h6>
                                                        <span class="fs-3 d-block text-muted">Get new emails</span>
                                                    </div>
                                                </a>
                                            </li>
                                            <li class="sidebar-item py-2">
                                                <a href="#" class="d-flex align-items-center position-relative">
                                                    <div
                                                        class="bg-danger-subtle rounded-circle round-40 me-3 p-6 d-flex align-items-center justify-content-center">
                                                        <iconify-icon icon="solar:cart-large-2-linear"
                                                            class="text-danger fs-5"></iconify-icon>
                                                    </div>
                                                    <div class="d-inline-block">
                                                        <h6 class="mb-0">User Profile</h6>
                                                        <span class="fs-3 d-block text-muted">learn more
                                                            information</span>
                                                    </div>
                                                </a>
                                            </li>
                                            <li class="sidebar-item py-2">
                                                <a href="#" class="d-flex align-items-center position-relative">
                                                    <div
                                                        class="bg-primary-subtle rounded-circle round-40 me-3 p-6 d-flex align-items-center justify-content-center">
                                                        <iconify-icon icon="solar:calendar-linear"
                                                            class="text-primary fs-5"></iconify-icon>
                                                    </div>
                                                    <div class="d-inline-block">
                                                        <h6 class="mb-0">Calendar App</h6>
                                                        <span class="fs-3 d-block text-muted">Get dates</span>
                                                    </div>
                                                </a>
                                            </li>
                                            <li class="sidebar-item py-2">
                                                <a href="#" class="d-flex align-items-center position-relative">
                                                    <div
                                                        class="bg-secondary-subtle rounded-circle round-40 me-3 p-6 d-flex align-items-center justify-content-center">
                                                        <iconify-icon icon="solar:bedside-table-linear"
                                                            class="text-secondary fs-5"></iconify-icon>
                                                    </div>
                                                    <div class="d-inline-block">
                                                        <h6 class="mb-0">Contact List Table</h6>
                                                        <span class="fs-3 d-block text-muted">Add new contact</span>
                                                    </div>
                                                </a>
                                            </li>
                                            <li class="sidebar-item py-2">
                                                <a href="#" class="d-flex align-items-center position-relative">
                                                    <div
                                                        class="bg-success-subtle rounded-circle round-40 me-3 p-6 d-flex align-items-center justify-content-center">
                                                        <iconify-icon icon="solar:palette-linear"
                                                            class="text-success fs-5"></iconify-icon>
                                                    </div>
                                                    <div class="d-inline-block">
                                                        <h6 class="mb-0">Notes Application</h6>
                                                        <span class="fs-3 d-block text-muted">To-do and Daily
                                                            tasks</span>
                                                    </div>
                                                </a>
                                            </li>
                                            <ul class="px-8 mt-7 mb-4">
                                                <li class="sidebar-item mb-3">
                                                    <h5 class="fs-5 fw-semibold">Quick Links</h5>
                                                </li>
                                                <li class="sidebar-item py-2">
                                                    <a class="fs-3" href="#">Pricing
                                                        Page</a>
                                                </li>
                                                <li class="sidebar-item py-2">
                                                    <a class="fs-3" href="#">Authentication Design</a>
                                                </li>
                                                <li class="sidebar-item py-2">
                                                    <a class="fs-3" href="#">Register Now</a>
                                                </li>
                                                <li class="sidebar-item py-2">
                                                    <a class="fs-3" href="#">404
                                                        Error Page</a>
                                                </li>
                                                <li class="sidebar-item py-2">
                                                    <a class="fs-3" href="#">Notes App</a>
                                                </li>
                                                <li class="sidebar-item py-2">
                                                    <a class="fs-3" href="#">User
                                                        Application</a>
                                                </li>
                                                <li class="sidebar-item py-2">
                                                    <a class="fs-3" href="#">Account Settings</a>
                                                </li>
                                            </ul>
                                        </ul>
                                    </li>
                                    <li class="sidebar-item">
                                        <a class="sidebar-link px-1" href="#" aria-expanded="false">
                                            <span class="d-flex">
                                                <iconify-icon icon="solar:chat-unread-outline"
                                                    class="fs-6"></iconify-icon>
                                            </span>
                                            <span class="hide-menu">Chat</span>
                                        </a>
                                    </li>
                                    <li class="sidebar-item">
                                        <a class="sidebar-link px-1" href="#" aria-expanded="false">
                                            <span class="d-flex">
                                                <iconify-icon icon="solar:calendar-minimalistic-outline"
                                                    class="fs-6"></iconify-icon>
                                            </span>
                                            <span class="hide-menu">Calendar</span>
                                        </a>
                                    </li>
                                    <li class="sidebar-item">
                                        <a class="sidebar-link px-1" href="#" aria-expanded="false">
                                            <span class="d-flex">
                                                <iconify-icon icon="solar:inbox-unread-outline"
                                                    class="fs-6"></iconify-icon>
                                            </span>
                                            <span class="hide-menu">Email</span>
                                        </a>
                                    </li>
                                </ul>
                            </div>
                        </nav>
                    </div> -->
                    <!-- end Mobile Offcanvas Menu -->
                </div>
            </header>
            <!--  Header End -->

            <div class="body-wrapper">
                <div class="container-fluid">
                    <?= $this->renderSection('content') ?>
                </div>
            </div>
        </div>

        <!--  Search Bar -->
        <!-- <div class="modal fade" id="exampleModal" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-scrollable modal-lg">
                <div class="modal-content rounded-1">
                    <div class="modal-header border-bottom">
                        <input type="search" class="form-control fs-2" placeholder="Search here" id="search" />
                        <a href="javascript:void(0)" data-bs-dismiss="modal" class="lh-1">
                            <i class="ti ti-x fs-5 ms-3"></i>
                        </a>
                    </div>
                    <div class="modal-body message-body" data-simplebar="">
                        <h5 class="mb-0 fs-5 p-1">Quick Page Links</h5>
                        <ul class="list mb-0 py-2">
                            <li class="p-1 mb-1 px-2 rounded bg-hover-light-black">
                                <a href="#">
                                    <span class="h6 mb-1">Modern</span>
                                    <span class="fs-2 text-muted d-block">/dashboards/dashboard1</span>
                                </a>
                            </li>
                            <li class="p-1 mb-1 px-2 rounded bg-hover-light-black">
                                <a href="#">
                                    <span class="h6 mb-1">Dashboard</span>
                                    <span class="fs-2 text-muted d-block">/dashboards/dashboard2</span>
                                </a>
                            </li>
                            <li class="p-1 mb-1 px-2 rounded bg-hover-light-black">
                                <a href="#">
                                    <span class="h6 mb-1">Contacts</span>
                                    <span class="fs-2 text-muted d-block">/apps/contacts</span>
                                </a>
                            </li>
                            <li class="p-1 mb-1 px-2 rounded bg-hover-light-black">
                                <a href="#">
                                    <span class="h6 mb-1">Posts</span>
                                    <span class="fs-2 text-muted d-block">/apps/blog/posts</span>
                                </a>
                            </li>
                            <li class="p-1 mb-1 px-2 rounded bg-hover-light-black">
                                <a href="#">
                                    <span class="h6 mb-1">Detail</span>
                                    <span
                                        class="fs-2 text-muted d-block">/apps/blog/detail/streaming-video-way-before-it-was-cool-go-dark-tomorrow</span>
                                </a>
                            </li>
                            <li class="p-1 mb-1 px-2 rounded bg-hover-light-black">
                                <a href="#">
                                    <span class="h6 mb-1">Shop</span>
                                    <span class="fs-2 text-muted d-block">/apps/ecommerce/shop</span>
                                </a>
                            </li>
                            <li class="p-1 mb-1 px-2 rounded bg-hover-light-black">
                                <a href="#">
                                    <span class="h6 mb-1">Modern</span>
                                    <span class="fs-2 text-muted d-block">/dashboards/dashboard1</span>
                                </a>
                            </li>
                            <li class="p-1 mb-1 px-2 rounded bg-hover-light-black">
                                <a href="#">
                                    <span class="h6 mb-1">Dashboard</span>
                                    <span class="fs-2 text-muted d-block">/dashboards/dashboard2</span>
                                </a>
                            </li>
                            <li class="p-1 mb-1 px-2 rounded bg-hover-light-black">
                                <a href="#">
                                    <span class="h6 mb-1">Contacts</span>
                                    <span class="fs-2 text-muted d-block">/apps/contacts</span>
                                </a>
                            </li>
                            <li class="p-1 mb-1 px-2 rounded bg-hover-light-black">
                                <a href="#">
                                    <span class="h6 mb-1">Posts</span>
                                    <span class="fs-2 text-muted d-block">/apps/blog/posts</span>
                                </a>
                            </li>
                            <li class="p-1 mb-1 px-2 rounded bg-hover-light-black">
                                <a href="#">
                                    <span class="h6 mb-1">Detail</span>
                                    <span
                                        class="fs-2 text-muted d-block">/apps/blog/detail/streaming-video-way-before-it-was-cool-go-dark-tomorrow</span>
                                </a>
                            </li>
                            <li class="p-1 mb-1 px-2 rounded bg-hover-light-black">
                                <a href="#">
                                    <span class="h6 mb-1">Shop</span>
                                    <span class="fs-2 text-muted d-block">/apps/ecommerce/shop</span>
                                </a>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div> -->
        <!-- END Search Bar -->

        <!-- Custom Modal -->
        <!-- small modal -->
        <div id="bs_modal_sm" class="modal fade" tabindex="-1" data-bs-backdrop="static">
            <div class="modal-dialog modal-sm modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header d-flex align-items-center">
                        <h4 class="modal-title" id="sm_modal_title"></h4>
                        <!-- <button type="button" class="btn-close" data-bs-dismiss="modal"></button> -->
                    </div>
                    <div class="modal-body" id="sm_modal_body"></div>
                </div>
            </div>
        </div>
        <!-- end small modal -->

        <!-- medium modal -->
        <div id="bs_modal_md" class="modal fade" tabindex="-1" data-bs-backdrop="static">
            <div class="modal-dialog modal-lg modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header d-flex align-items-center">
                        <h4 class="modal-title" id="md_modal_title"></h4>
                        <!-- <button type="button" class="btn-close" data-bs-dismiss="modal"></button> -->
                    </div>
                    <div class="modal-body" id="md_modal_body"></div>
                </div>
            </div>
        </div>
        <!-- end medium modal -->

        <!-- large modal -->
        <div id="bs_modal_lg" class="modal fade" tabindex="-1" data-bs-backdrop="static">
            <div class="modal-dialog modal-lg modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header d-flex align-items-center">
                        <h4 class="modal-title" id="lg_modal_title"></h4>
                        <!-- <button type="button" class="btn-close" data-bs-dismiss="modal"></button> -->
                    </div>
                    <div class="modal-body" id="lg_modal_body"></div>
                </div>
            </div>
        </div>
        <!-- end large modal -->

        <!-- extra large modal -->
        <div id="bs_modal_xlg" class="modal fade" tabindex="-1" data-bs-backdrop="static">
            <div class="modal-dialog modal-xl modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header d-flex align-items-center">
                        <h4 class="modal-title" id="xlg_modal_title"></h4>
                        <!-- <button type="button" class="btn-close" data-bs-dismiss="modal"></button> -->
                    </div>
                    <div class="modal-body" id="xlg_modal_body"></div>
                </div>
            </div>
        </div>
        <!-- end extra large modal -->
        <!-- End Custom Modal -->

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
    <!-- HTML5 QR Code -->
    <script src="<?= base_url('html5-qrcode.min.js'); ?>"></script>
    <!-- Toastr JS -->
    <script src="<?= base_url('assets/js/plugins/toastr-init.js'); ?>"></script>
    <!-- SweetAlert2 -->
    <script src="<?= base_url('assets/libs/sweetalert2/dist/sweetalert2.all.min.js'); ?>"></script>
    <!-- Datatable JS -->
    <script src="<?= base_url('assets/js/new_datatable/datatables.min.js') ?>"></script>
    <!-- JQuery Validation -->
    <script src="<?= base_url('assets/libs/jquery-validation/dist/jquery.validate.min.js') ?>"></script>
    <script src="<?= base_url('assets/libs/jquery-validation/dist/additional-methods.min.js') ?>"></script>
    <script src="<?= base_url('assets/libs/jquery-validation/dist/localization/messages_id.min.js') ?>"></script>
    <script src="<?= base_url('assets/js/jquery.form.min.js') ?>"></script>
    <!-- Select 2 -->
    <script src="<?= base_url('assets/libs/select2/dist/js/select2.full.min.js') ?>"></script>
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