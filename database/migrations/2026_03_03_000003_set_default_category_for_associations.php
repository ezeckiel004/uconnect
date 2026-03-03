<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Update all associations that don't have a category
        DB::table('users')
            ->where('type', 'association')
            ->whereNull('category')
            ->orWhere(function ($query) {
                $query->where('type', 'association')
                    ->where('category', '');
            })
            ->update(['category' => 'Sociale']);

        // Log the number of updated records
        $updated = DB::table('users')
            ->where('type', 'association')
            ->whereNotNull('category')
            ->where('category', '<>', '')
            ->count();

        \Illuminate\Support\Facades\Log::info('Migration: Updated ' . $updated . ' associations with default category (Sociale)');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Reverse: set category to NULL for associations that have 'Sociale' as default
        DB::table('users')
            ->where('type', 'association')
            ->where('category', 'Sociale')
            ->update(['category' => null]);
    }
};
