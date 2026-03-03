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
        // Update all associations that don't have a country and set a default country
        DB::table('users')
            ->where('type', 'association')
            ->whereNull('country')
            ->orWhere(function ($query) {
                $query->where('type', 'association')
                    ->where('country', '');
            })
            ->update(['country' => 'Maroc']);

        // Log the number of updated records
        $updated = DB::table('users')
            ->where('type', 'association')
            ->whereNotNull('country')
            ->where('country', '<>', '')
            ->count();

        \Illuminate\Support\Facades\Log::info('Migration: Updated ' . $updated . ' associations with default country (Maroc)');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Reverse: set country to NULL for associations that have 'Maroc' as default
        DB::table('users')
            ->where('type', 'association')
            ->where('country', 'Maroc')
            ->update(['country' => null]);
    }
};
