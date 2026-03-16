<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreTransactionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; 
    }

    public function rules(): array
    {
        return [
            'date'       => 'required|date',
            'pocket_id'  => 'required|exists:accounts,id',
            'account_id' => 'required|exists:accounts,id',
            'amount'     => 'required|numeric|min:1',
        ];
    }
}