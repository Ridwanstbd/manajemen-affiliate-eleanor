<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Challenge;
use App\Models\ChallengeWinner;
use App\Models\User;
use App\Services\Admin\ChallengeService;
use Illuminate\Http\Request;

class ChallengeWinnerController extends Controller
{
    protected $challengeService;

    public function __construct(ChallengeService $challengeService)
    {
        $this->challengeService = $challengeService;
    }

    public function manage($id)
    {
        $challenge = Challenge::with(['rewards', 'winners.user'])->findOrFail($id);
        
        $topGmv = $this->challengeService->suggestTopGMV($challenge, 10);
        $topVideos = $this->challengeService->suggestTopVideoCount($challenge, 10);
        
        $affiliators = User::where('role', 'AFFILIATOR')->get();

        return view('pages.admin.winner-challenge.index', compact(
            'challenge', 'topGmv', 'topVideos', 'affiliators'
        ));
    }

    public function store(Request $request, $challengeId)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'category' => 'required|string',
            'reward_given' => 'required|string',
        ]);

        try {
            $exists = ChallengeWinner::where('challenge_id', $challengeId)
                ->where('user_id', $request->user_id)
                ->where('category', $request->category)
                ->exists();

            if ($exists) {
                return redirect()->back()->with('error', 'Kreator ini sudah ditetapkan sebagai pemenang untuk kategori tersebut.');
            }

            $this->challengeService->assignWinner($challengeId, $request->all());
            
            return redirect()->back()->with('success', 'Pemenang berhasil ditetapkan!');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    public function destroy(Request $request, $challengeId, $winnerId)
    {
        try {
            ChallengeWinner::destroy($winnerId);
            return redirect()->back()->with('success', 'Status pemenang berhasil dibatalkan.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Terjadi kesalahan saat menghapus data.');
        }
    }
}