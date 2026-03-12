<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class TagSeeder extends Seeder
{
    /**
     * Заполнение таблицы game_tags.
     */
    public function run(): void
    {
        $now = now();
        $tags = [
            'Open World',
            'Multiplayer',
            'Singleplayer',
            'Fantasy',
            'Sci-Fi',
            'Horror',
            'Survival',
            'Sandbox',
            'Shooter',
            'Puzzle',
            'Story Rich',
            'Casual',
        ];

        foreach ($tags as $name) {
            DB::table('game_tags')->insert([
                'name' => $name,
                'slug' => Str::slug($name),
                'created_at' => $now,
                'updated_at' => $now,
            ]);
        }
    }
}