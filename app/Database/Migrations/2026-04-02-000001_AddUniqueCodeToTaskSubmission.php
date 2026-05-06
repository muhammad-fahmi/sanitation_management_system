<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddUniqueCodeToTaskSubmission extends Migration
{
    public function up()
    {
        try {
            $this->forge->addColumn('r_task_submission', [
                'unique_code' => [
                    'type' => 'varchar',
                    'constraint' => 50,
                    'null' => true,
                    'default' => null,
                    'after' => 'status',
                ],
            ]);
        } catch (\Throwable $e) {
            $message = strtolower($e->getMessage());
            if (!str_contains($message, 'already exists') && !str_contains($message, 'duplicate column')) {
                throw $e;
            }
        }

        // PostgreSQL cannot replace a view when column names/order differ.
        // Drop first, then create to avoid "cannot change name of view column" errors.
        $this->db->query('DROP VIEW IF EXISTS vw_task_submission_details');
        $this->db->query(
            "CREATE OR REPLACE VIEW vw_task_submission_details AS
            SELECT
                ts.task_submission_id,
                ts.date,
                ts.location_id,
                ts.item_id,
                ts.time_cleaned,
                ts.revision_message,
                ts.status,
                ts.unique_code,
                ts.submitted_by,
                ts.verified_by,
                ts.verified_at,
                d.task_submission_detail_id,
                d.action_id,
                d.quantity
            FROM r_task_submission ts
            JOIN r_task_submission_detail d ON d.task_submission_id = ts.task_submission_id"
        );
    }

    public function down()
    {
        // Restore the original view shape before dropping the column.
        $this->db->query('DROP VIEW IF EXISTS vw_task_submission_details');
        $this->db->query(
            "CREATE OR REPLACE VIEW vw_task_submission_details AS
            SELECT
                ts.task_submission_id,
                ts.date,
                ts.location_id,
                ts.item_id,
                ts.time_cleaned,
                ts.revision_message,
                ts.status,
                ts.submitted_by,
                ts.verified_by,
                ts.verified_at,
                d.task_submission_detail_id,
                d.action_id,
                d.quantity
            FROM r_task_submission ts
            JOIN r_task_submission_detail d ON d.task_submission_id = ts.task_submission_id"
        );

        try {
            $this->forge->dropColumn('r_task_submission', 'unique_code');
        } catch (\Throwable $e) {
            $message = strtolower($e->getMessage());
            if (!str_contains($message, 'does not exist') && !str_contains($message, 'unknown column')) {
                throw $e;
            }
        }
    }
}
