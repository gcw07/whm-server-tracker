<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateLighthouseAuditsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
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
            $table->longText('report')->nullable();
            $table->timestamps();

            $table->foreign('monitor_id')->references('id')->on('monitors')->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('monitors');
    }
}
