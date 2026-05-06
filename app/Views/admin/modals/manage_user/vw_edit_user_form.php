<form id="form_edit_user" action="<?= base_url('admin/manage/user/edit'); ?>" method="POST">
    <?= csrf_field(); ?>
    <input type="hidden" name="_method" value="PUT">
    <input type="hidden" name="id" value="<?= esc($user['user_id']) ?>">

    <div class="mb-3">
        <label for="edit_name" class="form-label">Nama</label>
        <input type="text" class="form-control" id="edit_name" name="name" value="<?= esc($user['name']) ?>">
    </div>

    <div class="mb-3">
        <label for="edit_role" class="form-label">Jabatan</label>
        <select class="form-select" id="edit_role" name="user_role">
            <option value="operator" <?= esc($user['user_role']) == 'operator' ? 'selected' : '' ?>>Operator</option>
            <option value="verifikator" <?= esc($user['user_role']) == 'verifikator' ? 'selected' : '' ?>>Verifikator
            </option>
            <option value="administrator" <?= esc($user['user_role']) == 'administrator' ? 'selected' : '' ?>>Administrator
            </option>
        </select>
    </div>

    <div class="mb-3">
        <label for="edit_username" class="form-label">Username</label>
        <input type="text" class="form-control" id="edit_username" name="username"
            value="<?= esc($user['username']) ?>">
    </div>

    <div class="mb-3">
        <label for="edit_password" class="form-label">
            Password
            <small class="text-muted">(Kosongkan jika tidak diubah)</small>
        </label>
        <input type="password" class="form-control" id="edit_password" name="password">
    </div>

    <div class="modal-footer">
        <button type="button" class="btn bg-danger-subtle text-danger d-flex align-items-center"
            data-bs-dismiss="modal">
            <iconify-icon icon="fa7-solid:cancel" width="20" height="20" class="me-1"></iconify-icon>
            Batal
        </button>
        <button type="submit" id="btnFormEditUser" class="btn btn-primary d-flex align-items-center">
            <iconify-icon icon="fa7-solid:save" width="20" height="20"></iconify-icon>
            Simpan
        </button>
    </div>
</form>

<script>
    $("#form_edit_user").validate($.extend({
        submitHandler: function (form, event) {
            event.preventDefault();
            let submit = $('#btnFormEditUser');
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
            name: {
                required: true
            },
            role: {
                required: true
            },
            username: {
                required: true,
                minlength: 3
            },
            password: {
                required: false,
                minlength: 3
            },
        },
        errorClass: 'is-invalid',
        validClass: 'is-valid',
        errorPlacement: function (error, element) {
            error.css('color', 'red');
            error.insertAfter(element);
        },
        messages: {
            name: {
                required: "Nama tidak boleh kosong."
            },
            role: {
                required: "Pilih salah satu jabatan."
            },
            username: {
                required: "Username harus diisi.",
                minlength: "Username tidak boleh kurang dari 3 karakter."
            },
            password: {
                minlength: "Password tidak boleh kurang dari 6 karakter."
            },
        },
    }));
</script>