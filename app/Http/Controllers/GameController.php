<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Game;
use App\Models\GameTag;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class GameController extends Controller
{
    /**
     * Форма создания игры.
     */
    public function create()
    {
        return view('games.create', [
            'categories' => Category::orderBy('name')->get(),
            'tags' => GameTag::orderBy('name')->get(),
        ]);
    }

    /**
     * Сохранение новой игры.
     */
    public function store(Request $request)
    {
        $seller = $request->user();

        if (!$seller) {
            abort(401, 'Unauthorized');
        }

        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'price' => ['required', 'numeric', 'min:0'],
            'currency' => ['required', 'string', 'size:3'],
            'category_ids' => ['required', 'array', 'min:1'],
            'category_ids.*' => ['integer', 'exists:categories,id'],
            'tag_ids' => ['nullable', 'array'],
            'tag_ids.*' => ['integer', 'exists:game_tags,id'],
        ]);

        $game = Game::create([
            'seller_id' => $seller->id,
            'title' => $validated['title'],
            'slug' => Str::slug($validated['title']) . '-' . Str::random(6),
            'description' => $validated['description'] ?? null,
            'price' => $validated['price'],
            'currency' => strtoupper($validated['currency']),
            'status' => 'pending',
        ]);

        // Привязываем категории и теги.
        $game->categories()->sync($validated['category_ids']);
        if (!empty($validated['tag_ids'])) {
            $game->tags()->sync($validated['tag_ids']);
        }

        return redirect('/profile')->with('status', 'Игра создана и отправлена на модерацию.');
    }

    /**
     * Форма редактирования игры.
     */
    public function edit(Request $request, Game $game)
    {
        $this->authorizeSeller($request, $game);

        return view('games.edit', [
            'game' => $game->load('files'),
            'categories' => Category::orderBy('name')->get(),
            'tags' => GameTag::orderBy('name')->get(),
        ]);
    }

    /**
     * Обновление игры.
     */
    public function update(Request $request, Game $game)
    {
        $this->authorizeSeller($request, $game);

        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'price' => ['required', 'numeric', 'min:0'],
            'currency' => ['required', 'string', 'size:3'],
            'category_ids' => ['required', 'array', 'min:1'],
            'category_ids.*' => ['integer', 'exists:categories,id'],
            'tag_ids' => ['nullable', 'array'],
            'tag_ids.*' => ['integer', 'exists:game_tags,id'],
        ]);

        $game->update([
            'title' => $validated['title'],
            'slug' => Str::slug($validated['title']) . '-' . $game->id,
            'description' => $validated['description'] ?? null,
            'price' => $validated['price'],
            'currency' => strtoupper($validated['currency']),
            'status' => 'pending',
        ]);

        $game->categories()->sync($validated['category_ids']);
        $game->tags()->sync($validated['tag_ids'] ?? []);

        return redirect('/profile')->with('status', 'Игра обновлена и отправлена на модерацию.');
    }

    /**
     * Загрузка файла игры продавцом.
     */
    public function uploadFile(Request $request, Game $game)
    {
        $this->authorizeSeller($request, $game);

        $validated = $request->validate([
            'file' => ['required', 'file', 'max:102400'],
            'version' => ['nullable', 'string', 'max:50'],
        ]);

        $file = $validated['file'];
        $baseName = Str::slug($game->title) . '-' . now()->format('YmdHis');
        $extension = $file->getClientOriginalExtension();
        $fileName = $baseName . ($extension ? ('.' . $extension) : '');

        $path = $file->storeAs('games', $fileName, 'local');

        $game->files()->create([
            'file_name' => $file->getClientOriginalName(),
            'file_path' => $path,
            'file_size' => $file->getSize(),
            'version' => $validated['version'] ?? null,
            'checksum' => hash_file('sha256', $file->getRealPath()),
        ]);

        return back()->with('status', 'Файл загружен.');
    }

    /**
     * Удаление игры.
     */
    public function delete(Request $request, Game $game)
    {
        $this->authorizeSeller($request, $game);

        $game->delete();

        return redirect('/profile')->with('status', 'Игра удалена.');
    }

    /**
     * Проверить, что игра принадлежит продавцу.
     */
    private function authorizeSeller(Request $request, Game $game): void
    {
        $seller = $request->user();

        if (!$seller) {
            abort(401, 'Unauthorized');
        }

        if ((int) $game->seller_id !== (int) $seller->id) {
            abort(403, 'Forbidden');
        }
    }
}