<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('monitor_cloudflare_checks', function (Blueprint $table) {
            $table->string('cloudflare_account_id')->nullable()->after('cloudflare_zone_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('monitor_cloudflare_checks', function (Blueprint $table) {
            $table->dropColumn('cloudflare_account_id');
        });
    }
};
