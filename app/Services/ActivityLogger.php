<?php

namespace App\Services;

use App\Models\Game;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class ActivityLogger
{
    /**
     * Логирование входа пользователя.
     */
    public function logLogin(User $user): void
    {
        $this->log($user, 'login');
    }

    /**
     * Логирование покупки.
     */
    public function logPurchase(User $user, Game $game): void
    {
        $this->log($user, 'purchase', $game);
    }

    /**
     * Логирование скачивания.
     */
    public function logDownload(User $user, Game $game): void
    {
        $this->log($user, 'download', $game);
    }

    /**
     * Логирование отзыва.
     */
    public function logReview(User $user, Game $game): void
    {
        $this->log($user, 'review', $game);
    }

    /**
     * Логирование поиска.
     *
     * @param array<string, mixed> $filters
     */
    public function logSearch(User $user, array $filters = []): void
    {
        $this->log($user, 'search', null, ['filters' => $filters]);
    }

    /**
     * Логирование добавления в избранное.
     */
    public function logWishlistAdd(User $user, Game $game): void
    {
        $this->log($user, 'wishlist_add', $game);
    }

    /**
     * Базовый логгер в user_game_activity.
     */
    private function log(User $user, string $eventType, ?Game $game = null, array $metadata = []): void
    {
        $payload = array_merge(['source' => 'app'], $metadata);

        DB::table('user_game_activity')->insert([
            'user_id' => $user->id,
            'game_id' => $game?->id,
            'event_type' => $eventType,
            'metadata' => json_encode($payload),
            'occurred_at' => now(),
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }
}
