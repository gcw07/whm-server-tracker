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
            $table->string('php_version')->nullable()->after('wordpress_version');
            $table->string('site_name')->nullable()->after('php_version');
            $table->string('active_theme')->nullable()->after('site_name');
            $table->string('active_theme_version')->nullable()->after('active_theme');
            $table->unsignedInteger('plugins_installed_count')->nullable()->after('active_theme_version');
            $table->unsignedInteger('themes_installed_count')->nullable()->after('plugins_installed_count');
            $table->unsignedInteger('plugin_updates_count')->nullable()->after('themes_installed_count');
            $table->unsignedInteger('theme_updates_count')->nullable()->after('plugin_updates_count');
            $table->string('check_source')->nullable()->after('theme_updates_count');
            $table->string('agent_version')->nullable()->after('check_source');
            $table->timestamp('last_response_at')->nullable()->after('agent_version');
        });
    }

    public function down(): void
    {
        Schema::table('monitor_wordpress_checks', function (Blueprint $table) {
            $table->dropColumn([
                'php_version',
                'site_name',
                'active_theme',
                'active_theme_version',
                'plugins_installed_count',
                'themes_installed_count',
                'plugin_updates_count',
                'theme_updates_count',
                'check_source',
                'agent_version',
                'last_response_at',
            ]);
        });
    }
};
