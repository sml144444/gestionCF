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
        Schema::create('absence_retard', function (Blueprint $table) {
            $table->id();
            
            $table->foreignId('id_user')->constrained('users')->cascadeOnDelete();
            
            $table->foreignId('id_cours')->constrained('cours')->cascadeOnDelete();
            
            $table->enum('type', ['retard', 'absence']);
            
            $table->float('duree')->nullable(); // heures ou minutes
            
            $table->boolean('justifie')->default(false);
            
            $table->string('file_justification')->nullable();
            
            $table->dateTime('date_event');
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('absence_retard');
    }
};
