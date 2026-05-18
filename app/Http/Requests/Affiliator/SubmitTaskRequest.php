<?php

namespace App\Http\Requests\Affiliator;

use Illuminate\Foundation\Http\FormRequest;

class SubmitTaskRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'tiktok_video_link' => 'required|url|max:1000',
            'products'          => 'required|array|min:1',
        ];
    }

    public function messages()
    {
        return [
            'tiktok_video_link.required' => 'Link video wajib diisi!',
            'tiktok_video_link.url'      => 'Format link tidak valid.',
            'tiktok_video_link.max'      => 'Link video terlalu panjang (maksimal 1000 karakter).',
            'products.required'          => 'Pilih minimal satu produk yang ditautkan pada video Anda.',
            'products.array'             => 'Format pilihan produk tidak valid.',
            'products.min'               => 'Pilih minimal satu produk yang ditautkan pada video Anda.',
        ];
    }
}