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
            $table->string('token')->nullable();
            $table->text('notes')->nullable();
            $table->json('settings');
            $table->timestamp('details_last_updated')->nullable();
            $table->timestamp('accounts_last_updated')->nullable();
            $table->timestamps();
        });
    }
};
