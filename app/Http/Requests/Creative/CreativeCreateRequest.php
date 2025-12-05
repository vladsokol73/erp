<?php

namespace App\Http\Requests\Creative;

use Illuminate\Foundation\Http\FormRequest;

class CreativeCreateRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'country_id' => 'required|integer|exists:countries,id',
            'tags' => 'sometimes|array',
            'tags.*' => 'integer|exists:tags,id',
            'files' => 'required|array|min:1',
            'files.*.code' => 'required|string|max:255',
            'files.*.url' => 'required|url',
            'files.*.type' => 'required|string|in:image,video',
            'files.*.resolution' => 'nullable|string|max:50',
        ];
    }

    public function messages(): array
    {
        return [
            'country_id.required' => 'Creative country is required.',
            'country_id.exists' => 'Selected Country cannot be found.',
            'tags.*.integer' => 'Creative tags must be an integer.',
            'tags.*.exists' => 'Selected Tags cannot be found.',

            'files.required' => 'At least one file is required.',
            'files.array' => 'Files must be an array.',
            'files.*.code.required' => 'Each file must have a code.',
            'files.*.url.required' => 'Each file must have a URL.',
            'files.*.url.url' => 'Each file URL must be a valid URL.',
            'files.*.type.required' => 'Each file must have a type.',
            'files.*.type.in' => 'File type must be one of the allowed types.',
            'files.*.resolution.string' => 'Resolution must be a string.',
        ];
    }
}
