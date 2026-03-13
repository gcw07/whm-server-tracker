<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement('
            INSERT INTO monitor_wordpress_checks (monitor_id, enabled, status, created_at, updated_at)
            SELECT id, true, \'not yet checked\', NOW(), NOW()
            FROM monitors
            WHERE id NOT IN (SELECT monitor_id FROM monitor_wordpress_checks)
        ');
    }

    public function down(): void
    {
        DB::table('monitor_wordpress_checks')->truncate();
    }
};
