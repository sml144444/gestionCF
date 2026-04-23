<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Delete all retard records (no longer used)
        DB::table('absence_retard')->where('type', 'retard')->delete();

        // 2. Change the enum to only 'absence'
        //    MySQL requires a full ALTER on enum columns
        DB::statement("ALTER TABLE absence_retard MODIFY COLUMN type ENUM('absence') NOT NULL");

        // 3. session_part already exists (from previous migration) as varchar(2).
        //    Just make sure default is 's1' and it accepts s1-s4.
        Schema::table('absence_retard', function (Blueprint $table) {
            $table->string('session_part', 2)->nullable(false)->default('s1')->change();
        });
    }

    public function down(): void
    {
        DB::statement("ALTER TABLE absence_retard MODIFY COLUMN type ENUM('retard','absence') NOT NULL");

        Schema::table('absence_retard', function (Blueprint $table) {
            $table->string('session_part', 2)->nullable()->default('s1')->change();
        });
    }
};