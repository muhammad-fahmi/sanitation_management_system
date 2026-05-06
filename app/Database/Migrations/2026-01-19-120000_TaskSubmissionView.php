<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class TaskSubmissionView extends Migration
{
    public function up()
    {
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
    }

    public function down()
    {
        $this->db->query('DROP VIEW IF EXISTS vw_task_submission_details');
    }
}
