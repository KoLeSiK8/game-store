<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Collection;

class AnalyticsService
{
    /**
     * Общая сумма продаж (по завершенным заказам).
     */
    public function totalSales(?string $from = null, ?string $to = null): float
    {
        $query = DB::table('orders')->where('status', 'completed');

        $this->applyDateFilter($query, $from, $to, 'orders.paid_at');

        return (float) $query->sum('total_amount');
    }

    /**
     * Топ игр по количеству проданных копий.
     */
    public function topGames(int $limit = 10, ?string $from = null, ?string $to = null, ?int $sellerId = null): Collection
    {
        $query = DB::table('order_items')
            ->join('games', 'games.id', '=', 'order_items.game_id')
            ->join('orders', 'orders.id', '=', 'order_items.order_id')
            ->where('orders.status', 'completed')
            ->select('games.id', 'games.title', DB::raw('SUM(order_items.quantity) as sold_count'))
            ->groupBy('games.id', 'games.title')
            ->orderByDesc('sold_count')
            ->limit($limit);

        if ($sellerId) {
            $query->where('games.seller_id', $sellerId);
        }

        $this->applyDateFilter($query, $from, $to, 'orders.paid_at');

        return $query->get();
    }

    /**
     * Выручка продавца по завершенным заказам.
     */
    public function sellerRevenue(int $sellerId, ?string $from = null, ?string $to = null): float
    {
        $query = DB::table('order_items')
            ->join('games', 'games.id', '=', 'order_items.game_id')
            ->join('orders', 'orders.id', '=', 'order_items.order_id')
            ->where('games.seller_id', $sellerId)
            ->where('orders.status', 'completed')
            ->select(DB::raw('SUM(order_items.price * order_items.quantity) as revenue'));

        $this->applyDateFilter($query, $from, $to, 'orders.paid_at');

        return (float) $query->value('revenue');
    }

    /**
     * Применить фильтры по датам (YYYY-MM-DD).
     */
    private function applyDateFilter($query, ?string $from, ?string $to, string $column): void
    {
        if ($from) {
            $query->whereDate($column, '>=', $from);
        }
        if ($to) {
            $query->whereDate($column, '<=', $to);
        }
    }
}