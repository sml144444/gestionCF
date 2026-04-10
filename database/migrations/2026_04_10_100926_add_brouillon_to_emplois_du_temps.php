<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // MySQL: modify enum to add 'brouillon'
        DB::statement("
            ALTER TABLE emplois_du_temps
            MODIFY COLUMN statut ENUM('actif','annule','brouillon')
            NOT NULL DEFAULT 'brouillon'
        ");
    }

    public function down(): void
    {
        // Revert draft rows to actif first, then remove the value
        DB::table('emplois_du_temps')
            ->where('statut', 'brouillon')
            ->update(['statut' => 'actif']);

        DB::statement("
            ALTER TABLE emplois_du_temps
            MODIFY COLUMN statut ENUM('actif','annule')
            NOT NULL DEFAULT 'actif'
        ");
    }
};