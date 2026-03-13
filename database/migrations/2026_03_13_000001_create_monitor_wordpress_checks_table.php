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
        Schema::create('monitor_wordpress_checks', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('monitor_id')->unique();
            $table->foreign('monitor_id')->references('id')->on('monitors')->cascadeOnDelete();
            $table->boolean('enabled')->default(true);
            $table->string('status')->default('not yet checked');
            $table->string('wordpress_version')->nullable();
            $table->text('failure_reason')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('monitor_wordpress_checks');
    }
};
