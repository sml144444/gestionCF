<?php
// database/migrations/2024_01_01_000001_create_user_notifications_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('user_notifications', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();

            // Type allows frontend to choose icon / color
            // e.g. reclamation_reply | reclamation_assigned | reclamation_status | note | default
            $table->string('type', 60)->default('default');

            $table->string('message');           // Human-readable text shown in dropdown
            $table->string('url')->nullable();   // Where to navigate on click
            $table->json('data')->nullable();    // Any extra context (ids, labels…)
            $table->timestamp('read_at')->nullable();
            $table->timestamps();

            // Fast unread-count queries
            $table->index(['user_id', 'read_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('user_notifications');
    }
};