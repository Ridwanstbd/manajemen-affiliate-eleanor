<?php

namespace App\Services\Affiliator;

use App\Models\KOLContract;
use Illuminate\Support\Facades\Auth;

class ContractService
{
    public function getContracts()
    {
        return KOLContract::where('user_id', Auth::id())
            ->with(['agreement'])
            ->latest()
            ->get();
    }

    public function getContractDetail($id)
    {
        return KOLContract::where('user_id', Auth::id())
            ->with(['agreement', 'products'])
            ->findOrFail($id);
    }
}