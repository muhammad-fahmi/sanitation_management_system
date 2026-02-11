<?= $this->extend('layouts/vw_master') ?>

<?= $this->section('style') ?>
<style>
    .table_container {
        overflow: auto;
        width: 100%;
    }

    #table_data_user th {
        text-align: center;
    }
</style>
<?= $this->endSection() ?>

<?= $this->section('page_title') ?>
Manajemen User
<?= $this->endSection() ?>

<?= $this->section('content') ?>

<div class="container p-3">

    <div class="card">
        <div class="card-header d-flex align-items-center">
            <h5 style="text-align: justify;">Manajemen User</h5>
            <button class="btn btn-md btn-primary ms-3 d-flex align-items-center"
                onclick="showModal('<?= site_url('admin/manage/user/new') ?>','Tambah User')">
                <iconify-icon icon="mdi:user-add" class="me-1" width="20" height="20"></iconify-icon>
                Tambah
            </button>
        </div>
        <div class="card-body p-3 table_container">
            <table class="table table-striped" id="table_data_user">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Nama</th>
                        <th>Jabatan</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                </tbody>
            </table>
        </div>

    </div>
</div>
<?= $this->endSection() ?>


<?= $this->section('script') ?>
<script>
    let table_data_user = $("#table_data_user").DataTable({
        responsive: true,
        processing: true,
        serverSide: true,
        order: [
            [1, "asc"],
        ],
        language: {
            searchPlaceholder: "Search..."
        },
        ajax: {
            url: '<?= base_url('admin/manage/user'); ?>',
            type: 'POST'
        },
        columns: [
            {
                data: 'no',
                className: 'vam',
                orderable: false,
                createdCell: function (td, cellData, rowData, row, col) {
                    $(td).html('<div class="text-center">' + cellData + '</div>');
                },
            },
            {
                data: 'username',
                className: 'vam',
                createdCell: function (td, cellData, rowData, row, col) {
                    $(td).html('<div class="text-center">' + cellData + '</div>');
                },
            },
            {
                data: 'slug',
                className: 'vam',
                createdCell: function (td, cellData, rowData, row, col) {
                    let role = '';
                    if (cellData == 'operator') {
                        role = 'Operator'
                    }
                    if (cellData == 'verifikator') {
                        role = 'Verifikator'
                    }
                    if (cellData == 'admin') {
                        role = 'Administrator'
                    }
                    $(td).html('<div class="text-center">' + role + '</div>');
                },
            },
            {
                data: 'user_id',
                className: 'vam',
                orderable: false,
                createdCell: function (td, cellData, rowData, row, col) {
                    $(td).html('<div class="text-start">' + cellData + '</div>');
                    let html_aksi = `
                        <div class="btn-group">
                            <button type="button" class="btn btn-md btn-info dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">
                                Pilih
                            </button>
                            <ul class="dropdown-menu">
                                <li>
                                    <a class="btn btn-md dropdown-item text-warning d-flex align-items-center" onclick="showModal('<?= site_url('admin/manage/user/edit/') ?>${cellData}', 'Edit User')">
                                        <iconify-icon icon="lucide:edit" width="20" height="20" class="me-2"></iconify-icon>
                                        Edit
                                    </a>
                                </li>
                                <li>
                                    <a class="btn btn-md dropdown-item text-danger d-flex align-items-center" onclick="showModal('<?= site_url('admin/manage/user/delete/') ?>${cellData}', 'Hapus User')">
                                        <iconify-icon icon="mdi:trash" width="20" height="20" class="me-2"></iconify-icon>
                                        Delete
                                    </a>
                                </li>
                            </ul>
                        </div>`;
                    $(td).html('<div class="text-center">' + html_aksi + '</div>');
                },
            },
        ]
    });


    function showModal(url, title = 'Form Data') {
        // Tampilkan indikator loading (opsional)
        $('#bs_modal_md .modal-body').html('<div class="text-center p-3">Loading...</div>');
        $('#bs_modal_md').modal('show');

        // AJAX GET ke route 'new' atau 'edit'
        $.ajax({
            type: "GET",
            url: url,
            dataType: "json",
            success: function (res) {
                if (res.status) {
                    // Update Judul & Isi Modal dengan respon dari Server
                    $('#md_modal_title').text(res.title || title);
                    $('#md_modal_body').html(res.html);
                } else {
                    alert('Gagal memuat form: ' + res.message);
                    $('#bs_modal_md').modal('hide');
                }
            },
            error: function (xhr) {
                alert('Error server: ' + xhr.statusText);
            }
        });
    }

    function reload_table() {
        table_data_user.ajax.reload();
    }

    // function toggleVisibility(rowData) {
    //     var x = document.getElementById("password_" + rowData);
    //     if (x.type === "password") {
    //         x.type = "text";
    //         // Optional: change the toggle text/icon
    //         event.target.textContent = "Hide";
    //     } else {
    //         x.type = "password";
    //         // Optional: change the toggle text/icon
    //         event.target.textContent = "Show";
    //     }
    // }
</script>
<?= $this->endSection() ?>