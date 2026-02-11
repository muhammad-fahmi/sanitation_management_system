<form id="form_add_location" action="<?= site_url('admin/manage/task/add/location') ?>" method="post">
    <?= csrf_field() ?>

    <div class="mb-3">
        <label for="add_location" class="form-label">Nama Lokasi</label>
        <input type="text" class="form-control" id="add_location" name="location" placeholder="Masukkan nama lokasi">
    </div>

    <div class="d-flex justify-content-end gap-2">
        <button type="button" class="btn btn-secondary d-flex align-items-center" data-bs-dismiss="modal">
            <iconify-icon icon="fa7-solid:ban" width="20" height="20"></iconify-icon>
            Batal
        </button>
        <button id="btnFormAddLocation" class="btn btn-primary d-flex align-items-center">
            <iconify-icon icon="fa7-solid:save" width="20" height="20"></iconify-icon>
            Simpan
        </button>
    </div>
</form>

<script>
    $("#form_add_location").validate($.extend({
        submitHandler: function (form, event) {
            event.preventDefault();
            var submit = $('#btnFormAddLocation');
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
            location: {
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
            location: {
                required: "Nama tidak boleh kosong."
            },
        },
    }));
</script>