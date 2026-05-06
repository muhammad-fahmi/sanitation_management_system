<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class Locations extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'location_id' => [
                'type' => 'int',
                'constraint' => 5,
                'unsigned' => true,
                'auto_increment' => true,
            ],
            'location_name' => [
                'type' => 'varchar',
                'constraint' => 255,
            ]
        ]);
        $this->forge->addKey('location_id', true);
        $this->forge->addUniqueKey('location_name');
        $this->forge->createTable('m_locations', true);
    }

    public function down()
    {
        $this->forge->dropTable('m_locations', true);
    }
}
