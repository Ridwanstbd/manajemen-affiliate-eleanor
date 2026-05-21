<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class AgreementRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    protected function prepareForValidation()
    {
        $this->merge([
            'is_active' => $this->has('is_active'),
            'is_kol'    => $this->has('is_kol'),
        ]);
    }

    public function rules()
    {
        return [
            'content'   => 'required|string',
            'is_active' => 'boolean',
            'is_kol'    => 'boolean',
        ];
    }

    public function messages()
    {
        return [
            'content.required'   => 'Konten persetujuan wajib diisi.',
            'content.string'     => 'Konten persetujuan harus berupa teks.',
            'is_active.boolean'  => 'Format status aktif tidak valid.',
            'is_kol.boolean'     => 'Format status khusus KOL tidak valid.',
        ];
    }
}