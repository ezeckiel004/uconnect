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
        'city',
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
        // Banking information
        'account_holder_name',
        'iban',
        'bic',
        'bank_name',
        'account_type',
        'account_address',
        'account_phone',
        'account_email',
        'banking_verified',
        'banking_verified_at',
    ];

    protected $hidden = [
        //
    ];

    protected function casts(): array
    {
        return [
            'objective_amount' => 'decimal:2',
            'collected_amount' => 'decimal:2',
            'start_date' => 'date',
            'deadline' => 'date',
            'validated_at' => 'datetime',
            'banking_verified_at' => 'datetime',
            'photos' => 'array',
            'banking_verified' => 'boolean',
        ];
    }

    /**
     * Get the image URL with absolute path
     */
    public function getImageUrlAttribute()
    {
        $url = $this->attributes['image_url'] ?? '';
        if ($url && !str_starts_with($url, 'http')) {
            $url = config('app.url') . '/' . ltrim($url, '/');
        }
        return $url;
    }

    /**
     * Get the user relationship with logo URL
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
     * Get the withdrawal requests for this cagnote
     */
    public function withdrawalRequests()
    {
        return $this->hasMany(WithdrawalRequest::class);
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
