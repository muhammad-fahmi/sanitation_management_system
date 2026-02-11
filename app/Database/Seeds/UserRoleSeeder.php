<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class UserRoleSeeder extends Seeder
{
    public function run()
    {
        $this->db->disableForeignKeyChecks();
        $this->db->transStart();
        $userRoles = [
            [
                'user_id' => 1,
                'role_id' => 1,
                'created_at' => date('Y-m-d H:i:s'),
            ],
            [
                'user_id' => 2,
                'role_id' => 2,
                'created_at' => date('Y-m-d H:i:s'),
            ],
            [
                'user_id' => 3,
                'role_id' => 2,
                'created_at' => date('Y-m-d H:i:s'),
            ],
            [
                'user_id' => 4,
                'role_id' => 2,
                'created_at' => date('Y-m-d H:i:s'),
            ],
            [
                'user_id' => 5,
                'role_id' => 3,
                'created_at' => date('Y-m-d H:i:s'),
            ]
        ];
        $this->db->table('user_roles')->insertBatch($userRoles);

        $this->db->transComplete();
        $this->db->enableForeignKeyChecks();
    }
}
