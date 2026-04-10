<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // ── Update EDU table ──────────────────────────────────
        Schema::table('edu', function (Blueprint $table) {
            // Add new columns
            $table->string('nom')->nullable()->after('edu_email');
            $table->string('prenom')->nullable()->after('nom');
            $table->string('filiere_code')->nullable()->after('prenom');
            $table->string('groupe_code')->nullable()->after('filiere_code');

            // Drop old FK columns if they exist
            if (Schema::hasColumn('edu', 'id_filiere')) {
                $table->dropForeign(['id_filiere']);
                $table->dropColumn('id_filiere');
            }
            if (Schema::hasColumn('edu', 'id_groupe')) {
                $table->dropForeign(['id_groupe']);
                $table->dropColumn('id_groupe');
            }
        });

        // ── Add code to filieres ──────────────────────────────
        Schema::table('filieres', function (Blueprint $table) {
            $table->string('code')->nullable()->unique()->after('name');
        });

        // ── Add code to groupes ───────────────────────────────
        Schema::table('groupes', function (Blueprint $table) {
            $table->string('code')->nullable()->unique()->after('name');
        });
    }

    public function down(): void
    {
        Schema::table('edu', function (Blueprint $table) {
            $table->dropColumn(['nom', 'prenom', 'filiere_code', 'groupe_code']);
            $table->foreignId('id_filiere')->nullable()->constrained('filieres')->nullOnDelete();
            $table->foreignId('id_groupe')->nullable()->constrained('groupes')->nullOnDelete();
        });

        Schema::table('filieres', function (Blueprint $table) {
            $table->dropColumn('code');
        });

        Schema::table('groupes', function (Blueprint $table) {
            $table->dropColumn('code');
        });
    }
};