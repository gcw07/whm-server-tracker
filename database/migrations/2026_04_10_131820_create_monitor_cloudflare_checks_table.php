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
        Schema::create('monitor_cloudflare_checks', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('monitor_id')->unique();
            $table->foreign('monitor_id')->references('id')->on('monitors')->cascadeOnDelete();
            $table->boolean('enabled')->default(true);
            $table->string('cloudflare_zone_id')->nullable()->index();
            $table->string('zone_status')->nullable();
            $table->timestamp('last_synced_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('monitor_cloudflare_checks');
    }
};
