<?php

namespace App\Http\Controllers;

use App\Models\Game;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SellerDashboardController extends Controller
{
    /**
     * Личный кабинет продавца.
     */
    public function index(Request $request): View
    {
        $seller = $request->user();

        if (!$seller) {
            abort(401, 'Unauthorized');
        }

        $games = Game::with(['categories', 'tags', 'files'])
            ->where('seller_id', $seller->id)
            ->orderByDesc('created_at')
            ->get();

        return view('seller.dashboard', [
            'games' => $games,
        ]);
    }
}