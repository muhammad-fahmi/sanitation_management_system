<form id="form_edit_action" action="<?= site_url('admin/manage/task/edit/action') ?>" method="post">
    <?= csrf_field() ?>
    <input type="hidden" name="_method" value="PUT">
    <input type="hidden" name="id" value="<?= esc($action['action_id']); ?>">
    <input type="hidden" name="item_id" value="<?= esc($action['item_id']); ?>">

    <div class="mb-3">
        <label for="edit_aksi" class="form-label">Nama</label>
        <input type="text" class="form-control" id="edit_aksi" name="aksi" value="<?= esc($action['action_name']) ?>"
            placeholder="Masukkan nama aksi">
    </div>

    <div class="d-flex justify-content-end gap-2">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
        <button id="btnFormEditItem" class="btn btn-primary">Update</button>
    </div>
</form>

<script>
    $("#form_edit_action").validate($.extend({
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
            error.css('color', 'red');
            error.insertAfter(element);
        },
        messages: {
            item_id: {
                required: "ID Item tidak boleh kosong."
            },
            aksi: {
                required: "Aksi tidak boleh kosong."
            },
        },
    }));
</script>