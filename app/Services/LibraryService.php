<?php

namespace App\Services;

use App\Models\Game;
use App\Models\User;
use App\Models\UserLibrary;

class LibraryService
{
    /**
     * Добавить игру в библиотеку пользователя.
     */
    public function addGameToLibrary(User $user, Game $game, ?int $orderItemId = null): UserLibrary
    {
        $entry = UserLibrary::where('user_id', $user->id)
            ->where('game_id', $game->id)
            ->first();

        if ($entry) {
            return $entry;
        }

        return UserLibrary::create([
            'user_id' => $user->id,
            'game_id' => $game->id,
            'order_item_id' => $orderItemId,
            'purchased_at' => now(),
        ]);
    }

    /**
     * Проверить, есть ли игра в библиотеке.
     */
    public function hasGame(User $user, Game $game): bool
    {
        return UserLibrary::where('user_id', $user->id)
            ->where('game_id', $game->id)
            ->exists();
    }

    /**
     * Получить библиотеку пользователя.
     */
    public function getUserLibrary(User $user)
    {
        return UserLibrary::with(['game', 'game.files'])
            ->where('user_id', $user->id)
            ->orderByDesc('purchased_at')
            ->get();
    }
}
