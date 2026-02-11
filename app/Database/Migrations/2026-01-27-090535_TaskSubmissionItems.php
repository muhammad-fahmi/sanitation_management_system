<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class TaskSubmissionItems extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id' => ['type' => 'int', 'unsigned' => true, 'auto_increment' => true],
            'task_submission_id' => ['type' => 'int', 'unsigned' => true],
            'item_id' => ['type' => 'int', 'unsigned' => true],
            'cleaning_frequency' => ['type' => 'int', 'unsigned' => true],
            'created_at' => ['type' => 'datetime', 'null' => true],
            'updated_at' => ['type' => 'datetime', 'null' => true],
            'deleted_at' => ['type' => 'datetime', 'null' => true]
        ]);
        $this->forge->addPrimaryKey('id');
        $this->forge->addKey('task_submission_id');
        $this->forge->addKey('item_id');
        $this->forge->addForeignKey('task_submission_id', 'task_submissions', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('item_id', 'items', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('task_submission_items', true);
    }

    public function down()
    {
        $this->forge->dropTable('task_submission_items', true);
    }
}
