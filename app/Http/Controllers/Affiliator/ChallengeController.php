<?php

namespace App\Http\Controllers\Affiliator;

use App\Http\Controllers\Controller;
use App\Services\Affiliator\ChallengeService;

class ChallengeController extends Controller
{
    protected $challengeService;

    public function __construct(ChallengeService $challengeService)
    {
        $this->challengeService = $challengeService;
    }

    public function index()
    {
        $activeChallenges = $this->challengeService->getActiveChallenges();
        $pastChallenges = $this->challengeService->getPastChallenges();

        return view('pages.affiliator.challenge.index', compact('activeChallenges', 'pastChallenges'));
    }

    public function show($id)
    {
        $challenge = $this->challengeService->getChallengeDetails($id);
        
        return view('pages.affiliator.challenge.detail', compact('challenge'));
    }
}