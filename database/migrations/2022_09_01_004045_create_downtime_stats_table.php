<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('downtime_stats', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('monitor_id');
            $table->date('date');
            $table->unsignedInteger('downtime_period');

            $table->foreign('monitor_id')->references('id')->on('monitors')->cascadeOnDelete();
        });
    }
};
