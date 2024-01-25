<?php

namespace App\Http\Requests\TodolistItem;

use Illuminate\Foundation\Http\FormRequest;

class TodolistItemUpdateRequest extends FormRequest
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
            'todolist_id' => 'prohibited',
            'name' => 'sometimes|string|min:2|max:255',
            'description' => 'sometimes|nullable|string|min:2|max:3000',
            'finished' => 'sometimes|boolean'
        ];
    }
}
