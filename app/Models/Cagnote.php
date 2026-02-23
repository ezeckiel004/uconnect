<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;

class Cagnote extends Model
{
    protected $fillable = [
        'user_id',
        'title',
        'description',
        'location',
        'category',
        'objective_amount',
        'collected_amount',
        'start_date',
        'deadline',
        'status',
        'image_url',
        'photos',
        'publication_status',
        'validated_at',
        'validated_by',
        'rejection_reason',
    ];

    protected $casts = [
        'objective_amount' => 'decimal:2',
        'collected_amount' => 'decimal:2',
        'start_date' => 'date',
        'deadline' => 'date',
        'validated_at' => 'datetime',
        'photos' => 'array',
    ];

    /**
     * Get the user that owns the cagnote
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the admin that validated this cagnote
     */
    public function validator()
    {
        return $this->belongsTo(User::class, 'validated_by');
    }

    /**
     * Get the donations for this cagnote
     */
    public function donations()
    {
        return $this->hasMany(Donation::class);
    }

    /**
     * Get the likes for this cagnote
     */
    public function likes()
    {
        return $this->hasMany(CagnoteLike::class);
    }

    /**
     * Check if a specific user has liked this cagnote
     */
    public function isLikedBy(User $user)
    {
        Log::info("isLikedBy called - User ID: {$user->id}, Cagnote ID: {$this->id}");
        
        $exists = $this->likes()->where('user_id', $user->id)->exists();
        
        Log::info("isLikedBy result - exists: " . ($exists ? 'TRUE' : 'FALSE'));
        Log::info("Query: SELECT EXISTS(SELECT 1 FROM cagnote_likes WHERE cagnote_id={$this->id} AND user_id={$user->id})");
        
        return $exists;
    }

    /**
     * Get count of likes
     */
    public function getLikeCount()
    {
        return $this->likes()->count();
    }
}
