<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class RegisterRequest extends FormRequest
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
            'tiktok_username' => [
                'required',
                'string',
                'max:100',
                'not_regex:/^@/',   
                'regex:/^\S*$/u',   
            ],
            'email' => [
                'required',
                'string',
                'email',           
                'max:100',      
                'unique:system_access_requests,email', 
            ],
            'phone_number' => [
                'required',
                'string',
                'regex:/^[0-9]+$/',
                'min:9',
                'max:14',    
                'unique:system_access_requests,phone_number',
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'tiktok_username.required'  => 'Username TikTok wajib diisi.',
            'tiktok_username.max'       => 'Username TikTok maksimal 100 karakter.',
            'tiktok_username.not_regex' => 'Username TikTok tidak boleh diawali dengan simbol @.',
            'tiktok_username.regex'     => 'Username TikTok tidak boleh mengandung spasi.',

            'email.required'            => 'Alamat email wajib diisi.',
            'email.email'               => 'Format alamat email tidak valid.',
            'email.max'                 => 'Alamat email maksimal 100 karakter.',
            'email.unique'              => 'Email ini sudah pernah digunakan untuk mendaftar.',

            'phone_number.required'     => 'Nomor HP/WhatsApp wajib diisi.',
            'phone_number.regex'        => 'Nomor HP hanya boleh berisi angka.',
            'phone_number.min'          => 'Nomor HP minimal terdiri dari 9 angka.',
            'phone_number.max'          => 'Nomor HP maksimal terdiri dari 14 angka.',
            'phone_number.unique'       => 'Nomor HP ini sudah pernah digunakan untuk mendaftar.',
        ];
    }
}
