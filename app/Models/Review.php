<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Review extends Model
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
        'rating',
        'content',
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
        'rating' => 'integer',
        'moderated_at' => 'datetime',
    ];

    /**
     * Review author.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Reviewed game.
     */
    public function game()
    {
        return $this->belongsTo(Game::class);
    }

    /**
     * Admin who moderated the review.
     */
    public function moderator()
    {
        return $this->belongsTo(User::class, 'moderated_by');
    }
}
