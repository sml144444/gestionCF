// database/migrations/xxxx_add_log_id_to_edu_table.php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('edu', function (Blueprint $table) {
            $table->foreignId('edu_import_log_id')
                  ->nullable()
                  ->after('used')
                  ->constrained('edu_import_logs')
                  ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('edu', function (Blueprint $table) {
            $table->dropForeign(['edu_import_log_id']);
            $table->dropColumn('edu_import_log_id');
        });
    }
};