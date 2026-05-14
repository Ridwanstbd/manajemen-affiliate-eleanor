<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class UpdateTaskDeadlineRequest extends FormRequest
{
    /**
     * Tentukan apakah user diizinkan untuk membuat request ini.
     *
     * @return bool
     */
    public function authorize()
    {
        return true; 
    }

    /**
     * Tentukan aturan (rules) validasi yang berlaku pada request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'task_deadline_days' => 'required|integer|min:1'
        ];
    }

    /**
     * Tentukan pesan error kustom untuk setiap aturan validasi (Opsional tapi direkomendasikan).
     *
     * @return array
     */
    public function messages()
    {
        return [
            'task_deadline_days.required' => 'Batas waktu tugas (dalam hari) wajib diisi.',
            'task_deadline_days.integer'  => 'Batas waktu harus berupa angka yang valid.',
            'task_deadline_days.min'      => 'Batas waktu minimal adalah 1 hari.',
        ];
    }
}