<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    use HasFactory;

    /**
     * Mass assignable fields.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'slug',
    ];

    /**
     * Games in this category.
     */
    public function games()
    {
        return $this->belongsToMany(Game::class, 'game_category_map')->withTimestamps();
    }
}
