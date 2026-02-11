<form id="form_delete_action" action="<?= site_url('admin/manage/task/delete/action') ?>" method="post">
    <input type="hidden" name="_method" value="DELETE">
    <input type="hidden" name="id" value="<?= esc($action['action_id']) ?>">

    <div class="text-center">
        <p>Apakah Anda yakin ingin menghapus aksi "<strong>
                <?= esc($action['action_name']) ?>
            </strong>"?</p>
        <p class="text-muted">Tindakan ini tidak dapat dibatalkan.</p>
    </div>
    <div class="d-flex justify-content-end gap-2 mt-4">
        <button type="button" class="btn btn-secondary d-flex align-items-center" data-bs-dismiss="modal">
            <iconify-icon icon="fa7-solid:ban" width="20" height="20" class="me-1"></iconify-icon>
            Batal
        </button>
        <button type="submit" id="btn_delete_action" class="btn btn-danger d-flex align-items-center">
            <iconify-icon icon="fa7-solid:trash" width="20" height="20" class="me-1"></iconify-icon>
            Hapus
        </button>
    </div>
</form>

<script>
    $('#form_delete_action').validate($.extend({
        submitHandler: function (form, event) {
            event.preventDefault();
            let submit = $('#btn_delete_action');
            // declare button submit disabling right here if needed
            submit.attr('disabled', true);
            $(form).ajaxSubmit({
                error: function (e) {
                    toastr.error('Unknown error, check console.');
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
        rules: {},
        errorClass: 'is-invalid',
        validClass: 'is-valid',
        /* if there's select2 element, please use this errorPlacement */
        errorPlacement: function (error, element) {
            error.insertAfter(element);
        },
        messages: {},
    }));
</script>