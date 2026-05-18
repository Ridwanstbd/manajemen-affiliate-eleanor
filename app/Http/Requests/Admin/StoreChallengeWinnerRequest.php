<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class StoreChallengeWinnerRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'user_id'      => 'required|exists:users,id',
            'category'     => 'required|string',
            'reward_given' => 'required|string',
        ];
    }

    public function messages()
    {
        return [
            'user_id.required'      => 'Kreator/User wajib dipilih.',
            'user_id.exists'        => 'Kreator tidak ditemukan dalam sistem.',
            'category.required'     => 'Kategori pemenang wajib diisi.',
            'category.string'       => 'Format kategori tidak valid.',
            'reward_given.required' => 'Hadiah yang diberikan wajib diisi.',
            'reward_given.string'   => 'Format hadiah tidak valid.',
        ];
    }
}