<?php

namespace App\Services;

use App\Models\Game;
use App\Models\User;
use App\Models\UserLibrary;
use App\Models\Wishlist;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class RecommendationService
{
    /**
     * Сформировать рекомендации для пользователя.
     */
    public function getRecommendations(User $user, int $limit = 12): Collection
    {
        $recommendations = collect();

        $ownedGameIds = UserLibrary::where('user_id', $user->id)
            ->pluck('game_id')
            ->unique()
            ->values()
            ->all();

        $wishlistGameIds = Wishlist::where('user_id', $user->id)
            ->pluck('game_id')
            ->unique()
            ->values()
            ->all();

        // 1) Игры из тех же категорий.
        $categoryIds = DB::table('game_category_map')
            ->whereIn('game_id', array_unique(array_merge($ownedGameIds, $wishlistGameIds)))
            ->pluck('category_id')
            ->unique()
            ->values()
            ->all();

        if (!empty($categoryIds)) {
            $sameCategory = Game::where('status', 'approved')
                ->whereNotIn('id', $ownedGameIds)
                ->whereHas('categories', function ($q) use ($categoryIds) {
                    $q->whereIn('categories.id', $categoryIds);
                })
                ->limit($limit)
                ->get();

            $recommendations = $recommendations->merge($sameCategory);
        }

        // 2) Популярные игры (по количеству продаж).
        if ($recommendations->count() < $limit) {
            $popular = Game::where('status', 'approved')
                ->whereNotIn('id', $ownedGameIds)
                ->withCount('orderItems')
                ->orderByDesc('order_items_count')
                ->limit($limit)
                ->get();

            $recommendations = $recommendations->merge($popular);
        }

        // 3) Игры из wishlist.
        if (!empty($wishlistGameIds)) {
            $wishlistGames = Game::where('status', 'approved')
                ->whereIn('id', $wishlistGameIds)
                ->get();

            $recommendations = $recommendations->merge($wishlistGames);
        }

        return $recommendations
            ->unique('id')
            ->take($limit)
            ->values();
    }
}