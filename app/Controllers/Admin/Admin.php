<?php

namespace App\Controllers\Admin;

use Amp\Parallel\Worker\Internal\TaskSubmission;
use App\Controllers\BaseController;
use App\Models\TaskSubmissionModel;
use App\Models\UserRoleModel;

class Admin extends BaseController
{
    public function index()
    {
        // Check user info
        if (session()->has('jwt')) {
            $decode = $this->jwt->decode(session()->get('jwt'));
            $role = $decode['user_role'] ?? $decode['slug'] ?? '';
            if (time() > ($decode['expire_time'] ?? 0) || $role !== 'admin') {
                return redirect()->to('auth/login');
            }
        } else {
            return redirect()->to('auth/login');
        }

        $sent_data = [
            'page_title' => 'Administrator Page',
            'user_info' => $this->jwt->decode(session()->get('jwt')),
        ];

        return view('admin/index', $sent_data);
    }

    public function get_stats()
    {
        $userRoleModel = new UserRoleModel();

        $data = [
            'total_users' => $userRoleModel->countAllResults(),
            'admin_count' => $userRoleModel->where('role_id', 1)->countAllResults(),
            'operator_count' => $userRoleModel->where('role_id', 2)->countAllResults(),
            'verifikator_count' => $userRoleModel->where('role_id', 3)->countAllResults(),
        ];

        return $this->response->setJSON([
            'status' => 200,
            'data' => $data
        ]);
    }

    public function get_room_visits()
    {
        $taskSubmissionModel = new TaskSubmissionModel();

        // Get date parameter, default to today if not provided
        $date = $this->request->getGet('date') ?? date('Y-m-d');

        $result = $taskSubmissionModel->get_visit_frequency(esc($date));

        return $this->response->setJSON([
            'status' => 200,
            'data' => $result
        ]);
    }
}
