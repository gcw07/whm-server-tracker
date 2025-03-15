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
        Schema::create('lighthouse_audits', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('monitor_id');
            $table->date('date');

            $table->integer('performance_score');
            $table->integer('accessibility_score');
            $table->integer('best_practices_score');
            $table->integer('seo_score');
            $table->integer('pwa_score');
            $table->integer('speed_index');
            $table->json('raw_results')->nullable();
            $table->longText('report')->nullable();
            $table->timestamps();

            $table->foreign('monitor_id')->references('id')->on('monitors')->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::drop('monitors');
    }
};
