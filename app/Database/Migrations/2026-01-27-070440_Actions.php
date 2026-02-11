<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class Actions extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id' => ['type' => 'int', 'constraint' => 5, 'unsigned' => true, 'auto_increment' => true],
            'item_id' => ['type' => 'int', 'constraint' => 5, 'unsigned' => true],
            'name' => ['type' => 'varchar', 'constraint' => 200],
            'created_at' => ['type' => 'datetime', 'null' => true],
            'updated_at' => ['type' => 'datetime', 'null' => true]
        ]);
        $this->forge->addPrimaryKey('id');
        $this->forge->addKey('item_id');
        $this->forge->addForeignKey('item_id', 'items', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('actions', true);
    }

    public function down()
    {
        $this->forge->dropTable('actions', true);
    }
}
