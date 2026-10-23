<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Laravel saves created_at and updated_at on every task, so the tasks table needs both columns.
     * Each one is added only if it is missing, so this is safe to run on any database.
     */
    public function up(): void
    {
        if (!Schema::hasColumn('tasks', 'created_at')) {
            Schema::table('tasks', function (Blueprint $table) {
                $table->timestamp('created_at')->nullable();
            });
        }

        if (!Schema::hasColumn('tasks', 'updated_at')) {
            Schema::table('tasks', function (Blueprint $table) {
                $table->timestamp('updated_at')->nullable();
            });
        }

        // Give the tasks that already exist a date (the Completed Tasks page orders by updated_at)
        DB::table('tasks')->whereNull('created_at')->update(['created_at' => now()]);
        DB::table('tasks')->whereNull('updated_at')->update(['updated_at' => DB::raw('COALESCE(created_at, CURRENT_TIMESTAMP)')]);
    }

    public function down(): void
    {
        // Nothing to undo: the columns may have existed before this migration, so they are kept.
    }
};