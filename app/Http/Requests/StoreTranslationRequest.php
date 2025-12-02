<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreTranslationRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; 
    }

    public function rules(): array
    {
        return [
            'key' => 'required|string',
            'locale' => 'required|string|exists:languages,code',
            'value' => 'required|string',
            'tags' => 'nullable|array',
            'tags.*' => 'string'
        ];
    }
}
