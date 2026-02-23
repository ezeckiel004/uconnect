<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Revert status to original ENUM values
        DB::statement("ALTER TABLE cagnotes MODIFY status ENUM('active', 'completed', 'archived') DEFAULT 'active'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // If needed, revert back
        DB::statement("ALTER TABLE cagnotes MODIFY status ENUM('active', 'completed', 'archived') DEFAULT 'active'");
    }
};
