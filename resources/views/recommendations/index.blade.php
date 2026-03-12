@extends('layouts.app')

@section('title', 'Рекомендации')

@section('content')
<main class="grid" style="gap: 20px;">
    <section class="card">
        <h2 style="margin-top: 0;">Рекомендации для вас</h2>
        <p class="muted">Подборка игр на основе ваших покупок и интересов.</p>
    </section>

    <section class="grid grid-3">
        @forelse ($games as $game)
            <div class="card" data-game-id="{{ $game->id }}">
                <div class="muted" style="font-size: 12px; text-transform: uppercase; letter-spacing: 0.08em;">
                    {{ $game->status ?? 'approved' }}
                </div>
                <h3 style="margin: 8px 0;">{{ $game->title }}</h3>
                <p class="muted" style="min-height: 48px;">
                    {{ $game->description ? \Illuminate\Support\Str::limit($game->description, 90) : 'Описание появится позже.' }}
                </p>
                <div style="margin-top: 10px; display: flex; justify-content: space-between; align-items: center;">
                    <strong>{{ number_format($game->price, 2, '.', ' ') }} {{ $game->currency }}</strong>
                </div>
                <div style="margin-top: 10px; display: flex; gap: 8px; flex-wrap: wrap;">
                    <a class="pill" href="/catalog">Открыть каталог</a>
                    <a class="pill" href="/games/{{ $game->id }}">Открыть страницу игры</a>
                    <form method="POST" action="/cart/toggle" class="cart-toggle">
                        @csrf
                        <input type="hidden" name="game_id" value="{{ $game->id }}">
                        <button class="pill" type="submit">В корзину</button>
                    </form>
                    <form method="POST" action="/wishlist/toggle/{{ $game->id }}" class="wishlist-toggle">
                        @csrf
                        <button class="pill" type="submit">В избранное</button>
                    </form>
                </div>
            </div>
        @empty
            <div class="card" style="grid-column: 1 / -1;">
                <strong>Пока нет рекомендаций.</strong>
                <div class="muted">Добавьте игры в избранное или совершите покупку.</div>
            </div>
        @endforelse
    </section>
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
