<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class ImportProductRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'files'   => 'required|array',
            'files.*' => 'required|file|mimes:xlsx,xls,csv',
        ];
    }

    public function messages()
    {
        return [
            'files.required'   => 'File wajib diunggah.',
            'files.array'      => 'Format unggahan tidak valid.',
            'files.*.required' => 'Setiap file wajib diisi.',
            'files.*.file'     => 'Data yang diunggah harus berupa file.',
            'files.*.mimes'    => 'Format file harus berupa Excel (xlsx, xls) atau CSV.',
        ];
    }
}