<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class GameFileSeeder extends Seeder
{
    /**
     * Заполнение таблицы game_files и создание тестовых файлов.
     */
    public function run(): void
    {
        $now = now();
        $games = DB::table('games')->select('id', 'title', 'slug', 'seller_id')->get();

        foreach ($games as $game) {
            $fileName = 'demo_' . ($game->slug ?: ('game-' . $game->id)) . '_v1.zip';
            $path = 'games/' . ($game->slug ?: Str::slug($game->title)) . '/' . $fileName;
            $content = 'Demo build for ' . $game->title;

            Storage::disk('local_private')->put($path, $content);
            $absolutePath = Storage::disk('local_private')->path($path);

            DB::table('game_files')->insert([
                'game_id' => $game->id,
                'uploaded_by' => $game->seller_id,
                'file_name' => $fileName,
                'original_file_name' => $fileName,
                'storage_disk' => 'local_private',
                'storage_path' => $path,
                'file_size' => strlen($content),
                'mime_type' => 'application/zip',
                'version' => '1.0.0',
                'md5_hash' => md5_file($absolutePath),
                'download_count' => 0,
                'is_active' => true,
                'created_at' => $now,
                'updated_at' => $now,
            ]);
        }
    }
}