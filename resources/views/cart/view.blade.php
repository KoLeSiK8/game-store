@extends('layouts.app')

@section('title', 'Корзина')

@section('content')
<main class="grid" style="gap: 20px;">
    <section class="card">
        <h2 style="margin-top: 0;">Корзина</h2>
        <p class="muted">Здесь собраны игры, которые вы планируете купить.</p>
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

        @if (empty($items))
            <div class="muted">Корзина пуста.</div>
        @else
            <div class="grid" style="gap: 12px;">
                @foreach ($items as $item)
                    <div class="card" style="display: flex; justify-content: space-between; align-items: center; gap: 12px; flex-wrap: wrap;">
                        <div>
                            <div><strong>{{ $item['game']->title }}</strong></div>
                            <div class="muted">{{ number_format($item['game']->price, 2, '.', ' ') }} {{ $item['game']->currency }}</div>
                            <div class="muted">Количество: {{ $item['quantity'] }}</div>
                        </div>

                        <div style="display: flex; gap: 8px; flex-wrap: wrap; justify-content: flex-end;">
                            <a class="pill" href="{{ route('games.show', $item['game']) }}">Страница игры</a>

                            @auth
                                <form method="POST" action="{{ route('orders.create') }}">
                                    @csrf
                                    <input type="hidden" name="game_id" value="{{ $item['game']->id }}">
                                    <button class="btn" type="submit">Купить</button>
                                </form>
                            @endauth

                            <form method="POST" action="{{ route('cart.remove') }}">
                                @csrf
                                <input type="hidden" name="game_id" value="{{ $item['game']->id }}">
                                <button class="pill" type="submit">Удалить</button>
                            </form>
                        </div>
                    </div>
                @endforeach
            </div>

            <div style="margin-top: 16px; display: flex; gap: 10px; flex-wrap: wrap;">
                <form method="POST" action="{{ route('cart.clear') }}">
                    @csrf
                    <button class="pill" type="submit">Очистить корзину</button>
                </form>

                @auth
                    <form method="POST" action="{{ route('orders.create') }}">
                        @csrf
                        <button class="btn" type="submit">Купить все</button>
                    </form>
                @endauth

                @guest
                    <a class="btn" href="/login">Войти для покупки</a>
                @endguest
            </div>
        @endif
    </section>
</main>
@endsection