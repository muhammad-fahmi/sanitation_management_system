<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class TaskSubmissionHistories extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id' => ['type' => 'INT', 'auto_increment' => true],
            'task_submission_id' => ['type' => 'INT', 'unsigned' => true], // Foreign Key ke task_submissions
            'user_id' => ['type' => 'INT'], // User ID siapa yang mengubah status (User/Supervisor)

            // Mencatat perubahan status
            'previous_status' => ['type' => 'VARCHAR', 'constraint' => 50, 'null' => true],
            'new_status' => ['type' => 'VARCHAR', 'constraint' => 50],

            // PENTING: Pesan revisi atau catatan approval
            'remarks' => ['type' => 'TEXT', 'null' => true],

            'created_at' => ['type' => 'DATETIME'],
        ]);
        $this->forge->addPrimaryKey('id');
        $this->forge->addKey('task_submission_id');
        $this->forge->addForeignKey('task_submission_id', 'task_submissions', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('task_submission_histories');
    }

    public function down()
    {
        $this->forge->dropTable('task_submission_histories');
    }
}
