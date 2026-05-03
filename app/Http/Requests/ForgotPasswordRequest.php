<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ForgotPasswordRequest extends FormRequest
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
            'email'    => 'required|email|max:100',
            'username' => 'required|string|max:100',
        ];
    }

    public function messages(): array
    {
        return [
            'email.required'    => 'Alamat email wajib diisi.',
            'email.email'       => 'Pastikan format alamat email sudah benar (contoh: nama@domain.com).',
            'email.max'         => 'Alamat email maksimal 100 karakter.',
            'username.required' => 'Username wajib disertakan untuk verifikasi.',
            'username.string'   => 'Format username tidak valid.',
            'username.max'      => 'Username tidak boleh lebih dari 100 karakter.',
        ];
    }
}
