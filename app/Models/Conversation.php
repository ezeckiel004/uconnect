<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Conversation extends Model
{
    use HasFactory;

    protected $fillable = [
        'association1_id',
        'association2_id',
        'last_message',
        'last_message_at',
        'last_sender_id',
    ];

    protected $casts = [
        'last_message_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Get the first association
     */
    public function association1(): BelongsTo
    {
        return $this->belongsTo(User::class, 'association1_id');
    }

    /**
     * Get the second association
     */
    public function association2(): BelongsTo
    {
        return $this->belongsTo(User::class, 'association2_id');
    }

    /**
     * Get the last sender
     */
    public function lastSender(): BelongsTo
    {
        return $this->belongsTo(User::class, 'last_sender_id');
    }

    /**
     * Get all messages in this conversation
     */
    public function messages(): HasMany
    {
        return $this->hasMany(Message::class)->orderBy('created_at', 'asc');
    }

    /**
     * Get the other association in the conversation
     */
    public function getOtherAssociation(int $userId): ?User
    {
        return $this->association1_id === $userId
            ? $this->association2
            : $this->association1;
    }
}
