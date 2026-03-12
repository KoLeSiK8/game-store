<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class GameFileSeeder extends Seeder
{
    /**
     * Заполнение таблицы game_files и создание тестовых файлов.
     */
    public function run(): void
    {
        $now = now();
        $games = DB::table('games')->select('id', 'title')->get();

        foreach ($games as $game) {
            $fileName = 'game_' . $game->id . '_v1.txt';
            $path = 'games/' . $fileName;
            $content = "Demo file for {$game->title}";

            // Создаем тестовый файл в storage/app/games.
            Storage::disk('local')->put($path, $content);

            DB::table('game_files')->insert([
                'game_id' => $game->id,
                'file_name' => $fileName,
                'file_path' => $path,
                'file_size' => strlen($content),
                'version' => '1.0',
                'checksum' => hash('sha256', $content),
                'created_at' => $now,
                'updated_at' => $now,
            ]);
        }
    }
}