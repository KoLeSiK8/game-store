<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class OrderSeeder extends Seeder
{
    /**
     * Заполнение таблиц orders, order_items и user_library.
     */
    public function run(): void
    {
        $now = now();

        $userRoleId = DB::table('roles')->where('name', 'user')->value('id');
        $userIds = DB::table('role_user')->where('role_id', $userRoleId)->pluck('user_id')->all();
        $games = DB::table('games')->select('id', 'price', 'currency')->get();

        for ($i = 1; $i <= 10; $i++) {
            $userId = $userIds[array_rand($userIds)];

            $orderId = DB::table('orders')->insertGetId([
                'user_id' => $userId,
                'status' => 'completed',
                'total_amount' => 0,
                'currency' => 'USD',
                'paid_at' => $now,
                'created_at' => $now,
                'updated_at' => $now,
            ]);

            $itemsCount = rand(1, 3);
            $total = 0;

            for ($j = 0; $j < $itemsCount; $j++) {
                $game = $games[rand(0, $games->count() - 1)];
                $quantity = rand(1, 2);
                $lineTotal = $game->price * $quantity;
                $total += $lineTotal;

                $orderItemId = DB::table('order_items')->insertGetId([
                    'order_id' => $orderId,
                    'game_id' => $game->id,
                    'price' => $game->price,
                    'currency' => $game->currency,
                    'quantity' => $quantity,
                    'created_at' => $now,
                    'updated_at' => $now,
                ]);

                // Добавляем игру в библиотеку пользователя.
                DB::table('user_library')->insert([
                    'user_id' => $userId,
                    'game_id' => $game->id,
                    'order_item_id' => $orderItemId,
                    'purchased_at' => $now,
                    'created_at' => $now,
                    'updated_at' => $now,
                ]);
            }

            // Обновляем сумму заказа.
            DB::table('orders')->where('id', $orderId)->update([
                'total_amount' => $total,
                'updated_at' => $now,
            ]);
        }
    }
}