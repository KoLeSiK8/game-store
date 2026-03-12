<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class UserCatalogStateController extends Controller
{
    /**
     * Страница каталога с пользовательским состоянием.
     */
    public function index(Request $request)
    {
        $user = $request->user();

        if (!$user) {
            abort(401, 'Unauthorized');
        }

        $cartGameIds = DB::table('cart_items')
            ->where('user_id', $user->id)
            ->pluck('game_id')
            ->all();

        $wishlistGameIds = DB::table('wishlists')
            ->where('user_id', $user->id)
            ->pluck('game_id')
            ->all();

        return response()->json([
            'cart' => $cartGameIds,
            'wishlist' => $wishlistGameIds,
        ]);
    }
}