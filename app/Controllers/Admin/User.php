<?php

namespace App\Controllers\Admin;

use App\Libraries\JwtService;
use App\Models\RolesModel;
use App\Models\UserModel;
use App\Models\UserRoleModel;
use CodeIgniter\HTTP\ResponseInterface;
use CodeIgniter\RESTful\ResourcePresenter;

class User extends ResourcePresenter
{
    /**
     * Helper method to build datatable response
     */
    private function _getDatatableResponse($model, $method, $param)
    {
        $result = $model->$method($param);
        return [
            'recordsTotal' => $result['total'],
            'recordsFiltered' => $result['filtered'],
            'data' => $result['data'],
            'draw' => $result['draw'],
        ];
    }

    /**
     * Helper method to get datatable parameters from request
     */
    private function _getDatatableParam($additionalParams = [])
    {
        $index = $this->request->getPost("order[0][column]") ?? 1;
        return array_merge([
            'start' => $this->request->getPost("start"),
            'length' => $this->request->getPost("length"),
            'order_column' => $this->request->getPost("columns[$index][data]") ?? 1,
            'order_sort' => $this->request->getPost("order[0][dir]") ?? "ASC",
            'search' => $this->request->getPost("search[value]"),
            'draw' => $this->request->getPost("draw"),
        ], $additionalParams);
    }

    /**
     * Present a view of resource objects.
     *
     * @return ResponseInterface
     */
    public function index()
    {
        $jwt = new JwtService();
        if ($this->request->isAJAX()) {
            if ($this->request->is('POST')) {
                $userRoleModel = new UserRoleModel();
                $param = $this->_getDatatableParam();
                $result = $this->_getDatatableResponse($userRoleModel, 'get_datatable', $param);
                return $this->response->setJSON($result);
            }
        }

        $sent_data = [
            'page_title' => 'Manage User',
            'user_info' => $jwt->decode(session()->get('jwt')),
        ];
        $html = view('admin/users/index', $sent_data);
        return $this->response->setBody($html);
    }

    /**
     * Present a view to present a specific resource object.
     *
     * @param int|string|null $id
     *
     * @return ResponseInterface
     */
    // public function show($id = null)
    // {
    //     //
    // }

    /**
     * Present a view to present a new single resource object.
     *
     * @return ResponseInterface
     */
    public function new()
    {
        $roleModel = new RolesModel();
        $roles = $roleModel->getAllRoles();

        $html = view('admin/users/modals/form_add', [
            'roles' => $roles,
            'action' => site_url('admin/manage/user/create')
        ]);

        if ($this->request->isAJAX()) {
            return $this->response->setJSON([
                'status' => true,
                'title' => 'Tambah Pengguna Baru',
                'html' => $html
            ]);
        }

        return $this->response->setBody($html);
    }

    /**
     * Process the creation/insertion of a new resource object.
     * This should be a POST.
     *
     * @return ResponseInterface
     */
    public function create()
    {
        // panggil model untuk menyimpan data untuk masing masing tabel
        $userModel = new UserModel();
        $userRoleModel = new userRoleModel();

        // 1. Mengambil data dinamis untuk role
        $roleModel = new RolesModel();
        $allowedRoles = $roleModel->getAllRoles();

        // 2. Mengubah data array menjadi 'comma-separated-value': "admin,operator,verifikator"
        $listString = implode(',', array_column($allowedRoles, 'id'));

        // Validation rules
        $rules = [
            'username' => 'required|min_length[3]|max_length[150]',
            'email' => 'required|valid_email|max_length[255]',
            'role_id' => "required|in_list[$listString]",
            'password' => 'required|min_length[6]',
            'password_confirm' => 'required|matches[password]'
        ];

        $messages = [
            'username' => [
                'min_length' => 'Username minimal 3 karakter',
            ],
            'role_id' => [
                'in_list' => 'Jabatan tidak valid'
            ],
            'password' => [
                'min_length' => 'Password minimal 6 karakter'
            ],
            'password_confirm' => [
                'matches' => 'Konfirmasi password tidak cocok dengan password'
            ]
        ];

        // validasi rules yang sudah ditetapkan
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
        $insertDataUser = [
            'username' => $data['username'],
            'email' => $data['email'],
            'password' => password_hash($data['password'], PASSWORD_BCRYPT),
            'is_active' => 1,
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s'),
        ];

        if ($userModel->save($insertDataUser)) {
            $newUserId = $userModel->getInsertID();
            $insertDataUserRole = [
                'user_id' => $newUserId,
                'role_id' => $data['role_id'],
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ];
            $userRoleModel->save($insertDataUserRole);
            $newUser = $userModel->find($newUserId);

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

    /**
     * Present a view to edit the properties of a specific resource object.
     *
     * @param int|string|null $id
     *
     * @return ResponseInterface
     */
    public function edit($id = null)
    {
        $userRoleModel = new UserRoleModel();
        $data = $userRoleModel->getUserDetail($id);

        if (!$data) {
            return $this->response->setJSON(['status' => false, 'message' => 'Data tidak ditemukan']);
        }

        $html = view('admin/users/modals/form_edit', [
            'data' => $data,
            'roles' => (new RolesModel())->getAllRoles(),
            'action' => site_url('admin/manage/user/update/' . $id)
        ]);

        if ($this->request->isAJAX()) {
            return $this->response->setJSON([
                'status' => true,
                'title' => 'Edit Data', // Title dinamis
                'html' => $html
            ]);
        }

        return $this->response->setBody($html);
    }

    /**
     * Process the updating, full or partial, of a specific resource object.
     * This should be a POST.
     *
     * @param int|string|null $id
     *
     * @return ResponseInterface
     */
    public function update($id = null)
    {
        $userModel = new UserModel();
        $userRoleModel = new UserRoleModel();

        // 1. Mengambil data dinamis untuk role
        $roleModel = new RolesModel();
        $allowedRoles = $roleModel->getAllRoles();

        // 2. Mengubah data array menjadi 'comma-separated-value': "admin,operator,verifikator"
        $listString = implode(',', array_column($allowedRoles, 'id'));

        // Validation rules
        $rules = [
            'username' => 'required|min_length[3]|max_length[150]',
            'role_id' => "required|in_list[$listString]",
            'email' => 'required|valid_email|max_length[255]',
            'password' => 'permit_empty|min_length[6]'
        ];

        if (!$this->validate($rules)) {
            return $this->response->setStatusCode(400)->setJSON([
                'status' => 400,
                'message' => 'Validasi gagal',
                'errors' => $this->validator->getErrors()
            ]);
        }

        $data = $this->validator->getValidated();

        // Prepare update data safely (avoid undefined array keys)
        $updateDataUser = [
            'username' => $data['username'] ?? null,
            'email' => $data['email'] ?? null,
            'updated_at' => date('Y-m-d H:i:s'),
        ];

        // Only update password if provided and not empty
        if (isset($data['password']) && $data['password'] !== '') {
            $updateDataUser['password'] = password_hash($data['password'], PASSWORD_BCRYPT);
        }

        // Skip model validation here because controller already validated inputs and model has stricter rules (e.g. password required)
        $userModel->skipValidation(true);

        if ($userModel->update($id, $updateDataUser)) {
            $userRoleModel->where('user_id', $id)->set(['role_id' => $data['role_id']])->update();
            return $this->response->setStatusCode(200)->setJSON([
                'status' => 200,
                'message' => 'User berhasil diupdate',
                'data' => $updateDataUser
            ]);
        }

        return $this->response->setStatusCode(500)->setJSON([
            'status' => 500,
            'message' => 'Gagal mengupdate user'
        ]);
    }

    /**
     * Present a view to confirm the deletion of a specific resource object.
     *
     * @param int|string|null $id
     *
     * @return ResponseInterface
     */
    public function remove($id = null)
    {
        //
    }

    /**
     * Process the deletion of a specific resource object.
     *
     * @param int|string|null $id
     *
     * @return ResponseInterface
     */
    public function delete($id = null)
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
