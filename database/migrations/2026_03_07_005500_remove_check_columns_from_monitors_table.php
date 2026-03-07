<?php

use App\Enums\BlacklistStatusEnum;
use App\Enums\DomainNameStatusEnum;
use App\Enums\LighthouseStatusEnum;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('monitors', function (Blueprint $table) {
            $table->dropColumn([
                'blacklist_check_enabled',
                'blacklist_status',
                'blacklist_check_failure_reason',
                'lighthouse_check_enabled',
                'lighthouse_status',
                'lighthouse_update_last_failed_at',
                'lighthouse_update_last_succeeded_at',
                'lighthouse_check_failure_reason',
                'domain_name_check_enabled',
                'domain_name_status',
                'domain_name_expiration_date',
                'domain_name_check_failure_reason',
                'nameservers',
                'is_on_cloudflare',
            ]);
        });
    }

    public function down(): void
    {
        Schema::table('monitors', function (Blueprint $table) {
            $table->boolean('blacklist_check_enabled')->default(true);
            $table->string('blacklist_status')->default(BlacklistStatusEnum::NotYetChecked->value);
            $table->text('blacklist_check_failure_reason')->nullable();
            $table->boolean('lighthouse_check_enabled')->default(true);
            $table->string('lighthouse_status')->default(LighthouseStatusEnum::NotYetChecked->value);
            $table->timestamp('lighthouse_update_last_failed_at')->nullable();
            $table->timestamp('lighthouse_update_last_succeeded_at')->nullable();
            $table->text('lighthouse_check_failure_reason')->nullable();
            $table->boolean('domain_name_check_enabled')->default(true);
            $table->string('domain_name_status')->default(DomainNameStatusEnum::NotYetChecked->value);
            $table->timestamp('domain_name_expiration_date')->nullable();
            $table->text('domain_name_check_failure_reason')->nullable();
            $table->json('nameservers')->nullable();
            $table->boolean('is_on_cloudflare')->default(false);
        });
    }
};
