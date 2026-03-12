@extends('layouts.app')

@section('title', 'Каталог игр')

@section('content')
<main class="grid" style="gap: 20px;">
    <section class="card">
        <h2 style="margin-top: 0;">Каталог игр</h2>
        <p class="muted" style="margin-top: 4px;">Фильтруйте игры по категориям, тегам, цене и рейтингу.</p>
    </section>

    <section class="grid" style="grid-template-columns: 280px 1fr; gap: 20px;">
        <aside class="card" style="height: fit-content;">
            <h3 style="margin-top: 0;">Фильтры</h3>
            <form method="GET" action="/catalog">
                <div class="field">
                    <label for="q">Поиск по названию</label>
                    <input id="q" name="q" class="input" placeholder="Например: Test Game 1" value="{{ request('q') }}">
                </div>

                <div class="field">
                    <label for="category">Категория</label>
                    <select id="category" name="category" class="input">
                        <option value="">Все категории</option>
                        @foreach ($categories as $category)
                            <option value="{{ $category->slug }}" @selected(request('category') === $category->slug)>
                                {{ $category->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="field">
                    <label for="tag">Тег</label>
                    <select id="tag" name="tag" class="input">
                        <option value="">Все теги</option>
                        @foreach ($tags as $tag)
                            <option value="{{ $tag->slug }}" @selected(request('tag') === $tag->slug)>
                                {{ $tag->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="field">
                    <label for="price_min">Цена от</label>
                    <input id="price_min" name="price_min" class="input" type="number" step="0.01" min="0" value="{{ request('price_min') }}">
                </div>

                <div class="field">
                    <label for="price_max">Цена до</label>
                    <input id="price_max" name="price_max" class="input" type="number" step="0.01" min="0" value="{{ request('price_max') }}">
                </div>

                <div class="field">
                    <label for="rating_min">Рейтинг от</label>
                    <select id="rating_min" name="rating_min" class="input">
                        <option value="">Любой</option>
                        @foreach ([1, 2, 3, 4, 5] as $rating)
                            <option value="{{ $rating }}" @selected(request('rating_min') == $rating)>
                                {{ $rating }}+
                            </option>
                        @endforeach
                    </select>
                </div>

                <button class="btn" type="submit">Применить</button>
                <a class="pill" style="display: inline-flex; margin-left: 8px;" href="/catalog">Сбросить</a>
            </form>
        </aside>

        <section class="grid grid-3">
            @forelse ($games as $game)
                <div class="card" data-game-id="{{ $game->id }}">
                    <div class="muted" style="font-size: 12px; text-transform: uppercase; letter-spacing: 0.08em;">
                        {{ $game->status ?? 'active' }}
                    </div>
                    <h3 style="margin: 8px 0;">{{ $game->title }}</h3>
                    <div style="margin-bottom: 8px;">
                        <a class="pill" href="/games/{{ $game->id }}">Открыть страницу игры</a>
                    </div>
                    <p class="muted" style="min-height: 48px;">
                        {{ $game->description ? \Illuminate\Support\Str::limit($game->description, 90) : 'Описание появится позже.' }}
                    </p>
                    <div class="muted" style="font-size: 14px;">
                        Категории: {{ $game->categories->pluck('name')->implode(', ') ?: '—' }}
                    </div>
                    <div class="muted" style="font-size: 14px;">
                        Теги: {{ $game->tags->pluck('name')->implode(', ') ?: '—' }}
                    </div>
                    <div style="margin-top: 10px; display: flex; justify-content: space-between; align-items: center;">
                        <strong>{{ number_format($game->price, 2, '.', ' ') }} {{ $game->currency }}</strong>
                        <span class="muted">Рейтинг: {{ $game->reviews_avg_rating ? number_format($game->reviews_avg_rating, 1) : '—' }}</span>
                    </div>
                    <div class="catalog-actions" style="margin-top: 10px; display: flex; gap: 8px; flex-wrap: wrap;">
                        @auth
                            <form method="POST" action="/cart/toggle" class="cart-toggle">
                                @csrf
                                <input type="hidden" name="game_id" value="{{ $game->id }}">
                                <button class="pill" type="submit">В корзину</button>
                            </form>
                            <form method="POST" action="/wishlist/toggle/{{ $game->id }}" class="wishlist-toggle">
                                @csrf
                                <button class="pill" type="submit">В избранное</button>
                            </form>
                        @endauth
                        @guest
                            <a class="pill" href="/login">Войти, чтобы добавить</a>
                        @endguest
                    </div>
                </div>
            @empty
                <div class="card" style="grid-column: 1 / -1;">
                    <strong>Игры не найдены.</strong>
                    <div class="muted">Попробуйте изменить фильтры.</div>
                </div>
            @endforelse
        </section>
    </section>

    <div>
        <style>
            .mini-pagination { display: flex; gap: 8px; align-items: center; flex-wrap: wrap; }
            .mini-pagination a, .mini-pagination span {
                display: inline-flex;
                align-items: center;
                justify-content: center;
                padding: 6px 10px;
                border: 1px solid var(--border);
                border-radius: 10px;
                font-size: 14px;
                background: #fff;
            }
            .mini-pagination .disabled { color: #94a3b8; }
            .mini-pagination .current { background: #0f172a; color: #fff; border-color: #0f172a; }
        </style>
        <div class="mini-pagination">
            @if ($games->onFirstPage())
                <span class="disabled">« Назад</span>
            @else
                <a href="{{ $games->previousPageUrl() }}">« Назад</a>
            @endif

            <span class="current">Страница {{ $games->currentPage() }} из {{ $games->lastPage() }}</span>

            @if ($games->hasMorePages())
                <a href="{{ $games->nextPageUrl() }}">Вперед »</a>
            @else
                <span class="disabled">Вперед »</span>
            @endif
        </div>
    </div>
</main>
@auth
<script>
    (async () => {
        try {
            const res = await fetch('/catalog/state', { headers: { 'Accept': 'application/json' } });
            if (!res.ok) return;
            const data = await res.json();
            const cartSet = new Set((data.cart || []).map(String));
            const wishSet = new Set((data.wishlist || []).map(String));

            document.querySelectorAll('[data-game-id]').forEach(card => {
                const id = card.getAttribute('data-game-id');
                const cartBtn = card.querySelector('.cart-toggle button');
                const wishBtn = card.querySelector('.wishlist-toggle button');

                if (cartBtn) {
                    cartBtn.textContent = cartSet.has(id) ? 'В корзине' : 'В корзину';
                }
                if (wishBtn) {
                    wishBtn.textContent = wishSet.has(id) ? 'В избранном' : 'В избранное';
                }
            });
        } catch (e) {
            // ignore
        }
    })();
</script>
@endauth
@endsection