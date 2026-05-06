@extends('layouts.app')

@section('title', 'Избранное')

@section('content')
<main class="grid" style="gap: 20px;">
    <section class="card">
        <h2 style="margin-top: 0;">Избранное</h2>
        <p class="muted">Список игр, которые вы сохранили.</p>
    </section>

    <section class="card">
        @if (session('status'))
            <div class="card" style="border-color: #bfdbfe; background: #eff6ff; margin-bottom: 12px;">
                {{ session('status') }}
            </div>
        @endif

        @if ($errors->any())
            <div class="card" style="border-color: #fecaca; background: #fef2f2; margin-bottom: 12px;">
                {{ $errors->first() }}
            </div>
        @endif

        @if ($items->isEmpty())
            <div class="muted">Избранное пусто.</div>
        @else
            <div class="grid" style="gap: 12px;">
                @foreach ($items as $item)
                    <div class="card" style="display: flex; justify-content: space-between; align-items: center; gap: 12px; flex-wrap: wrap;">
                        <div>
                            <strong>{{ $item->game->title }}</strong>
                            <div class="muted">{{ number_format($item->game->price, 2, '.', ' ') }} {{ $item->game->currency }}</div>
                        </div>

                        <div style="display: flex; gap: 8px; flex-wrap: wrap; justify-content: flex-end;">
                            <a class="pill" href="{{ route('games.show', $item->game) }}">Страница игры</a>

                            <form method="POST" action="{{ route('cart.toggle') }}">
                                @csrf
                                <input type="hidden" name="game_id" value="{{ $item->game->id }}">
                                <button class="pill" type="submit">
                                    {{ in_array($item->game->id, $cartGameIds ?? [], true) ? 'Убрать из корзины' : 'В корзину' }}
                                </button>
                            </form>

                            <form method="POST" action="{{ route('orders.create') }}">
                                @csrf
                                <input type="hidden" name="game_id" value="{{ $item->game->id }}">
                                <button class="btn" type="submit">Купить</button>
                            </form>

                            <form method="POST" action="{{ route('wishlist.remove', $item->game) }}">
                                @csrf
                                <button class="pill" type="submit">Убрать</button>
                            </form>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </section>
</main>
@endsection
