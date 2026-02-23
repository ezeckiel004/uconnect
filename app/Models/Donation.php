<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

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
            }
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
