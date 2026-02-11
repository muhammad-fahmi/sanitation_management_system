<form id="form_add_item" action="<?= site_url('admin/manage/task/add/item') ?>" method="post">
    <?= csrf_field() ?>
    <input type="hidden" name="location_id" value="<?= $location_id; ?>">

    <div class="mb-3">
        <label for="add_item" class="form-label">Nama</label>
        <input type="text" class="form-control" id="add_item" name="item" placeholder="Masukkan nama item">
    </div>

    <div class="d-flex justify-content-end gap-2">
        <button type="button" class="btn btn-secondary d-flex align-items-center" data-bs-dismiss="modal">
            <iconify-icon icon="fa7-solid:ban" width="20" height="20"></iconify-icon>
            Batal
        </button>
        <button id="btnFormAddItem" class="btn btn-primary d-flex align-items-center">
            <iconify-icon icon="fa7-solid:save" width="20" height="20"></iconify-icon>
            Simpan
        </button>
    </div>
</form>

<script>
    $("#form_add_item").validate($.extend({
        submitHandler: function (form, event) {
            event.preventDefault();
            var submit = $('#btnFormAddItem');
            submit.attr('disabled', true);
            $(form).ajaxSubmit({
                error: function (e) {
                    const msg = (e.responseJSON && e.responseJSON.message) ? e.responseJSON.message : 'Gagal menambahkan item';
                    const title = (e.responseJSON && e.responseJSON.title) ? e.responseJSON.title : 'Error';
                    toastr.error(msg, title);
                    submit.attr('disabled', false);
                },
                success: function (e) {
                    if (e.status) {
                        // what to do when the response status = true
                        reload_table();
                        toastr.success(e.message);
                        setTimeout(() => {
                            // modal dismiss
                            $('#bs_modal_md').modal('hide');
                        }, 350);
                    } else {
                        // what to do when the response status = false
                        toastr.info(e.message);
                        submit.attr('disabled', false);
                    }
                },
                dataType: 'json'
            });
        },
        rules: {
            location_id: {
                required: true
            },
            item: {
                required: true
            },
        },
        errorClass: 'is-invalid',
        validClass: 'is-valid',
        errorPlacement: function (error, element) {
            error.css('color', 'red')
            error.insertAfter(element);
        },
        messages: {
            location_id: {
                required: "ID Lokasi tidak boleh kosong."
            },
            item: {
                required: "Nama tidak boleh kosong."
            },
        },
    }));
</script>