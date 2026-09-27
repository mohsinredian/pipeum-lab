<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Location;
use App\Models\Floor;
// use Helper;
use Validator;
use Session;

class EntityController extends Controller
{
    public function entity_view($id){
        $floor = Floor::select('floor_name')->where(['id' => $id])->first();
        return view('admin.entity.create')->with(['floor'=>$floor]);
    }
}
