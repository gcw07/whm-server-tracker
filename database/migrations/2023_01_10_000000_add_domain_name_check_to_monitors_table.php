<?php

use App\Enums\DomainNameStatusEnum;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('monitors', function (Blueprint $table) {
            $table->after('lighthouse_check_failure_reason', function (Blueprint $table) {
                $table->boolean('domain_name_check_enabled')->default(true);
                $table->string('domain_name_status')->default(DomainNameStatusEnum::NotYetChecked->value);
                $table->timestamp('domain_name_expiration_date')->nullable();
                $table->text('domain_name_check_failure_reason')->nullable();
            });
        });
    }
};
