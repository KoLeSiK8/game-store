@extends('layouts.app')

@section('title', $game->title)

@section('content')
<main class="grid" style="gap: 20px;">
    <section class="card" style="display: grid; gap: 12px;">
        <div class="muted" style="text-transform: uppercase; letter-spacing: 0.08em; font-size: 12px;">
            {{ $game->status ?? 'approved' }}
        </div>
        <h2 style="margin: 0;">{{ $game->title }}</h2>
        <div class="muted">{{ $game->description ?? 'Описание появится позже.' }}</div>
        <div style="display: flex; gap: 10px; flex-wrap: wrap;">
            <span class="pill">{{ number_format($game->price, 2, '.', ' ') }} {{ $game->currency }}</span>
            <span class="pill">Категории: {{ $game->categories->pluck('name')->implode(', ') ?: '—' }}</span>
            <span class="pill">Теги: {{ $game->tags->pluck('name')->implode(', ') ?: '—' }}</span>
        </div>
        <div style="display: flex; gap: 8px; flex-wrap: wrap;">
            @auth
                <form method="POST" action="/cart/add">
                    @csrf
                    <input type="hidden" name="game_id" value="{{ $game->id }}">
                    <button class="pill" type="submit">В корзину</button>
                </form>
                <form method="POST" action="/wishlist/add/{{ $game->id }}">
                    @csrf
                    <button class="pill" type="submit">В избранное</button>
                </form>
                <a class="pill" href="/games/{{ $game->id }}/reviews/create">Оставить отзыв</a>
            @endauth
            @guest
                <a class="pill" href="/login">Войти, чтобы купить</a>
            @endguest
        </div>
    </section>

    <section class="card">
        <h3 style="margin-top: 0;">Файлы для скачивания</h3>
        @if ($game->files->isEmpty())
            <div class="muted">Файлы пока не загружены.</div>
        @else
            <div class="grid" style="gap: 8px;">
                @foreach ($game->files as $file)
                    <div style="display: flex; justify-content: space-between; align-items: center;">
                        <div>
                            <div><strong>{{ $file->file_name }}</strong></div>
                            <div class="muted">Версия: {{ $file->version ?? '—' }}</div>
                        </div>
                        <a class="pill" href="/library/download/{{ $file->id }}">Скачать</a>
                    </div>
                @endforeach
            </div>
        @endif
    </section>

    <section class="card">
        <h3 style="margin-top: 0;">Отзывы</h3>
        @if ($game->reviews->isEmpty())
            <div class="muted">Отзывов пока нет.</div>
        @else
            <div class="grid" style="gap: 12px;">
                @foreach ($game->reviews as $review)
                    <div class="card" style="display: grid; gap: 6px;">
                        <strong>{{ $review->user->name }}</strong>
                        <div class="muted">Оценка: {{ $review->rating }}</div>
                        <div>{{ $review->content ?? 'Без комментария.' }}</div>
                    </div>
                @endforeach
            </div>
        @endif
    </section>
</main>
@endsection