<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class TaskSubmissionActions extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id' => ['type' => 'int', 'unsigned' => true, 'auto_increment' => true],
            'task_submission_item_id' => ['type' => 'int', 'unsigned' => true],
            'action_id' => ['type' => 'int', 'unsigned' => true, 'default' => 0],
            'repetitions' => ['type' => 'int', 'unsigned' => true],
            'created_at' => ['type' => 'datetime', 'null' => true],
            'updated_at' => ['type' => 'datetime', 'null' => true],
            'deleted_at' => ['type' => 'datetime', 'null' => true]
        ]);
        $this->forge->addPrimaryKey('id');
        $this->forge->addKey('task_submission_item_id');
        $this->forge->addKey('action_id');
        $this->forge->addForeignKey('task_submission_item_id', 'task_submission_items', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('action_id', 'actions', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('task_submission_actions', true);
    }

    public function down()
    {
        $this->forge->dropTable('task_submission_actions', true);
    }
}
