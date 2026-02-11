<?php

namespace App\Models;

use CodeIgniter\Model;
use Tatter\Audits\Traits\AuditsTrait;

/**
 * @see AuditsTrait
 */
class PermissionModel extends Model
{
    use AuditsTrait;
    protected $table = 'permissions';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $useSoftDeletes = false;
    protected $protectFields = true;
    protected $allowedFields = [
        'name',
        'slug',
        'description',
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
        'name' => 'required|string|max_length[100]|is_unique[permissions.name,id,{id}]',
        'slug' => 'required|string|max_length[100]|is_unique[permissions.slug,id,{id}]',
        'description' => 'permit_empty|string|max_length[255]',
    ];
    protected $validationMessages = [
        'name' => [
            'required' => 'The permission name is required.',
            'max_length' => 'The permission name cannot exceed 100 characters.',
            'is_unique' => 'The permission name must be unique.',
        ],
        'slug' => [
            'required' => 'The permission slug is required.',
            'max_length' => 'The permission slug cannot exceed 100 characters.',
            'is_unique' => 'The permission slug must be unique.',
        ],
        'description' => [
            'max_length' => 'The description cannot exceed 255 characters.',
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
