<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\ChallengeRequest;
use App\Models\Challenge;
use App\Services\Admin\ChallengeService;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;
use Carbon\Carbon;

class ChallengeController extends Controller
{
    protected $challengeService;

    public function __construct(ChallengeService $challengeService)
    {
        $this->challengeService = $challengeService;
    }

    public function index()
    {
        return view('pages.admin.challenge.index');
    }

    public function data(Request $request)
    {
        if($request->ajax()){
            $query = Challenge::with('rewards');

            return DataTables::of($query)
                ->addIndexColumn()
                ->addColumn('banner', function($row) {
                    if($row->banner_image_path) {
                        $imageUrl = asset('storage/' . $row->banner_image_path); 
                        return '<img src="'.$imageUrl.'" onclick="openLightbox(\''.$imageUrl.'\')" style="width:48px; height:24px; object-fit:cover; border-radius:4px; cursor:pointer; border:1px solid #e2e8f0;">';
                    }
                    return '<span style="font-size:12px; color:#94a3b8;">No Image</span>';
                })
                ->addColumn('period', function($row) {
                    $start = Carbon::parse($row->start_date)->translatedFormat('d M Y');
                    $end = Carbon::parse($row->end_date)->translatedFormat('d M Y');
                    return "{$start} - {$end}";
                })
                ->addColumn('commission_bonus', function($row) {
                    return 'Rp ' . number_format($row->commission_bonus, 0, ',', '.');
                })
                ->addColumn('action', function($row) {
                    return view('pages.admin.challenge.action-buttons', compact('row'))->render();
                })
                ->rawColumns(['banner','action']) 
                ->make(true);
        }
    }

    public function create(ChallengeRequest $request)
    {
        try {
            $this->challengeService->create($request->validated());
            return redirect()->back()->with('success', 'Tantangan berhasil dibuat!');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal membuat tantangan: ' . $e->getMessage());
        }
    }

    public function update(ChallengeRequest $request)
    {
        $request->validate(['id' => 'required|exists:challenges,id']);

        try {
            $this->challengeService->update($request->id, $request->validated());
            return redirect()->back()->with('success', 'Tantangan berhasil diperbarui!');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal memperbarui tantangan: ' . $e->getMessage());
        }
    }
    public function destroy(Request $request)
    {
        $request->validate([
            'id' => 'required|exists:challenges,id'
        ]);

        try {
            $this->challengeService->delete($request->id);
            return redirect()->back()->with('success', 'Tantangan berhasil dihapus!');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal menghapus tantangan: ' . $e->getMessage());
        }
    }
}