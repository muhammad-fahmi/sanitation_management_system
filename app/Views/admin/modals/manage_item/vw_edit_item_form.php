<form id="form_edit_item" action="<?= site_url('admin/manage/task/edit/item') ?>" method="post">
    <?= csrf_field() ?>
    <input type="hidden" name="_method" value="PUT">
    <input type="hidden" name="id" value="<?= esc($item['item_id']); ?>">
    <input type="hidden" name="location_id" value="<?= esc($item['location_id']); ?>">

    <div class="mb-3">
        <label for="edit_item" class="form-label">Nama</label>
        <input type="text" class="form-control" id="edit_item" name="item" value="<?= esc($item['item_name']) ?>"
            placeholder="Masukkan nama item">
    </div>

    <div class="d-flex justify-content-end gap-2">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
        <button id="btnFormEditItem" class="btn btn-primary">Update</button>
    </div>
</form>

<script>
    $("#form_edit_item").validate($.extend({
        submitHandler: function (form, event) {
            event.preventDefault();
            let submit = $('#btnFormEditItem');
            submit.attr('disabled', true);
            $(form).ajaxSubmit({
                error: function (e) {
                    toastr.error(e.responseJSON.message, e.responseJSON.title);
                    console.log(e);
                    submit.attr('disabled', false);
                },
                success: function (e) {
                    if (e.status) {
                        // what to do when the response status = true
                        reload_table();
                        toastr.success(e.message);
                        setTimeout(() => {
                            // modal dismiss
                            $("#bs_modal_md").modal("hide");
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
            error.css('color', 'red');
            error.insertAfter(element);
        },
        messages: {
            location_id: {
                required: "ID Lokasi tidka boleh kosong."
            },
            item: {
                required: "Nama item tidak boleh kosong."
            },
        },
    }));
</script>