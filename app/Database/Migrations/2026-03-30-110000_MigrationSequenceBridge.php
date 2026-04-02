<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class MigrationSequenceBridge extends Migration
{
    public function up()
    {
        // No-op migration to bridge missing historical version.
    }

    public function down()
    {
        // No-op rollback.
    }
}
