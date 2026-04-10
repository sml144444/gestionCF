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
        Schema::create('modules', function (Blueprint $table) {
            $table->id();
            
            $table->foreignId('id_filiere')->constrained('filieres')->cascadeOnDelete();
            $table->foreignId('id_option')->nullable()->constrained('options')->nullOnDelete();
            
            $table->string('name');
            $table->float('coefficience');
            $table->integer('nbr_heure');
            
            $table->foreignId('id_user')->constrained('users')->cascadeOnDelete(); // formateur
            
            $table->enum('type', ['regional', 'local']);
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('modules');
    }
};
