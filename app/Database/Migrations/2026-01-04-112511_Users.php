<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class Users extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id' => ['type' => 'int', 'unsigned' => true, 'auto_increment' => true],
            'username' => ['type' => 'varchar', 'constraint' => 100],
            'email' => ['type' => 'varchar', 'constraint' => 255],
            'password' => ['type' => 'varchar', 'constraint' => 255],
            'is_active' => ['type' => 'int', 'constraint' => 2, 'default' => 1],
            'last_login_at' => ['type' => 'datetime', 'null' => true],
            'created_at' => ['type' => 'datetime', 'null' => true],
            'updated_at' => ['type' => 'datetime', 'null' => true],
            'deleted_at' => ['type' => 'datetime', 'null' => true],
        ]);
        $this->forge->addPrimaryKey('id');
        $this->forge->addUniqueKey('username');
        $this->forge->addUniqueKey('email');
        $this->forge->createTable('users', true);
    }

    public function down()
    {
        $this->forge->dropTable('users', true);
    }
}
