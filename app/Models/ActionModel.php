<?php

namespace App\Models;

use CodeIgniter\Model;

class ActionModel extends Model
{
    protected $table = 'm_actions';
    protected $primaryKey = 'action_id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $useSoftDeletes = false;
    protected $protectFields = true;
    protected $allowedFields = [
        'item_id',
        'action_name'
    ];

    public function get_datatable_action($data)
    {
        $base_query = $this->builder('m_actions ma')
            ->select('action_id,item_id,action_name')
            ->distinct();

        if ($data['search'] != '') {
            $query_result = $base_query
                ->groupStart()
                ->like('ma.action_name', (string) $data['search'], insensitiveSearch: true)
                ->groupEnd()
                ->where('ma.item_id', $data['item_id'] ?? '')
                ->orderBy($data['order_column'], $data['order_sort'])
                ->get($data['length'], offset: $data['start'])->getResultArray();

            $filter_count = count($query_result);
        } else {
            $query_result = $base_query
                ->where('ma.item_id', $data['item_id'] ?? '')
                ->orderBy($data['order_column'], $data['order_sort'])
                ->get($data['length'], offset: $data['start'])->getResultArray();

            $filter_count = $base_query
                ->where('ma.item_id', $data['item_id'] ?? '')
                ->get()->getNumRows();
        }

        $total_count = $base_query
            ->where('ma.item_id', $data['item_id'] ?? '')
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
