<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('schedules', function (Blueprint $table) {
            if (! Schema::hasColumn('schedules', 'study_date')) {
                $table->date('study_date')->nullable();
            }

            if (! Schema::hasColumn('schedules', 'start_time')) {
                $table->time('start_time')->nullable();
            }

            if (! Schema::hasColumn('schedules', 'end_time')) {
                $table->time('end_time')->nullable();
            }
        });
    }

    public function down(): void
    {
        Schema::table('schedules', function (Blueprint $table) {
            foreach (['study_date', 'start_time', 'end_time'] as $column) {
                if (Schema::hasColumn('schedules', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};