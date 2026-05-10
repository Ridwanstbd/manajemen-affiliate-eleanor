<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\Admin\LeaderboardService;

class LeaderboardController extends Controller
{
    protected $leaderboardService;

    public function __construct(LeaderboardService $leaderboardService)
    {
        $this->leaderboardService = $leaderboardService;
    }

    public function index(Request $request)
    {
        $currentTab = $request->query('tab', 'monthly');
        
        if ($currentTab === 'challenge') {
            $selectedChallenge = $request->query('selected_challenge');
            $result = $this->leaderboardService->getChallengeLeaderboard($selectedChallenge);
            
            return view('pages.admin.leaderboard.index', [
                'currentTab'          => $currentTab,
                'leadersData'         => $result['leaders'],
                'challenge'           => $result['info'],
                'availableChallenges' => $result['availableChallenges'],
            ]);
        } else {
            $selectedMonth = $request->query('selected_month');
            $result = $this->leaderboardService->getMonthlyLeaderboard($selectedMonth);
            
            return view('pages.admin.leaderboard.index', [
                'currentTab'         => $currentTab,
                'leadersData'        => $result['leaders'],
                'availableMonths'    => $result['availableMonths'],
                'selectedMonthLabel' => $result['selectedMonthLabel'],
            ]);
        }
    }
}