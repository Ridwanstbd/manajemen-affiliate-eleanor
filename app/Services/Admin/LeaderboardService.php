<?php

namespace App\Services\Admin;

use App\Models\CreatorMetric;
use App\Models\Challenge;
use App\Models\ImportHistory;
use Carbon\Carbon;

class LeaderboardService
{    
    public function getMonthlyLeaderboard($selectedMonthStr = null)
    {
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
        $selectedMonthLabel = $selectedDate->translatedFormat('F Y');

        $leaders = CreatorMetric::with(['user' => function($q) {
                $q->where('is_kol', false);
            }])
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
                SUM(attributed_orders) as total_orders,
                SUM(video_count) as total_videos,
                SUM(live_count) as total_lives
            ')
            ->groupBy('user_id')
            ->orderByDesc('total_gmv')
            ->limit(10)
            ->get();

        return [
            'leaders'            => $this->formatLeaderboardData($leaders),
            'availableMonths'    => $availableImports,
            'selectedMonthLabel' => $selectedMonthLabel,
        ];
    }

    public function getChallengeLeaderboard($selectedChallengeId = null)
    {
        $availableChallenges = Challenge::orderBy('created_at', 'desc')->get()->map(function ($c) {
            return [
                'value' => $c->id,
                'label' => $c->title
            ];
        });

        if ($selectedChallengeId) {
            $challenge = Challenge::find($selectedChallengeId);
        } else {
            $challenge = Challenge::latest()->first();
        }

        if (!$challenge) {
            return [
                'leaders'             => [],
                'info'                => null,
                'availableChallenges' => $availableChallenges
            ];
        }

        $leaders = CreatorMetric::with(['user' => function($q) {
                $q->where('is_kol', false);
            }])
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
                SUM(affiliate_gmv) as total_gmv, 
                SUM(attributed_orders) as total_orders,
                SUM(video_count) as total_videos,
                SUM(live_count) as total_lives
            ')
            ->groupBy('user_id')
            ->orderByDesc('total_gmv')
            ->limit(10)
            ->get();

        return [
            'leaders'             => $this->formatLeaderboardData($leaders),
            'info'                => $challenge,
            'availableChallenges' => $availableChallenges
        ];
    }

    private function formatLeaderboardData($leaders)
    {
        return $leaders->map(function ($item, $index) {
            return [
                '#' . ($index + 1),
                '@' . ($item->user->username ?? 'Unknown'),
                number_format($item->total_orders ?? 0, 0, ',', '.'),
                ($item->total_videos ?? 0) . ' / ' . ($item->total_lives ?? 0),
                'Rp ' . number_format($item->total_gmv ?? 0, 0, ',', '.')
            ];
        })->toArray();
    }
}