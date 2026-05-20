<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use App\Models\SystemAccessRequest;
use App\Models\SampleRequest;
use App\Models\TaskReport;
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
            'components.organisms.offcanvas',
            'components.organisms.sidebar'
        ], function ($view) {
            
            if (Auth::check()) {
                $user = Auth::user();
                if ($user->role === 'ADMINISTRATOR') {
                    $accessPendingCount = SystemAccessRequest::where('status', 'PENDING')->count();
                    $samplePendingCount = SampleRequest::where('status', 'PENDING')->count();
                    
                    $dbNotifications = $user->notifications()->latest()->take(5)->get();
                    $dbUnreadCount = $user->unreadNotifications()->count();

                    $totalNotificationCount = $accessPendingCount + $samplePendingCount + $dbUnreadCount;

                    if ($dbUnreadCount > 0) {
                        $user->unreadNotifications->markAsRead();
                    }

                    $dashboardService = app(DashboardService::class);
                    $dashboardData = $dashboardService->getDashboardStats();
                    $pendingTasksList = collect($dashboardData['pendingTasksList'] ?? []);

                    $systemNotifs = collect();
                    foreach ($dbNotifications as $notif) {
                        $systemNotifs->push((object)[
                            'title' => $notif->data['title'],
                            'name'  => 'Sistem Otomatis', 
                            'time'  => $notif->created_at->diffForHumans(),
                            'route' => $notif->data['route'] ?? '#',
                        ]);
                    }

                    $productUpdateCount = $user->unreadNotifications()->where('data->type', 'product_updated')->count(); 

                    $view->with([
                        'notificationCount' => $totalNotificationCount,
                        'pendingTasksList' => $systemNotifs->merge($pendingTasksList), 
                        'accessPendingCount' => $accessPendingCount,
                        'samplePendingCount' => $samplePendingCount,
                        'productUpdateCount' => $productUpdateCount,
                    ]);
                } 
                elseif ($user->role === 'AFFILIATOR') {
                    $notificationCount = 0;
                    $affiliatorNotifications = collect();

                    $shippedSamples = SampleRequest::where('user_id', $user->id)
                        ->where('status', 'SHIPPED')
                        ->latest('updated_at')
                        ->take(2)
                        ->get();

                    foreach ($shippedSamples as $sample) {
                        $affiliatorNotifications->push((object)[
                            'title' => 'Paket Sampel Dikirim 🚚',
                            'desc'  => 'Dikirim via ' . ($sample->courier ?? 'Kurir') . '. Resi: ' . ($sample->tracking_number ?? '-'),
                            'time'  => $sample->updated_at->diffForHumans(),
                            'route' => route('affiliator.sample-request.show', $sample->id), 
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
                            'route' => route('affiliator.sample-request.show', $sample->id),
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
                            'desc'  => 'Alasan: ' . ($sample->reject_reason ?? 'Tidak ada keterangan'),
                            'time'  => $sample->updated_at->diffForHumans(),
                            'route' => route('affiliator.sample-request.show', $sample->id),
                        ]);
                        $notificationCount++;
                    }

                    $pendingTasks = TaskReport::where('user_id', $user->id)
                        ->where('task_status', '!=', 'COMPLETED') 
                        ->whereNotNull('due_date')
                        ->get();

                    $now = Carbon::now();

                    foreach ($pendingTasks as $task) {
                        $dueDate = Carbon::parse($task->due_date);
                        
                        if ($dueDate->isPast()) {
                            $affiliatorNotifications->push((object)[
                                'title' => 'Peringatan Tugas Terlambat ⚠️',
                                'desc'  => 'Tugas konten video Anda telah melewati batas waktu pada ' . $dueDate->translatedFormat('d M Y') . '. Segera unggah link.',
                                'time'  => $task->updated_at->diffForHumans(),
                                'route' => route('affiliator.task.report', $task->id), 
                            ]);
                            $notificationCount++;
                        } 
                        elseif ($dueDate->diffInDays($now) <= 3) {
                            $affiliatorNotifications->push((object)[
                                'title' => 'Tenggat Waktu Tugas Mendekat ⏳',
                                'desc'  => 'Ingat, Anda harus mengunggah link video TikTok sebelum ' . $dueDate->translatedFormat('d M Y') . '.',
                                'time'  => $task->updated_at->diffForHumans(),
                                'route' => route('affiliator.task.report', $task->id),
                            ]);
                            $notificationCount++;
                        }
                    }

                    $sortedNotifications = $affiliatorNotifications->sortByDesc(function ($notif) {
                        return strtotime(str_replace(' yang lalu', '', $notif->time));
                    })->values();

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
                    'accessPendingCount' => 0,
                    'samplePendingCount' => 0,
                    'productUpdateCount' => 0,
                ]);
            }
        });
    }
}