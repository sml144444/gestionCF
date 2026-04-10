<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('emplois_du_temps', function (Blueprint $table) {
            $table->id();
            
            $table->foreignId('id_module')->constrained('modules')->cascadeOnDelete();
            $table->foreignId('id_groupe')->constrained('groupes')->cascadeOnDelete();
            $table->foreignId('id_salle')->nullable()->constrained('salles')->nullOnDelete();
            
            $table->dateTime('date_debut');
            $table->dateTime('date_fin');
            
            // gestionnaire
            $table->foreignId('id_user')->constrained('users')->cascadeOnDelete();
            
            $table->string('jour')->nullable(); // lundi, mardi...
            $table->enum('statut', ['actif', 'annule'])->default('actif');
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('emplois_du_temps');
    }
};
