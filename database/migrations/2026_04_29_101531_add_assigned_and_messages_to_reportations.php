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
        // Add assigned_to column to reportations table
        Schema::table('reportations', function (Blueprint $table) {
            $table->unsignedBigInteger('assigned_to')->nullable()->after('valide_by');
            $table->foreign('assigned_to')->references('id')->on('users')->nullOnDelete();
        });

        // Create reportation_messages table
        Schema::create('reportation_messages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('reportation_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->text('message');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Drop reportation_messages table first (due to foreign key constraints)
        Schema::dropIfExists('reportation_messages');

        // Drop the foreign key and column from reportations table
        Schema::table('reportations', function (Blueprint $table) {
            $table->dropForeign(['assigned_to']);
            $table->dropColumn('assigned_to');
        });
    }
};