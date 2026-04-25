<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('reclamation_messages', function (Blueprint $table) {
            $table->id();

            $table->foreignId('reclamation_id')
                ->constrained('reclamations')
                ->cascadeOnDelete();

            $table->foreignId('sender_id')
                ->constrained('users')
                ->cascadeOnDelete();

            $table->text('message');

            $table->timestamps();

            $table->index(['reclamation_id', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('reclamation_messages');
    }
};