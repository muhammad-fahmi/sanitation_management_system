<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\TaskSubmissionModel;

class Verifikator extends BaseController
{
    public function index()
    {
        // check JWT session to retrieve info
        if (!session()->has('jwt')) {
            return redirect()->to('auth/login');
        }
        $decode = $this->jwt->decode(session()->get('jwt'));
        if (time() > $decode['expire_time'] || $decode['user_role'] != 'verifikator') {
            return redirect()->to('auth/login');
        }

        $sent_data = [
            'page_title' => 'Verifikator Page',
            'user_info' => $this->jwt->decode(session()->get('jwt')),
        ];

        return view('verifikator/vw_dashboard', $sent_data);
    }

    public function verified()
    {
        // check JWT session to retrieve info
        if (!session()->has('jwt')) {
            return redirect()->to('auth/login');
        }
        $decode = $this->jwt->decode(session()->get('jwt'));
        if (time() > $decode['expire_time'] || $decode['user_role'] != 'verifikator') {
            return redirect()->to('auth/login');
        }

        $sent_data = [
            'page_title' => 'Tugas Terverifikasi',
            'user_info' => $this->jwt->decode(session()->get('jwt')),
        ];

        return view('verifikator/vw_verified', $sent_data);
    }

    public function get_datatable()
    {
        $taskSubmissionModel = new TaskSubmissionModel();

        $data = [
            'draw' => (int) ($this->request->getPost('draw') ?? 0),
            'start' => (int) ($this->request->getPost('start') ?? 0),
            'length' => (int) ($this->request->getPost('length') ?? 10),
            'search' => $this->request->getPost('search')['value'] ?? '',
            'location_id' => $this->request->getPost('location_id') ?? '0',
            'order_column' => '',
            'order_sort' => ''
        ];

        // Handle ordering
        $order = $this->request->getVar('order');
        if ($order && is_array($order) && isset($order[0])) {
            $columns = [
                1 => 'rts.date',
                2 => 'mi.name',
                3 => 'ma.action_name',
                4 => 'rm.name',
                5 => 'rts.status'
            ];
            $columnIndex = $order[0]['column'];
            $columnSortOrder = $order[0]['dir'];

            if (isset($columns[$columnIndex])) {
                $data['order_column'] = $columns[$columnIndex];
                $data['order_sort'] = $columnSortOrder;
            }
        }

        $result = $taskSubmissionModel->getSubmittedTasks($data);

        return $this->response->setJSON($result);
    }

    public function get_verified_datatable()
    {
        $taskSubmissionModel = new TaskSubmissionModel();

        $data = [
            'draw' => (int) ($this->request->getPost('draw') ?? 0),
            'start' => (int) ($this->request->getPost('start') ?? 0),
            'length' => (int) ($this->request->getPost('length') ?? 10),
            'search' => $this->request->getPost('search')['value'] ?? '',
            'location_id' => $this->request->getPost('location_id') ?? '0',
            'order_column' => '',
            'order_sort' => ''
        ];

        // Handle ordering
        $order = $this->request->getVar('order');
        if ($order && is_array($order) && isset($order[0])) {
            $columns = [
                1 => 'rts.date',
                2 => 'mi.name',
                3 => 'ma.action_name',
                4 => 'rm.name',
                5 => 'rts.status',
                6 => 'verified_by_name'
            ];
            $columnIndex = $order[0]['column'];
            $columnSortOrder = $order[0]['dir'];

            if (isset($columns[$columnIndex])) {
                $data['order_column'] = $columns[$columnIndex];
                $data['order_sort'] = $columnSortOrder;
            }
        }

        $result = $taskSubmissionModel->getVerifiedTasks($data);

        return $this->response->setJSON($result);
    }

    public function get_submitted_task()
    {
        // return $this->
    }

    public function modal()
    {
        $id = $this->request->getPost('id');
        if (!$id) {
            return $this->response->setJSON(['success' => false, 'message' => 'Task id is required']);
        }

        $taskSubmissionModel = new TaskSubmissionModel();
        $task = $taskSubmissionModel->find($id);

        if (!$task) {
            return $this->response->setJSON(['success' => false, 'message' => 'Task not found']);
        }

        // Get full details with joins (use current tables; return legacy keys)
        $taskDetails = $taskSubmissionModel->builder('task_submissions AS ts')
            ->select('
                ts.id AS task_submission_id,
                ts.date,
                ts.room_id AS location_id,
                tsi.item_id,
                ts.status,
                ts.revision_message,
                rm.name AS location_name,
                mi.name AS item_name,
                GROUP_CONCAT(DISTINCT ma.action_name ORDER BY ma.action_name SEPARATOR ", ") AS action_names
            ')
            ->join('task_submission_items AS tsi', 'tsi.task_submission_id = ts.id', 'left')
            ->join('task_submission_actions AS tsa', 'tsa.task_submission_item_id = tsi.id', 'left')
            ->join('m_actions AS ma', 'ma.action_id = tsa.action_id', 'left')
            ->join('rooms AS rm', 'rm.id = ts.room_id', 'left')
            ->join('items AS mi', 'mi.id = tsi.item_id', 'left')
            ->where('ts.id', $id)
            ->groupBy('ts.id, ts.date, ts.room_id, tsi.item_id, ts.status, ts.revision_message, rm.name, mi.name')
            ->get()
            ->getRowArray();

        return $this->response->setJSON(['success' => true, 'data' => $taskDetails]);
    }

    public function update()
    {
        $id = $this->request->getPost('id');
        $action = $this->request->getPost('action'); // 'verifikasi' or 'revisi'
        $revise_description = $this->request->getPost('revise_description') ?? '';

        $jwt = session()->get('jwt');
        if (!$jwt) {
            return $this->response->setStatusCode(401)->setJSON(['success' => false, 'message' => 'Unauthorized']);
        }

        $user = $this->jwt->decode($jwt);

        $taskSubmissionModel = new TaskSubmissionModel();
        $task = $taskSubmissionModel->find($id);

        if (!$task) {
            return $this->response->setStatusCode(404)->setJSON(['success' => false, 'message' => 'Task not found']);
        }

        if ($action === 'verifikasi') {
            $data = [
                'status' => 'approved',
                'verified_by' => $user['user_id'] ?? $user['id'] ?? null,
                'verified_at' => date('Y-m-d H:i:s')
            ];
        } elseif ($action === 'revisi') {
            $data = [
                // Mark as revision requested so downstream views can render the "Revisi" badge
                'status' => 'revision_requested',
                'revision_message' => $revise_description,
                'verified_by' => null,
                'verified_at' => null
            ];
        } else {
            return $this->response->setStatusCode(400)->setJSON(['success' => false, 'message' => 'Invalid action']);
        }

        $updated = $taskSubmissionModel->update($id, $data);

        if ($updated) {
            return $this->response->setJSON(['success' => true, 'message' => 'Status updated successfully']);
        }

        return $this->response->setStatusCode(500)->setJSON(['success' => false, 'message' => 'Failed to update status']);
    }
}
