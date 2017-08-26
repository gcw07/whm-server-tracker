<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateServersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('servers', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name');
            $table->string('address');
            $table->integer('port');
            $table->string('server_type');
            $table->string('token')->nullable();
            $table->text('notes')->nullable();
            $table->json('settings');
            $table->integer('disk_used')->nullable(); // in kilobytes
            $table->integer('disk_available')->nullable(); // in kilobytes
            $table->integer('disk_total')->nullable(); // in kilobytes
            $table->integer('disk_percentage')->nullable();
            $table->boolean('backup_enabled')->nullable();
            $table->string('backup_days')->nullable();
            $table->integer('backup_retention')->nullable();
            $table->timestamp('disk_last_updated')->nullable();
            $table->timestamp('backup_last_updated')->nullable();
            $table->timestamp('accounts_last_updated')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('servers');
    }
}
