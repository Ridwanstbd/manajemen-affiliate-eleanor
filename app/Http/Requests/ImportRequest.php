<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class ImportRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules()
    {
        return [
            'file_core_metrics' => ['required', 'file', 'mimes:xlsx'],
            'file_creator_list' => ['required', 'file', 'mimes:xlsx'],
            'file_live_list'    => ['required', 'file', 'mimes:xlsx'],
            'file_product_list' => ['required', 'file', 'mimes:xlsx'],
            'file_video_list'   => ['required', 'file', 'mimes:xlsx'],
        ];
    }

    public function messages()
    {
        return [
            'required' => 'File :attribute wajib diunggah.',
            'file'     => ':attribute harus berupa file.',
            'mimes'    => 'File :attribute harus berformat Excel (.xlsx).',
        ];
    }

    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            $expectedPrefixes = [
                'file_core_metrics' => 'Transaction_Analysis_Core_Metrics_',
                'file_creator_list' => 'Transaction_Analysis_Creator_List_',
                'file_live_list'    => 'Transaction_Analysis_Live_List_',
                'file_product_list' => 'Transaction_Analysis_Product_List_',
                'file_video_list'   => 'Transaction_Analysis_Video_List_'
            ];

            $extractedDates = [];

            foreach ($expectedPrefixes as $key => $prefix) {
                if ($this->hasFile($key)) {
                    $filename = $this->file($key)->getClientOriginalName();
                    
                    if (!str_starts_with($filename, $prefix)) {
                        $validator->errors()->add($key, "Nama file harus diawali dengan $prefix");
                    }
                    if (preg_match('/_(\d{8}-\d{8})\.xlsx$/', $filename, $matches)) {
                        $extractedDates[] = $matches[1];
                    } else {
                        $validator->errors()->add($key, "Format nama file tidak valid. Pastikan nama file berakhiran tanggal (contoh: _20260301-20260331.xlsx).");
                    }
                }
            }
            if (count($extractedDates) !== 5 || count(array_unique($extractedDates)) !== 1) {
                $validator->errors()->add(
                    'file_mismatch', 
                    'Gagal memproses! Isikan harus berjumlah 5 file dengan rentang tanggal yang sama.'
                );
            }
        });
    }
}