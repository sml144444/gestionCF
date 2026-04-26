<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('absence_retard', function (Blueprint $table) {
            // admin explicitly approved this absence without requiring a justification file.
            // justifie stays FALSE — the absence is NOT justified — but the warning is suppressed.
            $table->boolean('admin_validated')
                  ->default(false)
                  ->after('file_justification')
                  ->comment('Admin approved absence without file; suppresses formateur warning');
        });
    }

    public function down(): void
    {
        Schema::table('absence_retard', function (Blueprint $table) {
            $table->dropColumn('admin_validated');
        });
    }
};