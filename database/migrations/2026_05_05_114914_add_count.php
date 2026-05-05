<?php
// database/migrations/2024_01_02_000001_add_count_to_user_notifications_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('user_notifications', function (Blueprint $table) {
            // Tracks how many times the same notification was triggered
            // before the user read it. Used for "3 nouveaux messages" grouping.
            $table->unsignedSmallInteger('count')->default(1)->after('data');
        });
    }

    public function down(): void
    {
        Schema::table('user_notifications', function (Blueprint $table) {
            $table->dropColumn('count');
        });
    }
};