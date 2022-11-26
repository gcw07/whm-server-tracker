<?php

use App\Enums\BlacklistStatusEnum;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('monitors', function (Blueprint $table) {
            $table->after('certificate_check_failure_reason', function (Blueprint $table) {
                $table->boolean('blacklist_check_enabled')->default(true);
                $table->string('blacklist_status')->default(BlacklistStatusEnum::NotYetChecked->value);
                $table->text('blacklist_check_failure_reason')->nullable();
            });
        });
    }
};
