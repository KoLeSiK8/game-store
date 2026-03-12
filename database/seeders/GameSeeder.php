<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class GameSeeder extends Seeder
{
    /**
     * Заполнение таблиц games и связей с категориями/тегами.
     */
    public function run(): void
    {
        $now = now();

        $sellerRoleId = DB::table('roles')->where('name', 'seller')->value('id');
        $sellerIds = DB::table('role_user')->where('role_id', $sellerRoleId)->pluck('user_id')->all();
        $categoryIds = DB::table('categories')->pluck('id')->all();
        $tagIds = DB::table('game_tags')->pluck('id')->all();

        for ($i = 1; $i <= 30; $i++) {
            $sellerId = $sellerIds[array_rand($sellerIds)];

            $gameId = DB::table('games')->insertGetId([
                'seller_id' => $sellerId,
                'title' => "Test Game {$i}",
                'slug' => "test-game-{$i}",
                'description' => "Description for game {$i}",
                'price' => rand(10, 60),
                'currency' => 'USD',
                'status' => 'approved',
                'created_at' => $now,
                'updated_at' => $now,
            ]);

            // Назначаем 1-2 категории.
            $categoryCount = rand(1, 2);
            $categorySample = array_rand(array_flip($categoryIds), $categoryCount);
            $categorySample = is_array($categorySample) ? $categorySample : [$categorySample];

            foreach ($categorySample as $categoryId) {
                DB::table('game_category_map')->insert([
                    'game_id' => $gameId,
                    'category_id' => $categoryId,
                    'created_at' => $now,
                    'updated_at' => $now,
                ]);
            }

            // Назначаем 2-4 тега.
            $tagCount = rand(2, 4);
            $tagSample = array_rand(array_flip($tagIds), $tagCount);
            $tagSample = is_array($tagSample) ? $tagSample : [$tagSample];

            foreach ($tagSample as $tagId) {
                DB::table('game_tag_map')->insert([
                    'game_id' => $gameId,
                    'tag_id' => $tagId,
                    'created_at' => $now,
                    'updated_at' => $now,
                ]);
            }
        }
    }
}