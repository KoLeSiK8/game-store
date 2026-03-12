<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserLibrary extends Model
{
    use HasFactory;

    /**
     * Явное имя таблицы.
     *
     * @var string
     */
    protected $table = 'user_library';

    /**
     * Mass assignable fields.
     *
     * @var list<string>
     */
    protected $fillable = [
        'user_id',
        'game_id',
        'order_item_id',
        'purchased_at',
    ];

    /**
     * Attribute casting.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'purchased_at' => 'datetime',
    ];

    /**
     * Library owner.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Purchased game.
     */
    public function game()
    {
        return $this->belongsTo(Game::class);
    }

    /**
     * Source order item.
     */
    public function orderItem()
    {
        return $this->belongsTo(OrderItem::class);
    }
}
