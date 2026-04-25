<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Add assigned_to column
        Schema::table('reclamations', function (Blueprint $table) {
            $table->foreignId('assigned_to')
                ->nullable()
                ->after('status')
                ->constrained('users')
                ->nullOnDelete();
        });

        // 2. Extend status enum to include 'en_cours'
        DB::statement("
            ALTER TABLE reclamations
            MODIFY COLUMN status ENUM('en_attente','en_cours','traite')
            NOT NULL DEFAULT 'en_attente'
        ");
    }

    public function down(): void
    {
        Schema::table('reclamations', function (Blueprint $table) {
            $table->dropForeign(['assigned_to']);
            $table->dropColumn('assigned_to');
        });

        DB::statement("
            ALTER TABLE reclamations
            MODIFY COLUMN status ENUM('en_attente','traite')
            NOT NULL DEFAULT 'en_attente'
        ");
    }
};