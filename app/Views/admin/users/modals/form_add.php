<form id="form_add_user" action="<?= $action ?>" method="POST">
    <?= csrf_field() ?>
    <div class="mb-3">
        <label for="add_username" class="form-label">Username <span class="text-danger">*</span></label>
        <input type="text" class="form-control" id="add_username" name="username">
    </div>

    <div class="mb-3">
        <label for="add_email" class="form-label">Email <span class="text-danger">*</span></label>
        <input type="text" class="form-control" id="add_email" name="email">
    </div>

    <div class="mb-3">
        <label for="role_id" class="form-label fw-normal">Jabatan <span class="text-danger">*</span></label>
        <select class="form-select" id="role_id" name="role_id">
            <option value="" selected>Pilih Jabatan</option>
            <?php foreach ($roles as $role): ?>
                <option value="<?= $role['id'] ?>"><?= $role['name'] ?></option>
            <?php endforeach; ?>
        </select>
    </div>

    <div class="mb-3">
        <label for="add_password" class="form-label">Password <span class="text-danger">*</span></label>
        <input type="password" class="form-control" id="add_password" name="password">
    </div>

    <div class="mb-3">
        <label for="add_password_confirm" class="form-label">Konfirmasi Password <span
                class="text-danger">*</span></label>
        <input type="password" class="form-control" id="add_password_confirm" name="password_confirm">
    </div>

    <div class="modal-footer">
        <button type="button" class="btn btn-md bg-danger-subtle text-danger d-flex align-items-center"
            data-bs-dismiss="modal">
            <iconify-icon icon="fa7-solid:cancel" width="20" height="20" class="me-1"></iconify-icon>
            Batal
        </button>
        <button id="btnFormAddUser" class="btn btn-md btn-primary d-flex align-items-center">
            <iconify-icon icon="fa7-solid:user-plus" width="20" height="20" class="me-1"></iconify-icon>
            Tambah User
        </button>
    </div>
</form>

<script>
    $("#bs_modal_md").on('shown.bs.modal', function () {
        $("input#add_name").focus();
    });

    $("#form_add_user").validate($.extend({
        submitHandler: function (form, event) {
            event.preventDefault();
            let submit = $('#btnFormAddUser');
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
        rules: {
            username: {
                required: true,
                minlength: 3
            },
            email: {
                required: true,
                email: true
            },
            role_id: {
                required: true
            },
            password: {
                required: true,
                minlength: 6
            },
            password_confirm: {
                required: true,
                equalTo: '#add_password'
            },
        },
        errorClass: 'is-invalid',
        validClass: 'is-valid',
        errorPlacement: function (error, element) {
            error.css('color', 'red')
            error.insertAfter(element);
        },
        messages: {
            username: {
                required: "Username harus diisi.",
                minlength: "Username tidak boleh kurang dari 3 karakter."
            },
            email: {
                required: "Email harus diisi.",
                email: "Masukkan format email yang valid."
            },
            role_id: {
                required: "Pilih salah satu jabatan."
            },
            password: {
                required: "Password tidak boleh kosong.",
                minlength: "Password tidak boleh kurang dari 6 karakter."
            },
            password_confirm: {
                required: "Konfirmasi password tidak boleh kosong.",
                equalTo: "Masukkan kembali password yang sama."
            },
        },
    }));
</script>