<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Service;
use Session;
use Validator;
use OwenIt\Auditing\Models\Audit;


class DepartmentController extends Controller 
{
    public function __construct()
    {
        $this->middleware(function ($request, $next) {
            Session::put('active', 'department');

            return $next($request);
        });
    }
    public function department_list(Request $request){
        $user = \Auth()->user();
        if($request->ajax()){
           
            $departments = datatables()
                ->of(
                    Service::orderBy('id','desc')->get()
                )
                ->addColumn('status', function($data){

                    return $data->status == '1' ? 'Active' : 'Inactive';

                })
                ->addColumn('action', function($data) use ($user){
                    $button = '';
                    if ($user->can('edit_division')) {
                        $button = '<a href="/admin/service/edit/'.$data->id.'" class="btn btn-sm btn-clean btn-icon" title="Edit"><i class="fas fa-edit text-info"></i></a>'; 
                        // $button .= '&nbsp;&nbsp;';
                    }
                    // if ($user->can('delete_division')) {
                    //     $button .= '<a href="javascript:;" data-id="'.$data->id.'" class="btn btn-sm btn-clean btn-icon delete_department" title="Delete"><i class="fas fa-trash text-danger"></i></a>';
                    // }
                  
                    
                    return $button;           
                })
                ->addIndexColumn()
                ->rawColumns(['action','status'])
                ->make(true);

            return $departments;
        }
        return view('admin.department.list');
    }
    public function create_department(Request $request){
        return view('admin.department.create');
    }
    
    public function store_department(Request $request){
        $user_id = \Auth::user()->id;
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
            

                $department = Service::create([
                    'name'=>$request_input['name'],
                    'status'=>$request_input['status'],
                ]);
              
                $response['result'] = 'success';
                $response['msg'] = 'department created';
            }
        }
        catch(\Exception $e){
            app(\App\Exceptions\Handler::class)->report($e);
            $response['result'] = 'failure';
            $response['msg'] = $e->getMessage();
        }
 
        return response()->json($response);
    }

    public function edit_department(Request $request){

        $department = Service::findOrFail($request->id);
        return view('admin.department.edit' , compact('department'));
    }

    public function update_department(Request $request){
        $user_id = \Auth::user()->id;
        try{
            $request_input = $request->except('_token');
            $department_id = $request_input['department_id'];
            $department= Service::find($department_id);

        //    print_r($department_id);die;
           if($department->name!=$request_input['name']){
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
              //  unset($department_id);
                // print_r($department_id);die;
                $request_input['status'] = isset($request_input['status']) ? $request_input['status'] : 0;
                // print_r($department_id);die;
           Service::find($department_id)->update([
                    'name'=>$request_input['name'],
                    'status'=>$request_input['status'],
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
                
            
                Service::find($department_id)->update([
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
    public function delete_department(Request $request)
    {
        try {
            $id = $request['id'];
            if (!empty($id)) {
                $department = Service::findOrFail($id);
                
                $department->delete();

                $response['result'] = 'success';
                $response['msg'] = 'Service Deleted';
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
        $services = Service::orderBy('id', 'desc')->get();
        $output = '<html><head><style>';
        $output .= 'table {border-collapse: collapse; width: 100%;}';
        $output .= 'th, td {border: 1px solid black; padding: 8px; text-align: center;}';
        $output .= 'th {background-color: blue}';
        $output .= '</style></head><body>';
        $output .= '<table>';
        $output .= '<tr><th>S.no</th><th>Name</th><th>Status</th></tr>';
        
        foreach ($services as $key => $service) {
            $i = $key + 1;
            $output .= '<tr>';
            $output .= '<td>' . $i . '</td>';
            $output .= '<td>' . $service->name . '</td>';
            $output .= '<td>' . ($service->status == 1 ? 'Active' : 'Inactive') . '</td>';
            $output .= '</tr>';
        }
        
        $output .= '</table>';
        $output .= '</body></html>';
    
        header("Content-type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet");
        header("Content-Disposition: attachment; filename=Service_List.xls");
        header("Pragma: no-cache");
        header("Expires: 0");
    
        echo $output;
    }
    public function Logs(Request $request){
        $audits = Audit::where('auditable_type','App\Models\Service')->paginate(10);
        return view('admin.department.servicelog', compact('audits'));
    }
    public function export_logexcel(Request $request)
    {
        $audits = Audit::where('auditable_type','App\Models\Service')->get();
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
            $service = Service::find($audit->auditable_id);
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
        header("Content-Disposition: attachment; filename=Servicelog_List.xls");
        header("Pragma: no-cache");
        header("Expires: 0");
    
        echo $output;
    
    }
}





