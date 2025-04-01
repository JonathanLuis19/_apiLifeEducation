<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateUserRequest extends FormRequest
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
            'genre_id' => 'nullable|integer|exists:genres,id',
            'name' => 'nullable|string|max:255',
            'user' => 'nullable|string|max:100',
            'fecha_nacimiento' => 'nullable|date',
            'photo_profile' => 'nullable|string|max:255',
            'photo_portada' => 'nullable|string|max:255',
            'phone' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:255|unique:users,email,' . request()->route('id'),
            'password' => 'nullable|string|min:5|confirmed'
        ];
    }
}
