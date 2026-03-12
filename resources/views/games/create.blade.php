@extends('layouts.app')

@section('title', 'Новая игра')

@section('content')
<main class="grid" style="gap: 20px;">
    <section class="card" style="max-width: 800px; margin: 0 auto;">
        <h2 style="margin-top: 0;">Добавить игру</h2>

        @if ($errors->any())
            <div class="card" style="border-color: #fecaca; background: #fef2f2;">
                <div class="error">Проверьте заполнение полей.</div>
            </div>
        @endif

        <form method="POST" action="/seller/games">
            @csrf
            <div class="field">
                <label for="title">Название</label>
                <input class="input" id="title" name="title" type="text" value="{{ old('title') }}" required>
                @error('title')
                    <div class="error">{{ $message }}</div>
                @enderror
            </div>

            <div class="field">
                <label for="description">Описание</label>
                <textarea class="input" id="description" name="description" rows="4">{{ old('description') }}</textarea>
                @error('description')
                    <div class="error">{{ $message }}</div>
                @enderror
            </div>

            <div class="grid grid-2">
                <div class="field">
                    <label for="price">Цена</label>
                    <input class="input" id="price" name="price" type="number" step="0.01" min="0" value="{{ old('price', 19.99) }}" required>
                    @error('price')
                        <div class="error">{{ $message }}</div>
                    @enderror
                </div>

                <div class="field">
                    <label for="currency">Валюта</label>
                    <input class="input" id="currency" name="currency" type="text" maxlength="3" value="{{ old('currency', 'USD') }}" required>
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
                                @checked(in_array($category->id, old('category_ids', [])))>
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
                                @checked(in_array($tag->id, old('tag_ids', [])))>
                            {{ $tag->name }}
                        </label>
                    @endforeach
                </div>
                @error('tag_ids')
                    <div class="error">{{ $message }}</div>
                @enderror
            </div>

            <button class="btn" type="submit">Сохранить</button>
        </form>
    </section>
</main>
@endsection