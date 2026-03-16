<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class ForumPost extends Model
{
    protected $fillable = [
        'user_id',
        'title',
        'description',
        'category',
        'likes',
        'views',
        'file_path',
        'file_name',
        'file_size',
    ];

    protected $casts = [
        'likes' => 'integer',
        'views' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function comments(): HasMany
    {
        return $this->hasMany(ForumComment::class);
    }

    public function likedByUsers(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'forum_post_likes', 'post_id', 'user_id')
            ->withTimestamps();
    }

    public function viewedByUsers(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'forum_post_views', 'post_id', 'user_id')
            ->withTimestamps();
    }

    public function isLikedBy(User $user): bool
    {
        return $this->likedByUsers()->where('user_id', $user->id)->exists();
    }

    public function isViewedBy(User $user): bool
    {
        return $this->viewedByUsers()->where('user_id', $user->id)->exists();
    }

    /**
     * Check if the currently authenticated user has liked this post
     */
    public function isLikedByCurrentUser(): bool
    {
        $user = Auth::user();
        Log::info('📝 isLikedByCurrentUser() - PostID: ' . $this->id . ', User: ' . ($user ? 'UserID=' . $user->id : 'NULL'));
        
        if (!$user) {
            Log::info('📝 isLikedByCurrentUser() - No user, returning FALSE');
            return false;
        }
        
        $result = $this->isLikedBy($user);
        Log::info('📝 isLikedByCurrentUser() - PostID: ' . $this->id . ', UserID: ' . $user->id . ', Result: ' . ($result ? 'TRUE' : 'FALSE'));
        return $result;
    }

    /**
     * Check if the currently authenticated user has viewed this post
     */
    public function isViewedByCurrentUser(): bool
    {
        $user = Auth::user();
        
        if (!$user) {
            return false;
        }
        
        return $this->isViewedBy($user);
    }
}
