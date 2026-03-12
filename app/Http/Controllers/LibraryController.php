<?php

namespace App\Http\Controllers;

use App\Models\GameFile;
use App\Services\ActivityLogger;
use App\Services\DownloadService;
use App\Services\LibraryService;
use Illuminate\Http\Request;

class LibraryController extends Controller
{
    /**
     * Библиотека пользователя.
     */
    public function index(Request $request, LibraryService $libraryService)
    {
        $user = $request->user();

        if (!$user) {
            abort(401, 'Unauthorized');
        }

        return view('library.index', [
            'library' => $libraryService->getUserLibrary($user),
        ]);
    }

    /**
     * Скачивание игры (демо-заглушка).
     */
    public function downloadGame(Request $request, GameFile $gameFile, DownloadService $downloadService, ActivityLogger $activityLogger)
    {
        $user = $request->user();

        if (!$user) {
            abort(401, 'Unauthorized');
        }

        $response = $downloadService->download($gameFile, $user);

        $activityLogger->logDownload($user, $gameFile->game);

        return $response;
    }
}
