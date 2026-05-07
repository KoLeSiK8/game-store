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
                    <a class="pill" href="{{ route('library.index') }}">Уже в библиотеке</a>
                @else
                    <form method="POST" action="{{ route('orders.create') }}">
                        @csrf
                        <input type="hidden" name="game_id" value="{{ $game->id }}">
                        <button class="btn" type="submit">Купить</button>
                    </form>
                @endif

                <form method="POST" action="{{ route('cart.toggle') }}">
                    @csrf
                    <input type="hidden" name="game_id" value="{{ $game->id }}">
                    <button class="pill" type="submit">В корзину</button>
                </form>

                <form method="POST" action="{{ route('wishlist.toggle', $game) }}">
                    @csrf
                    <button class="pill" type="submit">В избранное</button>
                </form>

                @if ($isOwned)
                    <a class="pill" href="{{ route('reviews.create', $game) }}">Оставить отзыв</a>
                @endif
            @endauth

            @guest
                <a class="pill" href="{{ route('login') }}">Войти, чтобы купить</a>
            @endguest
        </div>
    </section>

    <section class="card">
        <h3 style="margin-top: 0;">Файлы игры</h3>

        @php
            $availableFiles = $game->files->where('is_active', true);
        @endphp

        @auth
            @if (!$isOwned)
                <div class="muted">Скачивание станет доступно после успешной покупки.</div>
            @elseif ($availableFiles->isEmpty())
                <div class="muted">Продавец пока не загрузил активный файл для скачивания.</div>
            @else
                <div class="grid" style="gap: 10px;">
                    @foreach ($availableFiles as $file)
                        <div class="card" style="display: flex; justify-content: space-between; align-items: center; gap: 12px; flex-wrap: wrap;">
                            <div>
                                <div><strong>{{ $file->original_file_name }}</strong></div>
                                <div class="muted">Версия: {{ $file->version }}</div>
                                <div class="muted">Размер: {{ number_format($file->file_size / 1048576, 2) }} MB</div>
                                <div class="muted">MD5: {{ $file->md5_hash ?: '—' }}</div>
                            </div>
                            <a class="pill" href="{{ route('library.files.show', $file) }}">Скачать</a>
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