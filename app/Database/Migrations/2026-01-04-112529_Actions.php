<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class Actions extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'action_id' => [
                'type' => 'int',
                'constraint' => 5,
                'unsigned' => true,
                'auto_increment' => true,
            ],
            'item_id' => [
                'type' => 'int',
                'constraint' => 5
            ],
            'action_name' => [
                'type' => 'varchar',
                'constraint' => 200
            ],
        ]);
        $this->forge->addKey('action_id', true);
        $this->forge->createTable('m_actions', true);
    }

    public function down()
    {
        $this->forge->dropTable('m_actions', true);
    }
}
