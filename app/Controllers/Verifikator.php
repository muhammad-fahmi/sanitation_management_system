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

    public function get_datatable()
    {
        $taskSubmissionModel = new TaskSubmissionModel();

        $data = [
            'draw' => (int) ($this->request->getPost('draw') ?? 0),
            'start' => (int) ($this->request->getPost('start') ?? 0),
            'length' => (int) ($this->request->getPost('length') ?? 10),
            'search' => $this->request->getPost('search')['value'] ?? '',
            'location_id' => $this->request->getPost('location_id') ?? '0',
            'date' => $this->request->getPost('date') ?? '0',
            'order_column' => '',
            'order_sort' => ''
        ];

        // Handle ordering
        $order = $this->request->getVar('order');
        if ($order && is_array($order) && isset($order[0])) {
            $columns = [
                1 => 'rts.date',
                2 => 'mi.item_name',
                3 => 'ma.action_name',
                4 => 'ml.location_name',
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

    public function get_locations()
    {
        $taskSubmissionModel = new TaskSubmissionModel();
        $date = $this->request->getPost('date') ?? '0';
        $locations = $taskSubmissionModel->getSubmittedLocations($date);

        return $this->response->setJSON([
            'success' => true,
            'data' => $locations,
        ]);
    }

    public function get_dates()
    {
        $taskSubmissionModel = new TaskSubmissionModel();
        $location_id = $this->request->getPost('location_id') ?? '0';
        $dates = $taskSubmissionModel->getSubmittedDates($location_id);

        return $this->response->setJSON([
            'success' => true,
            'data' => $dates,
        ]);
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

        // Get full details with joins
        $taskDetails = $taskSubmissionModel->builder('r_task_submission AS rts')
            ->select('
                rts.task_submission_id,
                rts.date,
                rts.location_id,
                rts.item_id,
                rts.status,
                rts.revision_message,
                ml.location_name,
                mi.item_name,
                GROUP_CONCAT(DISTINCT ma.action_name ORDER BY ma.action_name SEPARATOR ", ") AS action_names
            ')
            ->join('m_locations AS ml', 'ml.location_id = rts.location_id', 'left')
            ->join('m_items AS mi', 'mi.item_id = rts.item_id', 'left')
            ->join('r_task_submission_detail AS rtsd', 'rts.task_submission_id = rtsd.task_submission_id', 'left')
            ->join('m_actions AS ma', 'ma.action_id = rtsd.action_id', 'left')
            ->where('rts.task_submission_id', $id)
            ->groupBy('rts.task_submission_id, rts.date, rts.location_id, rts.item_id, rts.status, rts.revision_message, ml.location_name, mi.item_name')
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
                'status' => 'verified',
                'verified_by' => $user['user_id'] ?? null,
                'verified_at' => date('Y-m-d H:i:s')
            ];
        } elseif ($action === 'revisi') {
            $data = [
                // Mark as revised so downstream views can render the "Revisi" badge
                'status' => 'revised',
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
