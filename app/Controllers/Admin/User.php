<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\UserModel;

class User extends BaseController
{
    public function index()
    {
        if (!session()->has('jwt')) {
            $decode = $this->jwt->decode(session()->get('jwt'));
            if (time() > $decode['expire_time'] && !$decode['role'] == 'administrator') {
                return redirect()->to('auth/login');
            } else {
                return;
            }
        }

        $sent_data = [
            'page_title' => 'Manage User',
            'user_info' => $this->jwt->decode(session()->get('jwt')),
        ];
        return view('admin/vw_manage_user', $sent_data);
    }

    public function modal()
    {
        $id = $this->request->getVar('id');
        $type = $this->request->getVar('type');
        switch ($type) {
            case 'add':
                $formHtml = view('admin/modals/manage_user/vw_add_user_form');

                return $this->response->setStatusCode(200)->setJSON([
                    'status' => 200,
                    'title' => 'Tambah User',
                    'html' => $formHtml,
                ]);
            case 'edit':
                $userModel = new UserModel();
                $user = $userModel->getUserById($id);

                if (!$user) {
                    return $this->response->setStatusCode(404)->setJSON([
                        'status' => 404,
                        'message' => 'User tidak ditemukan'
                    ]);
                }

                $data = ['user' => $user];
                $formHtml = view('admin/modals/manage_user/vw_edit_user_form', $data);

                return $this->response->setStatusCode(200)->setJSON([
                    'status' => 200,
                    'title' => 'Edit User',
                    'html' => $formHtml,
                    'user' => $user
                ]);
            case 'delete':
                $userModel = new UserModel();
                $user = $userModel->getUserById($id);

                if (!$user) {
                    return $this->response->setStatusCode(404)->setJSON([
                        'status' => 404,
                        'message' => 'User tidak ditemukan'
                    ]);
                }

                $data = ['user' => $user];
                $formHtml = view('admin/modals/manage_user/vw_delete_user_form', $data);

                return $this->response->setStatusCode(200)->setJSON([
                    'status' => 200,
                    'html' => $formHtml,
                    'user' => $user
                ]);
            default:
                $response = [
                    'status' => 404,
                    'message' => 'Tipe modal tidak ditemukan.'
                ];
                return $this->response->setJSON($response);
        }
    }

    public function get_datatable()
    {
        $user = new UserModel();
        $index = $this->request->getVar("order[0][column]");
        // insert datatable param to transfer
        $param = array(
            'start' => $this->request->getVar("start"),
            'length' => $this->request->getVar("length"),
            'order_column' => $this->request->getVar("columns[$index][data]"),
            'order_sort' => $this->request->getVar("order[0][dir]"),
            'search' => $this->request->getVar("search[value]"),
            'draw' => $this->request->getVar("draw"),
            /* additional data can be declared here */
        );
        // move the db operation to the designated models
        $result = $user->get_datatable($param);
        // return the data back to the views
        $data["recordsTotal"] = $result['total'];
        $data["recordsFiltered"] = $result['filtered'];
        $data["data"] = $result['data'];
        $data["draw"] = $result['draw'];
        return $this->response->setJSON($data);
    }

    public function add()
    {
        $userModel = new UserModel();

        // Validation rules
        $rules = [
            'name' => 'required|min_length[3]|max_length[200]',
            'user_role' => 'required|in_list[operator,verifikator,administrator]',
            'username' => 'required|min_length[3]|max_length[150]',
            'password' => 'required|min_length[3]',
            'password_confirm' => 'required|matches[password]'
        ];

        $messages = [
            'password_confirm' => [
                'matches' => 'Konfirmasi password tidak cocok dengan password'
            ]
        ];

        if (!$this->validate($rules, $messages)) {
            return $this->response->setStatusCode(400)->setJSON([
                'status' => 400,
                'message' => 'Validasi gagal',
                'errors' => $this->validator->getErrors()
            ]);
        }

        $data = $this->validator->getValidated();

        // Check if username already exists
        if ($userModel->isUsernameExists($data['username'])) {
            return $this->response->setStatusCode(400)->setJSON([
                'status' => 400,
                'message' => 'Username sudah digunakan',
                'errors' => ['username' => 'Username sudah digunakan']
            ]);
        }

        // Prepare insert data
        $insertData = [
            'name' => $data['name'],
            'user_role' => $data['user_role'],
            'username' => $data['username'],
            'password' => password_hash($data['password'], PASSWORD_BCRYPT)
        ];

        if ($userModel->createUser($insertData)) {
            // Get the newly created user
            $newUserId = $userModel->getInsertID();
            $newUser = $userModel->getUserById($newUserId);

            return $this->response->setStatusCode(201)->setJSON([
                'status' => 201,
                'message' => 'User berhasil ditambahkan',
                'data' => $newUser
            ]);
        }

        return $this->response->setStatusCode(500)->setJSON([
            'status' => 500,
            'message' => 'Gagal menambahkan user'
        ]);
    }

    public function update()
    {
        $id = $this->request->getVar('id');
        $userModel = new UserModel();

        // Validation rules
        $rules = [
            'name' => 'required|min_length[3]|max_length[200]',
            'user_role' => 'required|in_list[operator,verifikator,administrator]',
            'username' => 'required|min_length[3]|max_length[150]',
            'password' => 'permit_empty|min_length[3]'
        ];

        if (!$this->validate($rules)) {
            return $this->response->setStatusCode(400)->setJSON([
                'status' => 400,
                'message' => 'Validasi gagal',
                'errors' => $this->validator->getErrors()
            ]);
        }

        $data = $this->validator->getValidated();

        // Check unique username
        if (!$userModel->isUsernameUnique($data['username'], $id)) {
            return $this->response->setStatusCode(400)->setJSON([
                'status' => 400,
                'message' => 'Username sudah digunakan',
                'errors' => ['username' => 'Username sudah digunakan']
            ]);
        }

        // Prepare update data
        $updateData = [
            'name' => $data['name'],
            'user_role' => $data['user_role'],
            'username' => $data['username']
        ];

        // Only update password if provided
        if (!empty($data['password'])) {
            $updateData['password'] = password_hash($data['password'], PASSWORD_BCRYPT);
        }

        if ($userModel->updateUser($id, $updateData)) {
            return $this->response->setStatusCode(200)->setJSON([
                'status' => 200,
                'message' => 'User berhasil diupdate',
                'data' => $updateData
            ]);
        }

        return $this->response->setStatusCode(500)->setJSON([
            'status' => 500,
            'message' => 'Gagal mengupdate user'
        ]);
    }

    public function delete()
    {
        $id = $this->request->getVar('id');
        $userModel = new UserModel();

        // Check if user exists
        $user = $userModel->getUserById($id);
        if (!$user) {
            return $this->response->setStatusCode(404)->setJSON([
                'status' => 404,
                'message' => 'User tidak ditemukan'
            ]);
        }

        // Prevent deleting yourself (optional security check)
        $currentUserId = session()->get('user_info')['id'] ?? null;
        if ($currentUserId == $id) {
            return $this->response->setStatusCode(400)->setJSON([
                'status' => 400,
                'message' => 'Anda tidak dapat menghapus akun sendiri'
            ]);
        }

        if ($userModel->deleteUser($id)) {
            return $this->response->setStatusCode(200)->setJSON([
                'status' => 200,
                'message' => 'User berhasil dihapus'
            ]);
        }

        return $this->response->setStatusCode(500)->setJSON([
            'status' => 500,
            'message' => 'Gagal menghapus user'
        ]);
    }
}
