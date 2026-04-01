<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Passport\HasApiTokens;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable, HasApiTokens;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'type',
        'code',
        'phone_number',
        'description',
        'logo_path',
        'category',
        'country',
        'stripe_connect_account_id',
        'stripe_connect_onboarded',
        'stripe_charges_enabled',
        'stripe_payouts_enabled',
        'first_login',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'stripe_connect_onboarded' => 'boolean',
            'stripe_charges_enabled' => 'boolean',
            'stripe_payouts_enabled' => 'boolean',
            'first_login' => 'boolean',
        ];
    }

    /**
     * Get the likes from this user
     */
    public function cagnoteLikes()
    {
        return $this->hasMany(CagnoteLike::class);
    }

    /**
     * Get the liked cagnotes
     */
    public function likedCagnotes()
    {
        return $this->belongsToMany(Cagnote::class, 'cagnote_likes');
    }

    /**
     * Get the donations made by this user
     */
    public function donations()
    {
        return $this->hasMany(Donation::class);
    }

    /**
     * Get the forum posts created by this user
     */
    public function forumPosts()
    {
        return $this->hasMany(ForumPost::class);
    }

    /**
     * Get the forum comments created by this user
     */
    public function forumComments()
    {
        return $this->hasMany(ForumComment::class);
    }

    /**
     * Get the forum replies created by this user
     */
    public function forumReplies()
    {
        return $this->hasMany(ForumReply::class);
    }

    /**
     * Get the forum posts liked by this user
     */
    public function likedForumPosts()
    {
        return $this->belongsToMany(ForumPost::class, 'forum_post_likes', 'user_id', 'post_id')
            ->withTimestamps();
    }
}
