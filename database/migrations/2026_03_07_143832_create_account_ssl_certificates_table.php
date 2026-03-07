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
        Schema::create('account_ssl_certificates', function (Blueprint $table) {
            $table->id();
            $table->foreignId('account_id')->constrained()->cascadeOnDelete();
            $table->string('user');
            $table->string('type');
            $table->string('servername');
            $table->json('domains');
            $table->timestamp('expires_at')->nullable();
            $table->string('issuer')->nullable();
            $table->timestamps();

            $table->unique(['account_id', 'servername']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('account_ssl_certificates');
    }
};
