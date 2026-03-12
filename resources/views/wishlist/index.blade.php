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

        @if ($items->isEmpty())
            <div class="muted">Избранное пусто.</div>
        @else
            <div class="grid" style="gap: 12px;">
                @foreach ($items as $item)
                    <div class="card" style="display: flex; justify-content: space-between; align-items: center;">
                        <div>
                            <strong>{{ $item->game->title }}</strong>
                            <div class="muted">{{ number_format($item->game->price, 2, '.', ' ') }} {{ $item->game->currency }}</div>
                        </div>
                        <form method="POST" action="/wishlist/remove/{{ $item->game->id }}">
                            @csrf
                            <button class="pill" type="submit">Убрать</button>
                        </form>
                    </div>
                @endforeach
            </div>
        @endif
    </section>
</main>
@endsection