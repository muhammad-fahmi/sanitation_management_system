<?php

namespace App\Controllers;

use App\Models\UserModel;

class Profile extends BaseController
{
    public function index()
    {
        if (!session()->has('jwt')) {
            return redirect()->to('auth/login');
        }

        $decoded = $this->jwt->decode(session()->get('jwt'));

        if (time() > (int) ($decoded['expire_time'] ?? 0)) {
            return redirect()->to('auth/login');
        }

        $userModel = new UserModel();
        $user = $userModel->getUserById($decoded['user_id']);

        if (!$user) {
            return redirect()->to('auth/login');
        }

        $sent_data = [
            'page_title' => 'My Profile',
            'user_info'  => $decoded,
            'user'       => $user,
        ];

        return view('profile/vw_profile', $sent_data);
    }

    public function update_info()
    {
        if (!session()->has('jwt')) {
            return $this->response->setStatusCode(401)->setJSON(['status' => 401, 'message' => 'Unauthorized']);
        }

        $decoded = $this->jwt->decode(session()->get('jwt'));

        $rules = [
            'name'     => 'required|min_length[3]|max_length[50]',
            'username' => 'required|min_length[3]|max_length[100]',
        ];

        if (!$this->validate($rules)) {
            return $this->response->setStatusCode(400)->setJSON([
                'status'  => 400,
                'message' => 'Validasi gagal',
                'errors'  => $this->validator->getErrors(),
            ]);
        }

        $data = $this->validator->getValidated();
        $userId = (int) $decoded['user_id'];

        $userModel = new UserModel();

        // Check username uniqueness (exclude current user)
        if (!$userModel->isUsernameUnique($data['username'], $userId)) {
            return $this->response->setStatusCode(400)->setJSON([
                'status'  => 400,
                'message' => 'Username sudah digunakan',
                'errors'  => ['username' => 'Username sudah digunakan'],
            ]);
        }

        $updateData = [
            'name'     => $data['name'],
            'username' => $data['username'],
        ];

        if (!$userModel->updateUser($userId, $updateData)) {
            return $this->response->setStatusCode(500)->setJSON([
                'status'  => 500,
                'message' => 'Gagal memperbarui profil',
            ]);
        }

        // Re-encode JWT with updated name
        $newPayload = [
            'user_id'     => $decoded['user_id'],
            'name'        => $data['name'],
            'user_role'   => $decoded['user_role'],
            'expire_time' => $decoded['expire_time'],
        ];
        $this->jwt->encode($newPayload);

        // Refresh cookies with same expiry duration
        $cookieExpiry = (int) ($decoded['expire_time'] - time());
        $this->response
            ->setCookie('auth_jwt', (string) session()->get('jwt'), $cookieExpiry, '', '/', '', false, true, 'Lax')
            ->setCookie('auth_key', (string) session()->get('key'), $cookieExpiry, '', '/', '', false, true, 'Lax');

        return $this->response->setStatusCode(200)->setJSON([
            'status'  => 200,
            'message' => 'Profil berhasil diperbarui',
        ]);
    }

    public function update_photo()
    {
        if (!session()->has('jwt')) {
            return $this->response->setStatusCode(401)->setJSON(['status' => 401, 'message' => 'Unauthorized']);
        }

        $decoded = $this->jwt->decode(session()->get('jwt'));
        $userId  = (int) $decoded['user_id'];

        $file = $this->request->getFile('photo');

        if (!$file || !$file->isValid()) {
            return $this->response->setStatusCode(400)->setJSON([
                'status'  => 400,
                'message' => 'File tidak valid atau tidak ditemukan',
            ]);
        }

        if ($file->hasMoved()) {
            return $this->response->setStatusCode(400)->setJSON([
                'status'  => 400,
                'message' => 'File sudah diproses sebelumnya',
            ]);
        }

        // Validate mime type (only jpg/jpeg/png)
        $allowedMimes = ['image/jpeg', 'image/png'];
        if (!in_array($file->getMimeType(), $allowedMimes, true)) {
            return $this->response->setStatusCode(400)->setJSON([
                'status'  => 400,
                'message' => 'Format file tidak didukung. Gunakan JPG atau PNG.',
            ]);
        }

        // Max 2 MB
        if ($file->getSizeByUnit('mb') > 2) {
            return $this->response->setStatusCode(400)->setJSON([
                'status'  => 400,
                'message' => 'Ukuran file maksimal 2 MB',
            ]);
        }

        // Get current user name (used as filename per convention)
        $userModel = new UserModel();
        $user = $userModel->getUserById($userId);
        if (!$user) {
            return $this->response->setStatusCode(404)->setJSON(['status' => 404, 'message' => 'User tidak ditemukan']);
        }

        $savePath  = FCPATH . 'assets' . DIRECTORY_SEPARATOR . 'profiles' . DIRECTORY_SEPARATOR;
        $fileName  = trim($user['name']) . '.jpg';

        // Move & convert to jpg using GD
        $tmpPath = $file->getTempName();
        $mime    = $file->getMimeType();

        if ($mime === 'image/png') {
            $src = imagecreatefrompng($tmpPath);
        } else {
            $src = imagecreatefromjpeg($tmpPath);
        }

        if (!$src) {
            return $this->response->setStatusCode(500)->setJSON([
                'status'  => 500,
                'message' => 'Gagal memproses gambar',
            ]);
        }

        // Crop square from center
        $origW = imagesx($src);
        $origH = imagesy($src);
        $side  = min($origW, $origH);
        $x     = (int) (($origW - $side) / 2);
        $y     = (int) (($origH - $side) / 2);
        $size  = 300;
        $dst   = imagecreatetruecolor($size, $size);

        if ($mime === 'image/png') {
            imagealphablending($dst, false);
            imagesavealpha($dst, true);
            $bg = imagecolorallocate($dst, 255, 255, 255);
            imagefill($dst, 0, 0, $bg);
        }

        imagecopyresampled($dst, $src, 0, 0, $x, $y, $size, $size, $side, $side);
        imagedestroy($src);

        if (!imagejpeg($dst, $savePath . $fileName, 90)) {
            imagedestroy($dst);
            return $this->response->setStatusCode(500)->setJSON([
                'status'  => 500,
                'message' => 'Gagal menyimpan foto',
            ]);
        }
        imagedestroy($dst);

        return $this->response->setStatusCode(200)->setJSON([
            'status'      => 200,
            'message'     => 'Foto profil berhasil diperbarui',
            'photo_url'   => base_url('assets/profiles/' . rawurlencode($fileName)) . '?v=' . time(),
        ]);
    }

    public function update_password()
    {
        if (!session()->has('jwt')) {
            return $this->response->setStatusCode(401)->setJSON(['status' => 401, 'message' => 'Unauthorized']);
        }

        $decoded = $this->jwt->decode(session()->get('jwt'));
        $userId = (int) $decoded['user_id'];

        $rules = [
            'current_password'  => 'required',
            'new_password'      => 'required|min_length[6]',
            'confirm_password'  => 'required|matches[new_password]',
        ];

        $messages = [
            'confirm_password' => [
                'matches' => 'Konfirmasi password tidak cocok dengan password baru',
            ],
        ];

        if (!$this->validate($rules, $messages)) {
            return $this->response->setStatusCode(400)->setJSON([
                'status'  => 400,
                'message' => 'Validasi gagal',
                'errors'  => $this->validator->getErrors(),
            ]);
        }

        $data = $this->validator->getValidated();

        $userModel = new UserModel();
        $user = $userModel->select('user_id, password')->find($userId);

        if (!$user) {
            return $this->response->setStatusCode(404)->setJSON(['status' => 404, 'message' => 'User tidak ditemukan']);
        }

        if (!password_verify($data['current_password'], $user['password'])) {
            return $this->response->setStatusCode(400)->setJSON([
                'status'  => 400,
                'message' => 'Password saat ini salah',
                'errors'  => ['current_password' => 'Password saat ini salah'],
            ]);
        }

        $hashed = password_hash($data['new_password'], PASSWORD_BCRYPT);

        if (!$userModel->updateUser($userId, ['password' => $hashed])) {
            return $this->response->setStatusCode(500)->setJSON([
                'status'  => 500,
                'message' => 'Gagal memperbarui password',
            ]);
        }

        return $this->response->setStatusCode(200)->setJSON([
            'status'  => 200,
            'message' => 'Password berhasil diperbarui',
        ]);
    }
}
