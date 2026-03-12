<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Game extends Model
{
    use HasFactory;

    /**
     * Mass assignable fields.
     *
     * @var list<string>
     */
    protected $fillable = [
        'seller_id',
        'title',
        'slug',
        'description',
        'price',
        'currency',
        'status',
        'moderated_by',
        'moderated_at',
    ];

    /**
     * Attribute casting.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'price' => 'decimal:2',
        'moderated_at' => 'datetime',
    ];

    /**
     * Seller (owner) of the game.
     */
    public function seller()
    {
        return $this->belongsTo(User::class, 'seller_id');
    }

    /**
     * Admin who moderated the game.
     */
    public function moderator()
    {
        return $this->belongsTo(User::class, 'moderated_by');
    }

    /**
     * Categories for this game.
     */
    public function categories()
    {
        return $this->belongsToMany(Category::class, 'game_category_map')->withTimestamps();
    }

    /**
     * Tags for this game.
     */
    public function tags()
    {
        return $this->belongsToMany(GameTag::class, 'game_tag_map', 'game_id', 'tag_id')
            ->withTimestamps();
    }

    /**
     * Files for this game.
     */
    public function files()
    {
        return $this->hasMany(GameFile::class);
    }

    /**
     * Order items that include this game.
     */
    public function orderItems()
    {
        return $this->hasMany(OrderItem::class);
    }

    /**
     * Reviews for this game.
     */
    public function reviews()
    {
        return $this->hasMany(Review::class);
    }

    /**
     * Library entries for this game.
     */
    public function libraryEntries()
    {
        return $this->hasMany(UserLibrary::class);
    }

    /**
     * Wishlist entries for this game.
     */
    public function wishlists()
    {
        return $this->hasMany(Wishlist::class);
    }

    /**
     * Activity records for this game.
     */
    public function activities()
    {
        return $this->hasMany(UserGameActivity::class);
    }
}
