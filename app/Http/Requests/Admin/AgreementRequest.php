<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class AgreementRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'content'   => 'required|string',
            'is_active' => 'required|boolean',
        ];
    }

    public function messages()
    {
        return [
            'content.required'   => 'Konten persetujuan wajib diisi.',
            'content.string'     => 'Konten persetujuan harus berupa teks.',
            'is_active.required' => 'Status aktif wajib dipilih.',
            'is_active.boolean'  => 'Format status tidak valid.',
        ];
    }
}