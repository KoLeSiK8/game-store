<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Wishlist extends Model
{
    use HasFactory;

    /**
     * Mass assignable fields.
     *
     * @var list<string>
     */
    protected $fillable = [
        'user_id',
        'game_id',
    ];

    /**
     * Wishlist owner.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Wishlisted game.
     */
    public function game()
    {
        return $this->belongsTo(Game::class);
    }
}
