<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('lighthouse_audits', function (Blueprint $table) {
            $table->integer('first_contentful_paint')->nullable()->after('speed_index');
            $table->integer('largest_contentful_paint')->nullable()->after('first_contentful_paint');
            $table->integer('time_to_interactive')->nullable()->after('largest_contentful_paint');
            $table->integer('total_blocking_time')->nullable()->after('time_to_interactive');
            $table->float('cumulative_layout_shift')->nullable()->after('total_blocking_time');
            $table->string('form_factor')->nullable()->after('cumulative_layout_shift');
            $table->dropColumn('report');
        });
    }

    public function down(): void
    {
        Schema::table('lighthouse_audits', function (Blueprint $table) {
            $table->dropColumn([
                'first_contentful_paint',
                'largest_contentful_paint',
                'time_to_interactive',
                'total_blocking_time',
                'cumulative_layout_shift',
                'form_factor',
            ]);
            $table->longText('report')->nullable()->after('raw_results');
        });
    }
};
