<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Service;
use Session;
use Validator;


class CreateNeedValidationController extends Controller 
{
    public function __construct()
    {
        $this->middleware(function ($request, $next) {
            Session::put('active', 'department');

            return $next($request);
        });
    }
  
}






