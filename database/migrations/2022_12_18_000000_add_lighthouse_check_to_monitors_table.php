<?php

use App\Enums\LighthouseStatusEnum;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('monitors', function (Blueprint $table) {
            $table->after('blacklist_check_failure_reason', function (Blueprint $table) {
                $table->boolean('lighthouse_check_enabled')->default(true);
                $table->string('lighthouse_status')->default(LighthouseStatusEnum::NotYetChecked->value);
                $table->timestamp('lighthouse_update_last_failed_at')->nullable();
                $table->timestamp('lighthouse_update_last_succeeded_at')->nullable();
                $table->text('lighthouse_check_failure_reason')->nullable();
            });
        });
    }
};
