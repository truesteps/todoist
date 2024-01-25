<?php

namespace App\Http\Requests\Todolist;

use Illuminate\Foundation\Http\FormRequest;

class TodolistCreateRequest extends FormRequest
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
            'name' => 'required|string|min:2|max:255|unique:todolists,name',
            'description' => 'sometimes|nullable|string|min:2|max:3000'
        ];
    }
}
