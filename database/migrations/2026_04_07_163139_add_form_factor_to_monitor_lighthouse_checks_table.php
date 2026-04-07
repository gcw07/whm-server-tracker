<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Add column if not already present (idempotent after a partial run).
        if (! Schema::hasColumn('monitor_lighthouse_checks', 'form_factor')) {
            Schema::table('monitor_lighthouse_checks', function (Blueprint $table) {
                $table->string('form_factor')->default('desktop')->after('monitor_id');
            });
        }

        // Add composite unique first so MySQL can use it to satisfy the FK before we drop the old one.
        Schema::table('monitor_lighthouse_checks', function (Blueprint $table) {
            $table->unique(['monitor_id', 'form_factor']);
        });

        Schema::table('monitor_lighthouse_checks', function (Blueprint $table) {
            $table->dropUnique(['monitor_id']);
        });

        // Insert a mobile row for every existing monitor check record.
        DB::table('monitor_lighthouse_checks')
            ->orderBy('id')
            ->each(function (object $row) {
                DB::table('monitor_lighthouse_checks')->insert([
                    'monitor_id' => $row->monitor_id,
                    'form_factor' => 'mobile',
                    'enabled' => $row->enabled,
                    'status' => $row->status,
                    'last_failed_at' => null,
                    'last_succeeded_at' => null,
                    'failure_reason' => null,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            });
    }

    public function down(): void
    {
        // Remove mobile rows first.
        DB::table('monitor_lighthouse_checks')->where('form_factor', 'mobile')->delete();

        Schema::table('monitor_lighthouse_checks', function (Blueprint $table) {
            $table->dropUnique(['monitor_id', 'form_factor']);
            $table->unique(['monitor_id']);
            $table->dropColumn('form_factor');
        });
    }
};
