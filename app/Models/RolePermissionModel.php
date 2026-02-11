<?php

namespace App\Models;

use CodeIgniter\Model;
use Tatter\Audits\Traits\AuditsTrait;

/**
 * @see AuditsTrait
 */
class RolePermissionModel extends Model
{
    use AuditsTrait;
    protected $table = 'role_permissions';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $useSoftDeletes = false;
    protected $protectFields = true;
    protected $allowedFields = [
        'role_id',
        'permission_id',
        'created_at',
        'updated_at'
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

    // Validation
    protected $validationRules = [
        'role_id' => 'required|integer',
        'permission_id' => 'required|integer',
    ];
    protected $validationMessages = [
        'role_id' => [
            'required' => 'Role ID is required.',
            'integer' => 'Role ID must be an integer.',
        ],
        'permission_id' => [
            'required' => 'Permission ID is required.',
            'integer' => 'Permission ID must be an integer.',
        ],
    ];
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
}
