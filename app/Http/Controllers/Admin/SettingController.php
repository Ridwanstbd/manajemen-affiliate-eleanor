<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\UpdateTaskDeadlineRequest;
use App\Services\Admin\SettingService;

class SettingController extends Controller
{
    protected $settingService;

    public function __construct(SettingService $settingService)
    {
        $this->settingService = $settingService;
    }

    public function updateTaskDeadline(UpdateTaskDeadlineRequest $request)
    {
        try {
            $days = $request->validated()['task_deadline_days'];

            $this->settingService->updateTaskDeadlineDays($days);
            
            return redirect()->back()->with('success', 'Pengaturan batas waktu tugas berhasil diperbarui.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal memperbarui pengaturan: ' . $e->getMessage());
        }
    }
}