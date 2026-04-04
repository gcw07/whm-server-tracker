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
        Schema::create('monitor_blacklist_results', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('monitor_id');
            $table->foreign('monitor_id')->references('id')->on('monitors')->cascadeOnDelete();
            $table->string('driver');
            $table->string('checked_value')->nullable();
            $table->boolean('listed')->default(false);
            $table->text('failure_reason')->nullable();
            $table->timestamp('checked_at')->nullable();
            $table->timestamps();

            $table->unique(['monitor_id', 'driver']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('monitor_blacklist_results');
    }
};
