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
        Schema::table('cagnotes', function (Blueprint $table) {
            $table->string('location')->nullable()->after('description');
            $table->string('category')->default('Infrastructure')->after('location');
            $table->date('start_date')->nullable()->after('deadline');
            $table->json('photos')->nullable()->after('image_url');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('cagnotes', function (Blueprint $table) {
            $table->dropColumn(['location', 'category', 'start_date', 'photos']);
        });
    }
};
