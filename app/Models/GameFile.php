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
        'uploaded_by',
        'file_name',
        'original_file_name',
        'storage_disk',
        'storage_path',
        'file_size',
        'mime_type',
        'version',
        'md5_hash',
        'download_count',
        'is_active',
    ];

    /**
     * Приведение типов атрибутов.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'file_size' => 'integer',
        'download_count' => 'integer',
        'is_active' => 'boolean',
    ];

    /**
     * Игра, к которой относится файл.
     */
    public function game()
    {
        return $this->belongsTo(Game::class);
    }

    /**
     * Продавец, загрузивший файл.
     */
    public function uploader()
    {
        return $this->belongsTo(User::class, 'uploaded_by');
    }
}