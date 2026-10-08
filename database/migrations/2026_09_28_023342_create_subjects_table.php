<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Only add the column if it isn't there yet
        if (Schema::hasTable('subjects') && !Schema::hasColumn('subjects', 'category')) {
            Schema::table('subjects', function (Blueprint $table) {
                $table->string('category', 50)->default('General');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('subjects', 'category')) {
            Schema::table('subjects', function (Blueprint $table) {
                $table->dropColumn('category');
            });
        }
    }
};