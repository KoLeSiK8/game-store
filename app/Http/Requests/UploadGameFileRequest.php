<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UploadGameFileRequest extends FormRequest
{
    /**
     * Проверить, что пользователь авторизован.
     */
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    /**
     * Правила валидации для загрузки цифрового файла игры.
     *
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'file' => ['required', 'file', 'max:512000', 'extensions:zip,rar,7z,exe'],
            'version' => ['required', 'string', 'max:50'],
            'make_active' => ['nullable', 'boolean'],
        ];
    }
}