<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('module_formateur_history', function (Blueprint $table) {
            $table->id();
            $table->foreignId('module_id')
                  ->constrained('modules')
                  ->cascadeOnDelete();
            $table->foreignId('formateur_id')
                  ->constrained('users')
                  ->cascadeOnDelete();
            $table->enum('type', ['principal', 'remplacement']);
            $table->timestamp('start_date');
            $table->timestamp('end_date')->nullable();   // null = still active
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->index(['module_id', 'is_active']);
            $table->index(['module_id', 'type', 'is_active']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('module_formateur_history');
    }
};