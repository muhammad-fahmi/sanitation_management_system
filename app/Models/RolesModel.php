<?php

namespace App\Models;

use CodeIgniter\Model;
use Tatter\Audits\Traits\AuditsTrait;

/**
 * @see AuditsTrait
 */
class RolesModel extends Model
{
    use AuditsTrait;
    protected $table = 'roles';
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
        'updated_at',
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
        'name' => 'required|string|max_length[255]',
        'slug' => 'required|string|max_length[255]|is_unique[roles.slug,id,{id}]',
        'description' => 'permit_empty|string',
    ];
    protected $validationMessages = [
        'name' => [
            'required' => 'The name field is required.',
            'string' => 'The name field must be a string.',
            'max_length' => 'The name field cannot exceed 255 characters.',
        ],
        'slug' => [
            'required' => 'The slug field is required.',
            'string' => 'The slug field must be a string.',
            'max_length' => 'The slug field cannot exceed 255 characters.',
            'is_unique' => 'The slug field must be unique.',
        ],
        'description' => [
            'string' => 'The description field must be a string.',
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

    public function getAllRoles()
    {
        return $this
            ->builder('roles as r')
            ->select('r.id,r.slug,r.name,r.description')
            ->get()
            ->getResultArray();
    }
}
