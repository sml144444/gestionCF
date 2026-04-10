<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('groupes', function (Blueprint $table) {
            // Group name: G1A, G1B, G2A, etc.
            $table->string('name')->nullable()->after('id');

            // Year: 1 = première année, 2 = deuxième année (2 ans ou 2.5 ans)
            $table->unsignedTinyInteger('annee')->default(1)->after('id_filiere');
        });
    }

    public function down(): void
    {
        Schema::table('groupes', function (Blueprint $table) {
            $table->dropColumn(['name', 'annee']);
        });
    }
};