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
        Schema::create('withdrawal_requests', function (Blueprint $table) {
            $table->id();
            
            // Foreign keys
            $table->foreignId('cagnote_id')->constrained('cagnotes')->onDelete('cascade');
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            
            // Amount information
            $table->decimal('original_amount', 12, 2)->comment('Total collected amount');
            $table->decimal('withdrawal_amount', 12, 2)->comment('90% of original amount (after 10% platform fee)');
            $table->decimal('platform_fee', 12, 2)->nullable()->comment('10% platform fee');
            
            // Status tracking
            $table->enum('status', ['pending', 'processed', 'rejected', 'failed'])->default('pending');
            $table->text('rejection_reason')->nullable();
            
            // Processing information
            $table->timestamp('processed_at')->nullable();
            $table->foreignId('processed_by')->nullable()->constrained('users')->onDelete('set null')->comment('Admin who processed this request');
            $table->string('transaction_reference')->nullable()->comment('Bank transaction reference');
            
            // Banking information snapshot
            $table->string('account_holder_name')->nullable();
            $table->string('iban')->nullable();
            $table->string('bic')->nullable();
            $table->string('bank_name')->nullable();
            $table->string('account_type')->nullable();
            $table->text('account_address')->nullable();
            $table->string('account_phone')->nullable();
            $table->string('account_email')->nullable();
            
            // Timestamps
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('withdrawal_requests');
    }
};
