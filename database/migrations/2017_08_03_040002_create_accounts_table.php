<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAccountsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('accounts', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('server_id');
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
}
