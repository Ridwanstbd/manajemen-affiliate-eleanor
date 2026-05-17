<?php

namespace App\Http\Controllers\Affiliator;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\Affiliator\LeaderboardService;

class LeaderboardController extends Controller
{
    public function __construct(
        protected LeaderboardService $leaderboardService
    ) {}

    public function index(Request $request)
    {
        $currentTab = $request->query('tab', 'monthly');

        if ($currentTab === 'challenge') {
            $data = $this->leaderboardService->getChallengeLeaderboard($request->query('challenge_id'));
        } else {
            $data = $this->leaderboardService->getMonthlyLeaderboard($request->query('month'));
        }

        $viewData = array_merge(['currentTab' => $currentTab], $data);

        return view('pages.affiliator.leaderboard.index', $viewData);
    }
}