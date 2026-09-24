<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Department;
use Illuminate\Http\Request;
use Session;
use Validator;
use App\Models\SupDept;
use OwenIt\Auditing\Models\Audit;

class SuperDepartmentController extends Controller
{
    public function __construct()
    {
        $this->middleware(function ($request, $next) {
            Session::put('active', 'supdepartment');

            return $next($request);
        });
    }

    public function supdepartment_list(Request $request){
        $user = \Auth()->user();
        if($request->ajax()){
           
            $departments = datatables()
                ->of(
                    SupDept::orderBy('id','desc')->get()
                )
                ->addColumn('departments', function ($data) {

                    $departmentIds = explode(',', $data->departments);
                    $departmentNames = [];
                    foreach ($departmentIds as $id) {
                        $departmentNames[] = getDepartmentName($id);
                    }
                    return implode(', ', $departmentNames);
                })
                ->addColumn('status', function($data){

                    return $data->status == '1' ? 'Active' : 'Inactive';

                })
                ->addColumn('action', function($data) use ($user){
                    $button = '';
                    if ($user->can('edit_division')) {
                        $button = '<a href="/admin/supdepartment/edit/'.$data->id.'" class="btn btn-sm btn-clean btn-icon" title="Edit"><i class="fas fa-edit text-info"></i></a>'; 
                       
                    }
                 
                    
                    return $button;           
                })
                ->addIndexColumn()
                ->rawColumns(['action','status'])
                ->make(true);

            return $departments;
        }
        return view('admin.supdepartment.list');
    }

   public function create_supdepartment(Request $request){
    $departments = Department::select('id', 'name')->where('status', 1)->get();
    return view('admin.supdepartment.create', ['departments' => $departments]);
}


    public function store_supdepartment(Request $request){
       
        try{
            $request_input = $request->except('_token');
          

            $rules = [
                'name' => 'required|string',
                 'status' => 'required|in:0,1',
            ];

            $messages = [
                'name.required' => 'Please enter department name',
                'status.required' => 'Please select status',
                
            ];
            $validator = Validator::make($request_input, $rules, $messages);
            if($validator->fails()){
                $response['msg'] = $validator->errors()->toArray();
                $response['result'] = 'error';
            }
            else{
                    
                $request_input['status'] = isset($request_input['status']) ? $request_input['status'] : 0;
            

                $department = SupDept::create([
                    'name'=>$request_input['name'],
                    'status'=>$request_input['status'],
                ]);
              
                $response['result'] = 'success';
                $response['msg'] = 'super department created';
            }
        }
        catch(\Exception $e){
            app(\App\Exceptions\Handler::class)->report($e);
            $response['result'] = 'failure';
            $response['msg'] = $e->getMessage();
        }
 
        return response()->json($response);
    }

    public function store_supdept(Request $request){
        $user_id = \Auth::user()->id;
        try {
            $request_input = $request->except('_token');
    
            $rules = [
                'name' => 'required|string',
                'status' => 'required|in:0,1',
            ];
    
            $messages = [
                'name.required' => 'Please enter super department name',
                'status.required' => 'Please select status',
            ];
    
            $validator = Validator::make($request_input, $rules, $messages);
            if($validator->fails()){
                $response['msg'] = $validator->errors()->toArray();
                $response['result'] = 'error';
            } else {
                $request_input['status'] = isset($request_input['status']) ? $request_input['status'] : 0;
                $departmentIds = null; // Initialize departmentIds variable
                
                if (isset($request->departments)) {
                    $departmentIds = implode(',', $request->departments);
                }
    
                $department = SupDept::create([
                    'name' => $request_input['name'],
                    'status' => $request_input['status'],
                    'departments' => $departmentIds,
                ]);

    
                $response['result'] = 'success';
                $response['msg'] = 'super department created';
            }
        } catch(\Exception $e) {
            app(\App\Exceptions\Handler::class)->report($e);
            $response['result'] = 'failure';
            $response['msg'] = $e->getMessage();
        }
    
        return response()->json($response);
    }
    

    public function edit_supdepartment(Request $request){

        $supdepart = SupDept::findOrFail($request->id);
        $departments = Department::select('id', 'name')->where('status', 1)->get();
        $selectedDepartments = explode(',', $supdepart->departments);
        return view('admin.supdepartment.edit' , compact('supdepart','departments','selectedDepartments'));
    }

    public function update_supdepartment(Request $request){
   
        $user_id = \Auth::user()->id;
        try{
            $request_input = $request->except('_token');
            $department_id = $request_input['department_id'];
            $department= SupDept::find($department_id);


       
           if($department->name == $request_input['name']){
            $rules = [
                'name' => 'required',
                'status' => 'required|in:0,1',];

            $messages = [
                'name.required' => 'Please enter service name',
                'status.required' => 'Please select status',

            ];

            $validator = Validator::make($request_input, $rules, $messages);
            if($validator->fails()){
                $response['msg'] = $validator->errors()->toArray();
                $response['result'] = 'error';
            }
            else{
                $department_id = $department_id;
                if (isset($request->departments)) {
                    $departmentIds = implode(',', $request->departments);
                }
             
                $request_input['status'] = isset($request_input['status']) ? $request_input['status'] : 0;
                
           SupDept::find($department_id)->update([
                    'name'=>$request_input['name'],
                    'status'=>$request_input['status'],
                    'departments' => $departmentIds ?? null,
                ]);
             
                
                $response['result'] = 'success';
                $response['msg'] = 'Service Updated';
            }

           }else{
            $rules = [
                'name' => 'required|string',
                'status' => 'required|in:0,1',
            ];

            $messages = [
                'name.required' => 'Please enter service name',
                'status.required' => 'Please select status',

            ];

            $validator = Validator::make($request_input, $rules, $messages);
            if($validator->fails()){
                $response['msg'] = $validator->errors()->toArray();
                $response['result'] = 'error';
            }
            else{
                $department_id = $request_input['department_id'];
                unset($request_input['department_id']);

                $request_input['status'] = isset($request_input['status']) ? $request_input['status'] : 0;
                
            
                SupDept::find($department_id)->update([
                    'name'=>$request_input['name'],
                    'status'=>$request_input['status'],
                ]);
                
                $response['result'] = 'success';
                $response['msg'] = 'Service Updated';
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

    public function export_excel(Request $request)
    {
        $supdeps = SupDept::orderBy('id', 'desc')->get();
       
        $output = '<html><head><style>';
        $output .= 'table {border-collapse: collapse; width: 100%;}';
        $output .= 'th, td {border: 1px solid black; padding: 8px; text-align: center;}';
        $output .= 'th {background-color: blue}';
        $output .= '</style></head><body>';
        $output .= '<table>';
        $output .= '<tr><th>S.no</th><th>Department Name</th><th>Sub-Department Name</th><th>Status</th></tr>';
        
        foreach ($supdeps as $key => $supdep) {
            $i = $key + 1;
            $output .= '<tr>';
            $output .= '<td>' . $i . '</td>';
            $output .= '<td>' . $supdep->name . '</td>';
            $subDep = explode(',', $supdep->departments);
            $SubDepName = [];
            foreach($subDep as $sub){
                $SubDepName[] = getDepartmentName($sub);
            }
            $output .= '<td>' . implode(',',$SubDepName) . '</td>';
            $output .= '<td>' . ($supdep->status == 1 ? 'Active' : 'Inactive') . '</td>';
            $output .= '</tr>';
        }
        
        $output .= '</table>';
        $output .= '</body></html>';
    
        header("Content-type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet");
        header("Content-Disposition: attachment; filename=Department_List.xls");
        header("Pragma: no-cache");
        header("Expires: 0");
    
        echo $output;
    }
    public function Logs(Request $request){
        $audits = Audit::where('auditable_type','App\Models\SupDept')->paginate(10);
        return view('admin.supdepartment.superdeplog', compact('audits'));
    } 

    public function export_logexcel(Request $request)
    {
        $audits = Audit::where('auditable_type','App\Models\SupDept')->get();
        $output = '<html><head><style>';
        $output .= 'table {border-collapse: collapse; width: 100%;}';
        $output .= 'th, td {border: 1px solid black; padding: 8px; text-align: center;}';
        $output .= 'th {background-color: blue}';
        $output .= '</style></head><body>';
        $output .= '<table>';
        $output .= '<tr>
        <th>S.No</th>
        <th>Event</th>
        <th>Audited By</th>
        <th >Audited At</th>
        <th></th>
        <th>Name</th>
        <th>Status</th></tr>';

        foreach($audits as $key => $audit){
            $newvalue =  $audit->new_values ?? [];
            $oldvalue =  $audit->old_values ?? [];
            $supdept = SupDept::find($audit->auditable_id);
            $userName = optional($audit->user)->name ?? ' ';
            $date = $audit->created_at->format('d-M-Y h:i:s A');
            $event = ucfirst($audit->event);
            $i = $key + 1;

            $output .= '<tr>';
            $output .= '<td rowspan="2">' . $i . '</td>';
            $output .= '<td rowspan="2">' . $event . '</td>'; 
            $output .= '<td rowspan="2">' . $userName . '</td>'; 
            $output .= '<td rowspan="2">' . $date . '</td>';
            $output .= '<td><b>'. "Old Value". "</b></td>";
            if( $event == 'Updated'){
            $output .= '<td>' . data_get($oldvalue, 'name') ?? '' . '</td>'; 

                if (isset($oldvalue['status'])){
                    if($oldvalue['status']==0){
                        $output .= '<td>'."Inactive" .'</td>';
                    }else{
                        $output .= '<td>'."Active" .'</td>';
                    }
                }else{
                    $output .= '<td></td>';
                }
            }else{
                $output .= '<td colspan="2"></td>';  
            }

            $output .= '</tr>';
            $output .= '<tr>';
            $output .= '<td><b>'. "New Value". "</b></td>";
            $output .= '<td>' . data_get($newvalue, 'name') ?? '' . '</td>'; 

                if (isset($newvalue['status'])){
                    if($newvalue['status']==0){
                        $output .= '<td>'."Inactive" .'</td>';
                    }else{
                        $output .= '<td>'."Active" .'</td>';
                    }
                }else{
                    $output .= '<td></td>';
                }
           
            $output .= '</tr>';
        }
                                             
        $output .= '</table>';
        $output .= '</body></html>';
        header("Content-type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet");
        header("Content-Disposition: attachment; filename=Departmentlog_List.xls");
        header("Pragma: no-cache");
        header("Expires: 0");
    
        echo $output;
    
    }
    
}
