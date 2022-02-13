<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('accounts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('server_id')->constrained('servers')->cascadeOnDelete();
            $table->string('domain');
            $table->string('user');
            $table->string('ip');
            $table->boolean('backup');
            $table->boolean('suspended');
            $table->string('suspend_reason');
            $table->timestamp('suspend_time')->nullable();
            $table->timestamp('setup_date')->nullable();
            $table->string('disk_used'); // in MB
            $table->string('disk_limit'); // in MB
            $table->string('plan');
            $table->timestamps();
        });
    }
};
