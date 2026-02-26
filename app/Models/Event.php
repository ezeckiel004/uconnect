<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Event extends Model
{
    protected $fillable = [
        'user_id',
        'title',
        'description',
        'location',
        'date',
        'time',
        'registration_link',
        'photos',
        'status',
        'validated_at',
        'validated_by',
        'rejection_reason',
    ];

    protected $casts = [
        'date' => 'date',
        'photos' => 'array',
        'validated_at' => 'datetime',
    ];

    /**
     * Get the user that owns the event
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the admin that validated this event
     */
    public function validator()
    {
        return $this->belongsTo(User::class, 'validated_by');
    }
}
