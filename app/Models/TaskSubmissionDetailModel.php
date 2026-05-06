<?php

namespace App\Models;

use App\Services\CachedDataPool;
use CodeIgniter\Model;

class TaskSubmissionDetailModel extends Model
{
    protected $table = 'r_task_submission_detail';
    protected $primaryKey = 'task_submission_detail_id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $useSoftDeletes = false;
    protected $protectFields = true;
    protected $afterInsert = ['invalidateTaskSubmissionPool'];
    protected $afterUpdate = ['invalidateTaskSubmissionPool'];
    protected $afterDelete = ['invalidateTaskSubmissionPool'];
    protected $allowedFields = [
        'task_submission_id',
        'action_id',
        'quantity'
    ];

    public function invalidateTaskSubmissionPool(array $data): array
    {
        (new CachedDataPool())->bump('task_submission');

        return $data;
    }
}
