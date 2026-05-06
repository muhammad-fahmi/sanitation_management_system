<?php

namespace App\Models;

use CodeIgniter\Model;

class ItemModel extends Model
{
    protected $table = 'm_items';
    protected $primaryKey = 'item_id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $useSoftDeletes = false;
    protected $protectFields = true;
    protected $allowedFields = [
        'location_id',
        'item_name'
    ];

    public function getRoomItem($location_id)
    {
        return $this->where('location_id', $location_id)->findAll();
    }

    public function get_datatable_item($data)
    {
        $base_query = $this->builder('m_items mi')
            ->select('item_id,location_id,item_name')
            ->distinct();

        if ($data['search'] != '') {
            $query_result = $base_query
                ->groupStart()
                ->like('mi.item_name', (string) $data['search'], insensitiveSearch: true)
                ->groupEnd()
                ->where('mi.location_id', $data['location_id'] ?? '')
                ->orderBy($data['order_column'], $data['order_sort'])
                ->get($data['length'], offset: $data['start'])->getResultArray();

            $filter_count = count($query_result);
        } else {
            $query_result = $base_query
                ->where('mi.location_id', $data['location_id'] ?? '')
                ->orderBy($data['order_column'], $data['order_sort'])
                ->get($data['length'], offset: $data['start'])->getResultArray();

            $filter_count = $base_query
                ->where('mi.location_id', $data['location_id'] ?? '')
                ->get()->getNumRows();
        }

        $total_count = $base_query
            ->where('mi.location_id', $data['location_id'] ?? '')
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
