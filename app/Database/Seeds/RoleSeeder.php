<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class RoleSeeder extends Seeder
{
    public function run()
    {
        $this->db->disableForeignKeyChecks();
        $this->db->transStart();
        $roles = [
            [
                'name' => 'administrator',
                'slug' => 'admin',
                'description' => 'Administrator Role',
                'created_at' => date('Y-m-d H:i:s'),
            ],
            [
                'name' => 'operator',
                'slug' => 'operator',
                'description' => 'Operator Role',
                'created_at' => date('Y-m-d H:i:s'),
            ],
            [
                'name' => 'verifikator',
                'slug' => 'verifikator',
                'description' => 'Verifikator Role',
                'created_at' => date('Y-m-d H:i:s'),
            ]
        ];
        $this->db->table('roles')->insertBatch($roles);

        $this->db->transComplete();
        $this->db->enableForeignKeyChecks();
    }
}
