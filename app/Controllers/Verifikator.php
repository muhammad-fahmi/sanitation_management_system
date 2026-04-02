<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\TaskSubmissionModel;

class Verifikator extends BaseController
{
    private ?bool $hasRevisionImageColumn = null;
    private ?bool $hasUniqueCodeColumn = null;

    private function canUseUniqueCodeColumn(): bool
    {
        if ($this->hasUniqueCodeColumn !== null) {
            return $this->hasUniqueCodeColumn;
        }

        try {
            $db = \Config\Database::connect();
            $this->hasUniqueCodeColumn = $db->fieldExists('unique_code', 'r_task_submission');
        } catch (\Throwable $e) {
            $this->hasUniqueCodeColumn = false;
        }

        return $this->hasUniqueCodeColumn;
    }

    private function canUseRevisionImageColumn(): bool
    {
        if ($this->hasRevisionImageColumn !== null) {
            return $this->hasRevisionImageColumn;
        }

        try {
            $db = \Config\Database::connect();
            $this->hasRevisionImageColumn = $db->fieldExists('revision_image_path', 'r_task_submission');
        } catch (\Throwable $e) {
            $this->hasRevisionImageColumn = false;
        }

        return $this->hasRevisionImageColumn;
    }

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
                2 => 'rts.date',
                3 => 'mi.item_name',
                4 => 'ma.action_name',
                5 => 'ml.location_name',
                6 => 'rts.status'
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

    public function rekapitulasi()
    {
        if (!session()->has('jwt')) {
            return redirect()->to('auth/login');
        }
        $decode = $this->jwt->decode(session()->get('jwt'));
        if (time() > $decode['expire_time'] || $decode['user_role'] != 'verifikator') {
            return redirect()->to('auth/login');
        }

        $sent_data = [
            'page_title' => 'Rekapitulasi',
            'user_info' => $decode,
        ];

        return view('verifikator/vw_rekapitulasi', $sent_data);
    }

    public function get_rekapitulasi_summary()
    {
        if (!session()->has('jwt')) {
            return $this->response->setStatusCode(401)->setJSON([
                'success' => false,
                'message' => 'Unauthorized',
            ]);
        }

        $decode = $this->jwt->decode(session()->get('jwt'));
        if (time() > $decode['expire_time'] || $decode['user_role'] != 'verifikator') {
            return $this->response->setStatusCode(401)->setJSON([
                'success' => false,
                'message' => 'Unauthorized',
            ]);
        }

        $taskSubmissionModel = new TaskSubmissionModel();
        $rows = $taskSubmissionModel->builder('r_task_submission')
            ->select('LOWER(status) AS status, COUNT(*) AS total', false)
            ->groupBy('LOWER(status)')
            ->get()
            ->getResultArray();

        $summary = [
            'pending' => 0,
            'revisi' => 0,
            'verified' => 0,
        ];

        foreach ($rows as $row) {
            $status = (string) ($row['status'] ?? '');
            $total = (int) ($row['total'] ?? 0);

            if ($status === 'pending') {
                $summary['pending'] += $total;
                continue;
            }

            if (in_array($status, ['revisi', 'revised', 'revise'], true)) {
                $summary['revisi'] += $total;
                continue;
            }

            if (in_array($status, ['verified', 'selesai'], true)) {
                $summary['verified'] += $total;
            }
        }

        return $this->response->setJSON([
            'success' => true,
            'data' => $summary,
        ]);
    }

    public function verify_all()
    {
        $jwt = session()->get('jwt');
        if (!$jwt) {
            return $this->response->setStatusCode(401)->setJSON(['success' => false, 'message' => 'Unauthorized']);
        }

        $user = $this->jwt->decode($jwt);
        if (time() > $user['expire_time'] || ($user['user_role'] ?? '') !== 'verifikator') {
            return $this->response->setStatusCode(401)->setJSON(['success' => false, 'message' => 'Unauthorized']);
        }

        $filters = [
            'location_id' => $this->request->getPost('location_id') ?? '0',
            'date' => $this->request->getPost('date') ?? '0',
            'search' => trim((string) ($this->request->getPost('search') ?? '')),
        ];

        $taskSubmissionModel = new TaskSubmissionModel();
        $updatedCount = $taskSubmissionModel->verifyPendingTasks($filters, isset($user['user_id']) ? (int) $user['user_id'] : null);

        if ($updatedCount === 0) {
            return $this->response->setJSON([
                'success' => true,
                'message' => 'Tidak ada tugas pending yang dapat diverifikasi.',
                'updated_count' => 0,
            ]);
        }

        return $this->response->setJSON([
            'success' => true,
            'message' => $updatedCount . ' tugas pending berhasil diverifikasi.',
            'updated_count' => $updatedCount,
        ]);
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
        $db = \Config\Database::connect();
        $hasUniqueCode = $this->canUseUniqueCodeColumn();
        $actionNamesAggregate = str_contains(strtolower($db->DBDriver ?? ''), 'postgre')
            ? "STRING_AGG(DISTINCT ma.action_name, ', ' ORDER BY ma.action_name) AS action_names"
            : 'GROUP_CONCAT(DISTINCT ma.action_name ORDER BY ma.action_name SEPARATOR ", ") AS action_names';

        $uniqueCodeSelect = $hasUniqueCode ? 'rts.unique_code' : "'' AS unique_code";

        $baseSelect = '
                rts.task_submission_id,
                rts.date,
                rts.location_id,
                rts.item_id,
                rts.status,
            ' . $uniqueCodeSelect . ',
                rts.revision_message,
                ml.location_name,
                mi.item_name,
                ' . $actionNamesAggregate;

        if ($this->canUseRevisionImageColumn()) {
            $baseSelect = '
                rts.task_submission_id,
                rts.date,
                rts.location_id,
                rts.item_id,
                rts.status,
                ' . $uniqueCodeSelect . ',
                rts.revision_message,
                rts.revision_image_path,
                ml.location_name,
                mi.item_name,
                ' . $actionNamesAggregate;
        }

        $groupByParts = [
            'rts.task_submission_id',
            'rts.date',
            'rts.location_id',
            'rts.item_id',
            'rts.status',
            'rts.revision_message',
            'ml.location_name',
            'mi.item_name',
        ];

        if ($hasUniqueCode) {
            $groupByParts[] = 'rts.unique_code';
        }

        if ($this->canUseRevisionImageColumn()) {
            $groupByParts[] = 'rts.revision_image_path';
        }

        $groupBy = implode(', ', $groupByParts);

        $taskDetails = $taskSubmissionModel->builder('r_task_submission AS rts')
            ->select($baseSelect, false)
            ->join('m_locations AS ml', 'ml.location_id = rts.location_id', 'left')
            ->join('m_items AS mi', 'mi.item_id = rts.item_id', 'left')
            ->join('r_task_submission_detail AS rtsd', 'rts.task_submission_id = rtsd.task_submission_id', 'left')
            ->join('m_actions AS ma', 'ma.action_id = rtsd.action_id', 'left')
            ->where('rts.task_submission_id', $id)
            ->groupBy($groupBy)
            ->get()
            ->getRowArray();

        if (!$taskDetails) {
            return $this->response->setJSON(['success' => false, 'message' => 'Task details not found']);
        }

        if (!$this->canUseRevisionImageColumn()) {
            $taskDetails['revision_image_path'] = null;
        } elseif (empty($taskDetails['revision_image_path']) && !empty($task['revision_image_path'])) {
            // Fallback from base task row in case aggregation query omits the value.
            $taskDetails['revision_image_path'] = $task['revision_image_path'];
        }

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
            $revisionImagePath = $task['revision_image_path'] ?? null;
            $imageFile = $this->request->getFile('revise_image');

            $canUseRevisionImage = $this->canUseRevisionImageColumn();

            if ($imageFile && $imageFile->isValid() && !$imageFile->hasMoved()) {
                if (!$canUseRevisionImage) {
                    // Gracefully continue update without image when schema is not ready.
                    $imageFile = null;
                }
            }

            if ($imageFile && $imageFile->isValid() && !$imageFile->hasMoved()) {
                $allowedExt = ['jpg', 'jpeg', 'png', 'webp'];
                $ext = strtolower((string) $imageFile->getExtension());

                if (!in_array($ext, $allowedExt, true)) {
                    return $this->response->setStatusCode(400)->setJSON([
                        'success' => false,
                        'message' => 'Format gambar tidak didukung. Gunakan jpg, jpeg, png, atau webp.',
                    ]);
                }

                if ($imageFile->getSizeByUnit('mb') > 5) {
                    return $this->response->setStatusCode(400)->setJSON([
                        'success' => false,
                        'message' => 'Ukuran gambar maksimal 5 MB.',
                    ]);
                }

                $uploadDir = FCPATH . 'uploads/revisions';
                if (!is_dir($uploadDir)) {
                    mkdir($uploadDir, 0755, true);
                }

                $newFileName = $imageFile->getRandomName();
                $imageFile->move($uploadDir, $newFileName);
                $revisionImagePath = 'uploads/revisions/' . $newFileName;

                if (!empty($task['revision_image_path'])) {
                    $oldPath = FCPATH . ltrim((string) $task['revision_image_path'], '/\\');
                    if (is_file($oldPath)) {
                        @unlink($oldPath);
                    }
                }
            }

            $data = [
                // Mark as revised so downstream views can render the "Revisi" badge
                'status' => 'revised',
                'revision_message' => $revise_description,
                'verified_by' => null,
                'verified_at' => null
            ];

            if ($canUseRevisionImage) {
                $data['revision_image_path'] = $revisionImagePath;
            }
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
