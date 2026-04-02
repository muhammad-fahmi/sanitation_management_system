<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class TaskSubmissionDetail extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'task_submission_detail_id' => [
                'type' => 'int',
                'constraint' => 5,
                'unsigned' => true,
                'auto_increment' => true,
            ],
            'task_submission_id' => [
                'type' => 'int',
                'constraint' => 5
            ],
            'action_id' => [
                'type' => 'int',
                'constraint' => 5
            ],
            'quantity' => [
                'type' => 'int',
                'constraint' => 5
            ],
        ]);
        $this->forge->addKey('task_submission_detail_id', true);
        $this->forge->createTable('r_task_submission_detail', true);
    }

    public function down()
    {
        $this->forge->dropTable('r_task_submission_detail', true);
    }
}
