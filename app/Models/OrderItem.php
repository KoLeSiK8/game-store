<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderItem extends Model
{
    use HasFactory;

    /**
     * Mass assignable fields.
     *
     * @var list<string>
     */
    protected $fillable = [
        'order_id',
        'game_id',
        'price',
        'currency',
        'quantity',
    ];

    /**
     * Attribute casting.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'price' => 'decimal:2',
    ];

    /**
     * Parent order.
     */
    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    /**
     * Purchased game.
     */
    public function game()
    {
        return $this->belongsTo(Game::class);
    }

    /**
     * Library entries created from this item.
     */
    public function libraryEntries()
    {
        return $this->hasMany(UserLibrary::class);
    }
}
