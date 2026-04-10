<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('cours', function (Blueprint $table) {
            $table->float('duree')->nullable()->after('description'); 
            // pour calcul progress
        });
    }

    public function down(): void
    {
        Schema::table('cours', function (Blueprint $table) {
            $table->dropColumn('duree');
        });
    }
};