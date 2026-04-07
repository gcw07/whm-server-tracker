<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('lighthouse_audits', function (Blueprint $table) {
            $table->index(['monitor_id', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::table('lighthouse_audits', function (Blueprint $table) {
            $table->dropIndex(['monitor_id', 'created_at']);
        });
    }
};
