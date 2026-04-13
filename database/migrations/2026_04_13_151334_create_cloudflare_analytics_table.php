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
        Schema::create('cloudflare_analytics', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('monitor_cloudflare_check_id');
            $table->foreign('monitor_cloudflare_check_id')
                ->references('id')->on('monitor_cloudflare_checks')->cascadeOnDelete();
            $table->date('date')->index();
            $table->unsignedBigInteger('unique_visitors')->nullable();
            $table->unsignedBigInteger('requests_total')->nullable();
            $table->unsignedBigInteger('bandwidth_total')->nullable(); // in bytes
            $table->timestamps();
            $table->unique(['monitor_cloudflare_check_id', 'date']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cloudflare_analytics');
    }
};
