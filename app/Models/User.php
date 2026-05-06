<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
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
            'is_banned' => 'boolean',
        ];
    }

    /**
     * Roles assigned to the user.
     */
    public function roles()
    {
        return $this->belongsToMany(Role::class, 'role_user')->withTimestamps();
    }

    /**
     * Games created by this seller.
     */
    public function games()
    {
        return $this->hasMany(Game::class, 'seller_id');
    }

    /**
     * Orders placed by the user.
     */
    public function orders()
    {
        return $this->hasMany(Order::class);
    }

    /**
     * Payments made by the user.
     */
    public function payments()
    {
        return $this->hasMany(Payment::class);
    }

    /**
     * Library entries for purchased games.
     */
    public function library()
    {
        return $this->hasMany(UserLibrary::class);
    }

    /**
     * Library entries (alias used in requirements).
     */
    public function libraryGames()
    {
        return $this->hasMany(UserLibrary::class);
    }

    /**
     * Reviews created by the user.
     */
    public function reviews()
    {
        return $this->hasMany(Review::class);
    }

    /**
     * Seller status requests from the user.
     */
    public function sellerRequests()
    {
        return $this->hasMany(SellerRequest::class);
    }

    /**
     * Wishlist entries for the user.
     */
    public function wishlists()
    {
        return $this->hasMany(Wishlist::class);
    }

    /**
     * Activity events for recommendations.
     */
    public function activities()
    {
        return $this->hasMany(UserGameActivity::class);
    }

    /**
     * Games moderated by this admin.
     */
    public function moderatedGames()
    {
        return $this->hasMany(Game::class, 'moderated_by');
    }

    /**
     * Reviews moderated by this admin.
     */
    public function moderatedReviews()
    {
        return $this->hasMany(Review::class, 'moderated_by');
    }

    /**
     * Seller requests reviewed by this admin.
     */
    public function reviewedSellerRequests()
    {
        return $this->hasMany(SellerRequest::class, 'reviewed_by');
    }
}
