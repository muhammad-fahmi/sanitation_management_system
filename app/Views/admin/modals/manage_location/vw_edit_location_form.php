<form id="form_edit_location" action="<?= site_url('admin/manage/task/edit/location') ?>" method="post">
    <?= csrf_field() ?>
    <input type="hidden" name="_method" value="PUT">
    <input type="hidden" name="id" value="<?= $location['id'] ?>">

    <div class="mb-3">
        <label for="edit_location" class="form-label">Nama Lokasi</label>
        <input type="text" class="form-control" id="edit_location" name="location"
            value="<?= esc($location['location_name']) ?>" placeholder="Masukkan nama lokasi">
    </div>

    <div class="d-flex justify-content-end gap-2">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
        <button type="submit" id="btnFormEditLocation" class="btn btn-primary">Update</button>
    </div>
</form>

<script>
    $("#form_edit_location").validate($.extend({
        submitHandler: function (form, event) {
            event.preventDefault();
            var submit = $('#btnFormEditLocation');
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
                required: "Nama lokasi tidak boleh kosong."
            },
        },
    }));
</script>