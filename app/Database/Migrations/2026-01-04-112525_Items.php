<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class Items extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'item_id' => [
                'type' => 'int',
                'constraint' => 5,
                'unsigned' => true,
                'auto_increment' => true,
            ],
            'location_id' => [
                'type' => 'int',
                'constraint' => 3
            ],
            'item_name' => [
                'type' => 'varchar',
                'constraint' => 100,
            ],
        ]);
        $this->forge->addKey('item_id', true);
        $this->forge->addKey('location_id');
        $this->forge->createTable('m_items', true);
    }

    public function down()
    {
        $this->forge->dropTable('m_items', true);
    }
}
