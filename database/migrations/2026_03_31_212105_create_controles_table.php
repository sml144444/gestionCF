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
        Schema::create('controles', function (Blueprint $table) {
            $table->id();
            
            $table->string('titre');
            
            $table->foreignId('id_module')->constrained('modules')->cascadeOnDelete();
            $table->foreignId('id_groupe')->constrained('groupes')->cascadeOnDelete();
            
            $table->enum('type', ['controle', 'EFM']);
            
            $table->integer('duree')->nullable(); // minutes
            $table->text('description')->nullable();
            
            $table->string('variante')->nullable();
            
            $table->foreignId('created_by')
                ->constrained('users')
                ->cascadeOnDelete();
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('controles');
    }
};
