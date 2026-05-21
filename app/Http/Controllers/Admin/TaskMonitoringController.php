<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\Admin\TaskMonitoringService;
use Illuminate\Http\Request;

class TaskMonitoringController extends Controller
{
    protected $taskMonitoringService;

    public function __construct(TaskMonitoringService $taskMonitoringService)
    {
        $this->taskMonitoringService = $taskMonitoringService;
    }

    public function index(Request $request)
    {
        $currentTab = $request->query('tab', 'pending');
        return view('pages.admin.task-monitoring.index', compact('currentTab'));
    }

    public function data(Request $request)
    {
        if($request->ajax()){
            $tab = $request->query('tab', 'pending');
            return $this->taskMonitoringService->getDatatablesData($tab);
        }
    }
}