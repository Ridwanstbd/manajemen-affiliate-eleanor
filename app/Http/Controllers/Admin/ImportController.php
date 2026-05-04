<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\ImportRequest;
use Illuminate\Http\Request;

class ImportController extends Controller
{
    public function getImportData()
    {
        view('pages.admin.import-xlsx');
    }
    public function importData(ImportRequest $request)
    {
        
    }
}
