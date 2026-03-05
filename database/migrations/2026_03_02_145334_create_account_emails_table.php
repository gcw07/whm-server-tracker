<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('account_emails', function (Blueprint $table) {
            $table->id();
            $table->foreignId('account_id')->constrained()->cascadeOnDelete();
            $table->string('email');
            $table->string('user');
            $table->string('domain');
            $table->unsignedBigInteger('disk_used')->default(0);
            $table->unsignedBigInteger('disk_quota')->default(0);
            $table->float('disk_used_percent')->default(0);
            $table->boolean('suspended_incoming')->default(false);
            $table->boolean('suspended_login')->default(false);
            $table->timestamps();

            $table->unique(['account_id', 'email']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('account_emails');
    }
};
