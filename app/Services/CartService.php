<?php

namespace App\Services;

use App\Models\Game;
use App\Models\User;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\DB;

class CartService
{
    /**
     * Добавить игру в корзину.
     */
    public function addGame(int $gameId, int $quantity = 1, ?User $user = null): void
    {
        $quantity = max(1, $quantity);
        $userId = $user?->id;

        if ($userId) {
            $existing = DB::table('cart_items')
                ->where('user_id', $userId)
                ->where('game_id', $gameId)
                ->first();

            if ($existing) {
                DB::table('cart_items')
                    ->where('user_id', $userId)
                    ->where('game_id', $gameId)
                    ->update([
                        'quantity' => $existing->quantity + $quantity,
                        'updated_at' => now(),
                    ]);
            } else {
                DB::table('cart_items')->insert([
                    'user_id' => $userId,
                    'game_id' => $gameId,
                    'quantity' => $quantity,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }

            return;
        }

        $cart = $this->getCart();
        $cart[$gameId] = ($cart[$gameId] ?? 0) + $quantity;
        $this->storeCart($cart);
    }

    /**
     * Удалить игру из корзины.
     */
    public function removeGame(int $gameId, ?User $user = null): void
    {
        $userId = $user?->id;

        if ($userId) {
            DB::table('cart_items')
                ->where('user_id', $userId)
                ->where('game_id', $gameId)
                ->delete();

            return;
        }

        $cart = $this->getCart();
        unset($cart[$gameId]);
        $this->storeCart($cart);
    }

    /**
     * Получить корзину (game_id => quantity).
     */
    public function getCart(?User $user = null): array
    {
        $userId = $user?->id;

        if ($userId) {
            return DB::table('cart_items')
                ->where('user_id', $userId)
                ->pluck('quantity', 'game_id')
                ->all();
        }

        $raw = Cookie::get('cart');
        if (!$raw) {
            return [];
        }

        $decoded = json_decode($raw, true);

        return is_array($decoded) ? $decoded : [];
    }

    /**
     * Полная очистка корзины.
     */
    public function clearCart(?User $user = null): void
    {
        $userId = $user?->id;

        if ($userId) {
            DB::table('cart_items')->where('user_id', $userId)->delete();
            return;
        }

        Cookie::queue(Cookie::forget('cart'));
    }

    /**
     * Получить список игр из корзины с моделями.
     */
    public function getCartItems(?User $user = null): array
    {
        $cart = $this->getCart($user);
        $gameIds = array_keys($cart);

        if (empty($gameIds)) {
            return [];
        }

        $games = Game::whereIn('id', $gameIds)->get()->keyBy('id');

        $items = [];
        foreach ($cart as $gameId => $qty) {
            if (!$games->has($gameId)) {
                continue;
            }

            $items[] = [
                'game' => $games[$gameId],
                'quantity' => $qty,
            ];
        }

        return $items;
    }

    /**
     * Сохранить корзину в cookie.
     *
     * @param array<int, int> $cart
     */
    private function storeCart(array $cart): void
    {
        // 30 дней хранения корзины.
        Cookie::queue('cart', json_encode($cart), 60 * 24 * 30);
    }
}
