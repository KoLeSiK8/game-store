<?php

namespace App\Services;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\User;
use App\Services\ActivityLogger;
use Illuminate\Support\Facades\DB;
use InvalidArgumentException;

class OrderService
{
    /**
     * Создать заказ на основе корзины.
     *
     * @param array<int, array{game: \App\Models\Game, quantity: int}> $items
     */
    public function createOrder(User $user, array $items, ?ActivityLogger $activityLogger = null): Order
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

                $orderItem = OrderItem::create([
                    'order_id' => $order->id,
                    'game_id' => $game->id,
                    'price' => $game->price,
                    'currency' => $game->currency,
                    'quantity' => $quantity,
                ]);

                // Добавляем игру в библиотеку пользователя.
                DB::table('user_library')->insert([
                    'user_id' => $user->id,
                    'game_id' => $game->id,
                    'order_item_id' => $orderItem->id,
                    'purchased_at' => now(),
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);

                // Логируем покупку.
                if ($activityLogger) {
                    $activityLogger->logPurchase($user, $game);
                }
            }

            $order->update([
                'total_amount' => $total,
                'currency' => $currency,
            ]);

            return $order;
        });
    }

    /**
     * Рассчитать сумму по корзине.
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
     * Завершить заказ.
     */
    public function completeOrder(Order $order): void
    {
        $order->update([
            'status' => 'completed',
            'paid_at' => now(),
        ]);
    }
}
