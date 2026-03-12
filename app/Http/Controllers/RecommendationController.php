<?php

namespace App\Http\Controllers;

use App\Services\RecommendationService;
use Illuminate\Http\Request;

class RecommendationController extends Controller
{
    /**
     * Страница рекомендаций.
     */
    public function index(Request $request, RecommendationService $recommendationService)
    {
        $user = $request->user();

        if (!$user) {
            abort(401, 'Unauthorized');
        }

        $games = $recommendationService->getRecommendations($user, 12);

        return view('recommendations.index', [
            'games' => $games,
        ]);
    }
}