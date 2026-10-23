<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Per-subject planning info. Existing subjects get "Medium" difficulty and 3 credit hours.
        Schema::table('subjects', function (Blueprint $table) {
            if (!Schema::hasColumn('subjects', 'difficulty')) {
                $table->unsignedTinyInteger('difficulty')->default(2);   // 1 easy, 2 medium, 3 hard
            }
            if (!Schema::hasColumn('subjects', 'credit_hours')) {
                $table->unsignedTinyInteger('credit_hours')->default(3);
            }
            if (!Schema::hasColumn('subjects', 'exam_date')) {
                $table->date('exam_date')->nullable();
            }
        });

        // One row per student: study windows, session style, peak energy, fixed weekly commitments.
        if (!Schema::hasTable('study_preferences')) {
            Schema::create('study_preferences', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('user_id')->unique();
                $table->unsignedSmallInteger('session_length')->default(50);   // 25, 50 or 90
                $table->string('peak', 20)->default('evening');                // morning, afternoon, evening
                $table->json('windows')->nullable();
                $table->json('commitments')->nullable();
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('study_preferences');

        Schema::table('subjects', function (Blueprint $table) {
            foreach (['difficulty', 'credit_hours', 'exam_date'] as $column) {
                if (Schema::hasColumn('subjects', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};