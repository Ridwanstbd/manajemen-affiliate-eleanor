<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\Admin\ChallengeService;
use Illuminate\Http\Request;

class ChallengeWinnerController extends Controller
{
    protected $challengeService;

    public function __construct(ChallengeService $challengeService)
    {
        $this->challengeService = $challengeService;
    }
    
    public function manage(Request $request)
    {}
    public function store(Request $request)
    {}
    public function destroy(Request $request)
    {}
}
