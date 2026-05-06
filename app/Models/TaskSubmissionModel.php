<?php

namespace App\Models;

use App\Contracts\DataPoolInterface;
use App\Services\CachedDataPool;
use CodeIgniter\Model;

class TaskSubmissionModel extends Model
{
    private ?bool $hasUniqueCodeColumn = null;
    private ?bool $hasRevisionImageColumn = null;
    private ?DataPoolInterface $dataPool = null;

    protected $table = 'r_task_submission';
    protected $primaryKey = 'task_submission_id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $useSoftDeletes = false;
    protected $protectFields = true;
    protected $afterInsert = ['invalidateTaskSubmissionPool'];
    protected $afterUpdate = ['invalidateTaskSubmissionPool'];
    protected $afterDelete = ['invalidateTaskSubmissionPool'];
    protected $allowedFields = [
        'date',
        'location_id',
        'item_id',
        'time_cleaned',
        'revision_message',
        'revision_image_path',
        'status',
        'unique_code',
        'submitted_by',
        'verified_by',
        'verified_at',
    ];

    private function dataPool(): DataPoolInterface
    {
        if ($this->dataPool === null) {
            $this->dataPool = new CachedDataPool();
        }

        return $this->dataPool;
    }

    private function buildSignature(string $method, array $payload = []): string
    {
        return $method . ':' . json_encode($payload);
    }

    public function invalidateTaskSubmissionPool(array $data): array
    {
        $this->dataPool()->bump('task_submission');

        return $data;
    }

    private function canUseUniqueCodeColumn(): bool
    {
        if ($this->hasUniqueCodeColumn !== null) {
            return $this->hasUniqueCodeColumn;
        }

        try {
            $this->hasUniqueCodeColumn = $this->db->fieldExists('unique_code', 'r_task_submission');
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
            $this->hasRevisionImageColumn = $this->db->fieldExists('revision_image_path', 'r_task_submission');
        } catch (\Throwable $e) {
            $this->hasRevisionImageColumn = false;
        }

        return $this->hasRevisionImageColumn;
    }

    /**
     * Filter data to remove fields that don't exist in the database
     * Useful for handling schema compatibility across different environments
     */
    public function filterDataForInsert(array $data): array
    {
        if (!$this->canUseUniqueCodeColumn()) {
            unset($data['unique_code']);
        }

        if (!$this->canUseRevisionImageColumn()) {
            unset($data['revision_image_path']);
        }

        return $data;
    }

    public function schemaHasRevisionImage(): bool
    {
        return $this->canUseRevisionImageColumn();
    }

    public function getNextSubmitSeq(int $userId, string $date): int
    {
        if (!$this->canUseUniqueCodeColumn()) {
            return 1;
        }

        $dateYmd = str_replace('-', '', $date);
        $prefix = '#' . $userId . '-' . $dateYmd . '-';

        $codes = $this->builder('r_task_submission')
            ->select('unique_code')
            ->where('submitted_by', $userId)
            ->where('date', $date)
            ->like('unique_code', $prefix, 'after')
            ->get()
            ->getResultArray();

        $maxSeq = 0;
        foreach ($codes as $row) {
            $code = (string) ($row['unique_code'] ?? '');
            if ($code === '' || !str_starts_with($code, $prefix)) {
                continue;
            }

            $seqPart = substr($code, strlen($prefix));
            if ($seqPart !== '' && ctype_digit($seqPart)) {
                $maxSeq = max($maxSeq, (int) $seqPart);
            }
        }

        return $maxSeq + 1;
    }

    private function getActionNamesAggregate(string $alias = 'action_name'): string
    {
        $driver = strtolower((string) ($this->db->DBDriver ?? ''));

        if (str_contains($driver, 'postgre')) {
            return 'STRING_AGG(DISTINCT ma.action_name, \', \' ORDER BY ma.action_name) AS ' . $alias;
        }

        return 'GROUP_CONCAT(DISTINCT ma.action_name ORDER BY ma.action_name SEPARATOR ", ") AS ' . $alias;
    }

    private function applySubmittedTaskFilters($builder, array $data = [])
    {
        if (!empty($data['location_id']) && $data['location_id'] !== '0') {
            $builder->where('rts.location_id', (int) $data['location_id']);
        }

        if (!empty($data['date']) && $data['date'] !== '0') {
            $builder->where('rts.date', $data['date']);
        }

        if (!empty($data['search'])) {
            $search = $data['search'];
            $builder->groupStart()
                ->like('ml.location_name', $search)
                ->orLike('mi.item_name', $search)
                ->orLike('ma.action_name', $search)
                ->orLike('rts.status', $search);

            if ($this->canUseUniqueCodeColumn()) {
                $builder->orLike('rts.unique_code', $search);
            }

            $builder->groupEnd();
        }

        return $builder;
    }

    public function getSubmissionsWithDetails($location_id, $date)
    {
        $signature = $this->buildSignature(__FUNCTION__, [
            'location_id' => (int) $location_id,
            'date' => (string) $date,
            'has_unique_code' => $this->canUseUniqueCodeColumn(),
        ]);

        return $this->dataPool()->remember('task_submission', $signature, function () use ($location_id, $date) {
            $uniqueCodeSelect = $this->canUseUniqueCodeColumn()
                ? 'rts.unique_code'
                : "'' AS unique_code";

            return $this->db->query(
                "SELECT
                    rts.task_submission_id,
                    rts.date,
                    rts.location_id,
                    rts.item_id,
                    rts.submitted_by,
                    rts.time_cleaned,
                    rts.status,
                    {$uniqueCodeSelect},
                    rtsd.task_submission_detail_id,
                    rtsd.action_id,
                    rtsd.quantity
                FROM r_task_submission rts
                LEFT JOIN r_task_submission_detail rtsd ON rts.task_submission_id = rtsd.task_submission_id
                WHERE rts.location_id = ? AND rts.date = ?",
                [$location_id, $date]
            )->getResultArray();
        }, 30);
    }

    public function getTotalsByLocationDate($location_id, $date)
    {
        $signature = $this->buildSignature(__FUNCTION__, [
            'location_id' => (int) $location_id,
            'date' => (string) $date,
        ]);

        return $this->dataPool()->remember('task_submission', $signature, function () use ($location_id, $date) {
            $result = $this->db->query(
                "SELECT
                    COUNT(DISTINCT rts.task_submission_id) AS items_cleaned_total,
                    COUNT(DISTINCT rtsd.action_id) AS actions_cleaned_total
                FROM r_task_submission rts
                LEFT JOIN r_task_submission_detail rtsd ON rts.task_submission_id = rtsd.task_submission_id
                WHERE rts.location_id = ? AND rts.date = ?",
                [$location_id, $date]
            )->getRowArray();

            return $result ?: ['items_cleaned_total' => 0, 'actions_cleaned_total' => 0];
        }, 30);
    }

    public function getSubmittedTasks($data = [])
    {
        $signature = $this->buildSignature(__FUNCTION__, [
            'data' => $data,
            'has_unique_code' => $this->canUseUniqueCodeColumn(),
            'driver' => (string) ($this->db->DBDriver ?? ''),
        ]);

        return $this->dataPool()->remember('task_submission', $signature, function () use ($data) {
            $actionNamesAggregate = $this->getActionNamesAggregate();
            $hasUniqueCode = $this->canUseUniqueCodeColumn();

            $selectParts = [
                'rts.task_submission_id',
                'rts.date',
                'rts.location_id',
                'rts.item_id',
                'rts.time_cleaned',
                'rts.status',
                $hasUniqueCode ? 'rts.unique_code' : "'' AS unique_code",
                'rts.revision_message',
                'rts.submitted_by',
                'rts.verified_by',
                'rts.verified_at',
                'ml.location_name',
                'mi.item_name',
                $actionNamesAggregate,
            ];

            $groupByParts = [
                'rts.task_submission_id',
                'rts.date',
                'rts.location_id',
                'rts.item_id',
                'rts.time_cleaned',
                'rts.status',
                'rts.revision_message',
                'rts.submitted_by',
                'rts.verified_by',
                'rts.verified_at',
                'ml.location_name',
                'mi.item_name',
            ];

            if ($hasUniqueCode) {
                $groupByParts[] = 'rts.unique_code';
            }

            $baseBuilder = $this->builder('r_task_submission AS rts')
                ->select(implode(",\n                ", $selectParts), false)
                ->join('r_task_submission_detail AS rtsd', 'rts.task_submission_id = rtsd.task_submission_id', 'left')
                ->join('m_locations AS ml', 'ml.location_id = rts.location_id', 'left')
                ->join('m_items AS mi', 'mi.item_id = rts.item_id', 'left')
                ->join('m_actions AS ma', 'ma.action_id = rtsd.action_id', 'left')
                ->groupBy(implode(', ', $groupByParts));

            $totalCount = (clone $baseBuilder)->countAllResults();

            $this->applySubmittedTaskFilters($baseBuilder, $data);

            $filterCount = (clone $baseBuilder)->countAllResults();

            if (!empty($data['order_column']) && !empty($data['order_sort'])) {
                $baseBuilder->orderBy($data['order_column'], $data['order_sort']);
            } else {
                $baseBuilder->orderBy('rts.date', 'DESC')->orderBy('rts.task_submission_id', 'DESC');
            }

            if (isset($data['length']) && isset($data['start'])) {
                $queryResult = $baseBuilder->get((int) $data['length'], (int) $data['start'])->getResultArray();
            } else {
                $queryResult = $baseBuilder->get()->getResultArray();
            }

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
        }, 20);
    }

    public function verifyPendingTasks(array $filters, ?int $verifiedBy): int
    {
        $builder = $this->builder('r_task_submission AS rts')
            ->select('rts.task_submission_id')
            ->join('r_task_submission_detail AS rtsd', 'rts.task_submission_id = rtsd.task_submission_id', 'left')
            ->join('m_locations AS ml', 'ml.location_id = rts.location_id', 'left')
            ->join('m_items AS mi', 'mi.item_id = rts.item_id', 'left')
            ->join('m_actions AS ma', 'ma.action_id = rtsd.action_id', 'left')
            ->where('rts.status', 'pending')
            ->groupBy('rts.task_submission_id');

        $this->applySubmittedTaskFilters($builder, $filters);

        $taskIds = array_column($builder->get()->getResultArray(), 'task_submission_id');

        if ($taskIds === []) {
            return 0;
        }

        $this->builder()
            ->whereIn('task_submission_id', $taskIds)
            ->set([
                'status' => 'verified',
                'verified_by' => $verifiedBy,
                'verified_at' => date('Y-m-d H:i:s'),
            ])
            ->update();

        $this->dataPool()->bump('task_submission');

        return count($taskIds);
    }

    public function getSubmittedLocations($date = null)
    {
        $signature = $this->buildSignature(__FUNCTION__, ['date' => $date]);

        return $this->dataPool()->remember('task_submission', $signature, function () use ($date) {
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
        }, 60);
    }

    public function getSubmittedDates($location_id = null)
    {
        $signature = $this->buildSignature(__FUNCTION__, ['location_id' => $location_id]);

        return $this->dataPool()->remember('task_submission', $signature, function () use ($location_id) {
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
        }, 60);
    }
}
