<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Session;
use Validator;
use App\Models\Task;
use App\Models\Ticket;
use App\Models\Division;
use App\Models\Employee;
use App\Models\Department;
use App\Models\User;
use App\Models\Location;
use App\Models\Service;
use Silber\Bouncer\Database\Role;
use Config;
use App\Models\FloorPlan;
use Illuminate\Support\Facades\DB;
use Exception;
use Hash;
use DateTime;
use DatePeriod;
use DateInterval;

class BrandController extends Controller
{
    public function __construct()
    {
        $this->middleware(function ($request, $next) {
            Session::put('active', 'brand');

            return $next($request);
        });
    }

    public function brand_list(Request $request)
    {
        $user = \Auth()->user();

        $ticketData = Task::with("division", "location","service","assignee")
        // ->when(($user->isAn('Supervisor') == true), function ($q) use ($user) {
        //        return $q->where('role_id', $user->role_id);
        //    })
        ->when(!empty($request['status']), function ($query) use($request) {
            return $query->where('status',$request['status']);
        })

        ->when(!empty($request['todate']), function ($query) use($request) {
            return $query->where('start_date',$request['todate'] );
        })
        // ->where(function ($query) use($start,$end) {
        //     $query->whereBetween('from_date',[$start,$end])
        //         ->orWhere(function ($q) use($start,$end){
        //                $q->where('start_date','>=', $start)
        //                 ->where('to_date', '<=',$end);
        //         });
        //   })
        ->get();
//  echo"<pre>";print_r($ticketData);die;
        if($request->ajax()){
            // echo"<pre>";print_r($ticketData);die;
                $ticket_list = datatables()
                ->of($ticketData)
                ->addColumn('id', function($data){

                    return $data->id != '' ? 'TD000'.$data->id : '';

                    return $data->id != '' ? 'TD000' . $data->id : '';
                })
                ->addColumn('status', function ($data) {

                    return $data->status == '1' ? 'New' : 'Closed';
                })
                ->addColumn('division', function ($data) {

                    return $data->division->name;
                })
                ->addColumn('location', function ($data) {

                    return $data->location->name;
                })
                ->addColumn('service', function ($data) {

                    return $data->service->name;
                })
                ->addColumn('assignee', function ($data) {

                    return $data->assignee->name;
                })

                ->addColumn('action', function ($data) use ($user) {
                    $button = '';
                    // if ($user->can('edit_division')) {

                    //     $button = '<a href="/admin/brands/edit/' . $data->id . '" class="btn btn-sm btn-clean btn-icon" title="Edit"><i class="fas fa-edit text-info"></i></a>';
                    //     // $button .= '&nbsp;&nbsp;';
                    // }
                    if ($user->can('delete_division')) {
                        $button .= '<a href="javascript:;" data-id="'.$data->id.'" class="btn btn-sm btn-clean btn-icon delete_brand" title="Delete"><i class="fas fa-trash text-danger"></i></a>';
                    }


                    return $button;
                })
                ->addIndexColumn()
                ->rawColumns(['action', 'status', 'division', 'location', 'frequency','assignee','employee'])
                ->make(true);
    //    echo"<pre>";print_r($ticket_list);die;
            return $ticket_list;
        }
        return view('admin.brand.list');
    }

    public function create_brand(Request $request)
    {
        $divisions = Division::select('id', 'name')->where('status', 1)->get();
        $locations = Location::select('id', 'name')->where('status', 1)->get();
        $role = Role::select('id', 'name')->get();
        $services = Service::select('id', 'name')->where('status', 1)->get();
        return view('admin.brand.create', compact('divisions', 'locations', 'services','role'));
    }
    public function store_brand(Request $request)
    {
        // echo"<pre>";
        //          print_r($request['floor']);
        //         exit();
        try {
            $request_input = $request->except('_token');

            $rules = [
                'task_name' => 'required|string|max:50|unique:brands,name,except,id',
                // 'status' => 'required|in:0,1',
            ];

            $messages = [
                //'name.required' => 'Please enter brand name',
                //'name.max' => 'brand name should not be more than 50 characters',
                //'status.required' => 'Please select status',

            ];
            $validator = Validator::make($request_input, $rules, $messages);
            if ($validator->fails()) {
                $response['msg'] = $validator->errors()->toArray();
                $response['result'] = 'error';
            } else {

                $request_input['status'] = isset($request_input['status']) ? $request_input['status'] : 0;

                 

                $brand = Task::create([
                    'task_name' => $request_input['task_name'],
                    'task_description' => $request_input['task_description'],
                    'frequency' => $request_input['frequency'],
                    'company_id' => $request_input['division'],
                    'location_id' => $request_input['location'],
                    'assigne_id' => $request_input['assignee'],
                    'start_date' => $request_input['start_date'],
                    'end_date' => $request_input['end_date'],
                    'role_id' => $request_input['role'],
                    'floor' => implode('+',$request_input['floor']),
                    'd1' => !empty($request_input['monday']) ? $request_input['monday'] : null,
                    'd2' => !empty($request_input['tuesday']) ? $request_input['tuesday'] : null,
                    'd3' => !empty($request_input['wednesday']) ? $request_input['wednesday'] : null,
                    'd4' => !empty($request_input['thursday']) ? $request_input['thursday'] : null,
                    'd5' => !empty($request_input['friday']) ? $request_input['friday'] : null,
                    'd6' => !empty($request_input['saturday']) ? $request_input['saturday'] : null,
                    'd7' => !empty($request_input['sunday']) ? $request_input['sunday'] : null,
                    'm1' => !empty($request_input['january']) ? $request_input['january'] : null,
                    'm2' => !empty($request_input['february']) ? $request_input['february'] : null,
                    'm3' => !empty($request_input['march']) ? $request_input['march'] : null,
                    'm4' => !empty($request_input['april']) ? $request_input['april'] : null,
                    'm5' => !empty($request_input['may']) ? $request_input['may'] : null,
                    'm6' => !empty($request_input['june']) ? $request_input['june'] : null,
                    'm7' => !empty($request_input['july']) ? $request_input['july'] : null,
                    'm8' => !empty($request_input['august']) ? $request_input['august'] : null,
                    'm9' => !empty($request_input['september']) ? $request_input['september'] : null,
                    'm10' => !empty($request_input['october']) ? $request_input['october'] : null,
                    'm11' => !empty($request_input['november']) ? $request_input['november'] : null,
                    'm12' => !empty($request_input['december']) ? $request_input['december'] : null,
                    'status' => 1,
                ]);
                $response['result'] = 'success';
                $response['msg'] = 'Task Created';
            }
        } catch (\Exception $e) {
            app(\App\Exceptions\Handler::class)->report($e);
            $response['result'] = 'failure';
            $response['msg'] = $e->getMessage();
        }


        if ($response['result'] = 'success') {
            $responseTicket =  $this->StoreTickets($request, $brand);
            if ($responseTicket['result'] = 'success') {
                return response()->json($responseTicket);
            }
        }
    }
    public function StoreTickets(Request $request, $task)
    {
        $startdate = $request->start_date;
        $endate = $request->end_date;
        $start = new DateTime($startdate);
        $end = new DateTime($endate);
        // otherwise the  end date is excluded (bug?)
        $end->modify('+1 day');

        $interval = $end->diff($start);

        // total days
        $days = $interval->days;

        // create an iterateable period of date (P1D equates to 1 day)
        $period = new DatePeriod($start, new DateInterval('P1D'), $end);

        // best stored as array, so you can add more than one
        
        $final_Array = array();
        foreach ($period as $dt) {
            $curr = $dt->format('l');
            $dates = $dt->format('Y-m-d');
            // print_r($dates);
            if (!empty($task->d1)) {
                if ($curr == 'Monday') {
                    $final_Array["Monday"][] = $dates;
                }
            }
            if (!empty($task->d2)) {
                if ($curr == 'Tuesday') {
                    $final_Array["Tuesday"][] = $dates;
                }
            }
            if (!empty($task->d3)) {
                if ($curr == 'Wednesday') {
                    $final_Array["Wednesday"][] = $dates;
                }
            }
            if (!empty($task->d4)) {
                if ($curr == 'Thursday') {
                    $final_Array["Thursday"][] = $dates;
                }
            }
            if (!empty($task->d5)) {
                if ($curr == 'Friday') {
                    $final_Array["Friday"][] = $dates;
                }
            }
            if (!empty($task->d6)) {
                if ($curr == 'Saturday') {
                    $final_Array["Saturday"][] = $dates;
                }
            }
            if (!empty($task->d7)) {
                if ($curr == 'Sunday') {
                    $final_Array["Sunday"][] = $dates;
                }
            }
            // substract if Saturday or Sunday
            if ($curr == 'Saturday' || $curr == 'Sunday') {
                $days--;
            }
        }

        //echo $days;
        
        foreach ($final_Array as $day => $dates) {
            $count = 0;
            $count = count($dates);
               for ($i = 1; $i <= $count; $i++) {
                    $idx = $i-1;
                    
                    $startDate = $dates[$idx];
                    $ticket = Ticket::create([
                        'task_name' => $task['task_name'],
                        'task_description' => $task['task_description'],
                        'company_id' => $task['company_id'],
                        'location_id' => $task['location_id'],
                        'assigne_id' => $task['assigne_id'],
                        'floor'=>$task['floor'],
                        'start_date' => $startDate,
                        'end_date' => $startDate,
                        'role_id' => $task['role_id'],
                        'status' => 1,
                        'day' => $day,
                        'task_id' => $task['id'],
                    ]);
                }
            
        }

        $response['result'] = 'success';
        $response['msg'] = 'Tickets Created';
        return $response;
    }

    public function edit_brand(Request $request)
    {
        $brand = Brand::findOrFail($request->id);
        return view('admin.brand.edit', compact('brand'));
    }

    public function update_brand(Request $request)
    {
        try {
            $request_input = $request->except('_token');
            $db_brand_name = Brand::find($request_input['brand_id']);
            if ($db_brand_name->name != $request_input['name']) {
                $rules = [
                    'name' => 'required|string|max:50|unique:brands,name,except,id',
                    'status' => 'required|in:0,1',
                ];

                $messages = [
                    'name.required' => 'Please enter brand name',
                    'name.max' => 'brand name should not be more than 50 characters',
                    'status.required' => 'Please select status',

                ];
                $validator = Validator::make($request_input, $rules, $messages);
                if ($validator->fails()) {
                    $response['msg'] = $validator->errors()->toArray();
                    $response['result'] = 'error';
                } else {

                    $brand_id = $request_input['brand_id'];
                    unset($request_input['brand_id']);

                    $request_input['status'] = isset($request_input['status']) ? $request_input['status'] : 0;


                    brand::find($brand_id)->update([
                        'name' => $request_input['name'],
                        'status' => $request_input['status'],
                    ]);

                    $response['result'] = 'success';
                    $response['msg'] = 'brand Updated';
                }
            } else {
                $brand_id = $request_input['brand_id'];
                unset($request_input['brand_id']);

                $request_input['status'] = isset($request_input['status']) ? $request_input['status'] : 0;


                brand::find($brand_id)->update([
                    'name' => $request_input['name'],
                    'status' => $request_input['status'],
                ]);

                $response['result'] = 'success';
                $response['msg'] = 'brand Updated';
            }
        } catch (\Exception $e) {
            app(\App\Exceptions\Handler::class)->report($e);
            $response['result'] = 'failure';
            $response['msg'] = $e->getMessage();
        }

        return response()->json($response);
    }
    public function delete_brand(Request $request)
    {
        try {
            $id = $request['id'];
            if (!empty($id)) {
                //$brand = Brand::findOrFail($id);
                $assets = Task::where('id',$id);
                $floor = Ticket::where('task_id',$id);
                
                $assets->delete();
                $floor->delete();

                $response['result'] = 'success';
                $response['msg'] = 'Task Deleted';
            } else {
                $response['result'] = 'failure';
                $response['msg'] = 'Select Brand';
            }
        } catch (Exception $e) {
            app(\App\Exceptions\Handler::class)->report($e);
            $response['result'] = 'failure';
            $response['msg'] = $e->getMessage();
        }

        return response()->json($response);
    }

    public function getAssigneeByLocation(Request $request)
    {

        try {
            $location_id = $request->locations ?? null;
           
            $assets = DB::table('employee')->select('employee.employee_id as employee_id','employee.name as name','employee.role_name as role_name','roles.*')->join('roles','roles.id','employee.role_name')->where('employee.location_id', $location_id)->orderBy('id', 'desc')->groupBy('roles.name')->get()->toArray();

            $floor = FloorPlan::select('id', 'floor_name')->where('location_id', $location_id)->get()->toArray();
            $html = '<div class="col-12 mb-1"> 
            <label class="col-sm-2 col-form-label eventLabel"><b>Select Floor</b></label>
       </div>';
            $count = count($floor);
            foreach($floor as $floorName) {
                $html.= '<div class="col-2 mb-1">
                            <div class="form-check">
                                <label class="form-check-label" for="">
                                    <input class="checkbox" type="checkbox" id="floor_'.$floorName['id'].'" name="floor[]" value="'.$floorName['floor_name'].'"> '.$floorName['floor_name'].'
                                </label>
                            </div>
                        </div>';
            }
            

            // $html = view("admin.brand.floor")->render();

            return response()->json(['result' => 'success', 'data' => $assets,'floorPlan' => $html]);
        } catch (Exception $e) {
            app(\App\Exceptions\Handler::class)->report($e);

            return response()->json(['result' => 'failure', 'msg' => $e->getMessage()]);
        }
    }
    public function getroleByassignee(Request $request)
    {

        try {
            $role_id = $request->role ?? null;
            $location_id = @$request->location;
            $assets = Employee::select('user_id', 'name')->where('role_name', $role_id)->where('location_id',$location_id)->orderBy('id', 'desc')->get()->toArray();
            return response()->json(['result' => 'success', 'data' => $assets]);
        } catch (Exception $e) {
            app(\App\Exceptions\Handler::class)->report($e);

            return response()->json(['result' => 'failure', 'msg' => $e->getMessage()]);
        }
    }

    public function getFloorByLocation(Request $request)
    {
        //dd($_POST['locations']);
        try {
            $location_id = $request->location ?? null;
            // dd($location_id);
            
            exit();
            return response()->json(['result' => 'success', 'data' => $assets]);
        } catch (Exception $e) {
            app(\App\Exceptions\Handler::class)->report($e);

            return response()->json(['result' => 'failure', 'msg' => $e->getMessage()]);
        }
    }
    public function ticket_list(Request $request)
    {
        //dd($_POST['locations']);
        $user = \Auth()->user();

        $ticketData = Ticket::with("division", "location",'service')
      
        ->when(!empty($request['status']), function ($query) use($request) {
            return $query->where('status',$request['status']);
        })

        ->when(!empty($request['todate']), function ($query) use($request) {
            return $query->where('start_date',$request['todate'] );
        })
        ->get();
        if($request->ajax()){
            // echo"<pre>";print_r($ticketData);die;
                $ticket_list = datatables()
                ->of($ticketData)
                ->addColumn('id', function($data){

                    return $data->id != '' ? 'T0'.$data->id : '';

                   return $data->id != '' ? $data->id : '';
               })
                ->addColumn('status', function ($data) {

                    return $data->status == '1' ? 'Open' : 'Closed';
                })
                ->addColumn('oc_status', function ($data) {

                    return ($data->oc_status == '1')  ? 'Open' : 'Closed';
                })
                ->addColumn('su_status', function ($data) {

                    return ($data->status == '0' || $data->status == '1') && $data->oc_status == '1'  ? 'Open' : 'Closed';
                })
                ->addColumn('division', function ($data) {

                    return $data->division->name;
                })
                ->addColumn('location', function ($data) {

                    return $data->location->name;
                })
              
                ->addColumn('service', function ($data) {

                    return $data->service->name;
                })
                ->addColumn('assignee', function ($data) {

                    return $data->assignee->name;
                })
                // ->addColumn('employee', function ($data) {

                //     return $data->employee->name ?? null;
                // })

                ->addColumn('action', function ($data) use ($user) {
                    $button = '';
                    // if ($user->can('edit_division')) {

                    //     $button = '<a href="/admin/brands/edit/' . $data->id . '" class="btn btn-sm btn-clean btn-icon" title="Edit"><i class="fas fa-edit text-info"></i></a>';
                    //     // $button .= '&nbsp;&nbsp;';
                    // }
                    if ($user->can('delete_division')) {
                        $button .= '<a href="javascript:;" data-id="'.$data->id.'" class="btn btn-sm btn-clean btn-icon delete_brand" title="Delete"><i class="fas fa-trash text-danger"></i></a>';
                    }

                    return $button;
                })
                ->addColumn('frequency', function ($data) use ($user) {
                    $button = '';
                    if ($user->can('edit_division')) {

                        $button = '<a href="/admin/brands/tickes/' . $data->id . '" class="btn btn-sm btn-clean btn-icon" title="Edit"><i class="fas fa-file text-info"></i></a>';
                        // $button .= '&nbsp;&nbsp;';
                    }
                    return $button;
                })
                ->addIndexColumn()
                ->rawColumns(['action', 'status', 'division', 'location', 'frequency','employee','su_status'])
                ->make(true);
   
            return $ticket_list;
        }
        return view('admin.brand.ticket_list');
      
    }

}
