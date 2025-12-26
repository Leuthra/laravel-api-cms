<?php

namespace App\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;

class PostRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'title'   => 'required|string|max:255',
            'content' => 'nullable|string',
            'type'    => 'nullable|string',
            'status'  => 'nullable|in:draft,published,scheduled',
            'payload' => 'nullable|array',
            'seo'     => 'nullable|array',
            'tags'    => 'nullable|array',
            'image'   => 'nullable|image|max:2048',
        ];
    }
}
