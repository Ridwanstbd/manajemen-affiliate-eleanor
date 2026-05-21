<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class ProductRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'seller_sku' => 'required|string|max:255',
            'name'       => 'required|string|max:255',
            'image'      => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ];
    }

    public function messages()
    {
        return [
            'seller_sku.required'   => 'SKU wajib diisi.',
            'seller_sku.string'     => 'SKU harus berupa teks.',
            'seller_sku.max'        => 'SKU maksimal 255 karakter.',
            'name.required'         => 'Nama produk wajib diisi.',
            'name.string'           => 'Nama produk harus berupa teks.',
            'name.max'              => 'Nama produk maksimal 255 karakter.',
            'image.image'           => 'File harus berupa gambar.',
            'image.mimes'           => 'Format gambar harus jpeg, png, atau jpg.',
            'image.max'             => 'Ukuran gambar maksimal 2MB (2048 KB).',
        ];
    }
}