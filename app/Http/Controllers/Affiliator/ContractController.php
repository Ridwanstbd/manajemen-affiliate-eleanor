<?php

namespace App\Http\Controllers\Affiliator;

use App\Http\Controllers\Controller;

class ContractController extends Controller
{
    public function index()
    {
        return view('pages.affiliator.contract.index');
    }
}
