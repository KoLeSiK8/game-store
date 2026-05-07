<?php

namespace App\Http\Controllers;

use App\Models\GameFile;
use App\Services\ActivityLogger;
use App\Services\DownloadService;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\Response;

class DownloadController extends Controller
{
    /**
     * Страница безопасной загрузки с прогрессом и управлением.
     */
    public function show(Request $request, GameFile $gameFile, DownloadService $downloadService): View
    {
        $user = $request->user();

        if (!$user) {
            abort(401, 'Unauthorized');
        }

        $gameFile->loadMissing('game');
        $downloadService->ensureUserCanDownload($gameFile, $user);

        return view('downloads.show', [
            'gameFile' => $gameFile,
        ]);
    }

    /**
     * Защищенное скачивание цифрового файла игры.
     */
    public function download(
        Request $request,
        GameFile $gameFile,
        DownloadService $downloadService,
        ActivityLogger $activityLogger
    ): Response {
        $user = $request->user();

        if (!$user) {
            abort(401, 'Unauthorized');
        }

        $rangeHeader = $request->header('Range');
        $response = $downloadService->download($gameFile->loadMissing('game'), $user, $rangeHeader);

        if ($downloadService->shouldCountDownload($rangeHeader)) {
            $downloadService->incrementDownloadCount($gameFile);
            $activityLogger->logDownload($user, $gameFile->game);
        }

        return $response;
    }
}