// database/migrations/xxxx_add_seen_at_to_reclamation_messages.php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('reclamation_messages', function (Blueprint $table) {
            $table->timestamp('seen_at')->nullable()->after('message');
            $table->timestamp('edited_at')->nullable()->after('seen_at');
        });
    }

    public function down(): void
    {
        Schema::table('reclamation_messages', function (Blueprint $table) {
            $table->dropColumn(['seen_at', 'edited_at']);
        });
    }
};