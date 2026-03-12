@extends('layouts.app')

@section('title', 'Редактирование игры')

@section('content')
<main class="grid" style="gap: 20px;">
    <section class="card" style="max-width: 900px; margin: 0 auto;">
        <h2 style="margin-top: 0;">Редактировать игру</h2>

        @if ($errors->any())
            <div class="card" style="border-color: #fecaca; background: #fef2f2;">
                <div class="error">Проверьте заполнение полей.</div>
            </div>
        @endif

        <form method="POST" action="/seller/games/{{ $game->id }}">
            @csrf
            @method('PUT')

            <div class="field">
                <label for="title">Название</label>
                <input class="input" id="title" name="title" type="text" value="{{ old('title', $game->title) }}" required>
                @error('title')
                    <div class="error">{{ $message }}</div>
                @enderror
            </div>

            <div class="field">
                <label for="description">Описание</label>
                <textarea class="input" id="description" name="description" rows="4">{{ old('description', $game->description) }}</textarea>
                @error('description')
                    <div class="error">{{ $message }}</div>
                @enderror
            </div>

            <div class="grid grid-2">
                <div class="field">
                    <label for="price">Цена</label>
                    <input class="input" id="price" name="price" type="number" step="0.01" min="0" value="{{ old('price', $game->price) }}" required>
                    @error('price')
                        <div class="error">{{ $message }}</div>
                    @enderror
                </div>

                <div class="field">
                    <label for="currency">Валюта</label>
                    <input class="input" id="currency" name="currency" type="text" maxlength="3" value="{{ old('currency', $game->currency) }}" required>
                    @error('currency')
                        <div class="error">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <div class="field">
                <label>Категории</label>
                <div class="grid" style="grid-template-columns: repeat(auto-fit, minmax(160px, 1fr)); gap: 8px;">
                    @foreach ($categories as $category)
                        <label class="pill" style="display: inline-flex; align-items: center; gap: 6px;">
                            <input type="checkbox" name="category_ids[]" value="{{ $category->id }}"
                                @checked(in_array($category->id, old('category_ids', $game->categories->pluck('id')->all())))>
                            {{ $category->name }}
                        </label>
                    @endforeach
                </div>
                @error('category_ids')
                    <div class="error">{{ $message }}</div>
                @enderror
            </div>

            <div class="field">
                <label>Теги</label>
                <div class="grid" style="grid-template-columns: repeat(auto-fit, minmax(160px, 1fr)); gap: 8px;">
                    @foreach ($tags as $tag)
                        <label class="pill" style="display: inline-flex; align-items: center; gap: 6px;">
                            <input type="checkbox" name="tag_ids[]" value="{{ $tag->id }}"
                                @checked(in_array($tag->id, old('tag_ids', $game->tags->pluck('id')->all())))>
                            {{ $tag->name }}
                        </label>
                    @endforeach
                </div>
                @error('tag_ids')
                    <div class="error">{{ $message }}</div>
                @enderror
            </div>

            <button class="btn" type="submit">Обновить</button>
        </form>

        <form method="POST" action="/seller/games/{{ $game->id }}" style="margin-top: 16px;">
            @csrf
            @method('DELETE')
            <button class="pill" type="submit" onclick="return confirm('Удалить игру?')">Удалить</button>
        </form>
    </section>

    <section class="card" style="max-width: 900px; margin: 0 auto;">
        <h3 style="margin-top: 0;">Файлы игры</h3>
        <p class="muted">Загружайте файлы в storage/app/games.</p>

        <form method="POST" action="/seller/games/{{ $game->id }}/files" enctype="multipart/form-data">
            @csrf
            <div class="field">
                <label for="file">Файл</label>
                <input class="input" id="file" name="file" type="file" required>
                @error('file')
                    <div class="error">{{ $message }}</div>
                @enderror
            </div>

            <div class="field">
                <label for="version">Версия</label>
                <input class="input" id="version" name="version" type="text" value="{{ old('version') }}">
            </div>

            <button class="btn" type="submit">Загрузить файл</button>
        </form>

        @if ($game->files->isEmpty())
            <div class="muted" style="margin-top: 12px;">Файлы пока не загружены.</div>
        @else
            <div class="grid" style="gap: 8px; margin-top: 12px;">
                @foreach ($game->files as $file)
                    <div class="card" style="display: flex; justify-content: space-between; align-items: center;">
                        <div>
                            <div><strong>{{ $file->file_name }}</strong></div>
                            <div class="muted">Версия: {{ $file->version ?? '—' }}</div>
                        </div>
                        <span class="muted">{{ number_format($file->file_size / 1024, 1) }} KB</span>
                    </div>
                @endforeach
            </div>
        @endif
    </section>
</main>
@endsection