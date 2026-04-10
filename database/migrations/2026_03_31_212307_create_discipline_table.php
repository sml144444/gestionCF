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
        Schema::create('discipline', function (Blueprint $table) {
            $table->id();
            
            $table->foreignId('id_user')->constrained('users')->cascadeOnDelete();
            
            $table->float('total_absence_heures')->default(0);
            $table->float('total_retard_minutes')->default(0);
            
            $table->float('note_discipline')->default(20);
            
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('discipline');
    }
};
