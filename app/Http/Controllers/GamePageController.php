<?php

namespace App\Http\Controllers;

use App\Models\Game;
use App\Services\LibraryService;
use Illuminate\Http\Request;
use Illuminate\View\View;

class GamePageController extends Controller
{
    /**
     * Страница игры.
     */
    public function show(Request $request, Game $game, LibraryService $libraryService): View
    {
        $game->load(['categories', 'tags', 'reviews.user', 'files.uploader']);

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