<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('monitor_outages', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('monitor_id');
            $table->dateTime('started_at');
            $table->dateTime('ended_at');
            $table->unsignedInteger('duration_seconds');
            $table->timestamp('created_at')->nullable();

            $table->foreign('monitor_id')->references('id')->on('monitors')->cascadeOnDelete();
            $table->index(['monitor_id', 'started_at', 'ended_at', 'duration_seconds'], 'monitor_outages_covering_index');
        });

        Schema::dropIfExists('downtime_stats');
    }

    public function down(): void
    {
        Schema::dropIfExists('monitor_outages');
    }
};
