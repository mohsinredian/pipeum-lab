<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\NVService;
use Illuminate\Http\Request;


class FormController extends Controller
{
    public function preview(Request $request,$id)
    {
        $user = \Auth()->user();
        $data = NVService::where('id',$id)->first();
       
        // Process form data here
       return view('admin.nvService.preview', compact('data'));
        //return response()->json(['preview' => $preview]);
    }
}
