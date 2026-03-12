<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ActivitySeeder extends Seeder
{
    /**
     * Заполнение таблицы user_game_activity.
     */
    public function run(): void
    {
        $now = now();

        $userRoleId = DB::table('roles')->where('name', 'user')->value('id');
        $userIds = DB::table('role_user')->where('role_id', $userRoleId)->pluck('user_id')->all();
        $gameIds = DB::table('games')->pluck('id')->all();

        $events = ['view_game', 'search', 'wishlist_add', 'purchase', 'download'];

        for ($i = 0; $i < 100; $i++) {
            $userId = $userIds[array_rand($userIds)];
            $gameId = $gameIds[array_rand($gameIds)];
            $eventType = $events[array_rand($events)];

            DB::table('user_game_activity')->insert([
                'user_id' => $userId,
                'game_id' => $gameId,
                'event_type' => $eventType,
                'metadata' => json_encode(['source' => 'seed']),
                'occurred_at' => $now,
                'created_at' => $now,
                'updated_at' => $now,
            ]);
        }
    }
}