<?php

namespace App\Services;

use App\Models\Game;
use App\Models\Review;
use App\Models\User;
use App\Models\UserLibrary;

class ReviewService
{
    /**
     * Проверить, может ли пользователь оставить отзыв.
     */
    public function canReview(User $user, Game $game): bool
    {
        return UserLibrary::where('user_id', $user->id)
            ->where('game_id', $game->id)
            ->exists();
    }

    /**
     * Создать отзыв.
     */
    public function createReview(User $user, Game $game, int $rating, ?string $content = null): Review
    {
        return Review::create([
            'user_id' => $user->id,
            'game_id' => $game->id,
            'rating' => $rating,
            'content' => $content,
            'status' => 'pending',
        ]);
    }
}