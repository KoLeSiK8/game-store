<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RoleSeeder extends Seeder
{
    /**
     * Заполнение таблицы roles.
     */
    public function run(): void
    {
        $now = now();

        // Базовые роли проекта.
        DB::table('roles')->insert([
            ['name' => 'admin', 'description' => 'Администратор', 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'seller', 'description' => 'Продавец', 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'user', 'description' => 'Пользователь', 'created_at' => $now, 'updated_at' => $now],
        ]);
    }
}