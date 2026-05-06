@extends('layouts.app')

@section('title', 'Моя библиотека')

@section('content')
<main class="grid" style="gap: 20px;">
    <section class="card">
        <h2 style="margin-top: 0;">Моя библиотека</h2>
        <p class="muted">Все купленные игры доступны для скачивания.</p>
    </section>

    <section class="card">
        @if ($library->isEmpty())
            <div class="muted">Библиотека пуста.</div>
        @else
            <div class="grid" style="gap: 12px;">
                @foreach ($library as $entry)
                    <div class="card" style="display: grid; gap: 10px;">
                        <div>
                            <strong>{{ $entry->game->title }}</strong>
                            <div class="muted">Куплено: {{ $entry->purchased_at->format('d.m.Y') }}</div>
                        </div>

                        <div>
                            <a class="pill" href="{{ route('games.show', $entry->game) }}">Открыть страницу игры</a>
                        </div>

                        @if ($entry->game->files->isEmpty())
                            <div class="muted">Файлы для скачивания пока не загружены.</div>
                        @else
                            <div class="grid" style="gap: 8px;">
                                @foreach ($entry->game->files as $file)
                                    <div style="display: flex; justify-content: space-between; align-items: center; gap: 12px; flex-wrap: wrap;">
                                        <div>
                                            <div><strong>{{ $file->file_name }}</strong></div>
                                            <div class="muted">Версия: {{ $file->version ?? '—' }}</div>
                                        </div>
                                        <a class="pill" href="{{ route('library.download', $file) }}">Скачать</a>
                                    </div>
                                @endforeach
                            </div>
                        @endif
                    </div>
                @endforeach
            </div>
        @endif
    </section>
</main>
@endsection