<?php

namespace App\Http\Controllers;

use App\Models\Game;
use App\Models\Wishlist;
use App\Services\ActivityLogger;
use Illuminate\Http\Request;

class WishlistController extends Controller
{
    /**
     * Список избранного пользователя.
     */
    public function list(Request $request)
    {
        $user = $request->user();

        if (!$user) {
            abort(401, 'Unauthorized');
        }

        $items = Wishlist::with('game')
            ->where('user_id', $user->id)
            ->orderByDesc('created_at')
            ->get();

        return view('wishlist.index', [
            'items' => $items,
        ]);
    }

    /**
     * Добавить игру в избранное.
     */
    public function add(Request $request, Game $game, ActivityLogger $activityLogger)
    {
        $user = $request->user();

        if (!$user) {
            abort(401, 'Unauthorized');
        }

        Wishlist::firstOrCreate([
            'user_id' => $user->id,
            'game_id' => $game->id,
        ]);

        $activityLogger->logWishlistAdd($user, $game);

        return back()->with('status', 'Игра добавлена в избранное.');
    }

    /**
     * Переключить игру в избранном (добавить/удалить).
     */
    public function toggle(Request $request, Game $game, ActivityLogger $activityLogger)
    {
        $user = $request->user();

        if (!$user) {
            abort(401, 'Unauthorized');
        }

        $exists = Wishlist::where('user_id', $user->id)
            ->where('game_id', $game->id)
            ->exists();

        if ($exists) {
            Wishlist::where('user_id', $user->id)
                ->where('game_id', $game->id)
                ->delete();

            return back()->with('status', 'Игра удалена из избранного.');
        }

        Wishlist::create([
            'user_id' => $user->id,
            'game_id' => $game->id,
        ]);

        $activityLogger->logWishlistAdd($user, $game);

        return back()->with('status', 'Игра добавлена в избранное.');
    }

    /**
     * Удалить игру из избранного.
     */
    public function remove(Request $request, Game $game)
    {
        $user = $request->user();

        if (!$user) {
            abort(401, 'Unauthorized');
        }

        Wishlist::where('user_id', $user->id)
            ->where('game_id', $game->id)
            ->delete();

        return back()->with('status', 'Игра удалена из избранного.');
    }
}
