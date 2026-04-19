<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('emplois_du_temps', function (Blueprint $table) {
            // Replacement formateur (null = no replacement, original formateur teaches)
            $table->foreignId('id_user_remplacant')
                  ->nullable()
                  ->after('id_user')
                  ->constrained('users')
                  ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('emplois_du_temps', function (Blueprint $table) {
            $table->dropForeign(['id_user_remplacant']);
            $table->dropColumn('id_user_remplacant');
        });
    }
};