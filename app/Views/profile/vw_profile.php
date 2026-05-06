<?= $this->extend('layouts/vw_master') ?>

<?= $this->section('style') ?>
<style>
    .profile-avatar {
        width: 100px;
        height: 100px;
        object-fit: cover;
    }
    .avatar-wrapper {
        position: relative;
        display: inline-block;
    }
    .avatar-edit-btn {
        position: absolute;
        bottom: 4px;
        right: 4px;
        width: 28px;
        height: 28px;
        border-radius: 50%;
        background: var(--bs-primary);
        color: #fff;
        border: 2px solid #fff;
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        font-size: 14px;
        padding: 0;
        transition: background 0.2s;
    }
    .avatar-edit-btn:hover {
        background: var(--bs-primary-dark, #0056b3);
    }
</style>
<?= $this->endSection() ?>

<?= $this->section('page_title') ?>
My Profile
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="container p-3">
    <div class="row">
        <!-- Left: Profile Card -->
        <div class="col-lg-4 col-md-5 mb-4">
            <div class="card text-center p-4">
                <div class="d-flex justify-content-center mb-3">
                    <div class="avatar-wrapper">
                        <img src="<?= profile_image_url($user_info['name'] ?? null) ?>"
                            alt="Profile Photo"
                            id="profile_avatar_img"
                            class="rounded-circle profile-avatar border border-3 border-primary" />
                        <button type="button" class="avatar-edit-btn" id="btn_edit_photo" title="Ganti Foto">
                            <iconify-icon icon="solar:camera-bold" width="14" height="14"></iconify-icon>
                        </button>
                    </div>
                    <!-- hidden file input -->
                    <input type="file" id="input_photo" name="photo" accept="image/jpeg,image/png" class="d-none" />
                </div>
                <h5 class="mb-1"><?= esc($user['name']) ?></h5>
                <span class="badge bg-primary-subtle text-primary mb-1"><?= esc($user['user_role']) ?></span>
                <p class="text-muted mb-0 fs-2">@<?= esc($user['username']) ?></p>
            </div>
        </div>

        <!-- Right: Edit Forms -->
        <div class="col-lg-8 col-md-7">

            <!-- Edit Info Card -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0">
                        <iconify-icon icon="solar:user-id-bold-duotone" class="me-1"></iconify-icon>
                        Informasi Akun
                    </h5>
                </div>
                <div class="card-body">
                    <form id="form_update_info">
                        <div class="mb-3">
                            <label for="name" class="form-label">Nama Lengkap <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="name" name="name"
                                value="<?= esc($user['name']) ?>" required />
                        </div>
                        <div class="mb-3">
                            <label for="username" class="form-label">Username <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="username" name="username"
                                value="<?= esc($user['username']) ?>" required />
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Peran</label>
                            <input type="text" class="form-control" value="<?= esc($user['user_role']) ?>" disabled />
                        </div>
                        <button type="submit" class="btn btn-primary d-flex align-items-center gap-1">
                            <iconify-icon icon="solar:disk-bold"></iconify-icon>
                            Simpan Perubahan
                        </button>
                    </form>
                </div>
            </div>

            <!-- Change Password Card -->
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">
                        <iconify-icon icon="solar:lock-password-bold-duotone" class="me-1"></iconify-icon>
                        Ubah Password
                    </h5>
                </div>
                <div class="card-body">
                    <form id="form_update_password">
                        <div class="mb-3">
                            <label for="current_password" class="form-label">Password Saat Ini <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <input type="password" class="form-control" id="current_password"
                                    name="current_password" required />
                                <button class="btn btn-outline-secondary toggle-password" type="button"
                                    data-target="#current_password">
                                    <iconify-icon icon="solar:eye-linear" id="icon_current_password"></iconify-icon>
                                </button>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label for="new_password" class="form-label">Password Baru <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <input type="password" class="form-control" id="new_password"
                                    name="new_password" required />
                                <button class="btn btn-outline-secondary toggle-password" type="button"
                                    data-target="#new_password">
                                    <iconify-icon icon="solar:eye-linear" id="icon_new_password"></iconify-icon>
                                </button>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label for="confirm_password" class="form-label">Konfirmasi Password Baru <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <input type="password" class="form-control" id="confirm_password"
                                    name="confirm_password" required />
                                <button class="btn btn-outline-secondary toggle-password" type="button"
                                    data-target="#confirm_password">
                                    <iconify-icon icon="solar:eye-linear" id="icon_confirm_password"></iconify-icon>
                                </button>
                            </div>
                        </div>
                        <button type="submit" class="btn btn-warning d-flex align-items-center gap-1">
                            <iconify-icon icon="solar:lock-bold"></iconify-icon>
                            Ubah Password
                        </button>
                    </form>
                </div>
            </div>

        </div>
    </div>
</div>
<?= $this->endSection() ?>

<?= $this->section('script') ?>
<script>
    // Toggle password visibility
    document.querySelectorAll('.toggle-password').forEach(function (btn) {
        btn.addEventListener('click', function () {
            const targetId = this.getAttribute('data-target');
            const input = document.querySelector(targetId);
            const icon = this.querySelector('iconify-icon');
            if (input.type === 'password') {
                input.type = 'text';
                icon.setAttribute('icon', 'solar:eye-closed-linear');
            } else {
                input.type = 'password';
                icon.setAttribute('icon', 'solar:eye-linear');
            }
        });
    });

    // Update profile info
    $('#form_update_info').on('submit', function (e) {
        e.preventDefault();

        const formData = {
            name: $('#name').val().trim(),
            username: $('#username').val().trim(),
        };

        $.ajax({
            url: '<?= base_url('profile/update_info') ?>',
            type: 'PUT',
            contentType: 'application/json',
            data: JSON.stringify(formData),
            success: function (res) {
                Swal.fire({
                    icon: 'success',
                    title: 'Berhasil',
                    text: res.message,
                    confirmButtonText: 'OK'
                }).then(function () {
                    location.reload();
                });
            },
            error: function (xhr) {
                const res = xhr.responseJSON;
                let errorMsg = res?.message ?? 'Terjadi kesalahan';
                if (res?.errors) {
                    const errorList = Object.values(res.errors).join('<br>');
                    errorMsg = errorList;
                }
                Swal.fire({ icon: 'error', title: 'Gagal', html: errorMsg });
            }
        });
    });

    // Edit photo — trigger hidden file input
    $('#btn_edit_photo').on('click', function () {
        $('#input_photo').val('').trigger('click');
    });

    $('#input_photo').on('change', function () {
        const file = this.files[0];
        if (!file) return;

        // Client-side preview
        const reader = new FileReader();
        reader.onload = function (e) {
            $('#profile_avatar_img').attr('src', e.target.result);
        };
        reader.readAsDataURL(file);

        // Upload
        const formData = new FormData();
        formData.append('photo', file);

        $.ajax({
            url: '<?= base_url('profile/update_photo') ?>',
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function (res) {
                // Update both profile avatar and sidebar avatar
                $('#profile_avatar_img').attr('src', res.photo_url);
                $('#sidebar_avatar_img').attr('src', res.photo_url);
                $('#topbar_avatar_img').attr('src', res.photo_url);
                $('#topbar_dropdown_avatar_img').attr('src', res.photo_url);
                Swal.fire({ icon: 'success', title: 'Berhasil', text: res.message, timer: 1800, showConfirmButton: false });
            },
            error: function (xhr) {
                // Revert preview on failure
                location.reload();
                const res = xhr.responseJSON;
                Swal.fire({ icon: 'error', title: 'Gagal', text: res?.message ?? 'Terjadi kesalahan' });
            }
        });
    });

    // Update password
    $('#form_update_password').on('submit', function (e) {
        e.preventDefault();

        const formData = {
            current_password: $('#current_password').val(),
            new_password: $('#new_password').val(),
            confirm_password: $('#confirm_password').val(),
        };

        $.ajax({
            url: '<?= base_url('profile/update_password') ?>',
            type: 'PUT',
            contentType: 'application/json',
            data: JSON.stringify(formData),
            success: function (res) {
                Swal.fire({
                    icon: 'success',
                    title: 'Berhasil',
                    text: res.message,
                    confirmButtonText: 'OK'
                }).then(function () {
                    $('#form_update_password')[0].reset();
                });
            },
            error: function (xhr) {
                const res = xhr.responseJSON;
                let errorMsg = res?.message ?? 'Terjadi kesalahan';
                if (res?.errors) {
                    const errorList = Object.values(res.errors).join('<br>');
                    errorMsg = errorList;
                }
                Swal.fire({ icon: 'error', title: 'Gagal', html: errorMsg });
            }
        });
    });
</script>
<?= $this->endSection() ?>
