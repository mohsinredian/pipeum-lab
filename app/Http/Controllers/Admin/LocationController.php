<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Session;
use App\Models\Location;
use App\Models\FloorPlan;
use App\Models\Division;
use Helper;
use Validator;


class LocationController extends Controller
{
    public function __construct()
    {
        $this->middleware(function ($request, $next) {
            Session::put('active', 'locations');

            return $next($request);
        });
    }

    public function location_list(Request $request)
    {
        $user = \Auth()->user();
        if ($request->ajax()) {
            $locations = datatables()
                ->of(
                    Location::with('division')->orderBy('id', 'desc')->get()
                )
                ->addColumn('division_name', function ($data) {

                    return $data->division->name;
                })
                ->addColumn('status', function ($data) {

                    return $data->status == '1' ? 'Active' : 'Inactive'; 
                })
                // ->addColumn('manage_floor', function ($data) use ($user) {
                //     $button = '';
                //     if ($user->can('manage_floor')) {
                //         $button = '<a href="/admin/locations/manage_floor/'.$data->id.'" class="btn btn-sm btn-clean btn-icon" title="Manage"><i class="fas fa-clipboard"></i></a>';
                //     }                         
                //     return $button;
                // })
                ->addColumn('action', function ($data) use ($user) {
                    $button = '';
                 
                    if ($user->can('edit_division')) {
                        $button = '<a href="/admin/locations/edit/' . $data->id . '" class="btn btn-sm btn-clean btn-icon" title="Edit"><i class="fas fa-edit text-info"></i></a> &nbsp; &nbsp';
                    }

                    return $button;
                })
                ->addIndexColumn()
             
                ->rawColumns(['action', 'division_name'])
                ->make(true);

            return $locations;
        }
        return view('admin.location.list');
    }
    public function create_location(Request $request)
    {
        $division = Division::select('id', 'name')->where('status', 1)->get();
        return view('admin.location.create', compact('division'));
    }

    public function manage_floor(Request $request)
    {
        $location_id = request()->segment(4);
        $locations = Location::select('id', 'name')->where('id', $location_id)->get();
        if(!empty($location_id)){
        $floor = FloorPlan::select('id','location_id','floor_name')->where('location_id', $location_id)->get();
        // print_r($floor);
        }
    //    echo"<pre>";print_r($locations[0]['id']);die;
        return view('admin.location.manage_floor', compact('locations','floor')
    );
    }
    public function view_floor(Request $request)
    {
        $location_id = request()->segment(4);
        $locations = Location::select('id', 'name')->where('id', $location_id)->get();
        if(!empty($location_id)){
        $floor = FloorPlan::select('id','location_id','floor_name')->where('location_id', $location_id)->get();
        // print_r($floor);
        }
    //    echo"<pre>";print_r($locations[0]['id']);die;
        return view('admin.location.view_floor', compact('locations','floor')
    );
    }
    public function store_location(Request $request)
    {
       

        // dd($request->all());
        try {
            $request_input = $request->except('_token');
            $name =$request_input['name'];
            $company_id=$request_input['company_id'];
           
            if (Location::where(['divisions_id' => $company_id, 'name' => $name])->exists()) {
                $rules = [
                    // 'division' => 'required|unique:locations,divisions_id,except,id',
                    'company_id' => 'required|unique:locations,divisions_id,except,id',
                    'name' => 'required|unique:locations,name,except,id',
                    'status' => 'required|in:0,1'
                ];
    
    
                $messages = [
                    // 'division' => 'The compane name has already been taken.',
                    'name' => 'The location  name has already been taken.',
                    'status' => 'required|in:0,1',
                    'company_id.required' => 'Please select company',
                    'name.required' => 'Please enter location name',
                    'name.max' => 'Location name should not be more than 255 characters',
                    'status.required' => 'Please select status',
    
                ];
                $validator = Validator::make($request_input, $rules, $messages);
                if ($validator->fails()) {
                    $response['msg'] = $validator->errors()->toArray();
                    $response['result'] = 'error';
                } else {
    
                    $request_input['status'] = isset($request_input['status']) ? $request_input['status'] : 0;
                    $location = Location::create([
                        'name' => $request_input['name'],
                        'divisions_id'=>$request_input['company_id'],
                       // 'divisions_id' => 1,
                        'status' => $request_input['status'],
                    ]);
                    $response['result'] = 'success';
                    $response['msg'] = 'Location created';
                }
            }else{
               
                    $rules = [
                        'name' => 'required',
                        'status' => 'required|in:0,1'
                    ];
        
        
                    $messages = [
                        'division' => 'The company name has already been taken.',
                        'name' => 'The location  name has already been taken.',
                        'status' => 'required|in:0,1',
                        'name.required' => 'Please enter location name',
                        'name.max' => 'Location name should not be more than 255 characters',
                        'status.required' => 'Please select status',
        
                    ];

                    
                    $validator = Validator::make($request_input, $rules, $messages);
                    if ($validator->fails()) {
                        $response['msg'] = $validator->errors()->toArray();
                        $response['result'] = 'error';
                    } else {
        
                        $request_input['status'] = isset($request_input['status']) ? $request_input['status'] : 0;
        
                       
                        $location = Location::create([
                            'name' => $request_input['name'],
                               'divisions_id'=>$request_input['company_id'],
                           // 'divisions_id' => 1,
                            'status' => $request_input['status'],
                        ]);
                       // echo $location;
                        
                        $response['result'] = 'success';
                        $response['msg'] = 'Location created';
                    }
                }
            
            
        } catch (\Exception $e) {
            app(\App\Exceptions\Handler::class)->report($e);
            $response['result'] = 'failure';
            $response['msg'] = $e->getMessage();
        }

        return response()->json($response);
    }

    public function edit_location(Request $request)
    {

        $location = Location::findOrFail($request->id);
        $divisions = Division::select('id', 'name')->where('status', 1)->get();

        return view('admin.location.edit', compact('location', 'divisions'));
    }
    public function update_location(Request $request)
    {
        try {
            $request_input = $request->except('_token');
            $location_id = $request_input['location_id'];
            $name=$request_input['name'];
            $divisions_id=$request_input['company_id'];
            $db_location = Location::where(['divisions_id' => $divisions_id, 'name' => $name])->first();

            if ($db_location ==null) {
                $rules = [
                    // 'company_id' => 'required|unique:locations,divisions_id,except,id',
                    'company_id' => 'required',
                    'name' => 'required|unique:locations,name,except,id',
                    'status' => 'required|in:0,1',
                ];
    
                $messages = [
                    'company_id.required' => 'Please select company name',
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
                    $location_id = $request_input['location_id'];
                    unset($request_input['location_id']);
    
                    $request_input['status'] = isset($request_input['status']) ? $request_input['status'] : 0;
                    
                
                    Location::find($location_id)->update([
                        'name'=>$request_input['name'],
                       'divisions_id'=>$request_input['company_id'],
                        'status'=>$request_input['status'],
                    ]);
                    
                    $response['result'] = 'success';
                    $response['msg'] = 'Location Updated';
                }
            } 
            
            else {
                $db_location = Location::find($location_id);
                if ($db_location->divisions_id==$request_input['company_id'] && $db_location->name==$request_input['name']) {
                    $rules = [
                        'company_id' => 'required',
                        'name' => 'required|max:255',
                        'status' => 'required|in:0,1',
                    ];
    
                    $messages = [
                        'company_id.required' => 'Please select company name',
                        'name.required' => 'Please enter location name',
                        'name.max' => 'Location name should not be more than 255 characters',
                        'status.required' => 'Please select status',
    
    
                    ];
    
                    $validator = Validator::make($request_input, $rules, $messages);
                    if ($validator->fails()) {
                        $response['msg'] = $validator->errors()->toArray();
                        $response['result'] = 'error';
                    } else {
                        $location_id = $request_input['location_id'];
                        unset($request_input['location_id']);
    
                        $request_input['status'] = isset($request_input['status']) ? $request_input['status'] : 0;
    
    
                        Location::find($location_id)->update([
                            'name' => $request_input['name'],
                            'divisions_id' => $request_input['company_id'],
                            'status' => $request_input['status'],
                        ]);
    
                        $response['result'] = 'success';
                        $response['msg'] = 'Location Updated';
                    }
                }
                elseif ($db_location->divisions_id==$request_input['company_id'] && $db_location->name!=$request_input['name']) {
                    $rules = [
                        'company_id' => 'required',
                        'name' => 'required|unique:locations,name,except,id',
                        'status' => 'required|in:0,1',
                    ];
    
                    $messages = [
                        'company_id.required' => 'Please select company name',
                        'name.required' => 'Please enter location name',
                        'name.max' => 'Location name should not be more than 255 characters',
                        'status.required' => 'Please select status',
    
    
                    ];
    
                    $validator = Validator::make($request_input, $rules, $messages);
                    if ($validator->fails()) {
                        $response['msg'] = $validator->errors()->toArray();
                        $response['result'] = 'error';
                    } else {
                        $location_id = $request_input['location_id'];
                        unset($request_input['location_id']);
    
                        $request_input['status'] = isset($request_input['status']) ? $request_input['status'] : 0;
    
    
                        Location::find($location_id)->update([
                            'name' => $request_input['name'],
                            'divisions_id' => $request_input['company_id'],
                            'status' => $request_input['status'],
                        ]);
    
                        $response['result'] = 'success';
                        $response['msg'] = 'Location Updated';
                    }
                }
                elseif ($db_location->divisions_id!=$request_input['company_id'] && $db_location->name==$request_input['name']) {
                    $rules = [
                        'division' => 'required|unique:locations,name,except,id',
                        'name' => 'required',
                        'status' => 'required|in:0,1',
                    ];
    
                    $messages = [
                        'division.required' => 'Please select company name',
                        'name.required' => 'Please enter location name',
                        'name.max' => 'Location name should not be more than 255 characters',
                        'status.required' => 'Please select status',
    
    
                    ];
    
                    $validator = Validator::make($request_input, $rules, $messages);
                    if ($validator->fails()) {
                        $response['msg'] = $validator->errors()->toArray();
                        $response['result'] = 'error';
                    } else {
                        $location_id = $request_input['location_id'];
                        unset($request_input['location_id']);
    
                        $request_input['status'] = isset($request_input['status']) ? $request_input['status'] : 0;
    
    
                        Location::find($location_id)->update([
                            'name' => $request_input['name'],
                            'divisions_id' => $request_input['company_id'],
                            'status' => $request_input['status'],
                        ]);
    
                        $response['result'] = 'success';
                        $response['msg'] = 'Location Updated';
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

    public function delete_location(Request $request)
    {
        try {
            $id = $request['id'];
            if (!empty($id)) {
                $location = Location::findOrFail($id);

                $location->delete();

                $response['result'] = 'success';
                $response['msg'] = 'Location Deleted';
            } else {
                $response['result'] = 'failure';
                $response['msg'] = 'Select Location';
            }
        } catch (Exception $e) {
            app(\App\Exceptions\Handler::class)->report($e);
            $response['result'] = 'failure';
            $response['msg'] = $e->getMessage();
        }

        return response()->json($response);
    }
    public function store_floor_plan(Request $request)
    {
        $data = $request['floor_data'];
        // echo"<pre>"; print_r($data);die;
        
       
        try {
            $request_input = $request->except('_token');
            foreach ($data as $key => $value) {
            $floor_plan = [
                'location_id' => $request_input['location_id'],
                'floor_name'=> str_replace('_',' ', $key),
                'store_room'=> $value['store'] ?? null,
                'gym'=>$value['gym'] ?? null,
                'corridor'=>$value['corridor'] ?? null,
                'cabins'=>$value['cabins'] ?? null,
                'panel_room'=>$value['panel_room']?? null,
                'reception_area'=>$value['reception_area'] ?? null,
                'meeting_room'=>$value['meeting_room'] ?? null,
                'ladies_washroom'=>$value['ladies_washroom'] ?? null,
                'gents_washroom'=>$value['gents_washroom'] ?? null,
                'board_room'=>$value['board_room'] ?? null,
                'common_area'=>$value['common_area'] ?? null,
                'status' => 1,
            ];  
            FloorPlan::create($floor_plan);
        }
            $response['result'] = 'success';
            $response['msg'] = 'Location created';
            //$name =$request_input['name'];
           // $company_id=$request_input['company_id'];
           
          // if (FloorPlan::where(['divisions_id' => $company_id, 'name' => $name])->exists()) {
               // $rules = [
                    //'division' => 'required|unique:locations,divisions_id,except,id',
                  //  'name' => 'required|unique:locations,name,except,id',
                    //'status' => 'required|in:0,1'
               // ];
    
    
               // $messages = [
                    // 'division' => 'The compane name has already been taken.',
                    // 'name' => 'The location  name has already been taken.',
                    // 'status' => 'required|in:0,1',
                    // 'division.required' => 'Please select division',
                    // 'name.required' => 'Please enter location name',
                    // 'name.max' => 'Location name should not be more than 255 characters',
                   // 'status.required' => 'Please select status',
    
                //];
                // $validator = Validator::make($request_input, $rules, $messages);
                // if ($validator->fails()) {
                //     $response['msg'] = $validator->errors()->toArray();
                //     $response['result'] = 'error';
                // } else {
    
                    // $request_input['status'] = isset($request_input['status']) ? $request_input['status'] : 0;
                    // $floor_datas = $request_input['floor_name'];
                    // $store_room = $request_input['store_room']?? null;
                    // $gym = $request_input['gym']?? null;
                    // $corridor = $request_input['corridor']?? null;
                    // $cabins = $request_input['cabins']?? null;
                    // $panel_room = $request_input['panel_room']?? null;
                    // $reception_area= $request_input['reception_area']?? null;
                    // $meeting_room = $request_input['meeting_room']?? null;
                    // $ladies_washroom = $request_input['ladies_washroom']?? null;
                    // $gents_washroom = $request_input['gents_washroom']?? null;
                    // $board_room = $request_input['board_room']?? null;
                    // $common_area = $request_input['common_area']?? null;
                
                    // $count = 0;
                    // $floor_plan= [];
                    // //dd($floor_datas);
                    // //print_r($fourth_floor);die;
                    // foreach($object_decoded as $idx => $floor_data)
                    // {
                    //     if(!empty($floor_data)) {
                    //         foreach($floor_data as $idy => $entity_data) {
                                
                    //             echo'<pre>---------------entityData';print_r($entity_data);
                    //             if(!empty($entity_data)) {
                    //                 foreach($entity_data as $idz => $entity_value) {
                    //                     // print_r("decode");
                    //                     // print_r($entity_value);
                    //                     // if() {

                    //                     // }
                                     
                    //                    echo'<pre>----------------enity_value';print_r($entity_value);
                    //                 } 
                                    
                                    
                    //             }
                    //         }
                    //     }
                        
                        
                        
                    //     $count = 0;
                    //     $floor_plan = [
                    //         'location_id' => $request_input['location_id'],
                    //         'floor_name'=>$request_input ?? null,
                    //         'store_room'=>$store_room ?? null,
                    //         'gym'=>$gym ?? null,
                    //         'corridor'=>$corridor ?? null,
                    //         'cabins'=>$cabins ?? null,
                    //         'panel_room'=>$panel_room?? null,
                    //         'reception_area'=>$reception_area ?? null,
                    //         'meeting_room'=>$meeting_room ?? null,
                    //         'ladies_washroom'=>$ladies_washroom ?? null,
                    //         'gents_washroom'=>$gents_washroom ?? null,
                    //         'board_room'=>$board_room ?? null,
                    //         'common_area'=>$common_area ?? null,
                    //         'status' => 1,
                    //     ];
                    //     //$count++;
                    //     //echo'<pre>';print_r($floor_plan);die;
                    //  //   FloorPlan::create($floor_plan);
                    // }//die;

                    
               // }
            // }else{
               
            //         $rules = [
            //             'name' => 'required',
            //             'status' => 'required|in:0,1'
            //         ];
        
        
            //         $messages = [
            //             'division' => 'The company name has already been taken.',
            //             'name' => 'The location  name has already been taken.',
            //             'status' => 'required|in:0,1',
            //             'name.required' => 'Please enter location name',
            //             'name.max' => 'Location name should not be more than 255 characters',
            //             'status.required' => 'Please select status',
        
            //         ];

                    
            //         $validator = FloorPlan::make($request_input, $rules, $messages);
            //         if ($validator->fails()) {
            //             $response['msg'] = $validator->errors()->toArray();
            //             $response['result'] = 'error';
            //         } else {
        
            //             $request_input['status'] = isset($request_input['status']) ? $request_input['status'] : 0;
        
                       
            //             $location = Location::create([
            //                 'name' => $request_input['name'],
            //                    'divisions_id'=>$request_input['company_id'],
            //                // 'divisions_id' => 1,
            //                 'status' => $request_input['status'],
            //             ]);
            //            // echo $location;
                        
            //             $response['result'] = 'success';
            //             $response['msg'] = 'Location created';
            //         }
              //  }
        
        } catch (\Exception $e) {
            app(\App\Exceptions\Handler::class)->report($e);
            $response['result'] = 'failure';
            $response['msg'] = $e->getMessage();
        }

        return response()->json($response);
    }

}
