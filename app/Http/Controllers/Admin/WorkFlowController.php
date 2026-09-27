<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\ActivityLogService;
use App\Models\Workflow;
use App\Models\Department;
use App\Models\Employee;
use Session;
use Validator;
use OwenIt\Auditing\Models\Audit;
use DB;

class WorkFlowController extends Controller
{
    //
    public function __construct(ActivityLogService $Logger)
    {
        $this->logger = $Logger;
        $this->middleware(function ($request, $next) {
            Session::put('active', 'workflow');

            return $next($request);
        });
    }

    public function workFlow_list(Request $request){

        $user = \Auth()->user();
 
        $workflow =  Workflow::with('workflow_dep','workflow_rew1','workflow_rew2','workflow_rew3','workflow_rew4','workflow_app')->get();
        //  echo($workflow);
        if($request->ajax()){
           
            $workflows = datatables()
                ->of(
                   $workflow
                )
                ->addColumn('work_dep', function ($data) {

                    return $data->workflow_dep->name;
                })
                ->addColumn('work_rew1', function($data){
                    if($data->work_rew1 == null){
                                return " ";
                              }
                                return getUserName($data->work_rew1);
    
                    })
                    ->addColumn('work_rew2', function($data){
                        if($data->work_rew2 == null){
                                    return " ";
                                  }
                                    return getUserName($data->work_rew2);
        
                        })
                        ->addColumn('work_rew3', function($data){
                            if($data->work_rew3 == null){
                                        return " ";
                                      }
                                        return getUserName($data->work_rew3);
            
                            })
                            ->addColumn('work_rew4', function($data){
                                if($data->work_rew4 == null){
                                            return " ";
                                          }
                                            return getUserName($data->work_rew4);
                
                                })
                                ->addColumn('approver', function($data){
                                    if($data->approver == null){
                                                return " ";
                                              }
                                                return getUserName($data->approver);
                    
                                    })
                                    ->addColumn('sr_no', function($data){
                                      return $data->sr_no;
                      
                                      })
                                    ->addColumn('status', function($data){
                                      if($data->status == 1){
                                                  return "Active";
                                                }
                                                return "Inactive";
                      
                                      })
             
                ->addColumn('action', function ($data) use ($user) {
                    $button = '<a href="/admin/Workflow/edit/' . $data->id . '" class="btn btn-sm btn-clean btn-icon" title="Edit"><i class="fas fa-edit text-info"></i></a>';
                
                    return $button;
                })
                ->addIndexColumn()
                ->rawColumns(['action','work_dep','work_rew1','work_rew2','work_rew3','work_rew4','approver'])
                ->make(true);

            return $workflows;
        }
     
        return view('admin.workflow.list');
    }


    public function create_workFlow(Request $request){
       
        $departments = Department::select('id', 'name')->where('status', 1)->get();

        return view('admin.workflow.create',compact('departments'));

    }


    public function store_workflow(Request $request)
    {
      $user_id = \Auth::user()->id;
      $sr_no = $request->sr_no;
      $department_id = $request->work_dep;
  
      $existingSrNo = Workflow::where('sr_no', $sr_no)->first();
      // $totalSrNo = Workflow::max('sr_no');

      // if(($sr_no > $totalSrNo)){
      //   return response()->json([
      //     'exists' => true,
      //     'message' => 'Please change the Sr No of CEO At last stage',
      // ]);
      // }elseif(($sr_no == $totalSrNo) && ($existingSrNo)){
      //     return response()->json([
      //       'exists' => true,
      //       'message' => 'This Sr No exists in CEO stage',
      //   ]);

      //   }else
        if($existingSrNo){
          return response()->json([
            'exists' => true,
            'message' => 'Already Exist Serial Number',
        ]);
        }
        try {
            $request_input = $request->except('_token');
            $rules = [
          
            ];

            $messages = [
         
            ];
            $validator = Validator::make($request_input, $rules, $messages);
            if ($validator->fails()) {
                $response['msg'] = $validator->errors()->toArray();
                $response['result'] = 'error';
            } else {
                
                if (empty($request['workflow_id'])) {

                $workflow = Workflow::create([
                    'work_dep' => $request_input['work_dep'],
                    'work_rew1' => $request_input['work_rew1'],
                    'work_rew2' => $request_input['work_rew2'],
                    'work_rew3' => $request_input['work_rew3'],
                    'work_rew4' => $request_input['work_rew4'],
                    'approver' => $request_input['approver'],
                    'sr_no' => $request_input['sr_no'],
                    'status' => $request_input['status'],
                
                ]);
          
                $response['result'] = 'success';
                $response['msg'] = 'CAPEX Workflow Saved Successfully';
           
        } 
          
  
        }
    

        } catch (\Exception $e) {
            app(\App\Exceptions\Handler::class)->report($e);
            $response['result'] = 'failure';
            $response['msg'] = $e->getMessage();
        }

        return response()->json($response);
    }

    public function edit_workFlow(Request $request)
    
    {
        $workflow = Workflow::findOrFail($request->id);
        $departments = Department::select('id', 'name')->where('status', 1)->get();
        return view('admin.workflow.edit',compact('workflow','departments')); 
    }

    public function update_workflow(Request $request)
    {
      $user_id = \Auth::user()->id;

        try {
          $workflow = Workflow::find($request['workflow_id']);

          $sr_no = $request->sr_no;
          $department_id = $request->work_dep;
      
          $existingSrNo = Workflow::where('sr_no', $sr_no)->first();
          // $totalSrNo = Workflow::max('sr_no');
    
            // if(($sr_no > $totalSrNo)){
            //   return response()->json([
            //     'exists' => true,
            //     'message' => 'Please change the Sr No of CEO At last stage',
            // ]);
            // }elseif(($sr_no == $totalSrNo) && ($sr_no != $workflow->sr_no)){
            //     return response()->json([
            //       'exists' => true,
            //       'message' => 'This Sr No exists in CEO stage',
            //   ]);
      
            // }else
            if(($sr_no != $workflow->sr_no) && ($existingSrNo)){
              return response()->json([
                'exists' => true,
                'message' => 'Already Exist Serial Number',
            ]);
          } 

          $nv_workflow = DB::table('capex_workflows_status')
          ->where('sr_no', $workflow->sr_no)->where('nv_stage_status',0)
          ->where('department_id', $workflow->work_dep)
          ->where('nv_budget_type', 'CAPEX')
          ->exists(); 

        if ($nv_workflow) {
            return response()->json([
                'exists' => true,
                'message' => 'Cannot proceed to update CAPEX Workflow. There is already pending NVs for this department and stage.'
            ]);
        }

            $request_input = $request->except('_token');
            
          

            $rules = [
          
            ];

            $messages = [
         
            ];
            $validator = Validator::make($request_input, $rules, $messages);
            if ($validator->fails()) {
                $response['msg'] = $validator->errors()->toArray();
                $response['result'] = 'error';
            } else {
                
                $workflow_id = $request['workflow_id'];
                Workflow::find($workflow_id)->update([
                    'work_dep' => $request_input['work_dep'],
                    'work_rew1' => $request_input['work_rew1']?? null,
                    'work_rew2' => $request_input['work_rew2']?? null,
                    'work_rew3' => $request_input['work_rew3']?? null,
                    'work_rew4' => $request_input['work_rew4']?? null,
                    'approver' => $request_input['approver']?? null,
                    'sr_no' => $request_input['sr_no'],
                    'status' => $request_input['status'],
                ]);
              
                $response['result'] = 'success';
                $response['msg'] = 'CAPEX Workflow Updated Successfully';
           
        } 

        } catch (\Exception $e) {
            app(\App\Exceptions\Handler::class)->report($e);
            $response['result'] = 'failure';
            $response['msg'] = $e->getMessage();
        }

        return response()->json($response);
       
    }

    public function changeUser(Request $request)
    {

    try {
        $dep_id = $request->dep_id ?? null;

        $employees = Employee::select('user_id', 'name')->where('department_id', $dep_id)->where('role_id',11)->where('status', 1)->orderBy('id', 'desc')->get()->toArray();

        return response()->json(['result' => 'success', 'data' => $employees]);
    } catch (Exception $e) {
        app(\App\Exceptions\Handler::class)->report($e);

        return response()->json(['result' => 'failure', 'msg' => $e->getMessage()]);
    }
}

public function export_excel(Request $request)
{
    $nv_workflows =  Workflow::orderBy('id', 'asc')->get();
    $output = '<html><head><style>';
    $output .= 'table {border-collapse: collapse; width: 100%;}';
    $output .= 'th, td {border: 1px solid black; padding: 8px; text-align: center;}';
    $output .= 'th {background-color: blue}';
    $output .= '</style></head><body>';
    $output .= '<table>';
    $output .= '<tr><th>S.no</th><th>Department</th><th>Reviewer 1</th><th>Reviewer 2</th><th>Reviewer 3</th><th>Reviewer 4</th><th>Approver</th></tr>';
    
    foreach ($nv_workflows as $key => $nv_workflows) {
        $i = $key + 1;
        $output .= '<tr>';
        $output .= '<td>' . $i . '</td>';
        $output .= '<td>' .$nv_workflows->workflow_dep->name . '</td>';
        if($nv_workflows->work_rew1 == null){
            $output .= '<td>' .'' . '</td>';
          }else{
            $output .= '<td>' .getUserName($nv_workflows->work_rew1) . '</td>';
          }
          if($nv_workflows->work_rew2 == null){
            $output .= '<td>' .'' . '</td>';
          }else{
            $output .= '<td>' .getUserName($nv_workflows->work_rew2) . '</td>';
          }
          if($nv_workflows->work_rew3 == null){
            $output .= '<td>' .'' . '</td>';
          }else{
            $output .= '<td>' .getUserName($nv_workflows->work_rew3) . '</td>';
          }
          if($nv_workflows->work_rew4 == null){
            $output .= '<td>' .'' . '</td>';
          }else{
            $output .= '<td>' .getUserName($nv_workflows->work_rew4) . '</td>';
          }
          if($nv_workflows->approver == null){
            $output .= '<td>' .'' . '</td>';
          }else{
            $output .= '<td>' .getUserName($nv_workflows->approver) . '</td>';
          }
        $output .= '</tr>';
    }
    
    $output .= '</table>';
    $output .= '</body></html>';

    header("Content-type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet");
    header("Content-Disposition: attachment; filename=CAPEXWokflow_List.xls");
    header("Pragma: no-cache");
    header("Expires: 0");

    echo $output;
}
public function Logs(Request $request){
  $audits = Audit::where('auditable_type','App\Models\Workflow')->paginate(10);
  return view('admin.workflow.log_nv', compact('audits'));
}
public function export_logexcel(Request $request)
{
  $audits = Audit::where('auditable_type','App\Models\Workflow')->get();
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
  <th>Audited At</th>
  <th></th>
  <th>Sub-Department Name</th>
  <th>Reviewer 1</th>
  <th>Reviewer 2</th>
  <th>Reviewer 3</th>
  <th>Reviewer 4</th>
  <th>Approver</th>';
   
  foreach($audits as $key => $audit){
   $newvalue =  $audit->new_values ?? [];
   $oldvalue =  $audit->old_values ?? [];
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
           $output .= '<td>' . getDepartmentName(data_get($oldvalue, 'work_dep')) ?? '' . '</td>'; 
           $output .= '<td>' . getUserName(data_get($oldvalue, 'work_rew1')) ?? '' . '</td>'; 
           $output .= '<td>' . getUserName(data_get($oldvalue, 'work_rew2')) ?? '' . '</td>'; 
           $output .= '<td>' . getUserName(data_get($oldvalue, 'work_rew3')) ?? '' . '</td>'; 
           $output .= '<td>' . getUserName(data_get($oldvalue, 'work_rew4')) ?? '' . '</td>'; 
           $output .= '<td>' . getUserName(data_get($oldvalue, 'approver')) ?? '' . '</td>';
         
       }else{
           $output .= '<td colspan="6"></td>';  
       }

       $output .= '</tr>';
       $output .= '<tr>';
       $output .= '<td><b>'. "New Value". "</b></td>";
       $output .= '<td>' . getDepartmentName(data_get($newvalue, 'work_dep')) ?? '' . '</td>';
       $output .= '<td>' . getUserName(data_get($newvalue, 'work_rew1')) ?? '' . '</td>'; 
       $output .= '<td>' . getUserName(data_get($newvalue, 'work_rew2')) ?? '' . '</td>'; 
       $output .= '<td>' . getUserName(data_get($newvalue, 'work_rew3')) ?? '' . '</td>'; 
       $output .= '<td>' . getUserName(data_get($newvalue, 'work_rew4')) ?? '' . '</td>'; 
       $output .= '<td>' . getUserName(data_get($newvalue, 'approver')) ?? '' . '</td>';
       $output .= '</tr>';
   }
    
    $output .= '</table>';
    $output .= '</body></html>';

    header("Content-type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet");
    header("Content-Disposition: attachment; filename=LogCAPEXWorkflow.xls");
    header("Pragma: no-cache");
    header("Expires: 0");

    echo $output;
}
}
