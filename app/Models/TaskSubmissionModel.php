<?php

namespace App\Models;

use CodeIgniter\Model;

class TaskSubmissionModel extends Model
{
    protected $table = 'r_task_submission';
    protected $primaryKey = 'task_submission_id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $useSoftDeletes = false;
    protected $protectFields = true;
    protected $allowedFields = [
        'date',
        'location_id',
        'item_id',
        'time_cleaned',
        'revision_message',
        'status',
        'submitted_by',
        'verified_by',
        'verified_at'
    ];

    public function getSubmissionsWithDetails($location_id, $date)
    {
        return $this->db->query("
            SELECT
                rts.task_submission_id,
                rts.date,
                rts.location_id,
                rts.item_id,
                rts.submitted_by,
                rts.time_cleaned,
                rts.status,
                rtsd.task_submission_detail_id,
                rtsd.action_id,
                rtsd.quantity
            FROM r_task_submission rts
            LEFT JOIN r_task_submission_detail rtsd ON rts.task_submission_id = rtsd.task_submission_id
            WHERE rts.location_id = ? AND rts.date = ?
        ", [$location_id, $date])->getResultArray();
    }

    public function getTotalsByLocationDate($location_id, $date)
    {
        $result = $this->db->query("
            SELECT
                COUNT(DISTINCT rts.task_submission_id) AS items_cleaned_total,
                COUNT(DISTINCT rtsd.action_id) AS actions_cleaned_total
            FROM r_task_submission rts
            LEFT JOIN r_task_submission_detail rtsd ON rts.task_submission_id = rtsd.task_submission_id
            WHERE rts.location_id = ? AND rts.date = ?
        ", [$location_id, $date])->getRowArray();
        return $result ?: ['items_cleaned_total' => 0, 'actions_cleaned_total' => 0];
    }

    public function getSubmittedTasks($data = [])
    {
        $baseBuilder = $this->builder('r_task_submission AS rts')
            ->select('
                rts.task_submission_id,
                rts.date,
                rts.location_id,
                rts.item_id,
                rts.time_cleaned,
                rts.status,
                rts.revision_message,
                rts.submitted_by,
                rts.verified_by,
                rts.verified_at,
                ml.location_name,
                mi.item_name,
                GROUP_CONCAT(DISTINCT ma.action_name ORDER BY ma.action_name SEPARATOR ", ") AS action_name
            ')
            ->join('r_task_submission_detail AS rtsd', 'rts.task_submission_id = rtsd.task_submission_id', 'left')
            ->join('m_locations AS ml', 'ml.location_id = rts.location_id', 'left')
            ->join('m_items AS mi', 'mi.item_id = rts.item_id', 'left')
            ->join('m_actions AS ma', 'ma.action_id = rtsd.action_id', 'left')
            ->groupBy('rts.task_submission_id, rts.date, rts.location_id, rts.item_id, rts.time_cleaned, rts.status, rts.revision_message, rts.submitted_by, rts.verified_by, rts.verified_at, ml.location_name, mi.item_name');

        // Total count before filtering (distinct submissions)
        $totalCount = (clone $baseBuilder)->countAllResults();

        // Apply location filter if provided
        if (!empty($data['location_id']) && $data['location_id'] !== '0') {
            $baseBuilder->where('rts.location_id', (int) $data['location_id']);
        }

        // Apply date filter if provided
        if (!empty($data['date']) && $data['date'] !== '0') {
            $baseBuilder->where('rts.date', $data['date']);
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

    public function getSubmittedLocations($date = null)
    {
        $builder = $this->builder('r_task_submission AS rts')
            ->distinct()
            ->select('rts.location_id, ml.location_name')
            ->join('m_locations AS ml', 'ml.location_id = rts.location_id', 'left')
            ->where('rts.location_id IS NOT NULL', null, false);

        if (!empty($date) && $date !== '0') {
            $builder->where('rts.date', $date);
        }

        return $builder
            ->orderBy('ml.location_name', 'ASC')
            ->get()
            ->getResultArray();
    }

    public function getSubmittedDates($location_id = null)
    {
        $builder = $this->builder('r_task_submission AS rts')
            ->distinct()
            ->select('rts.date')
            ->where('rts.date IS NOT NULL', null, false);

        if (!empty($location_id) && $location_id !== '0') {
            $builder->where('rts.location_id', (int) $location_id);
        }

        return $builder
            ->orderBy('rts.date', 'ASC')
            ->get()
            ->getResultArray();
    }
}
