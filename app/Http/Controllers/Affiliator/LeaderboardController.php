<?php

namespace App\Http\Controllers\Affiliator;

use App\Http\Controllers\Controller;

class LeaderboardController extends Controller
{
    public function index()
    {
        return view('pages.affiliator.leaderboard.index');
    }
}
