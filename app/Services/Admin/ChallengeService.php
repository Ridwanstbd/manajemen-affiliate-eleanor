<?php

namespace App\Services\Admin;

use App\Models\Challenge;
use App\Models\ChallengeWinner;
use App\Models\CreatorMetric;
use App\Models\Video;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class ChallengeService
{
    public function create(array $data)
    {
        DB::beginTransaction();
        try {
            if (isset($data['banner_image'])) {
                $data['banner_image_path'] = $data['banner_image']->store('challenges', 'public');
            }

            $challenge = Challenge::create([
                'title' => $data['title'],
                'rules' => $data['rules'],
                'start_date' => $data['start_date'],
                'end_date' => $data['end_date'],
                'commission_bonus' => $data['commission_bonus'],
                'banner_image_path' => $data['banner_image_path'] ?? null,
                'is_active' => $data['is_active'] ?? true,
            ]);

            if (!empty($data['rewards'])) {
                foreach ($data['rewards'] as $reward) {
                    $challenge->rewards()->create($reward);
                }
            }

            DB::commit();
            return $challenge;
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    public function update(int $id, array $data)
    {
        DB::beginTransaction();
        try {
            $challenge = Challenge::findOrFail($id);

            if (isset($data['banner_image'])) {
                if ($challenge->banner_image_path) {
                    Storage::disk('public')->delete($challenge->banner_image_path);
                }
                $data['banner_image_path'] = $data['banner_image']->store('challenges', 'public');
            }

            $challenge->update([
                'title' => $data['title'],
                'rules' => $data['rules'],
                'start_date' => $data['start_date'],
                'end_date' => $data['end_date'],
                'commission_bonus' => $data['commission_bonus'],
                'banner_image_path' => $data['banner_image_path'] ?? $challenge->banner_image_path,
                'is_active' => $data['is_active'] ?? $challenge->is_active,
            ]);

            if (isset($data['rewards'])) {
                $challenge->rewards()->delete();
                foreach ($data['rewards'] as $reward) {
                    $challenge->rewards()->create($reward);
                }
            }

            DB::commit();
            return $challenge;
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    public function delete(int $id)
    {
        $challenge = Challenge::findOrFail($id);
        
        if ($challenge->banner_image_path) {
            Storage::disk('public')->delete($challenge->banner_image_path);
        }

        return $challenge->delete();
    }

    public function assignWinner(int $challengeId, array $data)
    {
        return ChallengeWinner::create([
            'challenge_id' => $challengeId,
            'user_id' => $data['user_id'],
            'category' => $data['category'],
            'reward_given' => $data['reward_given'],
        ]);
    }

    public function suggestTopGMV(Challenge $challenge, $limit = 5)
    {
        return CreatorMetric::select('user_id', DB::raw('SUM(affiliate_gmv) as total_gmv'))
            ->whereHas('importHistory', function ($query) use ($challenge) {
                $query->where('start_date', '>=', $challenge->start_date)
                      ->where('end_date', '<=', $challenge->end_date);
            })
            ->groupBy('user_id')
            ->orderByDesc('total_gmv')
            ->with('user')
            ->limit($limit)
            ->get();
    }

    public function suggestTopVideoCount(Challenge $challenge, $limit = 5)
    {
        return Video::select('user_id', DB::raw('COUNT(id) as total_videos'))
            ->whereBetween('post_date', [$challenge->start_date, $challenge->end_date])
            ->groupBy('user_id')
            ->orderByDesc('total_videos')
            ->with('user')
            ->limit($limit)
            ->get();
    }
}