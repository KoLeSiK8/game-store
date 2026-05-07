<?php

namespace App\Services;

use App\Models\GameFile;
use App\Models\User;
use App\Models\UserLibrary;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\StreamedResponse;

class DownloadService
{
    /**
     * Получить доступные файлы игры.
     */
    public function getGameFiles($game)
    {
        return $game->files()->orderByDesc('is_active')->orderByDesc('created_at')->get();
    }

    /**
     * Проверить, можно ли пользователю скачивать файл.
     */
    public function ensureUserCanDownload(GameFile $gameFile, User $user): void
    {
        $hasAccess = UserLibrary::where('user_id', $user->id)
            ->where('game_id', $gameFile->game_id)
            ->exists();

        if (!$hasAccess) {
            abort(403, 'Forbidden');
        }

        if (!$gameFile->is_active) {
            abort(403, 'File version is not active');
        }

        if (!Storage::disk($gameFile->storage_disk)->exists($gameFile->storage_path)) {
            abort(404, 'File not found');
        }
    }

    /**
     * Проверить, нужно ли увеличивать счетчик загрузок.
     */
    public function shouldCountDownload(?string $rangeHeader): bool
    {
        if (!$rangeHeader) {
            return true;
        }

        return preg_match('/bytes=0-/i', $rangeHeader) === 1;
    }

    /**
     * Увеличить счетчик скачиваний файла.
     */
    public function incrementDownloadCount(GameFile $gameFile): void
    {
        $gameFile->increment('download_count');
    }

    /**
     * Подготовить защищенный ответ на скачивание файла.
     */
    public function download(GameFile $gameFile, User $user, ?string $rangeHeader = null): Response|StreamedResponse
    {
        $this->ensureUserCanDownload($gameFile, $user);

        if ($gameFile->storage_disk === 'local_private') {
            return $this->createLocalPrivateResponse($gameFile, $rangeHeader);
        }

        return $this->createDiskStreamResponse($gameFile);
    }

    /**
     * Создать стриминговый ответ для локального защищенного файла с поддержкой resume.
     */
    private function createLocalPrivateResponse(GameFile $gameFile, ?string $rangeHeader = null): StreamedResponse|Response
    {
        $absolutePath = Storage::disk($gameFile->storage_disk)->path($gameFile->storage_path);
        $fileSize = filesize($absolutePath);
        $start = 0;
        $end = max(0, $fileSize - 1);
        $status = 200;

        if ($rangeHeader && preg_match('/bytes=(\d*)-(\d*)/i', $rangeHeader, $matches)) {
            $rangeStart = $matches[1] !== '' ? (int) $matches[1] : null;
            $rangeEnd = $matches[2] !== '' ? (int) $matches[2] : null;

            if ($rangeStart === null && $rangeEnd !== null) {
                $start = max(0, $fileSize - $rangeEnd);
            } elseif ($rangeStart !== null) {
                $start = $rangeStart;
                $end = $rangeEnd !== null ? min($rangeEnd, $end) : $end;
            }

            if ($start > $end || $start >= $fileSize) {
                return response('', 416, [
                    'Content-Range' => 'bytes */' . $fileSize,
                    'Accept-Ranges' => 'bytes',
                ]);
            }

            $status = 206;
        }

        $contentLength = $end - $start + 1;
        $downloadName = $gameFile->original_file_name ?: $gameFile->file_name;
        $headers = [
            'Content-Type' => $gameFile->mime_type ?: 'application/octet-stream',
            'Content-Length' => (string) $contentLength,
            'Accept-Ranges' => 'bytes',
            'Content-Disposition' => $this->buildContentDisposition($downloadName),
            'X-Checksum-MD5' => $gameFile->md5_hash ?? '',
            'Cache-Control' => 'private, no-transform, no-store, must-revalidate',
        ];

        if ($status === 206) {
            $headers['Content-Range'] = sprintf('bytes %d-%d/%d', $start, $end, $fileSize);
        }

        return response()->stream(function () use ($absolutePath, $start, $contentLength) {
            $handle = fopen($absolutePath, 'rb');

            if ($handle === false) {
                return;
            }

            ignore_user_abort(true);
            set_time_limit(0);
            fseek($handle, $start);

            $remaining = $contentLength;
            $chunkSize = 1024 * 1024;

            while (!feof($handle) && $remaining > 0) {
                $readLength = min($chunkSize, $remaining);
                $buffer = fread($handle, $readLength);

                if ($buffer === false) {
                    break;
                }

                echo $buffer;
                flush();

                $remaining -= strlen($buffer);

                if (connection_aborted()) {
                    break;
                }
            }

            fclose($handle);
        }, $status, $headers);
    }

    /**
     * Базовый стриминговый ответ для будущих удаленных дисков, например S3.
     */
    private function createDiskStreamResponse(GameFile $gameFile): StreamedResponse
    {
        $stream = Storage::disk($gameFile->storage_disk)->readStream($gameFile->storage_path);
        $downloadName = $gameFile->original_file_name ?: $gameFile->file_name;

        return response()->stream(function () use ($stream) {
            if (!is_resource($stream)) {
                return;
            }

            while (!feof($stream)) {
                echo fread($stream, 1024 * 1024);
                flush();

                if (connection_aborted()) {
                    break;
                }
            }

            fclose($stream);
        }, 200, [
            'Content-Type' => $gameFile->mime_type ?: 'application/octet-stream',
            'Content-Disposition' => $this->buildContentDisposition($downloadName),
            'X-Checksum-MD5' => $gameFile->md5_hash ?? '',
        ]);
    }

    /**
     * Сформировать безопасный заголовок имени скачиваемого файла.
     */
    private function buildContentDisposition(string $fileName): string
    {
        $asciiFallback = str_replace(['"', "\r", "\n"], '', $fileName);

        return 'attachment; filename="' . $asciiFallback . '"; filename*=UTF-8\'\'' . rawurlencode($fileName);
    }
}