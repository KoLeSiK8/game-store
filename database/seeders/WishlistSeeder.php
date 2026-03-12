<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class WishlistSeeder extends Seeder
{
    /**
     * Заполнение таблицы wishlists.
     */
    public function run(): void
    {
        $now = now();

        $userRoleId = DB::table('roles')->where('name', 'user')->value('id');
        $userIds = DB::table('role_user')->where('role_id', $userRoleId)->pluck('user_id')->all();
        $gameIds = DB::table('games')->pluck('id')->all();

        $usedPairs = [];
        $count = 0;

        while ($count < 15) {
            $userId = $userIds[array_rand($userIds)];
            $gameId = $gameIds[array_rand($gameIds)];
            $key = $userId . ':' . $gameId;

            if (isset($usedPairs[$key])) {
                continue;
            }

            $usedPairs[$key] = true;

            DB::table('wishlists')->insert([
                'user_id' => $userId,
                'game_id' => $gameId,
                'created_at' => $now,
                'updated_at' => $now,
            ]);

            $count++;
        }
    }
}