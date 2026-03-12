<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Запуск всех сидеров проекта.
     */
    public function run(): void
    {
        $this->call([
            RoleSeeder::class,
            UserSeeder::class,
            CategorySeeder::class,
            TagSeeder::class,
            GameSeeder::class,
            GameFileSeeder::class,
            OrderSeeder::class,
            ReviewSeeder::class,
            WishlistSeeder::class,
            ActivitySeeder::class,
        ]);
    }
}