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
        Schema::create('bulletin', function (Blueprint $table) {
            $table->id();
            
            $table->foreignId('id_user')->constrained('users')->cascadeOnDelete();
            
            $table->float('moyenne_generale');
            $table->float('note_discipline');
            $table->float('note_eff')->nullable();
            
            $table->float('note_finale');
            
            $table->integer('annee'); // 1 ou 2
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bulletin');
    }
};
