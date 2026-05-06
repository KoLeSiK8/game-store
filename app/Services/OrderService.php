<?php

namespace App\Services;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use InvalidArgumentException;

class OrderService
{
    /**
     * Create a pending order based on prepared items.
     *
     * @param array<int, array{game: \App\Models\Game, quantity: int}> $items
     */
    public function createOrder(User $user, array $items): Order
    {
        if (empty($items)) {
            throw new InvalidArgumentException('Корзина пуста.');
        }

        return DB::transaction(function () use ($user, $items) {
            $currency = $items[0]['game']->currency ?? 'USD';

            $order = Order::create([
                'user_id' => $user->id,
                'status' => 'pending',
                'total_amount' => 0,
                'currency' => $currency,
                'paid_at' => null,
            ]);

            $total = 0;

            foreach ($items as $item) {
                $game = $item['game'];
                $quantity = max(1, (int) $item['quantity']);

                $lineTotal = $game->price * $quantity;
                $total += $lineTotal;

                OrderItem::create([
                    'order_id' => $order->id,
                    'game_id' => $game->id,
                    'price' => $game->price,
                    'currency' => $game->currency,
                    'quantity' => $quantity,
                ]);
            }

            $order->update([
                'total_amount' => $total,
                'currency' => $currency,
            ]);

            return $order->fresh(['items.game']);
        });
    }

    /**
     * Calculate total amount for prepared items.
     *
     * @param array<int, array{game: \App\Models\Game, quantity: int}> $items
     */
    public function calculateTotal(array $items): float
    {
        $total = 0.0;

        foreach ($items as $item) {
            $total += $item['game']->price * max(1, (int) $item['quantity']);
        }

        return $total;
    }

    /**
     * Mark order as completed.
     */
    public function completeOrder(Order $order): void
    {
        $order->update([
            'status' => 'completed',
            'paid_at' => now(),
        ]);
    }
}
