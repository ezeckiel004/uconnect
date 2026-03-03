<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class WithdrawalRequest extends Model
{
    protected $fillable = [
        'cagnote_id',
        'user_id',
        'original_amount',
        'withdrawal_amount',
        'platform_fee',
        'status',
        'rejection_reason',
        'processed_at',
        'processed_by',
        'transaction_reference',
        'account_holder_name',
        'iban',
        'bic',
        'bank_name',
        'account_type',
        'account_address',
        'account_phone',
        'account_email',
    ];

    protected $casts = [
        'original_amount' => 'decimal:2',
        'withdrawal_amount' => 'decimal:2',
        'platform_fee' => 'decimal:2',
        'processed_at' => 'datetime',
    ];

    /**
     * Get the cagnote that this withdrawal request is for
     */
    public function cagnote(): BelongsTo
    {
        return $this->belongsTo(Cagnote::class);
    }

    /**
     * Get the user (association) requesting withdrawal
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the admin who processed this request
     */
    public function processedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'processed_by');
    }

    /**
     * Check if withdrawal is pending
     */
    public function isPending(): bool
    {
        return $this->status === 'pending';
    }

    /**
     * Check if withdrawal is processed
     */
    public function isProcessed(): bool
    {
        return $this->status === 'processed';
    }
}

