<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class TaskSubmissionAttachments extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id' => ['type' => 'int', 'constraint' => 5, 'unsigned' => true, 'auto_increment' => true],
            'task_submission_action_id' => ['type' => 'int', 'constraint' => 5, 'unsigned' => true],
            'file_path' => ['type' => 'text', 'null' => true, 'default' => null],
            'created_at' => ['type' => 'datetime', 'null' => true],
            'updated_at' => ['type' => 'datetime', 'null' => true],
            'deleted_at' => ['type' => 'datetime', 'null' => true]
        ]);
        $this->forge->addPrimaryKey('id');
        $this->forge->addKey('task_submission_action_id');
        $this->forge->addForeignKey('task_submission_action_id', 'task_submission_actions', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('task_submission_attachments', true);
    }

    public function down()
    {
        $this->forge->dropTable('task_submission_attachments', true);
    }
}
