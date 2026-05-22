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
        Schema::table('monitor_wordpress_checks', function (Blueprint $table) {
            $table->string('core_update_version')->nullable()->after('theme_updates_count');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('monitor_wordpress_checks', function (Blueprint $table) {
            $table->dropColumn('core_update_version');
        });
    }
};
