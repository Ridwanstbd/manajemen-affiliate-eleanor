<?php

namespace App\Services\Admin;

use App\Models\Setting;

class SettingService
{
    public function updateTaskDeadlineDays(int $days)
    {
        return Setting::updateOrCreate(
            ['key' => 'task_deadline_days'],
            ['value' => $days]
        );
    }
}