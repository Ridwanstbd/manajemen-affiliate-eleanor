<?php

namespace App\Services\Affiliator;

use App\Models\CreatorMetric;
use App\Models\Challenge;
use App\Models\ImportHistory;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class LeaderboardService
{
    public function getMonthlyLeaderboard($selectedMonthStr = null)
    {
        $user = Auth::user();

        $availableImports = ImportHistory::select('start_date')
            ->orderBy('start_date', 'desc')
            ->get()
            ->map(function ($import) {
                $date = Carbon::parse($import->start_date);
                return [
                    'value' => $date->format('Y-m'),
                    'label' => $date->translatedFormat('F Y') 
                ];
            })
            ->unique('value') 
            ->values();

        if ($selectedMonthStr) {
            try {
                $selectedDate = Carbon::createFromFormat('Y-m', $selectedMonthStr);
            } catch (\Exception $e) {
                $selectedDate = Carbon::now();
            }
        } else {
            if ($availableImports->isNotEmpty()) {
                $selectedDate = Carbon::createFromFormat('Y-m', $availableImports->first()['value']);
            } else {
                $selectedDate = Carbon::now();
            }
        }

        $month = $selectedDate->month;
        $year = $selectedDate->year;

        $leadersQuery = CreatorMetric::with('user')
            ->whereHas('user', function($q) {
                $q->where('is_kol', false);
            })
            ->whereHas('importHistory', function($q) use ($month, $year) {
                $q->whereMonth('start_date', $month)
                  ->whereYear('start_date', $year);
            })
            ->selectRaw('
                user_id, 
                SUM(affiliate_gmv) as total_gmv, 
                SUM(items_sold) as total_items_sold
            ')
            ->groupBy('user_id')
            ->orderByDesc('total_gmv')
            ->get();

        $rankedLeaders = $leadersQuery->map(function ($item, $index) {
            $item->rank = $index + 1;
            $item->formatted_gmv = $this->formatToJuta($item->total_gmv);
            return $item;
        });

        $currentUserStat = $rankedLeaders->firstWhere('user_id', $user->id);

        return [
            'topLeaders' => $rankedLeaders->take(10), 
            'currentUser' => $currentUserStat,
            'availableMonths' => $availableImports,
            'selectedMonth' => $selectedDate->format('Y-m'),
        ];
    }

    public function getChallengeLeaderboard($selectedChallengeId = null)
    {
        $user = Auth::user();

        $availableChallenges = Challenge::orderBy('created_at', 'desc')->get()->map(function ($c) {
            return [
                'value' => $c->id,
                'label' => $c->title
            ];
        });

        $challenge = $selectedChallengeId 
            ? Challenge::with('rewards')->find($selectedChallengeId) 
            : Challenge::with('rewards')->latest()->first();

        if (!$challenge) {
            return [
                'topLeaders' => collect([]),
                'currentUser' => null,
                'challenge' => null,
                'availableChallenges' => $availableChallenges,
                'selectedChallengeId' => null,
            ];
        }

        $leadersQuery = CreatorMetric::with('user')
            ->whereHas('user', function($q) {
                $q->where('is_kol', false);
            })
            ->whereHas('importHistory', function($q) use ($challenge) {
                if (isset($challenge->start_date) && isset($challenge->end_date)) {
                    $q->whereDate('start_date', '>=', $challenge->start_date)
                      ->whereDate('end_date', '<=', $challenge->end_date);
                }
            })
            ->selectRaw('
                user_id, 
                SUM(video_count) as total_videos
            ')
            ->groupBy('user_id')
            ->orderByDesc('total_videos')
            ->get();

        $rankedLeaders = $leadersQuery->map(function ($item, $index) {
            $item->rank = $index + 1;
            return $item;
        });

        $currentUserStat = $rankedLeaders->firstWhere('user_id', $user->id);

        return [
            'topLeaders' => $rankedLeaders->take(10),
            'currentUser' => $currentUserStat,
            'challenge' => $challenge,
            'availableChallenges' => $availableChallenges,
            'selectedChallengeId' => $challenge->id,
        ];
    }

    private function formatToJuta($amount)
    {
        if ($amount >= 1000000) {
            return 'Rp ' . number_format($amount / 1000000, 2, ',', '.') . ' Jt';
        }
        return 'Rp ' . number_format($amount, 0, ',', '.');
    }
}