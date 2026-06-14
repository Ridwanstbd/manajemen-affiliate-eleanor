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
            'affiliate_center_screenshot' => [
                'required',
                'file',
                'mimes:jpg,jpeg,png,webp,gif',
                'max:5120', // max 5MB sebelum kompresi
            ],
        ];
    }

    public function messages()
    {
        return [
            'address.required' => 'Alamat pengiriman wajib diisi.',
            'address.string'   => 'Format alamat pengiriman tidak valid.',
            'address.min'      => 'Alamat pengiriman terlalu pendek. Mohon isi dengan lengkap.',
            'address.max'      => 'Alamat pengiriman terlalu panjang (maksimal 255 karakter).',

            'affiliate_center_screenshot.required' => 'Screenshot affiliate center 7 hari terakhir wajib diunggah.',
            'affiliate_center_screenshot.file'     => 'File screenshot tidak valid.',
            'affiliate_center_screenshot.mimes'    => 'Format screenshot harus JPG, PNG, WEBP, atau GIF.',
            'affiliate_center_screenshot.max'      => 'Ukuran screenshot maksimal 5MB.',
        ];
    }
}