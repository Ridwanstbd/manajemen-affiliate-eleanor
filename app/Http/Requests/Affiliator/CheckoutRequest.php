<?php

namespace App\Http\Requests\Affiliator;

use Illuminate\Foundation\Http\FormRequest;

class CheckoutRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'address' => 'required|string|min:10|max:255',
        ];
    }

    public function messages()
    {
        return [
            'address.required' => 'Alamat pengiriman wajib diisi.',
            'address.string'   => 'Format alamat pengiriman tidak valid.',
            'address.min'      => 'Alamat pengiriman terlalu pendek. Mohon isi dengan lengkap.',
            'address.max'      => 'Alamat pengiriman terlalu panjang (maksimal 255 karakter).',
        ];
    }
}