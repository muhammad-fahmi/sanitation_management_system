<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\UserModel;

class Dashboard extends BaseController
{
    public function index()
    {
        // Check user info
        if (session()->has('jwt')) {
            $decode = $this->jwt->decode(session()->get('jwt'));
            if (time() > $decode['expire_time'] || $decode['user_role'] != 'administrator') {
                return redirect()->to('auth/login');
            }
        } else {
            return redirect()->to('auth/login');
        }

        $sent_data = [
            'page_title' => 'Administrator Page',
            'user_info' => $this->jwt->decode(session()->get('jwt')),
        ];

        return view('admin/vw_dashboard_admin', $sent_data);
    }

    public function get_stats()
    {
        $userModel = new UserModel();

        $data = [
            'total_users' => $userModel->countAllResults(),
            'admin_count' => $userModel->where('user_role', 'administrator')->countAllResults(),
            'operator_count' => $userModel->where('user_role', 'operator')->countAllResults(),
            'verifikator_count' => $userModel->where('user_role', 'verifikator')->countAllResults(),
        ];

        return $this->response->setJSON([
            'status' => 200,
            'data' => $data
        ]);
    }

    public function get_room_visits()
    {
        $db = \Config\Database::connect();

        $result = $db->table('m_locations AS ml')
            ->select('ml.location_name, COUNT(rts.task_submission_id) AS visit_count')
            ->join('r_task_submission AS rts', 'rts.location_id = ml.location_id')
            ->groupBy('ml.location_id, ml.location_name')
            ->orderBy('visit_count', 'DESC')
            ->get()
            ->getResultArray();

        return $this->response->setJSON([
            'status' => 200,
            'data' => $result
        ]);
    }
}
