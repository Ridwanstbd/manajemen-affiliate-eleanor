<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UsernameRequest extends FormRequest
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
     */
    public function rules(): array
    {
        return [
            'username' => 'required|string|max:100'
        ];
    }
    public function messages(): array
    {
        return [
            'username.required' => 'Username wajib diisi untuk melanjutkan.',
            'username.string'   => 'Format username tidak valid.',
            'username.max'      => 'Username tidak boleh lebih dari 100 karakter.',
        ];
    }
}
