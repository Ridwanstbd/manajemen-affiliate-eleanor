<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class UserManagementRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        $routeName = $this->route()->getName();

        if (str_contains($routeName, 'store-kol')) {
            return [
                'user_id'              => 'required|exists:users,id',
                'start_date'           => 'required|date',
                'end_date'             => 'required|date|after:start_date',
                'contract_fee'         => 'required|numeric',
                'required_video_count' => 'required|integer',
                'product_ids'          => 'required|array',
                'product_ids.*'        => 'exists:products,id',
                'agreement_file'       => 'required|file|mimes:docx|max:5120',
            ];
        }

        if (str_contains($routeName, 'extend-kol')) {
            return [
                'original_contract_id' => 'required|exists:kol_contracts,id',
                'start_date'           => 'required|date',
                'end_date'             => 'required|date|after:start_date',
                'contract_fee'         => 'required|numeric',
                'required_video_count' => 'required|integer',
            ];
        }

        if (str_contains($routeName, 'approve-access') || str_contains($routeName, 'approveAccess')) {
            return ['id' => 'required|exists:system_access_requests,id'];
        }

        if (str_contains($routeName, 'reject-access') || str_contains($routeName, 'rejectAccess')) {
            return ['id' => 'required|string'];
        }

        if (str_contains($routeName, 'store-blacklist')) {
            return [
                'user_id'          => 'required|exists:users,id',
                'violation_reason' => 'required|string|max:1000',
            ];
        }

        if (str_contains($routeName, 'restore-blacklist')) {
            return ['id' => 'required|exists:users,id'];
        }

        return [];
    }

    public function messages()
    {
        return [
            'user_id.required'              => 'User wajib dipilih.',
            'user_id.exists'                => 'User tidak ditemukan.',
            'start_date.required'           => 'Tanggal mulai wajib diisi.',
            'start_date.date'               => 'Format tanggal mulai tidak valid.',
            'end_date.required'             => 'Tanggal selesai wajib diisi.',
            'end_date.date'                 => 'Format tanggal selesai tidak valid.',
            'end_date.after'                => 'Tanggal selesai harus setelah tanggal mulai.',
            'contract_fee.required'         => 'Biaya kontrak wajib diisi.',
            'contract_fee.numeric'          => 'Biaya kontrak harus berupa angka.',
            'required_video_count.required' => 'Target jumlah video wajib diisi.',
            'required_video_count.integer'  => 'Target video harus berupa bilangan bulat.',
            'product_ids.required'          => 'Minimal pilih 1 produk.',
            'product_ids.array'             => 'Format produk tidak valid.',
            'product_ids.*.exists'          => 'Produk yang dipilih tidak tersedia.',
            'original_contract_id.required' => 'Kontrak asli wajib dipilih.',
            'original_contract_id.exists'   => 'Kontrak tidak ditemukan.',
            'id.required'                   => 'ID wajib disertakan.',
            'id.exists'                     => 'Data tidak ditemukan di sistem.',
            'violation_reason.required'     => 'Alasan pelanggaran wajib diisi.',
            'violation_reason.max'          => 'Alasan maksimal 1000 karakter.',
            'agreement_file.required'       => 'File dokumen perjanjian kontrak wajib diupload.',
            'agreement_file.file'           => 'Upload harus berupa file yang valid.',
            'agreement_file.mimes'          => 'File perjanjian harus berformat .docx.',
            'agreement_file.max'            => 'Ukuran file maksimal 5 MB.',
        ];
    }
}