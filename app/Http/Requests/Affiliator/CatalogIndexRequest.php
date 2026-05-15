<?php

namespace App\Http\Requests\Affiliator;

use Illuminate\Foundation\Http\FormRequest;

class CatalogIndexRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; 
    }

    public function rules(): array
    {
        return [
            'search' => ['nullable', 'string', 'max:255'],
            'sort'   => ['nullable', 'string', 'in:newest,price_low,price_high'],
            'page'   => ['nullable', 'integer', 'min:1'],
        ];
    }
}