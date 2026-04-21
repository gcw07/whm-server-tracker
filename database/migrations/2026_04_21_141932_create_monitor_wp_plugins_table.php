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
        Schema::create('monitor_wp_plugins', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('monitor_id');
            $table->foreign('monitor_id')->references('id')->on('monitors')->cascadeOnDelete();
            $table->string('name');
            $table->string('file');
            $table->string('version');
            $table->boolean('active')->default(false);
            $table->boolean('update_available')->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('monitor_wp_plugins');
    }
};
