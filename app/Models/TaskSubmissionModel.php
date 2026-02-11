<?php

namespace App\Models;

use CodeIgniter\Model;
use Tatter\Audits\Traits\AuditsTrait;

/**
 * @see AuditsTrait
 */
class TaskSubmissionModel extends Model
{
    use AuditsTrait;
    protected $table = 'task_submissions';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $useSoftDeletes = false;
    protected $protectFields = true;
    protected $allowedFields = [
        'submission_code',
        'date',
        'room_id',
        'count_visited',
        'revision_message',
        'status',
        'submitted_by',
        'verified_by',
        'verified_at',
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    protected bool $allowEmptyInserts = false;
    protected bool $updateOnlyChanged = true;

    protected array $casts = [];
    protected array $castHandlers = [];

    // Dates
    protected $useTimestamps = false;
    protected $dateFormat = 'datetime';
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';
    protected $deletedField = 'deleted_at';


    // Validation
    protected $validationRules = [];
    protected $validationMessages = [];
    protected $skipValidation = false;
    protected $cleanValidationRules = true;

    // Callbacks
    protected $allowCallbacks = true;
    protected $beforeInsert = [];
    protected $afterInsert = ['auditInsert'];
    protected $beforeUpdate = [];
    protected $afterUpdate = ['auditUpdate'];
    protected $beforeFind = [];
    protected $afterFind = [];
    protected $beforeDelete = [];
    protected $afterDelete = ['auditDelete'];

    public function get_visit_frequency($date)
    {
        return $this->builder('task_submissions ts')->select('rm.name, ts.visit_frequency')
            ->join('rooms as rm', 'rm.id = ts.room_id AND ts.date = "' . $date . '"', 'left')
            ->get()->getResultArray();
    }

    public function getSubmissionsWithDetails($room_id, $date)
    {
        $ts = $this->db->prefixTable('task_submissions');
        $tsi = $this->db->prefixTable('task_submission_items');
        $tsa = $this->db->prefixTable('task_submission_actions');

        return $this->db->query(
            "SELECT
                ts.id AS task_submission_id,
                ts.date,
                ts.room_id AS location_id,
                tsi.item_id,
                ts.submitted_by,
                tsi.cleaning_frequency AS time_cleaned,
                ts.status,
                tsa.id AS task_submission_action_id,
                tsa.action_id,
                tsa.repetitions AS quantity
            FROM {$ts} ts
            LEFT JOIN {$tsi} tsi ON tsi.task_submission_id = ts.id
            LEFT JOIN {$tsa} tsa ON tsa.task_submission_item_id = tsi.id
            WHERE ts.room_id = ? AND DATE(ts.date) = ?",
            [$room_id, $date]
        )->getResultArray();
    }

    public function getTotalsByLocationDate($room_id, $date)
    {
        $ts = $this->db->prefixTable('task_submissions');
        $tsi = $this->db->prefixTable('task_submission_items');
        $tsa = $this->db->prefixTable('task_submission_actions');

        $result = $this->db->query(
            "SELECT
                COUNT(DISTINCT tsi.item_id) AS items_cleaned_total,
                COUNT(DISTINCT tsa.action_id) AS actions_cleaned_total
            FROM {$ts} ts
            LEFT JOIN {$tsi} tsi ON tsi.task_submission_id = ts.id
            LEFT JOIN {$tsa} tsa ON tsa.task_submission_item_id = tsi.id
            WHERE ts.room_id = ? AND DATE(ts.date) = ?",
            [$room_id, $date]
        )->getRowArray();

        return $result ?: ['items_cleaned_total' => 0, 'actions_cleaned_total' => 0];
    }

    public function getSubmittedTasks($data = [])
    {
        // Use current tables but return legacy keys (task_submission_id, location_id, item_id, item_name, location_name)
        $baseBuilder = $this->builder('task_submissions AS rts')
            ->select('
                rts.id AS task_submission_id,
                rts.date,
                rts.room_id AS location_id,
                tsi.item_id,
                tsi.cleaning_frequency AS time_cleaned,
                rts.status,
                rts.revision_message,
                rts.submitted_by,
                rts.verified_by,
                rts.verified_at,
                rm.name AS location_name,
                mi.name AS item_name,
                GROUP_CONCAT(DISTINCT ma.action_name ORDER BY ma.action_name SEPARATOR ", ") AS action_name
            ')
            ->join('task_submission_items AS tsi', 'rts.id = tsi.task_submission_id', 'left')
            ->join('task_submission_actions AS tsa', 'tsa.task_submission_item_id = tsi.id', 'left')
            ->join('m_actions AS ma', 'ma.action_id = tsa.action_id', 'left')
            ->join('rooms AS rm', 'rm.id = rts.room_id', 'left')
            ->join('items AS mi', 'mi.id = tsi.item_id', 'left')
            ->whereIn('rts.status', ['pending_review', 'revision_requested'])
            ->groupBy('rts.id, rts.date, rts.room_id, tsi.item_id, tsi.cleaning_frequency, rts.status, rts.revision_message, rts.submitted_by, rts.verified_by, rts.verified_at, rm.name, mi.name');

        // Total count before filtering (distinct submissions)
        $totalCount = (clone $baseBuilder)->countAllResults();

        // Apply location filter if provided
        if (!empty($data['location_id']) && $data['location_id'] !== '0') {
            $baseBuilder->where('rts.location_id', (int) $data['location_id']);
        }

        // Apply search if provided
        if (!empty($data['search'])) {
            $search = $data['search'];
            $baseBuilder->groupStart()
                ->like('ml.location_name', $search)
                ->orLike('mi.item_name', $search)
                ->orLike('ma.action_name', $search)
                ->orLike('rts.status', $search)
                ->groupEnd();
        }

        // Filtered count after search
        $filterCount = (clone $baseBuilder)->countAllResults();

        // Apply ordering
        if (!empty($data['order_column']) && !empty($data['order_sort'])) {
            $baseBuilder->orderBy($data['order_column'], $data['order_sort']);
        } else {
            $baseBuilder->orderBy('rts.date', 'DESC')->orderBy('rts.task_submission_id', 'DESC');
        }

        // Apply pagination
        if (isset($data['length']) && isset($data['start'])) {
            $queryResult = $baseBuilder->get((int) $data['length'], (int) $data['start'])->getResultArray();
        } else {
            $queryResult = $baseBuilder->get()->getResultArray();
        }

        // Add row numbering and convenience id field
        $no = isset($data['start']) ? ((int) $data['start']) + 1 : 1;
        foreach ($queryResult as &$row) {
            $row['no'] = $no++;
            $row['id'] = $row['task_submission_id'];
        }
        unset($row);

        return [
            'recordsTotal' => $totalCount,
            'recordsFiltered' => $filterCount,
            'data' => $queryResult,
            'draw' => isset($data['draw']) ? (int) $data['draw'] : 0,
        ];
    }

    public function getVerifiedTasks($data = [])
    {
        $baseBuilder = $this->builder('task_submissions AS rts')
            ->select('
                rts.id AS task_submission_id,
                rts.date,
                rts.room_id AS location_id,
                tsi.item_id,
                tsi.cleaning_frequency AS time_cleaned,
                rts.status,
                rts.revision_message,
                rts.submitted_by,
                rts.verified_by,
                rts.verified_at,
                rm.name AS location_name,
                mi.name AS item_name,
                mu.username as verified_by_name,
                GROUP_CONCAT(DISTINCT ma.action_name ORDER BY ma.action_name SEPARATOR ", ") AS action_name
            ')
            ->join('task_submission_items AS tsi', 'rts.id = tsi.task_submission_id', 'left')
            ->join('task_submission_actions AS tsa', 'tsa.task_submission_item_id = tsi.id', 'left')
            ->join('m_actions AS ma', 'ma.action_id = tsa.action_id', 'left')
            ->join('rooms AS rm', 'rm.id = rts.room_id', 'left')
            ->join('items AS mi', 'mi.id = tsi.item_id', 'left')
            ->join('users AS mu', 'mu.id = rts.verified_by', 'left')
            ->whereIn('rts.status', ['approved'])
            ->groupBy('rts.id, rts.date, rts.room_id, tsi.item_id, tsi.cleaning_frequency, rts.status, rts.revision_message, rts.submitted_by, rts.verified_by, rts.verified_at, rm.name, mi.name, mu.username');

        // Total count before filtering (distinct submissions)
        $totalCount = (clone $baseBuilder)->countAllResults();

        // Apply location filter if provided
        if (!empty($data['location_id']) && $data['location_id'] !== '0') {
            $baseBuilder->where('rts.location_id', (int) $data['location_id']);
        }

        // Apply search if provided
        if (!empty($data['search'])) {
            $search = $data['search'];
            $baseBuilder->groupStart()
                ->like('ml.location_name', $search)
                ->orLike('mi.item_name', $search)
                ->orLike('ma.action_name', $search)
                ->orLike('rts.status', $search)
                ->orLike('mu.name', $search)
                ->groupEnd();
        }

        // Filtered count after search
        $filterCount = (clone $baseBuilder)->countAllResults();

        // Apply ordering
        if (!empty($data['order_column']) && !empty($data['order_sort'])) {
            $baseBuilder->orderBy($data['order_column'], $data['order_sort']);
        } else {
            $baseBuilder->orderBy('rts.date', 'DESC')->orderBy('rts.task_submission_id', 'DESC');
        }

        // Apply pagination
        if (isset($data['length']) && isset($data['start'])) {
            $queryResult = $baseBuilder->get((int) $data['length'], (int) $data['start'])->getResultArray();
        } else {
            $queryResult = $baseBuilder->get()->getResultArray();
        }

        // Add row numbering and convenience id field
        $no = isset($data['start']) ? ((int) $data['start']) + 1 : 1;
        foreach ($queryResult as &$row) {
            $row['no'] = $no++;
            $row['id'] = $row['task_submission_id'];
        }
        unset($row);

        return [
            'recordsTotal' => $totalCount,
            'recordsFiltered' => $filterCount,
            'data' => $queryResult,
            'draw' => isset($data['draw']) ? (int) $data['draw'] : 0,
        ];
    }
}
