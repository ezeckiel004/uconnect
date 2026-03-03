<?php

namespace App\Models;

use App\Mail\WithdrawalRequestCreatedAdmin;
use App\Mail\WithdrawalRequestCreatedAssociation;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;

class Donation extends Model
{
    protected $fillable = [
        'user_id',
        'cagnote_id',
        'amount',
        'currency',
        'stripe_payment_intent_id',
        'stripe_charge_id',
        'status',
        'payment_method',
        'donor_email',
        'donor_name',
        'donor_message',
        'is_anonymous',
        'receipt_url',
        'metadata',
        'paid_at',
        'refunded_at',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'is_anonymous' => 'boolean',
        'metadata' => 'array',
        'paid_at' => 'datetime',
        'refunded_at' => 'datetime',
    ];

    /**
     * Get the user that made this donation
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the cagnote this donation was made to
     */
    public function cagnote()
    {
        return $this->belongsTo(Cagnote::class);
    }

    /**
     * Check if donation is complete
     */
    public function isComplete(): bool
    {
        return $this->status === 'success' && $this->paid_at !== null;
    }

    /**
     * Check if donation is pending
     */
    public function isPending(): bool
    {
        return $this->status === 'pending';
    }

    /**
     * Check if donation failed
     */
    public function isFailed(): bool
    {
        return $this->status === 'failed';
    }

    /**
     * Mark donation as paid
     */
    public function markAsPaid(?string $chargeId = null, ?string $receiptUrl = null): void
    {
        // Only increment if donation is not already paid
        $shouldIncrement = $this->status !== 'success';

        $this->update([
            'status' => 'success',
            'stripe_charge_id' => $chargeId,
            'receipt_url' => $receiptUrl,
            'paid_at' => now(),
        ]);

        // Update the cagnote collected_amount only if this is the first time marking as paid
        if ($shouldIncrement) {
            $cagnote = $this->cagnote;
            if ($cagnote) {
                $cagnote->increment('collected_amount', $this->amount);
                
                // Check if cagnote has reached or exceeded its objective
                if ($cagnote->collected_amount >= $cagnote->objective_amount) {
                    // Check if withdrawal request already exists
                    $existingWithdrawal = $cagnote->withdrawalRequests()
                        ->where('status', '!=', 'rejected')
                        ->where('status', '!=', 'failed')
                        ->exists();
                    
                    if (!$existingWithdrawal) {
                        // Create withdrawal request automatically
                        $this->createWithdrawalRequest($cagnote);
                    }
                }
            }
        }
    }

    /**
     * Create withdrawal request when cagnote reaches objective
     */
    private function createWithdrawalRequest($cagnote): void
    {
        try {
            $originalAmount = $cagnote->collected_amount;
            $platformFeePercent = 10;
            $platformFee = $originalAmount * ($platformFeePercent / 100);
            $withdrawalAmount = $originalAmount - $platformFee;

            // Create withdrawal request
            $withdrawalRequest = WithdrawalRequest::create([
                'cagnote_id' => $cagnote->id,
                'user_id' => $cagnote->user_id,
                'original_amount' => $originalAmount,
                'withdrawal_amount' => $withdrawalAmount,
                'platform_fee' => $platformFee,
                'status' => 'pending',
                // Save banking information from cagnote
                'account_holder_name' => $cagnote->account_holder_name,
                'iban' => $cagnote->iban,
                'bic' => $cagnote->bic,
                'bank_name' => $cagnote->bank_name,
                'account_type' => $cagnote->account_type,
                'account_address' => $cagnote->account_address,
                'account_phone' => $cagnote->account_phone,
                'account_email' => $cagnote->account_email,
            ]);

            Log::info('Withdrawal request created', [
                'withdrawal_request_id' => $withdrawalRequest->id,
                'cagnote_id' => $cagnote->id,
                'user_id' => $cagnote->user_id,
                'original_amount' => $originalAmount,
                'withdrawal_amount' => $withdrawalAmount,
            ]);

            // Get admin email (assuming there's an admin user or admin email config)
            $adminEmail = env('ADMIN_EMAIL', 'admin@uconnect.local');
            
            // Send email to admin
            Mail::to($adminEmail)->send(new WithdrawalRequestCreatedAdmin($withdrawalRequest));

            // Send email to association
            if ($cagnote->user && $cagnote->user->email) {
                Mail::to($cagnote->user->email)->send(new WithdrawalRequestCreatedAssociation($withdrawalRequest));
            }

            Log::info('Withdrawal request emails sent', [
                'withdrawal_request_id' => $withdrawalRequest->id,
                'admin_email' => $adminEmail,
                'association_email' => $cagnote->user?->email,
            ]);

        } catch (\Exception $e) {
            Log::error('Error creating withdrawal request:', [
                'error' => $e->getMessage(),
                'cagnote_id' => $cagnote->id,
                'trace' => $e->getTraceAsString(),
            ]);
        }
    }

    /**
     * Mark donation as failed
     */
    public function markAsFailed(): void
    {
        $this->update([
            'status' => 'failed',
            'paid_at' => null,
        ]);
    }
}
