<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class TaskSubmission extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'task_submission_id' => [
                'type' => 'int',
                'constraint' => 5,
                'unsigned' => true,
                'auto_increment' => true,
            ],
            'date' => [
                'type' => 'date'
            ],
            'location_id' => [
                'type' => 'int',
                'constraint' => 5
            ],
            'item_id' => [
                'type' => 'int',
                'constraint' => 5
            ],
            'time_cleaned' => [
                'type' => 'int',
                'default' => 1
            ],
            'revision_message' => [
                'type' => 'text',
                'null' => true,
                'default' => null
            ],
            'status' => [
                'type' => 'varchar',
                'constraint' => 20,
                'default' => 'pending'
            ],
            'submitted_by' => [
                'type' => 'int',
                'constraint' => 5
            ],
            'verified_by' => [
                'type' => 'int',
                'constraint' => 5,
                'null' => true
            ],
            'verified_at' => [
                'type' => 'datetime',
                'null' => true
            ]
        ]);
        $this->forge->addKey('task_submission_id', true);
        $this->forge->createTable('r_task_submission');
    }

    public function down()
    {
        $this->forge->dropTable('r_task_submission');
    }
}
