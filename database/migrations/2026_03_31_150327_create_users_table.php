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
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email')->unique();
            $table->string('password');
            
            $table->string('cin')->nullable();
            $table->string('phone')->nullable();
            
            $table->date('date_embauche')->nullable();
            $table->string('matricule_formateur')->nullable();
            $table->string('specialite')->nullable();
            $table->integer('nbr_heure_limit')->nullable();
            
            $table->json('document')->nullable();
            $table->string('photo')->nullable();
            $table->date('date_naissance')->nullable();
            
            $table->enum('role', ['admin', 'gestionnaire', 'formateur', 'stagiaire']);
            
            $table->foreignId('id_filiere')->nullable()->constrained('filieres')->nullOnDelete();
            $table->foreignId('id_option')->nullable()->constrained('options')->nullOnDelete();
            
            // $table->foreignId('id_groupe')
            // ->nullable()
            // ->constrained('groupes')
            // ->nullOnDelete();
            // $table->timestamps();

            $table->timestamps();
            $table->unsignedBigInteger('id_groupe')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};
