<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class UserSeeder extends Seeder
{
    public function run()
    {
        $this->db->disableForeignKeyChecks();

        try {
            // Truncate the table
            $this->db->table('m_users')->truncate();

            $data = [
                [
                    'name' => 'prasongko',
                    'user_role' => 'administrator',
                    'username' => 'prasongko',
                    'password' => password_hash('prasongko', PASSWORD_BCRYPT),
                    'is_active' => 1
                ],
            ];

            if (!empty($data)) {
                $this->db->table('m_users')->insertBatch($data);
                echo "✓ Seeded " . count($data) . " users\n";
            }
        } catch (\Exception $e) {
            echo "✗ Error seeding users: " . $e->getMessage() . "\n";
            throw $e;
        } finally {
            $this->db->enableForeignKeyChecks();
        }
    }
}
