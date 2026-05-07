<?php

namespace App\Services;

use App\Models\Game;
use App\Models\GameFile;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class GameFileService
{
    /**
     * Диск хранения цифровых файлов по умолчанию.
     */
    private const DEFAULT_DISK = 'local_private';

    /**
     * Загрузить файл игры в защищенное хранилище и создать запись в БД.
     */
    public function uploadGameFile(
        Game $game,
        User $seller,
        UploadedFile $uploadedFile,
        string $version,
        bool $makeActive = true
    ): GameFile {
        return DB::transaction(function () use ($game, $seller, $uploadedFile, $version, $makeActive) {
            $disk = self::DEFAULT_DISK;
            $safeGameDirectory = 'games/' . ($game->slug ?: Str::slug($game->title));
            $extension = strtolower($uploadedFile->getClientOriginalExtension());
            $storedFileName = Str::slug($game->title) . '_v' . str_replace('.', '_', $version) . '_' . now()->format('YmdHis');

            if ($extension !== '') {
                $storedFileName .= '.' . $extension;
            }

            $storagePath = Storage::disk($disk)->putFileAs($safeGameDirectory, $uploadedFile, $storedFileName);
            $absolutePath = Storage::disk($disk)->path($storagePath);
            $shouldActivate = $makeActive || !$game->files()->where('is_active', true)->exists();

            $gameFile = GameFile::create([
                'game_id' => $game->id,
                'uploaded_by' => $seller->id,
                'file_name' => $storedFileName,
                'original_file_name' => $uploadedFile->getClientOriginalName(),
                'storage_disk' => $disk,
                'storage_path' => $storagePath,
                'file_size' => $uploadedFile->getSize(),
                'mime_type' => $uploadedFile->getClientMimeType() ?: 'application/octet-stream',
                'version' => $version,
                'md5_hash' => $this->calculateMd5($absolutePath),
                'download_count' => 0,
                'is_active' => false,
            ]);

            if ($shouldActivate) {
                $this->activateVersion($gameFile);
            }

            return $gameFile->fresh();
        });
    }

    /**
     * Вычислить MD5-хэш файла для проверки целостности.
     */
    public function calculateMd5(string $absolutePath): string
    {
        return md5_file($absolutePath) ?: '';
    }

    /**
     * Сделать выбранную версию активной и деактивировать остальные.
     */
    public function activateVersion(GameFile $gameFile): GameFile
    {
        DB::transaction(function () use ($gameFile) {
            GameFile::where('game_id', $gameFile->game_id)->update(['is_active' => false]);

            $gameFile->update([
                'is_active' => true,
            ]);
        });

        return $gameFile->fresh();
    }

    /**
     * Удалить старую версию файла из хранилища и БД.
     */
    public function deleteGameFile(GameFile $gameFile): void
    {
        DB::transaction(function () use ($gameFile) {
            $wasActive = (bool) $gameFile->is_active;
            $gameId = $gameFile->game_id;

            if (Storage::disk($gameFile->storage_disk)->exists($gameFile->storage_path)) {
                Storage::disk($gameFile->storage_disk)->delete($gameFile->storage_path);
            }

            $gameFile->delete();

            if ($wasActive) {
                $latestVersion = GameFile::where('game_id', $gameId)
                    ->orderByDesc('created_at')
                    ->first();

                if ($latestVersion) {
                    $this->activateVersion($latestVersion);
                }
            }
        });
    }
}