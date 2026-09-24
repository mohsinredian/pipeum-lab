<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Location;
use App\Models\Floor;
use Helper;
use Validator;
use Session;

class FloorController extends Controller
{

    // public function __construct()
    // {
    //     $this->middleware(function ($request, $next) {
    //        Session::put('active', 'floor');

    //         return $next($request);
    //     });
    // }
    public function floor_list(Request $request)
    {
        $user = \Auth()->user();
        if($request->ajax()){
           
            $floor = datatables()
                ->of(
                    Floor::with('location')->orderBy('id', 'desc')->get()
                )
                // ->addColumn('location_name', function ($data) {

                //     return $data->location->name;
                // })
                ->addColumn('status', function ($data) {

                    return $data->status == '1' ? 'Active' : 'Inactive';
                })
            
                ->addColumn('action', function ($data) use ($user) {
                    $button = '';
                    if ($user->can('edit_division')) {
                        $button = '<a href="/admin/floor/edit/' . $data->id . '" class="btn btn-sm btn-clean btn-icon edit_floor" title="Edit"><i class="fas fa-edit text-info"></i></a> &nbsp; &nbsp;';
                        // $button .= '&nbsp;&nbsp;'.'<a href="/admin/floor/manage/'.$data->id.'" class="btn btn-sm btn-clean btn-icon" title="Manage"><i class="fas fa-clipboard"></i></a>';
                    }
                    // if ($user->can('edit_division')) {
                    //     $button .= '<a href="javascript:;" data-id="'.$data->id.'" class="btn btn-sm btn-clean btn-icon delete_floor" title="Delete"><i class="fas fa-trash text-danger"></i></a>';
                    // }


                    return $button;
                })
                ->addIndexColumn()  
                ->rawColumns(['action','location_name'])
                ->make(true);

            return $floor;
        }
        return view('admin.floor.list');
    }
    public function create_floor(Request $request){
        
        $divisions = Location::select('id','name')->first();
        return view('admin.floor.create')->with(['divisions'=>$divisions]);
    }

    public function store_floor(Request $request){
        try{
          
            $request_input = $request->except('_token');
            $name =$request_input['name'];
            $location_id = $request_input['location_id'];
           
            if (Floor::where(['location_id' => $location_id, 'floor_name' => $name])->exists()) {

            $rules = [
               'name' => 'required',
                // 'location_id' => 'required',
            ];

            $messages = [
               'name.required' => 'Please enter division name',
               'name.max' => 'Location name should not be more than 50 characters',
                // 'location_id.required' => 'Please enter division short code',
                // 'location_id.max' => 'short code should not be more than 10 characters',
                
            ];
           
            $validator = Validator::make($request_input, $rules, $messages);
           
            if($validator->fails()){

                $response['msg'] = $validator->errors()->toArray();
                $response['result'] = 'error';
            }
            else{
             
                $request_input['status'] = isset($request_input['status']) ? $request_input['status'] : 0;
                $floor = Floor::create([
                    'location_id'=>$location_id,
                    'floor_name'=>$request_input['name'],
                    'status'=>$request_input['status'],
                ]);

                $response['result'] = 'success';
                $response['msg'] = 'Floor created';
            }
        }else{
            $rules = [
                'name' => 'required',
                 // 'location_id' => 'required',
             ];
 
             $messages = [
                'name.required' => 'Please enter division name',
                'name.max' => 'Location name should not be more than 50 characters',
                 // 'location_id.required' => 'Please enter division short code',
                 // 'location_id.max' => 'short code should not be more than 10 characters',
                 
             ];
            
             $validator = Validator::make($request_input, $rules, $messages);
            
             if($validator->fails()){
 
                 $response['msg'] = $validator->errors()->toArray();
                 $response['result'] = 'error';
             }
             else{
              
                 $request_input['status'] = isset($request_input['status']) ? $request_input['status'] : 0;
                 $floor = Floor::create([
                     'location_id'=>$location_id,
                     'floor_name'=>$request_input['name'],
                     'status'=>$request_input['status'],
                 ]);
 
                 $response['result'] = 'success';
                 $response['msg'] = 'Floor created';
             }

        }  
        }
        catch(\Exception $e){
            app(\App\Exceptions\Handler::class)->report($e);
            $response['result'] = 'failure';
            $response['msg'] = $e->getMessage();
        }
 
        return response()->json($response);
    }
    public function edit_floor(Request $request)
    {

        $floor = Floor::findOrFail($request->id);
        $location = Location::select('id', 'name')->where('status', 1)->get();
        
        return view('admin.floor.edit', compact('location', 'floor'));
    }
    public function update_floor(Request $request)
    {
        try {
            $request_input = $request->except('_token');
            $floor_id = $request_input['floor_id'];
            $name=$request_input['name'];
            $location_id=$request_input['location_id'];
            $db_location = Floor::where(['location_id' => $location_id])->first();
            $rules = [
                'location_id' => 'required',
                'name' => 'required',
                'status' => 'required|in:0,1',
            ];

            $messages = [
                'location_id.required' => 'Please select location name',
                'name.required' => 'Please enter location name',
                'name.max' => 'Location name should not be more than 50 characters',
                'status.required' => 'Please select status',
                
                
            ];

            if ($db_location ==null) {
                $rules = [
                    'location_id' => 'required',
                    'name' => 'required',
                    'status' => 'required|in:0,1',
                ];
    
                $messages = [
                    'location_id.required' => 'Please select division name',
                    'name.required' => 'Please enter location name',
                    'name.max' => 'Location name should not be more than 50 characters',
                    'status.required' => 'Please select status',
                    
                    
                ];
    
                $validator = Validator::make($request_input, $rules, $messages);
                if($validator->fails()){
                    $response['msg'] = $validator->errors()->toArray();
                    $response['result'] = 'error';
                }
                else{
                    $floor_id = $request_input['id'];
                    unset($request_input['location_id']);
    
                    $request_input['status'] = isset($request_input['status']) ? $request_input['status'] : 0;
                    
                
                    Floor::find($floor_id)->update([
                        'floor_name'=>$request_input['name'],
                       'location_id'=>$request_input['location_id'],
                        'status'=>$request_input['status'],
                    ]);
                    
                    $response['result'] = 'success';
                    $response['msg'] = 'Location Updated';
                }
            } 
            
            else {
               
                $db_location = Floor::find($floor_id);
                if ($db_location->location_id==$request_input['location_id']) {
                    
    
                    $validator = Validator::make($request_input, $rules, $messages);
                    if ($validator->fails()) {
                        $response['msg'] = $validator->errors()->toArray();
                        $response['result'] = 'error';
                    } else {
                        $location_id = $request_input['location_id'];
                        unset($request_input['location_id']);
    
                        $request_input['status'] = isset($request_input['status']) ? $request_input['status'] : 0;
    
    
                        Floor::find($floor_id)->update([
                            'floor_name' => $request_input['name'],
                            'location_id' => $request_input['location_id'],
                            'status' => $request_input['status'],
                        ]);
    
                        $response['result'] = 'success';
                        $response['msg'] = 'Location Updated';
                    }
                }
                elseif ($db_location->location_id==$request_input['location_id']) {
                    // $rules = [
                    //     'division' => 'required',
                    //     'name' => 'required|unique:locations,name,except,id',
                    //     'status' => 'required|in:0,1',
                    // ];
    
                    // $messages = [
                    //     'division.required' => 'Please select division name',
                    //     'name.required' => 'Please enter location name',
                    //     'name.max' => 'Location name should not be more than 255 characters',
                    //     'status.required' => 'Please select status',
    
    
                    // ];
    
                    $validator = Validator::make($request_input, $rules, $messages);
                    if ($validator->fails()) {
                        $response['msg'] = $validator->errors()->toArray();
                        $response['result'] = 'error';
                    } else {
                        $location_id = $request_input['location_id'];
                        unset($request_input['location_id']);
    
                        $request_input['status'] = isset($request_input['status']) ? $request_input['status'] : 0;
    
    
                        Floor::find($floor_id)->update([
                            'floor_name' => $request_input['name'],
                            'location_id' => $request_input['location_id'],
                            'status' => $request_input['status'],
                        ]);
    
                        $response['result'] = 'success';
                        $response['msg'] = 'Location Updated';
                    }
                }
                elseif ($db_location->location_id!=$request_input['location_id']) {
                    // $rules = [
                    //     'division' => 'required|unique:locations,name,except,id',
                    //     'name' => 'required',
                    //     'status' => 'required|in:0,1',
                    // ];
    
                    // $messages = [
                    //     'division.required' => 'Please select division name',
                    //     'name.required' => 'Please enter location name',
                    //     'name.max' => 'Location name should not be more than 255 characters',
                    //     'status.required' => 'Please select status',
    
    
                    // ];
    
                    $validator = Validator::make($request_input, $rules, $messages);
                    if ($validator->fails()) {
                        $response['msg'] = $validator->errors()->toArray();
                        $response['result'] = 'error';
                    } else {
                        $location_id = $request_input['location_id'];
                        unset($request_input['location_id']);
    
                        $request_input['status'] = isset($request_input['status']) ? $request_input['status'] : 0;
    
    
                        Floor::find($floor_id)->update([
                            'floor_name' => $request_input['name'],
                            'location_id' => $request_input['location_id'],
                            'status' => $request_input['status'],
                        ]);
    
                        $response['result'] = 'success';
                        $response['msg'] = 'Floor Updated';
                    }
                }
                
            }
        } catch (\Exception $e) {
            app(\App\Exceptions\Handler::class)->report($e);
            $response['result'] = 'failure';
            $response['msg'] = $e->getMessage();
        }

        return response()->json($response);
    }

}
