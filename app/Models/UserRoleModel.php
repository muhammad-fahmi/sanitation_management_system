<?php

namespace App\Models;

use CodeIgniter\Model;
use Tatter\Audits\Traits\AuditsTrait;

/**
 * @see AuditsTrait
 */
class UserRoleModel extends Model
{
    use AuditsTrait;
    protected $table = 'user_roles';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $useSoftDeletes = false;
    protected $protectFields = true;
    protected $allowedFields = [
        'user_id',
        'role_id',
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

    public function getUserDetail($id)
    {
        return $this
            ->builder('user_roles as ur')
            ->select('ur.user_id,ur.role_id,u.username,u.email,u.password,r.slug')
            ->join('users as u', 'u.id = ur.user_id')
            ->join('roles as r', 'r.id = ur.role_id')
            ->where('ur.user_id', $id)
            ->get()
            ->getFirstRow('array');
    }

    public function get_datatable($data)
    {
        $base_query = $this->builder('user_roles as ur')->select('ur.user_id, u.username,r.slug')
            ->join('users as u', 'u.id = ur.user_id')
            ->join('roles as r', 'r.id = ur.role_id');

        if ($data['search'] != '') {
            $query_result = $base_query
                ->like('name', (string) $data['search'], insensitiveSearch: true)
                ->orLike('user_role', (string) $data['search'], insensitiveSearch: true)
                ->orderBy($data['order_column'], $data['order_sort'])
                ->get($data['length'], offset: $data['start'])->getResultArray();

            $filter_count = count($query_result);
        } else {
            $query_result = $base_query
                ->orderBy($data['order_column'], $data['order_sort'])
                ->get($data['length'], offset: $data['start'])->getResultArray();

            $filter_count = $base_query
                ->countAllResults(false);
        }

        $total_count = $base_query
            ->countAllResults(false);

        // Format data with numbering
        $no = $data['start'] + 1;
        $data_result = [];
        foreach ($query_result as $row) {
            $row['no'] = $no++;
            $data_result[] = $row;
        }

        return [
            'total' => $total_count,
            'filtered' => $filter_count,
            'data' => $data_result,
            'draw' => $data['draw'],
        ];
    }
}
