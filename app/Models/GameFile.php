<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GameFile extends Model
{
    use HasFactory;

    /**
     * Массово заполняемые поля.
     *
     * @var list<string>
     */
    protected $fillable = [
        'game_id',
        'file_name',
        'file_path',
        'file_size',
        'version',
        'checksum',
    ];

    /**
     * Игра, к которой относится файл.
     */
    public function game()
    {
        return $this->belongsTo(Game::class);
    }
}