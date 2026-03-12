@extends('layouts.app')

@section('title', 'Оформление заказа')

@section('content')
<main class="grid" style="gap: 20px;">
    <section class="card">
        <h2 style="margin-top: 0;">Оформление заказа</h2>
        <p class="muted">Проверьте корзину перед оплатой.</p>
    </section>

    <section class="card">
        @if ($errors->any())
            <div class="card" style="border-color: #fecaca; background: #fef2f2; margin-bottom: 12px;">
                <div class="error">{{ $errors->first() }}</div>
            </div>
        @endif

        @if (empty($items))
            <div class="muted">Корзина пуста.</div>
            <div style="margin-top: 12px;">
                <a class="btn" href="/catalog">Перейти в каталог</a>
            </div>
        @else
            <div class="grid" style="gap: 12px;">
                @foreach ($items as $item)
                    <div class="card" style="display: flex; justify-content: space-between; align-items: center;">
                        <div>
                            <strong>{{ $item['game']->title }}</strong>
                            <div class="muted">{{ $item['quantity'] }} x {{ number_format($item['game']->price, 2, '.', ' ') }} {{ $item['game']->currency }}</div>
                        </div>
                        <div>
                            <strong>{{ number_format($item['game']->price * $item['quantity'], 2, '.', ' ') }} {{ $item['game']->currency }}</strong>
                        </div>
                    </div>
                @endforeach
            </div>

            <div class="card" style="margin-top: 16px; display: flex; justify-content: space-between; align-items: center;">
                <strong>Итого:</strong>
                <strong>{{ number_format($total, 2, '.', ' ') }} USD</strong>
            </div>

            <form method="POST" action="/checkout" style="margin-top: 16px;">
                @csrf
                <button class="btn" type="submit">Подтвердить покупку</button>
            </form>
        @endif
    </section>
</main>
@endsection