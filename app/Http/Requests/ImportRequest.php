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
        $regexPattern = '/^.*_\d{8}-\d{8}\.xlsx$/';

        return [
            'file_core_metrics' => ['required', 'file', 'mimes:xlsx', "regex:$regexPattern"],
            'file_creator_list' => ['required', 'file', 'mimes:xlsx', "regex:$regexPattern"],
            'file_live_list'    => ['required', 'file', 'mimes:xlsx', "regex:$regexPattern"],
            'file_product_list' => ['required', 'file', 'mimes:xlsx', "regex:$regexPattern"],
            'file_video_list'   => ['required', 'file', 'mimes:xlsx', "regex:$regexPattern"],
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
                    }
                }
            }

            if (count($extractedDates) > 1) {
                $uniqueDates = array_unique($extractedDates);
                
                if (count($uniqueDates) > 1) {
                    $validator->errors()->add('file_mismatch', 'Gagal memproses! Rentang tanggal pada kelima file Excel tidak sinkron. Pastikan Anda mengunggah 5 file dari periode waktu yang sama persis.');
                }
            }
        });
    }
}