<?php

namespace App\Controllers;

use App\Models\UserModel;
use App\Models\UserRoleModel;
use CodeIgniter\HTTP\IncomingRequest;

/**
 * @property IncomingRequest $request
 */
class Auth extends BaseController
{
    // Auth - Login Entry Point
    public function login()
    {
        if ($this->request->is('GET')) {
            if (session()->has('jwt')) {
                $jwt = $this->jwt->decode(session()->get('jwt'));
                if (time() <= $jwt['expire_time']) {
                    if ($jwt['slug'] == 'admin') {
                        $role = 'admin';
                    } else if ($jwt['slug'] == 'operator') {
                        $role = 'operator';
                    } else if ($jwt['slug'] == 'verifikator') {
                        $role = 'verifikator';
                    } else {
                        return redirect()->to('auth/login');
                    }
                    return redirect()->to($role);
                }
            }

            $sent_data = [
                'page_title' => "Login Page"
            ];
            return view('auth/vw_login', $sent_data);
        }

        // Guard clause for non-POST requests
        if (!$this->request->is('post')) {
            return redirect('auth/login');
        }

        // Input validation
        $validation = service('validation');
        $rules = [
            'username' => [
                'label' => 'Username',
                'rules' => 'required|min_length[3]|max_length[50]',
                'errors' => [
                    'required' => '{field} harus diisi',
                    'min_length' => '{field} minimal 3 karakter',
                    'max_length' => '{field} maksimal 50 karakter'
                ]
            ],
            'password' => [
                'label' => 'Password',
                'rules' => 'required|min_length[3]',
                'errors' => [
                    'required' => '{field} harus diisi',
                    'min_length' => '{field} minimal 3 karakter'
                ]
            ]
        ];

        $data = $this->request->getPost(array_keys($rules));

        // Run Validation
        if (!$this->validateData($data, $rules)) {
            // Validation failed, return to login page with errors
            return redirect()->to('/auth/login')
                ->withInput()
                ->with('errors', $validation->getErrors());
        }

        $validData = $this->validator->getValidated();

        $input_username = $validData['username'];
        $input_password = $validData['password'];

        $userModel = new UserModel();
        $userRoleModel = new UserRoleModel();
        $id_user = $userModel->where('username', $input_username)->select('id')->first()['id'];
        $user = $userRoleModel->getUserDetail($id_user);

        if (!$user) {
            // User not found
            return redirect()->to('/auth/login')
                ->withInput()
                ->with('error', 'Username atau password salah');
        }

        [
            'user_id' => $user_id,
            'username' => $username,
            'password' => $password,
            'slug' => $slug,
        ] = $user;

        if (!password_verify($input_password, $password)) {
            // Password incorrect
            return redirect()->to('/auth/login')
                ->withInput()
                ->with('error', 'Username atau password salah');
        }

        // Handle different user roles
        if ($slug == 'verifikator') {
            $data = [
                'user_id' => $user_id,
                'username' => $username,
                'slug' => $slug,
                'expire_time' => strtotime('+1 day', time())
            ];

            $this->jwt->encode($data);
            return redirect('verifikator');
        }

        if ($slug == 'admin') {
            $data = [
                'user_id' => $user_id,
                'username' => $username,
                'slug' => $slug,
                'expire_time' => strtotime('+1 day', time())
            ];

            $this->jwt->encode($data);
            return redirect('admin');
        }

        // Store shift info in session including dates for notification logic
        $data = [
            'user_id' => $user_id,
            'username' => $username,
            'slug' => $slug,
            'expire_time' => strtotime('+1 day', time())
        ];

        $this->jwt->encode($data);

        return redirect('operator');
    }

    public function logout()
    {
        session()->destroy();
        return redirect()->to('auth/login');
    }
}
