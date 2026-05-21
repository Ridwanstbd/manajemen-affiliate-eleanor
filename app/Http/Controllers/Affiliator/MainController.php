<?php

namespace App\Http\Controllers\Affiliator;

use App\Http\Controllers\Controller;
use App\Models\Agreement;
use App\Services\Affiliator\DashboardService;
use Illuminate\Support\Facades\Auth;

class MainController extends Controller
{
    protected $dashboardService;

    public function __construct(DashboardService $dashboardService)
    {
        $this->dashboardService = $dashboardService;
    }

    public function index()
    {
        $dashboardData = $this->dashboardService->getDashboardStats();

        $isKol = Auth::user()->is_kol;

        if ($isKol) {
            $dashboardData['activeChallenges'] = collect();
        }

        return view('pages.affiliator.dashboard', $dashboardData);
    }
}