<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('servers', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('address');
            $table->integer('port');
            $table->string('server_type');
            $table->text('token')->nullable();
            $table->text('notes')->nullable();
            $table->json('settings')->nullable();
            $table->timestamp('server_update_last_failed_at')->nullable();
            $table->timestamp('server_update_last_succeeded_at')->nullable();
            $table->timestamps();
        });
    }
};
