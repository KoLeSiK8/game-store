<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    use HasFactory;

    /**
     * Mass assignable fields.
     *
     * @var list<string>
     */
    protected $fillable = [
        'order_id',
        'user_id',
        'amount',
        'currency',
        'payment_method',
        'card_last4',
        'status',
        'transaction_id',
    ];

    /**
     * Attribute casting.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'amount' => 'decimal:2',
    ];

    /**
     * Order linked to this payment.
     */
    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    /**
     * User who paid for the order.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
