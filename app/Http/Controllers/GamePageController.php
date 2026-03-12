<?php

namespace App\Http\Controllers;

use App\Models\Game;

class GamePageController extends Controller
{
    /**
     * Страница игры.
     */
    public function show(Game $game)
    {
        $game->load(['categories', 'tags', 'reviews.user', 'files']);

        return view('games.show', [
            'game' => $game,
        ]);
    }
}