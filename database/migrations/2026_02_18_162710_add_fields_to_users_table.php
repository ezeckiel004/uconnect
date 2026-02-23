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
        Schema::table('users', function (Blueprint $table) {
            $table->enum('type', ['association', 'donor', 'admin'])->default('donor')->after('email');
            $table->string('code')->unique()->nullable()->after('type');
            $table->string('phone_number')->nullable()->after('code');
            $table->text('description')->nullable()->after('phone_number');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['type', 'code', 'phone_number', 'description']);
        });
    }
};
