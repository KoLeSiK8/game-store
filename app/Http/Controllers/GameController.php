<?php

namespace App\Http\Controllers;

use App\Http\Requests\UploadGameFileRequest;
use App\Models\Category;
use App\Models\Game;
use App\Models\GameFile;
use App\Models\GameTag;
use App\Services\GameFileService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\View\View;

class GameController extends Controller
{
    /**
     * Форма создания игры.
     */
    public function create(): View
    {
        return view('games.create', [
            'categories' => Category::orderBy('name')->get(),
            'tags' => GameTag::orderBy('name')->get(),
        ]);
    }

    /**
     * Сохранение новой игры.
     */
    public function store(Request $request): RedirectResponse
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

        $game->categories()->sync($validated['category_ids']);
        $game->tags()->sync($validated['tag_ids'] ?? []);

        return redirect()->route('seller.dashboard')->with('status', 'Игра создана и отправлена на модерацию.');
    }

    /**
     * Форма редактирования игры.
     */
    public function edit(Request $request, Game $game): View
    {
        $this->authorizeSeller($request, $game);

        return view('games.edit', [
            'game' => $game->load(['categories', 'tags', 'files.uploader']),
            'categories' => Category::orderBy('name')->get(),
            'tags' => GameTag::orderBy('name')->get(),
        ]);
    }

    /**
     * Обновление игры.
     */
    public function update(Request $request, Game $game): RedirectResponse
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

        return redirect()->route('seller.dashboard')->with('status', 'Игра обновлена и повторно отправлена на модерацию.');
    }

    /**
     * Загрузка новой версии файла игры.
     */
    public function uploadFile(
        UploadGameFileRequest $request,
        Game $game,
        GameFileService $gameFileService
    ): RedirectResponse {
        $this->authorizeSeller($request, $game);

        $gameFileService->uploadGameFile(
            $game,
            $request->user(),
            $request->file('file'),
            $request->string('version')->toString(),
            $request->has('make_active')
        );

        return back()->with('status', 'Файл игры успешно загружен в защищенное хранилище.');
    }

    /**
     * Активировать выбранную версию файла.
     */
    public function activateFile(Request $request, Game $game, GameFile $gameFile, GameFileService $gameFileService): RedirectResponse
    {
        $this->authorizeSeller($request, $game);
        $this->authorizeGameFile($game, $gameFile);

        $gameFileService->activateVersion($gameFile);

        return back()->with('status', 'Версия файла активирована.');
    }

    /**
     * Удалить старую версию файла.
     */
    public function deleteFile(Request $request, Game $game, GameFile $gameFile, GameFileService $gameFileService): RedirectResponse
    {
        $this->authorizeSeller($request, $game);
        $this->authorizeGameFile($game, $gameFile);

        $gameFileService->deleteGameFile($gameFile);

        return back()->with('status', 'Файл игры удален.');
    }

    /**
     * Удаление игры.
     */
    public function delete(Request $request, Game $game): RedirectResponse
    {
        $this->authorizeSeller($request, $game);
        $game->delete();

        return redirect()->route('seller.dashboard')->with('status', 'Игра удалена.');
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

    /**
     * Проверить, что файл принадлежит выбранной игре.
     */
    private function authorizeGameFile(Game $game, GameFile $gameFile): void
    {
        if ((int) $gameFile->game_id !== (int) $game->id) {
            abort(404, 'File not found for this game');
        }
    }
}
