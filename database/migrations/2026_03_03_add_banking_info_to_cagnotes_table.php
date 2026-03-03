<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Add banking information fields for campaign fund transfer
     */
    public function up(): void
    {
        Schema::table('cagnotes', function (Blueprint $table) {
            // Account holder information
            if (!Schema::hasColumn('cagnotes', 'account_holder_name')) {
                $table->string('account_holder_name')->nullable()->after('city');
            }

            // IBAN (International Bank Account Number)
            if (!Schema::hasColumn('cagnotes', 'iban')) {
                $table->string('iban')->nullable()->after('account_holder_name');
            }

            // BIC (Bank Identifier Code) / SWIFT
            if (!Schema::hasColumn('cagnotes', 'bic')) {
                $table->string('bic')->nullable()->after('iban');
            }

            // Bank name
            if (!Schema::hasColumn('cagnotes', 'bank_name')) {
                $table->string('bank_name')->nullable()->after('bic');
            }

            // Account type: Individual, Association, Company, etc.
            if (!Schema::hasColumn('cagnotes', 'account_type')) {
                $table->enum('account_type', ['individual', 'association', 'company', 'other'])->default('individual')->after('bank_name');
            }

            // Address for bank verification
            if (!Schema::hasColumn('cagnotes', 'account_address')) {
                $table->text('account_address')->nullable()->after('account_type');
            }

            // Phone for bank verification
            if (!Schema::hasColumn('cagnotes', 'account_phone')) {
                $table->string('account_phone')->nullable()->after('account_address');
            }

            // Email for bank notifications
            if (!Schema::hasColumn('cagnotes', 'account_email')) {
                $table->string('account_email')->nullable()->after('account_phone');
            }

            // Mark if banking info is verified
            if (!Schema::hasColumn('cagnotes', 'banking_verified')) {
                $table->boolean('banking_verified')->default(false)->after('account_email');
            }

            // Date when banking info was verified
            if (!Schema::hasColumn('cagnotes', 'banking_verified_at')) {
                $table->timestamp('banking_verified_at')->nullable()->after('banking_verified');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('cagnotes', function (Blueprint $table) {
            $columns = [
                'account_holder_name',
                'iban',
                'bic',
                'bank_name',
                'account_type',
                'account_address',
                'account_phone',
                'account_email',
                'banking_verified',
                'banking_verified_at'
            ];

            foreach ($columns as $column) {
                if (Schema::hasColumn('cagnotes', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};
