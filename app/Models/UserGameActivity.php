<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserGameActivity extends Model
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
        'event_type',
        'metadata',
        'occurred_at',
    ];

    /**
     * Attribute casting.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'metadata' => 'array',
        'occurred_at' => 'datetime',
    ];

    /**
     * Activity owner.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Related game (optional).
     */
    public function game()
    {
        return $this->belongsTo(Game::class);
    }
}
