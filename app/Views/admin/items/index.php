<?= $this->extend('layouts/vw_master') ?>

<?= $this->section('style') ?>
<style>
    .table_container {
        overflow: auto;
        width: 100%;
    }

    #table_data_item th {
        text-align: center;
    }
</style>
<?= $this->endSection() ?>

<?= $this->section('page_title') ?>
Manajemen Lokasi
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="container p-3">
    <div class="d-flex justify-content-start my-3">
        <button onclick="history.back()" class="btn btn-primary text-end d-flex align-items-center">
            <iconify-icon icon="fa7-solid:arrow-left" width="20" height="20"></iconify-icon>
            Back
        </button>
    </div>

    <div class="card">
        <div class="card-body p-3 table_container">
            <table class="table table-striped">
                <tbody>
                    <tr>
                        <td>Lokasi</td>
                        <td>:</td>
                        <td class="text-capitalize"><?= $location_name ?></td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>

    <div class="card">
        <div class="card-header d-flex align-items-center">
            <h5>Manajemen Item</h5>
            <button class="btn btn-primary ms-3 d-flex align-items-center" onclick="modal('<?= $location_id ?>','add')">
                <iconify-icon icon="fa7-solid:plus" width="20" height="20"></iconify-icon>
                Tambah
            </button>
        </div>
        <div class="card-body p-3 table_container">
            <table class="table table-striped" id="table_data_item">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Nama Item</th>
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
    let table_data_item = new DataTable("#table_data_item", {
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
            url: '<?= site_url('admin/manage/task/get_datatable/item'); ?>',
            type: 'POST',
            data: {
                location_id: '<?= $location_id ?>'
            }
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
                data: 'item_name',
                className: 'vam',
                createdCell: function (td, cellData, rowData, row, col) {
                    $(td).html('<div class="text-center text-capitalize">' + cellData + '</div>');
                },
            },
            {
                data: 'item_id',
                className: 'vam',
                orderable: false,
                createdCell: function (td, cellData, rowData, row, col) {
                    // $(td).html('<div class="text-start">' + cellData + '</div>');
                    let html_aksi = `
                        <div class="btn-group">
                            <a class="btn btn-sm btn-info d-flex align-items-center" href="<?= base_url('admin/manage/task/' . $location_id . '/'); ?>${cellData}">
                                <iconify-icon icon="mdi:eye" width="18" height="18" class="me-1"></iconify-icon>
                                List Aksi
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
        let url = "<?= base_url('admin/manage/task/modal/item') ?>";
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
        table_data_item.ajax.reload();
    }
</script>
<?= $this->endSection() ?>