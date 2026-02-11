<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class TaskSubmissions extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id' => ['type' => 'int', 'unsigned' => true, 'auto_increment' => true],
            'submission_code' => ['type' => 'varchar', 'constraint' => 255],
            'date' => ['type' => 'datetime'],
            'room_id' => ['type' => 'int', 'unsigned' => true],
            'visit_frequency' => ['type' => 'int', 'unsigned' => true],
            'revision_message' => ['type' => 'text', 'null' => true],
            'status' => ['type' => 'enum', 'constraint' => ['pending_review', 'revision_requested', 'approved', 'rejected']], // pending, revised, verified, selesai
            'submitted_by' => ['type' => 'int', 'unsigned' => true],
            'verified_by' => ['type' => 'int', 'unsigned' => true],
            'created_at' => ['type' => 'datetime', 'null' => true],
            'updated_at' => ['type' => 'datetime', 'null' => true],
            'deleted_at' => ['type' => 'datetime', 'null' => true]
        ]);
        $this->forge->addPrimaryKey('id');
        $this->forge->addKey('date');
        $this->forge->addKey('room_id');
        $this->forge->addKey('submitted_by');
        $this->forge->addKey('verified_by');
        $this->forge->addKey('deleted_at');
        $this->forge->addUniqueKey('submission_code');
        $this->forge->addForeignKey('room_id', 'rooms', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('submitted_by', 'users', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('verified_by', 'users', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('task_submissions', true);
    }

    public function down()
    {
        $this->forge->dropTable('task_submissions', true);
    }
}
