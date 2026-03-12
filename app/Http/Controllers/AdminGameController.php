<?php

namespace App\Http\Controllers;

use App\Models\Game;
use Illuminate\Http\Request;

class AdminGameController extends Controller
{
    /**
     * Список игр на модерации.
     */
    public function index()
    {
        $games = Game::where('status', 'pending')
            ->orderByDesc('created_at')
            ->paginate(15);

        return view('admin.games.index', [
            'games' => $games,
        ]);
    }

    /**
     * Одобрить игру.
     */
    public function approve(Request $request, Game $game)
    {
        return $this->approveGame($request, $game);
    }

    /**
     * Отклонить игру.
     */
    public function reject(Request $request, Game $game)
    {
        return $this->rejectGame($request, $game);
    }

    /**
     * Одобрить игру (новое имя метода).
     */
    public function approveGame(Request $request, Game $game)
    {
        $admin = $request->user();

        if (!$admin) {
            abort(401, 'Unauthorized');
        }

        $game->update([
            'status' => 'approved',
            'moderated_by' => $admin->id,
            'moderated_at' => now(),
        ]);

        return back()->with('status', 'Игра одобрена.');
    }

    /**
     * Отклонить игру (новое имя метода).
     */
    public function rejectGame(Request $request, Game $game)
    {
        $admin = $request->user();

        if (!$admin) {
            abort(401, 'Unauthorized');
        }

        $game->update([
            'status' => 'rejected',
            'moderated_by' => $admin->id,
            'moderated_at' => now(),
        ]);

        return back()->with('status', 'Игра отклонена.');
    }
}