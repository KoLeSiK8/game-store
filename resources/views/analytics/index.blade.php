@extends('layouts.app')

@section('title', 'Аналитика продаж')

@section('content')
<main class="grid" style="gap: 20px;">
    <section class="card">
        <h2 style="margin-top: 0;">Аналитика продаж</h2>
        <p class="muted">Статистика продаж и популярные игры.</p>
    </section>

    <section class="card">
        <h3 style="margin-top: 0;">Фильтр по датам</h3>
        <form method="GET" action="/analytics" class="grid" style="grid-template-columns: repeat(auto-fit, minmax(180px, 1fr)); gap: 12px;">
            <div class="field">
                <label for="from">С даты</label>
                <input class="input" id="from" name="from" type="date" value="{{ $from }}">
            </div>
            <div class="field">
                <label for="to">По дату</label>
                <input class="input" id="to" name="to" type="date" value="{{ $to }}">
            </div>
            <div style="display: flex; align-items: end; gap: 8px;">
                <button class="btn" type="submit">Применить</button>
                <a class="pill" href="/analytics">Сбросить</a>
            </div>
        </form>
    </section>

    <section class="grid grid-2">
        <div class="card">
            <h3 style="margin-top: 0;">Общая выручка</h3>
            <div style="font-size: 24px; font-weight: 700;">{{ number_format($totalSales, 2, '.', ' ') }} USD</div>
            @if (!$isAdmin)
                <div class="muted" style="margin-top: 6px;">Статистика только по вашим играм.</div>
            @endif
        </div>
        <div class="card">
            <h3 style="margin-top: 0;">Топ игр</h3>
            @if ($topGames->isEmpty())
                <div class="muted">Данных пока нет.</div>
            @else
                <div class="grid" style="gap: 8px;">
                    @foreach ($topGames as $game)
                        <div style="display: flex; justify-content: space-between;">
                            <span>{{ $game->title }}</span>
                            <strong>{{ $game->sold_count }}</strong>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    </section>
</main>
@endsection