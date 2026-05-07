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
    Schema::table('reportation_messages', function (Blueprint $table) {
        $table->timestamp('seen_at')->nullable()->after('attachment_type');
    });
}
public function down(): void
{
    Schema::table('reportation_messages', function (Blueprint $table) {
        $table->dropColumn('seen_at');
    });
}
};
