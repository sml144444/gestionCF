<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('modules', function (Blueprint $table) {
            // Number of intermediate assessments (Contrôles)
            // EFM is always separate and always exists
            $table->unsignedTinyInteger('nbr_controles')
                  ->default(1)
                  ->after('nbr_heure')
                  ->comment('Number of contrôles (EFM always added automatically)');

            // annee column — skip if it already exists in your modules table
            // $table->unsignedTinyInteger('annee')->nullable()->after('type');

            // remplacant — skip if it already exists
            // $table->foreignId('id_user_remplacant')->nullable()->constrained('users')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('modules', function (Blueprint $table) {
            $table->dropColumn('nbr_controles');
        });
    }
};