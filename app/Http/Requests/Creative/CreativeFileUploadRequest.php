<?php

namespace App\Http\Requests\Creative;

use Illuminate\Foundation\Http\FormRequest;

class CreativeFileUploadRequest extends FormRequest
{
    public function authorize(): bool
    {
        // Можно добавить логику авторизации, если требуется
        return true;
    }

    public function rules(): array
    {
        $type = $this->input('type', 'image');
        return [
            'file' => $type === 'video'
                ? 'required|file|mimes:mp4,mov,avi,wmv,flv|max:512000'
                : 'required|file|mimes:jpeg,png,jpg,gif,svg|max:204800',
            'type' => 'nullable|string|in:image,video',
        ];
    }

    public function messages(): array
    {
        return [
            'file.required' => 'Файл не найден в запросе.',
            'file.mimes' => 'Недопустимый тип файла.',
            'file.max' => 'Размер файла превышает допустимый лимит.',
        ];
    }
}
