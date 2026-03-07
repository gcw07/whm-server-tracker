<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement('
            INSERT INTO monitor_blacklist_checks (monitor_id, enabled, status, failure_reason, created_at, updated_at)
            SELECT id, blacklist_check_enabled, blacklist_status, blacklist_check_failure_reason, NOW(), NOW()
            FROM monitors
        ');

        DB::statement('
            INSERT INTO monitor_lighthouse_checks (monitor_id, enabled, status, last_failed_at, last_succeeded_at, failure_reason, created_at, updated_at)
            SELECT id, lighthouse_check_enabled, lighthouse_status, lighthouse_update_last_failed_at, lighthouse_update_last_succeeded_at, lighthouse_check_failure_reason, NOW(), NOW()
            FROM monitors
        ');

        DB::statement('
            INSERT INTO monitor_domain_checks (monitor_id, enabled, status, expiration_date, failure_reason, nameservers, is_on_cloudflare, created_at, updated_at)
            SELECT id, domain_name_check_enabled, domain_name_status, domain_name_expiration_date, domain_name_check_failure_reason, nameservers, is_on_cloudflare, NOW(), NOW()
            FROM monitors
        ');
    }

    public function down(): void
    {
        DB::table('monitor_blacklist_checks')->truncate();
        DB::table('monitor_lighthouse_checks')->truncate();
        DB::table('monitor_domain_checks')->truncate();
    }
};
