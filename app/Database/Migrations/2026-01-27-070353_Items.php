<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class Items extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id' => ['type' => 'int', 'constraint' => 5, 'unsigned' => true, 'auto_increment' => true],
            'room_id' => ['type' => 'int', 'unsigned' => true],
            'name' => ['type' => 'varchar', 'constraint' => 100],
            'created_at' => ['type' => 'datetime', 'null' => true],
            'updated_at' => ['type' => 'datetime', 'null' => true]
        ]);
        $this->forge->addPrimaryKey('id');
        $this->forge->addKey('room_id');
        $this->forge->addForeignKey('room_id', 'rooms', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('items', true);
    }

    public function down()
    {
        $this->forge->dropTable('items', true);
    }
}
