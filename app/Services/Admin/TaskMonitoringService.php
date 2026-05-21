<?php

namespace App\Services\Admin;

use App\Models\User;
use Yajra\DataTables\Facades\DataTables;
use Carbon\Carbon;

class TaskMonitoringService
{
    public function getDatatablesData($tab)
    {
        $statusMap = [
            'pending' => 'PENDING',
            'disetujui' => 'APPROVED',
            'dalam-perjalanan' => 'SHIPPED',
            'ditolak' => 'REJECTED',
            'terkirim' => 'DELIVERED',
        ];

        $status = $statusMap[$tab] ?? 'PENDING';

        $query = User::where('role', 'AFFILIATOR')
            ->whereHas('sampleRequests', function ($q) use ($status) {
                $q->where('status', $status);
            })
            ->with(['taskReports.products', 'sampleRequests.details.product']);

        return DataTables::of($query)
            ->addIndexColumn()
            ->addColumn('username', function ($row) {
                return '@' . $row->username;
            })
            ->addColumn('video_progress', function ($row) {
                $total = $row->taskReports->count();
                $completed = $row->taskReports->where('task_status', 'COMPLETED')->count();
                return "{$completed} / {$total} Video";
            })
            ->addColumn('updated_at', function($row) {
                $latestUpdate = $row->taskReports->max('updated_at') ?? $row->updated_at;
                $months = [1=>'Jan', 2=>'Feb', 3=>'Mar', 4=>'Apr', 5=>'Mei', 6=>'Jun', 7=>'Jul', 8=>'Ags', 9=>'Sep', 10=>'Okt', 11=>'Nov', 12=>'Des'];
                $date = Carbon::parse($latestUpdate);
                return $date->format('d') . ' ' . $months[$date->format('n')] . ' ' . $date->format('Y');
            })
            ->addColumn('status', function($row) {
                $total = $row->taskReports->count();
                $completed = $row->taskReports->where('task_status', 'COMPLETED')->count();
                $hasOverdue = $row->taskReports->where('task_status', 'OVERDUE')->count() > 0;

                if ($total > 0 && $completed === $total) {
                    $statusText = 'Selesai';
                    $class = 'paid'; 
                } elseif ($hasOverdue) {
                    $statusText = 'Terlambat';
                    $class = 'overdue'; 
                } else {
                    $statusText = 'Dalam Proses';
                    $class = 'pending'; 
                }

                return view('components.atoms.badge', [
                    'slot' => $statusText,
                    'status' => $class
                ])->render();
            })
            ->addColumn('action', function($row) {
                return view('pages.admin.task-monitoring.action-buttons', compact('row'))->render();
            })
            ->rawColumns(['action', 'status'])
            ->make(true);
    }
}