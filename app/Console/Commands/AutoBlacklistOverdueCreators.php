<?php

namespace App\Console\Commands;

use App\Models\Setting;
use Illuminate\Console\Command;
use App\Models\SampleRequest;
use App\Models\Blacklist;
use App\Models\User;
use Carbon\Carbon;

class AutoBlacklistOverdueCreators extends Command
{
    protected $signature = 'creator:auto-blacklist';
    protected $description = 'Otomatis blacklist affiliator yang melewati tenggat waktu konten setelah sampel diterima.';

    public function handle()
    {
        $setting = Setting::where('key', 'task_deadline_days')->first();
        $maxDays = $setting ? (int) $setting->value : 14;
        $deadlineDate = Carbon::now()->subDays($maxDays);

        $overdueRequests = SampleRequest::where('status', 'DELIVERED')
            ->whereNotNull('delivered_at')
            ->where('delivered_at', '<', $deadlineDate)
            ->whereDoesntHave('taskReports', function ($query) {
                $query->where('task_status', 'COMPLETED');
            })
            ->get();

        foreach ($overdueRequests as $request) {
            Blacklist::firstOrCreate(
                ['user_id' => $request->user_id],
                [
                    'reason' => "Melewati batas waktu {$maxDays} hari penugasan sampel. ID Request: {$request->id}",
                    'created_at' => now(),
                    'updated_at' => now(),
                ]
            );

            User::where('id', $request->user_id)->update([
                'account_status' => 'BLACKLISTED'
            ]);
            
            $this->info("User ID {$request->user_id} otomatis diblacklist.");
        }

        $this->info("Proses auto-blacklist selesai dijalankan.");
    }
}