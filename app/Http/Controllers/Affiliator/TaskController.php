<?php

namespace App\Http\Controllers\Affiliator;

use App\Http\Controllers\Controller;
use App\Http\Requests\Affiliator\SubmitTaskRequest;
use App\Models\TaskReport;
use App\Services\Affiliator\TaskService;
use Illuminate\Http\Request;

class TaskController extends Controller
{
    public function __construct(
        protected TaskService $taskService
    ){}

    public function index(Request $request)
    {
        $tab = $request->query('tab', 'request-sample');
                                     
        $data = $this->taskService->getTabData($tab, $request);
        
        if ($request->ajax() || $request->wantsJson()) {
            $viewPath = $tab === 'completed' 
                ? 'pages.affiliator.task.completed.partials.items' 
                : 'pages.affiliator.task.all.partials.items';

            $html = view($viewPath, compact('data'))->render();
            
            return response()->json([
                'html' => $html,
                'next_page_url' => $data->nextPageUrl()
            ]);
        }

        $viewData = [
            'currentTab' => $tab,
            'data' => $data
        ];
        return view('pages.affiliator.task.index', $viewData);
    }

    public function show($id)
    {
        try {
            $task = $this->taskService->getTaskDetail(auth()->user(), (int)$id);
            
            return view('pages.affiliator.task.detail', compact('task'));
            
        } catch (\Exception $e) {
            return redirect()->route('affiliator.task.index')
                ->with('error', 'Tugas tidak ditemukan atau Anda tidak memiliki hak akses.');
        }
    }

    private function extractTikTokVideoId(string $url): array
    {
        if (preg_match('/tiktok\.com\/@([^\/]+)\/video\/(\d+)/', $url, $matches)) {
            $username = $matches[1];
            $videoId  = $matches[2];
            $cleanUrl = "https://www.tiktok.com/@{$username}/video/{$videoId}";
            return [$videoId, $cleanUrl];
        }

        $parsed = parse_url($url);
        if (!empty($parsed['query'])) {
            parse_str($parsed['query'], $queryParams);
            if (!empty($queryParams['share_item_id']) && ctype_digit($queryParams['share_item_id'])) {
                $videoId  = $queryParams['share_item_id'];
                $cleanUrl = "https://www.tiktok.com/video/{$videoId}";
                return [$videoId, $cleanUrl];
            }
        }

        return [null, null];
    }

    public function submitTask(SubmitTaskRequest $request, $id)
    {
        $url      = $request->tiktok_video_link;
        $finalUrl = $url;

        if (str_contains($url, 'vt.tiktok.com') || str_contains($url, 'vm.tiktok.com') || str_contains($url, 'tiktok.com/t/')) {
            $ch = curl_init($url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
            curl_setopt($ch, CURLOPT_NOBODY, true);
            curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) Chrome/120.0.0.0 Safari/537.36');
            curl_exec($ch);
            $finalUrl = curl_getinfo($ch, CURLINFO_EFFECTIVE_URL);
            curl_close($ch);
        }

        [$videoId, $cleanUrl] = $this->extractTikTokVideoId($finalUrl);

        if (!$videoId) {
            return back()->withErrors(['tiktok_video_link' => 'Tidak dapat menemukan ID Video dari link yang diberikan.']);
        }

        $isDuplicate = TaskReport::where('video_id', $videoId)
            ->where('id', '!=', $id)
            ->exists();

        if ($isDuplicate) {
            return back()->withErrors(['tiktok_video_link' => 'Video ini sudah pernah digunakan sebelumnya. Harap gunakan video lain yang unik.']);
        }

        $taskReport = TaskReport::findOrFail($id);
        $taskReport->update([
            'tiktok_video_link' => $cleanUrl,  
            'video_id'          => $videoId,
            'task_status'       => 'COMPLETED'
        ]);

        return redirect()->route('affiliator.task.show', $id)->with('success', 'Tugas berhasil dikumpulkan dan video diverifikasi!');
    }
}