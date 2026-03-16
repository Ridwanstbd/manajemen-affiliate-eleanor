<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreTransferRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'date'           => 'required|date',
            'from_pocket_id' => 'required|exists:accounts,id|different:to_pocket_id',
            'to_pocket_id'   => 'required|exists:accounts,id',
            'amount'         => 'required|numeric|min:1',
            'description'    => 'nullable|string|max:255',
        ];
    }

    public function messages(): array
    {
        return [
            'from_pocket_id.different' => 'Kantong tujuan tidak boleh sama dengan kantong sumber.',
        ];
    }
}