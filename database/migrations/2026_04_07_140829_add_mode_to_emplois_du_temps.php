<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('emplois_du_temps', function (Blueprint $table) {
            $table->enum('mode', ['presentiel', 'distance'])
                  ->default('presentiel')
                  ->after('statut');

            // Link for remote sessions (Teams, Zoom, etc.)
            $table->string('lien_distance')->nullable()->after('mode');
        });
    }

    public function down(): void
    {
        Schema::table('emplois_du_temps', function (Blueprint $table) {
            $table->dropColumn(['mode', 'lien_distance']);
        });
    }
};