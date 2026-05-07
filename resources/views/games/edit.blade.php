@extends('layouts.app')

@section('title', 'Редактирование игры')

@section('content')
<main class="grid" style="gap: 20px;">
    <section class="card" style="max-width: 900px; margin: 0 auto;">
        <h2 style="margin-top: 0;">Редактировать игру</h2>

        @if (session('status'))
            <div class="card" style="border-color: #bfdbfe; background: #eff6ff; margin-bottom: 12px;">
                {{ session('status') }}
            </div>
        @endif

        @if ($errors->any())
            <div class="card" style="border-color: #fecaca; background: #fef2f2; margin-bottom: 12px;">
                <div class="error">Проверьте заполнение полей.</div>
            </div>
        @endif

        <form method="POST" action="{{ route('seller.games.update', $game) }}">
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

        <form method="POST" action="{{ route('seller.games.delete', $game) }}" style="margin-top: 16px;">
            @csrf
            @method('DELETE')
            <button class="pill" type="submit" onclick="return confirm('Удалить игру?')">Удалить игру</button>
        </form>
    </section>

    <section class="card" style="max-width: 900px; margin: 0 auto;">
        <h3 style="margin-top: 0;">Файлы игры</h3>
        <p class="muted">Файлы хранятся в защищенном каталоге <code>storage/app/private/games</code> и доступны только после покупки.</p>

        <form method="POST" action="{{ route('seller.games.files.upload', $game) }}" enctype="multipart/form-data">
            @csrf

            <div class="field">
                <label for="file">Файл</label>
                <input class="input" id="file" name="file" type="file" required>
                <div class="muted">Разрешены: zip, rar, 7z, exe.</div>
                @error('file')
                    <div class="error">{{ $message }}</div>
                @enderror
            </div>

            <div class="grid grid-2">
                <div class="field">
                    <label for="version">Версия</label>
                    <input class="input" id="version" name="version" type="text" value="{{ old('version', '1.0.0') }}" required>
                    @error('version')
                        <div class="error">{{ $message }}</div>
                    @enderror
                </div>

                <div class="field" style="display: flex; align-items: flex-end;">
                    <label class="pill" style="display: inline-flex; align-items: center; gap: 8px; padding: 10px 14px;">
                        <input type="checkbox" name="make_active" value="1" @checked(old('make_active', true))>
                        Сделать версию активной
                    </label>
                </div>
            </div>

            <button class="btn" type="submit">Загрузить файл</button>
        </form>

        @if ($game->files->isEmpty())
            <div class="muted" style="margin-top: 12px;">Файлы пока не загружены.</div>
        @else
            <div class="grid" style="gap: 10px; margin-top: 16px;">
                @foreach ($game->files as $file)
                    <div class="card" style="display: grid; gap: 10px;">
                        <div style="display: flex; justify-content: space-between; gap: 12px; flex-wrap: wrap; align-items: center;">
                            <div>
                                <div><strong>{{ $file->original_file_name }}</strong></div>
                                <div class="muted">Сохранено как: {{ $file->file_name }}</div>
                            </div>
                            <div style="display: flex; gap: 8px; flex-wrap: wrap;">
                                @if ($file->is_active)
                                    <span class="pill">Активная версия</span>
                                @else
                                    <form method="POST" action="{{ route('seller.games.files.activate', [$game, $file]) }}">
                                        @csrf
                                        <button class="pill" type="submit">Активировать</button>
                                    </form>
                                @endif

                                <form method="POST" action="{{ route('seller.games.files.delete', [$game, $file]) }}">
                                    @csrf
                                    @method('DELETE')
                                    <button class="pill" type="submit" onclick="return confirm('Удалить эту версию файла?')">Удалить</button>
                                </form>
                            </div>
                        </div>

                        <div class="grid grid-2" style="gap: 8px;">
                            <div class="muted">Версия: {{ $file->version }}</div>
                            <div class="muted">Размер: {{ number_format($file->file_size / 1048576, 2) }} MB</div>
                            <div class="muted">MD5: {{ $file->md5_hash ?: '—' }}</div>
                            <div class="muted">Загрузок: {{ $file->download_count }}</div>
                            <div class="muted">Disk: {{ $file->storage_disk }}</div>
                            <div class="muted">Путь: {{ $file->storage_path }}</div>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </section>
</main>
@endsection