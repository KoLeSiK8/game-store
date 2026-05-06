<?php

namespace App\Http\Controllers;

use App\Models\Game;
use App\Services\LibraryService;
use Illuminate\Http\Request;

class GamePageController extends Controller
{
    /**
     * Show the game page.
     */
    public function show(Request $request, Game $game, LibraryService $libraryService)
    {
        $game->load(['categories', 'tags', 'reviews.user', 'files']);

        $user = $request->user();
        $isOwned = false;

        if ($user) {
            $isOwned = $libraryService->hasGame($user, $game);
        }

        return view('games.show', [
            'game' => $game,
            'isOwned' => $isOwned,
        ]);
    }
}
