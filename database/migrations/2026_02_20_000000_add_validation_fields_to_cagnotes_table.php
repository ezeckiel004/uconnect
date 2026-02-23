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
            $table->enum('publication_status', ['pending', 'under_review', 'approved', 'rejected'])->default('pending')->after('status');
            $table->timestamp('validated_at')->nullable()->after('publication_status');
            $table->foreignId('validated_by')->nullable()->constrained('users')->onDelete('set null')->after('validated_at');
            $table->text('rejection_reason')->nullable()->after('validated_by');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('cagnotes', function (Blueprint $table) {
            $table->dropColumn(['publication_status', 'validated_at', 'validated_by', 'rejection_reason']);
        });
    }
};
