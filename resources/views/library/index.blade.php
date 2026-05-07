@extends('layouts.app')

@section('title', 'Моя библиотека')

@section('content')
<main class="grid" style="gap: 20px;">
    <section class="card">
        <h2 style="margin-top: 0;">Моя библиотека</h2>
        <p class="muted">Все купленные игры доступны для защищенного скачивания через приложение.</p>
    </section>

    <section class="card">
        @if ($library->isEmpty())
            <div class="muted">Библиотека пуста.</div>
        @else
            <div class="grid" style="gap: 12px;">
                @foreach ($library as $entry)
                    @php
                        $files = $entry->game->files->where('is_active', true);
                    @endphp

                    <div class="card" style="display: grid; gap: 12px;">
                        <div>
                            <strong>{{ $entry->game->title }}</strong>
                            <div class="muted">Куплено: {{ $entry->purchased_at->format('d.m.Y') }}</div>
                        </div>

                        <div style="display: flex; gap: 8px; flex-wrap: wrap;">
                            <a class="pill" href="{{ route('games.show', $entry->game) }}">Открыть страницу игры</a>
                        </div>

                        @if ($files->isEmpty())
                            <div class="muted">Активные файлы для скачивания пока не загружены.</div>
                        @else
                            <div class="grid" style="gap: 8px;">
                                @foreach ($files as $file)
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
                    </div>
                @endforeach
            </div>
        @endif
    </section>
</main>
@endsection