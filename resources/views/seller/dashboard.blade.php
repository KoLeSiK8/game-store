@extends('layouts.app')

@section('title', 'Кабинет продавца')

@section('content')
<main class="grid" style="gap: 20px;">
    <section class="card">
        <h2 style="margin-top: 0;">Личный кабинет продавца</h2>
        <p class="muted">Здесь вы можете добавлять игры, редактировать версии файлов и отправлять изменения на модерацию администратору. Игра попадает в продажу только после одобрения администратора.</p>
        <div style="margin-top: 12px; display: flex; gap: 10px; flex-wrap: wrap;">
            <a class="btn" href="{{ route('seller.games.create') }}">Добавить игру</a>
            <a class="pill" href="{{ route('analytics.index') }}">Аналитика</a>
        </div>
    </section>

    @if (session('status'))
        <section class="card" style="border-color: #bfdbfe; background: #eff6ff;">
            {{ session('status') }}
        </section>
    @endif

    <section class="card">
        <h3 style="margin-top: 0;">Мои игры</h3>

        @if ($games->isEmpty())
            <div class="muted">У вас пока нет игр. Начните с создания новой карточки игры.</div>
        @else
            <div class="grid" style="gap: 12px;">
                @foreach ($games as $game)
                    <div class="card" style="display: grid; gap: 10px;">
                        <div style="display: flex; justify-content: space-between; gap: 12px; align-items: flex-start; flex-wrap: wrap;">
                            <div>
                                <strong>{{ $game->title }}</strong>
                                <div class="muted">Статус: {{ $game->status }}</div>
                                <div class="muted">Категории: {{ $game->categories->pluck('name')->implode(', ') ?: '—' }}</div>
                                <div class="muted">Теги: {{ $game->tags->pluck('name')->implode(', ') ?: '—' }}</div>
                            </div>
                            <div style="display: flex; gap: 8px; flex-wrap: wrap;">
                                <a class="pill" href="{{ route('games.show', $game) }}">Страница игры</a>
                                <a class="btn" href="{{ route('seller.games.edit', $game) }}">Редактировать</a>
                            </div>
                        </div>
                        <div class="muted">Версий файлов: {{ $game->files->count() }}</div>
                    </div>
                @endforeach
            </div>
        @endif
    </section>
</main>
@endsection