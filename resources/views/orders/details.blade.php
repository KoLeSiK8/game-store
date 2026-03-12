@extends('layouts.app')

@section('title', 'Детали заказа')

@section('content')
<main class="grid" style="gap: 20px;">
    <section class="card">
        <h2 style="margin-top: 0;">Заказ #{{ $order->id }}</h2>
        <div class="muted">Статус: {{ $order->status }}</div>
        <div class="muted">Дата: {{ $order->created_at->format('d.m.Y H:i') }}</div>
    </section>

    <section class="card">
        <h3 style="margin-top: 0;">Состав заказа</h3>
        <div class="grid" style="gap: 12px;">
            @foreach ($order->items as $item)
                <div class="card" style="display: flex; justify-content: space-between; align-items: center;">
                    <div>
                        <strong>{{ $item->game->title }}</strong>
                        <div class="muted">{{ $item->quantity }} x {{ number_format($item->price, 2, '.', ' ') }} {{ $item->currency }}</div>
                    </div>
                    <div>
                        <strong>{{ number_format($item->price * $item->quantity, 2, '.', ' ') }} {{ $item->currency }}</strong>
                    </div>
                </div>
            @endforeach
        </div>

        <div class="card" style="margin-top: 16px; display: flex; justify-content: space-between; align-items: center;">
            <strong>Итого:</strong>
            <strong>{{ number_format($order->total_amount, 2, '.', ' ') }} {{ $order->currency }}</strong>
        </div>
    </section>
</main>
@endsection