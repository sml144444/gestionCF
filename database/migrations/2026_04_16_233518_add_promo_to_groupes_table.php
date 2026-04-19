<?php
// database/migrations/xxxx_add_promo_to_groupes_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('groupes', function (Blueprint $table) {
            // e.g. 2024 → means Promo 2024–2026 (end year derived from filière.duree)
            $table->unsignedSmallInteger('promo')->nullable()->after('annee');
        });
    }

    public function down(): void
    {
        Schema::table('groupes', function (Blueprint $table) {
            $table->dropColumn('promo');
        });
    }
};