<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
// database/migrations/xxxx_add_remplacant_to_modules.php
public function up(): void
{
    Schema::table('modules', function (Blueprint $table) {
        $table->foreignId('id_user_remplacant')
              ->nullable()
              ->after('id_user')
              ->constrained('users')
              ->nullOnDelete();
    });
}

public function down(): void
{
    Schema::table('modules', function (Blueprint $table) {
        $table->dropForeign(['id_user_remplacant']);
        $table->dropColumn('id_user_remplacant');
    });
}
};
