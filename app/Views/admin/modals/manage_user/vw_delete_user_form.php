<?php
$jabatan = "";
$role = esc(data: $user['user_role']);
if ($role == 'operator') {
    $jabatan = "Petugas";
} else if ($role == 'verifikator') {
    $jabatan = "Verifikator";
} else if ($role == 'administrator') {
    $jabatan = "Admin";
}
?>
<form id="form_delete_user" action="<?= site_url('admin/manage/user/delete'); ?>" method="POST">
    <input type="hidden" name="_method" value="DELETE">
    <input type="hidden" name="id" value="<?= esc($user['user_id']) ?>">

    <div class="row g-2">
        <div class="col-12 form_mapel">
            <div class="col-12 mb-3">
                <div class="alert alert-danger m-0">
                    Apakah anda yakin untuk menghapus data <b><?= $jabatan; ?></b> ini?
                </div>
            </div>
            <table>
                <tr class="mb-2">
                    <td style="min-width: 120px; font-weight: 500;">Nama</td>
                    <td style="min-width: 10px;">:</td>
                    <td style="width: 100%;">
                        <?= esc($user['name']) ?>
                    </td>
                </tr>
                <tr class="mb-2">
                    <td style="min-width: 120px; font-weight: 500;">Jabatan</td>
                    <td style="min-width: 10px;">:</td>
                    <td style="width: 100%;">
                        <?= $jabatan; ?>
                    </td>
                </tr>
            </table>
        </div>
    </div>
    <div class="modal-footer">
        <button type="button" class="btn btn-md btn-danger d-flex align-items-center" data-bs-dismiss="modal">
            <iconify-icon icon="fa7-solid:cancel" width="20" height="20" class="me-1"></iconify-icon>
            Batal
        </button>
        <button id="btn_delete_user" class="btn btn-md btn-primary d-flex align-items-center">
            <iconify-icon icon="fa7-solid:trash" width="20" height="20" class="me-1"></iconify-icon>
            Hapus
        </button>
    </div>
</form>

<script>
    $('#form_delete_user').validate($.extend({
        submitHandler: function (form, event) {
            event.preventDefault();
            let submit = $('#btn_delete_user');
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