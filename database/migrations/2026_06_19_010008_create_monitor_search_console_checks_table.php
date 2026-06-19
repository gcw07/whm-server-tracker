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
        Schema::create('monitor_search_console_checks', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('monitor_id')->unique();
            $table->boolean('has_domain_property')->default(false);
            $table->string('domain_property')->nullable();
            $table->text('dns_txt_record')->nullable();
            $table->boolean('dns_added_to_cloudflare')->default(false);
            $table->timestamp('last_synced_at')->nullable();
            $table->timestamps();
            $table->foreign('monitor_id')->references('id')->on('monitors')->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('monitor_search_console_checks');
    }
};
