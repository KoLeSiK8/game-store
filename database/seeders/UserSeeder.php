<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Заполнение таблиц users и role_user.
     */
    public function run(): void
    {
        $now = now();

        // Получаем id ролей.
        $adminRoleId = DB::table('roles')->where('name', 'admin')->value('id');
        $sellerRoleId = DB::table('roles')->where('name', 'seller')->value('id');
        $userRoleId = DB::table('roles')->where('name', 'user')->value('id');

        // Администратор.
        $adminId = DB::table('users')->insertGetId([
            'name' => 'ADMIN',
            'email' => 'admin@gamestore.local',
            'password' => Hash::make('admin123'),
            'created_at' => $now,
            'updated_at' => $now,
        ]);

        DB::table('role_user')->insert([
            'role_id' => $adminRoleId,
            'user_id' => $adminId,
            'created_at' => $now,
            'updated_at' => $now,
        ]);

        // Продавцы.
        $sellerEmails = ['seller1@test.com', 'seller2@test.com'];
        foreach ($sellerEmails as $index => $email) {
            $sellerId = DB::table('users')->insertGetId([
                'name' => 'Seller ' . ($index + 1),
                'email' => $email,
                'password' => Hash::make('password'),
                'created_at' => $now,
                'updated_at' => $now,
            ]);

            DB::table('role_user')->insert([
                'role_id' => $sellerRoleId,
                'user_id' => $sellerId,
                'created_at' => $now,
                'updated_at' => $now,
            ]);
        }

        // Обычные пользователи.
        for ($i = 1; $i <= 10; $i++) {
            $userId = DB::table('users')->insertGetId([
                'name' => 'User ' . $i,
                'email' => "user{$i}@test.com",
                'password' => Hash::make('password'),
                'created_at' => $now,
                'updated_at' => $now,
            ]);

            DB::table('role_user')->insert([
                'role_id' => $userRoleId,
                'user_id' => $userId,
                'created_at' => $now,
                'updated_at' => $now,
            ]);
        }
    }
}