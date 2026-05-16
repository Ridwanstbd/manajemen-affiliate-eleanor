<?php

namespace App\Http\Controllers\Affiliator;

use App\Http\Controllers\Controller;
class ChallengeController extends Controller
{
    public function index()
    {
        return view('pages.affiliator.challenge.index');
    }
}
