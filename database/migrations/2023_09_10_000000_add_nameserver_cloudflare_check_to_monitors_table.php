<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('monitors', function (Blueprint $table) {
            $table->after('domain_name_check_failure_reason', function (Blueprint $table) {
                $table->json('nameservers')->nullable();
                $table->boolean('is_on_cloudflare')->default(false);
            });
        });
    }
};
