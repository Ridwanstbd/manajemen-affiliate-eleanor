<?php
namespace App\Services\Affiliator;

use App\Models\Challenge;
use Carbon\Carbon;

class ChallengeService
{
    public function getActiveChallenges()
    {
        $now = Carbon::now();
        
        return Challenge::where('is_active', true)
            ->whereDate('start_date', '<=', $now)
            ->whereDate('end_date', '>=', $now)
            ->orderBy('end_date', 'asc')
            ->get();
    }

    public function getPastChallenges()
    {
        $now = Carbon::now();
        
        return Challenge::where('is_active', true)
            ->whereDate('end_date', '<', $now)
            ->orderBy('end_date', 'desc')
            ->get();
    }

    public function getChallengeDetails($id)
    {
        return Challenge::with(['rewards', 'winners.user'])->findOrFail($id);
    }
}