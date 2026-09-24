<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Session;
use App\Models\SupDept;
use Validator;
use App\Models\Department;
use App\Models\Opex;
use App\Services\ExcelImportService;
use App\Models\DummyDepartment;
use Illuminate\Support\Facades\Schema;
use App\Services\ActivityLogService;
use App\Models\budget;
use Carbon\Carbon;
use App\Models\NeedValidation;
use File;
use App\Models\OpexBudget;
use OwenIt\Auditing\Models\Audit;

class OpexController extends Controller
{
    private $logger;
    public function __construct(ActivityLogService $Logger)
    {
        $this->logger = $Logger;
        $this->middleware(function ($request, $next) {
            Session::put('active', 'opex');

            return $next($request);
        });
    }
    public function opex_otp()
    {
        return view('admin.opex.opex_otp_verify');
    }
    public function opex_list(Request $request)
    {
        $user = \Auth()->user();
      
        $opex = Opex::with('department', 'subdepartment','superdep')->select('super_department','department_id','expenses_head',
        'activity','initial_approved_budget','id')->orderBy('id', 'desc')->get();
       
        return view('admin.opex.list',["opex"=>$opex]);
    }
    public function create_opex(Request $request)
    {
        $department = Department::select('id', 'name')->where('status', 1)->get();
        $supdepart = SupDept::select('id', 'name')->where('status', 1)->get();
        $selectedDepartments=[];
        $opex = Opex::get();
        if(!empty($opex)){
            $opex =Opex::select('department_id')->get(); 
            foreach($opex as $opex){
                array_push($selectedDepartments, $opex["department_id"]);
            }
        }
        return view('admin.opex.create', compact('department','supdepart','selectedDepartments'));
    }
    public function store_opex(Request $request)
    {
        $user_id = \Auth::user()->id;
       
        $budget = json_encode($request->initial_approved_budget);
        $transfer = $request->transfer_budget;
        $budgetArray = is_array($transfer) ? $transfer : [$transfer];
        $implodedBudgets = implode(',', $budgetArray);
        $additional = $request->additional_budget;
        $budgetArray1 = is_array($additional) ? $additional : [$additional];
        $implodedBudgets1 = implode(',', $budgetArray1);
        $revised = $request->revised_budget;
        $budgetArray2 = is_array($revised) ? $revised : [$revised];
        $implodedBudgets2 = implode(',', $budgetArray2);
        $budget1 = array($implodedBudgets);
        $budget2 = array($implodedBudgets1);
       $budget3 = array($implodedBudgets2);
     
        $budgetYearArr = $request->years;
        $revised_budget = $request->revised_budget;

        try {
            $request_input = $request->except('_token');

            $rules = [
                'department_id' => 'required|unique:opex,department_id',
                'expenses_head' => 'required|string',
            ];

            $messages = [
                'department_id.required' => 'Please select department',
                'department_id.unique' => 'This department already exists in OPEX',
                'expenses_head.required' => 'Please enter expense head',
            ];

            $validator = Validator::make($request_input, $rules, $messages);

            if ($validator->fails()) {
                return response()->json([
                    'result' => 'error',
                    'msg' => $validator->errors()->toArray()
                ]);
            } else {
                $data = [     
                    'super_department' => $request_input['super_department'],
                    'department_id' => $request_input['department_id'],
                    'expenses_head' => $request_input['expenses_head'],
                    'activity' => $request_input['activity'],
                    'initial_approved_budget' =>  $budget,
                    'transfer_budget' => $budget1[0],
                    'additional_budget'=> $budget2[0],
                    'revised_budget'=>$budget3[0],
                ];
                $opex = Opex::create($data);
                if($opex){
               
                foreach ($budgetYearArr as $key => $value) 
                {
                    $data_budget  =[
                        'opex_id' => $opex->id ?? '',
                        'department_id' => $opex->department_id ?? '',
                        'fiscal_year' => $value ?? '',
                        'revised_budget' => $revised_budget[$key] ?? '',
                       
                    ];
                    $yearBudget = OpexBudget::create($data_budget);
                }
            }
                $response['result'] = 'success';
                $response['msg'] = 'opex created';
            }
        } catch (\Exception $e) {
            app(\App\Exceptions\Handler::class)->report($e);
            $response['result'] = 'failure';
            $response['msg'] = $e->getMessage();
        }
        $activityLogService = new ActivityLogService();
        $activity = "Some activity description";
        $activityLogService->log($request, $activity);
        return response()->json($response);
    }

    public function edit_opex(Request $request)
    {
        $opex = Opex::findOrFail($request->id);
        $numeric_value=explode(',', $opex->transfer_budget);
        $department = Department::select('id', 'name')->where('status', 1)->get();
        $supdepart = SupDept::select('id', 'name')->where('status', 1)->get();
        $transfer_budget = explode(',',$opex->transfer_budget);
        $additional_budget = explode(',',$opex->additional_budget);


        $emp_dept = Opex::join('superdepartment','superdepartment.id','=','opex.super_department')->where('opex.id',$request->id)->first();
        $selectedDepartments = explode(',', $opex->department_id);
        $supdepts122 = SupDept::select('id', 'name','departments')->where('id',$emp_dept->super_department)->where('status', 1)->get();
        $supdepts = SupDept::select('id', 'name','departments')->where('status', 1)->get();
        foreach($supdepts122 as $dept)
        {
            $map_dept = explode(',',$dept->departments,);
            
        }
        $dept_name = Department::select('id', 'name')->where('status', 1)->whereIn('id',$map_dept)->get();

        $currentYear = date('y');
        $currentMonth = date('m');
        if ($currentMonth >= 4) {
            $startYear = $currentYear;
        } else {
            $startYear = $currentYear - 1;
        }
        $year = '20'.$startYear.'-'.$startYear + 1;
        
        $opex = Opex::where('id', $request->id)->orderBy('id', 'desc')->first();
    
        $opex_budget_approve =  OpexBudget::where('department_id', $opex->department_id)->where('fiscal_year', '>=', $year)->get();

        $selectedDep=[];
        $opexs = Opex::get();
        if(!empty($opexs)){
            $opexs =Opex::select('department_id')->where('department_id','!=',$opex->department_id)->get();
            foreach($opexs as $opexs){
                array_push($selectedDep, $opexs["department_id"]);
            }
        }
        return view('admin.opex.edit', compact('opex_budget_approve','map_dept','dept_name','supdepts','supdepts122','selectedDepartments','emp_dept','opex', 'department','supdepart','transfer_budget','additional_budget','selectedDep'));
    }

    public function update_opex(Request $request)
    {
        $user_id = \Auth::user()->id;

        $budget = json_encode($request->initial_approved_budget);
        $transfer = $request->transfer_budget;
        $budgetArray = is_array($transfer) ? $transfer : [$transfer];
        $implodedBudgets = implode(',', $budgetArray);
        $additional = $request->additional_budget;
        $budgetArray1 = is_array($additional) ? $additional : [$additional];
        $implodedBudgets1 = implode(',', $budgetArray1);
        $provision = implode(',',$request->provision_budget);
        $revised = $request->revised_budget;
        $budgetArray2 = is_array($revised) ? $revised : [$revised];
        $implodedBudgets2 = implode(',', $budgetArray2);
        $budget1 = array($implodedBudgets);
        $budget2 = array($implodedBudgets1);
        $budget3 = array($implodedBudgets2);
        $budget4 = array($provision);

        $budgetYearArr = $request->years;
        $revised_budget = $request->revised_budget;
        $provision_budget = $request->provision_budget;
       
        try {
            $request_input = $request->except('_token');
            $super_department = $request_input['super_department'];
            $opex_id = $request_input['opex_id'];
            $department_id = $request_input['department_id'];
            $expenses_head = $request_input['expenses_head'];
            $activity = $request_input['activity'];
            $budget = $budget ;
            
            $remark_add = $request_input['remark_add'];

            $db_capex = Opex::find($opex_id);

            $rules = [
                'department_id' => 'required',
                'expenses_head' => 'required',
                'activity' => 'required|in:0,1,2',
                'initial_approved_budget' => 'required',
            ];

            $messages = [
                'department_id.required' => 'Please select department',
                'expenses_head.required' => 'Please enter expense head',
                'activity.required' => 'Please select activity',
                'initial_approved_budget' => 'Please enter initial approved budget',
                

            ];

            $validator = Validator::make($request_input, $rules, $messages);
            if ($validator->fails()) {
                $response['msg'] = $validator->errors()->toArray();
                $response['result'] = 'error';
            }

            $opex_id = $request_input['opex_id'];

            Opex::find($opex_id)->update(
                [
                    'department_id' => $department_id,
                    'super_department' => $super_department,
                    'expenses_head' => $expenses_head,
                    'activity' => $activity,
                    'initial_approved_budget' =>  $budget,
                    'remark_add' =>  $remark_add,
                    'transfer_budget' => $budget1[0],
                    'additional_budget'=> $budget2[0],
                    'revised_budget'=>$budget3[0],
                    'provision_budget'=>$budget4[0],

                ]
            );
            foreach ($budgetYearArr as $key => $value) 
            {
                $data_budget  = [
                    'opex_id' => $opex_id ?? '',
                    'department_id' => $request_input['department_id'] ?? '',
                    'fiscal_year' => $value ?? '',
                    'revised_budget' => $revised_budget[$key] ?? '',
                    'provision_budget' => $provision_budget[$key] ?? '',
                ];
                DummyDepartment::where('department_id', $request_input['department_id'])
                ->where('year', $value)->where('budget_type', 'OPEX')
                ->update(['transfer_status'=> 1]);

                if (!OpexBudget::where('opex_id', $opex_id)->where('fiscal_year', $value)->exists()) {
                    OpexBudget::create($data_budget);
                } else {
                    OpexBudget::where('opex_id', $opex_id)
                        ->where('fiscal_year', $value)
                        ->update($data_budget);
                }
                $department_id = $request_input['department_id'] ?? '';
                $budget_type = "OPEX";
                $existing_budget = budget::where('dept_id', $department_id)
                ->where('fiscal_year', $value)
                ->where('budget_type', $budget_type)
                ->first();

                if ($existing_budget) {
                    $existing_budget->update(['budget_avl' => $revised_budget[$key]]);
                }
               
            }
                   
                    
            $data = [];

            $fileFields = [
                'attachment',
            ];
            
            foreach ($fileFields as $fieldName) {
                if ($request->hasFile($fieldName)) {
                    $file = $request->file($fieldName);
                    $newFileName = $file->getClientOriginalName();
                    $filePath = public_path('attachment-opex/' . $newFileName);
            
                    if (!File::exists($filePath)) {
                        $file->move(public_path('attachment-opex'), $newFileName);
                    }
            
                    $data[$fieldName] = $newFileName;
                }
            }
            Opex::find($opex_id)->update($data);
            
            $response['result'] = 'success';
            $response['msg'] = 'Opex Updated';
        } catch (\Exception $e) {
            app(\App\Exceptions\Handler::class)->report($e);
            $response['result'] = 'failure';
            $response['msg'] = $e->getMessage();
        }
        $activityLogService = new ActivityLogService();
        $activity = "Some activity description"; 
        $activityLogService->log($request, $activity);
        return response()->json($response);
    }
    public function delete_subdepartment(Request $request)
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
        $this->logger->log($request, $response['msg']);
        return response()->json($response);
    }


    public function upload(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'file' => 'required|max:2048'
            ]);

            if ($validator->fails()) {
                return response()->json(['error' => $validator->errors()], 422);
            }

            $path = $request->file('file')->store('opex');
            $import = new ExcelImportService(new Opex);
            $data = $import->import($path);
            $columns = array_diff(Schema::getColumnListing((new Opex)->getTable()), ['id', 'created_at', 'updated_at']);
            $data['rows'] = array_map(function ($row) use ($columns) {
                if (count($columns) !== count($row)) {
                    return response()->json(['error' => 'Invalid file: Number of columns do not match in all rows'], 422);
                }

                $row = array_combine($columns, $row);
                $department = Department::where('name', $row['department_id'])->first();
                $row['department_id'] = $department ? $department->id : null;
                return $row;
            }, $data['rows']);
            $import->seedDB($data['rows']);
            $this->logger->log($request, 'File imported successfully.');
            return response()->json(['message' => 'File imported successfully']);
        } catch (\Exception $e) {
            $message = $e->getMessage();
            return response()->json(['error' => $message], 422);
        }
    }

    public function export_excel(Request $request)
    {

        $startYear = 2022;
        $currentYear = date('Y');
        $endYear = $currentYear + 2;
        $numFields = $endYear - $startYear + 1;
    

        $opex =  Opex::with('department', 'subdepartment','superdep')->orderBy('id', 'desc')->get();
        $output = '<html><head><style>';
        $output .= 'table {border-collapse: collapse; width: 100%;}';
        $output .= 'th, td {border: 1px solid black; padding: 8px; text-align: center;}';
        $output .= 'th {background-color: blue}';
        $output .= '</style></head><body>';
        $output .= '<table>';
        $output .= '<tr>
            <th>S.no</th>
            <th>Department</th>
            <th>Sub-Department</th>
            <th>Expense Head</th>
            <th>Activity</th>';

        for ($i = 0; $i < $numFields; $i++){
            $approved_bgt= $startYear + $i ;
            $app_bgt= $startYear + $i + 1;
            $output .=   '<th>Initial Approved Budget FY' . $approved_bgt .'-'.$app_bgt. '</th>';
            }
          
        $output .= '</tr>';
        

        
        foreach ($opex as $key => $opex) {
            $i = $key + 1;
            $output .= '<tr>';
            $output .= '<td>' . $i . '</td>';

            if($opex->super_department == null){
                $output .= '<td>' .'' . '</td>';
             } else{
                $output .= '<td>' . $opex->superdep->name . '</td>';
            }
          
            if($opex->department_id == null){
                $output .= '<td>' .'' . '</td>';
             } else{
                $output .= '<td>' . $opex->department->name . '</td>';
            }
         
            $output .= '<td>' . $opex->expenses_head . '</td>';
            
            if ($opex->activity == '0') {
                $output .= '<td>' .'Activity 1' . '</td>';
            } elseif ($opex->activity == '1') {
                $output .= '<td>' .'Activity 2' . '</td>';
            } elseif ($opex->activity == '2') {
                $output .= '<td>' .'Activity 3' . '</td>';
            }

            $year = json_decode($opex->initial_approved_budget, true);
                  
            if (is_array($year)) {
                $year_count = count($year);
            } else {
                $year_count = 0;
            }
            for ($i = 0; $i < $numFields; $i++){
                $output .= '<td>';
                $formattedNumber = 0;
                if (isset($year[$i])) {
                    $number = $year[$i];
                    $formattedNumber = number_format((int)$number, 0, '.', ',');
                    $output .=  $formattedNumber ;
                }else{
                    $output .=  0 ;
                }
                $output .= '</td>';
            }

            $output .= '</tr>';
        }
        
        $output .= '</table>';
        $output .= '</body></html>';
    
        header("Content-type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet");
        header("Content-Disposition: attachment; filename=OPEX_List.xls");
        header("Pragma: no-cache");
        header("Expires: 0");
    
        echo $output;
    }
    public function Logs(Request $request){
       $audits = Audit::where('auditable_type','App\Models\Opex')->paginate(10);
       return view('admin.opex.logopex', compact('audits'));
    }

    public function export_logexcel(Request $request)
    {
 
        $startYear = date('Y');
        $currentYear = date('Y');
        $endYear = $currentYear + 2;
        $numFields = $endYear - $startYear + 1;

        $activityMapping = [
            '0' => 'Activity 1',
            '1' => 'Activity 2',
            '2' => 'Activity 3'
        ];
    
        $audits = Audit::where('auditable_type','App\Models\Opex')->get();
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
           <th>Department</th>
           <th>Sub-Department</th>
           <th>Expense Head</th>
           <th>Activity</th>';

            for ($i = 0; $i < $numFields; $i++){
               $approved_bgt= $startYear + $i ;
               $app_bgt= $startYear + $i + 1;
               $output .=   '<th>Initial Approved Budget FY ' . $approved_bgt .'-'.$app_bgt;
               }
               $output .= '</tr>';

               foreach($audits as $key => $audit){
                   $newvalue =  $audit->new_values ?? [];
                   $oldvalue =  $audit->old_values ?? [];
                   $userName = optional($audit->user)->name ?? ' ';
                   $date = $audit->created_at->format('d-M-Y h:i:s A');
                   $event = ucfirst($audit->event);
       
                   $output .= '<tr>';
                   $output .= '<td rowspan="2">' . ($key + 1) . '</td>';
                   $output .= '<td rowspan="2">' . $event . '</td>'; 
                   $output .= '<td rowspan="2">' . $userName . '</td>'; 
                   $output .= '<td rowspan="2">' . $date . '</td>';
                   $output .= '<td><b>'. "Old Value". "</b></td>";
                  
                   if( $event == 'Updated'){
                       $output .= '<td>' . getsuperdepname(data_get($oldvalue, 'super_department')) ?? '' . '</td>'; 
                       $output .= '<td>' . getDepartmentName(data_get($oldvalue, 'department_id')) ?? '' . '</td>'; 
                       $output .= '<td>' . data_get($oldvalue, 'expenses_head') ?? ''. '</td>';
                       if (isset($oldvalue['activity'])){
                          $output .= '<td>' . $activityMapping[$oldvalue['activity']] ?? ' ' . '</td>';
                       }else{
                           $output .= '<td></td>';
                       }

                       for ($i = 0; $i < $numFields; $i++){
                           $initial_approved_budget =  data_get($oldvalue, 'revised_budget');
                           $year = explode(',',$initial_approved_budget);
                           if (is_array($year)) {
                               $year_count = count($year);
                           } else {
                               $year_count = 0; 
                           }
                           $output .= '<td>';
                               $formattedNumber = 0;
                               if (isset($year[$i])) {
                                   $number = $year[$i];
                                   $formattedNumber = number_format((int)$number, 0, '.', ',');
                                   $output .=  $formattedNumber ;
                               }else{
                                   $output .=  0 ;
                               }
                               $output .= '</td>';
                       }
                     
                       
                   }else{
                       $output .= '<td colspan="7"></td>';  
                   }
       
                   $output .= '</tr>';
                   $output .= '<tr>';
                   $output .= '<td><b>'. "New Value". "</b></td>";
                   $output .= '<td>' . getsuperdepname(data_get($newvalue, 'super_department')) ?? '' . '</td>'; 
                   $output .= '<td>' . getDepartmentName(data_get($newvalue, 'department_id')) ?? '' . '</td>'; 
                   $output .= '<td>' . data_get($newvalue, 'expenses_head') ?? ''. '</td>';
                   if (isset($newvalue['activity'])){
                       $output .= '<td>' . $activityMapping[$newvalue['activity']] ?? ' ' . '</td>';
                   }else{
                       $output .= '<td></td>';
                   }

                   for ($i = 0; $i < $numFields; $i++){
                   $initial_approved_budget =  data_get($newvalue, 'revised_budget');
                   $year = explode(',',$initial_approved_budget);
                   if (is_array($year)) {
                       $year_count = count($year);
                   } else {
                       $year_count = 0; 
                   }
                   $output .= '<td>';
                       $formattedNumber = 0;
                       if (isset($year[$i])) {
                           $number = $year[$i];
                           $formattedNumber = number_format((int)$number, 0, '.', ',');
                           $output .=  $formattedNumber ;
                       }else{
                           $output .=  0 ;
                       }
                       $output .= '</td>';
                   }
                   
                   $output .= '</tr>';
               }
        $output .= '</table>';
        $output .= '</body></html>';
    
        header("Content-type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet");
        header("Content-Disposition: attachment; filename=LogOPEX_List.xls");
        header("Pragma: no-cache");
        header("Expires: 0");
    
        echo $output;
    }

public function getSubDepartments($id)
{
    $departments = SupDept::where('id', $id)->value('departments');

    // Make sure departments always becomes an array
    $itemsArray = [];

    if (!empty($departments)) {
        $itemsArray = explode(',', $departments);
        $itemsArray = array_map('trim', $itemsArray);  // remove spaces
        $itemsArray = array_filter($itemsArray);       // remove empty values
    }

    $data = Department::whereIn('id', $itemsArray)
        ->select('id', 'name')
        ->get();

    return response()->json([
        'data' => $data
    ]);
}


    public function getSubDepartments1() {
        $departments = department::pluck('name');
        return response()->json([
            'department' => $departments ?? '',
        ]);
    }

    public function updateDummyDepartment(Request $request)
    {
        $amount = $request->transfer;
        $revised = $request->revised;
        $afterTransfer = $request->afterTransfer;
       
        $transaction_type = $request->operation;
        $department_id = $request->department_id;
        $year = $request->year;
        $budget_type = 'OPEX';
    
        $trans_type = '';
        $transfer = 0;

        $creditAdd = DummyDepartment::where('transaction_type', 'Credit')->where('transfer_status',1)
        ->where('department_id', null)
        ->sum('amount');
        $creditDep = DummyDepartment::where('transaction_type', 'Credit')->where('transfer_status',1)
        ->where('department_id','!=', null)
        ->sum('amount');
        $debitDep = DummyDepartment::where('transaction_type', 'Debit')->where('transfer_status',1)
        ->where('department_id','!=', null)
        ->sum('amount');
        $netCredit = ($creditDep + $creditAdd) - $debitDep;

        if ($transaction_type == 'plus') {
            $trans_type = 'Debit';
            if (!empty($amount)) {
                if (!empty($netCredit) && $netCredit >= $amount) {
                $transfer = $amount;
                }else{
                return response()->json(['status'=>'false','message' => 'Dummy Budget is less than the Transfer Budget'], 200);
  
                }
            }
        } else {
            $trans_type = 'Credit';
            if (!empty($amount) ) {
                if (!empty($revised) && $revised >= $amount) {
                    $transfer = $amount;
                } else {
                    return response()->json(['status'=>'false','message' => 'Revised Budget is less than the Transfer Budget'], 200);
                }
            }
        }
    
        if (!empty($amount)) {
            $data = [
                'amount' => $transfer ?? '',
                'transaction_type' => $trans_type ?? '',
                'department_id' => $department_id ?? '',
                'year' => $year ?? '',
                'budget_type' => $budget_type ?? '',
            ];
    
          if( DummyDepartment::create($data)){
            $opex = Opex::where('department_id',$department_id)->first();
            $created_at = $opex->created_at;
            $created_year = date("Y", strtotime($opex->created_at));
            $created_month = date("m", strtotime($opex->created_at));
            if($created_month >= '4'){
               $created_FY =  $created_year."-".$created_year+1;
            }else{
                $created_FY = ($created_year-1)."-".$created_year;
            }
            $current_year =  date('Y');
            $current_month =  date('m');
            if($current_month >= '4'){
                $current_FY =  $current_year."-".$current_year+1;
             }else{
                 $current_FY = ($current_year-1)."-".$current_year;
             }
            $yearM = explode('-',$year);
            $yearM =$yearM[0];
           
            // if($created_FY == $current_FY){
            $index=(int)$yearM-(int)$current_year;
            // }else{
            //  $indext=(int)$current_FY-(int)$created_FY ;
            //  $index=(int)$yearM-(int)$current_year;
            //  $index=$index+$indext;
            // }  
            $revised_budget = explode(',',$opex->revised_budget);
            $revised_budget[$index] = $afterTransfer;
            $revisedBudget = implode(',',$revised_budget);
            // Opex::where('department_id',$department_id)
            // ->update(['revised_budget'=>$revisedBudget]);

            // OpexBudget::where('department_id', $department_id)
            // ->where('fiscal_year', $year)
            // ->update(['revised_budget'=>$afterTransfer]);

            // $existing_budget = budget::where('dept_id', $department_id)
            // ->where('fiscal_year', $year)
            // ->where('budget_type', $budget_type)
            // ->first();

            // if ($existing_budget) {
            //     $existing_budget->update(['budget_avl' => $afterTransfer]);
            // }
           
          }
            


        }
    
        return response()->json(['status'=>'true','message' => 'Data successfully saved'], 200);
    }

    public function cancelDummyDepartment(Request $request) {
        $department_id = $request->input('department_id');
        $years = $request->input('years');
       
         foreach ($years as $year) {
            DummyDepartment::where('department_id', $department_id)
            ->where('year', $year)
            ->where('transfer_status', 0)
            ->where('budget_type','OPEX')
            ->delete();
            }
        
    
        return response()->json(['status' => 'success']);
    }
}
