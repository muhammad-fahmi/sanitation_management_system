<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;
use \App\Models\UserModel;

class UserSeeder extends Seeder
{
    public function run()
    {

        $userModel = new UserModel();

        try {
            $userModel->builder()->db()->query('SET FOREIGN_KEY_CHECKS=0;');
            $userModel->builder()->truncate();
            $data = [
                [
                    'username' => 'prasongko',
                    'email' => 'prasongko37@gmail.com',
                    'password' => password_hash('prasongko123', PASSWORD_BCRYPT),
                    'is_active' => 1,
                    'created_at' => date('Y-m-d H:i:s'),
                    'updated_at' => date('Y-m-d H:i:s'),
                ],
                [
                    'username' => 'yanto',
                    'email' => 'yanto123@gmail.com',
                    'password' => password_hash('yanto123', PASSWORD_BCRYPT),
                    'is_active' => 1,
                    'created_at' => date('Y-m-d H:i:s'),
                    'updated_at' => date('Y-m-d H:i:s'),
                ],
                [
                    'username' => 'octo',
                    'email' => 'octo123@gmail.com',
                    'password' => password_hash('octo123', PASSWORD_BCRYPT),
                    'is_active' => 1,
                    'created_at' => date('Y-m-d H:i:s'),
                    'updated_at' => date('Y-m-d H:i:s'),
                ],
                [
                    'username' => 'theo',
                    'email' => 'theo123@gmail.com',
                    'password' => password_hash('theo123', PASSWORD_BCRYPT),
                    'is_active' => 1,
                    'created_at' => date('Y-m-d H:i:s'),
                    'updated_at' => date('Y-m-d H:i:s'),
                ],
                [
                    'username' => 'rian',
                    'email' => 'rian123@gmail.com',
                    'password' => password_hash('rian123', PASSWORD_BCRYPT),
                    'is_active' => 1,
                    'created_at' => date('Y-m-d H:i:s'),
                    'updated_at' => date('Y-m-d H:i:s'),
                ],
            ];


            if (!empty($data)) {
                foreach ($data as $user) {
                    $userModel->save($user);
                }
                echo "✓ Seeded " . count($data) . " users\n";
            }
        } catch (\Exception $e) {
            echo "✗ Error seeding users: " . $e->getMessage() . "\n";
            throw $e;
        } finally {
            $userModel->builder()->db()->query('SET FOREIGN_KEY_CHECKS=1;');
        }
    }
}
