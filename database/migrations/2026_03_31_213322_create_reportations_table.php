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
        Schema::create('reportations', function (Blueprint $table) {
            $table->id();
            
            $table->foreignId('id_emplois_du_temps')
                ->constrained('emplois_du_temps')
                ->cascadeOnDelete();
            
            // formateur 
            $table->foreignId('id_user')
                ->constrained('users')
                ->cascadeOnDelete();
            
            $table->dateTime('nouvelle_date_debut');
            $table->dateTime('nouvelle_date_fin');
            
            $table->text('raison')->nullable();
            
            $table->enum('status', ['en_attente', 'valide', 'refuse'])
                ->default('en_attente');
            
            // gestionnaire 
            $table->foreignId('valide_by')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('reportations');
    }
};
