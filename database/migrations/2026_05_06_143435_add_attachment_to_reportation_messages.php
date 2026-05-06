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
        $table->string('attachment_path')->nullable()->after('message');
        $table->string('attachment_name')->nullable()->after('attachment_path');
        $table->string('attachment_type')->nullable()->after('attachment_name'); // 'image' | 'file'
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('reportation_messages', function (Blueprint $table) {
            //
        });
    }
};
