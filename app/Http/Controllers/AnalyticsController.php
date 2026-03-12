<?php

namespace App\Http\Controllers;

use App\Services\AnalyticsService;
use App\Services\RoleService;
use Illuminate\Http\Request;

class AnalyticsController extends Controller
{
    /**
     * Страница аналитики.
     */
    public function index(Request $request, AnalyticsService $analyticsService, RoleService $roleService)
    {
        $user = $request->user();

        if (!$user) {
            abort(401, 'Unauthorized');
        }

        $isAdmin = $roleService->isAdmin($user);
        $isSeller = $roleService->isSeller($user);

        if (!$isAdmin && !$isSeller) {
            abort(403, 'Forbidden');
        }

        $from = $request->input('from');
        $to = $request->input('to');

        if ($isAdmin) {
            $totalSales = $analyticsService->totalSales($from, $to);
            $topGames = $analyticsService->topGames(10, $from, $to);
            $sellerRevenue = null;
        } else {
            $totalSales = $analyticsService->sellerRevenue($user->id, $from, $to);
            $topGames = $analyticsService->topGames(10, $from, $to, $user->id);
            $sellerRevenue = $totalSales;
        }

        return view('analytics.index', [
            'isAdmin' => $isAdmin,
            'from' => $from,
            'to' => $to,
            'totalSales' => $totalSales,
            'sellerRevenue' => $sellerRevenue,
            'topGames' => $topGames,
        ]);
    }
}