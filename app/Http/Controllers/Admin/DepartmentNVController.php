<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Department;
use App\Models\Employee;
use Session;
use Validator;
use OwenIt\Auditing\Models\Audit;


class DepartmentNVController extends Controller 
{
    public function __construct()
    {
        $this->middleware(function ($request, $next) {
            Session::put('active', 'departmentNv');

            return $next($request);
        });
    }
    public function department_nv_list(Request $request){
        $user = \Auth()->user();
        $dep =  Department::with('employee','deprew1','deprew2','deprew3','deprew4','groupcio')->orderBy('id','desc')->get();
       
        if($request->ajax()){
           
            $departments = datatables()
                ->of(
                   $dep
                )
            ->addColumn('status', function($data){
                    return $data->status == '1' ? 'Active' : 'Inactive';
                })
              
            ->addColumn('dep_rew1', function($data){
                return $data->dep_rew1 ? getUserName($data->dep_rew1) : " ";

                })
            ->addColumn('dep_rew2', function($data){
                return $data->dep_rew2 ? getUserName($data->dep_rew2) : " ";

                })
            ->addColumn('dep_rew3', function($data){
                return $data->dep_rew3 ? getUserName($data->dep_rew3) : " ";
                })
            ->addColumn('dep_rew4', function($data){
                return $data->dep_rew4 ? getUserName($data->dep_rew4) : " ";
                })
            ->addColumn('dep_hod', function($data){
                return $data->dep_hod ? getUserName($data->dep_hod) : " ";

                })
            ->addColumn('group_cio', function($data){
                return $data->group_cio ? getUserName($data->group_cio) : " ";

                })
               
                ->addColumn('action', function($data) use ($user){
                    $button = '';
                    if ($user->can('edit_division')) {
                        $button = '<a href="/admin/department/edit/'.$data->id.'" class="btn btn-sm btn-clean btn-icon" title="Edit"><i class="fas fa-edit text-info"></i></a>'; 
                       
                    }
                     return $button;           
                })
                ->addIndexColumn()
                ->rawColumns(['action','dep_hod','dep_rew1','dep_rew2','dep_rew3','dep_rew4','status','group_cio'])
                ->make(true);

            return $departments;
        }
        return view('admin.departmentNV.list');
    }
    public function create_nv_department(Request $request){
       
        return view('admin.departmentNV.create');
    }
    
    public function store_nv_department(Request $request)
    {
        try {

            $request_input = $request->except('_token');

            $rules = [
                'name'   => 'required|string',
                'status' => 'required|in:0,1',
            ];

            $messages = [
                'name.required'   => 'Please enter department name',
                'status.required' => 'Please select status',
            ];

            $validator = Validator::make($request_input, $rules, $messages);

            if ($validator->fails()) {

                return response()->json([
                    'result' => 'error',
                    'msg'    => $validator->errors()->toArray()
                ]);
            }

            $now = now();

            Department::create([
                'name'      => $request_input['name'],
                'status'    => $request_input['status'] ?? 0,
                'group_cio' => $request_input['group_cio'] ?? null,
                'dep_hod'   => $request_input['dep_hod'] ?? null,

                'dep_rew1'  => $request_input['dep_rew1'] ?? null,
                'dep_rew2'  => $request_input['dep_rew2'] ?? null,
                'dep_rew3'  => $request_input['dep_rew3'] ?? null,
                'dep_rew4'  => $request_input['dep_rew4'] ?? null,

                'dep_rew1_added_at' => !empty($request_input['dep_rew1']) ? $now : null,
                'dep_rew2_added_at' => !empty($request_input['dep_rew2']) ? $now : null,
                'dep_rew3_added_at' => !empty($request_input['dep_rew3']) ? $now : null,
                'dep_rew4_added_at' => !empty($request_input['dep_rew4']) ? $now : null,

                'prefix' => strtoupper($request->input('name')) ?? null,
            ]);

            return response()->json([
                'result' => 'success',
                'msg'    => 'Department created'
            ]);

        } catch (\Exception $e) {

            app(\App\Exceptions\Handler::class)->report($e);

            return response()->json([
                'result' => 'failure',
                'msg'    => $e->getMessage()
            ]);
        }
    }

    public function edit_nv_department(Request $request) {
        $department = Department::findOrFail($request->id);
        $employees = Employee::where('role_id', 11)->where('status', 1)->get();
    
        $emp_hods = [];
    
        foreach ($employees as $employee) {
            $decoded_department_ids = explode(',', $employee->department_id);
            if (is_array($decoded_department_ids)) {
                if (in_array($request->id, $decoded_department_ids, true)) {
                    $emp_hods[] = $employee;
                }
            } else {
                if ($decoded_department_ids == $request->id) {
                    $emp_hods[] = $employee;
                }
            }
        }
    
        return view('admin.departmentNV.edit', compact('department', 'emp_hods'));
    }    

    public function update_nv_department(Request $request)
    {
        try {

            $request_input = $request->except('_token');

            $department_id = $request_input['department_id'];
            $department = Department::findOrFail($department_id);

            // Validation rules
            $rules = [
                'name'   => 'required|string',
                'status' => 'required|in:0,1',
            ];

            $messages = [
                'name.required'   => 'Please enter Department name',
                'status.required' => 'Please select status',
            ];

            $validator = Validator::make($request_input, $rules, $messages);

            if ($validator->fails()) {

                return response()->json([
                    'result' => 'error',
                    'msg'    => $validator->errors()->toArray()
                ]);
            }

            $updateData = [
                'name'      => $request_input['name'],
                'status'    => $request_input['status'] ?? 0,
                'group_cio' => $request_input['group_cio'] ?? null,
                'dep_hod'   => $request_input['dep_hod'] ?? null,
                'prefix'    => strtoupper($request->input('name')) ?? null,
            ];

            /*
            |--------------------------------------------------------------------------
            | Reviewer 1
            |--------------------------------------------------------------------------
            */
            if ($department->dep_rew1 != ($request_input['dep_rew1'] ?? null)) {

                $updateData['dep_rew1'] = $request_input['dep_rew1'] ?? null;
                $updateData['dep_rew1_added_at'] = now();
            }

            /*
            |--------------------------------------------------------------------------
            | Reviewer 2
            |--------------------------------------------------------------------------
            */
            if ($department->dep_rew2 != ($request_input['dep_rew2'] ?? null)) {

                $updateData['dep_rew2'] = $request_input['dep_rew2'] ?? null;
                $updateData['dep_rew2_added_at'] = now();
            }

            /*
            |--------------------------------------------------------------------------
            | Reviewer 3
            |--------------------------------------------------------------------------
            */
            if ($department->dep_rew3 != ($request_input['dep_rew3'] ?? null)) {

                $updateData['dep_rew3'] = $request_input['dep_rew3'] ?? null;
                $updateData['dep_rew3_added_at'] = now();
            }

            /*
            |--------------------------------------------------------------------------
            | Reviewer 4
            |--------------------------------------------------------------------------
            */
            if ($department->dep_rew4 != ($request_input['dep_rew4'] ?? null)) {

                $updateData['dep_rew4'] = $request_input['dep_rew4'] ?? null;
                $updateData['dep_rew4_added_at'] = now();
            }

            // Update department
            $department->update($updateData);

            return response()->json([
                'result' => 'success',
                'msg'    => 'Department Updated'
            ]);

        } catch (\Exception $e) {

            app(\App\Exceptions\Handler::class)->report($e);

            return response()->json([
                'result' => 'failure',
                'msg'    => $e->getMessage()
            ]);
        }
    }

    public function delete_nv_department(Request $request)
    {
        try {
            $id = $request['id'];
            if (!empty($id)) {
                $department = Department::findOrFail($id);
                
                $department->delete();

                $response['result'] = 'success';
                $response['msg'] = 'Department Deleted';
            } else {
                $response['result'] = 'failure';
                $response['msg'] = 'Select Department';
            }
        } catch (Exception $e) {
            app(\App\Exceptions\Handler::class)->report($e);
            $response['result'] = 'failure';
            $response['msg'] = $e->getMessage();
        }

        return response()->json($response);
    }
    public function export_excel(Request $request)
    {
        $departments = Department::with('employee','deprew1','deprew2','deprew3','deprew4','groupcio')->orderBy('id','desc')->get();
        $output = '<html><head><style>';
        $output .= 'table {border-collapse: collapse; width: 100%;}';
        $output .= 'th, td {border: 1px solid black; padding: 8px; text-align: center;}';
        $output .= 'th {background-color: blue}';
        $output .= '</style></head><body>';
        $output .= '<table>';
        $output .= '<tr><th>S.no</th><th>Name</th><th>Reviewer 1</th><th>Reviewer 2</th><th>Reviewer 3</th><th>Reviewer 4</th><th>Department HOD</th><th>Group Head</th><th>Status</th></tr>';
        
        foreach ($departments as $key => $department) {
            $i = $key + 1;
            $output .= '<tr>';
            $output .= '<td>' . $i . '</td>';
            $output .= '<td>' . $department->name . '</td>';
          
            
              if($department->dep_rew1 == null){
                $output .= '<td>' . ''. '</td>';
              }else{
                $output .= '<td>' .getUserName($department->dep_rew1) . '</td>';
              }
           
              if($department->dep_rew2 == null){
                $output .= '<td>' . ''. '</td>';
              }else{
                $output .= '<td>' .getUserName($department->dep_rew2) . '</td>';
              }
           
              if($department->dep_rew3 == null){
                $output .= '<td>' . ''. '</td>';
              }else{
                $output .= '<td>' .getUserName($department->dep_rew3) . '</td>';
              }
           
              if($department->dep_rew4 == null){
                $output .= '<td>' . ''. '</td>';
              }else{
                $output .= '<td>' .getUserName($department->dep_rew4) . '</td>';
              }
              if($department->dep_hod == null){
                $output .= '<td>' . ''. '</td>';
              }else{
                $output .= '<td>' .getUserName($department->dep_hod) . '</td>';
              }
              if($department->group_cio == null){
                $output .= '<td>' . ''. '</td>';
              }else{
                $output .= '<td>' .getUserName($department->group_cio) . '</td>';
              }
           
           
            $output .= '<td>' . ($department->status == 1 ? 'Active' : 'Inactive') . '</td>';
            $output .= '</tr>';
        }
        
        $output .= '</table>';
        $output .= '</body></html>';
    
        header("Content-type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet");
        header("Content-Disposition: attachment; filename=Sub-Department_List.xls");
        header("Pragma: no-cache");
        header("Expires: 0");
    
        echo $output;
    }

    public function Logs(Request $request){
        $audits = Audit::where('auditable_type','App\Models\Department')->paginate(10);
        return view('admin.departmentNV.logdept', compact('audits'));
    } 

    public function export_logexcel(Request $request)
    {
       $audits = Audit::where('auditable_type','App\Models\Department')->get();
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
       <th>Sub-Department Name</th>
       <th>Reviewer 1</th>
       <th>Reviewer 2</th>
       <th>Reviewer 3</th>
       <th>Reviewer 4</th>
       <th>HOD</th>
       <th>Group Head</th>
       <th>Status</th></tr>';
        
       foreach($audits as $key => $audit){
        $newvalue =  $audit->new_values ?? [];
        $oldvalue =  $audit->old_values ?? [];
        $department = Department::find($audit->auditable_id);
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
                $output .= '<td>' . getUserName(data_get($oldvalue, 'dep_rew1')) ?? '' . '</td>'; 
                $output .= '<td>' . getUserName(data_get($oldvalue, 'dep_rew2')) ?? '' . '</td>'; 
                $output .= '<td>' . getUserName(data_get($oldvalue, 'dep_rew3')) ?? '' . '</td>'; 
                $output .= '<td>' . getUserName(data_get($oldvalue, 'dep_rew4')) ?? '' . '</td>'; 
                $output .= '<td>' . getUserName(data_get($oldvalue, 'dep_hod')) ?? '' . '</td>';
                $output .= '<td>' . getUserName(data_get($oldvalue, 'group_cio')) ?? '' . '</td>';  
               
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
                $output .= '<td colspan="8"></td>';  
            }

            $output .= '</tr>';
            $output .= '<tr>';
            $output .= '<td><b>'. "New Value". "</b></td>";
            $output .= '<td>' . data_get($newvalue, 'name') ?? '' . '</td>';
            $output .= '<td>' . getUserName(data_get($newvalue, 'dep_rew1')) ?? '' . '</td>'; 
            $output .= '<td>' . getUserName(data_get($newvalue, 'dep_rew2')) ?? '' . '</td>'; 
            $output .= '<td>' . getUserName(data_get($newvalue, 'dep_rew3')) ?? '' . '</td>'; 
            $output .= '<td>' . getUserName(data_get($newvalue, 'dep_rew4')) ?? '' . '</td>'; 
            $output .= '<td>' . getUserName(data_get($newvalue, 'dep_hod')) ?? '' . '</td>';
            $output .= '<td>' . getUserName(data_get($newvalue, 'group_cio')) ?? '' . '</td>';  

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
        header("Content-Disposition: attachment; filename=SubDepartmentlog_List.xls");
        header("Pragma: no-cache");
        header("Expires: 0");
    
        echo $output;
    
    }
}





