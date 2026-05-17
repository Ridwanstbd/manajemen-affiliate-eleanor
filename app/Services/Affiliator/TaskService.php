<?php

namespace App\Services\Affiliator;

use App\Models\TaskReport;
use App\Models\User;
use Illuminate\Http\Request;

class Taskservice
{
    public function getTabData($tab, Request $request)
    {
        switch ($tab) {
            case 'process-overdue':
                return $this->getTaskData($request);
            case 'completed':
                return $this->getCompletedTaskData($request);
            default:
                return $this->getTaskData($request);
        }
    }

    public function getTaskData(Request $request)
    {
        $user = auth()->user(); 

        $query = TaskReport::where('user_id', $user->id)
            ->whereIn('task_status', ['PROCESSING', 'OVERDUE']) 
            ->with(['products'])
            ->latest();

        return $query->paginate(10)->withQueryString();
    }

    public function getCompletedTaskData(Request $request)
    {
        $user = auth()->user(); 

        $query = TaskReport::where('user_id', $user->id)
            ->where('task_status', 'COMPLETED') 
            ->with(['products']) 
            ->latest();

        return $query->paginate(10)->withQueryString();
    }

    public function getTaskDetail(User $user, int $id): TaskReport
    {
        return TaskReport::where('user_id', $user->id)
            ->with(['products', 'sampleRequests']) 
            ->findOrFail($id);
    }
}