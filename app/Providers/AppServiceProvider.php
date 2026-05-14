<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Auth;
use App\Models\SystemAccessRequest;
use App\Models\SampleRequest;
use App\Services\Admin\DashboardService;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        View::composer([
            'layouts.app', 
            'components.organisms.header', 
            'components.organisms.offcanvas'
        ], function ($view) {
            
            if (Auth::check() && Auth::user()->role === 'ADMINISTRATOR') {
                
                $accessPendingCount = SystemAccessRequest::where('status', 'PENDING')->count();
                $samplePendingCount = SampleRequest::where('status', 'PENDING')->count();
                $totalNotificationCount = $accessPendingCount + $samplePendingCount;

                $dashboardService = app(DashboardService::class);
                $dashboardData = $dashboardService->getDashboardStats();
                $pendingTasksList = $dashboardData['pendingTasksList'] ?? [];

                $view->with([
                    'notificationCount' => $totalNotificationCount,
                    'pendingTasksList' => $pendingTasksList,
                ]);
            } else {
                $view->with([
                    'notificationCount' => 0,
                    'pendingTasksList' => collect([]),
                ]);
            }
        });
    }
}