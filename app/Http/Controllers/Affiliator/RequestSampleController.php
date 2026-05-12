<?php

namespace App\Http\Controllers\Affiliator;

use App\Http\Controllers\Controller;
use App\Models\TaskReport;
use Illuminate\Http\Request;

class RequestSampleController extends Controller
{
    public function submitTask(Request $request, $id)
    {
        $request->validate([
            'tiktok_video_link' => 'required|url|max:1000',
        ], [
            'tiktok_video_link.required' => 'Link video wajib diisi!',
            'tiktok_video_link.url' => 'Format link tidak valid.'
        ]);

        $url = $request->tiktok_video_link;
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

        preg_match('/video\/(\d+)/', $finalUrl, $matches);
        $videoId = $matches[1] ?? null;

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
            'tiktok_video_link' => $request->tiktok_video_link,
            'video_id' => $videoId,
            'task_status' => 'COMPLETED'
        ]);

        return redirect()->back()->with('success', 'Tugas berhasil dikumpulkan dan video diverifikasi!');
    }
}
