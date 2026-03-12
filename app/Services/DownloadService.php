<?php

namespace App\Services;

use App\Models\Game;
use App\Models\GameFile;
use App\Models\User;
use App\Models\UserLibrary;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\StreamedResponse;

class DownloadService
{
    /**
     * Получить файлы игры.
     */
    public function getGameFiles(Game $game)
    {
        return $game->files()->orderBy('created_at', 'desc')->get();
    }

    /**
     * Скачать файл игры (проверка доступа по библиотеке).
     */
    public function download(GameFile $gameFile, User $user): StreamedResponse
    {
        $hasAccess = UserLibrary::where('user_id', $user->id)
            ->where('game_id', $gameFile->game_id)
            ->exists();

        if (!$hasAccess) {
            abort(403, 'Forbidden');
        }

        // file_path хранится относительно storage/app
        $path = $gameFile->file_path;

        if (!Storage::disk('local')->exists($path)) {
            abort(404, 'File not found');
        }

        return Storage::disk('local')->download($path, $gameFile->file_name);
    }
}