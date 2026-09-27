<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Session;
use Validator;
use App\Models\Capex;
use App\Models\Boqmaterial;
use App\Models\Department;
use App\Services\ExcelImportService;
use App\Models\MasterMaterialboq;
use App\Models\SupDept;
use Illuminate\Support\Facades\Schema;
use App\Models\NeedValidation;
use App\Services\ActivityLogService;
use App\Models\budget;
use App\Models\NVMaterial;
use App\Models\NVService;
use App\Models\DummyDepartment;
use Carbon\Carbon;
use App\Models\CapexBudget;
use File;
use OwenIt\Auditing\Models\Audit;


class CapexController extends Controller
{
    private $logger;
    public function __construct(ActivityLogService $Logger)
    {
        $this->logger = $Logger;
        $this->middleware(function ($request, $next) {
            Session::put('active', 'capexmaster');

            return $next($request);
        });
    }
    public function capex_otp()
    {
        return view('admin.capex.capex_otp_verify');
    }

    public function capex_list(Request $request)
    {
        $user = \Auth()->user();
        $capexlist = Capex::with('superdep','dep')->select('super_department','department_id'
        ,'head','sub_head','brp_head','status','capx_fy_one','capx_fy_two','id')->orderBy('id', 'desc')->get();
        return view('admin.capex.list' ,["capexlist"=>$capexlist]);
    }
    public function create_capex_master(Request $request)
    {  
        $department = Department::select('id', 'name')->where('status', 1)->get();
        $supdepart = SupDept::select('id', 'name')->where('status', 1)->get();

      
        $selectedDepartments=[];
        $capex = Capex::get();
        if(!empty($capex)){
            $capex =Capex::select('department_id')->get(); 
            foreach($capex as $capex){
                array_push($selectedDepartments, $capex["department_id"]);
            }
        }
        
       
        return view('admin.capex.create',compact('department','supdepart','selectedDepartments'));
    }
    public function store_capex_master(Request $request)
    {
        $user_id = \Auth::user()->id;
        
        $budget_fy_one = json_encode($request->capx_fy_one);
    

        $budget_fy_two = json_encode($request->capx_fy_two);
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
       $budget_with = $request->capx_fy_two;
       $budget_wo = $request->capx_fy_one;
       $revised_budget = $request->revised_budget;
      
        try {
            $request_input = $request->except('_token');

            $rules = [
                'department_id' => 'required|unique:capex_master,department_id',
                'head' => 'required|in:0,1,2,3,4,5,6',
                'sub_head' => 'required|string',
                'brp_head' => 'required|in:0,1,2,3,4',
                'status' => 'required|in:0,1',
            ];

            $messages = [
                'department_id' => 'Please select Department',
                'department_id.unique' => 'This department already exists in CAPEX',
                'head.required' => 'Please select head',
                'sub_head.required' => 'Please enter sub head',
                'brp_head.required' => 'Please select bpr head',
                'status.required' => 'Please select Regular CAPEX/Project CAPEX',

            ];
            $validator = Validator::make($request_input, $rules, $messages);
            if ($validator->fails()) {
                $response['msg'] = $validator->errors()->toArray();
                $response['result'] = 'error';
            } else {

                $request_input['status'] = isset($request_input['status']) ? $request_input['status'] : 0;


                $data = [
                    'super_department' => $request_input['super_department'],
                    'department_id' => $request_input['department_id'],
                    'head' => $request_input['head'],
                    'sub_head' => $request_input['sub_head'],
                    'brp_head' => $request_input['brp_head'],
                    'status' => $request_input['status'],
                    'capx_fy_one' =>$budget_fy_one,
                    'capx_fy_two' => $budget_fy_two,
                    'transfer_budget' => $budget1[0],
                    'additional_budget'=> $budget2[0],
                    'revised_budget'=>$budget3[0],
                ];
                $capex = Capex::create($data);
                if($capex){

                foreach ($budgetYearArr as $key => $value) 
                    {
                        $data_budget  =[
                            'capex_id' => $capex->id ?? '',
                            'department_id' => $capex->department_id ?? '',
                            'fiscal_year' => $value ?? '',
                            'revised_budget' => $revised_budget[$key] ?? '',
                            'budget_with' => $budget_with[$key] ?? '',
                            'budget_wo' => $budget_wo[$key] ?? '',
                        ];
                        $yearBudget = CapexBudget::create($data_budget);
                    }
                }
              
                $response['result'] = 'success';
                $response['msg'] = 'capex created';
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

    public function edit_capex_master(Request $request)
    {
        $capexlist = Capex::findOrFail($request->id);
        $department = Department::select('id', 'name')->where('status', 1)->get();
        $supdepart = SupDept::select('id', 'name')->where('status', 1)->get();

        $emp_dept = Capex::join('superdepartment','superdepartment.id','=','capex_master.super_department')->where('capex_master.id',$request->id)->first();
        $selectedDepartments = explode(',', $capexlist->department_id);
        $supdepts122 = SupDept::select('id', 'name','departments')->where('id',$emp_dept->super_department)->where('status', 1)->get();
        $supdepts = SupDept::select('id', 'name','departments')->where('status', 1)->get();
        foreach($supdepts122 as $dept)
        {
            $map_dept = explode(',',$dept->departments,);
            
        }
        $currentYear = date('y');
        $currentMonth = date('m');
        if ($currentMonth >= 4) {
            $startYear = $currentYear;
        } else {
            $startYear = $currentYear - 1;
        }
        $year = '20'.$startYear.'-'.$startYear + 1;
        $capex = Capex::where('id', $request->id)->orderBy('id', 'desc')->first();
    
        $capex_budget_approve =  CapexBudget::where('department_id', $capex->department_id)->where('fiscal_year', '>=', $year)
        ->get();

        $dept_name = Department::select('id', 'name')->where('status', 1)->whereIn('id',$map_dept)->get();

        $selectedDep=[];
        $capex = Capex::get();
        if(!empty($capex)){
            $capex =Capex::select('department_id')->where('department_id','!=',$capexlist->department_id)->get();
            foreach($capex as $capex){
                array_push($selectedDep, $capex["department_id"]);
            }
        }
        return view('admin.capex.edit', compact('capex_budget_approve','map_dept','dept_name','supdepts','supdepts122','selectedDepartments','emp_dept','capexlist','department','supdepart','selectedDep'));
    }

    public function update_capex_master(Request $request)
    {
        $user_id = \Auth::user()->id;

        $budget_fy_one = json_encode($request->capx_fy_one);
        $capx_fy_two = json_encode($request->capx_fy_two);
        $capx_fy_two = json_encode($request->capx_fy_two);
        
        $transfer = implode(',',$request->transfer_budget);
        
        $additional = implode(',',$request->additional_budget);
        $provision = implode(',',$request->provision_budget);
        $revised = implode(',',$request->revised_budget);
        $budget1 = array($transfer);
        $budget2 = array($additional);
       $budget3 = array($revised);
       $budget4 = array($provision);

       $budgetYearArr = $request->years;
       $budget_with = $request->capx_fy_two;
       $budget_wo = $request->capx_fy_one;
       $revised_budget = $request->revised_budget;
       $provision_budget = $request->provision_budget;

        try {
            $request_input = $request->except('_token');
            $capexlist_id = $request_input['capexlist_id'];
            $department_id = $request_input['department_id'];
            $head = $request_input['head'];
            $sub_head = $request_input['sub_head'];
            $brp_head = $request_input['brp_head'];
            $status = $request_input['status'];
            $capx_fy_one = $request_input['capx_fy_one'];
            $capx_fy_two = $request_input['capx_fy_two'];
            $remark_capex = $request_input['remark_capex'];
            $db_capex = Capex::find($capexlist_id);

            if ($db_capex->department_id != $department_id &&$db_capex->head != $head && $db_capex->sub_head != $sub_head && $db_capex->brp_head != $brp_head && $db_capex->status != $status 
            && $db_capex->capx_fy_one != $capx_fy_one && $db_capex->capx_fy_two != $capx_fy_two) {
                $rules = [
                    'department_id' => 'required',
                    'head' => 'required|in:0,1,2,3,4,5,6',
                    'sub_head' => 'required|string',
                    'brp_head' => 'required|in:0,1,2,3,4',
                    'status' => 'required|in:0,1',
                    'capx_fy_one' => 'required|string',
                    'capx_fy_two' => 'required|string'
                ];

                $messages = [
                    'department_id' => 'Please select Department',
                    'head.required' => 'Please select head',
                    'sub_head.required' => 'Please enter sub head',
                    'brp_head.required' => 'Please select bpr head',
                    'status.required' => 'Please select Regular CAPEX/Project CAPEX',
                    'capx_fy_one' => 'Please enter CAPEX FY 24 (W/O OH & INT)',
                    'capx_fy_two' => 'Please enter CAPEX FY 24 (With OH & INT)'

                ];

                $validator = Validator::make($request_input, $rules, $messages);
                if ($validator->fails()) {
                    $response['msg'] = $validator->errors()->toArray();
                    $response['result'] = 'error';
                } else {
                    $capexlist_id = $request_input['capexlist_id'];
                    unset($request_input['capexlist_id']);


                    Capex::find($capexlist_id)->update(
                        [ 
                            'department_id' => $request_input['department_id'],
                            'super_department' => $request_input['super_department'],
                            'head' => $request_input['head'],
                            'sub_head' => $request_input['sub_head'],
                            'brp_head' => $request_input['brp_head'],
                            'status' => $request_input['status'],
                            'capx_fy_one' =>$capx_fy_one,
                            'capx_fy_two' => $capx_fy_two,
                            'transfer_budget' => $budget1[0],
                            'additional_budget'=> $budget2[0],
                            'revised_budget'=>$budget3[0],
                            'provision_budget'=>$budget4[0],
                            'remark_capex' => $request_input['remark_capex'],
                        ]
                    );
                  
                    foreach ($budgetYearArr as $key => $value) {
                        $data_budget  = [
                            'capex_id' => $capexlist_id ?? '',
                            'department_id' => $request_input['department_id'] ?? '',
                            'fiscal_year' => $value ?? '',
                            'revised_budget' => $revised_budget[$key] ?? '',
                            'provision_budget' => $provision_budget[$key] ?? '',
                            'budget_with' => $budget_with[$key] ?? '',
                            'budget_wo' => $budget_wo[$key] ?? '',
                        ];
                        DummyDepartment::where('department_id', $request_input['department_id'])
                        ->where('year', $value)->where('budget_type', 'CAPEX')
                        ->update(['transfer_status'=> 1]);
                    
                        if (!CapexBudget::where('capex_id', $capexlist_id)->where('fiscal_year', $value)->exists()) {
                            CapexBudget::create($data_budget);
                        } else {
                            CapexBudget::where('capex_id', $capexlist_id)
                                ->where('fiscal_year', $value)
                                ->update($data_budget);
                        }
                        $department_id = $request_input['department_id'] ?? '';
                        $budget_type = "CAPEX";
                        $existing_budget = budget::where('dept_id', $department_id)
                        ->where('fiscal_year', $value)
                        ->where('budget_type', $budget_type)
                        ->first();

                        if ($existing_budget) {
                            $existing_budget->update([
                                'budget_avl' => $revised_budget[$key],
                            ]);
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
                            $filePath = public_path('attachment-capex/' . $newFileName);
                    
                            // Check if a file with the same name already exists
                            if (!File::exists($filePath)) {
                                $file->move(public_path('attachment-capex'), $newFileName);
                            }
                    
                            $data[$fieldName] = $newFileName;
                        }
                    }
                    Capex::find($capexlist_id)->update($data);
                   
                    $response['result'] = 'success';
                    $response['msg'] = 'Company Updated';
                }
            } else {
                $rules = [
                    'department_id' => 'required',
                    'head' => 'required|in:0,1,2,3,4,5,6',
                    'sub_head' => 'required|string',
                    'brp_head' => 'required|in:0,1,2,3,4',
                    'status' => 'required|in:0,1',
                ];

                $messages = [
                    'department_id' => 'Please select Department',
                    'head.required' => 'Please select head',
                    'sub_head.required' => 'Please enter sub head',
                    'brp_head.required' => 'Please select bpr head',
                    'status.required' => 'Please select Regular Capex/Project Capex',

                ];

                $validator = Validator::make($request_input, $rules, $messages);
                if ($validator->fails()) {
                    $response['msg'] = $validator->errors()->toArray();
                    $response['result'] = 'error';
                } else {
                    $capexlist_id = $request_input['capexlist_id'];
                    unset($request_input['capexlist_id']);


                    Capex::find($capexlist_id)->update(
                        [  
                            'department_id' => $request_input['department_id'],
                            'super_department' => $request_input['super_department'],
                            'head' => $request_input['head'],
                            'sub_head' => $request_input['sub_head'],
                            'brp_head' => $request_input['brp_head'],
                            'status' => $request_input['status'],
                            'capx_fy_one' =>$capx_fy_one,
                            'capx_fy_two' => $capx_fy_two,
                            'transfer_budget' => $budget1[0],
                            'additional_budget'=> $budget2[0],
                            'revised_budget'=>$budget3[0],
                            'provision_budget'=>$budget4[0],
                            'remark_capex' => $request_input['remark_capex'],
                        ]
                    );
                    foreach ($budgetYearArr as $key => $value) {
                        $data_budget  = [
                            'capex_id' => $capexlist_id ?? '',
                            'department_id' => $request_input['department_id'] ?? '',
                            'fiscal_year' => $value ?? '',
                            'revised_budget' => $revised_budget[$key] ?? '',
                            'budget_with' => $budget_with[$key] ?? '',
                            'provision_budget' => $provision_budget[$key] ?? '',
                            'budget_wo' => $budget_wo[$key] ?? '',
                        ];
                        DummyDepartment::where('department_id', $request_input['department_id'])
                        ->where('year', $value)->where('budget_type', 'CAPEX')
                        ->update(['transfer_status'=> 1]);
                    
                        if (!CapexBudget::where('capex_id', $capexlist_id)->where('fiscal_year', $value)->exists()) {
                            CapexBudget::create($data_budget);
                        } else {
                            CapexBudget::where('capex_id', $capexlist_id)
                                ->where('fiscal_year', $value)
                                ->update($data_budget);
                        }

                        $department_id = $request_input['department_id'] ?? '';
                        $budget_type = "CAPEX";
                        $existing_budget = budget::where('dept_id', $department_id)
                        ->where('fiscal_year', $value)
                        ->where('budget_type', $budget_type)
                        ->first();

                        if ($existing_budget) {
                            $existing_budget->update([
                                'budget_avl' => $revised_budget[$key],
                            ]);
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
                            $filePath = public_path('attachment-capex/' . $newFileName);
                    
                            if (!File::exists($filePath)) {
                                $file->move(public_path('attachment-capex'), $newFileName);
                            }
                    
                            $data[$fieldName] = $newFileName;
                        }
                    }
                    Capex::find($capexlist_id)->update($data);
                  
                    $response['result'] = 'success';
                    $response['msg'] = 'Capex Updated';
                }
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

    public function delete_capex_master(Request $request)
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
            $path = $request->file('file')->store('capex');
            $import = new ExcelImportService(new Capex());
            $data = $import->import($path);
            $columns = array_diff(Schema::getColumnListing((new Capex())->getTable()), ['id', 'created_at', 'updated_at']);
            $head = [
                "Load Groth" => 0,
                "System" => 1,
                "Statutory" => 2,
                "Infrastructure" => 3,
                "Technology" => 4,
                "Deposit" => 5,
                "Overheads" => 6
            ];
            $brp_head = [
                "Performance Obligation" => 0,
                "Power Reliability" => 1,
                "Infrastructure Development" => 2,
            ];
            $data['rows'] = array_map(function ($row) use ($columns, $head, $brp_head) {
                if (count($columns) !== count($row)) {
                    return response()->json(['error' => 'Invalid file: Number of columns do not match in all rows'], 422);
                }
                $row = array_combine($columns, $row);
                $row['head'] = array_search($row['head'], $head);
                $row['brp_head'] = array_search($row['brp_head'], $brp_head);
                $row['status'] = $row['status'] == 'Regular' ? 0 : 1;
                return $row;
            }, $data['rows']);
            $import->seedDB($data['rows']);
            $this->logger->log($request, 'File Import successfully');
            return response()->json(['message' => 'File imported successfully']);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 422);
        }
    }

    public function autofetchdata(Request $request)
    {
        $key = $request->key;
        $check_data = MasterMaterialboq::where('activity', 'like', $key . '%')->select('activity', 'material_short_text', 'uom','rate_add')->first();
        $response = array();
      
        return response()->json($check_data);
    }

    public function autofetchdata_service(Request $request)
    {
        $key = $request->key;
        $check_data = Boqmaterial::where('activity', 'like', $key . '%')->select('activity', 'service_short_text','bun', 'rate_ser')->first();
        $response = array();
     
        return response()->json($check_data);
    }

    public function export_excel(Request $request)
    {
        $startYear = 2022;
        $currentYear = date('Y');
        $endYear = $currentYear + 2;
        $numFields = $endYear - $startYear + 1;

        $capex = Capex::with('superdep','dep')->orderBy('id', 'desc')->get();
        $output = '<html><head><style>';
        $output .= 'table {border-collapse: collapse; width: 100%;}';
        $output .= 'th, td {border: 1px solid black; padding: 8px; text-align: center;}';
        $output .= 'th {background-color: blue}';
        $output .= '</style></head><body>';
        $output .= '<table>';
        $output .= '<tr><th>S.no</th>
        <th>Super Department</th>
        <th>Department</th>
        <th>Head</th>
        <th>Sub Head</th>
        <th>BPR Head</th>
        <th>Regular CAPEX/Project CAPEX</th>';
        for ($i = 0; $i < $numFields; $i++){
            $approved_bgt= $startYear + $i ;
            $app_bgt= $startYear + $i + 1;
            $output .=   '<th>CAPEX FY' . $approved_bgt .'-'.$app_bgt. '(W/O OH & INT)</th>';
            $output .=   '<th>CAPEX FY' . $approved_bgt .'-'.$app_bgt. '(With OH & INT)</th>';
            }
        $output .= '</tr>';
        
        foreach ($capex as $key => $capex) {
            $i = $key + 1;
            $output .= '<tr>';
            $output .= '<td>' . $i . '</td>';
            if($capex->super_department == null){
                $output .= '<td>' .'' . '</td>';
            }else{
                $output .= '<td>' . $capex->superdep->name . '</td>';
            }
            if($capex->department_id == null){
                $output .= '<td>' .'' . '</td>';
             } else{
                $output .= '<td>' . $capex->dep->name . '</td>';
            }

            if ($capex->head == '0') {
                $output .= '<td>' . 'Load Growth' . '</td>';
            } elseif ($capex->head == '1') {
                $output .= '<td>' . 'System Improvement' . '</td>';
            } elseif ($capex->head == '2') {
                $output .= '<td>' . 'Statutory Requiremt' . '</td>';
            } elseif ($capex->head == '3') {
                $output .= '<td>' . 'Infrastructure' . '</td>';
            } elseif ($capex->head == '4') {
                $output .= '<td>' . 'Technology' . '</td>';
            } elseif ($capex->head == '5') {
                $output .= '<td>' . 'Deposit' . '</td>';
            } elseif ($capex->head == '6') {
                $output .= '<td>' . 'Overheads & Interest' . '</td>';
            }
            $output .= '<td>' .$capex->sub_head . '</td>';
            if ($capex->brp_head == '0') {
                $output .= '<td>' . 'Performance Obligation' . '</td>';
            } elseif ($capex->brp_head == '1') {
                $output .= '<td>' . 'Power Reliability' . '</td>';
            } elseif ($capex->brp_head == '2') {
                $output .= '<td>' . 'Infrastructure Development' . '</td>';
            }
            $output .= '<td>' . ($capex->status == 1 ? 'Project' : 'Regular') . '</td>';
            
            $year = json_decode($capex->capx_fy_one, true);
                  
            if (is_array($year)) {
                $year_count = count($year);
            } else {
                $year_count = 0;
            }
            $year_two = json_decode($capex->capx_fy_two, true);
            if (is_array($year_two)) {
                $year_count_two = count($year_two);
            } else {
                $year_count_two = 0; 
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

                $output .= '<td>';
                $formattedNumber = 0;
                if (isset($year_two[$i])) {
                    $number = $year_two[$i];
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
        header("Content-Disposition: attachment; filename=CAPEX_List.xls");
        header("Pragma: no-cache");
        header("Expires: 0");
    
        echo $output;
    }

    public function Logs(Request $request){
        $audits = Audit::where('auditable_type','App\Models\Capex')->paginate(10);
        return view('admin.capex.capexlog', compact('audits'));
     }

     public function export_logexcel(Request $request)
     {
 
         $startYear = date('Y');
         $currentYear = date('Y');
         $endYear = $currentYear + 2;
         $numFields = $endYear - $startYear + 1;

         $headMapping = [
            '0' => 'Load Growth',
            '1' => 'System Improvement',
            '2' => 'Statutory Requirement',
            '3' => 'Infrastructure',
            '4' => 'Technology',
            '5' => 'Deposit',
            '6' => 'Overheads & Interest'
        ];

        $brpHeadMapping = [
            '0' => 'Performance Obligation',
            '1' => 'Power Reliability',
            '2' => 'Infrastructure Development'
        ];
     
         $audits = Audit::where('auditable_type','App\Models\Capex')->get();
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
            <th>Head</th>
            <th>Sub Head</th>
            <th>BPR Head</th>
            <th>Regular CAPEX/Project CAPEX</th>';
 
             for ($i = 0; $i < $numFields; $i++){
                $approved_bgt= $startYear + $i ;
                $app_bgt= $startYear + $i + 1;
                $output .=   '<th>CAPEX FY' . $approved_bgt .'-'.$app_bgt. '(W/O OH & INT)</th>';
                $output .=   '<th>CAPEX FY' . $approved_bgt .'-'.$app_bgt. '(With OH & INT)</th>';
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

                        if (isset($oldvalue['head'])){
                           $output .= '<td>' . $headMapping[$oldvalue['head']] ?? ' ' . '</td>';
                        }else{
                            $output .= '<td></td>';
                        }
                        
                        $output .= '<td>' . data_get($oldvalue, 'sub_head') ?? ''. '</td>';
                        if (isset($oldvalue['brp_head'])){
                           $output .= '<td>' . $brpHeadMapping[$oldvalue['brp_head']] ?? ' ' . '</td>';
                        }else{
                            $output .= '<td></td>';
                        }
                        if (isset($oldvalue['status'])){
                            if($oldvalue['status']==1){
                                $output .= '<td>'."Project" .'</td>';
                            }else{
                                $output .= '<td>'."Regular" .'</td>';
                            }
                        }else{
                            $output .= '<td></td>';
                        }

                        for ($i = 0; $i < $numFields; $i++){
                            $capx_fy_one =  data_get($oldvalue, 'revised_budget');
                            $year = explode(',',$capx_fy_one);
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
                        
                            $capx_fy_two =  data_get($oldvalue, 'capx_fy_two');
                            $year_two = json_decode($capx_fy_two, true);
                            if (is_array($year_two)) {
                                $year_count = count($year_two);
                            } else {
                                $year_count = 0; 
                            }
                            $output .= '<td>';
                                $formattedNumber = 0;
                                if (isset($year_two[$i])) {
                                    $number = $year_two[$i];
                                    $formattedNumber = number_format((int)$number, 0, '.', ',');
                                    $output .=  $formattedNumber ;
                                }else{
                                    $output .=  0 ;
                                }
                                $output .= '</td>';
                        
                        }
                      
                        
                    }else{
                        $output .= '<td colspan="12"></td>';  
                    }
        
                    $output .= '</tr>';
                    $output .= '<tr>';
                    $output .= '<td><b>'. "New Value". "</b></td>";
                    $output .= '<td>' . getsuperdepname(data_get($newvalue, 'super_department')) ?? '' . '</td>'; 
                    $output .= '<td>' . getDepartmentName(data_get($newvalue, 'department_id')) ?? '' . '</td>'; 
        
                    if (isset($newvalue['head'])){
                       $output .= '<td>' . $headMapping[$newvalue['head']] ?? ' ' . '</td>';
                    }else{
                        $output .= '<td></td>';
                    }
                    $output .= '<td>' . data_get($newvalue, 'sub_head') ?? ''. '</td>';
                    if (isset($newvalue['brp_head'])){
                        $output .= '<td>' . $brpHeadMapping[$newvalue['brp_head']] ?? ' ' . '</td>';
                    }else{
                        $output .= '<td></td>';
                    }
                    if (isset($newvalue['status'])){
                    if($newvalue['status']==1){
                            $output .= '<td>'."Project" .'</td>';
                    }else{
                            $output .= '<td>'."Regular" .'</td>';
                    }
                    }else{
                        $output .= '<td></td>';
                    }
                    for ($i = 0; $i < $numFields; $i++){
                    $capx_fy_one =  data_get($newvalue, 'revised_budget');
                    $year = explode(',',$capx_fy_one);
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
                 
                    $capx_fy_two =  data_get($newvalue, 'capx_fy_two');
                    $year_two = json_decode($capx_fy_two, true);
                    if (is_array($year_two)) {
                        $year_count = count($year_two);
                    } else {
                        $year_count = 0; 
                    }
                    $output .= '<td>';
                        $formattedNumber = 0;
                        if (isset($year_two[$i])) {
                            $number = $year_two[$i];
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
         header("Content-Disposition: attachment; filename=LogCAPEX_List.xls");
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
        $budget_type = 'CAPEX';
    
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
            $capex = Capex::where('department_id',$department_id)->first();
            $created_at = $capex->created_at;
            $created_year = date("Y", strtotime($capex->created_at));
            $created_month = date("m", strtotime($capex->created_at));
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
            $revised_budget = explode(',',$capex->revised_budget);
            $revised_budget[$index] = $afterTransfer;
            $revisedBudget = implode(',',$revised_budget);
            // $capex = Capex::where('department_id',$department_id)
            // ->update(['revised_budget'=>$revisedBudget]);

            // CapexBudget::where('department_id', $department_id)
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
   public function DummyList(Request $request){
    $user = \Auth()->user();
        if ($request->ajax()) {

            $dummy_dep = datatables()
                ->of(
                    DummyDepartment::with('department')->where('transfer_status',1)->orderBy('id', 'desc')->get()
                )
                ->addColumn('department', function ($data) {

                    return $data->department->name ?? 'Dummy Department';
                })
               
               
                ->addColumn('updated_at', function ($data) {
                   return date("d-M-y h:i A", strtotime($data->updated_at));
                })

               
                ->addIndexColumn()
                ->rawColumns(['action', 'department'])
                ->make(true);

            return $dummy_dep;
        }
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
     
    return view('admin.DummyDepartment.list',['netCredit' => $netCredit ?? 0]);

    }
  
  public function saveBudget(Request $request){
    $amount = $request->budget;
    if (!empty($amount)) {
        $data = [
            'amount' => $amount ?? '',
            'transaction_type' => 'Credit',
            'department_id' => null,
            'year' => null,
            'budget_type' => null,
            'transfer_status' => 1,
        ];

        $dummy = DummyDepartment::create($data);
  
    }

    return response()->json(['status'=>'true','message' => 'Data successfully saved'], 200);
  }

  public function ProvisionalList(Request $request){
    $user = \Auth()->user();
        $nv_material_ids = NVMaterial::where('draft', 1)->pluck('nv_id')->toArray();
        $nv_service_ids = NVService::where('draft', 1)->pluck('nv_id')->toArray();
        $nv_ids = array_merge($nv_material_ids, $nv_service_ids);
    //  $nv_id = NVMaterial::where('draft',1)->pluck('nv_id');
     $nvs = NeedValidation::with('user','material','service','services')->whereIn('id',$nv_ids)->where('budgetary_provision','Approved')->where('delete_draft',0)->orderBy('id','desc')->paginate(10);
     
    return view('admin.Provisional.list',compact('nvs'));
  }

  public function cancelDummyDepartment(Request $request) {
    $department_id = $request->input('department_id');
    $years = $request->input('years');
    
    foreach ($years as $year) {
        DummyDepartment::where('department_id', $department_id)
        ->where('year', $year)
        ->where('transfer_status', 0)
        ->where('budget_type','CAPEX')
        ->delete();
        }
    

    return response()->json(['status' => 'success']);
}
} 
