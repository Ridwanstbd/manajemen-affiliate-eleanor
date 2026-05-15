<?php

namespace App\Http\Controllers\Affiliator;

use App\Http\Controllers\Controller;

class TaskController extends Controller
{
    public function index()
    {
        return view('pages.affiliator.task.index');
    }
    public function detail($id)
    {
        return view('pages.affiliator.task.detail');
    }
    public function report($id)
    {}
}
