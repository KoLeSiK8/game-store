@extends('layouts.app')

@section('title', 'История заказов')

@section('content')
<main class="grid" style="gap: 20px;">
    <section class="card">
        <h2 style="margin-top: 0;">История заказов</h2>
        <p class="muted">Все ваши покупки в одном месте.</p>
    </section>

    <section class="card">
        @if ($orders->isEmpty())
            <div class="muted">Заказов пока нет.</div>
        @else
            <div class="grid" style="gap: 12px;">
                @foreach ($orders as $order)
                    <div class="card" style="display: flex; justify-content: space-between; align-items: center;">
                        <div>
                            <div><strong>Заказ #{{ $order->id }}</strong></div>
                            <div class="muted">{{ $order->created_at->format('d.m.Y H:i') }}</div>
                            <div class="muted">Статус: {{ $order->status }}</div>
                        </div>
                        <div>
                            <div><strong>{{ number_format($order->total_amount, 2, '.', ' ') }} {{ $order->currency }}</strong></div>
                            <a class="pill" href="/orders/{{ $order->id }}">Подробнее</a>
                        </div>
                    </div>
                @endforeach
            </div>

            <div style="margin-top: 16px;">
                {{ $orders->links() }}
            </div>
        @endif
    </section>
</main>
@endsection