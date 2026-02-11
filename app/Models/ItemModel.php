<?php

namespace App\Models;

use CodeIgniter\Model;
use Tatter\Audits\Traits\AuditsTrait;

/**
 * @see AuditsTrait
 */
class ItemModel extends Model
{
    use AuditsTrait;
    protected $table = 'items';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $useSoftDeletes = false;
    protected $protectFields = true;
    protected $allowedFields = [
        'room_id',
        'name',
        'created_at',
        'updated_at'
    ];

    protected bool $allowEmptyInserts = false;
    protected bool $updateOnlyChanged = true;

    // Dates
    protected $useTimestamps = false;
    protected $dateFormat = 'datetime';
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';

    // Validation
    protected $validationRules = [
        'username' => 'required|alpha_numeric_space|min_length[3]|max_length[100]|is_unique[users.username,id,{id}]',
        'email' => 'required|valid_email|max_length[255]|is_unique[users.email,id,{id}]',
        'password' => 'required|min_length[6]|max_length[255]',
        'is_active' => 'integer|in_list[0,1]',
    ];
    protected $validationMessages = [
        'username' => [
            'required' => 'The username is required.',
            'alpha_numeric_space' => 'The username can only contain letters, numbers, and spaces.',
            'min_length' => 'The username must be at least 3 characters long.',
            'max_length' => 'The username must be at most 100 characters long.',
            'is_unique' => 'The username is already taken.',
        ],
        'email' => [
            'required' => 'The email is required.',
            'valid_email' => 'The email is invalid.',
            'max_length' => 'The email must be at most 255 characters long.',
            'is_unique' => 'The email is already taken.',
        ],
        'password' => [
            'required' => 'The password is required.',
            'min_length' => 'The password must be at least 6 characters long.',
        ],
        'is_active' => [
            'in_list' => 'The is_active field must be either 0 or 1.',
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

    public function getRoomItem($location_id)
    {
        // Legacy caller expects items with item_id/item_name and location_id keys
        return $this->where('room_id', $location_id)->select('id AS item_id, room_id AS location_id, name AS item_name')->findAll();
    }

    public function get_datatable_item($data)
    {
        $base_query = $this->builder('items mi')
            ->select('mi.id AS item_id, mi.room_id AS location_id, mi.name AS item_name')
            ->distinct();

        if ($data['search'] != '') {
            $query_result = $base_query
                ->groupStart()
                ->like('mi.name', (string) $data['search'], insensitiveSearch: true)
                ->groupEnd()
                ->where('mi.room_id', $data['location_id'] ?? '')
                ->orderBy($data['order_column'], $data['order_sort'])
                ->get($data['length'], offset: $data['start'])->getResultArray();

            $filter_count = count($query_result);
        } else {
            $query_result = $base_query
                ->where('mi.room_id', $data['location_id'] ?? '')
                ->orderBy($data['order_column'], $data['order_sort'])
                ->get($data['length'], offset: $data['start'])->getResultArray();

            $filter_count = $base_query
                ->where('mi.room_id', $data['location_id'] ?? '')
                ->get()->getNumRows();
        }

        $total_count = $base_query
            ->where('mi.room_id', $data['location_id'] ?? '')
            ->get()->getNumRows();

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
