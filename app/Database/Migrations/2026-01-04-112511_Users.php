<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class Users extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'user_id' => [
                'type' => 'int',
                'constraint' => 5,
                'unsigned' => true,
                'auto_increment' => true,
            ],
            'name' => [
                'type' => 'varchar',
                'constraint' => 50,
            ],
            'is_active' => [
                'type' => 'int',
                'constraint' => 2,
                'default' => 1
            ],
            'username' => [
                'type' => 'varchar',
                'constraint' => 100
            ],
            'password' => [
                'type' => 'text'
            ],
            'user_role' => [
                'type' => 'varchar',
                'constraint' => 20,
                'null' => true,
                'default' => null
            ]
        ]);
        $this->forge->addKey('user_id', true);
        $this->forge->addUniqueKey('username');
        $this->forge->createTable('m_users', true);
    }

    public function down()
    {
        $this->forge->dropTable('m_users', true);
    }
}
