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
            'tiktok_video_link' => 'required|url|max:5000',
            'product'          => 'required',
        ];
    }

    public function messages()
    {
        return [
            'tiktok_video_link.required' => 'Link video wajib diisi!',
            'tiktok_video_link.url'      => 'Format link tidak valid.',
            'tiktok_video_link.max'      => 'Link video terlalu panjang (maksimal 2500 karakter).',
            'product.required'          => 'Pilih minimal satu produk yang ditautkan pada video Anda.',
        ];
    }
}