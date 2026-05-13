<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class ChallengeRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        // Set ke true karena otorisasi biasanya sudah ditangani oleh middleware route
        return true; 
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        $rules = [
            'title' => 'required|string|max:255',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'commission_bonus' => 'required|numeric|min:0',
            'rules' => 'required|string',
            'rewards' => 'nullable|array',
            'rewards.*.target_metric' => 'required|string',
            'rewards.*.target_value' => 'required|numeric|min:1',
            'rewards.*.reward_description' => 'required|string|max:255',
        ];

        $bannerRule = [
            'nullable',
            'image',
            'mimes:jpg,jpeg,png,webp',
            'max:200',
            function ($attribute, $value, $fail) {
                if ($value) {
                    $imageSize = getimagesize($value->getRealPath());
                    if ($imageSize) {
                        $width = $imageSize[0];
                        $height = $imageSize[1];
                        
                        if ($width <= $height) {
                            $fail('Gambar banner harus berbentuk persegi panjang (lebar harus lebih besar dari tinggi).');
                        }
                    }
                }
            },
        ];

        $rules['banner_image'] = $bannerRule;

        return $rules;
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'title.required' => 'Judul tantangan wajib diisi.',
            'title.max' => 'Judul tantangan maksimal 255 karakter.',
            'start_date.required' => 'Tanggal mulai wajib diisi.',
            'start_date.date' => 'Format tanggal mulai tidak valid.',
            'end_date.required' => 'Tanggal selesai wajib diisi.',
            'end_date.date' => 'Format tanggal selesai tidak valid.',
            'end_date.after_or_equal' => 'Tanggal selesai tidak boleh kurang dari tanggal mulai.',
            'commission_bonus.required' => 'Bonus komisi wajib diisi (isi 0 jika tidak ada).',
            'commission_bonus.numeric' => 'Bonus komisi harus berupa angka.',
            'rules.required' => 'Syarat dan ketentuan wajib diisi.',
            
            'banner_image.image' => 'File banner harus berupa gambar.',
            'banner_image.mimes' => 'Format banner harus berupa: jpg, jpeg, png, atau webp.',
            'banner_image.max' => 'Ukuran file banner maksimal adalah 200 KB agar cepat dimuat.',
            
            'rewards.*.target_metric.required' => 'Jenis target wajib dipilih.',
            'rewards.*.target_value.required' => 'Nilai target wajib diisi.',
            'rewards.*.target_value.numeric' => 'Nilai target harus berupa angka.',
            'rewards.*.reward_description.required' => 'Deskripsi hadiah wajib diisi.',
        ];
    }
}