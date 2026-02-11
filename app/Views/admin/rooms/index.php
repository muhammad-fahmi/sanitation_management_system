<?= $this->extend('layouts/vw_master') ?>

<?= $this->section('style') ?>
<style>
    .table_container {
        overflow: auto;
        width: 100%;
    }

    #table_data_location th {
        text-align: center;
    }
</style>
<?= $this->endSection() ?>

<?= $this->section('page_title') ?>
Manajemen Lokasi
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="container p-3">
    <!-- <div class="d-flex justify-content-start my-3">
        <a href="/admin" class="btn btn-primary text-end">
            <iconify-icon icon="fa7-solid:arrow-rotate-back" width="20" height="20"></iconify-icon>
            Back
        </a>
    </div> -->

    <div class="card">
        <div class="card-header d-flex align-items-center">
            <h5>Manajemen Lokasi</h5>
            <button class="btn btn-primary ms-3 d-flex align-items-center" onclick="modal('','add')">
                <iconify-icon icon="fa7-solid:plus" width="20" height="20"></iconify-icon>
                Tambah
            </button>
        </div>
        <div class="card-body p-3 table_container">
            <table class="table table-striped" id="table_data_location">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Nama Lokasi</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody></tbody>
            </table>
        </div>
    </div>
</div>
<?= $this->endSection() ?>

<?= $this->section('script') ?>
<script>
    let table_data_location = new DataTable("#table_data_location", {
        responsive: true,
        processing: true,
        serverSide: true,
        order: [
            [1, 'asc'],
        ],
        language: {
            searchPlaceholder: "Search..."
        },
        ajax: {
            url: '<?= site_url('admin/manage/task/get_datatable/location'); ?>',
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
                data: 'location_name',
                className: 'vam',
                createdCell: function (td, cellData, rowData, row, col) {
                    $(td).html('<div class="text-center text-capitalize">' + cellData + '</div>');
                },
            },
            {
                data: 'location_id',
                className: 'vam',
                orderable: false,
                createdCell: function (td, cellData, rowData, row, col) {
                    // $(td).html('<div class="text-start">' + cellData + '</div>');
                    let html_aksi = `
                        <div class="btn-group">
                            <a class="btn btn-sm btn-info d-flex align-items-center" href="<?= base_url('admin/manage/task/') ?>${cellData}">
                                <iconify-icon icon="mdi:eye" width="18" height="18" class="me-1"></iconify-icon>
                                List Item
                            </a>
                            <button class="btn btn-sm btn-warning d-flex align-items-center" onclick="modal('${cellData}', 'edit')">
                                <iconify-icon icon="lucide:edit" width="18" height="18" class="me-1"></iconify-icon>
                                Edit
                            </button>
                            <button class="btn btn-sm btn-danger d-flex align-items-center" onclick="modal('${cellData}', 'delete')">
                                <iconify-icon icon="mdi:trash" width="18" height="18" class="me-1"></iconify-icon>
                                Delete
                            </button>
                        </div>`;
                    $(td).html('<div class="text-center">' + html_aksi + '</div>');
                },
            },
        ]
    });

    function modal(id, type) {
        let url = "<?= base_url('admin/manage/task/modal/location') ?>";
        $.ajax({
            type: "POST",
            url: url,
            data: {
                id: id,
                type: type
            },
            success: function (res) {
                if (res.status) {
                    $("#md_modal_title").html(res.title);
                    $("#md_modal_body").html(res.html);
                    $("#bs_modal_md").modal("show");
                } else {
                    toastr.info('Unknown error on modal calls.\n- ' + res.message);
                }
            }
        });
    }

    function reload_table() {
        table_data_location.ajax.reload();
    }
</script>
<?= $this->endSection() ?>