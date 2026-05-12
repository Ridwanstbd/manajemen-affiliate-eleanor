<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ChallengeController extends Controller
{
    public function index()
    {
        return view('pages.admin.challenge.index');
    }
    public function data(Request $request)
    {
        if($request->ajax()){
            
        }
    }
}
