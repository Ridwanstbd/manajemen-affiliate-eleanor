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
            
            if (Auth::check()) {
                $user = Auth::user();
                if ($user->role === 'ADMINISTRATOR') {
                    $accessPendingCount = SystemAccessRequest::where('status', 'PENDING')->count();
                    $samplePendingCount = SampleRequest::where('status', 'PENDING')->count();
                    $totalNotificationCount = $accessPendingCount + $samplePendingCount;

                    $dashboardService = app(DashboardService::class);
                    $dashboardData = $dashboardService->getDashboardStats();
                    $pendingTasksList = $dashboardData['pendingTasksList'] ?? collect([]);

                    $view->with([
                        'notificationCount' => $totalNotificationCount,
                        'pendingTasksList' => $pendingTasksList,
                    ]);
                } 

                elseif ($user->role === 'AFFILIATOR') {
                    $notificationCount = 0;
                    $affiliatorNotifications = collect();

                    $shippedSamples = SampleRequest::where('user_id', $user->id)
                        ->where('status', 'SHIPPED')
                        ->latest('updated_at')
                        ->take(3)
                        ->get();

                    foreach ($shippedSamples as $sample) {
                        $affiliatorNotifications->push((object)[
                            'title' => 'Paket Sampel Dikirim 🚚',
                            'desc'  => 'Dikirim via ' . ($sample->courier ?? 'Kurir') . '. Resi: ' . ($sample->tracking_number ?? '-'),
                            'time'  => $sample->updated_at->diffForHumans(),
                            'route' => '#', // Ganti dengan route ke halaman riwayat sampel affiliator
                            'color' => 'var(--primary-blue)'
                        ]);
                        $notificationCount++;
                    }

                    $approvedSamples = SampleRequest::where('user_id', $user->id)
                        ->where('status', 'APPROVED')
                        ->latest('updated_at')
                        ->take(2)
                        ->get();

                    foreach ($approvedSamples as $sample) {
                        $affiliatorNotifications->push((object)[
                            'title' => 'Pengajuan Sampel Disetujui ✅',
                            'desc'  => 'Admin sedang memproses logistik pengiriman paket Anda.',
                            'time'  => $sample->updated_at->diffForHumans(),
                            'route' => '#', 
                            'color' => 'var(--emerald)'
                        ]);
                        $notificationCount++;
                    }

                    $rejectedSamples = SampleRequest::where('user_id', $user->id)
                        ->where('status', 'REJECTED')
                        ->latest('updated_at')
                        ->take(2)
                        ->get();

                    foreach ($rejectedSamples as $sample) {
                        $affiliatorNotifications->push((object)[
                            'title' => 'Pengajuan Sampel Dibatalkan ❌',
                            'desc'  => 'Alasan: ' . ($sample->reject_reason ?? 'Tidak memenuhi syarat.'),
                            'time'  => $sample->updated_at->diffForHumans(),
                            'route' => '#', // Ganti dengan route ke halaman riwayat sampel affiliator
                            'color' => 'var(--rose)'
                        ]);
                        $notificationCount++;
                    }

                    $sortedNotifications = $affiliatorNotifications->sortByDesc('time')->values();

                    $view->with([
                        'notificationCount' => $notificationCount,
                        'affiliatorNotifications' => $sortedNotifications,
                    ]);
                }
            } else {
                $view->with([
                    'notificationCount' => 0,
                    'pendingTasksList' => collect([]),
                    'affiliatorNotifications' => collect([]),
                ]);
            }
        });
    }
}