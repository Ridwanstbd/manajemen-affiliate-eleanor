<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ClaimRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }
    public function rules(): array
    {
        return [
            'email'        => 'required|email|max:100|unique:users,email',
            'phone_number' => ['required', 'regex:/^[0-9]+$/', 'max:14'],
            'password'     => 'required|string|min:8'
        ];
    }

    /**
     * Get the custom messages for validator errors.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            // Pesan error untuk Email
            'email.required' => 'Alamat email wajib diisi.',
            'email.email'    => 'Format email tidak valid (harus mengandung @ dan domain).',
            'email.max'      => 'Alamat email maksimal terdiri dari 100 karakter.',
            'email.unique'   => 'Email ini sudah terdaftar atau diklaim di sistem.',

            // Pesan error untuk Phone Number
            'phone_number.required' => 'Nomor HP/WhatsApp wajib diisi.',
            'phone_number.regex'    => 'Nomor HP hanya boleh berisi angka (tanpa spasi, simbol, atau awalan +62).',
            'phone_number.max'      => 'Nomor HP maksimal terdiri dari 14 digit.',

            // Pesan error untuk Password
            'password.required' => 'Password wajib diisi.',
            'password.string'   => 'Password harus berupa teks.',
            'password.min'      => 'Password minimal harus terdiri dari 8 karakter.'
        ];
    }
}
