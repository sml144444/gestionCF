<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
   // database/migrations/xxxx_add_session_part_to_absence_retard.php
public function up(): void
{
    Schema::table('absence_retard', function (Blueprint $table) {
        $table->string('session_part', 2)->nullable()->default('s1')->after('duree');
        // s1 = first 2.5h, s2 = second 2.5h
    });
}

public function down(): void
{
    Schema::table('absence_retard', function (Blueprint $table) {
        $table->dropColumn('session_part');
    });
}
};
