<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GameTag extends Model
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
     * Games with this tag.
     */
    public function games()
    {
        return $this->belongsToMany(Game::class, 'game_tag_map', 'tag_id', 'game_id')
            ->withTimestamps();
    }
}
