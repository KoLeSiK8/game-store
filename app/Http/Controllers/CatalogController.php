<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Game;
use App\Models\GameTag;
use App\Services\ActivityLogger;
use Illuminate\Http\Request;

class CatalogController extends Controller
{
    /**
     * Каталог игр с фильтрами.
     */
    public function index(Request $request, ActivityLogger $activityLogger)
    {
        $query = Game::query()
            ->where('status', 'approved')
            ->with(['categories', 'tags'])
            ->withAvg('reviews', 'rating');

        // Логируем поиск/фильтрацию, если пользователь авторизован.
        $user = $request->user();
        $filters = $request->only(['q', 'category', 'tag', 'price_min', 'price_max', 'rating_min']);
        $hasFilters = collect($filters)->filter(fn ($v) => $v !== null && $v !== '')->isNotEmpty();
        if ($user && $hasFilters) {
            $activityLogger->logSearch($user, $filters);
        }

        // Фильтр по категории (slug или id).
        if ($request->filled('category')) {
            $category = $request->input('category');
            $query->whereHas('categories', function ($q) use ($category) {
                $q->where('categories.slug', $category)
                    ->orWhere('categories.id', $category);
            });
        }

        // Фильтр по тегу (slug или id).
        if ($request->filled('tag')) {
            $tag = $request->input('tag');
            $query->whereHas('tags', function ($q) use ($tag) {
                $q->where('game_tags.slug', $tag)
                    ->orWhere('game_tags.id', $tag);
            });
        }

        // Поиск по названию.
        if ($request->filled('q')) {
            $term = trim($request->input('q'));
            $query->where('title', 'like', '%' . $term . '%');
        }

        // Фильтр по цене.
        if ($request->filled('price_min')) {
            $query->where('price', '>=', $request->input('price_min'));
        }
        if ($request->filled('price_max')) {
            $query->where('price', '<=', $request->input('price_max'));
        }

        // Фильтр по рейтингу (средняя оценка).
        if ($request->filled('rating_min')) {
            $ratingMin = (float) $request->input('rating_min');
            $query->havingRaw('COALESCE(reviews_avg_rating, 0) >= ?', [$ratingMin]);
        }

        $games = $query->orderBy('title')->paginate(12)->withQueryString();

        return view('catalog.index', [
            'games' => $games,
            'categories' => Category::orderBy('name')->get(),
            'tags' => GameTag::orderBy('name')->get(),
        ]);
    }
}
