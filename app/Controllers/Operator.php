<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\ActionModel;
use App\Models\ItemModel;
use App\Models\RoomModel;
use App\Models\TaskSubmissionModel;
use App\Models\TaskSubmissionItemsModel;
use App\Models\TaskSubmissionActionsModel;

class Operator extends BaseController
{
    /**
     * Display operator dashboard with available rooms
     */
    public function index()
    {
        $location = new RoomModel();
        $taskSubmission = new TaskSubmissionModel();
        $rooms = $location->getAllRoom();
        $today = date('Y-m-d');

        // Preload submission and revision locations for today to avoid per-room queries
        $submittedLocationIds = $taskSubmission->select('room_id')
            ->where('DATE(date)', $today)
            ->groupBy('room_id')
            ->findColumn('room_id') ?? [];

        $revisionLocationIds = $taskSubmission->select('room_id')
            ->where('DATE(date)', $today)
            ->whereIn('status', ['revision_requested', 'rejected'])
            ->groupBy('room_id')
            ->findColumn('room_id') ?? [];

        // Store revision room count in session for use in layout
        session()->set('revision_room_count', count($revisionLocationIds));

        // Mark rooms that already have submissions today and those with revisions
        foreach ($rooms as &$room) {
            $roomId = $room['location_id'];
            $room['submitted'] = in_array($roomId, $submittedLocationIds, true);
            $room['has_revision'] = in_array($roomId, $revisionLocationIds, true);
        }

        return view('operator/vw_dashboard', [
            'page_title' => 'Dashboard User',
            'user_info' => $this->jwt->decode(session()->get('jwt')),
            'rooms' => $rooms
        ]);
    }

    /**
     * Display QR scan page with items for a specific location
     */
    public function scan($location_id = null)
    {
        if (!$location_id) {
            return redirect()->to('operator')->with('error', 'Location tidak ditemukan');
        }

        $itemModel = new ItemModel();
        $locationModel = new RoomModel();
        $taskSubmissionModel = new TaskSubmissionModel();
        $actionModel = new ActionModel();

        $items = $itemModel->getRoomItem($location_id);
        $locations = $locationModel->find($location_id);

        if (!$locations) {
            return redirect()->to('operator')->with('error', 'Lokasi tidak ditemukan');
        }

        // Ensure legacy field for views
        if (!isset($locations['location_name'])) {
            $locations['location_name'] = $locations['name'] ?? null;
            $locations['location_id'] = $locations['id'] ?? null;
        }

        $today = date('Y-m-d');
        $submissions = $taskSubmissionModel->getSubmissionsWithDetails($location_id, $today);

        // Fetch items with revision status (revision_requested/rejected)
        $db = \Config\Database::connect();
        $revisionItemIds = $db->table('task_submissions AS ts')
            ->select('tsi.item_id')
            ->join('task_submission_items AS tsi', 'tsi.task_submission_id = ts.id', 'left')
            ->where('ts.room_id', $location_id)
            ->where('DATE(ts.date)', $today)
            ->whereIn('ts.status', ['revision_requested', 'rejected'])
            ->groupBy('tsi.item_id')
            ->get()
            ->getResultArray();
        $revisionItemIdList = array_column($revisionItemIds, 'item_id');

        return view('operator/vw_qr_scan', [
            'page_title' => 'Scan Page',
            'user_info' => $this->jwt->decode(session()->get('jwt')),
            'items' => $items,
            'locations' => $locations,
            'actions' => $actionModel->findAll(),
            'submissions' => $submissions,
            'revision_items' => $revisionItemIdList
        ]);
    }

    /**
     * Return modal content based on type
     */
    public function modal()
    {
        $id = $this->request->getVar('id');
        $location_id = $this->request->getVar('location_id');
        $type = $this->request->getVar('type');

        if ($type === 'detail') {
            $actionModel = new ActionModel();
            $actions = $actionModel->where('item_id', $id)->findAll();

            // Fetch default clean action from m_actions with item_id=999 and action_name='sudah bersih'
            $cleanActionId = null;
            $defaultActionModel = new ActionModel();
            $defaultClean = $defaultActionModel
                ->where('item_id', 999)
                ->where('action_name', 'sudah bersih')
                ->first();

            if ($defaultClean && isset($defaultClean['action_id'])) {
                $cleanActionId = (int) $defaultClean['action_id'];
            }

            return $this->response->setJSON([
                'status' => 200,
                'title' => 'List Aksi',
                'html' => view('operator/modal/vw_modal_list_action', [
                    'actions' => $actions,
                    'item_id' => $id,
                    'location_id' => $location_id,
                    'clean_action_id' => $cleanActionId
                ])
            ]);
        }

        return $this->response->setStatusCode(404)->setJSON([
            'status' => 404,
            'message' => 'Modal tidak ditemukan'
        ]);
    }

    /**
     * Save task submission with error handling and validation
     */
    public function add_submission()
    {
        $user_id = $this->request->getVar('user_id');
        $submissions_json = $this->request->getVar('submissions');

        if (!$user_id) {
            return $this->response->setStatusCode(400)->setJSON(['status' => 400, 'message' => 'User ID tidak valid']);
        }

        $taskSubmissionModel = new TaskSubmissionModel();
        $taskSubmissionDetailModel = new TaskSubmissionActionsModel();

        if ($submissions_json) {
            $submissions = json_decode($submissions_json, true);

            if (!$submissions || empty($submissions)) {
                return $this->response->setStatusCode(400)->setJSON(['status' => 400, 'message' => 'Data submission tidak valid']);
            }

            $location_id = $submissions[0]['location_id'];
            $date = $submissions[0]['date'];

            // Fetch existing submissions for this location/date (join items to find per-item existing records)
            $db = \Config\Database::connect();
            $existingRows = $db->table('task_submissions AS ts')
                ->select('tsi.item_id, tsi.id AS task_submission_item_id, ts.id AS task_submission_id, ts.status, tsi.cleaning_frequency')
                ->join('task_submission_items AS tsi', 'tsi.task_submission_id = ts.id', 'left')
                ->where('ts.room_id', $location_id)
                ->where('DATE(ts.date)', $date)
                ->get()
                ->getResultArray();

            $existingTasksByItem = [];
            foreach ($existingRows as $ex) {
                $existingTasksByItem[(int) $ex['item_id']] = $ex;
            }

            // Group new submissions by item
            $grouped = [];
            foreach ($submissions as $submission) {
                $itemId = (int) $submission['item_id'];
                if (!isset($grouped[$itemId])) {
                    $grouped[$itemId] = [
                        'date' => $submission['date'],
                        'location_id' => (int) $submission['location_id'],
                        'item_id' => $itemId,
                        'submitted_by' => (int) $user_id,
                        'actions' => []
                    ];
                }

                $aid = $submission['action_id'];
                if (is_numeric($aid)) {
                    $grouped[$itemId]['actions'][] = (int) $aid;
                }
            }

            // Process submissions: update existing per-item records or create new submissions + items + actions
            foreach ($grouped as $itemId => $payload) {
                if (isset($existingTasksByItem[$itemId])) {
                    // Update existing task item and parent submission
                    $existingTask = $existingTasksByItem[$itemId];
                    $taskId = $existingTask['task_submission_id'];
                    $taskItemId = $existingTask['task_submission_item_id'];

                    // If status was revision requested or rejected, move back to pending_review
                    $currentStatus = $existingTask['status'] ?? null;
                    $newStatus = in_array($currentStatus, ['revision_requested', 'rejected'], true) ? 'pending_review' : $currentStatus;

                    if ($newStatus !== $currentStatus) {
                        $taskSubmissionModel->update($taskId, ['status' => $newStatus, 'verified_by' => null, 'verified_at' => null]);
                    }

                    // For each action, increment repetitions if exists or insert new action
                    foreach ($payload['actions'] as $actionId) {
                        $existingDetail = $taskSubmissionDetailModel->where('task_submission_item_id', $taskItemId)
                            ->where('action_id', $actionId)
                            ->first();

                        if ($existingDetail) {
                            $newReps = (int) ($existingDetail['repetitions'] ?? 1) + 1;
                            $db->table('task_submission_actions')->where('id', $existingDetail['id'])->update(['repetitions' => $newReps]);
                        } else {
                            $db->table('task_submission_actions')->insert(['task_submission_item_id' => $taskItemId, 'action_id' => $actionId, 'repetitions' => 1]);
                        }
                    }
                } else {
                    // Create a new parent task submission for this room/date
                    $data = [
                        'date' => $payload['date'],
                        'room_id' => $payload['location_id'],
                        'status' => 'pending_review',
                        'submitted_by' => $payload['submitted_by']
                    ];

                    $taskId = $taskSubmissionModel->insert($data);

                    // Insert task_submission_item
                    $db->table('task_submission_items')->insert(['task_submission_id' => $taskId, 'item_id' => $payload['item_id'], 'cleaning_frequency' => 1]);
                    $taskItemId = $db->insertID();

                    // Insert actions under that item
                    foreach ($payload['actions'] as $actionId) {
                        $db->table('task_submission_actions')->insert(['task_submission_item_id' => $taskItemId, 'action_id' => $actionId, 'repetitions' => 1]);
                    }
                }
            }

            return $this->response->setJSON(['status' => 200, 'message' => 'Submission berhasil disimpan']);
        }

        return $this->response->setStatusCode(400)->setJSON(['status' => 400, 'message' => 'Data submission tidak ditemukan']);
    }

    /**
     * Increment location visit count
     */
    public function increment_visit($location_id)
    {
        $jwt = session()->get('jwt');

        if (!$jwt) {
            return $this->response->setStatusCode(401)->setJSON(['error' => 'Unauthorized']);
        }

        $user = $this->jwt->decode($jwt);
        if (!isset($user['user_id'])) {
            return $this->response->setStatusCode(400)->setJSON(['error' => 'Invalid user']);
        }

        // Visit tracking no longer writes to r_task_submission to avoid schema mismatches
        return $this->response->setJSON(['status' => 'success']);
    }

    /**
     * Cancel a recorded action (delete action -> possibly delete parent item and submission if empty)
     */
    public function cancel_submission($action_id)
    {
        if (!$action_id) {
            return;
        }

        $taskSubmissionActionsModel = new TaskSubmissionActionsModel();
        $taskSubmissionItemsModel = new TaskSubmissionItemsModel();
        $taskSubmissionModel = new TaskSubmissionModel();

        // Try to find by primary id first
        $action = $taskSubmissionActionsModel->find($action_id);
        // Fallback: maybe caller passed the action_id (domain action) instead of record id
        if (!$action) {
            $action = $taskSubmissionActionsModel->where('action_id', $action_id)->first();
        }

        if (!$action) {
            return;
        }

        $taskItemId = $action['task_submission_item_id'];
        $taskItem = $taskSubmissionItemsModel->find($taskItemId);
        if (!$taskItem) {
            // delete the action if orphaned
            $taskSubmissionActionsModel->delete($action['id']);
            return;
        }

        $taskSubmissionId = $taskItem['task_submission_id'];

        // delete the action record
        $taskSubmissionActionsModel->delete($action['id']);

        // if no more actions for the item, delete the item
        $remainingActions = $taskSubmissionActionsModel->where('task_submission_item_id', $taskItemId)->countAllResults();
        if ($remainingActions === 0) {
            $taskSubmissionItemsModel->delete($taskItemId);

            // if no more items for the submission, delete the submission
            $remainingItems = $taskSubmissionItemsModel->where('task_submission_id', $taskSubmissionId)->countAllResults();
            if ($remainingItems === 0) {
                $taskSubmissionModel->delete($taskSubmissionId);
            }
        }
    }

    /**
     * Display revision tasks for operator
     */
    public function revisi()
    {
        $user = $this->jwt->decode(session()->get('jwt'));

        // Get submissions that need revision with optimized query
        $db = \Config\Database::connect();
        $revisedSubmissions = $db->table('task_submissions AS ts')
            ->select('
                ts.id AS task_submission_id,
                ts.date,
                ts.room_id AS location_id,
                tsi.item_id,
                ts.revision_message,
                ts.status,
                ts.submitted_by,
                rm.name AS location_name,
                mi.name AS item_name,
                GROUP_CONCAT(DISTINCT ma.action_name ORDER BY ma.action_name SEPARATOR ", ") AS action_names
            ')
            ->join('task_submission_items AS tsi', 'tsi.task_submission_id = ts.id')
            ->join('task_submission_actions AS tsa', 'tsa.task_submission_item_id = tsi.id', 'left')
            ->join('m_actions AS ma', 'ma.action_id = tsa.action_id', 'left')
            ->join('rooms AS rm', 'rm.id = ts.room_id', 'left')
            ->join('items AS mi', 'mi.id = tsi.item_id', 'left')
            ->whereIn('ts.status', ['revision_requested', 'rejected'])
            ->groupBy('ts.id, ts.date, ts.room_id, tsi.item_id, ts.revision_message, ts.status, ts.submitted_by, rm.location_name, mi.item_name')
            ->orderBy('ts.date', 'DESC')
            ->get()
            ->getResultArray();

        // Count unique locations needing revision
        $revisionRoomCount = count(array_unique(array_column($revisedSubmissions, 'location_id')));

        // Store revision count in session for use in layout
        session()->set('revision_room_count', $revisionRoomCount);

        return view('operator/vw_revisi', [
            'page_title' => 'Revisi Tugas',
            'user_info' => $user,
            'submissions' => $revisedSubmissions,
            'items' => (new ItemModel())->findAll(),
            'actions' => (new ActionModel())->findAll(),
            'locations' => (new RoomModel())->findAll(),
            'revision_room_count' => $revisionRoomCount
        ]);
    }

    /**
     * Get revision room count via AJAX for badge update
     */
    public function get_revision_count()
    {
        $taskSubmissionModel = new TaskSubmissionModel();

        // Get count of unique rooms with revisions
        $revisions = $taskSubmissionModel->select('room_id')
            ->whereIn('status', ['revision_requested', 'rejected'])
            ->groupBy('room_id')
            ->findAll();

        $revisionRoomCount = count($revisions);

        // Update session
        session()->set('revision_room_count', $revisionRoomCount);

        return $this->response->setJSON([
            'status' => 200,
            'count' => $revisionRoomCount
        ]);
    }
}
