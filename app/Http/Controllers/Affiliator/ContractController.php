<?php

namespace App\Http\Controllers\Affiliator;

use App\Http\Controllers\Controller;
use App\Models\KOLContract;

class ContractController extends Controller
{
    public function index()
    {
        $contracts = KOLContract::where('user_id', auth()->id())
            ->with('products')
            ->orderByDesc('created_at')
            ->get();

        return view('pages.affiliator.contract.index', compact('contracts'));
    }

    public function show($id)
    {
        $contract = KOLContract::where('user_id', auth()->id())
            ->with('products')
            ->findOrFail($id);

        return view('pages.affiliator.contract.detail', compact('contract'));
    }
}