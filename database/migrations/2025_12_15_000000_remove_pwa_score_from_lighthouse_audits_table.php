<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('lighthouse_audits', function (Blueprint $table) {
            $table->dropColumn('pwa_score');
        });
    }
};
