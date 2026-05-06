<?php

namespace App\Models;

use CodeIgniter\Model;

class LocationModel extends Model
{
    protected $table = 'm_locations';
    protected $primaryKey = 'location_id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $useSoftDeletes = false;
    protected $protectFields = true;
    protected $allowedFields = [
        'location_name'
    ];

    public function getAllRoom($limit = null, $offset = null)
    {
        $query = $this->select('location_id,location_name');
        if ($limit !== null) {
            $query = $query->limit($limit, $offset);
        }
        return $query->findAll();
    }

    public function get_datatable_location($data)
    {
        $base_query = $this->builder('m_locations ml')
            ->select('location_id,location_name')
            ->distinct();

        if ($data['search'] != '') {
            $query_result = $base_query
                ->groupStart()
                ->like('ml.location_name', (string) $data['search'], insensitiveSearch: true)
                ->groupEnd()
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
}
