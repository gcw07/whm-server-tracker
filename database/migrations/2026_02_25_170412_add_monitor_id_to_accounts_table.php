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
        Schema::table('accounts', function (Blueprint $table) {
            $table->after('server_id', function (Blueprint $table) {
                $table->unsignedInteger('monitor_id')->nullable();
            });
            $table->foreign('monitor_id')->references('id')->on('monitors')->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('accounts', function (Blueprint $table) {
            $table->dropForeign(['monitor_id']);
            $table->dropColumn('monitor_id');
        });
    }
};
