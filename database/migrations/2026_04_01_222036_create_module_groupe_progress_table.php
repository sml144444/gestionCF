<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('module_groupe_progress', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_module')->constrained('modules')->cascadeOnDelete();
            $table->foreignId('id_groupe')->constrained('groupes')->cascadeOnDelete();
            $table->enum('status', ['en_cours', 'termine'])->default('en_cours');
            $table->timestamps();

            $table->unique(['id_module', 'id_groupe']); // un seul progress par module+groupe
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('module_groupe_progress');
    }
};