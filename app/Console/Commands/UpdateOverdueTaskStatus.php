<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\TaskReport;
use Carbon\Carbon;

class UpdateOverdueTaskStatus extends Command
{
    protected $signature = 'task:update-overdue';
    protected $description = 'Otomatis mengubah status tugas menjadi OVERDUE jika sudah melewati due_date';

    public function handle()
    {
        $now = Carbon::now()->startOfDay();

        $updated = TaskReport::where('task_status', 'PROCESSING')
            ->whereNotNull('due_date')
            ->whereDate('due_date', '<', $now)
            ->update(['task_status' => 'OVERDUE']);

        $this->info("Selesai: {$updated} tugas diubah menjadi OVERDUE.");
    }
}