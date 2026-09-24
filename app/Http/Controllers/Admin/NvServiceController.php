<?php

namespace App\Http\Controllers\Admin;

use App\Jobs\SendEmailJob;
use Illuminate\Support\Facades\Queue;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Employee;
use Session;
use App\Models\Chat;
use App\Models\Division;
use App\Models\Department;
use App\Models\User;
use App\Models\Location;
use App\Models\Workflow;
use App\Models\NVService;
use App\Models\NVMaterial;
use App\Models\ServiceDoc;
use App\Models\ServiceBOQBulk;
use App\Models\Nvsericestatus;
use App\Models\NeedValidation;
use App\Models\TbleServiceStage;
use App\Models\Clarification;
use Config;
use App\Models\Capex;
use App\Models\Tax;
use App\Models\Opex;
use DB;
use Exception;
use Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Silber\Bouncer\Database\Role;
use App\Services\ExcelImportService;
use Illuminate\Support\Facades\Schema;
use App\Models\Boqmaterial;
use App\Models\Notification;
use Validator;
use PDF;
use ZipArchive;
use App\Models\budget;
use App\Models\CapexBudget;
use File;
use Carbon\Carbon;
use App\Models\OpexBudget;
use Illuminate\Support\Facades\Storage; 
use App\Models\OpexWorkflow;



class NvServiceController extends Controller
{
    public function __construct()
    {
        $this->middleware(function ($request, $next) {
            Session::put('active', 'employees');

            return $next($request);
        });
    }


    

    public function create_nv_service(Request $request)
    {
        $nv_id = request()->segment(4);
        $user = \Auth()->user();
        $nv = NeedValidation::where('id', $nv_id)->orderBy('id', 'desc')->first();
        $data = NeedValidation::where('user_id',$user->id)->select('fiscal_year')->first();
        $needvalidation = NeedValidation::select('id','proposal_type')->where('id', $nv_id)->orderBy('id', 'desc')->first();
        
        $service_details = "";
        $service_doc = "";
       
        $service_detail = NVService::select('*')->where('nv_id', $nv_id)->orderBy('id', 'desc')->first();
        if (empty($service_detail)) 
        {
            $service_details = "";
            $service_doc = "";
        } else 
        {

            if ($service_detail->service_id != "") 
            {

                $service_details = NVService::select('*')->where('id', $service_detail->id)->orderBy('id', 'desc')->first();
                $service_doc = ServiceDoc::select('*')->where('service_id', $service_detail->id)->orderBy('id', 'desc')->first();
            } else 
            {
                $segment_id =request()->segment(6);
                 if(!empty($segment_id)){
                    $service_details = NVService::select('*')->where('id', $segment_id)->orderBy('id', 'desc')->first();
                    $service_doc = ServiceDoc::select('*')->where('service_id', $segment_id)->orderBy('id', 'desc')->first();
                 }else{
                    $service_details = NVService::select('*')->where('id', $service_detail->id)->orderBy('id', 'desc')->first();
                    $service_doc = ServiceDoc::select('*')->where('service_id', $service_detail->id)->orderBy('id', 'desc')->first();
                 }
            }

            
        }
        $service = NVService::where('nv_id', $nv_id)->exists();
        if($service == true){
        $service_import = ServiceBOQBulk::where('nv_id', $nv_id)->where('service_id', $service_details->id)->orderBy('id', 'desc')->get(); 
        }else{
        $service_import = ServiceBOQBulk::where('nv_id', $nv_id)->orderBy('id', 'desc')->get();
        }
        $nv_year = NeedValidation::select('id', 'fiscal_year')->where('id', $nv_id)->first();
        $divisions = Division::select('id', 'name')->where('status', 1)->get();
        $employee = Employee::where('user_id', $user->id)->get();
        $firstEmployee = $employee->first();

        if (($user->role_id) != 1) {
            $departments = Department::select('id', 'name')->where('status', 1)->where('id', $firstEmployee ? $firstEmployee->department_id : null)->get();
        } else {
            $departments = Department::select('id', 'name')->where('status', 1)->get();
        }
        $userid = \Auth()->user()->id;
        $avlbgt = 0;
        $deptid = \Auth()->user()->department_id;
        $lastNv = NeedValidation::select('id','budgetary_provision','fiscal_year')
        ->where('id', '<' ,  $nv_id)
        ->whereIn('service_id', [1, 2])
        ->where('budget_type', $nv->budget_type)
        ->where('fiscal_year',$nv->fiscal_year)
        ->where('budgetary_provision', 'Approved')
        ->where('department_id', $deptid) 
        ->where('user_id', $userid) 
        ->where('delete_draft', 0) 
        ->orderBy('id', 'desc')
        ->first();

        $avl = budget::where('fiscal_year',$nv->fiscal_year)->where('dept_id',$nv->department_id)->where('budget_type', $nv->budget_type)->orderBy('id','desc')->first();
        if($nv->budget_type == "CAPEX")
        {
            
            // $budget12 = CAPEX::where('department_id',$nv->department_id)->latest('updated_at')->first();
            if(NVService::where('nv_id', $nv->id)->first()){
                $budget12 = NVService::where('nv_id', $nv->id)->where('dept_id', $nv->department_id)->first('budget_available');

            }else{

                $budget12 = CapexBudget::where('department_id', $nv->department_id)->where('fiscal_year', $nv->fiscal_year)->first('revised_budget');

            }
            
            
        }
        else
        {
            if(NVService::where('nv_id', $nv->id)->first()){
                $budget12 = NVService::where('nv_id', $nv->id)->where('dept_id', $nv->department_id)->first('budget_available');

            }else{
            $budget12 = OpexBudget::where('department_id', $nv->department_id)->where('fiscal_year', $nv->fiscal_year)->first('revised_budget');
            }
        }
//   dd(!isset($lastNv['id']));
        if (isset($nv['id']))
        {
            if (!$avl)
            {
                $avlbgt = $budget12;
            }
            else
            {
                $ser_budget = NVService::where('nv_id',$nv->id)->first('budget_available');
                if($ser_budget == null )
                {
                    $matt_budget = NVMaterial::where('nv_id',$nv->id)->first('budget_avl');

                    $budget = budget::where('fiscal_year',$nv->fiscal_year)->where('dept_id',$nv->department_id)->where('budget_type',$nv->budget_type)->first();
                    if($budget != null)
                    {
                    $lastTotalBudgetBoth = budget::where('nv_id', $budget->nv_id)->sum('total_budget');
                    $lastavlBudget = budget::where('nv_id', $budget->nv_id)->sum('budget_avl');
                    if($lastavlBudget >= $lastTotalBudgetBoth){
                        $finalamount = $lastavlBudget - $lastTotalBudgetBoth;

                    }else{
                        $finalamount = 0 ; 
                    }
                    $avlbgt = $matt_budget != null ? $matt_budget->budget_avl : $finalamount ;
                    }
                    // dd($lastavlBudget,$lastTotalBudgetBoth,$avlbgt,$matt_budget == null);  
                    // $avlbgt = $finalamount;
                    // dd($matt_budget->budget_avl,$lastTotalBudgetBoth,$lastavlBudget,$avlbgt);
                }
                else
                {
                    // dd(1);
                    $budget = budget::where('fiscal_year',$nv->fiscal_year)->where('dept_id',$nv->department_id)->where('budget_type',$nv->budget_type)->first();
                    if($budget != null)
                    {
                    $lastTotalBudgetBoth = budget::where('nv_id', $budget->nv_id)->sum('total_budget');
                    $lastavlBudget = budget::where('nv_id', $budget->nv_id)->sum('budget_avl');
                    if($lastavlBudget >= $lastTotalBudgetBoth){
                        $finalamount = $lastavlBudget - $lastTotalBudgetBoth;

                    }else{
                        $finalamount = 0 ; 
                    }
                    $avlbgt = $ser_budget == null ? $finalamount : $ser_budget->budget_available;
                    }
                    // $avlbgt =  $finalamount;
                    // dd($lastTotalBudgetBoth,$lastavlBudget,$avlbgt);
                }
            }   
                
                    
        }
       else 
        {
            
            $checkmaterialid = NVMaterial::select('id','nv_id')->where('nv_id', $lastNv->getKey())->first();
            $checkserviceid = NVService::select('id','nv_id')->where('nv_id', $lastNv->getKey())->first();
            $materialId =  $checkmaterialid ?  $checkmaterialid->id : null;
            $serviceId =  $checkserviceid ?  $checkserviceid->id : null;       
            $nv_status = Nvsericestatus::where('material_id', $materialId)
                                        // ->where('nv_id','<',$nv_id)
                                        ->orWhere('service_id', $serviceId)
                                        ->orderBy('id', 'desc')
                                        ->first();
            $nv1 = $nv->where('budgetary_provision','Additional')->orderBy('id','desc')->first(); 

        //   dd($nv_status->hod_status);
            if ($nv_status->hod_status == 2 || $nv_status->ces_rew1_status == 2 || $nv_status->ces_rew2_status == 2 || $nv_status->ces_rew3_status == 2 || $nv_status->ces_rew4_status == 2 || $nv_status->work_rew1_status == 2 || $nv_status->work_rew2_status == 2 || $nv_status->work_rew3_status == 2 || $nv_status->work_rew4_status == 2 || $nv_status->approver_status == 2 || $nv_status->work_rew1dep2_status == 2 || $nv_status->work_rew2dep2_status == 2 || $nv_status->work_rew3dep2_status == 2 || $nv_status->work_rew4dep2_status == 2 || $nv_status->approverdep2_status == 2 || $nv_status->work_rew1dep3_status == 2 || $nv_status->work_rew2dep3_status == 2 || $nv_status->work_rew3dep3_status == 2 || $nv_status->work_rew4dep3_status == 2 || $nv_status->approverdep3_status == 2 || $nv_status->work_rew1dep4_status == 2 || $nv_status->work_rew2dep4_status == 2 || $nv_status->work_rew3dep4_status == 2 || $nv_status->work_rew4dep4_status == 2 || $nv_status->approverdep4_status == 2 || $nv_status->work_rew1dep5_status == 2 || $nv_status->work_rew2dep5_status == 2 || $nv_status->work_rew3dep5_status == 2 || $nv_status->work_rew4dep5_status == 2 || $nv_status->approverdep5_status == 2)
            {
                $mat_budget = NVMaterial::where('nv_id',$nv_status->nv_id)->first();
            
                if($mat_budget == null)
                {
                    $ser_budget = NVService::where('nv_id',$nv_status->nv_id)->first();
                    
                    $budget = budget::where('fiscal_year',$nv->fiscal_year)->where('dept_id',$nv->department_id)->where('budget_type',$nv->budget_type)->first();
                    $budget_total = $budget->total_budget - $ser_budget->total_buget;
                    $lastavlBudget = budget::where('nv_id', $budget->id)->sum('budget_avl');
                    if($lastavlBudget >= $budget_total){
                        $finalamount = $lastavlBudget - $budget_total;
                    }else{
                        $finalamount = 0 ;
                    }
                    $avlbgt = $mat_budget == null ? $finalamount : $mat_budget->budget_avl;
                    // dd($avlbgt);

                }
                else
                {
                    $budget = budget::where('fiscal_year',$nv->fiscal_year)->where('dept_id',$nv->department_id)->where('budget_type',$nv->budget_type)->first();
                    $budget_total = $budget->total_budget - $mat_budget->total_budget_both;
                    $lastavlBudget = budget::where('nv_id', $budget->id)->sum('budget_avl');
                    if($lastavlBudget >= $budget_total){
                        $finalamount = $lastavlBudget - $budget_total;
                    }else{
                        $finalamount = 0 ;
                    }
                    $avlbgt = $mat_budget == null ? $finalamount : $mat_budget->budget_avl;
                   
                }
                $available_budget = NeedValidation::select('delete_draft','tbl_material.total_budget_both','tbl_service.total_buget','needvalidations.id','nvservicestatus.service_id','nvservicestatus.material_id')
                ->leftJoin('nvservicestatus','nvservicestatus.nv_id','=','needvalidations.id')
                ->leftJoin('tbl_material','tbl_material.nv_id','=','needvalidations.id')
                ->leftJoin('tbl_service','tbl_service.nv_id','=','needvalidations.id')
                ->where('fiscal_year', $data->fiscal_year) 
                ->where('delete_draft',0)
                ->orderBy('id', 'desc')
                ->get();
            }
            elseif($nv_status->ceo_status == 1 && $nv->budgetary_provision == "Additional")
            {
                if(isset($nv_status['material_id']))
                {
                    $lastTotalBudgetBoth1 = NVMaterial::where('nv_id', $lastNv->id-1)->sum('budget_avl');
                    $lastavlBudget1 = NVMaterial::where('nv_id', $nv1->id)->sum('total_budget_both');
                    if($lastavlBudget1 >= $lastTotalBudgetBoth1){
                        $finalamount1 = $lastavlBudget1 - $lastTotalBudgetBoth1;

                    }else{
                        $finalamount1 = 0 ; 
                    }
                    $avlbgt = $finalamount1;
                }
                if(isset($nv_status['service_id']))
                {
                    $lastTotalBudgetBoth = NVService::where('nv_id', $nv1->id)->sum('total_buget');
                    $lastavlBudget = NVService::where('nv_id', $lastNv->id)->sum('budget_available');
                    if($lastavlBudget >= $lastTotalBudgetBoth){
                        $finalamount = $lastavlBudget - $lastTotalBudgetBoth;

                    }else{
                        $finalamount = 0 ; 
                    }
                    $avlbgt = $finalamount;
                }  

                $available_budget = NeedValidation::select('delete_draft','tbl_material.total_budget_both','tbl_service.total_buget','needvalidations.id','nvservicestatus.service_id','nvservicestatus.material_id')
                ->leftJoin('nvservicestatus','nvservicestatus.nv_id','=','needvalidations.id')
                ->leftJoin('tbl_material','tbl_material.nv_id','=','needvalidations.id')
                ->leftJoin('tbl_service','tbl_service.nv_id','=','needvalidations.id')
                ->where('fiscal_year', $data->fiscal_year) 
                ->where('delete_draft',0)
                ->orderBy('id', 'desc')
                ->get();
            }
            else
            {
                // dd(1);
                $ser_budget = NVService::where('nv_id',$nv->id)->first('budget_available');
                // dd($ser_budget);
                if($ser_budget == null)
                {
                    $mat_budget = NVMaterial::where('nv_id',$nv->id)->first('budget_avl');
                    // dd($nv->id,$mat_budget);
                    $budget = budget::where('fiscal_year',$nv->fiscal_year)->where('dept_id',$nv->department_id)->where('budget_type',$nv->budget_type)->first();
                    $lastTotalBudgetBoth = budget::where('nv_id', $budget->nv_id)->sum('total_budget');
                    $lastavlBudget = budget::where('nv_id', $budget->nv_id)->sum('budget_avl');
                    if($lastavlBudget >= $lastTotalBudgetBoth){
                        $finalamount = $lastavlBudget - $lastTotalBudgetBoth;

                    }else{
                        $finalamount = 0 ; 
                    }
                    $avlbgt = $mat_budget == null ? $finalamount : $mat_budget->budget_avl;  
                    // $avlbgt = $finalamount;
                    // dd($mat_budget->budget_avl,$lastTotalBudgetBoth,$lastavlBudget,$avlbgt);
                }
                else
                {
                    $budget = budget::where('fiscal_year',$nv->fiscal_year)->where('dept_id',$nv->department_id)->where('budget_type',$nv->budget_type)->first();
                    $lastTotalBudgetBoth = budget::where('nv_id', $budget->nv_id)->sum('total_budget');
                    $lastavlBudget = budget::where('nv_id', $budget->nv_id)->sum('budget_avl');
                    if($lastavlBudget >= $lastTotalBudgetBoth){
                        $finalamount = $lastavlBudget - $lastTotalBudgetBoth;

                    }else{
                        $finalamount = 0 ; 
                    }
                    $avlbgt = $ser_budget == null ? $finalamount : $ser_budget->budget_available;  
                    // dd($ser_budget,$avlbgt);
                }
                
            }
        }
    
        $latestData = Nvsericestatus::where('nv_id',$nv_id)->get();
        $statusFields = [
            'ceo_status',
            'rv1_status',
            'rv2_status',
            'rv3_status',
            'rv4_status',
            'hod_status',
            'ces_rew1_status',
            'ces_rew2_status',
            'ces_rew3_status',
            'ces_rew4_status',
            'ces_status',
            'work_rew1_status',
            'work_rew2_status',
            'work_rew3_status',
            'work_rew4_status',
            'cpmg_status',
            'work_rew1dep2_status',
            'work_rew2dep2_status',
            'work_rew3dep2_status',
            'work_rew4dep2_status',
            'cto_status',
            'work_rew1dep3_status',
            'work_rew2dep3_status',
            'work_rew3dep3_status',
            'work_rew4dep3_status',
            'ceo_nominee_status',
            'work_rew1dep4_status',
            'work_rew2dep4_status',
            'work_rew3dep4_status',
            'work_rew4dep4_status',
            'ceo_nominee2_status',
            'groupcio_status',
        ];

        $latest = $latestData->filter(function ($data) use ($statusFields) {
            foreach ($statusFields as $field) {
                if ($data->$field == 2) {
                    return true;
                }
            }
            return false;
        });
        $locations = Location::select('id', 'name')->where('status', 1)->get();
        $role = Role::select('id', 'title',)->where('id', '!=', 1)->get();
        $taxes = Tax::select('id', 'tax',)->where('status',1)->get();
        $currentUrl = url()->current();
        $request->session()->put('current_url', $currentUrl);
        return view('admin.nvService.create', compact('taxes','service_import','latest','divisions','needvalidation' ,'locations', 'role', 'departments', 'service_details', 'service_doc','nv_year','nv','avlbgt'));
    }
    public function store_nv_service(Request $request)
    {

        $nv = NeedValidation::where('id',$request['nv_id'])->first();
        $total_ser_amo =$request->total_ser_amo;
        $budget_available = preg_replace('/[^\d.]/', '', $request->budget_avl);
        // dd($budget_available);
        try {

            $request_input = $request->except('_token');

        $status = $request->status;
        $draft = $request->draft;
        $just_of_proposal = $request->just_of_proposal;
        $broad_just = $request->broad_just;
        // dd( $just_of_proposal);
        $background = $request->background;
        $approved_nv =  NeedValidation::where('id',$request_input['nv_id'])->first();

        if($approved_nv->budgetary_provision == 'Approved'){
            if(!empty($request_input['tax_amount1'])){
                $total_mat_mat = $request_input['total_matyear1'] + (($request_input['total_matyear1'] * $request_input['tax_amount1']) / 100) ?? null;
            }else{
                $total_mat_mat = $request_input['total_matyear1'] ?? null;
            }
            if(!empty($request_input['tax_amount2'])){
                $total_mat_mat2 = $request_input['total_matyear2'] + (($request_input['total_matyear2'] * $request_input['tax_amount2']) / 100) ?? null;
            }else{
                $total_mat_mat2 = $request_input['total_matyear2'] ?? null;
            }
    
           if(!empty($request_input['tax_amount3'])){
                $total_mat_mat3 = $request_input['total_matyear3'] + (($request_input['total_matyear3'] * $request_input['tax_amount3']) / 100) ?? null;
            }else{
                $total_mat_mat3 = $request_input['total_matyear3'] ?? null;
            }
        }
  if($status == "submit_nv"){

    if (empty($request['service_id'])) {


        $rules = [
            // 'dop_ref_no' => 'required|numeric|unique:tbl_service,dop_ref_no,except,id',
            // 'short_code' => 'required|string|max:10|unique:divisions,short_code,except,id',
        ];

        $messages = [
            // 'dop_ref_no.unique' => 'Thid DOP ref no has already been taken',
            // 'name.max' => 'Division name should not be more than 50 characters',
            // 'short_code.required' => 'Please enter division short code',
            // 'short_code.max' => 'short code should not be more than 10 characters',

        ];
        $validator = Validator::make($request_input, $rules, $messages);
        if ($validator->fails()) {
            $response['msg'] = $validator->errors()->toArray();
            $response['result'] = 'error';
        } else {
        
        $nvservice = NVService::create([
            'dept_id' => $request_input['dept_id'],
            'nv_id' => $request_input['nv_id'],
            'company_id' => $request_input['company_id'],
            'draft' =>  $draft,
            'user_id' => \Auth::user()->id,
            'dop_ref_no' => $request_input['dop_ref_no'],
            'proposal_name' => $request_input['proposal_name'],
            'derc_ref_no' => $request_input['derc_ref_no']?? null,
            'derc_approval' => $request_input['derc_approval']?? null,
            'derc_app_date' => $request_input['derc_app_date']?? null,
            'prop_number' => $request_input['prop_number']?? null,
            'special_remarks' => $request_input['special_remarks'],
            'background' => $background,
            'just_of_proposal' => $just_of_proposal,
            'broad_just' => $broad_just,
            'past_3_year_actual_cost_fy' => implode(',', $request_input['past_3_year_actual_cost_fy']),
            'past_3_year_actual_cost' => implode(',', $request_input['past_3_year_actual_cost']),
            'past_3_year_actual_cost_service' => implode(',', $request_input['past_3_year_actual_cost_service']),
            'benefit' => $request_input['benefit'],
            'implements_years' =>$request_input['implements_years'],
            'implementation_period_from' => implode('.,', $request->input('imp_from')),
            'implementation_period_to' => implode(',', $request->input('imp_to')),
            'implementation_plan_year_wise' => implode('.,', $request->input('imp_plan')),
          //  'type_of_proposal' => $request_input['type_of_proposal'],
            'mode_of_award_of_service' => $request_input['mode_of_award_of_service'],
            'amc_proposal_sdate' => $request_input['amc_proposal_sdate'],
            'amc_proposal_edate' => $request_input['amc_proposal_edate'],
            'budget_available' => $budget_available,
            'estimate_amount_of_service' => $request_input['estimate_amount_of_service'],
            'estimate_amount_of_service_civil' => $request_input['estimate_amount_of_service_civil'],
            'tax1' => $request_input['tax1'] ?? null,
            'tax2' => $request_input['tax2'] ?? null,
            'tax3' => $request_input['tax3'] ?? null,
            'tax4' => $request_input['tax4'] ?? null,
            'tax5' => $request_input['tax5'] ?? null,
            'estimate_amount_of_rr_chnage' => $request_input['estimate_amount_of_rr_chnage'],
            'estimate_amount_other' => $request_input['estimate_amount_other'],
            'total_buget' => $request_input['total_buget'],
            'add_budget' => $request_input['add_budget'] ?? null,
            'approved_budget' => $request_input['approved_budget'] ?? null,
            'total_ser_amo' => $request_input['total_ser_amo'],
            'service_amount' => implode(',',$request_input['service_amount']),
            'service_description'=> implode(',',$request_input['service_description']),
            'cause_analysis' => $request_input['cause_analysis'],
            'past_practice_text' => $request_input['past_practice_text'],
            'total_mat_mat' => $total_mat_mat ?? null,
            'total_mat_mat2' => $total_mat_mat2 ?? null,
            'total_mat_mat3' => $total_mat_mat3 ?? null,
            'total_matyear1' => $request_input['total_matyear1'] ?? null,
            'total_matyear2' => $request_input['total_matyear2'] ?? null,
            'total_matyear3' => $request_input['total_matyear3'] ?? null,
            'tax_amount1' => $request_input['tax_amount1'] ?? null,
            'tax_amount2' => $request_input['tax_amount2'] ?? null,
            'tax_amount3' => $request_input['tax_amount3'] ?? null,
            // $data['created_by'] = \Auth::user()->id;
        ]);
        $nv = NeedValidation::where('id',$request_input['nv_id'])->first();
        if($nv->budgetary_provision == "Approved")
        {
            $bud_from_log = budget::where('dept_id', $request_input['dept_id'])->where('budget_type',$nv->budget_type)->where('fiscal_year',$nv->fiscal_year)->first('total_budget');
            $tot = $bud_from_log->total_budget == null ? 0 : $bud_from_log->total_budget;
            $budget =budget::where('dept_id', $request_input['dept_id'])->where('budget_type',$nv->budget_type)->where('fiscal_year',$nv->fiscal_year)->update([
                'total_budget' =>  ($tot +  $request_input['total_buget'])-$request_input['add_budget'],
                'service_id' => $nv->service_id,
            ]);

            if(!empty($request_input['implements_years'])){
                $Impyear = explode('-',$nv->fiscal_year);
                $nextStartYear1 = $Impyear[0] + 1;
                $nextEndYear1 = $Impyear[1] + 1;
                $nextStartYear2 = $Impyear[0] + 2;
                $nextEndYear2 = $Impyear[1] + 2;
                $nextYear1 = "{$nextStartYear1}-{$nextEndYear1}";
                $nextYear2 = "{$nextStartYear2}-{$nextEndYear2}";

                // if(!empty($total_mat_mat2)){
                //     $bud_from1 = budget::where('dept_id', $request_input['dept_id'])->where('budget_type',$nv->budget_type)->where('fiscal_year',$nextYear1)->first();
                //     if($bud_from1){
                //         $tot = $bud_from1->total_budget == null ? 0 : $bud_from1->total_budget;
                //         $budget =budget::where('dept_id',$nv->department_id)->where('budget_type',$nv->budget_type)->where('fiscal_year',$nextYear1)->update([
                //             'total_budget' => $tot + ($total_mat_mat2)
                //         ]);
                //     }
                // }

                // if(!empty($total_mat_mat3)){
                //     $bud_from2 = budget::where('dept_id', $request_input['dept_id'])->where('budget_type',$nv->budget_type)->where('fiscal_year',$nextYear2)->first();
                //     if($bud_from2){
                //         $tot = $bud_from2->total_budget == null ? 0 : $bud_from2->total_budget;
                //         $budget =budget::where('dept_id',$nv->department_id)->where('budget_type',$nv->budget_type)->where('fiscal_year',$nextYear2)->update([
                //             'total_budget' => $tot + ($total_mat_mat3)
                //         ]);
                //     }
                // }

                if($nv->budget_type == "CAPEX")
                {
                    $provision_budget2 = CapexBudget::where('department_id', $nv->department_id)->where('fiscal_year', $nextYear1)->first();
                    $provision_budget3 = CapexBudget::where('department_id', $nv->department_id)->where('fiscal_year', $nextYear2)->first();

                if($provision_budget2){
                if(!empty($total_mat_mat2)){
                    if ($nextYear1 == $provision_budget2->fiscal_year )
                    {
                     if($provision_budget2->provision_budget == null){
                            $capex_budget_approve =  CapexBudget::where('department_id', $nv->department_id)->where('fiscal_year',$provision_budget2->fiscal_year)->update([
                                'provision_budget' => $total_mat_mat2
                            ]);
                        }else{
                            $capexbudget =  $provision_budget2->provision_budget;
                            $total = $capexbudget + $total_mat_mat2;
                            $capex_budget_approve =  CapexBudget::where('department_id', $nv->department_id)->where('fiscal_year',$provision_budget2->fiscal_year)->update([
                                'provision_budget' => $total 
                            ]);
                        }
                    
                    }
                }
              }
                if($provision_budget3){
                    if(!empty($total_mat_mat3)){
                        if ($nextYear2 == $provision_budget3->fiscal_year )
                        {
                            if($provision_budget3->provision_budget == null){
                                $capex_budget_approve =  CapexBudget::where('department_id', $nv->department_id)->where('fiscal_year',$provision_budget3->fiscal_year)->update([
                                    'provision_budget' => $total_mat_mat3
                                ]);
                            }else{
                                $capexbudget =  $provision_budget3->provision_budget;
                                $total = $capexbudget + $total_mat_mat3;
                                $capex_budget_approve =  CapexBudget::where('department_id', $nv->department_id)->where('fiscal_year',$provision_budget3->fiscal_year)->update([
                                    'provision_budget' => $total 
                                ]);
                            }
                        
                        }
                    }
                }
                }elseif($nv->budget_type == "OPEX"){
                    $provision_budget2 = OpexBudget::where('department_id', $nv->department_id)->where('fiscal_year', $nextYear1)->first();
                    $provision_budget3 = OpexBudget::where('department_id', $nv->department_id)->where('fiscal_year', $nextYear2)->first();

                if($provision_budget2){
                if(!empty($total_mat_mat2)){
                    if ($nextYear1 == $provision_budget2->fiscal_year )
                    {
                     if($provision_budget2->provision_budget == null){
                            $capex_budget_approve =  OpexBudget::where('department_id', $nv->department_id)->where('fiscal_year',$provision_budget2->fiscal_year)->update([
                                'provision_budget' => $total_mat_mat2
                            ]);
                        }else{
                            $OpexBudget =  $provision_budget2->provision_budget;
                            $total = $OpexBudget + $total_mat_mat2;
                            $capex_budget_approve =  OpexBudget::where('department_id', $nv->department_id)->where('fiscal_year',$provision_budget2->fiscal_year)->update([
                                'provision_budget' => $total 
                            ]);
                        }
                    
                    }
                }
               }
                if($provision_budget3){
                    if(!empty($total_mat_mat3)){
                        if ($nextYear2 == $provision_budget3->fiscal_year )
                        {
                            if($provision_budget3->provision_budget == null){
                                $capex_budget_approve =  OpexBudget::where('department_id', $nv->department_id)->where('fiscal_year',$provision_budget3->fiscal_year)->update([
                                    'provision_budget' => $total_mat_mat3
                                ]);
                            }else{
                                $OpexBudget =  $provision_budget3->provision_budget;
                                $total = $OpexBudget + $total_mat_mat3;
                                $capex_budget_approve =  OpexBudget::where('department_id', $nv->department_id)->where('fiscal_year',$provision_budget3->fiscal_year)->update([
                                    'provision_budget' => $total 
                                ]);
                            }
                        
                        }
                    }
                }
                }
               
                 
                }
        }
        
        $count = Nvsericestatus::where('nv_id', $request_input['nv_id'])->count();
        $version_nv = ($count >= 1) ? $request_input['nv_id'] . '-v' . ($count + 1) : (string) $request_input['nv_id'];
        $nvservicestatus = new Nvsericestatus();
        $nvservicestatus->service_id = $nvservice->id;
        $nvservicestatus->nv_id = $request_input['nv_id'];
        $nvservicestatus->company_id = $request_input['company_id'];
        $nvservicestatus->draft = $draft;
        $nvservicestatus->version_nv = $version_nv;
        
        if ($nv->budget_type == "CAPEX") {
            $nvservicestatus->derc_info = '1';
        } elseif ($nv->budget_type == "OPEX") {
            $nvservicestatus->derc_info = !empty($request_input['prop_number']) ? '1' : '0';
        }
        
        $nvservicestatus->save();
        
        NeedValidation::where('id', $request_input['nv_id'])
        ->update(['proposal_type' => $request_input['proposal_type']]);  

        if (!empty($nvservice)) {
            $serviceId = $nvservice->id;
            $data = [];

            $data['service_id'] = $serviceId;
            $fileFields = [
                'cost_calculation_for_service',
                'copy_of_previous_work',
                'copy_of_derc_other',
                'consuption_details',
                'buget_stmt_for_both',
                'material_procurement',
                'photographs_of_product',
                'vend_quatation',
                'vendor_quatation',
                'others',
                'cm_rate_ref',
                'vendor_quat',
                'last_purchase_price',
                'user_estimation',
                'previous_wo_rc',
                'past_practice',
                'special_attch',
                'just_prop_upload',
            ];
            foreach ($fileFields as $fieldName) {
                if ($request->hasFile($fieldName)) {
                    $files = $request->file($fieldName);
            
                    if (is_array($files)) {
                        // Handle multiple files for 'others' field
                        $fileNames = [];
            
                        foreach ($files as $file) {
                            $newFileName = $file->getClientOriginalName();
                            $filePath = public_path('services-doc/' . $newFileName);
            
                            // Check if a file with the same name already exists
                            if (!File::exists($filePath)) {
                                $file->move(public_path('services-doc'), $newFileName);
                            }
            
                            $fileNames[] = $newFileName;
                        }
            
                        $data[$fieldName] = implode(',', $fileNames); // Convert array to string
                    } else {
                        // Handle single file for other fields
                        $file = $files;
                        $newFileName = $file->getClientOriginalName();
                        $filePath = public_path('services-doc/' . $newFileName);
            
                        // Check if a file with the same name already exists
                        if (!File::exists($filePath)) {
                            $file->move(public_path('services-doc'), $newFileName);
                        }
            
                        $data[$fieldName] = $newFileName;
                    }
                }
            }
            
          
       
            $data['created_by'] = \Auth::user()->id;
            $document = ServiceDoc::create($data);
        }
        $response['result'] = 'success';
        $response['msg'] = 'NV Service Created';
        if (Auth::user()->role_id == 9) {
            $nv_id = $request->nv_id;
            $user_id = Auth::user()->id;
            $employees = Employee::where("user_id", $user_id)->first();
            $departmentIds = explode(',', $employees->department_id);
            $departments = Department::whereIn("id", $departmentIds)->get();

            foreach ($departments as $department) {
                $hod = $department->dep_hod;
                $rv1 = $department->dep_rew1;
                $rv2 = $department->dep_rew2;
                $rv3 = $department->dep_rew3;
                $rv4 = $department->dep_rew4;
            
            }
            if(!empty($rv1)){
                $emp_rv1 = Employee::where('user_id', $department->dep_rew1)->first();
            }elseif(!empty($rv2)){
                $emp_rv1 = Employee::where('user_id', $department->dep_rew2)->first();
            }elseif(!empty($rv3)){
                $emp_rv1 = Employee::where('user_id', $department->dep_rew3)->first();
            }elseif(!empty($rv4)){
                $emp_rv1 = Employee::where('user_id', $department->dep_rew4)->first();
            }else{
                $emp_rv1 = Employee::where('user_id', $department->dep_hod)->first();
            }
            $to_emails = $emp_rv1->email;
            // $depart = Department::where("id",$employees->department_id)->first();
            $initiated_date = NVService::where('user_id', $user_id)->where('nv_id',$nv_id)->first();
            $initiated_by =  Employee::where('user_id', $initiated_date->user_id)->first();
            // $nv_type = NeedValidation::where('user_id', $user_id)->where('id', $nv_id)->first();
            $ini_date = NVService::where('user_id', $user_id)->where('nv_id',$nv_id)->first();
            $ini_by =  Employee::where('user_id', $ini_date->user_id)->first();

            $tble_service = NVService::where('nv_id', $nv_id)->first();
            $user = User::select('email','name','id')->where('id', $tble_service->user_id)->first();
            $nv_type = NeedValidation::where('id', $nv_id)->first();
            $depart = Department::where("id",  $nv_type->department_id)->first();
            // dd($ini_by);
            //   dd($to_emails);
            //   $to_emails="hareram.y@redianglobal.com";
            $p1 = "You have a new request that requires your approval:";
            $p2 = "Please review the request and take appropriate action.";
            $remark = "";
            //  send mail 
            Mail::send('emailtemp.doc_mail', ['username' => 'user','initiated_date' => $initiated_date,'initiated_by' => $initiated_by, 'ini_date' => $ini_date,'ini_by' => $ini_by,'p1' => $p1, 'p2' => $p2, 'remark' => $remark ?? '' , 'department' => $depart,'nv_type'=>$nv_type], function ($message) use ($to_emails) {
                $message->from(env('MAIL_FROM_ADDRESS'), 'NV');
                $message->to($to_emails);

                $message->subject("Need Validation Status Update : Seeking your validation");
            });
            $serviceType = ($nv_type->service_id == 1) ? 'Material' : 'Service';
            $subject = "Your NV (NV/{$nv_type->budget_type}/{$nv_type->fiscal_year}/{$depart->name}/{$serviceType}/{$nv_type->id}) has been submitted";
            $to_emails = $user->email;
            Mail::send('emailtemp.nvsubmit_mail', ['user' => $user, 'initiated_date' => $initiated_date, 'initiated_by' => $initiated_by, 'ini_date' => $ini_date,'ini_by' => $ini_by, 'p1' => $p1, 'p2' => $p2, 'nv_type'=>$nv_type,'remark' => $remark ?? '', 'department' => $depart, 'status' => $status,], function ($message) use ($to_emails,$subject) {
                $message->from(env('MAIL_FROM_ADDRESS'), 'NV');
                $message->to($to_emails);
                // //$message->cc('raushan@rediansoftware.com');
                $message->subject($subject);
            });
        }
        }
    } else {
        $service_id = $request['service_id'];
        $nvm = NVService::find($service_id);
        $nv_status = Nvsericestatus::where('service_id', $nvm->id)->first();
        $nv_stage = DB::table('capex_workflows_status')->where('service_id', $nvm->id)->get();

        $allStagesZero = true;
        $anyStageTwo = false;
        
        if ($nv_stage->count()) {
            foreach ($nv_stage as $stage) {
                if ($stage->nv_stage_status != 0) {
                    $allStagesZero = false;
                }
                if ($stage->nv_stage_status == 2) {
                    $anyStageTwo = true;
                }
            }
        }

        if ($nv_status->rv1_status == 0 && 
        $nv_status->rv2_status == 0 && 
        $nv_status->rv3_status == 0 && 
        $nv_status->rv4_status == 0 && 
        $nv_status->hod_status == 0 && 
        $nv_status->groupcio_status == 0 &&
        $allStagesZero) {
            // dd('hi');
            NVService::find($service_id)->update([
                'dept_id' => $request_input['dept_id'],
                'nv_id' => $request_input['nv_id'],
                'draft' =>  $draft,
                'company_id' => $request_input['company_id'],
                'user_id' => \Auth::user()->id,
                'dop_ref_no' => $request_input['dop_ref_no'],
                'proposal_name' => $request_input['proposal_name'],
                'background' => $background,
                'just_of_proposal' => $just_of_proposal,
                'broad_just' => $broad_just,
                'special_remarks' => $request_input['special_remarks'],
                'derc_ref_no' => $request_input['derc_ref_no']?? null,
                'derc_approval' => $request_input['derc_approval']?? null,
                'derc_app_date' => $request_input['derc_app_date']?? null,
                'prop_number' => $request_input['prop_number']?? null,
                'past_3_year_actual_cost_fy' => implode(',', $request_input['past_3_year_actual_cost_fy']),
                'past_3_year_actual_cost' => implode(',', $request_input['past_3_year_actual_cost']),
                'past_3_year_actual_cost_service' => implode(',', $request_input['past_3_year_actual_cost_service']),
                'benefit' => $request_input['benefit'],
                'implements_years' =>$request_input['implements_years'],
                'implementation_period_from' => implode(',', $request->input('imp_from')),
                'implementation_period_to' => implode(',', $request->input('imp_to')),
                'implementation_plan_year_wise' => implode('.,', $request->input('imp_plan')),
               // 'type_of_proposal' => $request_input['type_of_proposal'],
                'mode_of_award_of_service' => $request_input['mode_of_award_of_service'],
                'amc_proposal_sdate' => $request_input['amc_proposal_sdate'],
                'amc_proposal_edate' => $request_input['amc_proposal_edate'],
                'budget_available' => $budget_available,
                'estimate_amount_of_service' => $request_input['estimate_amount_of_service'],
                'estimate_amount_of_service_civil' => $request_input['estimate_amount_of_service_civil'],
                'tax1' => $request_input['tax1'] ?? null,
                'tax2' => $request_input['tax2'] ?? null,
                'tax3' => $request_input['tax3'] ?? null,
                'tax4' => $request_input['tax4'] ?? null,
                'tax5' => $request_input['tax5'] ?? null,
                'estimate_amount_of_rr_chnage' => $request_input['estimate_amount_of_rr_chnage'],
                'estimate_amount_other' => $request_input['estimate_amount_other'],
                'total_buget' => $request_input['total_buget'],
                'add_budget' => $request_input['add_budget'] ?? null,
                'approved_budget' => $request_input['approved_budget'] ?? null,
                'service_amount' => implode(',',$request_input['service_amount']),
                'total_ser_amo' => $request_input['total_ser_amo'],
                'service_description'=> implode(',',$request_input['service_description']),
                'cause_analysis' => $request_input['cause_analysis'],
                'past_practice_text' => $request_input['past_practice_text'],
                'total_mat_mat' => $total_mat_mat ?? null,
            'total_mat_mat2' => $total_mat_mat2 ?? null,
            'total_mat_mat3' => $total_mat_mat3 ?? null,
            'total_matyear1' => $request_input['total_matyear1'] ?? null,
            'total_matyear2' => $request_input['total_matyear2'] ?? null,
            'total_matyear3' => $request_input['total_matyear3'] ?? null,
            'tax_amount1' => $request_input['tax_amount1'] ?? null,
            'tax_amount2' => $request_input['tax_amount2'] ?? null,
            'tax_amount3' => $request_input['tax_amount3'] ?? null,
                
                // $data['created_by'] = \Auth::user()->id;
            ]);
            $nv = NeedValidation::where('id',$request_input['nv_id'])->first();
        if($nv->budgetary_provision == "Approved")
        {
            $bud_from_log = budget::where('dept_id', $request_input['dept_id'])->where('budget_type',$nv->budget_type)->where('fiscal_year',$nv->fiscal_year)->first('total_budget');
            $tot = $bud_from_log->total_budget == null ? 0 : $bud_from_log->total_budget;
            $budget =budget::where('dept_id', $request_input['dept_id'])->where('budget_type',$nv->budget_type)->where('fiscal_year',$nv->fiscal_year)->update([
                'total_budget' =>  ($tot +  $request_input['total_buget'])-$request_input['add_budget'],
                'service_id' => $nv->service_id,
            ]);
            if(!empty($request_input['implements_years'])){
                $Impyear = explode('-',$nv->fiscal_year);
                $nextStartYear1 = $Impyear[0] + 1;
                $nextEndYear1 = $Impyear[1] + 1;
                $nextStartYear2 = $Impyear[0] + 2;
                $nextEndYear2 = $Impyear[1] + 2;
                $nextYear1 = "{$nextStartYear1}-{$nextEndYear1}";
                $nextYear2 = "{$nextStartYear2}-{$nextEndYear2}";

                // if(!empty($total_mat_mat2)){
                //     $bud_from1 = budget::where('dept_id', $request_input['dept_id'])->where('budget_type',$nv->budget_type)->where('fiscal_year',$nextYear1)->first();
                //     if($bud_from1){
                //         $tot = $bud_from1->total_budget == null ? 0 : $bud_from1->total_budget;
                //         $budget =budget::where('dept_id',$nv->department_id)->where('budget_type',$nv->budget_type)->where('fiscal_year',$nextYear1)->update([
                //             'total_budget' => $tot + ($total_mat_mat2)
                //         ]);
                //     }
                // }

                // if(!empty($total_mat_mat3)){
                //     $bud_from2 = budget::where('dept_id', $request_input['dept_id'])->where('budget_type',$nv->budget_type)->where('fiscal_year',$nextYear2)->first();
                //     if($bud_from2){
                //         $tot = $bud_from2->total_budget == null ? 0 : $bud_from2->total_budget;
                //         $budget =budget::where('dept_id',$nv->department_id)->where('budget_type',$nv->budget_type)->where('fiscal_year',$nextYear2)->update([
                //             'total_budget' => $tot + ($total_mat_mat3)
                //         ]);
                //     }
                // }

                if($nv->budget_type == "CAPEX")
                {
                    $provision_budget2 = CapexBudget::where('department_id', $nv->department_id)->where('fiscal_year', $nextYear1)->first();
                    $provision_budget3 = CapexBudget::where('department_id', $nv->department_id)->where('fiscal_year', $nextYear2)->first();

                if($provision_budget2){
                if(!empty($total_mat_mat2)){
                    if ($nextYear1 == $provision_budget2->fiscal_year )
                    {
                     if($provision_budget2->provision_budget == null){
                            $capex_budget_approve =  CapexBudget::where('department_id', $nv->department_id)->where('fiscal_year',$provision_budget2->fiscal_year)->update([
                                'provision_budget' => $total_mat_mat2
                            ]);
                        }else{
                            $capexbudget =  $provision_budget2->provision_budget;
                            $total = $capexbudget + $total_mat_mat2;
                            $capex_budget_approve =  CapexBudget::where('department_id', $nv->department_id)->where('fiscal_year',$provision_budget2->fiscal_year)->update([
                                'provision_budget' => $total 
                            ]);
                        }
                    
                    }
                }
              }
                if($provision_budget3){
                    if(!empty($total_mat_mat3)){
                        if ($nextYear2 == $provision_budget3->fiscal_year )
                        {
                            if($provision_budget3->provision_budget == null){
                                $capex_budget_approve =  CapexBudget::where('department_id', $nv->department_id)->where('fiscal_year',$provision_budget3->fiscal_year)->update([
                                    'provision_budget' => $total_mat_mat3
                                ]);
                            }else{
                                $capexbudget =  $provision_budget3->provision_budget;
                                $total = $capexbudget + $total_mat_mat3;
                                $capex_budget_approve =  CapexBudget::where('department_id', $nv->department_id)->where('fiscal_year',$provision_budget3->fiscal_year)->update([
                                    'provision_budget' => $total 
                                ]);
                            }
                        
                        }
                    }
                }
                }elseif($nv->budget_type == "OPEX"){
                    $provision_budget2 = OpexBudget::where('department_id', $nv->department_id)->where('fiscal_year', $nextYear1)->first();
                    $provision_budget3 = OpexBudget::where('department_id', $nv->department_id)->where('fiscal_year', $nextYear2)->first();

                if($provision_budget2){
                if(!empty($total_mat_mat2)){
                    if ($nextYear1 == $provision_budget2->fiscal_year )
                    {
                     if($provision_budget2->provision_budget == null){
                            $capex_budget_approve =  OpexBudget::where('department_id', $nv->department_id)->where('fiscal_year',$provision_budget2->fiscal_year)->update([
                                'provision_budget' => $total_mat_mat2
                            ]);
                        }else{
                            $OpexBudget =  $provision_budget2->provision_budget;
                            $total = $OpexBudget + $total_mat_mat2;
                            $capex_budget_approve =  OpexBudget::where('department_id', $nv->department_id)->where('fiscal_year',$provision_budget2->fiscal_year)->update([
                                'provision_budget' => $total 
                            ]);
                        }
                    
                    }
                }
               }
                if($provision_budget3){
                    if(!empty($total_mat_mat3)){
                        if ($nextYear2 == $provision_budget3->fiscal_year )
                        {
                            if($provision_budget3->provision_budget == null){
                                $capex_budget_approve =  OpexBudget::where('department_id', $nv->department_id)->where('fiscal_year',$provision_budget3->fiscal_year)->update([
                                    'provision_budget' => $total_mat_mat3
                                ]);
                            }else{
                                $OpexBudget =  $provision_budget3->provision_budget;
                                $total = $OpexBudget + $total_mat_mat3;
                                $capex_budget_approve =  OpexBudget::where('department_id', $nv->department_id)->where('fiscal_year',$provision_budget3->fiscal_year)->update([
                                    'provision_budget' => $total 
                                ]);
                            }
                        
                        }
                    }
                }
                }
               
                 
                }
        }
          

                if ($nv->budget_type == "CAPEX") {
                    $derc_info = '1';
                } else if ($nv->budget_type == "OPEX") {
                    $derc_info = !empty($request_input['prop_number']) ? '1' : '0';
                }
                Nvsericestatus::where('service_id', $nv_status->service_id)
              ->update(['draft' => $draft,'derc_info' => $derc_info]);
            
            NeedValidation::where('id', $request_input['nv_id'])
            ->update(['proposal_type' => $request_input['proposal_type']]);  

        
            $data = [];

            $fileFields = [
                'cost_calculation_for_service',
                'copy_of_previous_work',
                'copy_of_derc_other',
                'consuption_details',
                'buget_stmt_for_both',
                'material_procurement',
                'photographs_of_product',
                'vendor_quatation',
                'vend_quatation',
                'others',
                'cm_rate_ref',
                'vendor_quat',
                'last_purchase_price',
                'user_estimation',
                'previous_wo_rc',
                'past_practice',
                'special_attch',
                'just_prop_upload',
            ];
            foreach ($fileFields as $fieldName) {
                if ($request->hasFile($fieldName)) {
                    $files = $request->file($fieldName);
            
                    if (is_array($files)) {
                        // Handle multiple files for 'others' field
                        $fileNames = [];
            
                        foreach ($files as $file) {
                            $newFileName = $file->getClientOriginalName();
                            $filePath = public_path('services-doc/' . $newFileName);
            
                            // Check if a file with the same name already exists
                            if (!File::exists($filePath)) {
                                $file->move(public_path('services-doc'), $newFileName);
                            }
            
                            $fileNames[] = $newFileName;
                        }
            
                        $data[$fieldName] = implode(',', $fileNames); // Convert array to string
                    } else {
                        // Handle single file for other fields
                        $file = $files;
                        $newFileName = $file->getClientOriginalName();
                        $filePath = public_path('services-doc/' . $newFileName);
            
                        // Check if a file with the same name already exists
                        if (!File::exists($filePath)) {
                            $file->move(public_path('services-doc'), $newFileName);
                        }
            
                        $data[$fieldName] = $newFileName;
                    }
                }
            }
            
         
       
            ServiceDoc::where('service_id', $service_id)->update($data);
            $response['result'] = 'success';
            $response['msg'] = 'Service Updated';
        } elseif (
            $nv_status->rv1_status == 2 || 
            $nv_status->rv2_status == 2 ||
             $nv_status->rv3_status == 2 || 
             $nv_status->rv4_status == 2 || 
             $nv_status->hod_status == 2 ||
             $nv_status->groupcio_status == 2 ||      
             $anyStageTwo) {
            $nvservice = NVService::create([
                'dept_id' => $request_input['dept_id'],
                'nv_id' => $request_input['nv_id'],
                'draft' =>  $draft,
                'company_id' => $request_input['company_id'],
                'user_id' => \Auth::user()->id,
                'dop_ref_no' => $request_input['dop_ref_no'],
                'proposal_name' => $request_input['proposal_name'],
                'background' => $background,
                'just_of_proposal' => $just_of_proposal,
                'broad_just' => $broad_just,
                'special_remarks' => $request_input['special_remarks'],
                'derc_ref_no' => $request_input['derc_ref_no']?? null,
                'derc_approval' => $request_input['derc_approval']?? null,
                'derc_app_date' => $request_input['derc_app_date']?? null,
                'prop_number' => $request_input['prop_number']?? null,
                'past_3_year_actual_cost_fy' => implode(',', $request_input['past_3_year_actual_cost_fy']),
                'past_3_year_actual_cost' => implode(',', $request_input['past_3_year_actual_cost']),
                'past_3_year_actual_cost_service' => implode(',', $request_input['past_3_year_actual_cost_service']),
                'benefit' => $request_input['benefit'],
                'implements_years' =>$request_input['implements_years'],
                'implementation_period_from' => implode(',', $request->input('imp_from')),
                'implementation_period_to' => implode(',', $request->input('imp_to')),
                'implementation_plan_year_wise' => implode('.,', $request->input('imp_plan')),
              //  'type_of_proposal' => $request_input['type_of_proposal'],
                'mode_of_award_of_service' => $request_input['mode_of_award_of_service'],
                'amc_proposal_sdate' => $request_input['amc_proposal_sdate'],
                'amc_proposal_edate' => $request_input['amc_proposal_edate'],
                'budget_available' => $budget_available,
                'estimate_amount_of_service' => $request_input['estimate_amount_of_service'],
                'estimate_amount_of_service_civil' => $request_input['estimate_amount_of_service_civil'],
                'tax1' => $request_input['tax1'] ?? null,
                'tax2' => $request_input['tax2'] ?? null,
                'tax3' => $request_input['tax3'] ?? null,
                'tax4' => $request_input['tax4'] ?? null,
                'tax5' => $request_input['tax5'] ?? null,
                'estimate_amount_of_rr_chnage' => $request_input['estimate_amount_of_rr_chnage'],
                'estimate_amount_other' => $request_input['estimate_amount_other'],
                'total_buget' => $request_input['total_buget'],
                'add_budget' => $request_input['add_budget'] ?? null,
                'approved_budget' => $request_input['approved_budget'] ?? null,
                'service_amount' => implode(',',$request_input['service_amount']),
                'service_description'=> implode(',',$request_input['service_description']),
                'total_ser_amo' => $request_input['total_ser_amo'],
                'cause_analysis' => $request_input['cause_analysis'],
                'past_practice_text' => $request_input['past_practice_text'],
                'total_mat_mat' => $total_mat_mat ?? null,
                'total_mat_mat2' => $total_mat_mat2 ?? null,
                'total_mat_mat3' => $total_mat_mat3 ?? null,
                'total_matyear1' => $request_input['total_matyear1'] ?? null,
                'total_matyear2' => $request_input['total_matyear2'] ?? null,
                'total_matyear3' => $request_input['total_matyear3'] ?? null,
                'tax_amount1' => $request_input['tax_amount1'] ?? null,
                'tax_amount2' => $request_input['tax_amount2'] ?? null,
                'tax_amount3' => $request_input['tax_amount3'] ?? null,
                // $data['created_by'] = \Auth::user()->id;
            ]);

            $nv = NeedValidation::where('id',$request_input['nv_id'])->first();
        if($nv->budgetary_provision == "Approved")
        {
            $bud_from_log = budget::where('dept_id', $request_input['dept_id'])->where('budget_type',$nv->budget_type)->where('fiscal_year',$nv->fiscal_year)->first('total_budget');
            $tot = $bud_from_log->total_budget == null ? 0 : $bud_from_log->total_budget;
            $budget =budget::where('dept_id', $request_input['dept_id'])->where('budget_type',$nv->budget_type)->where('fiscal_year',$nv->fiscal_year)->update([
                'total_budget' =>  $tot +  ($request_input['total_buget'])-$request_input['add_budget'],
                'service_id' => $nv->service_id,
            ]);
            if(!empty($request_input['implements_years'])){
                $Impyear = explode('-',$nv->fiscal_year);
                $nextStartYear1 = $Impyear[0] + 1;
                $nextEndYear1 = $Impyear[1] + 1;
                $nextStartYear2 = $Impyear[0] + 2;
                $nextEndYear2 = $Impyear[1] + 2;
                $nextYear1 = "{$nextStartYear1}-{$nextEndYear1}";
                $nextYear2 = "{$nextStartYear2}-{$nextEndYear2}";

                // if(!empty($total_mat_mat2)){
                //     $bud_from1 = budget::where('dept_id', $request_input['dept_id'])->where('budget_type',$nv->budget_type)->where('fiscal_year',$nextYear1)->first();
                //     if($bud_from1){
                //         $tot = $bud_from1->total_budget == null ? 0 : $bud_from1->total_budget;
                //         $budget =budget::where('dept_id',$nv->department_id)->where('budget_type',$nv->budget_type)->where('fiscal_year',$nextYear1)->update([
                //             'total_budget' => $tot + ($total_mat_mat2)
                //         ]);
                //     }
                // }

                // if(!empty($total_mat_mat3)){
                //     $bud_from2 = budget::where('dept_id', $request_input['dept_id'])->where('budget_type',$nv->budget_type)->where('fiscal_year',$nextYear2)->first();
                //     if($bud_from2){
                //         $tot = $bud_from2->total_budget == null ? 0 : $bud_from2->total_budget;
                //         $budget =budget::where('dept_id',$nv->department_id)->where('budget_type',$nv->budget_type)->where('fiscal_year',$nextYear2)->update([
                //             'total_budget' => $tot + ($total_mat_mat3)
                //         ]);
                //     }
                // }

                if($nv->budget_type == "CAPEX")
                {
                    $provision_budget2 = CapexBudget::where('department_id', $nv->department_id)->where('fiscal_year', $nextYear1)->first();
                    $provision_budget3 = CapexBudget::where('department_id', $nv->department_id)->where('fiscal_year', $nextYear2)->first();

                if($provision_budget2){
                if(!empty($total_mat_mat2)){
                    if ($nextYear1 == $provision_budget2->fiscal_year )
                    {
                     if($provision_budget2->provision_budget == null){
                            $capex_budget_approve =  CapexBudget::where('department_id', $nv->department_id)->where('fiscal_year',$provision_budget2->fiscal_year)->update([
                                'provision_budget' => $total_mat_mat2
                            ]);
                        }else{
                            $capexbudget =  $provision_budget2->provision_budget;
                            $total = $capexbudget + $total_mat_mat2;
                            $capex_budget_approve =  CapexBudget::where('department_id', $nv->department_id)->where('fiscal_year',$provision_budget2->fiscal_year)->update([
                                'provision_budget' => $total 
                            ]);
                        }
                    
                    }
                }
              }
                if($provision_budget3){
                    if(!empty($total_mat_mat3)){
                        if ($nextYear2 == $provision_budget3->fiscal_year )
                        {
                            if($provision_budget3->provision_budget == null){
                                $capex_budget_approve =  CapexBudget::where('department_id', $nv->department_id)->where('fiscal_year',$provision_budget3->fiscal_year)->update([
                                    'provision_budget' => $total_mat_mat3
                                ]);
                            }else{
                                $capexbudget =  $provision_budget3->provision_budget;
                                $total = $capexbudget + $total_mat_mat3;
                                $capex_budget_approve =  CapexBudget::where('department_id', $nv->department_id)->where('fiscal_year',$provision_budget3->fiscal_year)->update([
                                    'provision_budget' => $total 
                                ]);
                            }
                        
                        }
                    }
                }
                }elseif($nv->budget_type == "OPEX"){
                    $provision_budget2 = OpexBudget::where('department_id', $nv->department_id)->where('fiscal_year', $nextYear1)->first();
                    $provision_budget3 = OpexBudget::where('department_id', $nv->department_id)->where('fiscal_year', $nextYear2)->first();

                if($provision_budget2){
                if(!empty($total_mat_mat2)){
                    if ($nextYear1 == $provision_budget2->fiscal_year )
                    {
                     if($provision_budget2->provision_budget == null){
                            $capex_budget_approve =  OpexBudget::where('department_id', $nv->department_id)->where('fiscal_year',$provision_budget2->fiscal_year)->update([
                                'provision_budget' => $total_mat_mat2
                            ]);
                        }else{
                            $OpexBudget =  $provision_budget2->provision_budget;
                            $total = $OpexBudget + $total_mat_mat2;
                            $capex_budget_approve =  OpexBudget::where('department_id', $nv->department_id)->where('fiscal_year',$provision_budget2->fiscal_year)->update([
                                'provision_budget' => $total 
                            ]);
                        }
                    
                    }
                }
               }
                if($provision_budget3){
                    if(!empty($total_mat_mat3)){
                        if ($nextYear2 == $provision_budget3->fiscal_year )
                        {
                            if($provision_budget3->provision_budget == null){
                                $capex_budget_approve =  OpexBudget::where('department_id', $nv->department_id)->where('fiscal_year',$provision_budget3->fiscal_year)->update([
                                    'provision_budget' => $total_mat_mat3
                                ]);
                            }else{
                                $OpexBudget =  $provision_budget3->provision_budget;
                                $total = $OpexBudget + $total_mat_mat3;
                                $capex_budget_approve =  OpexBudget::where('department_id', $nv->department_id)->where('fiscal_year',$provision_budget3->fiscal_year)->update([
                                    'provision_budget' => $total 
                                ]);
                            }
                        
                        }
                    }
                }
                }
               
                 
                }
        }
            $service_id = $request['service_id'];
            $nvm = NVService::find($service_id);
            $nv_status = Nvsericestatus::where('service_id', $nvm->id)->orderBy('id', 'asc')->first();

            Nvsericestatus::where('service_id', $nv_status->service_id)
            ->update(['previous_draft_status' => '1']);

        $count = Nvsericestatus::where('nv_id', $request_input['nv_id'])->count();
        $version_nv = ($count >= 1) ? $request_input['nv_id'] . '-v' . ($count + 1) : (string) $request_input['nv_id'];
        $nvservicestatus = new Nvsericestatus();
        $nvservicestatus->service_id = $nvservice->id;
        $nvservicestatus->nv_id = $request_input['nv_id'];
        $nvservicestatus->company_id = $request_input['company_id'];
        $nvservicestatus->draft = $draft;
        $nvservicestatus->version_nv = $version_nv;
        
        if ($nv->budget_type == "CAPEX") {
            $nvservicestatus->derc_info = '1';
        } elseif ($nv->budget_type == "OPEX") {
            $nvservicestatus->derc_info = !empty($request_input['prop_number']) ? '1' : '0';
        }
        
        $nvservicestatus->save();

            NeedValidation::where('id', $request_input['nv_id'])
            ->update(['proposal_type' => $request_input['proposal_type']]);  

            if (!empty($nvservice)) {
                $serviceId = $nvservice->id;
                $data = [];

                $data['service_id'] = $serviceId;
                $fileFields = [
                    'cost_calculation_for_service',
                    'copy_of_previous_work',
                    'copy_of_derc_other',
                    'consuption_details',
                    'buget_stmt_for_both',
                    'material_procurement',
                    'photographs_of_product',
                    'vendor_quatation',
                    'vend_quatation',
                    'others',
                    'cm_rate_ref',
                    'vendor_quat',
                    'last_purchase_price',
                    'user_estimation',
                    'previous_wo_rc',
                    'past_practice',
                    'special_attch',
                    'just_prop_upload',
                ];
                foreach ($fileFields as $fieldName) {
                    if ($request->hasFile($fieldName)) {
                        $files = $request->file($fieldName);
                
                        if (is_array($files)) {
                            // Handle multiple files for 'others' field
                            $fileNames = [];
                
                            foreach ($files as $file) {
                                $newFileName = $file->getClientOriginalName();
                                $filePath = public_path('services-doc/' . $newFileName);
                
                                // Check if a file with the same name already exists
                                if (!File::exists($filePath)) {
                                    $file->move(public_path('services-doc'), $newFileName);
                                }
                
                                $fileNames[] = $newFileName;
                            }
                
                            $data[$fieldName] = implode(',', $fileNames); // Convert array to string
                        } else {
                            // Handle single file for other fields
                            $file = $files;
                            $newFileName = $file->getClientOriginalName();
                            $filePath = public_path('services-doc/' . $newFileName);
                
                            // Check if a file with the same name already exists
                            if (!File::exists($filePath)) {
                                $file->move(public_path('services-doc'), $newFileName);
                            }
                
                            $data[$fieldName] = $newFileName;
                        }
                    }
                }
             
      
                $data['created_by'] = \Auth::user()->id;
                $document = ServiceDoc::create($data);
            }
            $response['result'] = 'success';
            $response['msg'] = 'New Service Created';
        }
        //  }
        $response['result'] = 'success';
        $response['msg'] = 'Service Updated';
        
        if (Auth::user()->role_id == 9) {
            $nv_id = $request->nv_id;
            $user_id = Auth::user()->id;
            $employees = Employee::where("user_id", $user_id)->first();
            $departmentIds = explode(',', $employees->department_id);
            $departments = Department::whereIn("id", $departmentIds)->get();

            foreach ($departments as $department) {
                $hod = $department->dep_hod;
                $rv1 = $department->dep_rew1;
                $rv2 = $department->dep_rew2;
                $rv3 = $department->dep_rew3;
                $rv4 = $department->dep_rew4;
            
            }
            if(!empty($rv1)){
                $emp_rv1 = Employee::where('user_id', $department->dep_rew1)->first();
            }elseif(!empty($rv2)){
                $emp_rv1 = Employee::where('user_id', $department->dep_rew2)->first();
            }elseif(!empty($rv3)){
                $emp_rv1 = Employee::where('user_id', $department->dep_rew3)->first();
            }elseif(!empty($rv4)){
                $emp_rv1 = Employee::where('user_id', $department->dep_rew4)->first();
            }else{
                $emp_rv1 = Employee::where('user_id', $department->dep_hod)->first();
            }
            $to_emails = $emp_rv1->email;
            // $depart = Department::where("id",$employees->department_id)->first();
            $initiated_date = NVService::where('user_id', $user_id)->where('nv_id',$nv_id)->first();
            $initiated_by =  Employee::where('user_id', $initiated_date->user_id)->first();
            // $nv_type = NeedValidation::where('user_id', $user_id)->where('id', $nv_id)->first();
            $ini_date = NVService::where('user_id', $user_id)->where('nv_id',$nv_id)->first();
            $ini_by =  Employee::where('user_id', $ini_date->user_id)->first();

            $tble_service = NVService::where('nv_id', $nv_id)->first();
            $user = User::select('email','name','id')->where('id', $tble_service->user_id)->first();
            $nv_type = NeedValidation::where('id', $nv_id)->first();
            $depart = Department::where("id",  $nv_type->department_id)->first();
            // dd($ini_by);
            //   dd($to_emails);
            //   $to_emails="hareram.y@redianglobal.com";
            $p1 = "You have a new request that requires your approval:";
            $p2 = "Please review the request and take appropriate action.";
            $remark = "";
            //  send mail 
            Mail::send('emailtemp.doc_mail', ['username' => 'user','initiated_date' => $initiated_date,'initiated_by' => $initiated_by, 'ini_date' => $ini_date,'ini_by' => $ini_by,'p1' => $p1, 'p2' => $p2, 'remark' => $remark ?? '' , 'department' => $depart,'nv_type'=>$nv_type], function ($message) use ($to_emails) {
                $message->from(env('MAIL_FROM_ADDRESS'), 'NV');
                $message->to($to_emails);

                $message->subject("Need Validation Status Update : Seeking your validation");
            });
            $serviceType = ($nv_type->service_id == 1) ? 'Material' : 'Service';
            $subject = "Your NV (NV/{$nv_type->budget_type}/{$nv_type->fiscal_year}/{$depart->name}/{$serviceType}/{$nv_type->id}) has been submitted";
            $to_emails = $user->email;
            Mail::send('emailtemp.nvsubmit_mail', ['user' => $user, 'initiated_date' => $initiated_date, 'initiated_by' => $initiated_by, 'ini_date' => $ini_date,'ini_by' => $ini_by, 'p1' => $p1, 'p2' => $p2, 'nv_type'=>$nv_type,'remark' => $remark ?? '', 'department' => $depart, 'status' => $status,], function ($message) use ($to_emails,$subject) {
                $message->from(env('MAIL_FROM_ADDRESS'), 'NV');
                $message->to($to_emails);
                // //$message->cc('raushan@rediansoftware.com');
                $message->subject($subject);
            });
        }
    
}
  }elseif($status == "save_nv"){

    if (empty($request['service_id'])) {


        $rules = [
            // 'dop_ref_no' => 'required|numeric|unique:tbl_service,dop_ref_no,except,id',
            // 'short_code' => 'required|string|max:10|unique:divisions,short_code,except,id',
        ];

        $messages = [
            // 'dop_ref_no.unique' => 'Thid DOP ref no has already been taken',
            // 'name.max' => 'Division name should not be more than 50 characters',
            // 'short_code.required' => 'Please enter division short code',
            // 'short_code.max' => 'short code should not be more than 10 characters',

        ];
        $validator = Validator::make($request_input, $rules, $messages);
        if ($validator->fails()) {
            $response['msg'] = $validator->errors()->toArray();
            $response['result'] = 'error';
        } else {
           
            if($request_input['implements_years']  == Null)
            {
                $period_from =  Null;
                $period_to = Null;
                $period_plan = Null;
            }
            else
            {
                $period_from =  implode(',', $request->input('imp_from'));
                $period_to = implode(',', $request->input('imp_to'));
                $period_plan = implode('.,', $request->input('imp_plan'));
            }
        $nvservice = NVService::create([
            'dept_id' => $request_input['dept_id'],
            'draft' =>  $draft,
            'nv_id' => $request_input['nv_id'],
            'company_id' => $request_input['company_id'],
            'user_id' => \Auth::user()->id,
            'dop_ref_no' => $request_input['dop_ref_no'],
            'proposal_name' => $request_input['proposal_name'],
            'background' => $background,
            'just_of_proposal' => $just_of_proposal,
            'broad_just' => $broad_just,
            'special_remarks' => $request_input['special_remarks'],
            'derc_ref_no' => $request_input['derc_ref_no']?? null,
            'derc_approval' => $request_input['derc_approval']?? null,
            'derc_app_date' => $request_input['derc_app_date']?? null,
            'prop_number' => $request_input['prop_number']?? null,
            'past_3_year_actual_cost_fy' => implode(',', $request_input['past_3_year_actual_cost_fy']),
            'past_3_year_actual_cost' => implode(',', $request_input['past_3_year_actual_cost']),
            'past_3_year_actual_cost_service' => implode(',', $request_input['past_3_year_actual_cost_service']),
            'benefit' => $request_input['benefit'],
            'implements_years' =>$request_input['implements_years'],
            // 'implementation_period_from' => implode(',', $request->input('imp_from')),
            'implementation_period_from' => $period_from,
            'implementation_period_to' => $period_to,
            'implementation_plan_year_wise' => $period_plan,
            // 'implementation_period_to' => implode(',', $request->input('imp_to')),
            // 'implementation_plan_year_wise' => implode('.,', $request->input('imp_plan')),
          //  'type_of_proposal' => $request_input['type_of_proposal'],
            'mode_of_award_of_service' => $request_input['mode_of_award_of_service'],
            'amc_proposal_sdate' => $request_input['amc_proposal_sdate'],
            'amc_proposal_edate' => $request_input['amc_proposal_edate'],
            'budget_available' => $budget_available,
            'estimate_amount_of_service' => $request_input['estimate_amount_of_service'],
            'estimate_amount_of_service_civil' => $request_input['estimate_amount_of_service_civil'],
            'tax1' => $request_input['tax1'] ?? null,
            'tax2' => $request_input['tax2'] ?? null,
            'tax3' => $request_input['tax3'] ?? null,
            'tax4' => $request_input['tax4'] ?? null,
            'tax5' => $request_input['tax5'] ?? null,
            'estimate_amount_of_rr_chnage' => $request_input['estimate_amount_of_rr_chnage'],
            'estimate_amount_other' => $request_input['estimate_amount_other'],
            'total_buget' => $request_input['total_buget'],
            'add_budget' => $request_input['add_budget'] ?? null,
            'approved_budget' => $request_input['approved_budget'] ?? null,
            'total_ser_amo' => $request_input['total_ser_amo'],
            'service_amount' => implode(',',$request_input['service_amount']),
            'service_description'=> implode(',',$request_input['service_description']),
            'cause_analysis' => $request_input['cause_analysis'],
            'past_practice_text' => $request_input['past_practice_text'],
            'edit_count'=> 1,
            'total_matyear1' => $request_input['total_matyear1'] ?? null,
            'total_matyear2' => $request_input['total_matyear2'] ?? null,
            'total_matyear3' => $request_input['total_matyear3'] ?? null,
            'tax_amount1' => $request_input['tax_amount1'] ?? null,
            'tax_amount2' => $request_input['tax_amount2'] ?? null,
            'tax_amount3' => $request_input['tax_amount3'] ?? null,
            // $data['created_by'] = \Auth::user()->id;
        ]);
      
        
        $count = Nvsericestatus::where('nv_id', $request_input['nv_id'])->count();
        $version_nv = ($count >= 1) ? $request_input['nv_id'] . '-v' . ($count + 1) : (string) $request_input['nv_id'];
        $nvservicestatus = new Nvsericestatus();
        $nvservicestatus->service_id = $nvservice->id;
        $nvservicestatus->nv_id = $request_input['nv_id'];
        $nvservicestatus->company_id = $request_input['company_id'];
        $nvservicestatus->draft = $draft;
        $nvservicestatus->version_nv = $version_nv;
        
        if ($nv->budget_type == "CAPEX") {
            $nvservicestatus->derc_info = '1';
        } elseif ($nv->budget_type == "OPEX") {
            $nvservicestatus->derc_info = !empty($request_input['prop_number']) ? '1' : '0';
        }
        
        $nvservicestatus->save();

        NeedValidation::where('id', $request_input['nv_id'])
        ->update(['proposal_type' => $request_input['proposal_type']]);

        if (!empty($nvservice)) {
            $serviceId = $nvservice->id;
            $data = [];
          
            $data['service_id'] = $serviceId;
            $fileFields = [
                'cost_calculation_for_service',
                'copy_of_previous_work',
                'copy_of_derc_other',
                'consuption_details',
                'buget_stmt_for_both',
                'material_procurement',
                'photographs_of_product',
                'vendor_quatation',
                'vend_quatation',
                'others',
                'cm_rate_ref',
                'vendor_quat',
                'last_purchase_price',
                'user_estimation',
                'previous_wo_rc',
                'past_practice',
                'special_attch',
                'just_prop_upload',
            ];
            foreach ($fileFields as $fieldName) {
                if ($request->hasFile($fieldName)) {
                    $files = $request->file($fieldName);
            
                    if (is_array($files)) {
                        // Handle multiple files for 'others' field
                        $fileNames = [];
            
                        foreach ($files as $file) {
                            $newFileName = $file->getClientOriginalName();
                            $filePath = public_path('services-doc/' . $newFileName);
            
                            // Check if a file with the same name already exists
                            if (!File::exists($filePath)) {
                                $file->move(public_path('services-doc'), $newFileName);
                            }
            
                            $fileNames[] = $newFileName;
                        }
            
                        $data[$fieldName] = implode(',', $fileNames); // Convert array to string
                    } else {
                        // Handle single file for other fields
                        $file = $files;
                        $newFileName = $file->getClientOriginalName();
                        $filePath = public_path('services-doc/' . $newFileName);
            
                        // Check if a file with the same name already exists
                        if (!File::exists($filePath)) {
                            $file->move(public_path('services-doc'), $newFileName);
                        }
            
                        $data[$fieldName] = $newFileName;
                    }
                }
            }
            
           
    
            $data['created_by'] = \Auth::user()->id;
            $document = ServiceDoc::create($data);
        }
        $response['result'] = 'success';
        $response['msg'] = 'NV Service Created';
     
        }
    } else {

        $service_id = $request['service_id'];
        $nvm = NVService::find($service_id);
        $nv_status = Nvsericestatus::where('service_id', $nvm->id)->first();
        $nv_stage = DB::table('capex_workflows_status')->where('service_id', $nvm->id)->get();

        $allStagesZero = true;
        $anyStageTwo = false;
        
        if ($nv_stage->count()) {
            foreach ($nv_stage as $stage) {
                if ($stage->nv_stage_status != 0) {
                    $allStagesZero = false;
                }
                if ($stage->nv_stage_status == 2) {
                    $anyStageTwo = true;
                }
            }
        }

        if ($nv_status->rv1_status == 0 && 
            $nv_status->rv2_status == 0 && 
            $nv_status->rv3_status == 0 && 
            $nv_status->rv4_status == 0 && 
            $nv_status->hod_status == 0 && 
            $nv_status->groupcio_status == 0 &&
            $allStagesZero) {
                    if($request_input['implements_years']  == Null)
                    {
                        $period_from =  Null;
                        $period_to = Null;
                        $period_plan = Null;
                    }
                    else
                    {
                        $period_from =  implode(',', $request->input('imp_from'));
                        $period_to = implode(',', $request->input('imp_to'));
                        $period_plan = implode('.,', $request->input('imp_plan'));
                    }
            NVService::find($service_id)->update([
                'dept_id' => $request_input['dept_id'],
                'nv_id' => $request_input['nv_id'],
                'draft' =>  $draft,
                'company_id' => $request_input['company_id'],
                'user_id' => \Auth::user()->id,
                'dop_ref_no' => $request_input['dop_ref_no'],
                'proposal_name' => $request_input['proposal_name'],
                'background' => $background,
                'just_of_proposal' => $just_of_proposal,
                'broad_just' => $broad_just,
                'special_remarks' => $request_input['special_remarks'],
                'derc_ref_no' => $request_input['derc_ref_no']?? null,
                'derc_approval' => $request_input['derc_approval']?? null,
                'derc_app_date' => $request_input['derc_app_date']?? null,
                'prop_number' => $request_input['prop_number']?? null,
                'past_3_year_actual_cost_fy' => implode(',', $request_input['past_3_year_actual_cost_fy']),
                'past_3_year_actual_cost' => implode(',', $request_input['past_3_year_actual_cost']),
                'past_3_year_actual_cost_service' => implode(',', $request_input['past_3_year_actual_cost_service']),
                'benefit' => $request_input['benefit'],
                'implements_years' =>$request_input['implements_years'],
                'implementation_period_from' => $period_from,
                'implementation_period_to' => $period_to,
                'implementation_plan_year_wise' => $period_plan,
                // 'implementation_period_from' => implode(',', $request->input('imp_from')),
                // 'implementation_period_to' => implode(',', $request->input('imp_to')),
                // 'implementation_plan_year_wise' => implode('.,', $request->input('imp_plan')),
               // 'type_of_proposal' => $request_input['type_of_proposal'],
                'mode_of_award_of_service' => $request_input['mode_of_award_of_service'],
                'amc_proposal_sdate' => $request_input['amc_proposal_sdate'],
                'amc_proposal_edate' => $request_input['amc_proposal_edate'],
                'budget_available' => $budget_available,
                'estimate_amount_of_service' => $request_input['estimate_amount_of_service'],
                'estimate_amount_of_service_civil' => $request_input['estimate_amount_of_service_civil'],
                'tax1' => $request_input['tax1'] ?? null,
                'tax2' => $request_input['tax2'] ?? null,
                'tax3' => $request_input['tax3'] ?? null,
                'tax4' => $request_input['tax4'] ?? null,
                'tax5' => $request_input['tax5'] ?? null,
                'estimate_amount_of_rr_chnage' => $request_input['estimate_amount_of_rr_chnage'],
                'estimate_amount_other' => $request_input['estimate_amount_other'],
                'total_buget' => $request_input['total_buget'],
                'add_budget' => $request_input['add_budget'] ?? null,
                'approved_budget' => $request_input['approved_budget'] ?? null,
                'total_ser_amo' => $request_input['total_ser_amo'],
                'service_amount' => implode(',',$request_input['service_amount']),
                'service_description'=> implode(',',$request_input['service_description']),
                'cause_analysis' => $request_input['cause_analysis'],
                'past_practice_text' => $request_input['past_practice_text'],
                'total_matyear1' => $request_input['total_matyear1'] ?? null,
                'total_matyear2' => $request_input['total_matyear2'] ?? null,
                'total_matyear3' => $request_input['total_matyear3'] ?? null,
                'tax_amount1' => $request_input['tax_amount1'] ?? null,
                'tax_amount2' => $request_input['tax_amount2'] ?? null,
                'tax_amount3' => $request_input['tax_amount3'] ?? null,
                // $data['created_by'] = \Auth::user()->id;
            ]);
          

            if ($nv->budget_type == "CAPEX") {
                $derc_info = '1';
            } else if ($nv->budget_type == "OPEX") {
                $derc_info = !empty($request_input['prop_number']) ? '1' : '0';
            }
            Nvsericestatus::where('service_id', $nv_status->service_id)
          ->update(['draft' => $draft,'derc_info' => $derc_info]);

            NeedValidation::where('id', $request_input['nv_id'])
        ->update(['proposal_type' => $request_input['proposal_type']]);        
          
            $data = [];

            $fileFields = [
                'cost_calculation_for_service',
                'copy_of_previous_work',
                'copy_of_derc_other',
                'consuption_details',
                'buget_stmt_for_both',
                'material_procurement',
                'photographs_of_product',
                'vendor_quatation',
                'vend_quatation',
                'others',
                'cm_rate_ref',
                'vendor_quat',
                'last_purchase_price',
                'user_estimation',
                'previous_wo_rc',
                'past_practice',
                'special_attch',
                'just_prop_upload',
            ];
            foreach ($fileFields as $fieldName) {
                if ($request->hasFile($fieldName)) {
                    $files = $request->file($fieldName);
            
                    if (is_array($files)) {
                        // Handle multiple files for 'others' field
                        $fileNames = [];
            
                        foreach ($files as $file) {
                            $newFileName = $file->getClientOriginalName();
                            $filePath = public_path('services-doc/' . $newFileName);
            
                            // Check if a file with the same name already exists
                            if (!File::exists($filePath)) {
                                $file->move(public_path('services-doc'), $newFileName);
                            }
            
                            $fileNames[] = $newFileName;
                        }
            
                        $data[$fieldName] = implode(',', $fileNames); // Convert array to string
                    } else {
                        // Handle single file for other fields
                        $file = $files;
                        $newFileName = $file->getClientOriginalName();
                        $filePath = public_path('services-doc/' . $newFileName);
            
                        // Check if a file with the same name already exists
                        if (!File::exists($filePath)) {
                            $file->move(public_path('services-doc'), $newFileName);
                        }
            
                        $data[$fieldName] = $newFileName;
                    }
                }
            }
            
          
    
            ServiceDoc::where('service_id', $service_id)->update($data);
            $response['result'] = 'success';
            $response['msg'] = 'Service Updated';
        } elseif (
            $nv_status->rv1_status == 2 || 
            $nv_status->rv2_status == 2 ||
             $nv_status->rv3_status == 2 || 
             $nv_status->rv4_status == 2 || 
             $nv_status->hod_status == 2 ||
            $nv_status->groupcio_status == 2 ||       
            $anyStageTwo) {
                if($request_input['implements_years']  == Null)
                {
                    $period_from =  Null;
                    $period_to = Null;
                    $period_plan = Null;
                }
                else
                {
                    $period_from =  implode(',', $request->input('imp_from'));
                    $period_to = implode(',', $request->input('imp_to'));
                    $period_plan = implode('.,', $request->input('imp_plan'));
                }
            $nvservice = NVService::create([
                'dept_id' => $request_input['dept_id'],
                'nv_id' => $request_input['nv_id'],
                'company_id' => $request_input['company_id'],
                'draft' =>  $draft,
                'user_id' => \Auth::user()->id,
                'dop_ref_no' => $request_input['dop_ref_no'],
                'proposal_name' => $request_input['proposal_name'],
                'background' => $background,
                'just_of_proposal' => $just_of_proposal,
                'broad_just' => $broad_just,
                'special_remarks' => $request_input['special_remarks'],
                'derc_ref_no' => $request_input['derc_ref_no']?? null,
                'derc_approval' => $request_input['derc_approval']?? null,
                'derc_app_date' => $request_input['derc_app_date']?? null,
                'prop_number' => $request_input['prop_number']?? null,
                'past_3_year_actual_cost_fy' => implode(',', $request_input['past_3_year_actual_cost_fy']),
                'past_3_year_actual_cost' => implode(',', $request_input['past_3_year_actual_cost']),
                'past_3_year_actual_cost_service' => implode(',', $request_input['past_3_year_actual_cost_service']),
                'benefit' => $request_input['benefit'],
                'implements_years' => 0,
                'implementation_period_from' =>null,
                'implementation_period_to' =>null,
                'implementation_plan_year_wise' => null,
                // 'implementation_period_from' => implode(',', $request->input('imp_from')),
                // 'implementation_period_to' => implode(',', $request->input('imp_to')),
                // 'implementation_plan_year_wise' => implode('.,', $request->input('imp_plan')),
              //  'type_of_proposal' => $request_input['type_of_proposal'],
                'mode_of_award_of_service' => $request_input['mode_of_award_of_service'],
                'amc_proposal_sdate' => $request_input['amc_proposal_sdate'],
                'amc_proposal_edate' => $request_input['amc_proposal_edate'],
                'budget_available' => $budget_available,
                'estimate_amount_of_service' => $request_input['estimate_amount_of_service'],
                'estimate_amount_of_service_civil' => $request_input['estimate_amount_of_service_civil'],
                'tax1' => $request_input['tax1'] ?? null,
                'tax2' => $request_input['tax2'] ?? null,
                'tax3' => $request_input['tax3'] ?? null,
                'tax4' => $request_input['tax4'] ?? null,
                'tax5' => $request_input['tax5'] ?? null,
                'estimate_amount_of_rr_chnage' => $request_input['estimate_amount_of_rr_chnage'],
                'estimate_amount_other' => $request_input['estimate_amount_other'],
                'total_buget' => $request_input['total_buget'],
                'add_budget' => $request_input['add_budget'] ?? null,
                'approved_budget' => $request_input['approved_budget'] ?? null,
                'total_ser_amo' => $request_input['total_ser_amo'],
                'service_amount' => implode(',',$request_input['service_amount']),
                'service_description'=> implode(',',$request_input['service_description']),
                'cause_analysis' => $request_input['cause_analysis'],
                'past_practice_text' => $request_input['past_practice_text'],
                'total_matyear1' => $request_input['total_matyear1'] ?? null,
                'total_matyear2' => $request_input['total_matyear2'] ?? null,
                'total_matyear3' => $request_input['total_matyear3'] ?? null,
                'tax_amount1' => $request_input['tax_amount1'] ?? null,
                'tax_amount2' => $request_input['tax_amount2'] ?? null,
                'tax_amount3' => $request_input['tax_amount3'] ?? null,
                // $data['created_by'] = \Auth::user()->id;
            ]);
          
            $service_id = $request['service_id'];
            $nvm = NVService::find($service_id);
            $nv_status = Nvsericestatus::where('service_id', $nvm->id)->orderBy('id', 'asc')->first();

            Nvsericestatus::where('service_id', $nv_status->service_id)
            ->update(['previous_draft_status' => '1']);

        $count = Nvsericestatus::where('nv_id', $request_input['nv_id'])->count();
        $version_nv = ($count >= 1) ? $request_input['nv_id'] . '-v' . ($count + 1) : (string) $request_input['nv_id'];
        $nvservicestatus = new Nvsericestatus();
        $nvservicestatus->service_id = $nvservice->id;
        $nvservicestatus->nv_id = $request_input['nv_id'];
        $nvservicestatus->company_id = $request_input['company_id'];
        $nvservicestatus->draft = $draft;
        $nvservicestatus->version_nv = $version_nv;
        
        if ($nv->budget_type == "CAPEX") {
            $nvservicestatus->derc_info = '1';
        } elseif ($nv->budget_type == "OPEX") {
            $nvservicestatus->derc_info = !empty($request_input['prop_number']) ? '1' : '0';
        }
        
        $nvservicestatus->save();

         NeedValidation::where('id', $request_input['nv_id'])
        ->update(['proposal_type' => $request_input['proposal_type']]);    

            if (!empty($nvservice)) {
                $serviceId = $nvservice->id;
                $data = [];

                $data['service_id'] = $serviceId;
                $fileFields = [
                    'cost_calculation_for_service',
                    'copy_of_previous_work',
                    'copy_of_derc_other',
                    'consuption_details',
                    'buget_stmt_for_both',
                    'material_procurement',
                    'photographs_of_product',
                    'vendor_quatation',
                    'vend_quatation',
                    'others',
                    'cm_rate_ref',
                    'vendor_quat',
                    'last_purchase_price',
                    'user_estimation',
                    'previous_wo_rc',
                    'past_practice',
                    'special_attch',
                    'just_prop_upload',
                ];
                
                foreach ($fileFields as $fieldName) {
                    if ($request->hasFile($fieldName)) {
                        $files = $request->file($fieldName);
                
                        if (is_array($files)) {
                            // Handle multiple files for 'others' field
                            $fileNames = [];
                
                            foreach ($files as $file) {
                                $newFileName = $file->getClientOriginalName();
                                $filePath = public_path('services-doc/' . $newFileName);
                
                                // Check if a file with the same name already exists
                                if (!File::exists($filePath)) {
                                    $file->move(public_path('services-doc'), $newFileName);
                                }
                
                                $fileNames[] = $newFileName;
                            }
                
                            $data[$fieldName] = implode(',', $fileNames); // Convert array to string
                        } else {
                            // Handle single file for other fields
                            $file = $files;
                            $newFileName = $file->getClientOriginalName();
                            $filePath = public_path('services-doc/' . $newFileName);
                
                            // Check if a file with the same name already exists
                            if (!File::exists($filePath)) {
                                $file->move(public_path('services-doc'), $newFileName);
                            }
                
                            $data[$fieldName] = $newFileName;
                        }
                    }
                }
             
                $data['created_by'] = \Auth::user()->id;

                $document = ServiceDoc::create($data);
            }
            $response['result'] = 'success';
            $response['msg'] = 'New Service Created';
        }
        //  }
        $response['result'] = 'success';
        $response['msg'] = 'Service Updated';
    
}
  }


              
        } catch (\Exception $e) {
            app(\App\Exceptions\Handler::class)->report($e);
            $response['result'] = 'failure';
            $response['msg'] = $e->getMessage();
        }

        return response()->json($response);
    }

    public function uploadFile($request, $field, $serviceId)
    {
        $fileName = null;
        if ($request->hasFile($field)) {
            $file = $request->file($field);


            $extension = $file->getClientOriginalName();
            $fileName = time() . '.' . $extension;;
            // $fileName = time() . '_' . str_replace(' ', '_', $fileName);
            // $photo_path = public_path('/uploads/service/'.$serviceId);

            // if (!\File::exists($photo_path)) {
            //     \File::makeDirectory($photo_path, 0775, true);
            // }

            $file->move('services-doc/', $fileName);
        }
        return $fileName;
    }
    public function sendEmail3(Request $request)
    {
        $user_id =\Auth()->user()->id;
        $nv_id = $request->nv_id;
        $user_name = $request->user_name;
        $receiver_user_id = $request->user_id;
        $service_id = $request->service_id;
        $selectedEmails = $request->emails;
      
        $clarificationRemark = $request->remark;
        $clarificationId = $request->clarificationId;
        $is_replied = ($request->is_replied)?$request->is_replied:0;

        $file = $request->file('file') ?? null;
        if(!empty($file)){
            $filePath = $file->getClientOriginalName();
            $filePaths = public_path('clarification-file/' . $filePath);
    
            if (!File::exists($filePaths)) {
                $file->move(public_path('clarification-file/'), $filePath);
            }
        }
        // if(!is_array($selectedEmails)){
        //     $selectedEmails = explode(" ",$selectedEmails);
        // }
        $employees = Employee::select('email','department_id','name')->where("name", $selectedEmails)->first();
       
    
        $departmentIds = explode(',', $employees->department_id);
        // dd($departmentIds);
        $departments = Department::whereIn("id", $departmentIds)->get();
       
        $service_detail = NVService::select('*')->where('nv_id', $nv_id)->orderBy('id', 'desc')->first();
        
        $nv_type = NeedValidation::where('id', $nv_id)->first();
        
        $employee = Employee::where('user_id', $user_id)->first();
        $depart = Department::where("id", $nv_type->department_id)->first();
        $ini_by =  Employee::where('user_id', $user_id)->first();
        
        // $selectedEmails = $request->emails;
 
        // Fetch user names based on email addresses
        $userNames = Employee::where('name', $selectedEmails)->pluck('name', 'email');
    // dd($userNames->email);
        // Check if $nv_type and $depart are not null
        $employee_id = Employee::pluck('user_id');


        $ccuser = DB::table('tbl_service')
        ->join('nvservicestatus', 'tbl_service.nv_id', '=', 'nvservicestatus.nv_id')
        ->where('tbl_service.nv_id', '=', $nv_id)
        ->where(function($query) {
            $query->orWhere('nvservicestatus.rv1_status', [1])
                ->orWhere('nvservicestatus.rv2_status', [1])
                ->orWhere('nvservicestatus.rv3_status', [1])
                ->orWhere('nvservicestatus.rv4_status', [1])
                ->orWhere('nvservicestatus.hod_status', [1])
                ->orWhere('nvservicestatus.ces_rew1_status', [1])
                ->orWhere('nvservicestatus.ces_rew2_status', [1])
                ->orWhere('nvservicestatus.ces_rew3_status', [1])
                ->orWhere('nvservicestatus.ces_rew4_status', [1])
                ->orWhere('nvservicestatus.ces_status', [1])
                ->orWhere('nvservicestatus.approver_status', [1])
                ->orWhere('nvservicestatus.work_rew1_status', [1])
                ->orWhere('nvservicestatus.work_rew2_status', [1])
                ->orWhere('nvservicestatus.work_rew3_status', [1])
                ->orWhere('nvservicestatus.work_rew4_status', [1])
                ->orWhere('nvservicestatus.approverdep2_status', [1])
                ->orWhere('nvservicestatus.work_rew1dep2_status', [1])
                ->orWhere('nvservicestatus.work_rew2dep2_status', [1])
                ->orWhere('nvservicestatus.work_rew3dep2_status', [1])
                ->orWhere('nvservicestatus.work_rew4dep2_status', [1])
                ->orWhere('nvservicestatus.approverdep3_id', [1])
                ->orWhere('nvservicestatus.work_rew1dep3_status', [1])
                ->orWhere('nvservicestatus.work_rew2dep3_status', [1])
                ->orWhere('nvservicestatus.work_rew3dep3_status', [1])
                ->orWhere('nvservicestatus.work_rew4dep3_status', [1])
                ->orWhere('nvservicestatus.approverdep4_status', [1])
                ->orWhere('nvservicestatus.work_rew1dep4_status', [1])
                ->orWhere('nvservicestatus.work_rew2dep4_status', [1])
                ->orWhere('nvservicestatus.work_rew3dep4_status', [1])
                ->orWhere('nvservicestatus.work_rew4dep4_status', [1])
                ->orWhere('nvservicestatus.groupcio_status', [1])
                ->orWhere('nvservicestatus.ceo_status', [1]);
        })
        ->select('nvservicestatus.rv1_id', 'nvservicestatus.rv2_id', 'nvservicestatus.rv3_id',
                 'nvservicestatus.rv3_id', 'nvservicestatus.rv4_id', 'nvservicestatus.hod_id'
                 , 'nvservicestatus.ces_rew1_id', 'nvservicestatus.ces_rew2_id', 'nvservicestatus.ces_rew3_id'
                 , 'nvservicestatus.ces_rew4_id', 'nvservicestatus.ces_id'   , 'nvservicestatus.work_rew1_id', 'nvservicestatus.work_rew2_id', 'nvservicestatus.work_rew3_id'
                 , 'nvservicestatus.work_rew4_id', 'nvservicestatus.approver_id'  , 'nvservicestatus.work_rew1dep2_id', 'nvservicestatus.work_rew2dep2_id', 'nvservicestatus.work_rew3dep2_id'
                 , 'nvservicestatus.work_rew4dep2_id', 'nvservicestatus.approverdep2_id'   , 'nvservicestatus.work_rew1dep3_id', 'nvservicestatus.work_rew2dep3_id', 'nvservicestatus.work_rew3dep3_id'
                 , 'nvservicestatus.work_rew4dep3_id', 'nvservicestatus.approverdep3_id', 'nvservicestatus.work_rew1dep4_id', 
                  'nvservicestatus.work_rew2dep4_id', 'nvservicestatus.work_rew3dep4_id','nvservicestatus.work_rew4dep4_id', 'nvservicestatus.approverdep4_id'   )
                ->get();

        $employee_emails = Employee::whereIn('user_id', $employee_id)
        ->pluck('email','user_id');

        $id_email_mapping = [];

        foreach ($ccuser as $item) {
            $id_email_mapping[] = isset($employee_emails[$item->rv1_id]) ? $employee_emails[$item->rv1_id] : null;
            $id_email_mapping[] = isset($employee_emails[$item->rv2_id]) ? $employee_emails[$item->rv2_id] : null;
            $id_email_mapping[] = isset($employee_emails[$item->rv3_id]) ? $employee_emails[$item->rv3_id] : null;
            $id_email_mapping[] = isset($employee_emails[$item->rv4_id]) ? $employee_emails[$item->rv4_id] : null;
            $id_email_mapping[] = isset($employee_emails[$item->hod_id]) ? $employee_emails[$item->hod_id] : null;
            $id_email_mapping[] = isset($employee_emails[$item->ces_rew1_id]) ? $employee_emails[$item->ces_rew1_id] : null;
            $id_email_mapping[] = isset($employee_emails[$item->ces_rew2_id]) ? $employee_emails[$item->ces_rew2_id] : null;
            $id_email_mapping[] = isset($employee_emails[$item->ces_rew3_id]) ? $employee_emails[$item->ces_rew3_id] : null;
            $id_email_mapping[] = isset($employee_emails[$item->ces_rew4_id]) ? $employee_emails[$item->ces_rew4_id] : null;
            $id_email_mapping[] = isset($employee_emails[$item->ces_id]) ? $employee_emails[$item->ces_id] : null;
    
            $id_email_mapping[] = isset($employee_emails[$item->work_rew1_id]) ? $employee_emails[$item->work_rew1_id] : null;
            $id_email_mapping[] = isset($employee_emails[$item->work_rew2_id]) ? $employee_emails[$item->work_rew2_id] : null;
            $id_email_mapping[] = isset($employee_emails[$item->work_rew3_id]) ? $employee_emails[$item->work_rew3_id] : null;
            $id_email_mapping[] = isset($employee_emails[$item->work_rew4_id]) ? $employee_emails[$item->work_rew4_id] : null;
            $id_email_mapping[] = isset($employee_emails[$item->approver_id]) ? $employee_emails[$item->approver_id] : null;
    
            $id_email_mapping[] = isset($employee_emails[$item->work_rew1dep2_id]) ? $employee_emails[$item->work_rew1dep2_id] : null;
            $id_email_mapping[] = isset($employee_emails[$item->work_rew2dep2_id]) ? $employee_emails[$item->work_rew2dep2_id] : null;
            $id_email_mapping[] = isset($employee_emails[$item->work_rew3dep2_id]) ? $employee_emails[$item->work_rew3dep2_id] : null;
            $id_email_mapping[] = isset($employee_emails[$item->work_rew4dep2_id]) ? $employee_emails[$item->work_rew4dep2_id] : null;
            $id_email_mapping[] = isset($employee_emails[$item->approverdep2_id]) ? $employee_emails[$item->approverdep2_id] : null;
           
            $id_email_mapping[] = isset($employee_emails[$item->work_rew1dep3_id]) ? $employee_emails[$item->work_rew1dep3_id] : null;
            $id_email_mapping[] = isset($employee_emails[$item->work_rew2dep3_id]) ? $employee_emails[$item->work_rew2dep3_id] : null;
            $id_email_mapping[] = isset($employee_emails[$item->work_rew3dep3_id]) ? $employee_emails[$item->work_rew3dep3_id] : null;
            $id_email_mapping[] = isset($employee_emails[$item->work_rew4dep3_id]) ? $employee_emails[$item->work_rew4dep3_id] : null;
            $id_email_mapping[] = isset($employee_emails[$item->approverdep3_id]) ? $employee_emails[$item->approverdep3_id] : null;
    
            $id_email_mapping[] = isset($employee_emails[$item->work_rew1dep4_id]) ? $employee_emails[$item->work_rew1dep4_id] : null;
            $id_email_mapping[] = isset($employee_emails[$item->work_rew2dep4_id]) ? $employee_emails[$item->work_rew2dep4_id] : null;
            $id_email_mapping[] = isset($employee_emails[$item->work_rew3dep4_id]) ? $employee_emails[$item->work_rew3dep4_id] : null;
            $id_email_mapping[] = isset($employee_emails[$item->work_rew4dep4_id]) ? $employee_emails[$item->work_rew4dep4_id] : null;
            $id_email_mapping[] = isset($employee_emails[$item->approverdep4_id]) ? $employee_emails[$item->approverdep4_id] : null;
        }
        
     
        $id_email_mapping = array_values(array_filter(array_unique($id_email_mapping)));
        $emails= $employees->email;
        $name= $employees->name;
       
            $data = [
                'remark' => $clarificationRemark,
                'selectedEmails' => $selectedEmails,
                'userNames' => $userNames,
                'nv_type' => $nv_type,
                'department' => $depart,
                'ini_by' =>  $ini_by,
                'name' => $name,
                'emails' =>  $emails,
                
            ];
    
            try {
                
            
                    if($is_replied==1){
                        $subject = "Reply for {$ini_by->name } on NV no: NV/{$nv_type->budget_type}/{$nv_type->fiscal_year}/{$depart->name}" . ($nv_type->service_id == 1 ? "/Material/{$nv_type->id}" : ($nv_type->service_id == 2 ? "/Service/{$nv_type->id}" : ''));
                    }else{
                        $subject = "Clarification required from {$ini_by->name } on NV no: NV/{$nv_type->budget_type}/{$nv_type->fiscal_year}/{$depart->name}" . ($nv_type->service_id == 1 ? "/Material/{$nv_type->id}" : ($nv_type->service_id == 2 ? "/Service/{$nv_type->id}" : ''));
                    }
                    Mail::send('emailtemp.doc_mail1', $data, function ($message) use ($emails,$name,$id_email_mapping, $nv_type, $depart, $ini_by, $subject,$file) {
                        $message->from(env('MAIL_FROM_ADDRESS'), 'NV');
                        $message->to($emails);
                        foreach ($id_email_mapping as $ccEmail) {
                            $message->cc($ccEmail);
                        }
                        $message->subject($subject);
                        if (!empty($file)) {
                            // Attach the file to the email
                            $message->attach(public_path('clarification-file/' . $file->getClientOriginalName()));
                        }
                    });
            
                if($is_replied==1){
                    //dd(111);
                    $update = Clarification::find($clarificationId)->update(['is_replied'=>1,'clarification_remark_reply' => $clarificationRemark,'reply_timestamp'=>date('Y-m-d H:i:s'), 'reply_attachment' =>  $filePath ?? null,]);
                }else{
                    $clarification= Clarification::create([
                    'service_id' => $service_id,
                    'nv_id' => $nv_id,
                    'user_id' => $user_id ,
                    'receiver_user_id'=>$receiver_user_id,
                    'user_name' =>  $user_name,
                    'clarification_remark' => $clarificationRemark,
                    ]);
                }
           

            $response = [
                'success' => true
            ];
        } catch (\Exception $e) {
            $response = [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }

        return response()->json($response);
    }
    public function sendEmail4(Request $request)
    {
        // dd($request->all());
        $to = $request->to;
        $cc = $request->cc;
        $subject = $request->subject;
        $remark = $request->remark;
       
        // Logic to send the email
        $data = [
            'remark' => $remark
        ];
        
        // Example using the Mail facade
        Mail::send('emailtemp.doc_mail1', $data, function($message) use ($to, $cc, $subject, $remark) {
            $message->to($to)
                ->cc($cc)
                ->subject($subject);
               
        });
        
        // Return a response indicating the email was sent successfully
        return response()->json(['success' => true]);
    }
    
    public function preview(Request $request, $nv_id)
    {
        $user = \Auth()->user();
        $nv_id = $request->nv_id;
        $segment_id =request()->segment(5);
        if(!empty($segment_id)){
            $service_details = NVService::where('id', $segment_id)->orderBy('id', 'desc')->first();
            $service_doc = ServiceDoc::where('service_id', $segment_id)->orderBy('id', 'desc')->first();
        }else{
            $service_details = NVService::where('nv_id', $nv_id)->orderBy('id', 'desc')->first();
            $service_doc = ServiceDoc::where('service_id', $service_details->id)->orderBy('id', 'desc')->first();  
        }

        $files = [];
        if(!empty($service_doc->cost_calculation_for_service)){
            array_push($files, $service_doc["cost_calculation_for_service"]);
        }if(!empty($service_doc->vend_quatation)){
            array_push($files, $service_doc["vend_quatation"]);
        }if(!empty($service_doc->copy_of_previous_work)){
            array_push($files, $service_doc["copy_of_previous_work"]);
        }if(!empty($service_doc->copy_of_derc_other)){
            array_push($files, $service_doc["copy_of_derc_other"]);
        }if(!empty($service_doc->consuption_details)){
            array_push($files, $service_doc["consuption_details"]);
        }if(!empty($service_doc->buget_stmt_for_both)){
            array_push($files, $service_doc["buget_stmt_for_both"]);
        }if(!empty($service_doc->photographs_of_product)){
            array_push($files, $service_doc["photographs_of_product"]);
        }if(!empty($service_doc->material_procurement)){
            array_push($files, $service_doc["material_procurement"]);
        }if(!empty($service_doc->vendor_quatation)){
            array_push($files, $service_doc["vendor_quatation"]);
        }if(!empty($service_doc->cm_rate_ref)){
            array_push($files, $service_doc["cm_rate_ref"]);
        }if(!empty($service_doc->vendor_quat)){
            array_push($files, $service_doc["vendor_quat"]);
        }if(!empty($service_doc->last_purchase_price)){
            array_push($files, $service_doc["last_purchase_price"]);
        }if(!empty($service_doc->user_estimation)){
            array_push($files, $service_doc["user_estimation"]);
        }if(!empty($service_doc->special_attch)){
            array_push($files, $service_doc["special_attch"]);
        }
        if(!empty($service_doc->just_prop_upload)){
            array_push($files, $service_doc["just_prop_upload"]);
        }
        if(!empty($service_doc->past_practice)){
            array_push($files, $service_doc["past_practice"]);
        }if(!empty($service_doc->previous_wo_rc)){
            array_push($files, $service_doc["previous_wo_rc"]);
        }
        if(!empty($service_doc->others)){
            $others = explode(',',$service_doc->others);
            $count_others = count($others);
        }else{
            $count_others = 0 ;
        }
       $countFiles = (count($files)) + $count_others;

        $nv_year = NeedValidation::select('id', 'fiscal_year','proposal_type')->where('id', $nv_id)->first();
       
        // $employees = Employee::where('department_id', $service_details->dept_id)->where('user_id','!=', $user->id)->where('role_id', 9)->get();
       
        $service_nv = NeedValidation::where("id", $nv_id)
            ->select('user_id','id')->first();
        $ser = NVService::where('nv_id', $service_nv->id)->where('user_id', $service_nv->user_id)->pluck('user_id');
      
        if(!empty($service_nv)){
            // dd( $ser);
        $employees = Employee::where('department_id', $service_details->dept_id)
        ->where('user_id','!=', $user->id)
        ->whereIn('user_id', $ser)
        ->get();
        // dd($employees);
          }
        
        $ap_rj_status = Nvsericestatus::where('service_id', $service_details->id)->first();
        // Process form data here
        $chats = Chat::where('service_id',$service_details->id)->where('user_login_id',$user->id)->get();
        // $clarifications = Clarification::where('service_id',$service_details->id)->where('user_id',$user->id)->get();
        $clarifications = Clarification::where('service_id',$service_details->id)->where('nv_id',$nv_id)->get();
     
        $nv = NeedValidation::where('id',$nv_id)->orderBy('id', 'desc')->first();
        //$clarifications = Clarification::where('material_id',$material_details->id)->where('user_id',$user->id)->get();
        $clarificationsrecevier = Clarification::where('service_id',$service_details->id)->where('receiver_user_id',$user->id)->get();
        $clarificationssender = Clarification::select('user_id')->where('service_id',$service_details->id)->where('receiver_user_id',$user->id)->pluck('user_id')->toArray();
        $clarificationssenderemailid = Employee::whereIn('user_id', $clarificationssender)->pluck('name'); 

       if($nv->budget_type == "CAPEX" && $nv->budgetary_provision == "Approved"){
        $capex_budget = CapexBudget::where('department_id', $nv->department_id)->where('fiscal_year', $nv->fiscal_year)->exists();
        if($capex_budget){
         $capex_budget1 = CapexBudget::where('department_id', $nv->department_id)->select('fiscal_year')->get();
        foreach($capex_budget1 as $cap){
         if($nv->fiscal_year == $cap->fiscal_year) {
         $budget1 = CapexBudget::where('department_id', $nv->department_id)->where('fiscal_year', $cap->fiscal_year)->first();
         $budget = $budget1->revised_budget;
         // dd($budget->revised_budget);
         }
        }

        if(!budget::where('dept_id',$nv->department_id)->where('budget_type',$nv->budget_type)->where('fiscal_year',$nv->fiscal_year)->exists()){
            budget::insert([
                'dept_id'=>$nv->department_id ?? null,
                'budget_type' => $nv->budget_type ?? null,
                'fiscal_year' => $nv->fiscal_year ?? null,
                'budget_avl' => $budget ?? null,
                'service_id' => $service_details->id ?? null,
                'nv_id'=> $nv_id ?? null,
            ]);
           }
        
        }
    }elseif($nv->budget_type == "OPEX" && $nv->budgetary_provision == "Approved"){
        $opex_budget = OpexBudget::where('department_id', $nv->department_id)->where('fiscal_year', $nv->fiscal_year)->exists();

        if($opex_budget)
        {
         $opex_budget1 = OpexBudget::where('department_id', $nv->department_id)->select('fiscal_year')->get();

        foreach($opex_budget1 as $cap)
        {
            if($nv->fiscal_year == $cap->fiscal_year) 
            {
                $budget1 = OpexBudget::where('department_id', $nv->department_id)->where('fiscal_year', $cap->fiscal_year)->first();
                $budget = $budget1->revised_budget;
            }
        }

        if(!budget::where('dept_id',$nv->department_id)->where('budget_type',$nv->budget_type)->where('fiscal_year',$nv->fiscal_year)->exists())
        {
            budget::insert([
                'dept_id'=>$nv->department_id ?? null,
                'budget_type' => $nv->budget_type ?? null,
                'fiscal_year' => $nv->fiscal_year ?? null,
                'budget_avl' => $budget ?? null,
                'service_id' => $service_details->id ?? null,
                'nv_id'=> $nv_id ?? null,
            ]);

           }
        
        }
    }
        $workflowStatus = DB::table('capex_workflows_status')->where('nv_id',$nv_id)->where('department_id',16)->first();
        $transfer_to_nominee1 = $workflowStatus->transfer_to_nominee1 ?? 0;
        
        return view('admin.nvService.preview', compact('transfer_to_nominee1','countFiles','service_details', 'service_doc', 'user','employees','ap_rj_status','chats','nv_year','nv','clarifications','clarificationsrecevier','clarificationssenderemailid'));
      
    }
    public function approvedByStatus(Request $request)
    {
        $user = Auth::user();
        // $id=[];
        $status = $request->status_id;
        $service_id = $request->service_id;
        $nv_id = $request->nv_id;
        $id = $nv_id;
        $remark = $request->remark;
        $dop_ref_no = $request->dop_ref_no;
        $check_ceonm2 = $request->check_ceonm2 ?? null;
        $check_technology = 1 ;
        $derc_info =$request->derc_info;

        $file = $request->file('approval_attachements')??null;
        if(!empty($file )){
            $newFileName = $file->getClientOriginalName();
            $filePath = public_path('approval-remark/' . $newFileName);
    
            if (!File::exists($filePath)) {
                $file->move(public_path('approval-remark'), $newFileName);
            }
    
        }
        
        $employees = Employee::where("user_id", $user->id)->first();
        $nv12 = NeedValidation::where('id', $request->nv_id)->first();
        
        $departmentIds = explode(',', $employees->department_id);
        $departments = Department::whereIn("id", $departmentIds)->get();

        $datas = NVService::where('nv_id', $id)->orderBy('id', 'desc')->first();
        $employs = Employee::with('department')->where("user_id", $datas->user_id)->first();
        if ($employs && $employs->department) {
            $department = $employs->department;
            
            $hod = $department->dep_hod;
            $rv1 = $department->dep_rew1;
            $rv2 = $department->dep_rew2;
            $rv3 = $department->dep_rew3;
            $rv4 = $department->dep_rew4;
            $group_cio = $department->group_cio;
        } else {
            $hod = $rv1 = $rv2 = $rv3 = $rv4 = $group_cio = null;
        }

        $id0 = Workflow::where("id", 1)->first();
        $id1 = Workflow::skip(1)->first();
        $id2 = Workflow::skip(2)->first();
        // $id3 = Workflow::skip(3)->first();
        $id3 = OpexWorkflow::where("id", 1)->first();
        if($nv12->budget_type == "CAPEX"){
            $id4 = Workflow::skip(3)->first();
            $id5 = Workflow::skip(4)->first();
        }else{
            $id4 = OpexWorkflow::skip(1)->first();
            $id5 = OpexWorkflow::skip(2)->first();
        }
     
        $tble_service = NVService::where('id', $service_id)->first();
        
        if ($status == 'Approve') {
            $status = 1;
        } elseif ($status == 'Reject') {
            $status = 2;
        } else {
            $status = 0;
        }

        $tble_service = NVService::where('id', $service_id)->orderBy('id','desc')->first();

        if(!empty($rv1) && $rv1 == $user->id){
            $update_status = Nvsericestatus::where('service_id', $service_id)->first();
            $update_status->update([
                'rv1_id' => $user->id,
                'rv1_status' => $status,
                'rv1_timestamp' => date('Y-m-d H:i'),
                'rv1_action_ip' => request()->ip(),
                'rv1_remark' => $remark,
                'rv1_attachement' => $newFileName??null
            ]);

            $initiated_date = NVService::select('created_at')->where('id', $service_id)->first();
            $initiated_by = User::select('name','id')->where('id', $tble_service->user_id)->first();
            $user = User::select('email')->where('id', $tble_service->user_id)->first();
            $approved = Nvsericestatus::where('service_id', $service_id)->first();
            $approved_by = User::select('name','id','email')->where('id', $approved->rv1_id)->first();
            $approved_date = Nvsericestatus::select('rv1_timestamp')->where('service_id', $service_id)->first();

            $user_id = Auth::user()->id;
            $nv_id = $request->nv_id;
            $nv_type = NeedValidation::where('id', $nv_id)->first();
            $employee = Employee::where('user_id', $user_id)->first();
            $depart = Department::where("id",  $nv_type->department_id)->first();
            $ini_date = NVService::where('nv_id', $nv_id)->first();
            $ini_by =  Employee::where('user_id', $ini_date->user_id)->first();

            $name = $approved_by->name;
            $date = $approved_date->rv1_timestamp;

            if($department->dep_rew2 === null){
                $emp_rv = Employee::where('user_id', $department->dep_rew3)->first();

                if($department->dep_rew3 === null){
                    $emp_rv = Employee::where('user_id', $department->dep_rew4)->first();
                    if($department->dep_rew4 === null){
                        $emp_rv = Employee::where('user_id', $department->dep_hod)->first();
                    }else{
                        $emp_rv = Employee::where('user_id', $department->dep_rew4)->first();
                    }
                }else{
                    $emp_rv = Employee::where('user_id', $department->dep_rew3)->first();
                }
            }else{
                $emp_rv = Employee::where('user_id', $department->dep_rew2)->first();
            }
            
            if ($status == 1) {
                $status = "Approved";
            } elseif ($status == 2) {
                $status = "Rejected";
                $update_draft = NVService::where('id', $service_id)->first();
                $update_draft->update([
                    'draft' => '0',
                ]);
                Nvsericestatus::where('service_id', $service_id)
                ->update(['is_reject' => 1]);
                $nv = NeedValidation::where('id',$request->nv_id)->first();
                if($nv->budgetary_provision == "Approved"){
                $nvservice = NVService::where('id', $service_id)->first();
                $budget = budget::where('fiscal_year',$nv->fiscal_year)->where('dept_id',$nv->department_id)->where('budget_type',$nv->budget_type)->first();
                $total = $budget->total_budget - $nvservice->approved_budget;
                $budget = budget::where('fiscal_year',$nv->fiscal_year)->where('dept_id',$nv->department_id)->where('budget_type',$nv->budget_type)->update([
                    'total_budget' => $total ?? '',
                ]);
              }
            }

             $p1 = "You have a new request that requires your approval:";
             $p2 = "Please review the request and take appropriate action.";
             $serviceType = ($nv_type->service_id == 1) ? 'Material' : 'Service';
             $subject = "Your NV (NV/{$nv_type->budget_type}/{$nv_type->fiscal_year}/{$depart->name}/{$serviceType}/{$nv_type->id}) has been {$status}";
             $to_emails = $user->email;

             Mail::send('emailtemp.initiator_mail', ['username' => 'user', 'initiated_date' => $initiated_date, 'initiated_by' => $initiated_by,'name' => $name, 'ini_date' => $ini_date,'ini_by' => $ini_by, 'p1' => $p1, 'p2' => $p2, 'nv_type'=>$nv_type,'remark' => $remark ?? '', 'department' => $depart, 'status' => $status,], function ($message) use ($to_emails,$subject) {
                 $message->from(env('MAIL_FROM_ADDRESS'), 'NV');
                 $message->to($to_emails);
                 $message->subject($subject);
             });
     
             $p1 = "Your nv request has been Rejected:";
             $p2 = "";
             $to_emails = $emp_rv->email;
             Mail::send('emailtemp.doc_mail', ['username' => 'user','initiated_date' => $initiated_date,'initiated_by' => $initiated_by,'ini_date' => $ini_date,'ini_by' => $ini_by, 'p1' => $p1, 'p2' => $p2, 'date'=> $date,'name'=> $name,'status'=>$status, 'department' => $depart,'nv_type'=>$nv_type], function ($message) use ($to_emails) {
                 $message->from(env('MAIL_FROM_ADDRESS'), 'NV');
                 $message->to($to_emails);
                 $message->subject("Need Validation Status Update : Seeking your validation");
             });

        }elseif(!empty($rv2) && $rv2 == $user->id){
            $update_status = Nvsericestatus::where('service_id', $service_id)->first();
            $update_status->update([
                'rv2_id' => $user->id,
                'rv2_status' => $status,
                'rv2_timestamp' => date('Y-m-d H:i'),
                'rv2_action_ip' => request()->ip(),
                'rv2_remark' => $remark,
                'rv2_attachement' => $newFileName??null
            ]);

            $initiated_date = NVService::select('created_at')->where('id', $service_id)->first();
            $initiated_by = User::select('name','id')->where('id', $tble_service->user_id)->first();
            $user = User::select('email')->where('id', $tble_service->user_id)->first();
            $approved = Nvsericestatus::where('service_id', $service_id)->first();
            $approved_by = User::select('name','id','email')->where('id', $approved->rv2_id)->first();
            $approved_date = Nvsericestatus::select('rv2_timestamp')->where('service_id', $service_id)->first();

            $name = $approved_by->name;
            $date = $approved_date->rv2_timestamp;

            $user_id = Auth::user()->id;
            $nv_id = $request->nv_id;
            $nv_type = NeedValidation::where('id', $nv_id)->first();
            $employee = Employee::where('user_id', $user_id)->first();
            $depart = Department::where("id",  $nv_type->department_id)->first();
            $ini_date = NVService::where('nv_id', $nv_id)->first();
            $ini_by =  Employee::where('user_id', $ini_date->user_id)->first();


                if($department->dep_rew3 === null){
                    $emp_rv = Employee::where('user_id', $department->dep_rew4)->first();
                    if($department->dep_rew4 === null){
                        $emp_rv = Employee::where('user_id', $department->dep_hod)->first();
                    }else{
                        $emp_rv = Employee::where('user_id', $department->dep_rew4)->first();
                    }
                }else{
                    $emp_rv = Employee::where('user_id', $department->dep_rew3)->first();
                }
           
            
            if ($status == 1) {
                $status = "Approved";
            } elseif ($status == 2) {
                $status = "Rejected";
                $update_draft = NVService::where('id', $service_id)->first();
                $update_draft->update([
                    'draft' => '0',
                ]);
                Nvsericestatus::where('service_id', $service_id)
                ->update(['is_reject' => 1]);
                $nv = NeedValidation::where('id',$request->nv_id)->first();
                if($nv->budgetary_provision == "Approved"){
                $nvservice = NVService::where('id', $service_id)->first();
                $budget = budget::where('fiscal_year',$nv->fiscal_year)->where('dept_id',$nv->department_id)->where('budget_type',$nv->budget_type)->first();
                $total = $budget->total_budget - $nvservice->approved_budget;
                $budget = budget::where('fiscal_year',$nv->fiscal_year)->where('dept_id',$nv->department_id)->where('budget_type',$nv->budget_type)->update([
                    'total_budget' => $total ?? '',
                ]);} 
            }

            $p1 = "You have a new request that requires your approval:";
            $p2 = "Please review the request and take appropriate action.";
            $serviceType = ($nv_type->service_id == 1) ? 'Material' : 'Service';
            $subject = "Your NV (NV/{$nv_type->budget_type}/{$nv_type->fiscal_year}/{$depart->name}/{$serviceType}/{$nv_type->id}) has been {$status}";
            $to_emails = $user->email;
            Mail::send('emailtemp.initiator_mail', ['username' => 'user', 'initiated_date' => $initiated_date, 'initiated_by' => $initiated_by,'name' => $name, 'ini_date' => $ini_date,'ini_by' => $ini_by, 'p1' => $p1, 'p2' => $p2, 'nv_type'=>$nv_type,'remark' => $remark ?? '', 'department' => $depart, 'status' => $status,], function ($message) use ($to_emails,$subject) {
                $message->from(env('MAIL_FROM_ADDRESS'), 'NV');
                $message->to($to_emails);
                $message->subject($subject);
            });
    
            $p1 = "Your nv request has been Rejected:";
            $p2 = "";
            $to_emails = $emp_rv->email;
            Mail::send('emailtemp.doc_mail', ['username' => 'user','initiated_date' => $initiated_date,'initiated_by' => $initiated_by,'ini_date' => $ini_date,'ini_by' => $ini_by, 'p1' => $p1, 'p2' => $p2, 'date'=> $date,'name'=> $name,'status'=>$status, 'department' => $depart,'nv_type'=>$nv_type], function ($message) use ($to_emails) {
                $message->from(env('MAIL_FROM_ADDRESS'), 'NV');
                $message->to($to_emails);
                $message->subject("Need Validation Status Update : Seeking your validation");
            });

        }elseif(!empty($rv3) && $rv3 == $user->id){
            $update_status = Nvsericestatus::where('service_id', $service_id)->first();
            $update_status->update([
                'rv3_id' => $user->id,
                'rv3_status' => $status,
                'rv3_timestamp' => date('Y-m-d H:i'),
                'rv3_action_ip' => request()->ip(),
                'rv3_remark' => $remark,
                'rv3_attachement' => $newFileName??null
            ]);

            $initiated_date = NVService::select('created_at')->where('id', $service_id)->first();
            $initiated_by = User::select('name','id')->where('id', $tble_service->user_id)->first();
            $user = User::select('email')->where('id', $tble_service->user_id)->first();
            $approved = Nvsericestatus::where('service_id', $service_id)->first();
            $approved_by = User::select('name','id','email')->where('id', $approved->rv3_id)->first();
            $approved_date = Nvsericestatus::select('rv3_timestamp')->where('service_id', $service_id)->first();

            $name = $approved_by->name;
            $date = $approved_date->rv3_timestamp;

            $user_id = Auth::user()->id;
            $nv_id = $request->nv_id;
            $nv_type = NeedValidation::where('id', $nv_id)->first();
            $employee = Employee::where('user_id', $user_id)->first();
            $depart = Department::where("id",  $nv_type->department_id)->first();
            $ini_date = NVService::where('nv_id', $nv_id)->first();
            $ini_by =  Employee::where('user_id', $ini_date->user_id)->first();
            
            if($department->dep_rew4 === null){
                $emp_rv = Employee::where('user_id', $department->dep_hod)->first();
            }else{
                $emp_rv = Employee::where('user_id', $department->dep_rew4)->first();
            }
            
            
            if ($status == 1) {
                $status = "Approved";
            } elseif ($status == 2) {
                $status = "Rejected";
                $update_draft = NVService::where('id', $service_id)->first();
                $update_draft->update([
                    'draft' => '0',
                ]);
                Nvsericestatus::where('service_id', $service_id)
                ->update(['is_reject' => 1]);
                $nv = NeedValidation::where('id',$request->nv_id)->first();
                if($nv->budgetary_provision == "Approved"){
                $nvservice = NVService::where('id', $service_id)->first();
                $budget = budget::where('fiscal_year',$nv->fiscal_year)->where('dept_id',$nv->department_id)->where('budget_type',$nv->budget_type)->first();
                $total = $budget->total_budget - $nvservice->approved_budget;
                $budget = budget::where('fiscal_year',$nv->fiscal_year)->where('dept_id',$nv->department_id)->where('budget_type',$nv->budget_type)->update([
                    'total_budget' => $total ?? '',
                ]);}
            }

            $p1 = "You have a new request that requires your approval:";
            $p2 = "Please review the request and take appropriate action.";
            $serviceType = ($nv_type->service_id == 1) ? 'Material' : 'Service';
            $subject = "Your NV (NV/{$nv_type->budget_type}/{$nv_type->fiscal_year}/{$depart->name}/{$serviceType}/{$nv_type->id}) has been {$status}";
            $to_emails = $user->email;
            Mail::send('emailtemp.initiator_mail', ['username' => 'user', 'initiated_date' => $initiated_date, 'initiated_by' => $initiated_by,'name' => $name, 'ini_date' => $ini_date,'ini_by' => $ini_by, 'p1' => $p1, 'p2' => $p2, 'nv_type'=>$nv_type,'remark' => $remark ?? '', 'department' => $depart, 'status' => $status,], function ($message) use ($to_emails,$subject) {
                $message->from(env('MAIL_FROM_ADDRESS'), 'NV');
                $message->to($to_emails);
                $message->subject($subject);
            });
    
            $p1 = "Your nv request has been Rejected:";
            $p2 = "";
            $to_emails = $emp_rv->email;
            Mail::send('emailtemp.doc_mail', ['username' => 'user','initiated_date' => $initiated_date,'initiated_by' => $initiated_by,'ini_date' => $ini_date,'ini_by' => $ini_by, 'p1' => $p1, 'p2' => $p2, 'date'=> $date,'name'=> $name,'status'=>$status, 'department' => $depart,'nv_type'=>$nv_type], function ($message) use ($to_emails) {
                $message->from(env('MAIL_FROM_ADDRESS'), 'NV');
                $message->to($to_emails);
                $message->subject("Need Validation Status Update : Seeking your validation");
            });

        }elseif(!empty($rv4) && $rv4 == $user->id){
            $update_status = Nvsericestatus::where('service_id', $service_id)->first();
            $update_status->update([
                'rv4_id' => $user->id,
                'rv4_status' => $status,
                'rv4_timestamp' => date('Y-m-d H:i'),
                'rv4_action_ip' => request()->ip(),
                'rv4_remark' => $remark,
                'rv4_attachement' => $newFileName??null
            ]);
            $initiated_date = NVService::select('created_at')->where('id', $service_id)->first();
            $initiated_by = User::select('name','id')->where('id', $tble_service->user_id)->first();
            $user = User::select('email')->where('id', $tble_service->user_id)->first();
            $approved = Nvsericestatus::where('service_id', $service_id)->first();
            $approved_by = User::select('name','id','email')->where('id', $approved->rv4_id)->first();
            $approved_date = Nvsericestatus::select('rv4_timestamp')->where('service_id', $service_id)->first();

            $name = $approved_by->name;
            $date = $approved_date->rv4_timestamp;

            $user_id = Auth::user()->id;
            $nv_id = $request->nv_id;
            $nv_type = NeedValidation::where('id', $nv_id)->first();
            $employee = Employee::where('user_id', $user_id)->first();
            $depart = Department::where("id",  $nv_type->department_id)->first();
            $ini_date = NVService::where('nv_id', $nv_id)->first();
            $ini_by =  Employee::where('user_id', $ini_date->user_id)->first();

            $emp_rv = Employee::where('user_id', $department->dep_hod)->first();
            if ($status == 1) {
                $status = "Approved";
            } elseif ($status == 2) {
                $status = "Rejected";
                $update_draft = NVService::where('id', $service_id)->first();
                $update_draft->update([
                    'draft' => '0',
                ]);
                Nvsericestatus::where('service_id', $service_id)
                ->update(['is_reject' => 1]);
                $nv = NeedValidation::where('id',$request->nv_id)->first();
                if($nv->budgetary_provision == "Approved"){
                $nvservice = NVService::where('id', $service_id)->first();
                $budget = budget::where('fiscal_year',$nv->fiscal_year)->where('dept_id',$nv->department_id)->where('budget_type',$nv->budget_type)->first();
                $total = $budget->total_budget - $nvservice->approved_budget;
                $budget = budget::where('fiscal_year',$nv->fiscal_year)->where('dept_id',$nv->department_id)->where('budget_type',$nv->budget_type)->update([
                    'total_budget' => $total ?? '',
                ]);} 
            }

            $p1 = "You have a new request that requires your approval:";
            $p2 = "Please review the request and take appropriate action.";
            $serviceType = ($nv_type->service_id == 1) ? 'Material' : 'Service';
            $subject = "Your NV (NV/{$nv_type->budget_type}/{$nv_type->fiscal_year}/{$depart->name}/{$serviceType}/{$nv_type->id}) has been {$status}";
            $to_emails = $user->email;
            Mail::send('emailtemp.initiator_mail', ['username' => 'user', 'initiated_date' => $initiated_date, 'initiated_by' => $initiated_by,'name' => $name, 'ini_date' => $ini_date,'ini_by' => $ini_by, 'p1' => $p1, 'p2' => $p2, 'nv_type'=>$nv_type,'remark' => $remark ?? '', 'department' => $depart, 'status' => $status,], function ($message) use ($to_emails,$subject) {
                $message->from(env('MAIL_FROM_ADDRESS'), 'NV');
                $message->to($to_emails);
                $message->subject($subject);
            });
            $p1 = "Your nv request has been Rejected:";
            $p2 = "";
            $to_emails = $emp_rv->email;
            Mail::send('emailtemp.doc_mail', ['username' => 'user','initiated_date' => $initiated_date,'initiated_by' => $initiated_by,'ini_date' => $ini_date,'ini_by' => $ini_by, 'p1' => $p1, 'p2' => $p2, 'date'=> $date,'name'=> $name,'status'=>$status, 'department' => $depart,'nv_type'=>$nv_type], function ($message) use ($to_emails) {
                $message->from(env('MAIL_FROM_ADDRESS'), 'NV');
                $message->to($to_emails);
                $message->subject("Need Validation Status Update : Seeking your validation");
            });

        }elseif(!empty($hod) && $hod == $user->id){
            $update_status = Nvsericestatus::where('service_id', $service_id)->first();
            $updateData = [
                'hod_id' => $user->id,
                'hod_status' => $status,
                'hod_timestamp' => date('Y-m-d H:i'),
                'hod_action_ip' => request()->ip(),
                'hod_remark' => $remark,
                'hod_attachement' => $newFileName??null
            ];
          
            $update_status->update($updateData);

           if(empty($group_cio) && ($status == 1)){
                $workflows = Workflow::where('status', 1)->get();
                $opex_workflows = OpexWorkflow::where('status', 1)->get();
            
                if ($nv12->budget_type == 'CAPEX' && $workflows->isNotEmpty()) {
                    $workflow_serial = 1;
                    $first_user_id = null;
                     
                    foreach ($workflows->sortBy('sr_no')->groupBy('sr_no') as $sr_no => $records) {
                                $hasInsert = false;
                        
                        foreach ($records as $record) {
                            $commonData = [
                                'nv_id'         => $nv_id,
                                'service_id'   => $service_id,
                                'department_id' => $record->work_dep,
                                'sr_no'         => $record->sr_no,
                                'nv_stage_status' => 0,
                                'created_at'    => now(),
                                'updated_at'    => now(),
                                'nv_budget_type'   => $nv12->budget_type,
                            ];
                            for ($i = 1; $i <= 4; $i++) {
                                $field = 'work_rew' . $i;
                    
                                if (!empty($record->$field)) {
                                    DB::table('capex_workflows_status')->insert(
                                        array_merge($commonData, [
                                            'workflow_user_id' => $record->$field,
                                            'reviewer_name'    => $field,
                                            'workflow_serial'  =>$workflow_serial,
                                             
                                        ])
                                    );
                                    $hasInsert = true;
                                      
                                }
                            }
                            if (!empty($record->approver)) {
                            
                                DB::table('capex_workflows_status')->insert(
                                    array_merge($commonData, [
                                    'workflow_user_id' => $record->approver,
                                    'reviewer_name'    => 'approver',
                                    'workflow_serial'  =>$workflow_serial,
                                     
                                    ])
                                );
                                $hasInsert = true;
                                  
                                if ($workflow_serial == 1 && $first_user_id === null) {
                                    $first_user_id = $record->approver;
                                }
                            }
                        }
                        if ($hasInsert) {
                            $workflow_serial++;
                        }
                    }
                }elseif($nv12->budget_type == 'OPEX' && $opex_workflows->isNotEmpty() ){
                    $workflow_serial = 1;
                    $first_user_id = null;
                     
                    foreach ($opex_workflows->sortBy('sr_no')->groupBy('sr_no') as $sr_no => $records) {
                            $hasInsert = false;
                            
                        foreach ($records as $record) {
                            $commonData = [
                                'nv_id'         => $nv_id,
                                'service_id'   => $service_id,
                                'department_id' => $record->work_dep,
                                'sr_no'         => $record->sr_no,
                                'nv_stage_status' => 0,
                                'created_at'    => now(),
                                'updated_at'    => now(),
                                'nv_budget_type'   => $nv12->budget_type,
                            ];
                            for ($i = 1; $i <= 4; $i++) {
                                $field = 'work_rew' . $i;
                    
                                if (!empty($record->$field)) {
                                
                                    DB::table('capex_workflows_status')->insert(
                                        array_merge($commonData, [
                                            'workflow_user_id' => $record->$field,
                                            'reviewer_name'    => $field,
                                            'workflow_serial'  =>$workflow_serial,
                                             
                                        ])
                                    );
                                    $hasInsert = true;
                                      
                                }
                            }
                            if (!empty($record->approver)) {
                            
                                DB::table('capex_workflows_status')->insert(
                                    array_merge($commonData, [
                                    'workflow_user_id' => $record->approver,
                                    'reviewer_name'    => 'approver',
                                    'workflow_serial'  =>$workflow_serial,
                                     
                                    ])
                                );
                                $hasInsert = true;
                                  
                                if ($workflow_serial == 1 && $first_user_id === null) {
                                    $first_user_id = $record->approver;
                                }
                            }
                        }
                        if ($hasInsert) {
                            $workflow_serial++;
                        }
                    }
                }
           }
            $initiated_date = NVService::select('created_at')->where('id', $service_id)->first();
            $initiated_by = User::select('name','id')->where('id', $tble_service->user_id)->first();
            $user = User::select('email')->where('id', $tble_service->user_id)->first();
            $approved = Nvsericestatus::where('service_id', $service_id)->first();

            $approved_by = User::select('name','id','email')->where('id', $approved->hod_id)->first();
            $approved_date = Nvsericestatus::select('hod_timestamp')->where('service_id', $service_id)->first();

            $name = $approved_by->name;
            $date = $approved_date->hod_timestamp;

            $user_id = Auth::user()->id;
            $nv_id = $request->nv_id;
            $nv_type = NeedValidation::where('id', $nv_id)->first();
            $employee = Employee::where('user_id', $user_id)->first();
            $depart = Department::where("id",  $nv_type->department_id)->first();
            $ini_date = NVService::where('nv_id', $nv_id)->first();
            $ini_by =  Employee::where('user_id', $ini_date->user_id)->first();

            
            if(!empty($group_cio)){
                $emp_rv = User::select('email')->where('id', $group_cio)->first();
            }else{
                if(!empty($first_user_id)){
                    $emp_rv = User::select('email')->where('id', $first_user_id)->first();
                }
            }

            if ($status == 1) {
                $status = "Approved";
            } elseif ($status == 2) {
                $status = "Rejected";
                $update_draft = NVService::where('id', $service_id)->first();
                $update_draft->update([
                    'draft' => '0',
                ]);
                 Nvsericestatus::where('service_id', $service_id)
                ->update(['is_reject' => '1']);
                $nv = NeedValidation::where('id',$request->nv_id)->first();
                if($nv->budgetary_provision == "Approved"){
                $nvservice = NVService::where('id', $service_id)->first();
                $budget = budget::where('fiscal_year',$nv->fiscal_year)->where('dept_id',$nv->department_id)->where('budget_type',$nv->budget_type)->first();
                $total = $budget->total_budget - $nvservice->approved_budget;
                $budget = budget::where('fiscal_year',$nv->fiscal_year)->where('dept_id',$nv->department_id)->where('budget_type',$nv->budget_type)->update([
                    'total_budget' => $total ?? '',
                ]);}
            }

            $p1 = "You have a new request that requires your approval:";
            $p2 = "Please review the request and take appropriate action.";
            $serviceType = ($nv_type->service_id == 1) ? 'Material' : 'Service';
            $subject = "Your NV (NV/{$nv_type->budget_type}/{$nv_type->fiscal_year}/{$depart->name}/{$serviceType}/{$nv_type->id}) has been {$status}";
            $to_emails = $user->email;
            Mail::send('emailtemp.initiator_mail', ['username' => 'user', 'initiated_date' => $initiated_date, 'initiated_by' => $initiated_by,'name' => $name, 'ini_date' => $ini_date,'ini_by' => $ini_by, 'p1' => $p1, 'p2' => $p2, 'nv_type'=>$nv_type,'remark' => $remark ?? '', 'department' => $depart, 'status' => $status,], function ($message) use ($to_emails,$subject) {
                $message->from(env('MAIL_FROM_ADDRESS'), 'NV');
                $message->to($to_emails);
                $message->subject($subject);
            });
    
            $p1 = "Your nv request has been Rejected:";
            $p2 = "";
            $to_emails = $emp_rv->email;
            if(!empty($emp_rv->email)){
                Mail::send('emailtemp.doc_mail', ['username' => 'user','initiated_date' => $initiated_date,'initiated_by' => $initiated_by,'ini_date' => $ini_date,'ini_by' => $ini_by, 'p1' => $p1, 'p2' => $p2, 'date'=> $date,'name'=> $name,'status'=>$status, 'department' => $depart,'nv_type'=>$nv_type], function ($message) use ($to_emails) {
                    $message->from(env('MAIL_FROM_ADDRESS'), 'NV');
                    $message->to($to_emails);
                    $message->subject("Need Validation Status Update : Seeking your validation");
                });
            }
        }elseif (!empty($group_cio) && $group_cio == $user->id) { 
            $update_status = Nvsericestatus::where('service_id', $service_id)->first();
           
            $updateData = [
                'groupcio_id' => $user->id,
                'groupcio_status' => $status,
                'groupcio_timestamp' => date('Y-m-d H:i'),
                'groupcio_action_ip' => request()->ip(),
                'groupcio_remark' => $remark,
                'groupcio_attachement' => $newFileName??null,
            ];
           
            $update_status->update($updateData);

            $workflows = Workflow::where('status', 1)->get();
            $opex_workflows = OpexWorkflow::where('status', 1)->get();
            
            if($status == 1){
                if($nv12->budget_type == 'CAPEX' && $workflows->isNotEmpty()) {
                    $workflow_serial = 1;
                    $first_user_id = null;
                     
                    foreach ($workflows->sortBy('sr_no')->groupBy('sr_no') as $sr_no => $records) {
                                        $hasInsert = false;
                                
                        foreach ($records as $record) {
                            $commonData = [
                                'nv_id'         => $nv_id,
                                'service_id'   => $service_id,
                                'department_id' => $record->work_dep,
                                'sr_no'         => $record->sr_no,
                                'nv_stage_status' => 0,
                                'created_at'    => now(),
                                'updated_at'    => now(),
                                'nv_budget_type'   => $nv12->budget_type,
                            ];
                            for ($i = 1; $i <= 4; $i++) {
                                $field = 'work_rew' . $i;
                    
                                if (!empty($record->$field)) {
                                    DB::table('capex_workflows_status')->insert(
                                        array_merge($commonData, [
                                            'workflow_user_id' => $record->$field,
                                            'reviewer_name'    => $field,
                                            'workflow_serial'  =>$workflow_serial,
                                             
                                        ])
                                    );
                                    $hasInsert = true;
                                      
                                }
                            }
                            if (!empty($record->approver)) {
                                DB::table('capex_workflows_status')->insert(
                                    array_merge($commonData, [
                                        'workflow_user_id' => $record->approver,
                                        'reviewer_name'    => 'approver',
                                        'workflow_serial'  =>$workflow_serial,
                                         
                                    ])
                                );
                                $hasInsert = true;
                                  
                                if ($workflow_serial == 1 && $first_user_id === null) {
                                    $first_user_id = $record->approver;
                                }
                            }
                        }
                        if ($hasInsert) {
                            $workflow_serial++;
                        }
                    }
                
                }elseif($nv12->budget_type == 'OPEX' && $opex_workflows->isNotEmpty() ){
                    $workflow_serial = 1;
                    $first_user_id = null;
                     
                    foreach ($opex_workflows->sortBy('sr_no')->groupBy('sr_no') as $sr_no => $records) {
                                    $hasInsert = false;
                            
                        foreach ($records as $record) {
                            $commonData = [
                                'nv_id'         => $nv_id,
                                'service_id'   => $service_id,
                                'department_id' => $record->work_dep,
                                'sr_no'         => $record->sr_no,
                                'nv_stage_status' => 0,
                                'created_at'    => now(),
                                'updated_at'    => now(),
                                'nv_budget_type'   => $nv12->budget_type,
                            ];
                            for ($i = 1; $i <= 4; $i++) {
                                $field = 'work_rew' . $i;
                    
                                if (!empty($record->$field)) {
                                    DB::table('capex_workflows_status')->insert(
                                        array_merge($commonData, [
                                            'workflow_user_id' => $record->$field,
                                            'reviewer_name'    => $field,
                                            'workflow_serial'  =>$workflow_serial,
                                             
                                        ])
                                    );

                                    $hasInsert = true;
                                      
                                }
                            }
                            if (!empty($record->approver)) {
                                DB::table('capex_workflows_status')->insert(
                                    array_merge($commonData, [
                                        'workflow_user_id' => $record->approver,
                                        'reviewer_name'    => 'approver',
                                        'workflow_serial'  =>$workflow_serial,
                                         
                                    ])
                                );
                                $hasInsert = true;
                                  
                                if ($workflow_serial == 1 && $first_user_id === null) {
                                    $first_user_id = $record->approver;
                                }
                            }
                        }
                        if ($hasInsert) {
                            $workflow_serial++;
                        }
                    }
                }
            }

            $initiated_date = NVService::select('created_at')->where('id', $service_id)->first();
            $initiated_by = User::select('name','id')->where('id', $tble_service->user_id)->first();
            $user = User::select('email')->where('id', $tble_service->user_id)->first();
            $approved = Nvsericestatus::where('service_id', $service_id)->first();
            $approved_by = User::select('name','id','email')->where('id', $approved->groupcio_id)->first();
            $approved_date = Nvsericestatus::select('groupcio_timestamp')->where('service_id', $service_id)->first();

            $name = $approved_by->name;
            $date = $approved_date->groupcio_timestamp;

            $user_id = Auth::user()->id;
            $nv_id = $request->nv_id;
            $nv_type = NeedValidation::where('id', $nv_id)->first();
            $employee = Employee::where('user_id', $user_id)->first();
            $depart = Department::where("id",  $nv_type->department_id)->first();
            $ini_date = NVService::where('nv_id', $nv_id)->first();
            $ini_by =  Employee::where('user_id', $ini_date->user_id)->first();

            if(!empty($first_user_id)){
                $emp_rv = User::select('email')->where('id', $first_user_id)->first();
            }

            if ($status == 1) {
                $status = "Approved";
            } elseif ($status == 2) {
                $status = "Rejected";
                $update_draft = NVService::where('id', $service_id)->first();
                $update_draft->update([
                    'draft' => '0',
                ]);
                 Nvsericestatus::where('service_id', $service_id)
                ->update(['is_reject' => 1]);
                $nv = NeedValidation::where('id',$request->nv_id)->first();
                if($nv->budgetary_provision == "Approved"){
                $nvservice = NVService::where('id', $service_id)->first();
                $budget = budget::where('fiscal_year',$nv->fiscal_year)->where('dept_id',$nv->department_id)->where('budget_type',$nv->budget_type)->first();
                $total = $budget->total_budget - $nvservice->approved_budget;
                $budget = budget::where('fiscal_year',$nv->fiscal_year)->where('dept_id',$nv->department_id)->where('budget_type',$nv->budget_type)->update([
                    'total_budget' => $total ?? '',
                ]);}
            }

            $p1 = "You have a new request that requires your approval:";
            $p2 = "Please review the request and take appropriate action.";
            $serviceType = ($nv_type->service_id == 1) ? 'Material' : 'Service';
            $subject = "Your NV (NV/{$nv_type->budget_type}/{$nv_type->fiscal_year}/{$depart->name}/{$serviceType}/{$nv_type->id}) has been {$status}";
            $to_emails = $user->email;
            Mail::send('emailtemp.initiator_mail', ['username' => 'user', 'initiated_date' => $initiated_date, 'initiated_by' => $initiated_by,'name' => $name, 'ini_date' => $ini_date,'ini_by' => $ini_by, 'p1' => $p1, 'p2' => $p2, 'nv_type'=>$nv_type,'remark' => $remark ?? '', 'department' => $depart, 'status' => $status,], function ($message) use ($to_emails,$subject) {
                $message->from(env('MAIL_FROM_ADDRESS'), 'NV');
                $message->to($to_emails);
                $message->subject($subject);
            });

            // $p1 = "Your nv request has been Rejected:";
            // $p2 = "";
            // $to_emails = $emp_rv->email;
            // if(!empty($emp_rv->email)){
            //     Mail::send('emailtemp.doc_mail', ['username' => 'user', 'initiated_date' => $initiated_date, 'initiated_by' => $initiated_by, 'ini_date' => $ini_date,'ini_by' => $ini_by, 'p1' => $p1, 'p2' => $p2, 'date' => $date, 'name' => $name, 'status' => $status, 'department' => $depart,'nv_type'=>$nv_type,], function ($message) use ($to_emails) {
            //         $message->from(env('MAIL_FROM_ADDRESS'), 'NV');
            //         $message->to($to_emails);
            //         $message->subject("Need Validation Status Update : Seeking your validation");
            //     });
            // }

        }else{
                $workflows = DB::table('capex_workflows_status')
                ->where('transfer_to_nominee1',0)
                ->where('service_id', $service_id)
                ->where('workflow_user_id', $user->id)
                ->where('department_id', $user->department_id)
                ->where('nv_budget_type', $nv12->budget_type)
                ->get();
                foreach ($workflows as $workflow) {
        
                    $capexWorkflowUsers = $workflow->workflow_user_id;
                    $reviewer = $workflow->reviewer_name;
                    $workflow_serial    = $workflow->workflow_serial;
                    $budgetType = $workflow->nv_budget_type;
                   
                    if (!empty($capexWorkflowUsers) && $capexWorkflowUsers == $user->id) {

                        $updateData = [
                            'nv_stage_status' => $status,
                            'nv_stage_timestamp' => date('Y-m-d H:i'),
                            'nv_stage_action_ip' => request()->ip(),
                            'nv_stage_remark' => $remark,
                            'nv_stage_attachement' =>  $newFileName??null,
                            'nv_stage_signature' => $user->signature_id ?? null
                        ];
                
                        $isReviewer = strtolower($workflow->reviewer_name) != 'approver';
                
                        if ($workflow->reviewer_name != 'approver' ) {
                            DB::table('capex_workflows_status')
                                ->where('id', $workflow->id)
                                ->update($updateData);
                                $transfer_ceonom1 = $request->transfer_to_nominee1;
                            // 2. Update only status for other reviewers in same workflow_serial & nv_id
                                if($transfer_ceonom1 == 1){
                                DB::table('capex_workflows_status')
                                    ->where('nv_id', $workflow->nv_id)
                                    ->where('workflow_serial', $workflow_serial)
                                    ->where('reviewer_name', '!=', 'approver')
                                    ->where('workflow_user_id', '==', $user->id)
                                    ->update(['nv_stage_status' => $status]);
                                     DB::table('capex_workflows_status')->where('department_id', 16)->where('nv_id',$workflow->nv_id)->update(['nv_stage_status' => 0, 'transfer_to_nominee1' => 1]);
                                }else{
                                DB::table('capex_workflows_status')
                                    ->where('nv_id', $workflow->nv_id)
                                    ->where('workflow_serial', $workflow_serial)
                                    ->where('reviewer_name', '!=', 'approver')
                                    ->where('workflow_user_id', '==', $user->id)
                                    ->update(['nv_stage_status' => $status]);

                                }                        
                            } else {
                            // If user is an approver, update only their record
                            DB::table('capex_workflows_status')
                                ->where('id', $workflow->id)
                                 ->where('workflow_user_id', $user->id) // Fixed: removed '=='
                                ->update($updateData);
                        }
                        
                        NVService::where('id', $service_id)
                        ->update(['check_ceonm2' => $check_ceonm2]);

                        if ($status == 1) {
                            $status = "Approved";
                            $ceoStage = DB::table('capex_workflows_status as cws')
                            ->join(DB::raw('(SELECT nv_id, MAX(workflow_serial) as max_serial
                                            FROM capex_workflows_status
                                            GROUP BY nv_id) as latest'),
                                function ($join) {
                                    $join->on('cws.nv_id', '=', 'latest.nv_id')
                                            ->on('cws.workflow_serial', '=', 'latest.max_serial');
                                })
                            ->where('cws.service_id', $service_id)
                            ->where('cws.nv_budget_type', $nv12->budget_type)
                            ->first();
                            if (!empty($ceoStage)){
                                if($ceoStage->workflow_user_id == $user->id){
                                    Nvsericestatus::where('service_id', $service_id)
                                   ->update(['ceo_status' => 1]);
                                    $nv = NeedValidation::where('id', $nv_id)->where('delete_draft',0)->orderBy('id', 'desc')->first();
                        
                                    if($nv->budget_type == "CAPEX")
                                    {
                                       $capex_budget = Capex::where('department_id', $nv->department_id)->first('additional_budget');
                                       $additional_budget = CapexBudget::where('department_id', $nv->department_id)->where('fiscal_year', $nv->fiscal_year)->first();
                        
                                    }
                                    else
                                    {
                                      $opex_budget = Opex::where('department_id', $nv->department_id)->first('additional_budget');
                                      $additional_budget = OpexBudget::where('department_id', $nv->department_id)->where('fiscal_year', $nv->fiscal_year)->first();
                          
                                    }
                             
                                    if ($nv->budget_type == "CAPEX" && $nv->budgetary_provision == "Additional" && $nv->fiscal_year == $additional_budget->fiscal_year )
                                    {
                                        $data = NVService::where('nv_id', $nv->id)->orderBy('id', 'desc')->first("total_buget");
                                    
                                        if($additional_budget->additional_budget == null){
                                            $capex_budget_approve =  CapexBudget::where('department_id', $nv->department_id)->where('fiscal_year',$additional_budget->fiscal_year)->update([
                                                'additional_budget' => $data->total_buget
                                            ]);
                                        }else{
                                            $capexbudget =  $additional_budget->additional_budget;
                                            $total = $capexbudget + $data->total_buget;
                                            // dd($total);
                                            $capex_budget_approve =  CapexBudget::where('department_id', $nv->department_id)->where('fiscal_year',$additional_budget->fiscal_year)->update([
                                                'additional_budget' => $total 
                                            ]);
                                        }
                                         
                                    }
                                    elseif($nv->budget_type == "OPEX" && $nv->budgetary_provision == "Additional" && $nv->fiscal_year == $additional_budget->fiscal_year )
                                    {
                                        $data = NVservice::where('nv_id', $nv->id)->orderBy('id', 'desc')->first("total_buget");
                                    
                                        if($additional_budget->additional_budget == null){
                                            $capex_budget_approve =  OpexBudget::where('department_id', $nv->department_id)->where('fiscal_year',$additional_budget->fiscal_year)->update([
                                                'additional_budget' => $data->total_buget
                                            ]);
                                        }else{
                                            $capexbudget =  $additional_budget->additional_budget;
                                            $total = $capexbudget + $data->total_buget;
                                        
                                            $capex_budget_approve =  OpexBudget::where('department_id', $nv->department_id)->where('fiscal_year',$additional_budget->fiscal_year)->update([
                                                'additional_budget' => $total 
                                            ]);
                                        }
                                    }elseif($nv->budget_type == "OPEX" && $nv->budgetary_provision == "Approved" && $nv->fiscal_year == $additional_budget->fiscal_year )
                                    {
                                        $data = NVservice::where('nv_id', $nv->id)->orderBy('id', 'desc')->first("add_budget");
                                    
                                        if($additional_budget->additional_budget == null){
                                            $capex_budget_approve =  OpexBudget::where('department_id', $nv->department_id)->where('fiscal_year',$additional_budget->fiscal_year)->update([
                                                'additional_budget' => $data->add_budget
                                            ]);
                                        }else{
                                            $capexbudget =  $additional_budget->additional_budget;
                                            $total = $capexbudget + $data->add_budget;
                                        
                                            $capex_budget_approve =  OpexBudget::where('department_id', $nv->department_id)->where('fiscal_year',$additional_budget->fiscal_year)->update([
                                                'additional_budget' => $total 
                                            ]);
                                        }
                                    }elseif ($nv->budget_type == "CAPEX" && $nv->budgetary_provision == "Approved" && $nv->fiscal_year == $additional_budget->fiscal_year )
                                    {
                                        $data = NVService::where('nv_id', $nv->id)->orderBy('id', 'desc')->first("add_budget");
                                    
                                        if($additional_budget->additional_budget == null){
                                            $capex_budget_approve =  CapexBudget::where('department_id', $nv->department_id)->where('fiscal_year',$additional_budget->fiscal_year)->update([
                                                'additional_budget' => $data->add_budget
                                            ]);
                                        }else{
                                            $capexbudget =  $additional_budget->additional_budget;
                                            $total = $capexbudget + $data->add_budget;
                                            // dd($total);
                                            $capex_budget_approve =  CapexBudget::where('department_id', $nv->department_id)->where('fiscal_year',$additional_budget->fiscal_year)->update([
                                                'additional_budget' => $total 
                                            ]);
                                        }
                                         
                                    }
                                }
                            }
                          
            
                        }elseif ($status == 2) {
                            $status = "Rejected";
                            $update_draft = NVService::where('id', $service_id)->first();
                            $update_draft->update([
                                'draft' => '0',
                            ]);
                            Nvsericestatus::where('service_id', $service_id)
                            ->update(['is_reject' => 1]);
                            $nv = NeedValidation::where('id',$request->nv_id)->first();
                            if($nv->budgetary_provision == "Approved"){
                                $nvservice = NVService::where('id', $service_id)->first();
                                $budget = budget::where('fiscal_year',$nv->fiscal_year)->where('dept_id',$nv->department_id)->where('budget_type',$nv->budget_type)->first();
                                $total = $budget->total_budget - $nvservice->approved_budget;
                                $budget = budget::where('fiscal_year',$nv->fiscal_year)->where('dept_id',$nv->department_id)->where('budget_type',$nv->budget_type)->update([
                                    'total_budget' => $total ?? '',
                                ]);
                            }
                        }

                        $initiated_date = NVService::select('created_at')->where('id', $service_id)->first();
                        $initiated_by = User::select('name','id')->where('id', $tble_service->user_id)->first();
                        $user = User::select('email')->where('id', $tble_service->user_id)->first();
                        $approved_by = User::select('name','id','email')->where('id', $workflow->workflow_user_id)->first();
            
                        $name = $approved_by->name;
                        $date = $workflow->nv_stage_timestamp;
                      
                        $user_id = Auth::user()->id;
                        $nv_id = $request->nv_id;
                        $nv_type = NeedValidation::where('id', $nv_id)->first();
                        $employee = Employee::where('user_id', $user_id)->first();
                        $depart = Department::where("id",  $nv_type->department_id)->first();
                        $ini_date = NVService::where('nv_id', $nv_id)->first();
                        $ini_by =  Employee::where('user_id', $ini_date->user_id)->first();
                    

                        $to_emails = [];
                        $to_emails2 = [];
                        if ($isReviewer) {
                            // Reviewer approved, notify approver at the same workflow_serial
                            $mailToNext = DB::table('capex_workflows_status')
                                ->where('transfer_to_nominee1','=',0)
                                ->where('service_id', $service_id)
                                ->where('nv_budget_type', $nv12->budget_type)
                                ->where('workflow_serial', $workflow->workflow_serial)
                                ->where('reviewer_name', 'approver')
                                ->first();

                            if ($mailToNext) {
                                $emp = User::select('email')->where('id', $mailToNext->workflow_user_id)->first();
                                if ($emp && !empty($emp->email)) {
                                    $to_emails2[] = $emp->email;
                                }
                            }

                        } else {
                            // Approver approved, check for reviewers in next workflow_serial
                            $nextWorkflowSerial = $workflow->workflow_serial + 1;
                            $reviewers = DB::table('capex_workflows_status')
                                ->where('transfer_to_nominee1','=',0)
                                ->where('service_id', $service_id)
                                ->where('nv_budget_type', $nv12->budget_type)
                                ->where('workflow_serial', $nextWorkflowSerial)
                                ->where('reviewer_name', '!=', 'approver')
                                ->get();

                            if ($reviewers->isNotEmpty()) {
                                foreach ($reviewers as $reviewer) {
                                    $emp = User::select('email')->where('id', $reviewer->workflow_user_id)->first();
                                    if ($emp && !empty($emp->email)) {
                                        $to_emails2[] = $emp->email;
                                    }
                                }
                            } else {    
                                // No reviewers, fallback to approver of next workflow_serial
                                $approver = DB::table('capex_workflows_status')
                                    ->where('transfer_to_nominee1', '=' , 0)
                                    ->where('service_id', $service_id)
                                    ->where('nv_budget_type', $nv12->budget_type)
                                    ->where('workflow_serial', $nextWorkflowSerial)
                                    ->where('reviewer_name', 'approver')
                                    ->first();

                                if ($approver) {
                                    $emp = User::select('email')->where('id', $approver->workflow_user_id)->first();
                                    if ($emp && !empty($emp->email)) {
                                        $to_emails2[] = $emp->email;
                                    }
                                }
                            }
                        }

                        // dd($to_emails);
                        
                        $p1 = "You have a new request that requires your approval:";
                        $p2 = "Please review the request and take appropriate action.";
                        $serviceType = ($nv_type->service_id == 1) ? 'Material' : 'Service';
                        $subject = "Your NV (NV/{$nv_type->budget_type}/{$nv_type->fiscal_year}/{$depart->name}/{$serviceType}/{$nv_type->id}) has been {$status}";
                        $to_emails = $user->email;
                        
                        Mail::send('emailtemp.initiator_mail', ['username' => 'user', 'initiated_date' => $initiated_date, 'initiated_by' => $initiated_by,'name' => $name, 'ini_date' => $ini_date,'ini_by' => $ini_by, 'p1' => $p1, 'p2' => $p2, 'nv_type'=>$nv_type,'remark' => $remark ?? '', 'department' => $depart, 'status' => $status,], function ($message) use ($to_emails,$subject) {
                            $message->from(env('MAIL_FROM_ADDRESS'), 'NV');
                            $message->to($to_emails);
                            $message->subject($subject);
                        });
                
                        $p1 = "Your nv request has been Rejected:";
                        $p2 = "";
                        // $to_emails = $emp_rv->email;
                        if (!empty($to_emails2)) {
                            Mail::send('emailtemp.doc_mail', ['username' => 'user','initiated_date' => $initiated_date,'initiated_by' => $initiated_by,'ini_date' => $ini_date,'ini_by' => $ini_by, 'p1' => $p1, 'p2' => $p2, 'date'=> $date,'name'=> $name,'status'=>$status, 'department' => $depart,'nv_type'=>$nv_type], function ($message) use ($to_emails2) {
                                $message->from(env('MAIL_FROM_ADDRESS'), 'NV');
                                $message->to($to_emails2);
                                $message->subject("Need Validation Status Update : Seeking your validation");
                            });
                        }
                    }
              }
        }
       
            // // PDF SAVE FOR ZOHO SIGN
            // $service_details =   NVService::where('nv_id', $id)->orderBy('id', 'desc')->first();
            // $service_doc = ServiceDoc::where('service_id', $service_details->id)->orderBy('id', 'desc')->first();
            // $employeesss = Employee::where('department_id', $service_details->dept_id)->get();
            // $nv_year = NeedValidation::select('id', 'fiscal_year')->where('id', $id)->first();
            // $service_import = ServiceBOQBulk::where('nv_id',$id)->where('service_id',$service_details->id)->orderBy('id', 'desc')->get();
            // $data = [
            //     'id'=>$id ?? '',
            //     'employees'=>$employeesss ?? '',
            //     'service_details'=> $service_details ?? '',
            //     'service_doc'=>$service_doc ?? '',
            //     'nv_year'=>$nv_year ?? '',
            //     'chats'=> $chats ?? '',
            //     'service_import'=>$service_import ?? '',
            // ];
            // $mainContentView1 = view('admin/nvService/service_dataPdf', $data)->render();
            // $mainContentView2 = view('admin/nvService/nv-pdf-service', $data)->render();
            // if(count($data['service_import']) > 0){
            // $mainContentView3 = view('admin/nvService/BOQpdf', $data)->render();
            // $pdfContent = $mainContentView2 .$mainContentView1 . $mainContentView3;
            // }else{
            // $pdfContent = $mainContentView2 .$mainContentView1;
            // }
        
            // $pdf = PDF::loadHTML($pdfContent);
        
            // $tempDir = public_path('pdf/');
            // File::makeDirectory($tempDir, 0755, true, true);
            // $pdfFile = $tempDir . 'NVService.pdf';
            // $pdf->save($pdfFile);


        // //   // <-----> ZOHO SIGNATURE START <----->

        //         $refreshToken = "1000.a4d3679b075d2c00f69f30d8f9c30011.98674e2421d6c7a57c06e326d0c5a7b9";
        //         $clientID = "1000.JY9FCMA9X7YY5DA1KWNNU3WC4RNM8Z";
        //         $clientSecret = "4bccf973b553e05f54b628365d345032f2ccee7ba5";
        //         $redirectURI = "https://sign.rediansoftware.com";
                
        //         // Prepare POST data
        //         $postData = array(
        //             'refresh_token' => $refreshToken,
        //             'client_id' => $clientID,
        //             'client_secret' => $clientSecret,
        //             'redirect_uri' => $redirectURI,
        //             'grant_type' => 'refresh_token'
        //         );
                
        //         // Initialize cURL session
        //         $curl = curl_init();
                
        //         // Set cURL options
        //         curl_setopt_array($curl, array(
        //             CURLOPT_URL => "https://accounts.zoho.in/oauth/v2/token",
        //             CURLOPT_RETURNTRANSFER => true,
        //             CURLOPT_POST => true,
        //             CURLOPT_POSTFIELDS => http_build_query($postData)
        //         ));
                
        //         // Execute the request
        //         $response = curl_exec($curl);
                
        //         // Check for errors
        //         if(curl_errno($curl)){
        //             echo 'Curl error: ' . curl_error($curl);
        //         }
                
        //         // Close cURL session
        //         curl_close($curl);
                
        //         // Output the response
        //         $responseData = json_decode($response, true);
                
        //         $accessToken= $responseData['access_token'];
                
                
                
        //         // Create document and add recipients
        //         $actionsJson = new \stdClass();
        //         $actionsJson->recipient_name = $approved_by->name;
        //         $actionsJson->recipient_email = $approved_by->email;
        //         // dd($approved_by->email);
        //         $actionsJson->action_type = "SIGN";
        //         $actionsJson->private_notes = "Please get back to us for further queries";
        //         $actionsJson->signing_order = 0;
        //         $actionsJson->verify_recipient = false;
        //         $actionsJson->verification_type = "EMAIL";
        //         // Set is_embedded as true for generating embedded signing URL
        //         $actionsJson->is_embedded = true;
                
        //         $requestJSON = new \stdClass();
        //         $requestJSON->request_name = "00" . $nv_type->id . "/" . $serviceType   . " - " . $approved_by->name;
        //         // $requestJSON->request_name = "NV/" . $serviceType . "/" . $nv_type->budget_type . "/" . $depart->name . "/" . $nv_type->fiscal_year . "/00" . $nv_type->id .  "  (" . $status .  " by - " . $approved_by->name .")";
        //         $requestJSON->expiration_days = 1;
        //         $requestJSON->is_sequential = true;
        //         $requestJSON->email_reminders = true;
        //         $requestJSON->reminder_period = 8;
        //         $requestJSON->actions = array($actionsJson);
                
        //         $request = new \stdClass();
        //         $request->requests = $requestJSON;
        //         $data = json_encode($request);
                
        //         // Set up POST data with file and JSON data
        //         $POST_DATA = array(
        //             'data' => $data,
        //             // /home/pooja/Desktop/NVMaterial.pdf
        //             'file' => new \CURLFile(public_path('pdf/NVService.pdf'))
        //         );
                
        //         $curl = curl_init("https://sign.zoho.in/api/v1/requests");
        //         curl_setopt($curl, CURLOPT_TIMEOUT, 30);
        //         curl_setopt($curl, CURLOPT_HTTPHEADER, array(
        //             "Authorization: Zoho-oauthtoken {$accessToken}",
        //         ));
        //         curl_setopt($curl, CURLOPT_POST, true);
        //         curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        //         curl_setopt($curl, CURLOPT_POSTFIELDS, $POST_DATA);
        //         $response = curl_exec($curl);
        //         $jsonbody = json_decode($response); // contains field types response
        //         // dd($jsonbody);
        //         if ($jsonbody->status == "success") {
                
        //             $createdRequestId = $jsonbody->requests->request_id;
        //             $requestId = $createdRequestId;
        //             // Save the ID from the response to update later
        //             $createdRequest = new \stdClass();
        //             $createdRequest = $jsonbody->requests;
                
        //         } else {
        //             // Error check for error
        //             echo $jsonbody->message;
        //         }
                
        //         curl_close($curl);
                
        //         // Set your access token
        //         //$accessToken = '1000.3c95949d463e74f4a1293177d9d47b79.e32d291c198a7d43ea71ac9d5c75f6d5';
                
        //         // Retrieve necessary data from your object
        //         $action_id = $createdRequest->actions[0]->action_id;
        //         $recipientName = $createdRequest->actions[0]->recipient_name;
        //         $recipientEmail = $createdRequest->actions[0]->recipient_email;
        //         $action_type = $createdRequest->actions[0]->action_type;
        //         $document_id = $createdRequest->document_ids[0]->document_id;
                
        //         try {
        //             // Prepare actions JSON
        //             $actionsJson1 = array(
        //                 "action_id" => $action_id,
        //                 "recipient_name" => $recipientName,
        //                 "recipient_email" => $recipientEmail,
        //                 "action_type" => $action_type
        //             );
                
        //             // Prepare field JSON
        //             $fieldJson = array(
        //                 "document_id" => $document_id,
        //                 "field_name" => "Signature",
        //                 "field_type_name" => "Signature",
        //                 "field_label" => "Text - 1",
        //                 "field_category" => "Signature",
        //                 "abs_width" => "0",
        //                 "abs_height" => "0",
        //                 "is_mandatory" => false,
        //                 "x_coord" => "39",
        //                 "y_coord" => "610",
        //                 "page_no" => 0
        //             );
                
        //             // Attach field JSON to actions JSON
        //             $actionsJson1["fields"] = array($fieldJson);
                
        //             // Prepare document JSON
        //             $documentJson1 = array(
        //                 "actions" => array($actionsJson1)
        //             );
                
        //             // Prepare data for the request
        //             $data1 = array(
        //                 "requests" => $documentJson1
        //             );
                
        //             // Encode data as JSON
        //             $jsonData = json_encode($data1);
                
        //             // Initialize cURL session
        //             $ch = curl_init();
                
        //             // Set cURL options
        //             curl_setopt($ch, CURLOPT_URL, "https://sign.zoho.in/api/v1/requests/{$requestId}/submit");
        //             curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        //             curl_setopt($ch, CURLOPT_POST, true);
        //             curl_setopt($ch, CURLOPT_POSTFIELDS, $jsonData);
        //             curl_setopt($ch, CURLOPT_HTTPHEADER, array(
        //                 "Authorization: Zoho-oauthtoken {$accessToken}",
        //                 "Content-Type: application/json"
        //             ));
                
        //             // Execute the request
        //             $response = curl_exec($ch);
                
        //             // Check for errors
        //             if ($response === false) {
        //                 throw new Exception("cURL error: " . curl_error($ch));
        //             }
                
        //             // Decode the response
        //             $responseData = json_decode($response, true);
                
        //             // Handle the response
        //             if (isset($responseData["status"]) && $responseData["status"] == "success") {
        //                 $result = array(
        //                     "status" => "success",
        //                     "message" => "Document submitted successfully",
        //                     "actionId" => $responseData["requests"]["actions"][0]["action_id"],
        //                     "requestId" => $responseData["requests"]["request_id"]
        //                 );
        //             } else {
        //                 $result = array(
        //                     "status" => "failure",
        //                     "message" => "Failed to submit the document"
        //                 );
        //             }
                
        //             // Close cURL session
        //             curl_close($ch);
                
        //             // Return result
        //         // return $result;
                
        //         } catch (Exception $e) {
        //             // Handle exceptions
        //             return array(
        //                 "status" => "failure",
        //                 "message" => "Failed to submit the document: " . $e->getMessage()
        //             );
        //         }
                
                
        //         //$accessToken = '1000.3c95949d463e74f4a1293177d9d47b79.e32d291c198a7d43ea71ac9d5c75f6d5';
        //         $createdRequestId = $jsonbody->requests->request_id;
        //         $requestId = $createdRequestId;
        //         $actionId = $createdRequest->actions[0]->action_id;
        //         $host = "https://www.aajtak.in/";
               
        //             $ch = curl_init();
                
        //             // Set the POST data
        //             $postData = array(
        //                 "host" => $host
        //             );
                
        //             // Set cURL options
        //             curl_setopt($ch, CURLOPT_URL, "https://sign.zoho.in/api/v1/requests/{$requestId}/actions/{$actionId}/embedtoken");
        //             curl_setopt($ch, CURLOPT_POST, true);
        //             curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($postData));
        //             curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        //             curl_setopt($ch, CURLOPT_HTTPHEADER, array(
        //                 "Authorization: Zoho-oauthtoken {$accessToken}",
        //                 "Content-Type: application/x-www-form-urlencoded"
        //             ));
                
        //             // Execute the request
        //             $response = curl_exec($ch);
        //         //echo $response;
        //             // Check for errors
        //             if ($response === false) {
        //                 throw new Exception("cURL error: " . curl_error($ch));
        //             }
                
        //             // Decode the response
        //           // Decode the response
        //             $responseData = json_decode($response, true);

        //             // Handle the response
        //             if (isset($responseData["status"]) && $responseData["status"] == "success") {
        //                 $signurl = $responseData["sign_url"];
                      
        //             } 
        // // // <-----> ZOHO SIGNATURE END <----->


        $result = array(
            "result" => "success", 
            "service_id" => $service_id, 
            "sign_url" => $signurl ?? '' ,
            "status" => $status,
        );
        // dd($result);
        return response()->json($result);
    }
    // Common function to send approval emails
    function sendApprovalEmails($user, $emp_rv, $status,$initiated_date,$initiated_by,$approved_by, $approved_date)
    {
        $p1 = "You have a new request that requires your approval:";
        $p2 = "Please review the request and take appropriate action.";
        $status = "Approved";
        $to_emails = $user->email;
        // $this->sendEmail($to_emails, "Need Validation Status Update : Seeking your validation", ['username' => 'user', 'p1' => $p1, 'p2' => $p2]);
        Queue::push(new SendEmailJob($to_emails, "Need Validation Status Update : Seeking your validation", ['username' => 'user', 'p1' => $p1, 'p2' => $p2,'status'=>$status,'initiated_date'=>$initiated_date,'initiated_by'=> $initiated_by,'approved_date'=> $approved_date,'approved_by'=> $approved_by]));

        $p1 = "Your nv request has been approved:";
        $p2 = "";
        $status = "Approved";
        $to_emails = $emp_rv->email;
        Queue::push(new SendEmailJob($to_emails, "Need Validation Status Update : Seeking your validation", ['username' => 'user', 'p1' => $p1, 'p2' => $p2,'status'=>$status,'initiated_date'=>$initiated_date,'initiated_by'=> $initiated_by,'approved_date'=> $approved_date,'approved_by'=> $approved_by]));

        // $this->sendEmail($to_emails, "Need Validation Status Update : Seeking your validation", ['username' => 'user', 'p1' => $p1, 'p2' => $p2]);
    }

    // Common function to send rejection emails
    function sendRejectionEmails($user,$emp_rv, $status,$initiated_date,$initiated_by,$approved_by, $approved_date)
    {
        $p1 = "You have a new request that requires your approval:";
        $p2 = "Please review the request and take appropriate action.";
        $status = "Rejected";
        $to_emails = $user->email;
        // $this->sendEmail($to_emails, "Need Validation Status Update : Seeking your validation", ['username' => 'user', 'p1' => $p1, 'p2' => $p2]);
        Queue::push(new SendEmailJob($to_emails, "Need Validation Status Update : Seeking your validation", ['username' => 'user', 'p1' => $p1, 'p2' => $p2,'status'=>$status,'initiated_date'=>$initiated_date,'initiated_by'=> $initiated_by,'approved_date'=> $approved_date,'approved_by'=> $approved_by]));


        $p1 = "Your nv request has been Rejected:";
        $p2 = "";
        $status = "Rejected";
        $to_emails = $emp_rv->email;
        Queue::push(new SendEmailJob($to_emails, "Need Validation Status Update : Seeking your validation", ['username' => 'user', 'p1' => $p1, 'p2' => $p2,'status'=>$status,'initiated_date'=>$initiated_date,'initiated_by'=> $initiated_by,'approved_date'=> $approved_date,'approved_by'=> $approved_by]));

        // $this->sendEmail($to_emails, "Need Validation Status Update : Seeking your validation", ['username' => 'user', 'p1' => $p1, 'p2' => $p2]);
    }
    // Reusable function to send emails
    function sendEmail($to_emails, $subject, $data)
    {
        Mail::send('emailtemp.doc_mail', $data, function ($message) use ($to_emails, $subject) {
            $message->from(env('MAIL_FROM_ADDRESS'), 'NV');
            $message->to($to_emails);
            $message->subject($subject);
        });
    }

    public function downloadNvservicePdf($id, $userId, Request $request)
    {
        set_time_limit(300);
        ini_set('memory_limit', '1024M');

        $user = \Auth()->user();
        // if ($user->role_id != 1 && (int)$userId !== $user->id) {
        //     abort(403, 'Unauthorized access.');
        // }
        $id = $request->id;

        // ✅ Fetch main data
        $NeedValidation = NeedValidation::findOrFail($id);

        $service_details = NVService::where('nv_id', $id)
            ->orderBy('id', 'desc')
            ->first();

        if (!$service_details) {
            abort(404, 'Service details not found');
        }

        $service_doc = ServiceDoc::where('service_id', $service_details->id)
            ->orderBy('id', 'desc')
            ->first();

        $employees = Employee::where('department_id', $service_details->dept_id)
            ->limit(20)
            ->get();

        $chats = Chat::where('service_id', $id)
            ->where('user_login_id', $user->id)
            ->limit(50)
            ->get();

        $nv_year = NeedValidation::select('id', 'fiscal_year')
            ->where('id', $id)
            ->first();

        $service_import = ServiceBOQBulk::where('nv_id', $id)
            ->where('service_id', $service_details->id)
            ->orderBy('id', 'desc')
            ->limit(200)
            ->get();

        $nv_number = "NV/" 
            . (optional($NeedValidation->service)->name ?? '') . "/" 
            . ($NeedValidation->budget_type ?? '') . "/" 
            . (optional($service_details->department)->name ?? '') 
            . "/FY" . ($nv_year->fiscal_year ?? '') 
            . "/00" . ($service_details->nv_id ?? '');

        $data = [
            'id' => $id,
            'employees' => $employees,
            'service_details' => $service_details,
            'service_doc' => $service_doc,
            'nv_year' => $nv_year,
            'chats' => $chats,
            'service_import' => $service_import,
            'nv_number' => $nv_number,
        ];

        $content1 = view('admin.nvService.nv-pdf-service', $data)->render();
        $content2 = view('admin.nvService.service_dataPdf', $data)->render();

        if ($service_import->count() > 0) {
            $content3 = view('admin.nvService.BOQpdf', $data)->render();
            $pdfContent = $content1 . $content2 . $content3;
        } else {
            $pdfContent = $content1 . $content2;
        }

            $pdf = PDF::loadHTML($pdfContent);


        return $pdf->stream('NVService.pdf');
    }

    public function downloadServiceFiles($id, $userId, Request $request)
    {
        $user = \Auth()->user();
        // if ($user->role_id != 1 && (int)$userId !== $user->id) {
        //     abort(403, 'Unauthorized access.');
        // }
        $id = $request->id;

        // Find the service details and associated service documents
        $service_details = NVService::where('nv_id', $id)->first();
        if (!$service_details) {
            abort(404, 'Service not found for this NV.');
        }
        $service_doc = ServiceDoc::where('service_id', $service_details->id)->first();
                // Create a temporary zip file
                $zip = new ZipArchive();
                $zipFilename = 'ServiceAttachments.zip';
                $zip->open($zipFilename, ZipArchive::CREATE | ZipArchive::OVERWRITE);

                try {
                    // Add files to the zip archive if they exist
                    if (!empty($service_doc->cost_calculation_for_service)) {
                        $zip->addFile(public_path('services-doc/'.$service_doc->cost_calculation_for_service), $service_doc->cost_calculation_for_service);
                    }if (!empty($service_doc->copy_of_previous_work)) {
                        $zip->addFile(public_path('services-doc/'.$service_doc->copy_of_previous_work), $service_doc->copy_of_previous_work);
                    }
                    if (!empty($service_doc->copy_of_derc_other)) {
                        $zip->addFile(public_path('services-doc/'.$service_doc->copy_of_derc_other), $service_doc->copy_of_derc_other);
                    }
                    if (!empty($service_doc->consuption_details)) {
                        $zip->addFile(public_path('services-doc/'.$service_doc->consuption_details), $service_doc->consuption_details);
                    }
                    if (!empty($service_doc->buget_stmt_for_both)) {
                        $zip->addFile(public_path('services-doc/'.$service_doc->buget_stmt_for_both), $service_doc->buget_stmt_for_both);
                    }
                    if (!empty($service_doc->photographs_of_product)) {
                        $zip->addFile(public_path('services-doc/'.$service_doc->photographs_of_product), $service_doc->photographs_of_product);
                    }
                    if (!empty($service_doc->material_procurement)) {
                        $zip->addFile(public_path('services-doc/'.$service_doc->material_procurement), $service_doc->material_procurement);
                    }
                    if (!empty($service_doc->vendor_quatation)) {
                        $zip->addFile(public_path('services-doc/'.$service_doc->vendor_quatation), $service_doc->vendor_quatation);
                    }
                    if (!empty($service_doc->others)) {
                        $zip->addFile(public_path('services-doc/'.$service_doc->others), $service_doc->others);
                    }
                    if (!empty($service_doc->vend_quatation)) {
                        $zip->addFile(public_path('services-doc/'.$service_doc->vend_quatation), $service_doc->vend_quatation);
                    }
                
                 
                    $zip->close();

                    // Download the zip file
                    if (file_exists($zipFilename)) {
                        $headers = [
                            'Content-Type' => 'application/zip',
                            'Content-Disposition' => 'attachment; filename="' . $zipFilename . '"',
                        ];
                        $response = response()->download($zipFilename, 'ServiceAttachments.zip', $headers);

                        // Unset attachments data and delete the temporary zip file
                      //  unlink($zipFilename);

                        return $response;
                    } else {
                      
                        return redirect()->back()->with('Failed to create the zip file.');
                    }
                } catch (Exception $e) {
                    // Handle any exceptions that may occur during the zip file creation
                    // return response()->json(['error' => 'An error occurred while creating the zip file.']);
                    return redirect()->back()->with('Failed to create the zip file.');
                }
    }

    public function store_service_form(Request $request)
    {
        $nv = NeedValidation::where('id',$request['nv_id'])->first();
        $total_ser_amo =$request->total_ser_amo;
        $budget_available = preg_replace('/[^\d.]/', '', $request->budget_avl);
    
        try {

       $request_input = $request->except('_token');
        $status = $request->status;
        $draft = $request->draft1;
        $just_of_proposal = $request->just_Prop;
        $broad_just = $request->broad_just;
        $background = $request->background;
        if($request['nvid']){
            $nv_mat = NVService::where('nv_id',$request['nvid'])->exists();
        }
      
        if ( empty($request['service_id']) && ($nv_mat == false)) {

               $nvservice = NVService::create([
                'dept_id' => $request_input['dept_id'],
                'draft' =>  $draft,
                'nv_id' => $request_input['nv_id'],
                'company_id' => $request_input['company_id'],
                'user_id' => \Auth::user()->id,
                'dop_ref_no' => $request_input['dop_ref_no'],
                'proposal_name' => $request_input['proposal_name'],
                'background' => $background,
                'just_of_proposal' => $just_of_proposal,
                'broad_just' => $broad_just,
                'special_remarks' => $request_input['special_remarks'],
                'derc_ref_no' => $request_input['derc_ref_no']?? null,
                'derc_approval' => $request_input['derc_approval']?? null,
                'derc_app_date' => $request_input['derc_app_date']?? null,
                'prop_number' => $request_input['prop_number']?? null,
                'past_3_year_actual_cost_fy' => implode(',', $request_input['past_3_year_actual_cost_fy']),
                'past_3_year_actual_cost' => implode(',', $request_input['past_3_year_actual_cost']),
                'past_3_year_actual_cost_service' => implode(',', $request_input['past_3_year_actual_cost_service']),
                'benefit' => $request_input['benefit'],
                'implements_years' =>$request_input['implements_years'],
                'implementation_period_from' => implode(',', $request->input('imp_from')),
                'implementation_period_to' => implode(',', $request->input('imp_to')),
                'implementation_plan_year_wise' => implode('.,', $request->input('imp_plan')),
              //  'type_of_proposal' => $request_input['type_of_proposal'],
                'mode_of_award_of_service' => $request_input['mode_of_award_of_service'],
                'amc_proposal_sdate' => $request_input['amc_proposal_sdate'],
                'amc_proposal_edate' => $request_input['amc_proposal_edate'],
                'budget_available' => $budget_available,
                'estimate_amount_of_service' => $request_input['estimate_amount_of_service'],
                'estimate_amount_of_service_civil' => $request_input['estimate_amount_of_service_civil'],
                'tax1' => $request_input['tax1'] ?? null,
                'tax2' => $request_input['tax2'] ?? null,
                'tax3' => $request_input['tax3'] ?? null,
                'tax4' => $request_input['tax4'] ?? null,
                'tax5' => $request_input['tax5'] ?? null,
                'estimate_amount_of_rr_chnage' => $request_input['estimate_amount_of_rr_chnage'],
                'estimate_amount_other' => $request_input['estimate_amount_other'],
                'total_buget' => $request_input['total_buget'],
                'add_budget' => $request_input['add_budget'] ?? null,
                'approved_budget' => $request_input['approved_budget'] ?? null,
                'total_ser_amo' => $request_input['total_ser_amo'],
                'service_amount' => implode(',',$request_input['service_amount']),
                'service_description'=> implode(',',$request_input['service_description']),
                'cause_analysis' => $request_input['cause_analysis'],
                'past_practice_text' => $request_input['past_practice_text'],
                'total_matyear1' => $request_input['total_matyear1'] ?? null,
                'total_matyear2' => $request_input['total_matyear2'] ?? null,
                'total_matyear3' => $request_input['total_matyear3'] ?? null,
                'tax_amount1' => $request_input['tax_amount1'] ?? null,
                'tax_amount2' => $request_input['tax_amount2'] ?? null,
                'tax_amount3' => $request_input['tax_amount3'] ?? null,
                // $data['created_by'] = \Auth::user()->id;
            ]);
            
            $count = Nvsericestatus::where('nv_id', $request_input['nv_id'])->count();
            $version_nv = ($count >= 1) ? $request_input['nv_id'] . '-v' . ($count + 1) : (string) $request_input['nv_id'];
            $nvservicestatus = new Nvsericestatus();
            $nvservicestatus->service_id = $nvservice->id;
            $nvservicestatus->nv_id = $request_input['nv_id'];
            $nvservicestatus->company_id = $request_input['company_id'];
            $nvservicestatus->draft = $draft;
            $nvservicestatus->version_nv = $version_nv;
            
            if ($nv->budget_type == "CAPEX") {
                $nvservicestatus->derc_info = '1';
            } elseif ($nv->budget_type == "OPEX") {
                $nvservicestatus->derc_info = !empty($request_input['prop_number']) ? '1' : '0';
            }
            
            $nvservicestatus->save();

            NeedValidation::where('id', $request_input['nv_id'])
            ->update(['proposal_type' => $request_input['proposal_type']]);
    
            if (!empty($nvservice)) {
                $serviceId = $nvservice->id;
                $data = [];
              
                $data['service_id'] = $serviceId;
                $fileFields = [
                    'cost_calculation_for_service',
                    'copy_of_previous_work',
                    'copy_of_derc_other',
                    'consuption_details',
                    'buget_stmt_for_both',
                    'material_procurement',
                    'photographs_of_product',
                    'vendor_quatation',
                    'vend_quatation',
                    'others',
                    'cm_rate_ref',
                    'vendor_quat',
                    'last_purchase_price',
                    'user_estimation',
                    'previous_wo_rc',
                    'past_practice',
                    'special_attch',
                    'just_prop_upload',
                ];
                foreach ($fileFields as $fieldName) {
                    if ($request->hasFile($fieldName)) {
                        $files = $request->file($fieldName);
                
                        if (is_array($files)) {
                            // Handle multiple files for 'others' field
                            $fileNames = [];
                
                            foreach ($files as $file) {
                                $newFileName = $file->getClientOriginalName();
                                $filePath = public_path('services-doc/' . $newFileName);
                
                                // Check if a file with the same name already exists
                                if (!File::exists($filePath)) {
                                    $file->move(public_path('services-doc'), $newFileName);
                                }
                
                                $fileNames[] = $newFileName;
                            }
                
                            $data[$fieldName] = implode(',', $fileNames); // Convert array to string
                        } else {
                            // Handle single file for other fields
                            $file = $files;
                            $newFileName = $file->getClientOriginalName();
                            $filePath = public_path('services-doc/' . $newFileName);
                
                            // Check if a file with the same name already exists
                            if (!File::exists($filePath)) {
                                $file->move(public_path('services-doc'), $newFileName);
                            }
                
                            $data[$fieldName] = $newFileName;
                        }
                    }
                }
                
             
                $data['created_by'] = \Auth::user()->id;
                $document = ServiceDoc::create($data);
            }
            $response['result'] = 'success';
            $response['msg'] = 'NV Service Created';
            $response['service_id'] = $nvservice->id;
        
        
        } else {
            $service_id = $request['service_id'];
        $nvm = NVService::find($service_id);
        $nv_status = Nvsericestatus::where('service_id', $nvm->id)->first();
        $nv_stage = DB::table('capex_workflows_status')->where('service_id', $nvm->id)->get();

        $allStagesZero = true;
        
        if ($nv_stage->count()) {
            foreach ($nv_stage as $stage) {
                if ($stage->nv_stage_status != 0) {
                    $allStagesZero = false;
                }
            }
        }

        if ($nv_status->rv1_status == 0 && 
                    $nv_status->rv2_status == 0 && 
                    $nv_status->rv3_status == 0 && 
                    $nv_status->rv4_status == 0 && 
                    $nv_status->hod_status == 0 && 
                    $nv_status->groupcio_status == 0 &&
                    $allStagesZero) {
            // dd('hi');
            NVService::find($service_id)->update([
                'dept_id' => $request_input['dept_id'],
                'nv_id' => $request_input['nv_id'],
                'draft' =>  $draft,
                'company_id' => $request_input['company_id'],
                'user_id' => \Auth::user()->id,
                'dop_ref_no' => $request_input['dop_ref_no'],
                'proposal_name' => $request_input['proposal_name'],
                'background' => $background,
                'just_of_proposal' => $just_of_proposal,
                'broad_just' => $broad_just,
                'special_remarks' => $request_input['special_remarks'],
                'derc_ref_no' => $request_input['derc_ref_no']?? null,
                'derc_approval' => $request_input['derc_approval']?? null,
                'derc_app_date' => $request_input['derc_app_date']?? null,
                'prop_number' => $request_input['prop_number']?? null,
                'past_3_year_actual_cost_fy' => implode(',', $request_input['past_3_year_actual_cost_fy']),
                'past_3_year_actual_cost' => implode(',', $request_input['past_3_year_actual_cost']),
                'past_3_year_actual_cost_service' => implode(',', $request_input['past_3_year_actual_cost_service']),
                'benefit' => $request_input['benefit'],
                'implements_years' =>$request_input['implements_years'],
                'implementation_period_from' => implode(',', $request->input('imp_from')),
                'implementation_period_to' => implode(',', $request->input('imp_to')),
                'implementation_plan_year_wise' => implode('.,', $request->input('imp_plan')),
               // 'type_of_proposal' => $request_input['type_of_proposal'],
                'mode_of_award_of_service' => $request_input['mode_of_award_of_service'],
                'amc_proposal_sdate' => $request_input['amc_proposal_sdate'],
                'amc_proposal_edate' => $request_input['amc_proposal_edate'],
                'budget_available' => $budget_available,
                'estimate_amount_of_service' => $request_input['estimate_amount_of_service'],
                'estimate_amount_of_service_civil' => $request_input['estimate_amount_of_service_civil'],
                'tax1' => $request_input['tax1'] ?? null,
                'tax2' => $request_input['tax2'] ?? null,
                'tax3' => $request_input['tax3'] ?? null,
                'tax4' => $request_input['tax4'] ?? null,
                'tax5' => $request_input['tax5'] ?? null,
                'estimate_amount_of_rr_chnage' => $request_input['estimate_amount_of_rr_chnage'],
                'estimate_amount_other' => $request_input['estimate_amount_other'],
                'total_buget' => $request_input['total_buget'],
                'add_budget' => $request_input['add_budget'] ?? null,
                'approved_budget' => $request_input['approved_budget'] ?? null,
                'total_ser_amo' => $request_input['total_ser_amo'],
                'service_amount' => implode(',',$request_input['service_amount']),
                'service_description'=> implode(',',$request_input['service_description']),
                'cause_analysis' => $request_input['cause_analysis'],
                'past_practice_text' => $request_input['past_practice_text'],
                'total_matyear1' => $request_input['total_matyear1'] ?? null,
                'total_matyear2' => $request_input['total_matyear2'] ?? null,
                'total_matyear3' => $request_input['total_matyear3'] ?? null,
                'tax_amount1' => $request_input['tax_amount1'] ?? null,
                'tax_amount2' => $request_input['tax_amount2'] ?? null,
                'tax_amount3' => $request_input['tax_amount3'] ?? null,
                // $data['created_by'] = \Auth::user()->id;
            ]);

            if ($nv->budget_type == "CAPEX") {
                $derc_info = '1';
            } else if ($nv->budget_type == "OPEX") {
                $derc_info = !empty($request_input['prop_number']) ? '1' : '0';
            }
            Nvsericestatus::where('service_id', $nv_status->service_id)
          ->update(['draft' => $draft,'derc_info' => $derc_info]);

            NeedValidation::where('id', $request_input['nv_id'])
        ->update(['proposal_type' => $request_input['proposal_type']]);        
          
            $data = [];

            $fileFields = [
                'cost_calculation_for_service',
                'copy_of_previous_work',
                'copy_of_derc_other',
                'consuption_details',
                'buget_stmt_for_both',
                'material_procurement',
                'photographs_of_product',
                'vendor_quatation',
                'vend_quatation',
                'others',
                'cm_rate_ref',
                'vendor_quat',
                'last_purchase_price',
                'user_estimation',
                'previous_wo_rc',
                'past_practice',
                'special_attch',
                'just_prop_upload',
            ];
            foreach ($fileFields as $fieldName) {
                if ($request->hasFile($fieldName)) {
                    $files = $request->file($fieldName);
            
                    if (is_array($files)) {
                        // Handle multiple files for 'others' field
                        $fileNames = [];
            
                        foreach ($files as $file) {
                            $newFileName = $file->getClientOriginalName();
                            $filePath = public_path('services-doc/' . $newFileName);
            
                            // Check if a file with the same name already exists
                            if (!File::exists($filePath)) {
                                $file->move(public_path('services-doc'), $newFileName);
                            }
            
                            $fileNames[] = $newFileName;
                        }
            
                        $data[$fieldName] = implode(',', $fileNames); // Convert array to string
                    } else {
                        // Handle single file for other fields
                        $file = $files;
                        $newFileName = $file->getClientOriginalName();
                        $filePath = public_path('services-doc/' . $newFileName);
            
                        // Check if a file with the same name already exists
                        if (!File::exists($filePath)) {
                            $file->move(public_path('services-doc'), $newFileName);
                        }
            
                        $data[$fieldName] = $newFileName;
                    }
                }
            }
           
    
            ServiceDoc::where('service_id', $service_id)->update($data);
            $response['result'] = 'success';
            $response['msg'] = 'Service Updated';
        } 
    
        }
     
        } catch (\Exception $e) {
            app(\App\Exceptions\Handler::class)->report($e);
            $response['result'] = 'failure';
            $response['msg'] = $e->getMessage();
        }

        return response()->json($response);
        
    }

    public function fetch_data_service(Request $request, $nv_id, $id)
    {
        $data = NVService::where('nv_id',$nv_id)->where('id',$id)->orderBy('id','desc')->get();
     
        return response()->json($data);
    }
    public function bulkServiceProviderStore(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'serviceboq' => 'required|max:2048',
            ]);
    
            if ($validator->fails()) {
                return response()->json(['ValidationError' => $validator->errors()], 422);
            }
  
    
            $path = $request->file('serviceboq')->store('matboq');
            $import = new ExcelImportService(new ServiceBOQBulk);
            $data = $import->import($path);
    
            $columns = array_diff(Schema::getColumnListing((new ServiceBOQBulk)->getTable()), ['id', 'created_at', 'updated_at','service_id','file','status']);
            $nv_id = $request->nv_id;
            $service_id = $request->service_id;
         
            $sequenceNumber = 800000000; // Set the starting sequence number
            $duplicateMaterialCodes = [];
    
            $data['rows'] = array_map(function ($row) use ($columns, $nv_id, $service_id, &$duplicateMaterialCodes, &$sequenceNumber) {
                if (count($columns) !== count($row)) {
                     // Strip out extra columns
                 $row = array_slice($row, 0, count($columns));
                    // return response()->json(['SheetError' => 'Invalid file: Number of columns do not match in all rows'], 422);
                }
            // dd($columns, $row);
                $row = array_combine($columns, $row);
    
                // if (empty($row['service_code'])) {
                //     return response()->json(['ServiceCodeError' => 'Service code is required'], 422);
                // }
    
                if ($row['service_code'] == 'N/A' || $row['service_code'] == '') {
                    $row['service_code'] = (string)$sequenceNumber++;
                }
    
                $validator = Validator::make($row, [
                    'qty' => 'required',
                ]);
    
                if ($validator->fails()) {
                    $duplicateMaterialCodes[] = $row['service_code'];
                }
    
                if ($row['service_code'] != 'N/A' || $row['service_code'] != '') {
                    // Only query the database if 'service_code' is not 'N/A'
                    $service_data = Boqmaterial::select('rate_ser', 'bun', 'service_short_text')
                        ->where('activity', $row['service_code'])
                        ->first();
                    }
                    // if ($service_data) {
                    //     $row['uom'] = $service_data->bun;
                    //     $row['description'] = $service_data->service_short_text;
                    // }
                    if (!empty($service_data)) {
                        $row['uom'] = $service_data->bun;
                        $row['description'] = $service_data->service_short_text;
                    } else {
                        // Use a temporary variable to swap values
                        $temp = $row['uom'];  // Store 'uom' in a temporary variable
                        $row['uom'] = $row['description'];  // Assign 'material_short_text' to 'uom'
                        $row['description'] = $temp;  // Assign the temporary value to 'material_short_text'
                    }
               
    
                $row['nv_id'] = $nv_id;
                $row['service_id'] = $service_id;
    
                $row['qty'] = max(0,intval($row['qty']));
                // $row['rate'] = max(0,$row['rate']);
                // $amount = isset($row['rate']) ? max(0,$row['rate'] * $row['qty']) : 0;
                if (!empty($service_data->rate_ser)) {
                    $row['rate'] = max(0, floatval($service_data->rate_ser)); 
              } else {
                  $row['rate'] = max(0, floatval($row['rate']));
              }
              $amount = '';
              if (!empty($row['rate']) && is_numeric($row['rate']) && is_numeric($row['qty'])) {
                  $amount = max(0, $row['rate'] * $row['qty']);
              } else {
                  $amount = 0;
              }
                $row['amount'] = $amount;
    
                return $row;
            }, $data['rows']);
    
            $import->seedDB($data['rows']);
    
            return response()->json(['message' => 'File imported successfully', 'data' => $data]);
        } catch (\Exception $e) {
            $message = $e->getMessage();
            return response()->json(['UploadError' => $e], 422);
        }
    }

    public function list_serviceBoq(Request $request)
    {


        $user = \Auth()->user();
        $nv_id = $request->nv_id;
        $service_id = $request->service_id;
        // dd( $nv_id);
        if ($request->ajax()) {

            $serviceBoq = datatables()
                ->of(
                    ServiceBOQBulk::where('nv_id', $nv_id)->where('service_id', $service_id)->orderBy('id', 'desc')->get()
                )

                ->addColumn('action', function ($data) use ($user) {
                   
                    $editButton = '<a href="/admin/nv_service/edit_service/' . $data->id . '" class="btn btn-sm btn-clean btn-icon" title="Edit"><i class="fas fa-edit text-info"></i></a>';

                    $deleteButton = '<a href="javascript:;" data-id="'.$data->id.'" class="btn btn-sm btn-clean btn-icon delete_service" title="Delete"><i class="fas fa-trash text-danger"></i></a>';

                    return $editButton . $deleteButton;
               
                })
                ->addIndexColumn()
                ->rawColumns(['action'])
                ->make(true);

            return $serviceBoq;
        }
        return view('admin.nvService.create');
    }

    public function edit_service($id)
    {
        //  $company = Division::select('id', 'name')->get();
        $data = ServiceBOQBulk::where(['id' => $id])->first();
        $nv_service = NeedValidation::where('id', $data->nv_id)->first();
        $serviceCodes = ServiceBOQBulk::pluck('service_code')->toArray();
        $escapedServiceCodes = [];

        foreach ($serviceCodes as $code) {
            if (is_string($code)) {
                $escapedServiceCodes[] = htmlspecialchars($code);
            } else {
                $escapedServiceCodes[] = $code;
            }
        }

        // return view('admin.nvMaterial.edit_service')->with(['data' => $data]);
        return view('admin.nvService.edit_service', compact('data', 'serviceCodes','nv_service'));
    }

    public function serviceBoqStore(Request $request)
    {

        try {
            $request_input = $request->except("_token");
            // dd($_POST);
            $nv_id = $request_input["nv_id"];
            $service_id = $request_input["service_id"];

            if (NeedValidation::where(["id" => $nv_id])->exists()) {
                $rules = [
                    "service_code_0" => "required",
                    "ser_rate" => "required",
                    "ser_quantity" => "required",
                    // "prop_type" => "required",
                    // "nv_type" => "required",
                    // "fiscal_year" => "required",
                ];

                $messages = [
                    "service_code_0.required" => "Please enter Service Code",
                    "ser_rate.required" => "Please enter Rate",
                    "ser_quantity.required" => "Please enter Quantity",
                    // "Please enter budgetary provision",
                    // "prop_type.required" => "Please enter proposal type",
                    // "nv_type.required" => "Please enter nv type",
                    // "fiscal_year.required" => "Please enter fiscal year",
                ];
                $validator = Validator::make($request_input, $rules, $messages);
                
                if ($validator->fails()) {
                    $response["msg"] = $validator->errors()->toArray();
                    $response["result"] = "error";
                } else {
                    $serviceBoq = ServiceBOQBulk::create([

                        'nv_id' =>  $request_input["nv_id"],
                        'service_id' =>  $service_id,
                        'service_code' => $request_input["service_code_0"],
                        'description' =>  $request_input["ser_des_0"],
                        'uom' =>  $request_input["ser_uom_0"],
                        'rate' => $request_input["ser_rate"],
                        'qty' => $request_input["ser_quantity"],
                        'amount' =>  $request_input["ser_total_amount"],
                    ]);
                    $response["result"] = "success";
                    $response["msg"] = "Service BOQ created";
                }
            } else {
                $rules = ["service_code_0" => "required",];
                $messages = ["service_code_0.required" => "Please enter service code",];

                $validator = Validator::make($request_input, $rules, $messages);
                if ($validator->fails()) {
                    $response["msg"] = $validator->errors()->toArray();
                    $response["result"] = "error";
                } else {
                    $serviceBoq = ServiceBOQBulk::create([
                        'nv_id' =>  $request_input["nv_id"],
                        'service_id' =>  $service_id,
                        'service_code' => $request_input["service_code_0"],
                        'description' =>  $request_input["ser_des_0"],
                        'uom' =>  $request_input["ser_uom_0"],
                        'rate' => $request_input["ser_rate"],
                        'qty' => $request_input["ser_quantity"],
                        'amount' =>  $request_input["ser_total_amount"],
                    ]);
                    // echo $location;

                    $response["result"] = "success";
                    $response["msg"] = "Service BOQ created";
                }
            }
        } catch (\Exception $e) {
            app(\App\Exceptions\Handler::class)->report($e);
            $response["result"] = "failure";
            $response["msg"] = $e->getMessage();
        }

        return response()->json($response);
    }
    
    public function delete_nv_service(Request $request,$id)
    {
    
        $data = NeedValidation::where('id',$id)->update([
            'delete_draft' => 1,
        ]);
       
        return response()->json([
            "data"             => $data ??'',
            "result"           => "success" ?? ','
        ]);
    }
    public function delete_all_service(Request $request,$id)
    {
        $service_id = $request->service_id;
        try {
            $allservices = ServiceBOQBulk::where('nv_id', $id)->where('service_id', $service_id);
            $count = $allservices->count(); // Count the number of records affected
    
            if ($count > 0) {
                $allservices->delete();
                $response['result'] = 'success';
                $response['msg'] = 'All services related to this nv is deleted.';
            } else {
                $response['result'] = 'failure';
                $response['msg'] = 'No services found for nv_id '.$id.'.';
            }
        } catch (Exception $e) {
            app(\App\Exceptions\Handler::class)->report($e);
            $response['result'] = 'failure';
            $response['msg'] = $e->getMessage();
        }
    
        return response()->json($response);
    }

    public function sendclarification(Request $request)
    {
        $user_id =\Auth()->user()->id;
        $nv_id = $request->nv_id;
        $user_name = $request->user_name;
        $receiver_user_id = $request->user_id;
        $service_id = $request->service_id;
        $selectedEmails = $request->emails;
        $proposal_name = $request->proposal_name;
        $file = $request->file('file') ?? null;
        if(!empty($file)){
            $filePath = $file->getClientOriginalName();
            $filePaths = public_path('clarification-file/' . $filePath);
    
            if (!File::exists($filePaths)) {
                $file->move(public_path('clarification-file/'), $filePath);
            }
        }
      
        $clarificationRemark = $request->remark;
        $clarificationId = $request->clarificationId;
        $is_replied = ($request->is_replied)?$request->is_replied:0;
        if(!is_array($selectedEmails)){
            $selectedEmails = explode(" ",$selectedEmails);
        }
        $employees = Employee::whereIn("email", $selectedEmails)->first();
       
    
        $departmentIds = explode(',', $employees->department_id);
        // dd($departmentIds);
        $departments = Department::whereIn("id", $departmentIds)->get();
       
        $service_detail = NVService::select('*')->where('nv_id', $nv_id)->orderBy('id', 'desc')->first();
        
        $nv_type = NeedValidation::where('id', $nv_id)->first();
        
        $employee = Employee::where('user_id', $user_id)->first();
        $depart = Department::where("id", $nv_type->department_id)->first();
        $ini_by =  Employee::where('user_id', $user_id)->first();
        
        // $selectedEmails = $request->emails;
 
        // Fetch user names based on email addresses
        $userNames = Employee::whereIn('email', $selectedEmails)->pluck('name', 'email');
    // dd($userNames->email);
        // Check if $nv_type and $depart are not null
        $employee_id = Employee::pluck('user_id');


        $ccuser = DB::table('tbl_service')
        ->join('nvservicestatus', 'tbl_service.nv_id', '=', 'nvservicestatus.nv_id')
        ->where('tbl_service.nv_id', '=', $nv_id)
        ->where(function($query) {
            $query->orWhere('nvservicestatus.rv1_status', [1])
                ->orWhere('nvservicestatus.rv2_status', [1])
                ->orWhere('nvservicestatus.rv3_status', [1])
                ->orWhere('nvservicestatus.rv4_status', [1])
                ->orWhere('nvservicestatus.hod_status', [1])
                ->orWhere('nvservicestatus.ces_rew1_status', [1])
                ->orWhere('nvservicestatus.ces_rew2_status', [1])
                ->orWhere('nvservicestatus.ces_rew3_status', [1])
                ->orWhere('nvservicestatus.ces_rew4_status', [1])
                ->orWhere('nvservicestatus.ces_status', [1])
                ->orWhere('nvservicestatus.approver_status', [1])
                ->orWhere('nvservicestatus.work_rew1_status', [1])
                ->orWhere('nvservicestatus.work_rew2_status', [1])
                ->orWhere('nvservicestatus.work_rew3_status', [1])
                ->orWhere('nvservicestatus.work_rew4_status', [1])
                ->orWhere('nvservicestatus.approverdep2_status', [1])
                ->orWhere('nvservicestatus.work_rew1dep2_status', [1])
                ->orWhere('nvservicestatus.work_rew2dep2_status', [1])
                ->orWhere('nvservicestatus.work_rew3dep2_status', [1])
                ->orWhere('nvservicestatus.work_rew4dep2_status', [1])
                ->orWhere('nvservicestatus.approverdep3_id', [1])
                ->orWhere('nvservicestatus.work_rew1dep3_status', [1])
                ->orWhere('nvservicestatus.work_rew2dep3_status', [1])
                ->orWhere('nvservicestatus.work_rew3dep3_status', [1])
                ->orWhere('nvservicestatus.work_rew4dep3_status', [1])
                ->orWhere('nvservicestatus.approverdep4_status', [1])
                ->orWhere('nvservicestatus.work_rew1dep4_status', [1])
                ->orWhere('nvservicestatus.work_rew2dep4_status', [1])
                ->orWhere('nvservicestatus.work_rew3dep4_status', [1])
                ->orWhere('nvservicestatus.work_rew4dep4_status', [1])
                ->orWhere('nvservicestatus.groupcio_status', [1])
                ->orWhere('nvservicestatus.ceo_status', [1]);
        })
        ->select('nvservicestatus.rv1_id', 'nvservicestatus.rv2_id', 'nvservicestatus.rv3_id',
                 'nvservicestatus.rv3_id', 'nvservicestatus.rv4_id', 'nvservicestatus.hod_id'
                 , 'nvservicestatus.ces_rew1_id', 'nvservicestatus.ces_rew2_id', 'nvservicestatus.ces_rew3_id'
                 , 'nvservicestatus.ces_rew4_id', 'nvservicestatus.ces_id'   , 'nvservicestatus.work_rew1_id', 'nvservicestatus.work_rew2_id', 'nvservicestatus.work_rew3_id'
                 , 'nvservicestatus.work_rew4_id', 'nvservicestatus.approver_id'  , 'nvservicestatus.work_rew1dep2_id', 'nvservicestatus.work_rew2dep2_id', 'nvservicestatus.work_rew3dep2_id'
                 , 'nvservicestatus.work_rew4dep2_id', 'nvservicestatus.approverdep2_id'   , 'nvservicestatus.work_rew1dep3_id', 'nvservicestatus.work_rew2dep3_id', 'nvservicestatus.work_rew3dep3_id'
                 , 'nvservicestatus.work_rew4dep3_id', 'nvservicestatus.approverdep3_id', 'nvservicestatus.work_rew1dep4_id', 
                  'nvservicestatus.work_rew2dep4_id', 'nvservicestatus.work_rew3dep4_id','nvservicestatus.work_rew4dep4_id', 'nvservicestatus.approverdep4_id'   )
                ->get();

        $employee_emails = Employee::whereIn('user_id', $employee_id)
        ->pluck('email','user_id');

        $id_email_mapping = [];

        foreach ($ccuser as $item) {
            $id_email_mapping[] = isset($employee_emails[$item->rv1_id]) ? $employee_emails[$item->rv1_id] : null;
            $id_email_mapping[] = isset($employee_emails[$item->rv2_id]) ? $employee_emails[$item->rv2_id] : null;
            $id_email_mapping[] = isset($employee_emails[$item->rv3_id]) ? $employee_emails[$item->rv3_id] : null;
            $id_email_mapping[] = isset($employee_emails[$item->rv4_id]) ? $employee_emails[$item->rv4_id] : null;
            $id_email_mapping[] = isset($employee_emails[$item->hod_id]) ? $employee_emails[$item->hod_id] : null;
            $id_email_mapping[] = isset($employee_emails[$item->ces_rew1_id]) ? $employee_emails[$item->ces_rew1_id] : null;
            $id_email_mapping[] = isset($employee_emails[$item->ces_rew2_id]) ? $employee_emails[$item->ces_rew2_id] : null;
            $id_email_mapping[] = isset($employee_emails[$item->ces_rew3_id]) ? $employee_emails[$item->ces_rew3_id] : null;
            $id_email_mapping[] = isset($employee_emails[$item->ces_rew4_id]) ? $employee_emails[$item->ces_rew4_id] : null;
            $id_email_mapping[] = isset($employee_emails[$item->ces_id]) ? $employee_emails[$item->ces_id] : null;
    
            $id_email_mapping[] = isset($employee_emails[$item->work_rew1_id]) ? $employee_emails[$item->work_rew1_id] : null;
            $id_email_mapping[] = isset($employee_emails[$item->work_rew2_id]) ? $employee_emails[$item->work_rew2_id] : null;
            $id_email_mapping[] = isset($employee_emails[$item->work_rew3_id]) ? $employee_emails[$item->work_rew3_id] : null;
            $id_email_mapping[] = isset($employee_emails[$item->work_rew4_id]) ? $employee_emails[$item->work_rew4_id] : null;
            $id_email_mapping[] = isset($employee_emails[$item->approver_id]) ? $employee_emails[$item->approver_id] : null;
    
            $id_email_mapping[] = isset($employee_emails[$item->work_rew1dep2_id]) ? $employee_emails[$item->work_rew1dep2_id] : null;
            $id_email_mapping[] = isset($employee_emails[$item->work_rew2dep2_id]) ? $employee_emails[$item->work_rew2dep2_id] : null;
            $id_email_mapping[] = isset($employee_emails[$item->work_rew3dep2_id]) ? $employee_emails[$item->work_rew3dep2_id] : null;
            $id_email_mapping[] = isset($employee_emails[$item->work_rew4dep2_id]) ? $employee_emails[$item->work_rew4dep2_id] : null;
            $id_email_mapping[] = isset($employee_emails[$item->approverdep2_id]) ? $employee_emails[$item->approverdep2_id] : null;
           
            $id_email_mapping[] = isset($employee_emails[$item->work_rew1dep3_id]) ? $employee_emails[$item->work_rew1dep3_id] : null;
            $id_email_mapping[] = isset($employee_emails[$item->work_rew2dep3_id]) ? $employee_emails[$item->work_rew2dep3_id] : null;
            $id_email_mapping[] = isset($employee_emails[$item->work_rew3dep3_id]) ? $employee_emails[$item->work_rew3dep3_id] : null;
            $id_email_mapping[] = isset($employee_emails[$item->work_rew4dep3_id]) ? $employee_emails[$item->work_rew4dep3_id] : null;
            $id_email_mapping[] = isset($employee_emails[$item->approverdep3_id]) ? $employee_emails[$item->approverdep3_id] : null;
    
            $id_email_mapping[] = isset($employee_emails[$item->work_rew1dep4_id]) ? $employee_emails[$item->work_rew1dep4_id] : null;
            $id_email_mapping[] = isset($employee_emails[$item->work_rew2dep4_id]) ? $employee_emails[$item->work_rew2dep4_id] : null;
            $id_email_mapping[] = isset($employee_emails[$item->work_rew3dep4_id]) ? $employee_emails[$item->work_rew3dep4_id] : null;
            $id_email_mapping[] = isset($employee_emails[$item->work_rew4dep4_id]) ? $employee_emails[$item->work_rew4dep4_id] : null;
            $id_email_mapping[] = isset($employee_emails[$item->approverdep4_id]) ? $employee_emails[$item->approverdep4_id] : null;
        }
        
     
        $id_email_mapping = array_values(array_filter(array_unique($id_email_mapping)));
       
            $data = [
                'remark' => $clarificationRemark,
                'selectedEmails' => $selectedEmails,
                'userNames' => $userNames,
                'nv_type' => $nv_type,
                'department' => $depart,
                'ini_by' =>  $ini_by,
                'proposal_name' =>  $proposal_name,
                
            ];
    
            try {
                
                
                foreach ($selectedEmails as $email) {
                    if($is_replied==1){
                        $subject = "Reply for {$ini_by->name } on NV no: NV/{$nv_type->budget_type}/{$nv_type->fiscal_year}/{$depart->name}" . ($nv_type->service_id == 1 ? "/Material/{$nv_type->id}" : ($nv_type->service_id == 2 ? "/Service/{$nv_type->id}" : ''));
                    }else{
                        $subject = "Clarification required from {$ini_by->name } on NV no: NV/{$nv_type->budget_type}/{$nv_type->fiscal_year}/{$depart->name}" . ($nv_type->service_id == 1 ? "/Material/{$nv_type->id}" : ($nv_type->service_id == 2 ? "/Service/{$nv_type->id}" : ''));
                    }
                    Mail::send('emailtemp.doc_mail_clarification', $data, function ($message) use ($email, $id_email_mapping, $nv_type, $depart, $ini_by, $subject, $file,$proposal_name) {
                        $message->from(env('MAIL_FROM_ADDRESS'), 'NV');
                        $message->to($email);
                        foreach ($id_email_mapping as $ccEmail) {
                            $message->cc($ccEmail);
                        }
                        $message->subject($subject);
                        if (!empty($file)) {
                            // Attach the file to the email
                            $message->attach(public_path('clarification-file/' . $file->getClientOriginalName()));
                        }
                
                    });
                }
                if($is_replied==1){
                    //dd(111);
                    $update = Clarification::find($clarificationId)->update(['is_replied'=>1,'clarification_remark_reply' => $clarificationRemark,'reply_timestamp'=>date('Y-m-d H:i:s')]);
                }else{
                    $clarification= Clarification::create([
                    'service_id' => $service_id,
                    'nv_id' => $nv_id,
                    'user_id' => $user_id ,
                    'receiver_user_id'=>$receiver_user_id,
                    'user_name' =>  $user_name,
                    'attachment' =>  $filePath ?? null,
                    'clarification_remark' => $clarificationRemark,
                    ]);
                }
           

            $response = [
                'success' => true
            ];
        } catch (\Exception $e) {
            $response = [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }

        return response()->json($response);
    }

    public function datafetch(Request $request){
        $nv_id = $request->nv_id;
        $service_id = $request->service_id;
        $material_import   =  ServiceBOQBulk::where('nv_id',$nv_id)->where('service_id',$service_id)->orderBy('id', 'desc')->get();
        $total = 0;
        $m_importAmount = 0;
        $totalvalue = $total;

        foreach ($material_import as $key => $m_import){

       
        $m_importAmount =  $m_import->amount;
     

        if (!empty($m_importAmount)){
            $total = $total + $m_importAmount;
        }else{
            $total = $total;
        }
       
        }
        // dd($total);
        $response = [
            'success' => true,
            'total' => $total,
        ];
        return response()->json($response);

    }
    public function AttachedFiles(Request $request){
        $id = $request->id;
        $service_details = NVService::where('nv_id', $id)->orderBy('id', 'desc')->first();
      
        $service_doc = ServiceDoc::where('service_id', $service_details->id)->orderBy('id', 'desc')->first();
        
        return view('admin.nvService.attachedFiles',compact('service_doc'));
    
      }
    public function deleteFile(Request $request)
    {
        $fileId = $request->input('fileId');
        $fileType = $request->input('fileType'); 
    
        $serviceDoc = ServiceDoc::find($fileId);
    
        if ($serviceDoc) {
            $filePath = '';
    
            switch ($fileType) {
                case 'cm_rate_ref':
                    $filePath = $serviceDoc->cm_rate_ref;
                    $serviceDoc->update(['cm_rate_ref' => null]);
                    break;
                case 'vendor_quat':
                    $filePath = $serviceDoc->vendor_quat;
                    $serviceDoc->update(['vendor_quat' => null]);
                    break;
                case 'last_purchase_price':
                    $filePath = $serviceDoc->last_purchase_price;
                    $serviceDoc->update(['last_purchase_price' => null]);
                    break;
                case 'user_estimation':
                    $filePath = $serviceDoc->user_estimation;
                    $serviceDoc->update(['user_estimation' => null]);
                    break;
                case 'previous_wo_rc':
                    $filePath = $serviceDoc->previous_wo_rc;
                    $serviceDoc->update(['previous_wo_rc' => null]);
                    break;
                case 'others':
                    $filePath = $serviceDoc->others;
                    $serviceDoc->update(['others' => null]);
                    break;
                case 'cost_calculation_for_service':
                    $filePath = $serviceDoc->cost_calculation_for_service;
                    $serviceDoc->update(['cost_calculation_for_service' => null]);
                    break;
                case 'past_practice':
                    $filePath = $serviceDoc->past_practice;
                    $serviceDoc->update(['past_practice' => null]);
                    break;
                case 'special_attch':
                    $filePath = $serviceDoc->special_attch;
                    $serviceDoc->update(['special_attch' => null]);
                    break;
                case 'just_prop_upload':
                    $filePath = $serviceDoc->just_prop_upload;
                    $serviceDoc->update(['just_prop_upload' => null]);
                    break;
                case 'copy_of_previous_work':
                    $filePath = $serviceDoc->copy_of_previous_work;
                    $serviceDoc->update(['copy_of_previous_work' => null]);
                    break;
                case 'copy_of_derc_other':
                    $filePath = $serviceDoc->copy_of_derc_other;
                    $serviceDoc->update(['copy_of_derc_other' => null]);
                    break;
                 case 'consuption_details':
                    $filePath = $serviceDoc->consuption_details;
                    $serviceDoc->update(['consuption_details' => null]);
                    break;
                case 'buget_stmt_for_both':
                    $filePath = $serviceDoc->buget_stmt_for_both;
                    $serviceDoc->update(['buget_stmt_for_both' => null]);
                    break;
                case 'photographs_of_product':
                    $filePath = $serviceDoc->photographs_of_product;
                    $serviceDoc->update(['photographs_of_product' => null]);
                    break;
                case 'material_procurement':
                    $filePath = $serviceDoc->material_procurement;
                    $serviceDoc->update(['material_procurement' => null]);
                    break;
                case 'vendor_quatation':
                    $filePath = $serviceDoc->vendor_quatation;
                    $serviceDoc->update(['vendor_quatation' => null]);
                    break;
                case 'vend_quatation':
                    $filePath = $serviceDoc->vend_quatation;
                    $serviceDoc->update(['vend_quatation' => null]);
                    break;
                default:
                    return response()->json(['error' => 'Invalid file type'], 400);
            }
    
            if (!empty($filePath)) {
                Storage::delete('services-doc/' . $filePath);
    
                return response()->json(['success' => true]);
            } else {
                return response()->json(['error' => 'File reference not found in the request'], 400);
            }
        }
    
        return response()->json(['error' => 'File not found'], 404);
    }

    public function removeServiceAmount(Request $request)
    {
       $nv_id= $request->input('nv_id');
       $index= $request->input('index');
       $service_id= $request->input('service_id');
     
        $nvmaterial = NVService::where('nv_id',$nv_id)->where('id',$service_id)->first();
        if ($nvmaterial) {
            $service_amount = explode(',', $nvmaterial->service_amount);
            if (isset($service_amount[$index])) {
                unset($service_amount[$index]);
                $service_amount = array_values($service_amount);
                $nvmaterial->service_amount = implode(',', $service_amount);
                NVService::where('nv_id',$nv_id)
                     ->update(['service_amount'=>$nvmaterial->service_amount]);
            }
    
            $service_description = explode(',', $nvmaterial->service_description);
            if (isset($service_description[$index])) {
                unset($service_description[$index]);
                $service_description = array_values($service_description);
                $nvmaterial->service_description = implode(',', $service_description);
                NVService::where('nv_id',$nv_id)
                     ->update(['service_description'=>$nvmaterial->service_description]);
            }
          
    
            return response()->json(['success' => true, 'message' => 'Service amount removed successfully.']);
        }
    
        return response()->json(['success' => false, 'message' => 'Service not found']);
    }

}
