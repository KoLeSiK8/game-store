@extends('layouts.app')

@section('title', 'Корзина')

@section('content')
<main class="grid" style="gap: 20px;">
    <section class="card">
        <h2 style="margin-top: 0;">Корзина</h2>
        <p class="muted">Список игр, которые вы хотите купить.</p>
    </section>

    <section class="card">
        @if (session('status'))
            <div class="card" style="border-color: #bfdbfe; background: #eff6ff; margin-bottom: 12px;">
                {{ session('status') }}
            </div>
        @endif

        @if (empty($items))
            <div class="muted">Корзина пуста.</div>
        @else
            <div class="grid" style="gap: 12px;">
                @foreach ($items as $item)
                    <div class="card" style="display: flex; justify-content: space-between; align-items: center;">
                        <div>
                            <div><strong>{{ $item['game']->title }}</strong></div>
                            <div class="muted">{{ number_format($item['game']->price, 2, '.', ' ') }} {{ $item['game']->currency }}</div>
                            <div class="muted">Количество: {{ $item['quantity'] }}</div>
                        </div>
                        <form method="POST" action="/cart/remove">
                            @csrf
                            <input type="hidden" name="game_id" value="{{ $item['game']->id }}">
                            <button class="pill" type="submit">Удалить</button>
                        </form>
                    </div>
                @endforeach
            </div>

            <form method="POST" action="/cart/clear" style="margin-top: 16px;">
                @csrf
                <button class="pill" type="submit">Очистить корзину</button>
            </form>

            <div style="margin-top: 16px;">
                <a class="btn" href="/checkout">Перейти к оформлению</a>
            </div>
        @endif
    </section>
</main>
@endsection
