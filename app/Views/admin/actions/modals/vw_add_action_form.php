<form id="form_add_action" action="<?= site_url('admin/manage/task/add/action') ?>" method="post">
    <?= csrf_field() ?>
    <input type="hidden" name="item_id" value="<?= $item_id ?>">


    <div class="mb-3">
        <label for="add_aksi" class="form-label">Nama</label>
        <input type="text" class="form-control" id="add_aksi" name="aksi" placeholder="Masukkan nama aksi">
    </div>

    <div class="d-flex justify-content-end gap-2">
        <button type="button" class="btn btn-secondary d-flex align-items-center" data-bs-dismiss="modal">
            <iconify-icon icon="fa7-solid:ban" width="20" height="20"></iconify-icon>
            Batal
        </button>
        <button id="btnFormAddAksi" class="btn btn-primary d-flex align-items-center">
            <iconify-icon icon="fa7-solid:save" width="20" height="20"></iconify-icon>
            Simpan
        </button>
    </div>
</form>

<script>
    $("#form_add_action").validate($.extend({
        submitHandler: function (form, event) {
            event.preventDefault();
            var submit = $('#btnFormAddAksi');
            submit.attr('disabled', true);
            $(form).ajaxSubmit({
                error: function (e) {
                    toastr.error(e.responseJSON.message, e.responseJSON.title);
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
            item_id: {
                required: true
            },
            aksi: {
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
            item_id: {
                required: "Item ID tidka boleh kosong"
            },
            aksi: {
                required: "Aksi tidak boleh kosong."
            },
        },
    }));
</script>