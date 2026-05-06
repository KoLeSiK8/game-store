@extends('layouts.app')

@section('title', $game->title)

@section('content')
<main class="grid" style="gap: 20px;">
    <section class="card" style="display: grid; gap: 16px;">
        @if (session('status'))
            <div class="card" style="border-color: #bfdbfe; background: #eff6ff;">
                {{ session('status') }}
            </div>
        @endif

        @if ($errors->any())
            <div class="card" style="border-color: #fecaca; background: #fef2f2;">
                {{ $errors->first() }}
            </div>
        @endif

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
                @if ($isOwned)
                    <a class="pill" href="/library">Уже в библиотеке</a>
                @else
                    <form method="POST" action="/orders/create">
                        @csrf
                        <input type="hidden" name="game_id" value="{{ $game->id }}">
                        <button class="btn" type="submit">Купить</button>
                    </form>
                @endif

                <form method="POST" action="/cart/toggle">
                    @csrf
                    <input type="hidden" name="game_id" value="{{ $game->id }}">
                    <button class="pill" type="submit">В корзину</button>
                </form>

                <form method="POST" action="/wishlist/toggle/{{ $game->id }}">
                    @csrf
                    <button class="pill" type="submit">В избранное</button>
                </form>

                @if ($isOwned)
                    <a class="pill" href="/games/{{ $game->id }}/reviews/create">Оставить отзыв</a>
                @endif
            @endauth

            @guest
                <a class="pill" href="/login">Войти, чтобы купить</a>
            @endguest
        </div>
    </section>

    <section class="card">
        <h3 style="margin-top: 0;">Файлы игры</h3>

        @auth
            @if (!$isOwned)
                <div class="muted">Скачивание станет доступно после успешной покупки.</div>
            @elseif ($game->files->isEmpty())
                <div class="muted">Файлы пока не загружены продавцом.</div>
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
        @else
            <div class="muted">Войдите в аккаунт, чтобы получить доступ к покупке и загрузке.</div>
        @endauth
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
