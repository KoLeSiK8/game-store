<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ReviewSeeder extends Seeder
{
    /**
     * Заполнение таблицы reviews.
     */
    public function run(): void
    {
        $now = now();

        $userRoleId = DB::table('roles')->where('name', 'user')->value('id');
        $userIds = DB::table('role_user')->where('role_id', $userRoleId)->pluck('user_id')->all();
        $gameIds = DB::table('games')->pluck('id')->all();

        $usedPairs = [];
        $count = 0;

        while ($count < 20) {
            $userId = $userIds[array_rand($userIds)];
            $gameId = $gameIds[array_rand($gameIds)];
            $key = $userId . ':' . $gameId;

            if (isset($usedPairs[$key])) {
                continue;
            }

            $usedPairs[$key] = true;

            DB::table('reviews')->insert([
                'user_id' => $userId,
                'game_id' => $gameId,
                'rating' => rand(3, 5),
                'content' => 'Great game!',
                'status' => 'approved',
                'created_at' => $now,
                'updated_at' => $now,
            ]);

            $count++;
        }
    }
}