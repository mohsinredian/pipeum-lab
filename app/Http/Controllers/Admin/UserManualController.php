<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class UserManualController extends Controller
{
    public function index($id){
        return view('admin.user_manual',compact('id'));
    }
}
