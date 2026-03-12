<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class CategorySeeder extends Seeder
{
    /**
     * Заполнение таблицы categories.
     */
    public function run(): void
    {
        $now = now();
        $categories = ['Action', 'RPG', 'Strategy', 'Simulation', 'Adventure', 'Indie'];

        foreach ($categories as $name) {
            DB::table('categories')->insert([
                'name' => $name,
                'slug' => Str::slug($name),
                'created_at' => $now,
                'updated_at' => $now,
            ]);
        }
    }
}