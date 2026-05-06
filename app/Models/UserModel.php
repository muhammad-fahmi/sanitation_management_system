<?php

namespace App\Models;

use CodeIgniter\Model;

class UserModel extends Model
{
    protected $table = 'm_users';
    protected $primaryKey = 'user_id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $useSoftDeletes = false;
    protected $protectFields = true;
    protected $allowedFields = [
        'name',
        'is_active',
        'username',
        'password',
        'user_role'
    ];

    public function get_datatable($data)
    {
        $base_query = $this->builder('m_users AS mu')
            ->select('user_id,name,user_role')
            ->distinct();

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
                ->get()->getNumRows();
        }

        $total_count = $base_query
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

    public function getUserById($id): ?array
    {
        return $this->select('user_id, name, user_role, username, password')->find($id);
    }

    public function createUser(array $data): bool
    {
        return $this->insert($data) !== false;
    }

    public function updateUser($id, array $data): bool
    {
        return $this->update($id, $data);
    }

    public function deleteUser($id): bool
    {
        return $this->delete($id);
    }

    public function isUsernameUnique(string $username, int $excludeId): bool
    {
        return $this->where('username', $username)
            ->where('user_id !=', $excludeId)
            ->countAllResults() === 0;
    }

    public function isUsernameExists(string $username): bool
    {
        return $this->where('username', $username)->countAllResults() > 0;
    }
}
