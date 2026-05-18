<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class SampleRequestStatusRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        if ($this->has('reject_reason')) {
            return [
                'sample_request_id' => 'required|exists:sample_requests,id',
                'reject_reason'     => 'required|string|max:500'
            ];
        }

        return [
            'sample_request_id' => 'required|exists:sample_requests,id',
            'courier'           => 'required|string',
            'tracking_number'   => 'required|string',
            'shipping_cost'     => 'nullable|numeric'
        ];
    }

    public function messages()
    {
        return [
            'sample_request_id.required' => 'ID Pengajuan Sampel wajib diisi.',
            'sample_request_id.exists'   => 'Data pengajuan sampel tidak ditemukan.',
            'courier.required'           => 'Nama kurir wajib diisi.',
            'tracking_number.required'   => 'Nomor resi wajib diisi.',
            'shipping_cost.numeric'      => 'Biaya pengiriman harus berupa angka.',
            'reject_reason.required'     => 'Alasan penolakan wajib diisi.',
            'reject_reason.max'          => 'Alasan penolakan maksimal 500 karakter.',
        ];
    }
}