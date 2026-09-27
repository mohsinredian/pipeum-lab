<?php
namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Pagination\Paginator;
use Illuminate\Pagination\LengthAwarePaginator;
use Session;
use App\Models\NVService;
use App\Models\NeedValidation;
use App\Models\Circle;
use App\Models\Department;
use App\Models\Employee;
use App\Models\Division;
use App\Models\Nvsericestatus;
use App\Models\User;
use App\Models\Workflow;
use App\Models\Ticket;
use App\Models\Vendor;
use App\Models\Location;
use App\Models\NVMaterial;
use App\Models\InventorySerialNumberMapping;
use Silber\Bouncer\Database\Role;
use App\Models\Signaturelog;
use App\Models\Service;
use DB;
use DateTime;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use PDF;
class DashboardController extends Controller {
    public function __construct() {
        $this->middleware(function ($request, $next) {
            Session::put("active", "dashboard");
            return $next($request);
        });
    } 
    public function dashboard(Request $request) {
        $fiscal_year = $request->fiscal_year;
        $company_id = $request->company_id;
        $user = \Auth::user();
        $user_id = [];
        $currentDate = Carbon::now();

        if ($currentDate->month >= 4) {
            $financialYearStart = Carbon::create($currentDate->year, 4, 1);
        } else {
            $financialYearStart = Carbon::create($currentDate->year - 1, 4, 1);
        }
        $financialYearEnd = $financialYearStart->copy()->addYear()->subDay();
        $currentFinancialYear = $financialYearStart->format('Y') . '-' . $financialYearEnd->format('y');

        $nextFinancialYearStart = $financialYearStart->copy()->addYear();
        $nextFinancialYearEnd = $nextFinancialYearStart->copy()->addYear()->subDay();
        $nextFinancialYear = $nextFinancialYearStart->format('Y') . '-' . $nextFinancialYearEnd->format('y');

        $nextToNextFinancialYearStart = $nextFinancialYearStart->copy()->addYear();
        $nextToNextFinancialYearEnd = $nextToNextFinancialYearStart->copy()->addYear()->subDay();
        $nextToNextFinancialYear = $nextToNextFinancialYearStart->format('Y') . '-' . $nextToNextFinancialYearEnd->format('y');

  if($company_id){
    $totalId = NeedValidation::where('delete_draft',0)->where('company_id',$company_id)->where('fiscal_year',$currentFinancialYear)->pluck('id');
    $latestData = Nvsericestatus::whereIn('nv_id', $totalId)->get();
  }elseif($fiscal_year){
    $totalId = NeedValidation::where('delete_draft',0)->where('fiscal_year',$fiscal_year)->pluck('id');
    $latestData = Nvsericestatus::whereIn('nv_id', $totalId)->get();
  }else{
    $totalId = NeedValidation::where('delete_draft',0)->where('fiscal_year',$currentFinancialYear)->pluck('id');
    $latestData = Nvsericestatus::whereIn('nv_id', $totalId)->get();
  }
  
    $totalAmount = DB::table('tbl_material')
       ->join('nvservicestatus', 'tbl_material.id', '=', 'nvservicestatus.material_id')
    ->whereIn('tbl_material.nv_id', $totalId)
    ->whereIn('nvservicestatus.nv_id', $latestData->pluck('nv_id'))
    ->where('nvservicestatus.ceo_status', 1)
    ->sum('tbl_material.total_budget_both') + DB::table('tbl_service')
        ->join('nvservicestatus', 'tbl_service.id', '=', 'nvservicestatus.service_id')
    ->whereIn('tbl_service.nv_id', $totalId)
    ->whereIn('nvservicestatus.nv_id', $latestData->pluck('nv_id'))
    ->where('nvservicestatus.ceo_status', 1)
    ->sum('tbl_service.total_buget');
  
      
        $pendingAmount = DB::table('tbl_material')
           ->join('nvservicestatus', 'tbl_material.id', '=', 'nvservicestatus.material_id')
        ->whereIn('tbl_material.nv_id', $totalId)
        ->whereIn('nvservicestatus.nv_id', $latestData->pluck('nv_id'))
        ->where('nvservicestatus.ceo_status', 0)
        ->whereIn('nvservicestatus.rv1_status', [0,1])
                ->whereIn('nvservicestatus.rv2_status', [0,1])
                ->whereIn('nvservicestatus.rv3_status', [0,1])
                ->whereIn('nvservicestatus.rv4_status', [0,1])
                ->whereIn('nvservicestatus.hod_status', [0,1])
                ->whereIn('nvservicestatus.ces_rew1_status', [0,1])
                ->whereIn('nvservicestatus.ces_rew2_status', [0,1])
                ->whereIn('nvservicestatus.ces_rew3_status', [0,1])
                ->whereIn('nvservicestatus.ces_rew4_status', [0,1])
                ->whereIn('nvservicestatus.ces_status', [0,1])
                ->whereIn('nvservicestatus.cpmg_status', [0,1])
                ->whereIn('nvservicestatus.work_rew1_status', [0,1])
                ->whereIn('nvservicestatus.work_rew2_status', [0,1])
                ->whereIn('nvservicestatus.work_rew3_status', [0,1])
                ->whereIn('nvservicestatus.work_rew4_status', [0,1])
                ->whereIn('nvservicestatus.cto_status', [0,1])
                ->whereIn('nvservicestatus.work_rew1dep2_status', [0,1])
                ->whereIn('nvservicestatus.work_rew2dep2_status', [0,1])
                ->whereIn('nvservicestatus.work_rew3dep2_status', [0,1])
                ->whereIn('nvservicestatus.work_rew4dep2_status', [0,1])
                ->whereIn('nvservicestatus.ceo_nominee_status', [0,1])
                ->whereIn('nvservicestatus.work_rew1dep3_status', [0,1])
                ->whereIn('nvservicestatus.work_rew2dep3_status', [0,1])
                ->whereIn('nvservicestatus.work_rew3dep3_status', [0,1])
                ->whereIn('nvservicestatus.work_rew4dep3_status', [0,1])
                ->whereIn('nvservicestatus.ceo_nominee2_status', [0,1])
                ->whereIn('nvservicestatus.work_rew1dep4_status', [0,1])
                ->whereIn('nvservicestatus.work_rew2dep4_status', [0,1])
                ->whereIn('nvservicestatus.work_rew3dep4_status', [0,1])
                ->whereIn('nvservicestatus.work_rew4dep4_status', [0,1])
                ->whereIn('nvservicestatus.groupcio_status', [0,1])
        ->sum('tbl_material.total_budget_both') + DB::table('tbl_service')
            ->join('nvservicestatus', 'tbl_service.id', '=', 'nvservicestatus.service_id')
        ->whereIn('tbl_service.nv_id', $totalId)
        ->whereIn('nvservicestatus.nv_id', $latestData->pluck('nv_id'))
        ->where('nvservicestatus.ceo_status', 0)
        ->whereIn('nvservicestatus.rv1_status', [0,1])
                ->whereIn('nvservicestatus.rv2_status', [0,1])
                ->whereIn('nvservicestatus.rv3_status', [0,1])
                ->whereIn('nvservicestatus.rv4_status', [0,1])
                ->whereIn('nvservicestatus.hod_status', [0,1])
                ->whereIn('nvservicestatus.ces_rew1_status', [0,1])
                ->whereIn('nvservicestatus.ces_rew2_status', [0,1])
                ->whereIn('nvservicestatus.ces_rew3_status', [0,1])
                ->whereIn('nvservicestatus.ces_rew4_status', [0,1])
                ->whereIn('nvservicestatus.ces_status', [0,1])
                ->whereIn('nvservicestatus.cpmg_status', [0,1])
                ->whereIn('nvservicestatus.work_rew1_status', [0,1])
                ->whereIn('nvservicestatus.work_rew2_status', [0,1])
                ->whereIn('nvservicestatus.work_rew3_status', [0,1])
                ->whereIn('nvservicestatus.work_rew4_status', [0,1])
                ->whereIn('nvservicestatus.cto_status', [0,1])
                ->whereIn('nvservicestatus.work_rew1dep2_status', [0,1])
                ->whereIn('nvservicestatus.work_rew2dep2_status', [0,1])
                ->whereIn('nvservicestatus.work_rew3dep2_status', [0,1])
                ->whereIn('nvservicestatus.work_rew4dep2_status', [0,1])
                ->whereIn('nvservicestatus.ceo_nominee_status', [0,1])
                ->whereIn('nvservicestatus.work_rew1dep3_status', [0,1])
                ->whereIn('nvservicestatus.work_rew2dep3_status', [0,1])
                ->whereIn('nvservicestatus.work_rew3dep3_status', [0,1])
                ->whereIn('nvservicestatus.work_rew4dep3_status', [0,1])
                ->whereIn('nvservicestatus.ceo_nominee2_status', [0,1])
                ->whereIn('nvservicestatus.work_rew1dep4_status', [0,1])
                ->whereIn('nvservicestatus.work_rew2dep4_status', [0,1])
                ->whereIn('nvservicestatus.work_rew3dep4_status', [0,1])
                ->whereIn('nvservicestatus.work_rew4dep4_status', [0,1])
                ->whereIn('nvservicestatus.groupcio_status', [0,1])
        ->sum('tbl_service.total_buget');

        $rejectedAmount = DB::table('tbl_material')
           ->join('nvservicestatus', 'tbl_material.id', '=', 'nvservicestatus.material_id')
        ->whereIn('tbl_material.nv_id', $totalId)
        ->whereIn('nvservicestatus.nv_id', $latestData->pluck('nv_id'))
        ->where(function ($query) {
            $query->orWhere('nvservicestatus.ceo_status', 2)
            ->orWhere('nvservicestatus.rv1_status', 2)
            ->orWhere('nvservicestatus.rv2_status', 2)
            ->orWhere('nvservicestatus.rv3_status', 2)
            ->orWhere('nvservicestatus.rv4_status', 2)
            ->orWhere('nvservicestatus.hod_status', 2)
            ->orWhere('nvservicestatus.ces_rew1_status', 2)
            ->orWhere('nvservicestatus.ces_rew2_status', 2)
            ->orWhere('nvservicestatus.ces_rew3_status', 2)
            ->orWhere('nvservicestatus.ces_rew4_status', 2)
            ->orWhere('nvservicestatus.cpmg_status', 2)
            ->orWhere('nvservicestatus.work_rew1_status', 2)
            ->orWhere('nvservicestatus.work_rew2_status', 2)
            ->orWhere('nvservicestatus.work_rew3_status', 2)
            ->orWhere('nvservicestatus.work_rew4_status', 2)
            ->orWhere('nvservicestatus.ces_status', 2)
            ->orWhere('nvservicestatus.work_rew1dep2_status', 2)
            ->orWhere('nvservicestatus.work_rew2dep2_status', 2)
            ->orWhere('nvservicestatus.work_rew3dep2_status', 2)
            ->orWhere('nvservicestatus.work_rew4dep2_status', 2)
            ->orWhere('nvservicestatus.cto_status', 2)
            ->orWhere('nvservicestatus.work_rew1dep3_status', 2)
            ->orWhere('nvservicestatus.work_rew2dep3_status', 2)
            ->orWhere('nvservicestatus.work_rew3dep3_status', 2)
            ->orWhere('nvservicestatus.work_rew4dep3_status', 2)
            ->orWhere('nvservicestatus.ceo_nominee_status', 2)
            ->orWhere('nvservicestatus.work_rew1dep4_status', 2)
            ->orWhere('nvservicestatus.work_rew2dep4_status', 2)
            ->orWhere('nvservicestatus.work_rew3dep4_status', 2)
            ->orWhere('nvservicestatus.work_rew4dep4_status', 2)
            ->orWhere('nvservicestatus.ceo_nominee2_status', 2)
            ->orWhere('nvservicestatus.groupcio_status', 2);
        })
        ->sum('tbl_material.total_budget_both') +  DB::table('tbl_service')
            ->join('nvservicestatus', 'tbl_service.id', '=', 'nvservicestatus.service_id')
        ->whereIn('tbl_service.nv_id', $totalId)
        ->whereIn('nvservicestatus.nv_id', $latestData->pluck('nv_id'))
        ->where(function ($query) {
            $query->orWhere('nvservicestatus.ceo_status', 2)
            ->orWhere('nvservicestatus.rv1_status', 2)
            ->orWhere('nvservicestatus.rv2_status', 2)
            ->orWhere('nvservicestatus.rv3_status', 2)
            ->orWhere('nvservicestatus.rv4_status', 2)
            ->orWhere('nvservicestatus.hod_status', 2)
            ->orWhere('nvservicestatus.ces_rew1_status', 2)
            ->orWhere('nvservicestatus.ces_rew2_status', 2)
            ->orWhere('nvservicestatus.ces_rew3_status', 2)
            ->orWhere('nvservicestatus.ces_rew4_status', 2)
            ->orWhere('nvservicestatus.cpmg_status', 2)
            ->orWhere('nvservicestatus.work_rew1_status', 2)
            ->orWhere('nvservicestatus.work_rew2_status', 2)
            ->orWhere('nvservicestatus.work_rew3_status', 2)
            ->orWhere('nvservicestatus.work_rew4_status', 2)
            ->orWhere('nvservicestatus.ces_status', 2)
            ->orWhere('nvservicestatus.work_rew1dep2_status', 2)
            ->orWhere('nvservicestatus.work_rew2dep2_status', 2)
            ->orWhere('nvservicestatus.work_rew3dep2_status', 2)
            ->orWhere('nvservicestatus.work_rew4dep2_status', 2)
            ->orWhere('nvservicestatus.cto_status', 2)
            ->orWhere('nvservicestatus.work_rew1dep3_status', 2)
            ->orWhere('nvservicestatus.work_rew2dep3_status', 2)
            ->orWhere('nvservicestatus.work_rew3dep3_status', 2)
            ->orWhere('nvservicestatus.work_rew4dep3_status', 2)
            ->orWhere('nvservicestatus.ceo_nominee_status', 2)
            ->orWhere('nvservicestatus.work_rew1dep4_status', 2)
            ->orWhere('nvservicestatus.work_rew2dep4_status', 2)
            ->orWhere('nvservicestatus.work_rew3dep4_status', 2)
            ->orWhere('nvservicestatus.work_rew4dep4_status', 2)
            ->orWhere('nvservicestatus.ceo_nominee2_status', 2)
            ->orWhere('nvservicestatus.groupcio_status', 2);
        })
        ->sum('tbl_service.total_buget');
   
    
        $approvedAmount = DB::table('tbl_material')
           ->join('nvservicestatus', 'tbl_material.id', '=', 'nvservicestatus.material_id')
        ->whereIn('tbl_material.nv_id', $totalId)
        ->whereIn('nvservicestatus.nv_id', $latestData->pluck('nv_id'))
        ->where('nvservicestatus.ceo_status', 1)
        ->sum('tbl_material.total_budget_both') +  DB::table('tbl_service')
            ->join('nvservicestatus', 'tbl_service.id', '=', 'nvservicestatus.service_id')
        ->whereIn('tbl_service.nv_id', $totalId)
        ->whereIn('nvservicestatus.nv_id', $latestData->pluck('nv_id'))
        ->where('nvservicestatus.ceo_status', 1)
        ->sum('tbl_service.total_buget');
 
        $departments_with_group_cio = [];
        $departments_without_group_cio = [];
        $all_departments = Department::where('status', 1)->get();

        foreach ($all_departments as $all_department) {
            if (!empty($all_department->group_cio)) {
                $departments_with_group_cio[] = $all_department->id;
            } else {
                $departments_without_group_cio[] = $all_department->id;
            }
        }

        $company = Division::select('id','name')->where('status','1')->get();
        $employees = Employee::where("user_id", $user->id)->first();
        $employeess = Employee::where("report_to", $user->id)->get();
        foreach ($employeess as $employee) {
            array_push($user_id, $employee["user_id"]);
        }
        $ceo_status = $request->approved;
        $pending_nv = $request->pendingnv;
        $total1_status = $request->total;
        $pending_status = $request->pending;
        $reject_status = $request->rejected;
        $hod_status = $request->hodStages;
        $cpmg_status = $request->cpmgStages;
        $cto_status = $request->ctoStages;
        $ceo_nominee_status = $request->ceo_nomneeStages;
        $ceo_nominee2_status = $request->ceo_nomnee2Stages;
        $ceo_status2 = $request->ceoStages;

        $currentFiscal = NeedValidation::where('fiscal_year',$currentFinancialYear)->where("delete_draft", 0)->pluck("id");


        $dept_data = Department::pluck('name');

        if ($company_id) {
            $companywise_nv = NeedValidation::where('company_id',$company_id)->where('fiscal_year',$currentFinancialYear)->where("delete_draft", 0)->pluck("id");
            $departmentNVCounts = [];
        
            foreach ($dept_data as $departmentName) {
                $department = Department::where('name', $departmentName)->first();
        
                if ($department) {
                    $count = NVService::where('dept_id', $department->id)
                        ->whereIn('nv_id', $companywise_nv)
                        ->count() +
                        NVMaterial::where('dept_id', $department->id)
                        ->whereIn('nv_id', $companywise_nv)
                        ->count();
        
                    $departmentNVCounts[$departmentName] = $count;
                }
            }
        } elseif ($fiscal_year) {
            $fiscal_totalId = NeedValidation::where('fiscal_year', $fiscal_year)
            ->where("delete_draft", 0)
            ->pluck("id");
            $departmentNVCounts = [];
        
            foreach ($dept_data as $departmentName) {
                $department = Department::where('name', $departmentName)->first();
        
                if ($department) {
                    $count = NVService::where('dept_id', $department->id)
                        ->whereIn('nv_id', $fiscal_totalId)
                        ->count() +
                        NVMaterial::where('dept_id', $department->id)
                        ->whereIn('nv_id', $fiscal_totalId)
                        ->count();
        
                    $departmentNVCounts[$departmentName] = $count;
                }
            }
        }else {
            $departmentNVCounts = [];
        
            foreach ($dept_data as $departmentName) {
                $department = Department::where('name', $departmentName)->first();
        
                if ($department) {
                    $count = NVService::where('dept_id', $department->id)->whereIn('nv_id',$currentFiscal)->count() +
                        NVMaterial::where('dept_id', $department->id)->whereIn('nv_id',$currentFiscal)->count();
        
                    $departmentNVCounts[$departmentName] = $count;
                }
            }
        }
        
        $dept_data_p = Department::paginate(12);

        $fiscal_totalId = NeedValidation::where('fiscal_year', $fiscal_year)
        ->where("delete_draft", 0)
        ->pluck("id");
      $companywise_nv = NeedValidation::where('company_id',$company_id)->where("delete_draft", 0)->pluck("id");

      if($company_id){
        $hodApproval = Nvsericestatus::where("hod_status", 0)->where('company_id',$company_id)->whereIn('nv_id',$currentFiscal)->count();
    }elseif($fiscal_year){
        $hodApproval = Nvsericestatus::where("hod_status", 0)->whereIn('nv_id',$fiscal_totalId)->count();
    }else{
        $hodApproval = Nvsericestatus::where("hod_status", 0)->whereIn('nv_id',$currentFiscal)->count();
    }
    if($company_id){
        $cesApproval = Nvsericestatus::where("hod_status", 1)->where("ces_status", 0)->where("derc_info", 1)->where('company_id',$company_id)->whereIn('nv_id',$currentFiscal)->count();
     
    }elseif($fiscal_year){
        $cesApproval = Nvsericestatus::where("hod_status", 1)->where("ces_status", 0)->where("derc_info", 1)->whereIn('nv_id',$fiscal_totalId)->count();
    }else{
        $cesApproval = Nvsericestatus::where("hod_status", 1)->where("ces_status", 0)->where("derc_info", 1)->whereIn('nv_id',$currentFiscal)->count();
    }
    if($company_id){
        $cpmg = Nvsericestatus::where('company_id',$company_id)->where('hod_status', 1)->where('cpmg_status', 0)->whereIn('nv_id',$currentFiscal)->get();
        $Pendingcpmg = $cpmg->filter(function ($data) {
            return in_array($data->hod_status, [1]) &&
                in_array($data->cpmg_status, [0]) &&
                (
                    (in_array($data->derc_info, [0]) && in_array($data->ces_status, [0]))
                    ||
                    (in_array($data->derc_info, [1]) && in_array($data->ces_status, [1]))
                );
        });
        $cpmgApproval = $Pendingcpmg->count();
           
    }   elseif($fiscal_year){
        $cpmg = Nvsericestatus::where('hod_status', 1)->where('cpmg_status', 0)->whereIn('nv_id',$fiscal_totalId)->get();
        $Pendingcpmg = $cpmg->filter(function ($data) {
            return in_array($data->hod_status, [1]) &&
                in_array($data->cpmg_status, [0]) &&
                (
                    (in_array($data->derc_info, [0]) && in_array($data->ces_status, [0]))
                    ||
                    (in_array($data->derc_info, [1]) && in_array($data->ces_status, [1]))
                );
        });
        $cpmgApproval = $Pendingcpmg->count();
           
    }else {
        $cpmg = Nvsericestatus::where('hod_status', 1)->where('cpmg_status', 0)->whereIn('nv_id',$currentFiscal)->get();
        $Pendingcpmg = $cpmg->filter(function ($data) {
         return in_array($data->hod_status, [1]) &&
             in_array($data->cpmg_status, [0]) &&
             (
                 (in_array($data->derc_info, [0]) && in_array($data->ces_status, [0]))
                 ||
                 (in_array($data->derc_info, [1]) && in_array($data->ces_status, [1]))
             );
     });
     $cpmgApproval = $Pendingcpmg->count();
    }
    if($company_id){
            $btApproval = Nvsericestatus::where("cpmg_status", 1)->where("check_technology", 1)->where('cto_status', 0)->where('company_id',$company_id)->whereIn('nv_id',$currentFiscal)->count();
        }  elseif($fiscal_year){
            $btApproval = Nvsericestatus::where("cpmg_status", 1)->where("check_technology", 1)->where('cto_status', 0)->whereIn('nv_id',$fiscal_totalId)->count();
        }else {
            $btApproval = Nvsericestatus::where("cpmg_status", 1)->where("check_technology", 1)->where('cto_status', 0)->whereIn('nv_id',$currentFiscal)->count();
        }
    if($company_id){
        $ceonominee1 = Nvsericestatus::where('company_id',$company_id)->whereIn('nv_id',$currentFiscal)->where('cpmg_status', 1)->where('ceo_nominee_status', 0)->get();
        $Pendingnominee1 = $ceonominee1->filter(function ($data) {
        return in_array($data->cpmg_status, [1]) &&
            in_array($data->ceo_nominee_status, [0]) &&
            (
                (in_array($data->check_technology, [0]) && in_array($data->cto_status, [0]))
                ||
                (in_array($data->check_technology, [1]) && in_array($data->cto_status, [1]))
            );
    });
    $ceonominee1Approval = $Pendingnominee1->count();
    } elseif($fiscal_year){
        $ceonominee1 = Nvsericestatus::whereIn('nv_id',$fiscal_totalId)->where('cpmg_status', 1)->where('ceo_nominee_status', 0)->get();
        $Pendingnominee1 = $ceonominee1->filter(function ($data) {
        return in_array($data->cpmg_status, [1]) &&
            in_array($data->ceo_nominee_status, [0]) &&
            (
                (in_array($data->check_technology, [0]) && in_array($data->cto_status, [0]))
                ||
                (in_array($data->check_technology, [1]) && in_array($data->cto_status, [1]))
            );
    });
    $ceonominee1Approval = $Pendingnominee1->count();
    } else {
        $ceonominee1 = Nvsericestatus::where('cpmg_status', 1)->where('ceo_nominee_status', 0)->whereIn('nv_id',$currentFiscal)->get();
        $Pendingnominee1 = $ceonominee1->filter(function ($data) {
        return in_array($data->cpmg_status, [1]) &&
            in_array($data->ceo_nominee_status, [0]) &&
            (
                (in_array($data->check_technology, [0]) && in_array($data->cto_status, [0]))
                ||
                (in_array($data->check_technology, [1]) && in_array($data->cto_status, [1]))
            );
    });
    $ceonominee1Approval = $Pendingnominee1->count();
    }
    if (!empty($departments_with_group_cio)) {
        $withGH = Nvsericestatus::join('needvalidations','needvalidations.id','=','nvservicestatus.nv_id')
        ->join('department','department.id','=','needvalidations.department_id')
        ->whereIn('department.id',$departments_with_group_cio)
        ->where('nvservicestatus.groupcio_status',1)->where('nvservicestatus.ceo_nominee2_status', 0);
        
        if($company_id){
        $withGH = $withGH->where('nvservicestatus.company_id',$company_id)->whereIn('nvservicestatus.nv_id',$currentFiscal)->count();

        }elseif($fiscal_year){
            $withGH = $withGH->whereIn('nvservicestatus.nv_id',$fiscal_totalId)->count();
        }else{
            $withGH = $withGH->whereIn('nvservicestatus.nv_id',$currentFiscal)->count();
        }
    }

        if (!empty($departments_without_group_cio)) {
            $WithoutGH = Nvsericestatus::join('needvalidations','needvalidations.id','=','nvservicestatus.nv_id')
            ->join('department','department.id','=','needvalidations.department_id')
            ->whereIn('department.id',$departments_without_group_cio)
            ->where('nvservicestatus.ceo_nominee_status',1)->where('nvservicestatus.ceo_nominee2_status', 0);
           
            if($company_id){
            $WithoutGH = $WithoutGH->where('nvservicestatus.company_id',$company_id)->whereIn('nvservicestatus.nv_id',$currentFiscal)->count();

            }elseif($fiscal_year){
                $WithoutGH = $WithoutGH->whereIn('nvservicestatus.nv_id',$fiscal_totalId)->count();
            }else{
                $WithoutGH = $WithoutGH->whereIn('nvservicestatus.nv_id',$currentFiscal)->count();
            }
        }
            $ceonominee2Approval = ($withGH + $WithoutGH ) ?? 0;

if (!empty($departments_with_group_cio)) {
    $withGH = Nvsericestatus::join('needvalidations','needvalidations.id','=','nvservicestatus.nv_id')
    ->join('department','department.id','=','needvalidations.department_id')
    ->whereIn('department.id',$departments_with_group_cio)
    ->where('nvservicestatus.ceo_nominee_status',1)->where('nvservicestatus.ceo_nominee2_status', 0)->where('groupcio_status', 0);
    
    if($company_id){
    $withGH = $withGH->where('nvservicestatus.company_id',$company_id)->whereIn('nvservicestatus.nv_id',$currentFiscal)->count();

    }elseif($fiscal_year){
        $withGH = $withGH->whereIn('nvservicestatus.nv_id',$fiscal_totalId)->count();
    }else{
        $withGH = $withGH->whereIn('nvservicestatus.nv_id',$currentFiscal)->count();
    }
}
$groupcioApproval = $withGH ?? 0;
     if($company_id){
      
            $ceoApproval = Nvsericestatus::where('ceo_nominee2_status', 1)->where('ceo_status', 0)->where('company_id',$company_id)->whereIn('nv_id',$currentFiscal)->count();
           
    } elseif($fiscal_year){
  
            $ceoApproval = Nvsericestatus::where('ceo_nominee2_status', 1)->where('ceo_status', 0)->whereIn('nv_id',$fiscal_totalId)->count();
         
    }else{
            $ceoApproval = Nvsericestatus::where('ceo_nominee2_status', 1)->where('ceo_status', 0)->whereIn('nv_id',$currentFiscal)->count();
      
    }

        if (!empty($user->role_id == 1)) {
           $user = \Auth::user()->id;
                  
                    
                   
            if($company_id){
                $approvedNV = Nvsericestatus::where("ceo_status", 1)->whereIn('nv_id',$companywise_nv)->whereIn('nv_id',$currentFiscal)->count();
           
            } 
            elseif($fiscal_year){
                $approvedNV = Nvsericestatus::where("ceo_status", 1)->whereIn('nv_id',$fiscal_totalId)->count();
           
            } else {
                $approvedNV = Nvsericestatus::where("ceo_status", 1)->whereIn('nv_id',$currentFiscal)->count();
            }
       
         
            if($company_id){    
                $totalNV=Nvsericestatus::whereIn('nv_id',$companywise_nv)->whereIn('nv_id',$currentFiscal)->count();
                
            } elseif($fiscal_year){
                $totalNV=Nvsericestatus::whereIn('nv_id',$fiscal_totalId)->count();
           
            }else {
                $totalNV=Nvsericestatus::whereIn('nv_id',$currentFiscal)->count();
            }
            if($company_id){
                $rejectedNV = $latestData->filter(function ($data) {
                    return in_array($data->ceo_status, [2]) ||
                    $data->rv1_status == 2 ||
                    $data->rv2_status == 2 ||
                    $data->rv3_status == 2 ||
                    $data->rv4_status == 2 ||
                    $data->hod_status == 2 ||
                    $data->ces_rew1_status == 2 ||
                    $data->ces_rew2_status == 2 ||
                    $data->ces_rew3_status == 2 ||
                    $data->ces_rew4_status == 2 ||
                    $data->ces_status == 2 ||
                    $data->work_rew1_status == 2 ||
                    $data->work_rew2_status == 2 ||
                    $data->work_rew3_status == 2 ||
                    $data->work_rew4_status == 2 ||
                    $data->cpmg_status == 2 ||
                    $data->work_rew1dep2_status == 2 ||
                    $data->work_rew2dep2_status == 2 ||
                    $data->work_rew3dep2_status == 2 ||
                    $data->work_rew4dep2_status == 2 ||
                    $data->cto_status == 2 ||
                    $data->work_rew1dep3_status == 2 ||
                    $data->work_rew2dep3_status == 2 ||
                    $data->work_rew3dep3_status == 2 ||
                    $data->work_rew4dep3_status == 2 ||
                    $data->ceo_nominee_status == 2 ||
                    $data->work_rew1dep4_status == 2 ||
                    $data->work_rew2dep4_status == 2 ||
                    $data->work_rew3dep4_status == 2 ||
                    $data->work_rew4dep4_status == 2 ||
                    $data->ceo_nominee2_status == 2 ||
                    $data->groupcio_status == 2;
                })->whereIn("nv_id",$companywise_nv)->whereIn('nv_id',$currentFiscal)->count();
                
           
                }elseif($fiscal_year){
                    $rejectedNV = $latestData->filter(function ($data) {
                        return in_array($data->ceo_status, [2]) ||
                        $data->rv1_status == 2 ||
                        $data->rv2_status == 2 ||
                        $data->rv3_status == 2 ||
                        $data->rv4_status == 2 ||
                        $data->hod_status == 2 ||
                        $data->ces_rew1_status == 2 ||
                        $data->ces_rew2_status == 2 ||
                        $data->ces_rew3_status == 2 ||
                        $data->ces_rew4_status == 2 ||
                        $data->ces_status == 2 ||
                        $data->work_rew1_status == 2 ||
                        $data->work_rew2_status == 2 ||
                        $data->work_rew3_status == 2 ||
                        $data->work_rew4_status == 2 ||
                        $data->cpmg_status == 2 ||
                        $data->work_rew1dep2_status == 2 ||
                        $data->work_rew2dep2_status == 2 ||
                        $data->work_rew3dep2_status == 2 ||
                        $data->work_rew4dep2_status == 2 ||
                        $data->cto_status == 2 ||
                        $data->work_rew1dep3_status == 2 ||
                        $data->work_rew2dep3_status == 2 ||
                        $data->work_rew3dep3_status == 2 ||
                        $data->work_rew4dep3_status == 2 ||
                        $data->ceo_nominee_status == 2 ||
                        $data->work_rew1dep4_status == 2 ||
                        $data->work_rew2dep4_status == 2 ||
                        $data->work_rew3dep4_status == 2 ||
                        $data->work_rew4dep4_status == 2 ||
                        $data->ceo_nominee2_status == 2 ||
                        $data->groupcio_status == 2;
                    })->whereIn('nv_id',$fiscal_totalId)->count();
               
                } else {
                    $rejectedNV = $latestData->filter(function ($data) {
                        return in_array($data->ceo_status, [2]) ||
                        $data->rv1_status == 2 ||
                        $data->rv2_status == 2 ||
                        $data->rv3_status == 2 ||
                        $data->rv4_status == 2 ||
                        $data->hod_status == 2 ||
                        $data->ces_rew1_status == 2 ||
                        $data->ces_rew2_status == 2 ||
                        $data->ces_rew3_status == 2 ||
                        $data->ces_rew4_status == 2 ||
                        $data->ces_status == 2 ||
                        $data->work_rew1_status == 2 ||
                        $data->work_rew2_status == 2 ||
                        $data->work_rew3_status == 2 ||
                        $data->work_rew4_status == 2 ||
                        $data->cpmg_status == 2 ||
                        $data->work_rew1dep2_status == 2 ||
                        $data->work_rew2dep2_status == 2 ||
                        $data->work_rew3dep2_status == 2 ||
                        $data->work_rew4dep2_status == 2 ||
                        $data->cto_status == 2 ||
                        $data->work_rew1dep3_status == 2 ||
                        $data->work_rew2dep3_status == 2 ||
                        $data->work_rew3dep3_status == 2 ||
                        $data->work_rew4dep3_status == 2 ||
                        $data->ceo_nominee_status == 2 ||
                        $data->work_rew1dep4_status == 2 ||
                        $data->work_rew2dep4_status == 2 ||
                        $data->work_rew3dep4_status == 2 ||
                        $data->work_rew4dep4_status == 2 ||
                        $data->ceo_nominee2_status == 2 ||
                        $data->groupcio_status == 2;
                    })->whereIn('nv_id',$currentFiscal)->count();
                
                 
            }
            $totalId = NeedValidation::where('delete_draft',0)->pluck('id');
            $fiscal_totalId = NeedValidation::where('fiscal_year', $fiscal_year)
            ->where("delete_draft", 0)
            ->pluck("id");
           
            if($company_id){
                $latestData = Nvsericestatus::whereIn('nv_id',$companywise_nv)->whereIn('nv_id',$currentFiscal)->get();
            }elseif($fiscal_year){
                $latestData = Nvsericestatus::whereIn('nv_id', $fiscal_totalId)->get();
            }else {
                $latestData = Nvsericestatus::whereIn("nv_id", $totalId)->whereIn('nv_id',$currentFiscal)->get();
            }
            $pendingNV = $latestData->filter(function ($data) {
                return in_array($data->ceo_status, [0])
                && in_array($data->rv1_status, [0,1])
                && in_array($data->rv2_status, [0,1])
                && in_array($data->rv3_status, [0,1])
                && in_array($data->rv4_status, [0,1])
                && in_array($data->hod_status, [0, 1])
                && in_array($data->ces_rew1_status, [0,1])
                && in_array($data->ces_rew2_status, [0,1])
                && in_array($data->ces_rew3_status, [0,1])
                && in_array($data->ces_rew4_status, [0,1])
                && in_array($data->cpmg_status, [0, 1])
                && in_array($data->work_rew1_status, [0,1])
                && in_array($data->work_rew2_status, [0,1])
                && in_array($data->work_rew3_status, [0,1])
                && in_array($data->work_rew4_status, [0,1])
                && in_array($data->ces_status, [0, 1])
                && in_array($data->work_rew1dep2_status, [0,1])
                && in_array($data->work_rew2dep2_status, [0,1])
                && in_array($data->work_rew3dep2_status, [0,1])
                && in_array($data->work_rew4dep2_status, [0,1])
                && in_array($data->cto_status, [0, 1])
                && in_array($data->work_rew1dep3_status, [0,1])
                && in_array($data->work_rew2dep3_status, [0,1])
                && in_array($data->work_rew3dep3_status, [0,1])
                && in_array($data->work_rew4dep3_status, [0,1])
                && in_array($data->ceo_nominee_status, [0, 1])
                && in_array($data->work_rew1dep4_status, [0,1])
                && in_array($data->work_rew2dep4_status, [0,1])
                && in_array($data->work_rew3dep4_status, [0,1])
                && in_array($data->work_rew4dep4_status, [0,1])
                && in_array($data->ceo_nominee2_status, [0, 1])
                && in_array($data->groupcio_status, [0, 1]) ;
            })->count();
       
             if($company_id){
                    $totalNV = Nvsericestatus::whereIn('nv_id',$companywise_nv)->whereIn('nv_id',$currentFiscal)->where('ceo_status', 1)->count();
                   
                } elseif($fiscal_year) {
                    $totalNV = Nvsericestatus::whereIn("nv_id", $fiscal_totalId)->where('ceo_status', 1)->count();
                }else {
                    $totalNV = Nvsericestatus::whereIn("nv_id", $totalId)->whereIn('nv_id',$currentFiscal)->where('ceo_status', 1)->count();
                }
       
           
           
            $nvIds = NeedValidation::where('delete_draft',0)->pluck("id");
            if($company_id){
                $nv_sm_data = Nvsericestatus::whereIn('nv_id',$companywise_nv)->whereIn('nv_id',$currentFiscal)->with(['service', 'material', 'user'])->orderBy('id', 'asc');
               
            }elseif($fiscal_year){
                $nv_sm_data = Nvsericestatus::whereIn('nv_id',$fiscal_totalId)->with(['service', 'material', 'user'])->orderBy('id', 'asc');
               
            } else {
                $nv_sm_data = Nvsericestatus::whereIn('nv_id', $nvIds)->whereIn('nv_id',$currentFiscal)->with(['service', 'material', 'user'])->orderBy('id', 'asc');
               
            }
            if ($ceo_status !== null) {
                $nv_sm_data = $nv_sm_data->where('ceo_status', $ceo_status);
            }  elseif ($pending_nv !== null) {
                $nv_sm_data = $nv_sm_data->where('ceo_status','!=', $pending_nv);
            }
             elseif ($pending_status !== null) {
                $nv_sm_data = $nv_sm_data->where('ceo_status', $pending_status)
                ->whereIn('rv1_status', [0,1])
                ->whereIn('rv2_status', [0,1])
                ->whereIn('rv3_status', [0,1])
                ->whereIn('rv4_status', [0,1])
                ->whereIn('hod_status', [0,1])
                ->whereIn('ces_rew1_status', [0,1])
                ->whereIn('ces_rew2_status', [0,1])
                ->whereIn('ces_rew3_status', [0,1])
                ->whereIn('ces_rew4_status', [0,1])
                ->whereIn('ces_status', [0,1])
                ->whereIn('cpmg_status', [0,1])
                ->whereIn('work_rew1_status', [0,1])
                ->whereIn('work_rew2_status', [0,1])
                ->whereIn('work_rew3_status', [0,1])
                ->whereIn('work_rew4_status', [0,1])
                ->whereIn('cto_status', [0,1])
                ->whereIn('work_rew1dep2_status', [0,1])
                ->whereIn('work_rew2dep2_status', [0,1])
                ->whereIn('work_rew3dep2_status', [0,1])
                ->whereIn('work_rew4dep2_status', [0,1])
                ->whereIn('ceo_nominee_status', [0,1])
                ->whereIn('work_rew1dep3_status', [0,1])
                ->whereIn('work_rew2dep3_status', [0,1])
                ->whereIn('work_rew3dep3_status', [0,1])
                ->whereIn('work_rew4dep3_status', [0,1])
                ->whereIn('ceo_nominee2_status', [0,1])
                ->whereIn('work_rew1dep4_status', [0,1])
                ->whereIn('work_rew2dep4_status', [0,1])
                ->whereIn('work_rew3dep4_status', [0,1])
                ->whereIn('work_rew4dep4_status', [0,1])
                ->whereIn('groupcio_status', [0,1]);
            } elseif ($reject_status !== null) {
                $nv_sm_data = $nv_sm_data->where('ceo_status', $reject_status)
                ->orWhere('rv1_status', 2)
                ->orWhere('rv2_status', 2)
                ->orWhere('rv3_status', 2)
                ->orWhere('rv4_status', 2)
                ->orWhere('hod_status', 2)
                ->orWhere('ces_rew1_status', 2)
                ->orWhere('ces_rew2_status', 2)
                ->orWhere('ces_rew3_status', 2)
                ->orWhere('ces_rew4_status', 2)
                ->orWhere('cpmg_status', 2)
                ->orWhere('work_rew1_status', 2)
                ->orWhere('work_rew2_status', 2)
                ->orWhere('work_rew3_status', 2)
                ->orWhere('work_rew4_status', 2)
                ->orWhere('ces_status', 2)
                ->orWhere('work_rew1dep2_status', 2)
                ->orWhere('work_rew2dep2_status', 2)
                ->orWhere('work_rew3dep2_status', 2)
                ->orWhere('work_rew4dep2_status', 2)
                ->orWhere('cto_status', 2)
                ->orWhere('work_rew1dep3_status', 2)
                ->orWhere('work_rew2dep3_status', 2)
                ->orWhere('work_rew3dep3_status', 2)
                ->orWhere('work_rew4dep3_status', 2)
                ->orWhere('ceo_nominee_status', 2)
                ->orWhere('work_rew1dep4_status', 2)
                ->orWhere('work_rew2dep4_status', 2)
                ->orWhere('work_rew3dep4_status', 2)
                ->orWhere('work_rew4dep4_status', 2)
                ->orWhere('ceo_nominee2_status', 2)
                ->orWhere('groupcio_status', 2);
            
            }elseif ($total1_status !== null) {
                $nv_sm_data = $nv_sm_data->where('ceo_status', $total1_status);
                
            }
            elseif ($hod_status !== null) {
                $nv_sm_data = $nv_sm_data->where('hod_status', $hod_status);
                 
                   
            }elseif ($cpmg_status !== null) {
                $nv_sm_data = $nv_sm_data->where('cpmg_status', $cpmg_status)
                    ->whereIn('hod_status', [1]);
                   
            }elseif ($cto_status !== null) {
                $nv_sm_data = $nv_sm_data->where('cto_status', $cto_status)
                    ->whereIn('hod_status', [1])
                    ->whereIn('check_technology', [1])
                    ->whereIn('cpmg_status', [1]);
                   
            }elseif ($ceo_nominee_status !== null) {
                $nv_sm_data = $nv_sm_data->where('ceo_nominee_status', $ceo_nominee_status)
                    ->whereIn('hod_status', [1])
                    ->whereIn('check_technology', [1])
                    ->whereIn('check_technology', [1])
                    ->whereIn('cpmg_status', [1]);
                 
            }elseif ($ceo_nominee2_status !== null) {
                $nv_sm_data = $nv_sm_data->where('ceo_nominee2_status', $ceo_nominee2_status)
                    ->whereIn('hod_status', [1])
                    ->whereIn('check_technology', [1])
                    ->whereIn('check_technology', [1])
                    ->whereIn('cpmg_status', [1])
                    ->whereIn('ceo_nominee_status', [1]);
                 
            }elseif ($ceo_status2 !== null) {
                $nv_sm_data = $nv_sm_data->where('ceo_status', $ceo_status2)
                   
                ->whereIn('hod_status', [1])
                ->whereIn('check_technology', [1])
                ->whereIn('ceo_nominee_status', [1])
                ->whereIn('ceo_nominee2_status', [1])
                ->whereIn('cpmg_status', [1]);
                 
            }

            $nv_sm_data = $nv_sm_data->get();

            if($fiscal_year){
                $BRPLnv = NeedValidation::where('company_id','6')->where('fiscal_year', $fiscal_year)
                ->where("delete_draft", 0)
                ->pluck("id");

            }else{
                $BRPLnv = NeedValidation::where('company_id','6')->where('fiscal_year',$currentFinancialYear)->where("delete_draft", 0)->pluck("id");

            }


            $pendingAmountBRPL = DB::table('tbl_material')
               ->join('nvservicestatus', 'tbl_material.id', '=', 'nvservicestatus.material_id')
            ->whereIn('tbl_material.nv_id', $BRPLnv)
            ->where('nvservicestatus.ceo_status', 0)
                 ->whereIn('nvservicestatus.rv1_status', [0,1])
                 ->whereIn('nvservicestatus.rv2_status', [0,1])
                 ->whereIn('nvservicestatus.rv3_status', [0,1])
                 ->whereIn('nvservicestatus.rv4_status', [0,1])
                 ->whereIn('nvservicestatus.hod_status', [0,1])
                 ->whereIn('nvservicestatus.ces_rew1_status', [0,1])
                 ->whereIn('nvservicestatus.ces_rew2_status', [0,1])
                 ->whereIn('nvservicestatus.ces_rew3_status', [0,1])
                 ->whereIn('nvservicestatus.ces_rew4_status', [0,1])
                 ->whereIn('nvservicestatus.ces_status', [0,1])
                 ->whereIn('nvservicestatus.cpmg_status', [0,1])
                 ->whereIn('nvservicestatus.work_rew1_status', [0,1])
                 ->whereIn('nvservicestatus.work_rew2_status', [0,1])
                 ->whereIn('nvservicestatus.work_rew3_status', [0,1])
                 ->whereIn('nvservicestatus.work_rew4_status', [0,1])
                 ->whereIn('nvservicestatus.cto_status', [0,1])
                 ->whereIn('nvservicestatus.work_rew1dep2_status', [0,1])
                 ->whereIn('nvservicestatus.work_rew2dep2_status', [0,1])
                 ->whereIn('nvservicestatus.work_rew3dep2_status', [0,1])
                 ->whereIn('nvservicestatus.work_rew4dep2_status', [0,1])
                 ->whereIn('nvservicestatus.ceo_nominee_status', [0,1])
                 ->whereIn('nvservicestatus.work_rew1dep3_status', [0,1])
                 ->whereIn('nvservicestatus.work_rew2dep3_status', [0,1])
                 ->whereIn('nvservicestatus.work_rew3dep3_status', [0,1])
                 ->whereIn('nvservicestatus.work_rew4dep3_status', [0,1])
                 ->whereIn('nvservicestatus.ceo_nominee2_status', [0,1])
                 ->whereIn('nvservicestatus.work_rew1dep4_status', [0,1])
                 ->whereIn('nvservicestatus.work_rew2dep4_status', [0,1])
                 ->whereIn('nvservicestatus.work_rew3dep4_status', [0,1])
                 ->whereIn('nvservicestatus.work_rew4dep4_status', [0,1])
                 ->whereIn('nvservicestatus.groupcio_status', [0,1])
            ->sum('tbl_material.total_budget_both')+DB::table('tbl_service')
                ->join('nvservicestatus', 'tbl_service.id', '=', 'nvservicestatus.service_id')
            ->whereIn('tbl_service.nv_id', $BRPLnv)
            ->where('nvservicestatus.ceo_status', 0)
                 ->whereIn('nvservicestatus.rv1_status', [0,1])
                 ->whereIn('nvservicestatus.rv2_status', [0,1])
                 ->whereIn('nvservicestatus.rv3_status', [0,1])
                 ->whereIn('nvservicestatus.rv4_status', [0,1])
                 ->whereIn('nvservicestatus.hod_status', [0,1])
                 ->whereIn('nvservicestatus.ces_rew1_status', [0,1])
                 ->whereIn('nvservicestatus.ces_rew2_status', [0,1])
                 ->whereIn('nvservicestatus.ces_rew3_status', [0,1])
                 ->whereIn('nvservicestatus.ces_rew4_status', [0,1])
                 ->whereIn('nvservicestatus.ces_status', [0,1])
                 ->whereIn('nvservicestatus.cpmg_status', [0,1])
                 ->whereIn('nvservicestatus.work_rew1_status', [0,1])
                 ->whereIn('nvservicestatus.work_rew2_status', [0,1])
                 ->whereIn('nvservicestatus.work_rew3_status', [0,1])
                 ->whereIn('nvservicestatus.work_rew4_status', [0,1])
                 ->whereIn('nvservicestatus.cto_status', [0,1])
                 ->whereIn('nvservicestatus.work_rew1dep2_status', [0,1])
                 ->whereIn('nvservicestatus.work_rew2dep2_status', [0,1])
                 ->whereIn('nvservicestatus.work_rew3dep2_status', [0,1])
                 ->whereIn('nvservicestatus.work_rew4dep2_status', [0,1])
                 ->whereIn('nvservicestatus.ceo_nominee_status', [0,1])
                 ->whereIn('nvservicestatus.work_rew1dep3_status', [0,1])
                 ->whereIn('nvservicestatus.work_rew2dep3_status', [0,1])
                 ->whereIn('nvservicestatus.work_rew3dep3_status', [0,1])
                 ->whereIn('nvservicestatus.work_rew4dep3_status', [0,1])
                 ->whereIn('nvservicestatus.ceo_nominee2_status', [0,1])
                 ->whereIn('nvservicestatus.work_rew1dep4_status', [0,1])
                 ->whereIn('nvservicestatus.work_rew2dep4_status', [0,1])
                 ->whereIn('nvservicestatus.work_rew3dep4_status', [0,1])
                 ->whereIn('nvservicestatus.work_rew4dep4_status', [0,1])
                 ->whereIn('nvservicestatus.groupcio_status', [0,1])
            ->sum('tbl_service.total_buget');
         
   
            $rejectedAmountBRPL = DB::table('tbl_material')
               ->join('nvservicestatus', 'tbl_material.id', '=', 'nvservicestatus.material_id')
            ->whereIn('tbl_material.nv_id', $BRPLnv)
            ->where(function ($query) {
                $query->orWhere('nvservicestatus.ceo_status', 2)
                ->orWhere('nvservicestatus.rv1_status', 2)
                ->orWhere('nvservicestatus.rv2_status', 2)
                ->orWhere('nvservicestatus.rv3_status', 2)
                ->orWhere('nvservicestatus.rv4_status', 2)
                ->orWhere('nvservicestatus.hod_status', 2)
                ->orWhere('nvservicestatus.ces_rew1_status', 2)
                ->orWhere('nvservicestatus.ces_rew2_status', 2)
                ->orWhere('nvservicestatus.ces_rew3_status', 2)
                ->orWhere('nvservicestatus.ces_rew4_status', 2)
                ->orWhere('nvservicestatus.cpmg_status', 2)
                ->orWhere('nvservicestatus.work_rew1_status', 2)
                ->orWhere('nvservicestatus.work_rew2_status', 2)
                ->orWhere('nvservicestatus.work_rew3_status', 2)
                ->orWhere('nvservicestatus.work_rew4_status', 2)
                ->orWhere('nvservicestatus.ces_status', 2)
                ->orWhere('nvservicestatus.work_rew1dep2_status', 2)
                ->orWhere('nvservicestatus.work_rew2dep2_status', 2)
                ->orWhere('nvservicestatus.work_rew3dep2_status', 2)
                ->orWhere('nvservicestatus.work_rew4dep2_status', 2)
                ->orWhere('nvservicestatus.cto_status', 2)
                ->orWhere('nvservicestatus.work_rew1dep3_status', 2)
                ->orWhere('nvservicestatus.work_rew2dep3_status', 2)
                ->orWhere('nvservicestatus.work_rew3dep3_status', 2)
                ->orWhere('nvservicestatus.work_rew4dep3_status', 2)
                ->orWhere('nvservicestatus.ceo_nominee_status', 2)
                ->orWhere('nvservicestatus.work_rew1dep4_status', 2)
                ->orWhere('nvservicestatus.work_rew2dep4_status', 2)
                ->orWhere('nvservicestatus.work_rew3dep4_status', 2)
                ->orWhere('nvservicestatus.work_rew4dep4_status', 2)
                ->orWhere('nvservicestatus.ceo_nominee2_status', 2)
                ->orWhere('nvservicestatus.groupcio_status', 2);
            })
            ->sum('tbl_material.total_budget_both')+DB::table('tbl_service')
                ->join('nvservicestatus', 'tbl_service.id', '=', 'nvservicestatus.service_id')
            ->whereIn('tbl_service.nv_id', $BRPLnv)
            ->where(function ($query) {
                $query->orWhere('nvservicestatus.ceo_status', 2)
                ->orWhere('nvservicestatus.rv1_status', 2)
                ->orWhere('nvservicestatus.rv2_status', 2)
                ->orWhere('nvservicestatus.rv3_status', 2)
                ->orWhere('nvservicestatus.rv4_status', 2)
                ->orWhere('nvservicestatus.hod_status', 2)
                ->orWhere('nvservicestatus.ces_rew1_status', 2)
                ->orWhere('nvservicestatus.ces_rew2_status', 2)
                ->orWhere('nvservicestatus.ces_rew3_status', 2)
                ->orWhere('nvservicestatus.ces_rew4_status', 2)
                ->orWhere('nvservicestatus.cpmg_status', 2)
                ->orWhere('nvservicestatus.work_rew1_status', 2)
                ->orWhere('nvservicestatus.work_rew2_status', 2)
                ->orWhere('nvservicestatus.work_rew3_status', 2)
                ->orWhere('nvservicestatus.work_rew4_status', 2)
                ->orWhere('nvservicestatus.ces_status', 2)
                ->orWhere('nvservicestatus.work_rew1dep2_status', 2)
                ->orWhere('nvservicestatus.work_rew2dep2_status', 2)
                ->orWhere('nvservicestatus.work_rew3dep2_status', 2)
                ->orWhere('nvservicestatus.work_rew4dep2_status', 2)
                ->orWhere('nvservicestatus.cto_status', 2)
                ->orWhere('nvservicestatus.work_rew1dep3_status', 2)
                ->orWhere('nvservicestatus.work_rew2dep3_status', 2)
                ->orWhere('nvservicestatus.work_rew3dep3_status', 2)
                ->orWhere('nvservicestatus.work_rew4dep3_status', 2)
                ->orWhere('nvservicestatus.ceo_nominee_status', 2)
                ->orWhere('nvservicestatus.work_rew1dep4_status', 2)
                ->orWhere('nvservicestatus.work_rew2dep4_status', 2)
                ->orWhere('nvservicestatus.work_rew3dep4_status', 2)
                ->orWhere('nvservicestatus.work_rew4dep4_status', 2)
                ->orWhere('nvservicestatus.ceo_nominee2_status', 2)
                ->orWhere('nvservicestatus.groupcio_status', 2);
            })
            ->sum('tbl_service.total_buget');
       
   
            $approvedAmountBRPL = DB::table('tbl_material')
               ->join('nvservicestatus', 'tbl_material.id', '=', 'nvservicestatus.material_id')
            ->whereIn('tbl_material.nv_id', $BRPLnv)->where('nvservicestatus.ceo_status', 1)
            ->sum('tbl_material.total_budget_both')+DB::table('tbl_service')
                ->join('nvservicestatus', 'tbl_service.id', '=', 'nvservicestatus.service_id')
            ->whereIn('tbl_service.nv_id', $BRPLnv)->where('nvservicestatus.ceo_status', 1)
            ->sum('tbl_service.total_buget');

            $fileDataBRPL =Nvsericestatus::
            select(
                DB::raw('MONTH(created_at) as month'),
                DB::raw('SUM(CASE WHEN ceo_status = "1" THEN 1 ELSE 0 END) as approved_count'),
                DB::raw('SUM(CASE WHEN (rv1_status = "2" OR rv2_status = "2" OR rv3_status = "2" OR rv4_status = "2" OR hod_status = "2" OR
                    ces_rew1_status = "2" OR ces_rew2_status = "2" OR ces_rew3_status = "2" OR ces_rew4_status = "2" OR cpmg_status = "2" OR
                    work_rew1_status = "2" OR work_rew2_status = "2" OR work_rew3_status = "2" OR work_rew4_status = "2" OR cto_status = "2" OR
                    work_rew1dep2_status = "2" OR work_rew2dep2_status = "2" OR work_rew3dep2_status = "2" OR work_rew4dep2_status = "2" OR ceo_nominee_status = "2" OR
                    work_rew1dep3_status = "2" OR work_rew2dep3_status = "2" OR work_rew3dep3_status = "2" OR work_rew4dep3_status = "2" OR ceo_nominee2_status = "2" OR
                    work_rew1dep4_status = "2" OR work_rew2dep4_status = "2" OR work_rew3dep4_status = "2" OR work_rew4dep4_status = "2" OR
                    ceo_status = "2" OR groupcio_status = "2")  THEN 1 ELSE 0 END) as rejected_count'),

                DB::raw('SUM(CASE WHEN (ceo_status = "0" AND
                rv1_status IN ("0", "1") AND rv2_status IN ("0", "1") AND rv3_status IN ("0", "1") AND rv4_status IN ("0", "1") AND hod_status IN ("0", "1") AND
                ces_rew1_status IN ("0", "1") AND ces_rew2_status IN ("0", "1") AND ces_rew3_status IN ("0", "1") AND ces_rew4_status IN ("0", "1") AND cpmg_status IN ("0", "1") AND
                work_rew1_status IN ("0", "1") AND work_rew2_status IN ("0", "1") AND work_rew3_status IN ("0", "1") AND work_rew4_status IN ("0", "1") AND cto_status IN ("0", "1") AND
                work_rew1dep2_status IN ("0", "1") AND work_rew2dep2_status IN ("0", "1") AND work_rew3dep2_status IN ("0", "1") AND work_rew4dep2_status IN ("0", "1") AND ceo_nominee_status IN ("0", "1") AND
                work_rew1dep3_status IN ("0", "1") AND work_rew2dep3_status IN ("0", "1") AND work_rew3dep3_status IN ("0", "1") AND work_rew4dep3_status IN ("0", "1") AND ceo_nominee2_status IN ("0", "1") AND
                work_rew1dep4_status IN ("0", "1") AND work_rew2dep4_status IN ("0", "1") AND work_rew3dep4_status IN ("0", "1") AND work_rew4dep4_status IN ("0", "1") AND
                    ceo_status IN ("0", "1") AND groupcio_status IN ("0", "1"))  THEN 1 ELSE 0 END) as pending_count'),
        
            )
            ->whereIn('nv_id', $BRPLnv)->with(['service', 'material', 'user'])
            ->whereYear('created_at', Carbon::now()->year)
            ->groupBy('month')
            ->orderBy('month')
            ->get();

        $brpl_hodApproval = Nvsericestatus::whereIn('nv_id', $BRPLnv)->where("hod_status", 0)->count();
        $brpl_cesApproval = Nvsericestatus::whereIn('nv_id', $BRPLnv)->where("hod_status", 1)->where("derc_info", 1)->where("ces_status", 0)->count();
     

        $cpmg = Nvsericestatus::whereIn('nv_id', $BRPLnv)->where('hod_status', 1)->where('cpmg_status', 0)->get();
        $Pendingcpmg = $cpmg->filter(function ($data) {
            return in_array($data->hod_status, [1]) &&
                in_array($data->cpmg_status, [0]) &&
                (
                    (in_array($data->derc_info, [0]) && in_array($data->ces_status, [0]))
                    ||
                    (in_array($data->derc_info, [1]) && in_array($data->ces_status, [1]))
                );
        });
        $brpl_cpmgApproval = $Pendingcpmg->count();
        $brpl_ctoApproval = Nvsericestatus::whereIn('nv_id', $BRPLnv)->where("cpmg_status", 1)->where("check_technology", 1)->where("cto_status", 0)->count();
        $ceonominee1 = Nvsericestatus::whereIn('nv_id', $BRPLnv)->where('cpmg_status', 1)->where('ceo_nominee_status', 0)->get();
        $Pendingnominee1 = $ceonominee1->filter(function ($data) {
        return in_array($data->cpmg_status, [1]) &&
            in_array($data->ceo_nominee_status, [0]) &&
            (
                (in_array($data->check_technology, [0]) && in_array($data->cto_status, [0]))
                ||
                (in_array($data->check_technology, [1]) && in_array($data->cto_status, [1]))
            );
    });
    $brpl_ceon1Approval = $Pendingnominee1->count();
    if(!empty( $group_cio)){
        $brpl_ceon2Approval = Nvsericestatus::whereIn('nv_id', $BRPLnv)->where("groupcio_status", 1)->where("ceo_nominee2_status", 0)->count();
    }else{
        $brpl_ceon2Approval = Nvsericestatus::whereIn('nv_id', $BRPLnv)->where("ceo_nominee_status", 1)->where("ceo_nominee2_status", 0)->count();
 
    }
        if(!empty( $group_cio)){
            $brpl_groupcioApproval = Nvsericestatus::whereIn('nv_id', $BRPLnv)->where('ceo_nominee_status', 1)->where('groupcio_status', 0)->where('ceo_nominee2_status', 0)->count();

        }else{
            $brpl_groupcioApproval =0;
        }
     
            $brpl_ceoApproval = Nvsericestatus::whereIn('nv_id', $BRPLnv)->where('ceo_nominee2_status', 1)->where('ceo_status', 0)->count();
       

        $BRPLlabels = [];
        $BRPLapprovedData = [];
        $BRPLrejectedData = [];
        $BRPLpendingData = [];
    
        foreach ($fileDataBRPL as $dataPointBRPL) {
            $monthBRPL = Carbon::createFromFormat('!m', $dataPointBRPL->month)->format('F');

            $BRPLlabels[] = $monthBRPL;
            $BRPLapprovedData[] = $dataPointBRPL->approved_count;
            $BRPLrejectedData[] = $dataPointBRPL->rejected_count;
            $BRPLpendingData[] = $dataPointBRPL->pending_count;
            
        }
        if($fiscal_year){
            $BYPLnv = NeedValidation::where('company_id','5')->where('fiscal_year', $fiscal_year)
            ->where("delete_draft", 0)
            ->pluck("id");

        }else{
            $BYPLnv = NeedValidation::where('company_id','5')->where('fiscal_year',$currentFinancialYear)->where("delete_draft", 0)->pluck("id");

        }

        $approvedAmountBYPL = DB::table('tbl_material')
           ->join('nvservicestatus', 'tbl_material.id', '=', 'nvservicestatus.material_id')
        ->whereIn('tbl_material.nv_id',$BYPLnv)->where('nvservicestatus.ceo_status', 1)
        ->sum('tbl_material.total_budget_both')+DB::table('tbl_service')
            ->join('nvservicestatus', 'tbl_service.id', '=', 'nvservicestatus.service_id')
        ->whereIn('tbl_service.nv_id',$BYPLnv)->where('nvservicestatus.ceo_status', 1)
        ->sum('tbl_service.total_buget');

        $pendingAmountBYPL= DB::table('tbl_material')
           ->join('nvservicestatus', 'tbl_material.id', '=', 'nvservicestatus.material_id')
        ->whereIn('tbl_material.nv_id', $BYPLnv)
             ->where('nvservicestatus.ceo_status', 0)
             ->whereIn('nvservicestatus.rv1_status', [0,1])
             ->whereIn('nvservicestatus.rv2_status', [0,1])
             ->whereIn('nvservicestatus.rv3_status', [0,1])
             ->whereIn('nvservicestatus.rv4_status', [0,1])
             ->whereIn('nvservicestatus.hod_status', [0,1])
             ->whereIn('nvservicestatus.ces_rew1_status', [0,1])
             ->whereIn('nvservicestatus.ces_rew2_status', [0,1])
             ->whereIn('nvservicestatus.ces_rew3_status', [0,1])
             ->whereIn('nvservicestatus.ces_rew4_status', [0,1])
             ->whereIn('nvservicestatus.ces_status', [0,1])
             ->whereIn('nvservicestatus.cpmg_status', [0,1])
             ->whereIn('nvservicestatus.work_rew1_status', [0,1])
             ->whereIn('nvservicestatus.work_rew2_status', [0,1])
             ->whereIn('nvservicestatus.work_rew3_status', [0,1])
             ->whereIn('nvservicestatus.work_rew4_status', [0,1])
             ->whereIn('nvservicestatus.cto_status', [0,1])
             ->whereIn('nvservicestatus.work_rew1dep2_status', [0,1])
             ->whereIn('nvservicestatus.work_rew2dep2_status', [0,1])
             ->whereIn('nvservicestatus.work_rew3dep2_status', [0,1])
             ->whereIn('nvservicestatus.work_rew4dep2_status', [0,1])
             ->whereIn('nvservicestatus.ceo_nominee_status', [0,1])
             ->whereIn('nvservicestatus.work_rew1dep3_status', [0,1])
             ->whereIn('nvservicestatus.work_rew2dep3_status', [0,1])
             ->whereIn('nvservicestatus.work_rew3dep3_status', [0,1])
             ->whereIn('nvservicestatus.work_rew4dep3_status', [0,1])
             ->whereIn('nvservicestatus.ceo_nominee2_status', [0,1])
             ->whereIn('nvservicestatus.work_rew1dep4_status', [0,1])
             ->whereIn('nvservicestatus.work_rew2dep4_status', [0,1])
             ->whereIn('nvservicestatus.work_rew3dep4_status', [0,1])
             ->whereIn('nvservicestatus.work_rew4dep4_status', [0,1])
             ->whereIn('nvservicestatus.groupcio_status', [0,1])
        ->sum('tbl_material.total_budget_both')+DB::table('tbl_service')
            ->join('nvservicestatus', 'tbl_service.id', '=', 'nvservicestatus.service_id')
        ->whereIn('tbl_service.nv_id', $BYPLnv)
        ->where('nvservicestatus.ceo_status', 0)
        ->whereIn('nvservicestatus.rv1_status', [0,1])
        ->whereIn('nvservicestatus.rv2_status', [0,1])
        ->whereIn('nvservicestatus.rv3_status', [0,1])
        ->whereIn('nvservicestatus.rv4_status', [0,1])
        ->whereIn('nvservicestatus.hod_status', [0,1])
        ->whereIn('nvservicestatus.ces_rew1_status', [0,1])
        ->whereIn('nvservicestatus.ces_rew2_status', [0,1])
        ->whereIn('nvservicestatus.ces_rew3_status', [0,1])
        ->whereIn('nvservicestatus.ces_rew4_status', [0,1])
        ->whereIn('nvservicestatus.ces_status', [0,1])
        ->whereIn('nvservicestatus.cpmg_status', [0,1])
        ->whereIn('nvservicestatus.work_rew1_status', [0,1])
        ->whereIn('nvservicestatus.work_rew2_status', [0,1])
        ->whereIn('nvservicestatus.work_rew3_status', [0,1])
        ->whereIn('nvservicestatus.work_rew4_status', [0,1])
        ->whereIn('nvservicestatus.cto_status', [0,1])
        ->whereIn('nvservicestatus.work_rew1dep2_status', [0,1])
        ->whereIn('nvservicestatus.work_rew2dep2_status', [0,1])
        ->whereIn('nvservicestatus.work_rew3dep2_status', [0,1])
        ->whereIn('nvservicestatus.work_rew4dep2_status', [0,1])
        ->whereIn('nvservicestatus.ceo_nominee_status', [0,1])
        ->whereIn('nvservicestatus.work_rew1dep3_status', [0,1])
        ->whereIn('nvservicestatus.work_rew2dep3_status', [0,1])
        ->whereIn('nvservicestatus.work_rew3dep3_status', [0,1])
        ->whereIn('nvservicestatus.work_rew4dep3_status', [0,1])
        ->whereIn('nvservicestatus.ceo_nominee2_status', [0,1])
        ->whereIn('nvservicestatus.work_rew1dep4_status', [0,1])
        ->whereIn('nvservicestatus.work_rew2dep4_status', [0,1])
        ->whereIn('nvservicestatus.work_rew3dep4_status', [0,1])
        ->whereIn('nvservicestatus.work_rew4dep4_status', [0,1])
        ->whereIn('nvservicestatus.groupcio_status', [0,1])
        ->sum('tbl_service.total_buget');

        $rejectedAmountBYPL = DB::table('tbl_material')
           ->join('nvservicestatus', 'tbl_material.id', '=', 'nvservicestatus.material_id')
        ->whereIn('tbl_material.nv_id', $BYPLnv)
        ->where(function ($query) {
            $query->orWhere('nvservicestatus.ceo_status', 2)
            ->orWhere('nvservicestatus.rv1_status', 2)
            ->orWhere('nvservicestatus.rv2_status', 2)
            ->orWhere('nvservicestatus.rv3_status', 2)
            ->orWhere('nvservicestatus.rv4_status', 2)
            ->orWhere('nvservicestatus.hod_status', 2)
            ->orWhere('nvservicestatus.ces_rew1_status', 2)
            ->orWhere('nvservicestatus.ces_rew2_status', 2)
            ->orWhere('nvservicestatus.ces_rew3_status', 2)
            ->orWhere('nvservicestatus.ces_rew4_status', 2)
            ->orWhere('nvservicestatus.cpmg_status', 2)
            ->orWhere('nvservicestatus.work_rew1_status', 2)
            ->orWhere('nvservicestatus.work_rew2_status', 2)
            ->orWhere('nvservicestatus.work_rew3_status', 2)
            ->orWhere('nvservicestatus.work_rew4_status', 2)
            ->orWhere('nvservicestatus.ces_status', 2)
            ->orWhere('nvservicestatus.work_rew1dep2_status', 2)
            ->orWhere('nvservicestatus.work_rew2dep2_status', 2)
            ->orWhere('nvservicestatus.work_rew3dep2_status', 2)
            ->orWhere('nvservicestatus.work_rew4dep2_status', 2)
            ->orWhere('nvservicestatus.cto_status', 2)
            ->orWhere('nvservicestatus.work_rew1dep3_status', 2)
            ->orWhere('nvservicestatus.work_rew2dep3_status', 2)
            ->orWhere('nvservicestatus.work_rew3dep3_status', 2)
            ->orWhere('nvservicestatus.work_rew4dep3_status', 2)
            ->orWhere('nvservicestatus.ceo_nominee_status', 2)
            ->orWhere('nvservicestatus.work_rew1dep4_status', 2)
            ->orWhere('nvservicestatus.work_rew2dep4_status', 2)
            ->orWhere('nvservicestatus.work_rew3dep4_status', 2)
            ->orWhere('nvservicestatus.work_rew4dep4_status', 2)
            ->orWhere('nvservicestatus.ceo_nominee2_status', 2)
            ->orWhere('nvservicestatus.groupcio_status', 2);
        })
        ->sum('tbl_material.total_budget_both')+ DB::table('tbl_service')
            ->join('nvservicestatus', 'tbl_service.id', '=', 'nvservicestatus.service_id')
        ->whereIn('tbl_service.nv_id', $BYPLnv)
        ->where(function ($query) {
            $query->orWhere('nvservicestatus.ceo_status', 2)
                ->orWhere('nvservicestatus.rv1_status', 2)
                ->orWhere('nvservicestatus.rv2_status', 2)
                ->orWhere('nvservicestatus.rv3_status', 2)
                ->orWhere('nvservicestatus.rv4_status', 2)
                ->orWhere('nvservicestatus.hod_status', 2)
                ->orWhere('nvservicestatus.ces_rew1_status', 2)
                ->orWhere('nvservicestatus.ces_rew2_status', 2)
                ->orWhere('nvservicestatus.ces_rew3_status', 2)
                ->orWhere('nvservicestatus.ces_rew4_status', 2)
                ->orWhere('nvservicestatus.cpmg_status', 2)
                ->orWhere('nvservicestatus.work_rew1_status', 2)
                ->orWhere('nvservicestatus.work_rew2_status', 2)
                ->orWhere('nvservicestatus.work_rew3_status', 2)
                ->orWhere('nvservicestatus.work_rew4_status', 2)
                ->orWhere('nvservicestatus.ces_status', 2)
                ->orWhere('nvservicestatus.work_rew1dep2_status', 2)
                ->orWhere('nvservicestatus.work_rew2dep2_status', 2)
                ->orWhere('nvservicestatus.work_rew3dep2_status', 2)
                ->orWhere('nvservicestatus.work_rew4dep2_status', 2)
                ->orWhere('nvservicestatus.cto_status', 2)
                ->orWhere('nvservicestatus.work_rew1dep3_status', 2)
                ->orWhere('nvservicestatus.work_rew2dep3_status', 2)
                ->orWhere('nvservicestatus.work_rew3dep3_status', 2)
                ->orWhere('nvservicestatus.work_rew4dep3_status', 2)
                ->orWhere('nvservicestatus.ceo_nominee_status', 2)
                ->orWhere('nvservicestatus.work_rew1dep4_status', 2)
                ->orWhere('nvservicestatus.work_rew2dep4_status', 2)
                ->orWhere('nvservicestatus.work_rew3dep4_status', 2)
                ->orWhere('nvservicestatus.work_rew4dep4_status', 2)
                ->orWhere('nvservicestatus.ceo_nominee2_status', 2)
                ->orWhere('nvservicestatus.groupcio_status', 2);
        })
        ->sum('tbl_service.total_buget');
        // dd( $BYPLnv );     
         $fileDataBYPL =Nvsericestatus::
        select(
            DB::raw('MONTH(created_at) as month'),
            DB::raw('SUM(CASE WHEN ceo_status = "1" THEN 1 ELSE 0 END) as approved_count'),
            DB::raw('SUM(CASE WHEN (rv1_status = "2" OR rv2_status = "2" OR rv3_status = "2" OR rv4_status = "2" OR hod_status = "2" OR
            ces_rew1_status = "2" OR ces_rew2_status = "2" OR ces_rew3_status = "2" OR ces_rew4_status = "2" OR cpmg_status = "2" OR
            work_rew1_status = "2" OR work_rew2_status = "2" OR work_rew3_status = "2" OR work_rew4_status = "2" OR cto_status = "2" OR
            work_rew1dep2_status = "2" OR work_rew2dep2_status = "2" OR work_rew3dep2_status = "2" OR work_rew4dep2_status = "2" OR ceo_nominee_status = "2" OR
            work_rew1dep3_status = "2" OR work_rew2dep3_status = "2" OR work_rew3dep3_status = "2" OR work_rew4dep3_status = "2" OR ceo_nominee2_status = "2" OR
            work_rew1dep4_status = "2" OR work_rew2dep4_status = "2" OR work_rew3dep4_status = "2" OR work_rew4dep4_status = "2" OR
            ceo_status = "2" OR groupcio_status = "2")  THEN 1 ELSE 0 END) as rejected_count'),

            DB::raw('SUM(CASE WHEN (ceo_status = "0" AND
            rv1_status IN ("0", "1") AND rv2_status IN ("0", "1") AND rv3_status IN ("0", "1") AND rv4_status IN ("0", "1") AND hod_status IN ("0", "1") AND
            ces_rew1_status IN ("0", "1") AND ces_rew2_status IN ("0", "1") AND ces_rew3_status IN ("0", "1") AND ces_rew4_status IN ("0", "1") AND cpmg_status IN ("0", "1") AND
            work_rew1_status IN ("0", "1") AND work_rew2_status IN ("0", "1") AND work_rew3_status IN ("0", "1") AND work_rew4_status IN ("0", "1") AND cto_status IN ("0", "1") AND
            work_rew1dep2_status IN ("0", "1") AND work_rew2dep2_status IN ("0", "1") AND work_rew3dep2_status IN ("0", "1") AND work_rew4dep2_status IN ("0", "1") AND ceo_nominee_status IN ("0", "1") AND
            work_rew1dep3_status IN ("0", "1") AND work_rew2dep3_status IN ("0", "1") AND work_rew3dep3_status IN ("0", "1") AND work_rew4dep3_status IN ("0", "1") AND ceo_nominee2_status IN ("0", "1") AND
            work_rew1dep4_status IN ("0", "1") AND work_rew2dep4_status IN ("0", "1") AND work_rew3dep4_status IN ("0", "1") AND work_rew4dep4_status IN ("0", "1") AND
            ceo_status IN ("0", "1") AND groupcio_status IN ("0", "1"))  THEN 1 ELSE 0 END) as pending_count'),
         
        )
        ->whereIn('nv_id', $BYPLnv)->with(['service', 'material', 'user'])
        ->whereYear('created_at', Carbon::now()->year)
        ->groupBy('month')
        ->orderBy('month')
        ->get();

    $bypl_hodApproval = Nvsericestatus::whereIn('nv_id', $BYPLnv)->where("hod_status", 0)->count();
    $bypl_cesApproval = Nvsericestatus::whereIn('nv_id', $BYPLnv)->where("hod_status", 1)->where("derc_info", 1)->where("ces_status", 0)->count();
   
    $cpmg = Nvsericestatus::whereIn('nv_id', $BYPLnv)->where('hod_status', 1)->where('cpmg_status', 0)->get();
    $Pendingcpmg = $cpmg->filter(function ($data) {
        return in_array($data->hod_status, [1]) &&
            in_array($data->cpmg_status, [0]) &&
            (
                (in_array($data->derc_info, [0]) && in_array($data->ces_status, [0]))
                ||
                (in_array($data->derc_info, [1]) && in_array($data->ces_status, [1]))
            );
    });
    $bypl_cpmgApproval = $Pendingcpmg->count();
    $bypl_ctoApproval = Nvsericestatus::whereIn('nv_id', $BYPLnv)->where("cpmg_status", 1)->where("check_technology", 1)->where("cto_status", 0)->count();
    $ceonominee1 = Nvsericestatus::whereIn('nv_id', $BYPLnv)->where('cpmg_status', 1)->where('ceo_nominee_status', 0)->get();
    $Pendingnominee1 = $ceonominee1->filter(function ($data) {
    return in_array($data->cpmg_status, [1]) &&
        in_array($data->ceo_nominee_status, [0]) &&
        (
            (in_array($data->check_technology, [0]) && in_array($data->cto_status, [0]))
            ||
            (in_array($data->check_technology, [1]) && in_array($data->cto_status, [1]))
        );
});
$bypl_ceon1Approval = $Pendingnominee1->count(); 
if(!empty( $group_cio)){
    $bypl_ceon2Approval = Nvsericestatus::whereIn('nv_id', $BYPLnv)->where("groupcio_status", 1)->where("ceo_nominee2_status", 0)->count();
}else{
    $bypl_ceon2Approval = Nvsericestatus::whereIn('nv_id', $BYPLnv)->where("ceo_nominee_status", 1)->where("ceo_nominee2_status", 0)->count();
  
}
    if(!empty( $group_cio)){
        $bypl_groupcioApproval = Nvsericestatus::whereIn('nv_id', $BYPLnv)->where('ceo_nominee_status', 1)->where('groupcio_status', 0)->where('ceo_nominee2_status', 0)->count();

    }else{
        $bypl_groupcioApproval = 0;

    }
  
        $bypl_ceoApproval = Nvsericestatus::whereIn('nv_id', $BYPLnv)->where('ceo_nominee2_status', 1)->where('ceo_status', 0)->count();
      

    $BYPLlabels = [];	
    $BYPLapprovedData = [];	
    $BYPLrejectedData = [];	
    $BYPLpendingData = [];	
    foreach ($fileDataBYPL as $dataPointBYPL) {	
        $monthBYPL = Carbon::createFromFormat('!m', $dataPointBYPL->month)->format('F');	
        $BYPLlabels[] = $monthBYPL;	
        $BYPLapprovedData[] = $dataPointBYPL->approved_count;	
        $BYPLrejectedData[] = $dataPointBYPL->rejected_count;	
        $BYPLpendingData[] = $dataPointBYPL->pending_count;	
    }	
       return view("admin.dashboard", compact("dept_data","dept_data_p","currentFinancialYear","nextFinancialYear","nextToNextFinancialYear","departmentNVCounts","approvedAmount","rejectedAmount","pendingAmount","totalAmount","company", "company_id" , "ceoApproval", "ceonominee1Approval", "ceonominee2Approval", "nv_sm_data", "totalNV", "approvedNV", "rejectedNV", "pendingNV" , "hodApproval", "cpmgApproval", "btApproval"             ,'BRPLlabels','BRPLapprovedData','BRPLrejectedData','BRPLpendingData','BYPLlabels','BYPLapprovedData','BYPLrejectedData','BYPLpendingData','bypl_hodApproval','brpl_hodApproval','bypl_cpmgApproval','brpl_cpmgApproval','bypl_ceon1Approval','brpl_ceon1Approval','bypl_ctoApproval','brpl_ctoApproval','bypl_ceoApproval','brpl_ceoApproval','bypl_ceon2Approval','brpl_ceon2Approval','bypl_cesApproval','brpl_cesApproval','cesApproval','approvedAmountBRPL','pendingAmountBRPL','rejectedAmountBRPL','approvedAmountBYPL','pendingAmountBYPL','rejectedAmountBYPL','groupcioApproval','bypl_groupcioApproval','brpl_groupcioApproval'));
    } else {	
        $id0 = Workflow::where("id",1)->first();
        $id1 = Workflow::skip(1)->first();
        $id2 = Workflow::skip(2)->first();
        $id3 = Workflow::skip(3)->first();
        $id4 = Workflow::skip(4)->first();
        $id5 = Workflow::skip(5)->first();
       
        $employee = Employee::where('user_id', $user->id)->with('department')->first();	
        $allusers = Employee::where('department_id', $employee->department_id)->get();	
        $allNormalUsers = $allusers->where('role_id', 9)->pluck('user_id');	
        $employees = Employee::where("user_id", $user->id)->first();
        $departmentIds = explode(',', $employees->department_id);
       
                $departments = Department::whereIn("id", $departmentIds)->get();
                foreach ($departments as $dep) {
                    $dep_id = $dep->id;
                    $hod = $dep->dep_hod;
                    $dep_rew1 = $dep->dep_rew1;
                    $dep_rew2 = $dep->dep_rew2;
                    $dep_rew3 = $dep->dep_rew3;
                    $dep_rew4 = $dep->dep_rew4;
                    $group_cio = $dep->group_cio;
                    
        $department = optional($employee->department);	
        $Values = [$hod, $dep_rew1, $dep_rew2, $dep_rew3, $dep_rew4];	
        $departmentIds = explode(',', $user->department_id);

        if ($dep_rew1 == $user->id) {	
            $Values = [$user->id, $dep_rew1];	

            if($fiscal_year){
                $totalId = NeedValidation::where('fiscal_year', $fiscal_year)
                ->where(function ($query) use ($Values, $allNormalUsers, $departmentIds) {
                    $query->whereIn('user_id', $Values)
                          ->orWhereIn('user_id', $allNormalUsers)
                          ->orWhereIn('department_id', $departmentIds);
                })
                ->pluck('id');
            }else{
                $totalId = NeedValidation::where('fiscal_year', $currentFinancialYear)
                ->where(function ($query) use ($Values, $allNormalUsers, $departmentIds) {
                    $query->whereIn('user_id', $Values)
                          ->orWhereIn('user_id', $allNormalUsers)
                          ->orWhereIn('department_id', $departmentIds);
                })
                ->pluck('id');
            }
          
            if($company_id){
                $totalNV = Nvsericestatus::whereIn("nv_id", $totalId)->where('rv1_status', 1)->where('company_id',$company_id)->count();
            }else {
                $totalNV = Nvsericestatus::whereIn("nv_id", $totalId)->where('rv1_status', 1)->count();
            }
            
            
              
            if($company_id){
                $latestData = Nvsericestatus::whereIn("nv_id", $totalId)->where('company_id',$company_id)->get();
            }else {
                $latestData = Nvsericestatus::whereIn("nv_id", $totalId)->get();
            }	
            $totalAmount = DB::table('tbl_material')
               ->join('nvservicestatus', 'tbl_material.id', '=', 'nvservicestatus.material_id')
             ->whereIn('tbl_material.nv_id', $totalId)
             ->whereIn('nvservicestatus.nv_id', $latestData->pluck('nv_id'))

            ->where('nvservicestatus.rv1_status', 1)
            ->sum('tbl_material.total_budget_both') + DB::table('tbl_service')
            ->join('nvservicestatus', 'tbl_service.id', '=', 'nvservicestatus.service_id')
             ->whereIn('tbl_service.nv_id', $totalId)
             ->whereIn('nvservicestatus.nv_id', $latestData->pluck('nv_id'))

            ->where('nvservicestatus.rv1_status', 1)
            ->sum('tbl_service.total_buget');
          
            
            $pendingAmount = DB::table('tbl_material')
               ->join('nvservicestatus', 'tbl_material.id', '=', 'nvservicestatus.material_id')
             ->whereIn('tbl_material.nv_id', $totalId)
             ->whereIn('nvservicestatus.nv_id', $latestData->pluck('nv_id'))
            ->where('nvservicestatus.rv1_status', 0)->where('nvservicestatus.draft',1)
            ->sum('tbl_material.total_budget_both') + DB::table('tbl_service')
               ->join('nvservicestatus', 'tbl_service.id', '=', 'nvservicestatus.service_id')
             ->whereIn('tbl_service.nv_id', $totalId)
             ->whereIn('nvservicestatus.nv_id', $latestData->pluck('nv_id'))
            ->where('nvservicestatus.rv1_status', 0)->where('nvservicestatus.draft',1)
            ->sum('tbl_service.total_buget');
    
            $rejectedAmount = DB::table('tbl_material')
               ->join('nvservicestatus', 'tbl_material.id', '=', 'nvservicestatus.material_id')
               ->whereIn('tbl_material.nv_id', $totalId)
            ->whereIn('nvservicestatus.nv_id', $latestData->pluck('nv_id'))
            ->where(function ($query) {
                $query->where('nvservicestatus.rv1_status', 2);
            })
            ->sum('tbl_material.total_budget_both') + DB::table('tbl_service')
               ->join('nvservicestatus', 'tbl_service.id', '=', 'nvservicestatus.service_id')
               ->whereIn('tbl_service.nv_id', $totalId)
            ->whereIn('nvservicestatus.nv_id', $latestData->pluck('nv_id'))
            ->where(function ($query) {
                $query->where('nvservicestatus.rv1_status', 2);
            })
            ->sum('tbl_service.total_buget');
       
    
            $approvedAmount = DB::table('tbl_material')
             ->whereIn('tbl_material.nv_id', $totalId)
             ->whereIn('nvservicestatus.nv_id', $latestData->pluck('nv_id'))

               ->join('nvservicestatus', 'tbl_material.id', '=', 'nvservicestatus.material_id')
            ->where('nvservicestatus.ceo_status', 1)
            ->sum('tbl_material.total_budget_both') +  DB::table('tbl_service')
            ->whereIn('tbl_service.nv_id', $totalId)
            ->whereIn('nvservicestatus.nv_id', $latestData->pluck('nv_id'))

              ->join('nvservicestatus', 'tbl_service.id', '=', 'nvservicestatus.service_id')
           ->where('nvservicestatus.ceo_status', 1)
           ->sum('tbl_service.total_buget');


            if($company_id){
                    $approvedNV = $latestData->where('ceo_status', 1)->where('company_id',$company_id)->count();
                }else{
                    $approvedNV = $latestData->where('ceo_status', 1)->count();
                }	
            $rejectedNV = $latestData->filter(function ($data) {	
                return in_array($data->ceo_status, [2]) || 		
                $data->rv1_status == 2 ;	
               
            })->count();	
          	
            if($company_id){
                $pendingNV = Nvsericestatus::whereIn("nv_id", $totalId)->where('draft',1)->where('rv1_status', 0)->where('company_id',$company_id)->count();
            }	 else {
                $pendingNV = Nvsericestatus::whereIn("nv_id", $totalId)->where('draft',1)->where('rv1_status', 0)->count();
            }
             	
          
        
            $nvIds = NeedValidation::whereIn("user_id", $user_id)->pluck("id");	
            if($fiscal_year){
                $nv = NeedValidation::where('fiscal_year', $fiscal_year)->where(function ($query) use ($user, $allNormalUsers, $departmentIds, $currentFinancialYear) {
                    $query->where('user_id', $user->id)
                          ->orWhereIn('user_id', $allNormalUsers)
                          ->orWhereIn('department_id', $departmentIds);
                })
                ->whereHas('service')
                ->select('id')
                ->get();  
            }else{
                $nv = NeedValidation::where('fiscal_year', $currentFinancialYear)->where(function ($query) use ($user, $allNormalUsers, $departmentIds, $currentFinancialYear) {
                    $query->where('user_id', $user->id)
                          ->orWhereIn('user_id', $allNormalUsers)
                          ->orWhereIn('department_id', $departmentIds);
                })
                ->whereHas('service')
                ->select('id')
                ->get();  
            }
                   
            
            $nv_ids = $nv->pluck('id');	
            if($company_id){
                    $nv_sm_data = Nvsericestatus::whereIn('nv_id', $nv_ids)->where('draft',1)->where('company_id',$company_id)->with(['service', 'material', 'user'])->orderBy('id', 'asc');
            } else {
                    $nv_sm_data = Nvsericestatus::whereIn('nv_id', $nv_ids)->where('draft',1)->with(['service', 'material', 'user'])->orderBy('id', 'asc');
            }	
            $total0_status = $request->total;
            $pending0_status = $request->pending;
            $reject0_status = $request->rejected;

           
          
           
            if ($ceo_status !== null) {
                $nv_sm_data = $nv_sm_data->where('ceo_status', $ceo_status);
            } elseif ($pending0_status !== null) {
                $nv_sm_data = $nv_sm_data->where('rv1_status', $pending0_status)
                    ->whereIn('ceo_status', [0, 1])
                    ->whereIn('cpmg_status', [0, 1])
                    ->whereIn('cto_status', [0, 1])
                    ->whereIn('ceo_nominee_status', [0, 1])    
                    ->whereIn('ceo_nominee2_status', [0, 1]);
            } elseif ($reject0_status !== null) {
                $nv_sm_data = $nv_sm_data->where('rv1_status', $reject0_status)
                    ->orWhere('hod_status', [2])
                    ->orWhere('cpmg_status', [2])
                    ->orWhere('cto_status', [2])
                    ->orWhere('ceo_nominee_status', [2])
                    ->orWhere('ceo_nominee2_status', [2]);
            }elseif ($total0_status !== null) {
                $nv_sm_data = $nv_sm_data->where('rv1_status', $total0_status);
                  
            }
            $nv_sm_data = $nv_sm_data->get();
            if($fiscal_year){
                $BRPLnv = NeedValidation::whereIn("department_id", $departmentIds)->where('company_id','6')->where('fiscal_year',$fiscal_year)->pluck("id");
        
            }else{
                $BRPLnv = NeedValidation::whereIn("department_id", $departmentIds)->where('company_id','6')->where('fiscal_year',$currentFinancialYear)->pluck("id");

            }
            $pendingAmountBRPL = DB::table('tbl_material')
               ->join('nvservicestatus', 'tbl_material.id', '=', 'nvservicestatus.material_id')
            ->whereIn('tbl_material.nv_id', $BRPLnv)
            ->where('nvservicestatus.rv1_status', 0)->where('nvservicestatus.draft',1)
            ->sum('tbl_material.total_budget_both')+DB::table('tbl_service')
               ->join('nvservicestatus', 'tbl_service.id', '=', 'nvservicestatus.service_id')
            ->whereIn('tbl_service.nv_id', $BRPLnv)
            ->where('nvservicestatus.rv1_status', 0)->where('nvservicestatus.draft',1)
            ->sum('tbl_service.total_buget');
    
            $rejectedAmountBRPL = DB::table('tbl_material')
               ->join('nvservicestatus', 'tbl_material.id', '=', 'nvservicestatus.material_id')
            ->whereIn('tbl_material.nv_id', $BRPLnv)
            ->where(function ($query) {
                $query->where('nvservicestatus.rv1_status', 2);
            })
            ->sum('tbl_material.total_budget_both')+DB::table('tbl_service')
               ->join('nvservicestatus', 'tbl_service.id', '=', 'nvservicestatus.service_id')
            ->whereIn('tbl_service.nv_id', $BRPLnv)
            ->where(function ($query) {
                $query->where('nvservicestatus.rv1_status', 2);
            })
            ->sum('tbl_service.total_buget');

          $approvedAmountBRPL = DB::table('tbl_material')
               ->join('nvservicestatus', 'tbl_material.id', '=', 'nvservicestatus.material_id')
            ->whereIn('tbl_material.nv_id', $BRPLnv)->where('nvservicestatus.ceo_status', 1)
            ->sum('tbl_material.total_budget_both')+DB::table('tbl_service')
               ->join('nvservicestatus', 'tbl_service.id', '=', 'nvservicestatus.service_id')
            ->whereIn('tbl_service.nv_id', $BRPLnv)->where('nvservicestatus.ceo_status', 1)
            ->sum('tbl_service.total_buget');
            $fileDataBRPL =Nvsericestatus::
            select(
                DB::raw('MONTH(created_at) as month'),
                DB::raw('SUM(CASE WHEN ceo_status = "1" THEN 1 ELSE 0 END) as approved_count'),
                DB::raw('SUM(CASE WHEN ( rv1_status = "2")  THEN 1 ELSE 0 END ) as rejected_count'),
                DB::raw('SUM(CASE WHEN (rv1_status = "0" AND  draft = "1" ) THEN 1 ELSE 0 END) as pending_count'),
            )
         
            ->whereIn('nv_id', $BRPLnv)->with(['service', 'material', 'user'])
            ->whereYear('created_at', Carbon::now()->year)
            ->groupBy('month')
            ->orderBy('month')
            ->get();
        $BRPLlabels = [];
        $BRPLapprovedData = [];
        $BRPLrejectedData = [];
        $BRPLpendingData = [];
    
        foreach ($fileDataBRPL as $dataPointBRPL) {
            $monthBRPL = Carbon::createFromFormat('!m', $dataPointBRPL->month)->format('F');
    
            $BRPLlabels[] = $monthBRPL;
            $BRPLapprovedData[] = $dataPointBRPL->approved_count;
            $BRPLrejectedData[] = $dataPointBRPL->rejected_count;
            $BRPLpendingData[] = $dataPointBRPL->pending_count;
        }
    if($fiscal_year){
        $BYPLnv = NeedValidation::whereIn("department_id", $departmentIds)->where('company_id','5')->where('fiscal_year',$fiscal_year)->pluck("id");

    }else{
        $BYPLnv = NeedValidation::whereIn("department_id", $departmentIds)->where('company_id','5')->where('fiscal_year',$currentFinancialYear)->pluck("id");

    }
        $pendingAmountBYPL = DB::table('tbl_material')
           ->join('nvservicestatus', 'tbl_material.id', '=', 'nvservicestatus.material_id')
        ->whereIn('tbl_material.nv_id', $BYPLnv)
        ->where('nvservicestatus.rv1_status', 0)->where('nvservicestatus.draft',1)
        ->sum('tbl_material.total_budget_both')+DB::table('tbl_service')
           ->join('nvservicestatus', 'tbl_service.id', '=', 'nvservicestatus.service_id')
        ->whereIn('tbl_service.nv_id', $BYPLnv)
        ->where('nvservicestatus.rv1_status', 0)->where('nvservicestatus.draft',1)
        ->sum('tbl_service.total_buget');

        $rejectedAmountBYPL = DB::table('tbl_material')
           ->join('nvservicestatus', 'tbl_material.id', '=', 'nvservicestatus.material_id')
        ->whereIn('tbl_material.nv_id', $BYPLnv)
        ->where(function ($query) {
            $query->where('nvservicestatus.rv1_status', 2);
        })
        ->sum('tbl_material.total_budget_both')+DB::table('tbl_service')
           ->join('nvservicestatus', 'tbl_service.id', '=', 'nvservicestatus.service_id')
        ->whereIn('tbl_service.nv_id', $BYPLnv)
        ->where(function ($query) {
            $query->where('nvservicestatus.rv1_status', 2);
        })
        ->sum('tbl_service.total_buget');

      $approvedAmountBYPL = DB::table('tbl_material')
           ->join('nvservicestatus', 'tbl_material.id', '=', 'nvservicestatus.material_id')
        ->whereIn('tbl_material.nv_id', $BYPLnv)->where('nvservicestatus.ceo_status', 1)
        ->sum('tbl_material.total_budget_both')+DB::table('tbl_service')
           ->join('nvservicestatus', 'tbl_service.id', '=', 'nvservicestatus.service_id')
        ->whereIn('tbl_service.nv_id', $BYPLnv)->where('nvservicestatus.ceo_status', 1)
        ->sum('tbl_service.total_buget');
         $fileDataBYPL =Nvsericestatus::
        select(
            DB::raw('MONTH(created_at) as month'),
            DB::raw('SUM(CASE WHEN ceo_status = "1" THEN 1 ELSE 0 END) as approved_count'),
            DB::raw('SUM(CASE WHEN ( rv1_status = "2")  THEN 1 ELSE 0 END) as rejected_count'),
            DB::raw('SUM(CASE WHEN (rv1_status = "0" AND  draft = "1" )  THEN 1 ELSE 0 END) as pending_count'),
        ) 
        ->whereIn('nv_id', $BYPLnv)->with(['service', 'material', 'user'])
        ->whereYear('created_at', Carbon::now()->year)
        ->groupBy('month')
        ->orderBy('month')
        ->get();
    $BYPLlabels = [];
    $BYPLapprovedData = [];
    $BYPLrejectedData = [];
    $BYPLpendingData = [];

    foreach ($fileDataBYPL as $dataPointBYPL) {
        $monthBYPL = Carbon::createFromFormat('!m', $dataPointBYPL->month)->format('F');

        $BYPLlabels[] = $monthBYPL;
        $BYPLapprovedData[] = $dataPointBYPL->approved_count;
        $BYPLrejectedData[] = $dataPointBYPL->rejected_count;
        $BYPLpendingData[] = $dataPointBYPL->pending_count;
     }
            return view("admin.dashboard", compact("cesApproval","currentFinancialYear","nextFinancialYear","nextToNextFinancialYear","approvedAmount", "rejectedAmount", "pendingAmount", "totalAmount","company", "company_id" , "nv_sm_data", "totalNV", "approvedNV", "rejectedNV", "pendingNV" , "hodApproval", "cpmgApproval", "btApproval", "ceoApproval"             ,'BRPLlabels','BRPLapprovedData','BRPLrejectedData','BRPLpendingData','BYPLlabels','BYPLapprovedData','BYPLrejectedData','BYPLpendingData','approvedAmountBYPL','approvedAmountBRPL','rejectedAmountBYPL','rejectedAmountBRPL','pendingAmountBYPL','pendingAmountBRPL'));
    } elseif ($dep_rew2 == $user->id) {
        $Values = [$user->id, $dep_rew1];
        $departmentIds = explode(',', $user->department_id);
        if($fiscal_year){
            $totalId = NeedValidation::where('fiscal_year', $fiscal_year)
            ->where(function ($query) use ($Values, $allNormalUsers, $departmentIds) {
                $query->whereIn('user_id', $Values)
                    ->orWhereIn('user_id', $allNormalUsers)
                    ->orWhereIn('department_id', $departmentIds);
            })
            ->pluck('id');
        }else{
            $totalId = NeedValidation::where('fiscal_year', $currentFinancialYear)
            ->where(function ($query) use ($Values, $allNormalUsers, $departmentIds) {
                $query->whereIn('user_id', $Values)
                    ->orWhereIn('user_id', $allNormalUsers)
                    ->orWhereIn('department_id', $departmentIds);
            })
            ->pluck('id');
        }
        

    if($company_id){
        $latestData = Nvsericestatus::whereIn("nv_id", $totalId)->where('company_id',$company_id)->get();
    }else {
        $latestData = Nvsericestatus::whereIn("nv_id", $totalId)->get();
    }

    $totalAmount = DB::table('tbl_material')
       ->join('nvservicestatus', 'tbl_material.id', '=', 'nvservicestatus.material_id')
     ->whereIn('tbl_material.nv_id', $totalId)
     ->whereIn('nvservicestatus.nv_id', $latestData->pluck('nv_id'))

    ->where('nvservicestatus.rv2_status', 1)
    ->sum('tbl_material.total_budget_both') + DB::table('tbl_service')
        ->join('nvservicestatus', 'tbl_service.id', '=', 'nvservicestatus.service_id')
     ->whereIn('tbl_service.nv_id', $totalId)
     ->whereIn('nvservicestatus.nv_id', $latestData->pluck('nv_id'))

    ->where('nvservicestatus.rv2_status', 1)
    ->sum('tbl_service.total_buget');
  
    if(!empty($dep_rew1)){
        $pendingAmount = DB::table('tbl_material')
           ->join('nvservicestatus', 'tbl_material.id', '=', 'nvservicestatus.material_id')
         ->whereIn('tbl_material.nv_id', $totalId)
         ->whereIn('nvservicestatus.nv_id', $latestData->pluck('nv_id'))
        ->where('nvservicestatus.rv2_status', 0)
        ->where('nvservicestatus.rv1_status', 1)
        ->sum('tbl_material.total_budget_both') + DB::table('tbl_service')
            ->join('nvservicestatus', 'tbl_service.id', '=', 'nvservicestatus.service_id')
         ->whereIn('tbl_service.nv_id', $totalId)
         ->whereIn('nvservicestatus.nv_id', $latestData->pluck('nv_id'))
        ->where('nvservicestatus.rv2_status', 0)
        ->where('nvservicestatus.rv1_status', 1)
        ->sum('tbl_service.total_buget');
    }else{
        $pendingAmount = DB::table('tbl_material')
           ->join('nvservicestatus', 'tbl_material.id', '=', 'nvservicestatus.material_id')
         ->whereIn('tbl_material.nv_id', $totalId)
         ->whereIn('nvservicestatus.nv_id', $latestData->pluck('nv_id'))
        ->where('nvservicestatus.rv2_status', 0)
        ->where('nvservicestatus.draft', 1)
        ->sum('tbl_material.total_budget_both') + DB::table('tbl_service')
            ->join('nvservicestatus', 'tbl_service.id', '=', 'nvservicestatus.service_id')
         ->whereIn('tbl_service.nv_id', $totalId)
         ->whereIn('nvservicestatus.nv_id', $latestData->pluck('nv_id'))
        ->where('nvservicestatus.rv2_status', 0)
        ->where('nvservicestatus.draft', 1)
        ->sum('tbl_service.total_buget');
    }
   

    $rejectedAmount = DB::table('tbl_material')
       ->join('nvservicestatus', 'tbl_material.id', '=', 'nvservicestatus.material_id')
       ->whereIn('tbl_material.nv_id', $totalId)
    ->whereIn('nvservicestatus.nv_id', $latestData->pluck('nv_id'))
    ->where(function ($query) {
        $query->where('nvservicestatus.rv2_status', 2);
    })
    ->sum('tbl_material.total_budget_both') +  DB::table('tbl_service')
        ->join('nvservicestatus', 'tbl_service.id', '=', 'nvservicestatus.service_id')
        ->whereIn('tbl_service.nv_id', $totalId)
    ->whereIn('nvservicestatus.nv_id', $latestData->pluck('nv_id'))
    ->where(function ($query) {
        $query->where('nvservicestatus.rv2_status', 2);
    })
    ->sum('tbl_service.total_buget');


    $approvedAmount = DB::table('tbl_material')
     ->whereIn('tbl_material.nv_id', $totalId)
     ->whereIn('nvservicestatus.nv_id', $latestData->pluck('nv_id'))
       ->join('nvservicestatus', 'tbl_material.id', '=', 'nvservicestatus.material_id')
    ->where('nvservicestatus.ceo_status', 1)
    ->sum('tbl_material.total_budget_both') +  DB::table('tbl_service')
    ->whereIn('tbl_service.nv_id', $totalId)
    ->whereIn('nvservicestatus.nv_id', $latestData->pluck('nv_id'))
       ->join('nvservicestatus', 'tbl_service.id', '=', 'nvservicestatus.service_id')
   ->where('nvservicestatus.ceo_status', 1)
   ->sum('tbl_service.total_buget');

        if($company_id){
            $approvedNV = $latestData->where('ceo_status', 1)->where('company_id',$company_id)->count();
        }else{
            $approvedNV = $latestData->where('ceo_status', 1)->count();
        }
        $rejectedNV = $latestData->filter(function ($data) {
            return in_array($data->ceo_status, [2]) || 
            $data->rv2_status == 2 ;
        })->count();
        if(!empty($dep_rew1)){
        $pendingNV = $latestData->filter(function ($data) {
            return  in_array($data->rv1_status, [1]) &&
                in_array($data->rv2_status, [0]);
        })->count();
    }else{
        $pendingNV = $latestData->filter(function ($data) {
            return  in_array($data->draft, [1]) &&
                in_array($data->rv2_status, [0]);
        })->count();
    }
        if($company_id){
            $totalNV = Nvsericestatus::whereIn("nv_id", $totalId)->where('rv2_status', 1)->where('company_id',$company_id)->count();
        } else {
            $totalNV = Nvsericestatus::whereIn("nv_id", $totalId)->where('rv2_status', 1)->count();
        }
         
      
      
        $nvIds = NeedValidation::whereIn("user_id", $Values)->pluck("id");
        if($fiscal_year){
            $nv = NeedValidation::where('fiscal_year', $fiscal_year)->where(function ($query) use ($user, $allNormalUsers, $departmentIds, $currentFinancialYear) {
                $query->where('user_id', $user->id)
                      ->orWhereIn('user_id', $allNormalUsers)
                      ->orWhereIn('department_id', $departmentIds);
            })
            ->whereHas('service') 
            ->select('id')
            ->get();
        }else{
            $nv = NeedValidation::where('fiscal_year', $currentFinancialYear)->where(function ($query) use ($user, $allNormalUsers, $departmentIds, $currentFinancialYear) {
                $query->where('user_id', $user->id)
                      ->orWhereIn('user_id', $allNormalUsers)
                      ->orWhereIn('department_id', $departmentIds);
            })
            ->whereHas('service') 
            ->select('id')
            ->get();
        }
       

       $nv_ids = $nv->pluck('id');
        if(!empty($dep_rew1)){
            if($company_id){
                $nv_sm_data = Nvsericestatus::whereIn('nv_id', $nv_ids)->with(['service', 'material', 'user'])->where('rv1_status',1)->where('company_id',$company_id)->orderBy('id', 'asc');
            } else {
                $nv_sm_data = Nvsericestatus::whereIn('nv_id', $nv_ids)->with(['service', 'material', 'user'])->where('rv1_status',1)->orderBy('id', 'asc');
            }
    
        }else{
            if($company_id){
                $nv_sm_data = Nvsericestatus::whereIn('nv_id', $nv_ids)->with(['service', 'material', 'user'])->where('draft',1)->where('company_id',$company_id)->orderBy('id', 'asc');
            } else {
                $nv_sm_data = Nvsericestatus::whereIn('nv_id', $nv_ids)->with(['service', 'material', 'user'])->where('draft',1)->orderBy('id', 'asc');
            }
        }
       
        $total4_status = $request->total;
        $pending4_status = $request->pending;
        $reject4_status = $request->rejected;

        if ($ceo_status !== null) {
            $nv_sm_data = $nv_sm_data->where('ceo_status', $ceo_status);
        } elseif ($pending4_status !== null) {
            $nv_sm_data = $nv_sm_data->where('rv2_status', $pending4_status)
                ->whereIn('ceo_status', [0, 1])
                ->whereIn('cpmg_status', [0, 1])
                ->whereIn('cto_status', [0, 1])
                ->whereIn('ceo_nominee_status', [0, 1])    
                ->whereIn('ceo_nominee2_status', [0, 1]);
        } elseif ($reject4_status !== null) {
            $nv_sm_data = $nv_sm_data->where('rv2_status', $reject4_status)
                ->orWhere('hod_status', [2])
                ->orWhere('cpmg_status', [2])
                ->orWhere('cto_status', [2])
                ->orWhere('ceo_nominee_status', [2])
                ->orWhere('ceo_nominee2_status', [2]);
        }elseif ($total4_status !== null) {
            $nv_sm_data = $nv_sm_data->where('rv2_status', $total4_status);
              
        }
        $nv_sm_data = $nv_sm_data->get();
        if($fiscal_year){
            $BRPLnv = NeedValidation::whereIn("department_id", $departmentIds)->where('company_id','6')->where('fiscal_year', $fiscal_year)->pluck("id");

        }else{
            $BRPLnv = NeedValidation::whereIn("department_id", $departmentIds)->where('company_id','6')->where('fiscal_year', $currentFinancialYear)->pluck("id");

        }
        if(!empty($dep_rew1)){
            $pendingAmountBRPL = DB::table('tbl_material')
               ->join('nvservicestatus', 'tbl_material.id', '=', 'nvservicestatus.material_id')
            ->whereIn('tbl_material.nv_id', $BRPLnv)
            ->where('nvservicestatus.rv2_status', 0)
            ->where('nvservicestatus.rv1_status', 1)
            ->sum('tbl_material.total_budget_both')+DB::table('tbl_service')
                ->join('nvservicestatus', 'tbl_service.id', '=', 'nvservicestatus.service_id')
            ->whereIn('tbl_service.nv_id', $BRPLnv)
            ->where('nvservicestatus.rv2_status', 0)
            ->where('nvservicestatus.rv1_status', 1)
            ->sum('tbl_service.total_buget');
        }else{
            $pendingAmountBRPL = DB::table('tbl_material')
               ->join('nvservicestatus', 'tbl_material.id', '=', 'nvservicestatus.material_id')
            ->whereIn('tbl_material.nv_id', $BRPLnv)
            ->where('nvservicestatus.rv2_status', 0)
            ->where('nvservicestatus.draft', 1)
            ->sum('tbl_material.total_budget_both')+DB::table('tbl_service')
                ->join('nvservicestatus', 'tbl_service.id', '=', 'nvservicestatus.service_id')
            ->whereIn('tbl_service.nv_id', $BRPLnv)
            ->where('nvservicestatus.rv2_status', 0)
            ->where('nvservicestatus.draft', 1)
            ->sum('tbl_service.total_buget');
        }
      

        $rejectedAmountBRPL = DB::table('tbl_material')
           ->join('nvservicestatus', 'tbl_material.id', '=', 'nvservicestatus.material_id')
        ->whereIn('tbl_material.nv_id', $BRPLnv)
        ->where(function ($query) {
            $query->where('nvservicestatus.rv2_status', 2);
        })
        ->sum('tbl_material.total_budget_both')+DB::table('tbl_service')
            ->join('nvservicestatus', 'tbl_service.id', '=', 'nvservicestatus.service_id')
        ->whereIn('tbl_service.nv_id', $BRPLnv)
        ->where(function ($query) {
            $query->where('nvservicestatus.rv2_status', 2);
        })
        ->sum('tbl_service.total_buget');

      $approvedAmountBRPL = DB::table('tbl_material')
           ->join('nvservicestatus', 'tbl_material.id', '=', 'nvservicestatus.material_id')
        ->whereIn('tbl_material.nv_id', $BRPLnv)->where('nvservicestatus.ceo_status', 1)
        ->sum('tbl_material.total_budget_both')+DB::table('tbl_service')
            ->join('nvservicestatus', 'tbl_service.id', '=', 'nvservicestatus.service_id')
        ->whereIn('tbl_service.nv_id', $BRPLnv)->where('nvservicestatus.ceo_status', 1)
        ->sum('tbl_service.total_buget');

        if(!empty($dep_rew1)){
            $fileDataBRPL =Nvsericestatus::
            select(
                DB::raw('MONTH(created_at) as month'),
                DB::raw('SUM(CASE WHEN ceo_status = "1" THEN 1 ELSE 0 END) as approved_count'),
                DB::raw('SUM(CASE WHEN ( rv2_status = "2")  THEN 1 ELSE 0 END ) as rejected_count'),
                DB::raw('SUM(CASE WHEN (rv1_status = "1" AND rv2_status = "0" ) THEN 1 ELSE 0 END) as pending_count'),
            )
         
            ->whereIn('nv_id', $BRPLnv)->with(['service', 'material', 'user'])
            ->whereYear('created_at', Carbon::now()->year)
            ->groupBy('month')
            ->orderBy('month')
            ->get();
        }else{
            $fileDataBRPL =Nvsericestatus::
            select(
                DB::raw('MONTH(created_at) as month'),
                DB::raw('SUM(CASE WHEN ceo_status = "1" THEN 1 ELSE 0 END) as approved_count'),
                DB::raw('SUM(CASE WHEN ( rv2_status = "2")  THEN 1 ELSE 0 END ) as rejected_count'),
                DB::raw('SUM(CASE WHEN (draft = "1" AND rv2_status = "0" ) THEN 1 ELSE 0 END) as pending_count'),
            )
         
            ->whereIn('nv_id', $BRPLnv)->with(['service', 'material', 'user'])
            ->whereYear('created_at', Carbon::now()->year)
            ->groupBy('month')
            ->orderBy('month')
            ->get();

        }
       
    $BRPLlabels = [];
    $BRPLapprovedData = [];
    $BRPLrejectedData = [];
    $BRPLpendingData = [];

    foreach ($fileDataBRPL as $dataPointBRPL) {
        $monthBRPL = Carbon::createFromFormat('!m', $dataPointBRPL->month)->format('F');

        $BRPLlabels[] = $monthBRPL;
        $BRPLapprovedData[] = $dataPointBRPL->approved_count;
        $BRPLrejectedData[] = $dataPointBRPL->rejected_count;
        $BRPLpendingData[] = $dataPointBRPL->pending_count;
    }
if($fiscal_year){
    $BYPLnv = NeedValidation::whereIn("department_id", $departmentIds)->where('company_id','5')->where('fiscal_year', $fiscal_year)->pluck("id");

}else{
    $BYPLnv = NeedValidation::whereIn("department_id", $departmentIds)->where('company_id','5')->where('fiscal_year', $currentFinancialYear)->pluck("id");

}
    if(!empty($dep_rew1)){
        $pendingAmountBYPL = DB::table('tbl_material')
           ->join('nvservicestatus', 'tbl_material.id', '=', 'nvservicestatus.material_id')
        ->whereIn('tbl_material.nv_id', $BYPLnv)
        ->where('nvservicestatus.rv2_status', 0)
        ->where('nvservicestatus.rv1_status', 1)
        ->sum('tbl_material.total_budget_both')+DB::table('tbl_service')
            ->join('nvservicestatus', 'tbl_service.id', '=', 'nvservicestatus.service_id')
        ->whereIn('tbl_service.nv_id', $BYPLnv)
        ->where('nvservicestatus.rv2_status', 0)
        ->where('nvservicestatus.rv1_status', 1)
        ->sum('tbl_service.total_buget');
    }else{
        $pendingAmountBYPL = DB::table('tbl_material')
           ->join('nvservicestatus', 'tbl_material.id', '=', 'nvservicestatus.material_id')
        ->whereIn('tbl_material.nv_id', $BYPLnv)
        ->where('nvservicestatus.rv2_status', 0)
        ->where('nvservicestatus.draft', 1)
        ->sum('tbl_material.total_budget_both')+DB::table('tbl_service')
            ->join('nvservicestatus', 'tbl_service.id', '=', 'nvservicestatus.service_id')
        ->whereIn('tbl_service.nv_id', $BYPLnv)
        ->where('nvservicestatus.rv2_status', 0)
        ->where('nvservicestatus.draft', 1)
        ->sum('tbl_service.total_buget');
    }
 

    $rejectedAmountBYPL = DB::table('tbl_material')
       ->join('nvservicestatus', 'tbl_material.id', '=', 'nvservicestatus.material_id')
    ->whereIn('tbl_material.nv_id', $BYPLnv)
    ->where(function ($query) {
        $query->where('nvservicestatus.rv2_status', 2);
    })
    ->sum('tbl_material.total_budget_both')+DB::table('tbl_service')
        ->join('nvservicestatus', 'tbl_service.id', '=', 'nvservicestatus.service_id')
    ->whereIn('tbl_service.nv_id', $BYPLnv)
    ->where(function ($query) {
        $query->where('nvservicestatus.rv2_status', 2);
    })
    ->sum('tbl_service.total_buget');

  $approvedAmountBYPL = DB::table('tbl_material')
       ->join('nvservicestatus', 'tbl_material.id', '=', 'nvservicestatus.material_id')
    ->whereIn('tbl_material.nv_id', $BYPLnv)->where('nvservicestatus.ceo_status', 1)
    ->sum('tbl_material.total_budget_both')+DB::table('tbl_service')
        ->join('nvservicestatus', 'tbl_service.id', '=', 'nvservicestatus.service_id')
    ->whereIn('tbl_service.nv_id', $BYPLnv)->where('nvservicestatus.ceo_status', 1)
    ->sum('tbl_service.total_buget');
    if(!empty($dep_rew1)){
        $fileDataBYPL =Nvsericestatus::
        select(
            DB::raw('MONTH(created_at) as month'),
            DB::raw('SUM(CASE WHEN ceo_status = "1" THEN 1 ELSE 0 END) as approved_count'),
            DB::raw('SUM(CASE WHEN ( rv2_status = "2")  THEN 1 ELSE 0 END) as rejected_count'),
            DB::raw('SUM(CASE WHEN (rv1_status = "1" AND rv2_status = "0" )  THEN 1 ELSE 0 END) as pending_count'),
        ) 
        ->whereIn('nv_id', $BYPLnv)->with(['service', 'material', 'user'])
        ->whereYear('created_at', Carbon::now()->year)
        ->groupBy('month')
        ->orderBy('month')
        ->get();
    }else{
        $fileDataBYPL =Nvsericestatus::
        select(
            DB::raw('MONTH(created_at) as month'),
            DB::raw('SUM(CASE WHEN ceo_status = "1" THEN 1 ELSE 0 END) as approved_count'),
            DB::raw('SUM(CASE WHEN ( rv2_status = "2")  THEN 1 ELSE 0 END) as rejected_count'),
            DB::raw('SUM(CASE WHEN (draft = "1" AND rv2_status = "0" )  THEN 1 ELSE 0 END) as pending_count'),
        ) 
        ->whereIn('nv_id', $BYPLnv)->with(['service', 'material', 'user'])
        ->whereYear('created_at', Carbon::now()->year)
        ->groupBy('month')
        ->orderBy('month')
        ->get();

    }
$BYPLlabels = [];
$BYPLapprovedData = [];
$BYPLrejectedData = [];
$BYPLpendingData = [];

foreach ($fileDataBYPL as $dataPointBYPL) {
    $monthBYPL = Carbon::createFromFormat('!m', $dataPointBYPL->month)->format('F');

    $BYPLlabels[] = $monthBYPL;
    $BYPLapprovedData[] = $dataPointBYPL->approved_count;
    $BYPLrejectedData[] = $dataPointBYPL->rejected_count;
    $BYPLpendingData[] = $dataPointBYPL->pending_count;
 }

        return view("admin.dashboard", compact("approvedAmount" ,"currentFinancialYear","nextFinancialYear","nextToNextFinancialYear","rejectedAmount", "pendingAmount", "totalAmount","company", "company_id" , "nv_sm_data", "totalNV", "approvedNV", "rejectedNV", "pendingNV" , "hodApproval", "cpmgApproval", "btApproval", "ceoApproval"             ,'BRPLlabels','BRPLapprovedData','BRPLrejectedData','BRPLpendingData','BYPLlabels','BYPLapprovedData','BYPLrejectedData','BYPLpendingData','approvedAmountBYPL','approvedAmountBRPL','rejectedAmountBYPL','rejectedAmountBRPL','pendingAmountBYPL','pendingAmountBRPL'));
    } elseif ($dep_rew3 == $user->id) {
        $Values = [$user->id, $dep_rew1, $dep_rew2];
        $departmentIds = explode(',', $user->department_id);
        if($fiscal_year){
            $totalId = NeedValidation::where('fiscal_year', $fiscal_year)
            ->where(function ($query) use ($Values, $allNormalUsers, $departmentIds) {
                $query->whereIn('user_id', $Values)
                      ->orWhereIn('user_id', $allNormalUsers)
                      ->orWhereIn('department_id', $departmentIds);
            })
            ->pluck('id');
        }else{
            $totalId = NeedValidation::where('fiscal_year', $currentFinancialYear)
            ->where(function ($query) use ($Values, $allNormalUsers, $departmentIds) {
                $query->whereIn('user_id', $Values)
                      ->orWhereIn('user_id', $allNormalUsers)
                      ->orWhereIn('department_id', $departmentIds);
            })
            ->pluck('id');
        }
      
    if($company_id){
        $latestData = Nvsericestatus::whereIn("nv_id", $totalId)->where('company_id',$company_id)->get();
    }else {
        $latestData = Nvsericestatus::whereIn("nv_id", $totalId)->get();
    }

    $totalAmount = DB::table('tbl_material')
       ->join('nvservicestatus', 'tbl_material.id', '=', 'nvservicestatus.material_id')
     ->whereIn('tbl_material.nv_id', $totalId)
     ->whereIn('nvservicestatus.nv_id', $latestData->pluck('nv_id'))
    ->where('nvservicestatus.rv3_status', 1)
    ->sum('tbl_material.total_budget_both') +  DB::table('tbl_service')
        ->join('nvservicestatus', 'tbl_service.id', '=', 'nvservicestatus.service_id')
     ->whereIn('tbl_service.nv_id', $totalId)
     ->whereIn('nvservicestatus.nv_id', $latestData->pluck('nv_id'))
    ->where('nvservicestatus.rv3_status', 1)
    ->sum('tbl_service.total_buget');
  
    if(!empty($dep_rew2)){
        $pendingAmount = DB::table('tbl_material')
           ->join('nvservicestatus', 'tbl_material.id', '=', 'nvservicestatus.material_id')
         ->whereIn('tbl_material.nv_id', $totalId)
         ->whereIn('nvservicestatus.nv_id', $latestData->pluck('nv_id'))
        ->where('nvservicestatus.rv3_status', 0)
        ->where('nvservicestatus.rv2_status', 1)
        ->sum('tbl_material.total_budget_both') + DB::table('tbl_service')
            ->join('nvservicestatus', 'tbl_service.id', '=', 'nvservicestatus.service_id')
         ->whereIn('tbl_service.nv_id', $totalId)
         ->whereIn('nvservicestatus.nv_id', $latestData->pluck('nv_id'))
        ->where('nvservicestatus.rv3_status', 0)
        ->where('nvservicestatus.rv2_status', 1)
        ->sum('tbl_service.total_buget');
    }elseif(!empty($dep_rew1)){
        $pendingAmount = DB::table('tbl_material')
           ->join('nvservicestatus', 'tbl_material.id', '=', 'nvservicestatus.material_id')
         ->whereIn('tbl_material.nv_id', $totalId)
         ->whereIn('nvservicestatus.nv_id', $latestData->pluck('nv_id'))
        ->where('nvservicestatus.rv3_status', 0)
        ->where('nvservicestatus.rv1_status', 1)
        ->sum('tbl_material.total_budget_both') + DB::table('tbl_service')
            ->join('nvservicestatus', 'tbl_service.id', '=', 'nvservicestatus.service_id')
         ->whereIn('tbl_service.nv_id', $totalId)
         ->whereIn('nvservicestatus.nv_id', $latestData->pluck('nv_id'))
        ->where('nvservicestatus.rv3_status', 0)
        ->where('nvservicestatus.rv1_status', 1)
        ->sum('tbl_service.total_buget');
    }else{
        $pendingAmount = DB::table('tbl_material')
           ->join('nvservicestatus', 'tbl_material.id', '=', 'nvservicestatus.material_id')
         ->whereIn('tbl_material.nv_id', $totalId)
         ->whereIn('nvservicestatus.nv_id', $latestData->pluck('nv_id'))
        ->where('nvservicestatus.rv3_status', 0)
        ->where('nvservicestatus.draft', 1)
        ->sum('tbl_material.total_budget_both') + DB::table('tbl_service')
            ->join('nvservicestatus', 'tbl_service.id', '=', 'nvservicestatus.service_id')
         ->whereIn('tbl_service.nv_id', $totalId)
         ->whereIn('nvservicestatus.nv_id', $latestData->pluck('nv_id'))
        ->where('nvservicestatus.rv3_status', 0)
        ->where('nvservicestatus.draft', 1)
        ->sum('tbl_service.total_buget');
    }
   

    $rejectedAmount = DB::table('tbl_material')
       ->join('nvservicestatus', 'tbl_material.id', '=', 'nvservicestatus.material_id')
       ->whereIn('tbl_material.nv_id', $totalId)
    ->whereIn('nvservicestatus.nv_id', $latestData->pluck('nv_id'))
    ->where(function ($query) {
        $query->where('nvservicestatus.rv3_status', 2);
    })
    ->sum('tbl_material.total_budget_both') + DB::table('tbl_service')
        ->join('nvservicestatus', 'tbl_service.id', '=', 'nvservicestatus.service_id')
        ->whereIn('tbl_service.nv_id', $totalId)
    ->whereIn('nvservicestatus.nv_id', $latestData->pluck('nv_id'))
    ->where(function ($query) {
        $query->where('nvservicestatus.rv3_status', 2);
    })
    ->sum('tbl_service.total_buget');


    $approvedAmount = DB::table('tbl_material')
     ->whereIn('tbl_material.nv_id', $totalId)
     ->whereIn('nvservicestatus.nv_id', $latestData->pluck('nv_id'))
       ->join('nvservicestatus', 'tbl_material.id', '=', 'nvservicestatus.material_id')
    ->where('nvservicestatus.ceo_status', 1)
    ->sum('tbl_material.total_budget_both') + DB::table('tbl_service')
    ->whereIn('tbl_service.nv_id', $totalId)
    ->whereIn('nvservicestatus.nv_id', $latestData->pluck('nv_id'))
       ->join('nvservicestatus', 'tbl_service.id', '=', 'nvservicestatus.service_id')
   ->where('nvservicestatus.ceo_status', 1)
   ->sum('tbl_service.total_buget');

        if($company_id){
            $approvedNV = $latestData->where('ceo_status', 1)->where('company_id',$company_id)->count();
        }else{
            $approvedNV = $latestData->where('ceo_status', 1)->count();
        }
        $rejectedNV = $latestData->filter(function ($data) {
            return in_array($data->ceo_status, [2]) || 
            $data->rv3_status == 2 ;
        })->count();
        if(!empty($dep_rew2)){
            $pendingNV = $latestData->filter(function ($data) {
                return  in_array($data->rv2_status, [1]) &&
                    in_array($data->rv3_status, [0]);
            })->count();
        }elseif(!empty($dep_rew1)){
            $pendingNV = $latestData->filter(function ($data) {
                return in_array($data->rv1_status, [1]) &&
                    in_array($data->rv3_status, [0]);
            })->count();
        
        }else{
            $pendingNV = $latestData->filter(function ($data) {
                return  in_array($data->draft, [1]) &&
                    in_array($data->rv3_status, [0]);
            })->count();
        }
      
        if($company_id){
            $totalNV = Nvsericestatus::whereIn("nv_id", $totalId)->where('rv3_status', 1)->where('company_id',$company_id)->count();
        } else {
            $totalNV = Nvsericestatus::whereIn("nv_id", $totalId)->where('rv3_status', 1)->count();
        }
         
       
        
        $nvIds = NeedValidation::whereIn("user_id", $Values)->pluck("id");
        if($fiscal_year){
            $nv = NeedValidation::where('fiscal_year', $fiscal_year)->where(function ($query) use ($user, $allNormalUsers, $departmentIds, $currentFinancialYear) {
                $query->where('user_id', $user->id)
                      ->orWhereIn('user_id', $allNormalUsers)
                      ->orWhereIn('department_id', $departmentIds);
            })
            ->whereHas('service') 
            ->select('id')
            ->get();
        }else{
            $nv = NeedValidation::where('fiscal_year', $currentFinancialYear)->where(function ($query) use ($user, $allNormalUsers, $departmentIds, $currentFinancialYear) {
                $query->where('user_id', $user->id)
                      ->orWhereIn('user_id', $allNormalUsers)
                      ->orWhereIn('department_id', $departmentIds);
            })
            ->whereHas('service') 
            ->select('id')
            ->get();
        }
      
        $nv_ids = $nv->pluck('id');
        if(!empty($dep_rew2)){
          
                $nv_sm_data = Nvsericestatus::whereIn('nv_id', $nv_ids)->with(['service', 'material', 'user'])
            ->where('rv2_status',1)
            ->orderBy('id', 'asc');
         
        }elseif(!empty($dep_rew1)){
           
                $nv_sm_data = Nvsericestatus::whereIn('nv_id', $nv_ids)->with(['service', 'material', 'user'])
            ->where('rv1_status',1)
            ->orderBy('id', 'asc');
          
        
        }else{
          
            $nv_sm_data = Nvsericestatus::whereIn('nv_id', $nv_ids)->with(['service', 'material', 'user'])
            ->where('draft',1)
            ->orderBy('id', 'asc');
          
        }
     
        $total5_status = $request->total;
        $pending5_status = $request->pending;
        $reject5_status = $request->rejected;

       
      
       
        if ($ceo_status !== null) {
            $nv_sm_data = $nv_sm_data->where('ceo_status', $ceo_status);
        } elseif ($pending5_status !== null) {
            $nv_sm_data = $nv_sm_data->where('rv3_status', $pending5_status)
                ->whereIn('ceo_status', [0, 1])
                ->whereIn('cpmg_status', [0, 1])
                ->whereIn('cto_status', [0, 1])
                ->whereIn('ceo_nominee_status', [0, 1])    
                ->whereIn('ceo_nominee2_status', [0, 1]);
        } elseif ($reject5_status !== null) {
            $nv_sm_data = $nv_sm_data->where('rv3_status', $reject5_status)
                ->orWhere('hod_status', [2])
                ->orWhere('cpmg_status', [2])
                ->orWhere('cto_status', [2])
                ->orWhere('ceo_nominee_status', [2])
                ->orWhere('ceo_nominee2_status', [2]);
        }elseif ($total5_status !== null) {
            $nv_sm_data = $nv_sm_data->where('rv3_status', $total5_status);
              
        }
        $nv_sm_data = $nv_sm_data->get();
        if($fiscal_year){
            $BRPLnv = NeedValidation::whereIn("department_id", $departmentIds)->where('company_id','6')->where('fiscal_year', $fiscal_year)->pluck("id");

        }else{
            $BRPLnv = NeedValidation::whereIn("department_id", $departmentIds)->where('company_id','6')->where('fiscal_year', $currentFinancialYear)->pluck("id");

        }
        if(!empty($dep_rew2)){
            $pendingAmountBRPL = DB::table('tbl_material')
           ->join('nvservicestatus', 'tbl_material.id', '=', 'nvservicestatus.material_id')
        ->whereIn('tbl_material.nv_id', $BRPLnv)
        ->where('nvservicestatus.rv3_status', 0)
        ->where('nvservicestatus.rv2_status', 1)
        ->sum('tbl_material.total_budget_both')+DB::table('tbl_service')
            ->join('nvservicestatus', 'tbl_service.id', '=', 'nvservicestatus.service_id')
        ->whereIn('tbl_service.nv_id', $BRPLnv)
        ->where('nvservicestatus.rv3_status', 0)
        ->where('nvservicestatus.rv2_status', 1)
        ->sum('tbl_service.total_buget');
        }elseif(!empty($dep_rew1)){
            $pendingAmountBRPL = DB::table('tbl_material')
               ->join('nvservicestatus', 'tbl_material.id', '=', 'nvservicestatus.material_id')
            ->whereIn('tbl_material.nv_id', $BRPLnv)
            ->where('nvservicestatus.rv3_status', 0)
            ->where('nvservicestatus.rv1_status', 1)
            ->sum('tbl_material.total_budget_both')+DB::table('tbl_service')
                ->join('nvservicestatus', 'tbl_service.id', '=', 'nvservicestatus.service_id')
            ->whereIn('tbl_service.nv_id', $BRPLnv)
            ->where('nvservicestatus.rv3_status', 0)
            ->where('nvservicestatus.rv1_status', 1)
            ->sum('tbl_service.total_buget');
        
        }else{
            $pendingAmountBRPL = DB::table('tbl_material')
           ->join('nvservicestatus', 'tbl_material.id', '=', 'nvservicestatus.material_id')
        ->whereIn('tbl_material.nv_id', $BRPLnv)
        ->where('nvservicestatus.rv3_status', 0)
        ->where('nvservicestatus.draft', 1)
        ->sum('tbl_material.total_budget_both')+DB::table('tbl_service')
            ->join('nvservicestatus', 'tbl_service.id', '=', 'nvservicestatus.service_id')
        ->whereIn('tbl_service.nv_id', $BRPLnv)
        ->where('nvservicestatus.rv3_status', 0)
        ->where('nvservicestatus.draft', 1)
        ->sum('tbl_service.total_buget');
        }
      

        $rejectedAmountBRPL = DB::table('tbl_material')
           ->join('nvservicestatus', 'tbl_material.id', '=', 'nvservicestatus.material_id')
        ->whereIn('tbl_material.nv_id', $BRPLnv)
        ->where(function ($query) {
            $query->where('nvservicestatus.rv3_status', 2);
        })
        ->sum('tbl_material.total_budget_both')+DB::table('tbl_service')
            ->join('nvservicestatus', 'tbl_service.id', '=', 'nvservicestatus.service_id')
        ->whereIn('tbl_service.nv_id', $BRPLnv)
        ->where(function ($query) {
            $query->where('nvservicestatus.rv3_status', 2);
        })
        ->sum('tbl_service.total_buget');

      $approvedAmountBRPL = DB::table('tbl_material')
           ->join('nvservicestatus', 'tbl_material.id', '=', 'nvservicestatus.material_id')
        ->whereIn('tbl_material.nv_id', $BRPLnv)->where('nvservicestatus.ceo_status', 1)
        ->sum('tbl_material.total_budget_both')+DB::table('tbl_service')
            ->join('nvservicestatus', 'tbl_service.id', '=', 'nvservicestatus.service_id')
        ->whereIn('tbl_service.nv_id', $BRPLnv)->where('nvservicestatus.ceo_status', 1)
        ->sum('tbl_service.total_buget');
        if(!empty($dep_rew2)){
            $fileDataBRPL =Nvsericestatus::
            select(
                DB::raw('MONTH(created_at) as month'),
                DB::raw('SUM(CASE WHEN ceo_status = "1" THEN 1 ELSE 0 END) as approved_count'),
                DB::raw('SUM(CASE WHEN ( rv3_status = "2")  THEN 1 ELSE 0 END ) as rejected_count'),
                DB::raw('SUM(CASE WHEN (rv2_status = "1" AND rv3_status = "0" ) THEN 1 ELSE 0 END) as pending_count'),
            )
         
            ->whereIn('nv_id', $BRPLnv)->with(['service', 'material', 'user'])
            ->whereYear('created_at', Carbon::now()->year)
            ->groupBy('month')
            ->orderBy('month')
            ->get();
        }elseif(!empty($dep_rew1)){
            $fileDataBRPL =Nvsericestatus::
            select(
                DB::raw('MONTH(created_at) as month'),
                DB::raw('SUM(CASE WHEN ceo_status = "1" THEN 1 ELSE 0 END) as approved_count'),
                DB::raw('SUM(CASE WHEN ( rv3_status = "2")  THEN 1 ELSE 0 END ) as rejected_count'),
                DB::raw('SUM(CASE WHEN (rv1_status = "1" AND rv3_status = "0" ) THEN 1 ELSE 0 END) as pending_count'),
            )
         
            ->whereIn('nv_id', $BRPLnv)->with(['service', 'material', 'user'])
            ->whereYear('created_at', Carbon::now()->year)
            ->groupBy('month')
            ->orderBy('month')
            ->get();
        
        }else{
            $fileDataBRPL =Nvsericestatus::
        select(
            DB::raw('MONTH(created_at) as month'),
            DB::raw('SUM(CASE WHEN ceo_status = "1" THEN 1 ELSE 0 END) as approved_count'),
            DB::raw('SUM(CASE WHEN ( rv3_status = "2")  THEN 1 ELSE 0 END ) as rejected_count'),
            DB::raw('SUM(CASE WHEN (draft = "1" AND rv3_status = "0" ) THEN 1 ELSE 0 END) as pending_count'),
        )
     
        ->whereIn('nv_id', $BRPLnv)->with(['service', 'material', 'user'])
        ->whereYear('created_at', Carbon::now()->year)
        ->groupBy('month')
        ->orderBy('month')
        ->get();
        }
      
    $BRPLlabels = [];
    $BRPLapprovedData = [];
    $BRPLrejectedData = [];
    $BRPLpendingData = [];

    foreach ($fileDataBRPL as $dataPointBRPL) {
        $monthBRPL = Carbon::createFromFormat('!m', $dataPointBRPL->month)->format('F');

        $BRPLlabels[] = $monthBRPL;
        $BRPLapprovedData[] = $dataPointBRPL->approved_count;
        $BRPLrejectedData[] = $dataPointBRPL->rejected_count;
        $BRPLpendingData[] = $dataPointBRPL->pending_count;
    }
if($fiscal_year){
    $BYPLnv = NeedValidation::whereIn("department_id", $departmentIds)->where('company_id','5')->where('fiscal_year', $fiscal_year)->pluck("id");

}else{
    $BYPLnv = NeedValidation::whereIn("department_id", $departmentIds)->where('company_id','5')->where('fiscal_year', $currentFinancialYear)->pluck("id");

}
    if(!empty($dep_rew2)){
        $pendingAmountBYPL = DB::table('tbl_material')
           ->join('nvservicestatus', 'tbl_material.id', '=', 'nvservicestatus.material_id')
        ->whereIn('tbl_material.nv_id', $BYPLnv)
        ->where('nvservicestatus.rv3_status', 0)
        ->where('nvservicestatus.rv2_status', 1)
        ->sum('tbl_material.total_budget_both')+DB::table('tbl_service')
            ->join('nvservicestatus', 'tbl_service.id', '=', 'nvservicestatus.service_id')
        ->whereIn('tbl_service.nv_id', $BYPLnv)
        ->where('nvservicestatus.rv3_status', 0)
        ->where('nvservicestatus.rv2_status', 1)
        ->sum('tbl_service.total_buget');
    }elseif(!empty($dep_rew1)){
        $pendingAmountBYPL = DB::table('tbl_material')
       ->join('nvservicestatus', 'tbl_material.id', '=', 'nvservicestatus.material_id')
    ->whereIn('tbl_material.nv_id', $BYPLnv)
    ->where('nvservicestatus.rv3_status', 0)
    ->where('nvservicestatus.rv1_status', 1)
    ->sum('tbl_material.total_budget_both')+DB::table('tbl_service')
        ->join('nvservicestatus', 'tbl_service.id', '=', 'nvservicestatus.service_id')
    ->whereIn('tbl_service.nv_id', $BYPLnv)
    ->where('nvservicestatus.rv3_status', 0)
    ->where('nvservicestatus.rv1_status', 1)
    ->sum('tbl_service.total_buget');
    
    }else{
        $pendingAmountBYPL = DB::table('tbl_material')
           ->join('nvservicestatus', 'tbl_material.id', '=', 'nvservicestatus.material_id')
        ->whereIn('tbl_material.nv_id', $BYPLnv)
        ->where('nvservicestatus.rv3_status', 0)
        ->where('nvservicestatus.draft', 1)
        ->sum('tbl_material.total_budget_both')+DB::table('tbl_service')
            ->join('nvservicestatus', 'tbl_service.id', '=', 'nvservicestatus.service_id')
        ->whereIn('tbl_service.nv_id', $BYPLnv)
        ->where('nvservicestatus.rv3_status', 0)
        ->where('nvservicestatus.draft', 1)
        ->sum('tbl_service.total_buget');
    }
 

    $rejectedAmountBYPL = DB::table('tbl_material')
       ->join('nvservicestatus', 'tbl_material.id', '=', 'nvservicestatus.material_id')
    ->whereIn('tbl_material.nv_id', $BYPLnv)
    ->where(function ($query) {
        $query->where('nvservicestatus.rv3_status', 2);
    })
    ->sum('tbl_material.total_budget_both')+DB::table('tbl_service')
        ->join('nvservicestatus', 'tbl_service.id', '=', 'nvservicestatus.service_id')
    ->whereIn('tbl_service.nv_id', $BYPLnv)
    ->where(function ($query) {
        $query->where('nvservicestatus.rv3_status', 2);
    })
    ->sum('tbl_service.total_buget');

  $approvedAmountBYPL = DB::table('tbl_material')
       ->join('nvservicestatus', 'tbl_material.id', '=', 'nvservicestatus.material_id')
    ->whereIn('tbl_material.nv_id', $BYPLnv)->where('nvservicestatus.ceo_status', 1)
    ->sum('tbl_material.total_budget_both')+DB::table('tbl_service')
        ->join('nvservicestatus', 'tbl_service.id', '=', 'nvservicestatus.service_id')
    ->whereIn('tbl_service.nv_id', $BYPLnv)->where('nvservicestatus.ceo_status', 1)
    ->sum('tbl_service.total_buget');
    if(!empty($dep_rew2)){
        $fileDataBYPL =Nvsericestatus::
        select(
            DB::raw('MONTH(created_at) as month'),
            DB::raw('SUM(CASE WHEN ceo_status = "1" THEN 1 ELSE 0 END) as approved_count'),
            DB::raw('SUM(CASE WHEN ( rv3_status = "2")  THEN 1 ELSE 0 END) as rejected_count'),
            DB::raw('SUM(CASE WHEN (rv2_status = "1" AND rv3_status = "0" )  THEN 1 ELSE 0 END) as pending_count'),
        ) 
        ->whereIn('nv_id', $BYPLnv)->with(['service', 'material', 'user'])
        ->whereYear('created_at', Carbon::now()->year)
        ->groupBy('month')
        ->orderBy('month')
        ->get();
    }elseif(!empty($dep_rew1)){
        $fileDataBYPL =Nvsericestatus::
        select(
            DB::raw('MONTH(created_at) as month'),
            DB::raw('SUM(CASE WHEN ceo_status = "1" THEN 1 ELSE 0 END) as approved_count'),
            DB::raw('SUM(CASE WHEN ( rv3_status = "2")  THEN 1 ELSE 0 END) as rejected_count'),
            DB::raw('SUM(CASE WHEN (rv1_status = "1" AND rv3_status = "0" )  THEN 1 ELSE 0 END) as pending_count'),
        ) 
        ->whereIn('nv_id', $BYPLnv)->with(['service', 'material', 'user'])
        ->whereYear('created_at', Carbon::now()->year)
        ->groupBy('month')
        ->orderBy('month')
        ->get();
    
    }else{
        $fileDataBYPL =Nvsericestatus::
    select(
        DB::raw('MONTH(created_at) as month'),
        DB::raw('SUM(CASE WHEN ceo_status = "1" THEN 1 ELSE 0 END) as approved_count'),
        DB::raw('SUM(CASE WHEN ( rv3_status = "2")  THEN 1 ELSE 0 END) as rejected_count'),
        DB::raw('SUM(CASE WHEN (draft = "1" AND rv3_status = "0" )  THEN 1 ELSE 0 END) as pending_count'),
    ) 
    ->whereIn('nv_id', $BYPLnv)->with(['service', 'material', 'user'])
    ->whereYear('created_at', Carbon::now()->year)
    ->groupBy('month')
    ->orderBy('month')
    ->get();
    }
$BYPLlabels = [];
$BYPLapprovedData = [];
$BYPLrejectedData = [];
$BYPLpendingData = [];

foreach ($fileDataBYPL as $dataPointBYPL) {
    $monthBYPL = Carbon::createFromFormat('!m', $dataPointBYPL->month)->format('F');

    $BYPLlabels[] = $monthBYPL;
    $BYPLapprovedData[] = $dataPointBYPL->approved_count;
    $BYPLrejectedData[] = $dataPointBYPL->rejected_count;
    $BYPLpendingData[] = $dataPointBYPL->pending_count;
 }
        return view("admin.dashboard", compact("approvedAmount","currentFinancialYear","nextFinancialYear","nextToNextFinancialYear", "rejectedAmount", "pendingAmount", "totalAmount","company", "company_id" , "nv_sm_data", "totalNV", "approvedNV", "rejectedNV", "pendingNV" , "hodApproval", "cpmgApproval", "btApproval", "ceoApproval"             ,'BRPLlabels','BRPLapprovedData','BRPLrejectedData','BRPLpendingData','BYPLlabels','BYPLapprovedData','BYPLrejectedData','BYPLpendingData','approvedAmountBYPL','approvedAmountBRPL','rejectedAmountBYPL','rejectedAmountBRPL','pendingAmountBYPL','pendingAmountBRPL'));
    } elseif ($dep_rew4 == $user->id) {
        $Values = [$user->id, $dep_rew1, $dep_rew2, $dep_rew3];
        $departmentIds = explode(',', $user->department_id);
        if($fiscal_year){
            $totalId = NeedValidation::where('fiscal_year', $fiscal_year)
            ->where(function ($query) use ($Values, $allNormalUsers, $departmentIds) {
                $query->whereIn('user_id', $Values)
                      ->orWhereIn('user_id', $allNormalUsers)
                      ->orWhereIn('department_id', $departmentIds);
            })
            ->pluck('id');
        }else{
            $totalId = NeedValidation::where('fiscal_year', $currentFinancialYear)
            ->where(function ($query) use ($Values, $allNormalUsers, $departmentIds) {
                $query->whereIn('user_id', $Values)
                      ->orWhereIn('user_id', $allNormalUsers)
                      ->orWhereIn('department_id', $departmentIds);
            })
            ->pluck('id');
        }
       
    if($company_id){
        $latestData = Nvsericestatus::whereIn("nv_id", $totalId)->where('company_id',$company_id)->get();
    }else {
        $latestData = Nvsericestatus::whereIn("nv_id", $totalId)->get();
    }

    $totalAmount = DB::table('tbl_material')
       ->join('nvservicestatus', 'tbl_material.id', '=', 'nvservicestatus.material_id')
     ->whereIn('tbl_material.nv_id', $totalId)
     ->whereIn('nvservicestatus.nv_id', $latestData->pluck('nv_id'))
    ->where('nvservicestatus.rv4_status', 1)
    ->sum('tbl_material.total_budget_both') + DB::table('tbl_service')
       ->join('nvservicestatus', 'tbl_service.id', '=', 'nvservicestatus.service_id')
     ->whereIn('tbl_service.nv_id', $totalId)
     ->whereIn('nvservicestatus.nv_id', $latestData->pluck('nv_id'))
    ->where('nvservicestatus.rv4_status', 1)
    ->sum('tbl_service.total_buget');
  
    if(!empty($dep_rew3)){
        $pendingAmount = DB::table('tbl_material')
           ->join('nvservicestatus', 'tbl_material.id', '=', 'nvservicestatus.material_id')
         ->whereIn('tbl_material.nv_id', $totalId)
         ->whereIn('nvservicestatus.nv_id', $latestData->pluck('nv_id'))
        ->where('nvservicestatus.rv4_status', 0)
        ->where('nvservicestatus.rv3_status', 1)
        ->sum('tbl_material.total_budget_both') +  DB::table('tbl_service')
           ->join('nvservicestatus', 'tbl_service.id', '=', 'nvservicestatus.service_id')
         ->whereIn('tbl_service.nv_id', $totalId)
         ->whereIn('nvservicestatus.nv_id', $latestData->pluck('nv_id'))
        ->where('nvservicestatus.rv4_status', 0)
        ->where('nvservicestatus.rv3_status', 1)
        ->sum('tbl_service.total_buget');
    }elseif(!empty($dep_rew2)){
        $pendingAmount = DB::table('tbl_material')
           ->join('nvservicestatus', 'tbl_material.id', '=', 'nvservicestatus.material_id')
         ->whereIn('tbl_material.nv_id', $totalId)
         ->whereIn('nvservicestatus.nv_id', $latestData->pluck('nv_id'))
        ->where('nvservicestatus.rv4_status', 0)
        ->where('nvservicestatus.rv2_status', 1)
        ->sum('tbl_material.total_budget_both') +  DB::table('tbl_service')
           ->join('nvservicestatus', 'tbl_service.id', '=', 'nvservicestatus.service_id')
         ->whereIn('tbl_service.nv_id', $totalId)
         ->whereIn('nvservicestatus.nv_id', $latestData->pluck('nv_id'))
        ->where('nvservicestatus.rv4_status', 0)
        ->where('nvservicestatus.rv2_status', 1)
        ->sum('tbl_service.total_buget');
    }elseif(!empty($dep_rew1)){
        $pendingAmount = DB::table('tbl_material')
           ->join('nvservicestatus', 'tbl_material.id', '=', 'nvservicestatus.material_id')
         ->whereIn('tbl_material.nv_id', $totalId)
         ->whereIn('nvservicestatus.nv_id', $latestData->pluck('nv_id'))
        ->where('nvservicestatus.rv4_status', 0)
        ->where('nvservicestatus.rv1_status', 1)
        ->sum('tbl_material.total_budget_both') +  DB::table('tbl_service')
           ->join('nvservicestatus', 'tbl_service.id', '=', 'nvservicestatus.service_id')
         ->whereIn('tbl_service.nv_id', $totalId)
         ->whereIn('nvservicestatus.nv_id', $latestData->pluck('nv_id'))
        ->where('nvservicestatus.rv4_status', 0)
        ->where('nvservicestatus.rv1_status', 1)
        ->sum('tbl_service.total_buget');
    }else{
        $pendingAmount = DB::table('tbl_material')
           ->join('nvservicestatus', 'tbl_material.id', '=', 'nvservicestatus.material_id')
         ->whereIn('tbl_material.nv_id', $totalId)
         ->whereIn('nvservicestatus.nv_id', $latestData->pluck('nv_id'))
        ->where('nvservicestatus.rv4_status', 0)
        ->where('nvservicestatus.draft', 1)
        ->sum('tbl_material.total_budget_both') +  DB::table('tbl_service')
           ->join('nvservicestatus', 'tbl_service.id', '=', 'nvservicestatus.service_id')
         ->whereIn('tbl_service.nv_id', $totalId)
         ->whereIn('nvservicestatus.nv_id', $latestData->pluck('nv_id'))
        ->where('nvservicestatus.rv4_status', 0)
        ->where('nvservicestatus.draft', 1)
        ->sum('tbl_service.total_buget');
    }
  

    $rejectedAmount = DB::table('tbl_material')
       ->join('nvservicestatus', 'tbl_material.id', '=', 'nvservicestatus.material_id')
       ->whereIn('tbl_material.nv_id', $totalId)
    ->whereIn('nvservicestatus.nv_id', $latestData->pluck('nv_id'))
    ->where(function ($query) {
        $query->where('nvservicestatus.rv4_status', 2);
    })
    ->sum('tbl_material.total_budget_both') + DB::table('tbl_service')
       ->join('nvservicestatus', 'tbl_service.id', '=', 'nvservicestatus.service_id')
       ->whereIn('tbl_service.nv_id', $totalId)
    ->whereIn('nvservicestatus.nv_id', $latestData->pluck('nv_id'))
    ->where(function ($query) {
        $query->where('nvservicestatus.rv4_status', 2);
    })
    ->sum('tbl_service.total_buget');


    $approvedAmount = DB::table('tbl_material')
     ->whereIn('tbl_material.nv_id', $totalId)
     ->whereIn('nvservicestatus.nv_id', $latestData->pluck('nv_id'))
       ->join('nvservicestatus', 'tbl_material.id', '=', 'nvservicestatus.material_id')
    ->where('nvservicestatus.ceo_status', 1)
    ->sum('tbl_material.total_budget_both') + DB::table('tbl_service')
    ->whereIn('tbl_service.nv_id', $totalId)
    ->whereIn('nvservicestatus.nv_id', $latestData->pluck('nv_id'))
      ->join('nvservicestatus', 'tbl_service.id', '=', 'nvservicestatus.service_id')
   ->where('nvservicestatus.ceo_status', 1)
   ->sum('tbl_service.total_buget');



        if($company_id){
            $approvedNV = $latestData->where('ceo_status', 1)->where('company_id',$company_id)->count();
        }else{
            $approvedNV = $latestData->where('ceo_status', 1)->count();
        }
        $rejectedNV = $latestData->filter(function ($data) {
            return in_array($data->ceo_status, [2]) || 
            $data->rv4_status == 2 ;
        })->count();
        if(!empty($dep_rew3)){
            $pendingNV = $latestData->filter(function ($data) {
                return  in_array($data->rv3_status, [1]) &&
                    in_array($data->rv4_status, [0]);
            })->count();
        }elseif(!empty($dep_rew2)){
            $pendingNV = $latestData->filter(function ($data) {
                return  in_array($data->rv2_status, [1]) &&
                    in_array($data->rv4_status, [0]);
            })->count();
        }elseif(!empty($dep_rew1)){
            $pendingNV = $latestData->filter(function ($data) {
                return  in_array($data->rv1_status, [1]) &&
                    in_array($data->rv4_status, [0]);
            })->count();
        }else{
            $pendingNV = $latestData->filter(function ($data) {
                return  in_array($data->draft, [1]) &&
                    in_array($data->rv4_status, [0]);
            })->count();
        }
        if($company_id){
            $totalNV = Nvsericestatus::whereIn("nv_id", $totalId)->where('rv4_status', 1)->where('company_id',$company_id)->count();
        } else {
            $totalNV = Nvsericestatus::whereIn("nv_id", $totalId)->where('rv4_status', 1)->count();
        }
         
       
       
        $nvIds = NeedValidation::whereIn("user_id", $Values)->pluck("id");
        if($fiscal_year){
            $nv = NeedValidation::where('fiscal_year', $fiscal_year)->where(function ($query) use ($user, $allNormalUsers, $departmentIds, $currentFinancialYear) {
                $query->where('user_id', $user->id)
                      ->orWhereIn('user_id', $allNormalUsers)
                      ->orWhereIn('department_id', $departmentIds);
            })
            ->whereHas('service') // Assuming you have a relationship named 'service' defined in the NeedValidation model
            ->select('id')
            ->get();  
        }else{
            $nv = NeedValidation::where('fiscal_year', $currentFinancialYear)->where(function ($query) use ($user, $allNormalUsers, $departmentIds, $currentFinancialYear) {
                $query->where('user_id', $user->id)
                      ->orWhereIn('user_id', $allNormalUsers)
                      ->orWhereIn('department_id', $departmentIds);
            })
            ->whereHas('service') // Assuming you have a relationship named 'service' defined in the NeedValidation model
            ->select('id')
            ->get();  
        }
           $nv_ids = $nv->pluck('id');
        if(!empty($dep_rew3)){
          
                $nv_sm_data = Nvsericestatus::whereIn('nv_id', $nv_ids)->with(['service', 'material', 'user'])
            ->where('rv3_status',1)
            ->orderBy('id', 'asc');
        
        }elseif(!empty($dep_rew2)){
           
                $nv_sm_data = Nvsericestatus::whereIn('nv_id', $nv_ids)->with(['service', 'material', 'user'])
            ->where('rv2_status',1)
            ->orderBy('id', 'asc');
          
        }elseif(!empty($dep_rew1)){
           
                $nv_sm_data = Nvsericestatus::whereIn('nv_id', $nv_ids)->with(['service', 'material', 'user'])
            ->where('rv1_status',1)
            ->orderBy('id', 'asc');
          
        }else{
            
                $nv_sm_data = Nvsericestatus::whereIn('nv_id', $nv_ids)->with(['service', 'material', 'user'])
            ->where('draft',1)
            ->orderBy('id', 'asc');
           
        }
       
        $total6_status = $request->total;
        $pending6_status = $request->pending;
        $reject6_status = $request->rejected;

       
      
       
        if ($ceo_status !== null) {
            $nv_sm_data = $nv_sm_data->where('ceo_status', $ceo_status);
        } elseif ($pending6_status !== null) {
            $nv_sm_data = $nv_sm_data->where('rv4_status', $pending6_status)
                ->whereIn('ceo_status', [0, 1])
                ->whereIn('cpmg_status', [0, 1])
                ->whereIn('cto_status', [0, 1])
                ->whereIn('ceo_nominee_status', [0, 1])    
                ->whereIn('ceo_nominee2_status', [0, 1]);
        } elseif ($reject6_status !== null) {
            $nv_sm_data = $nv_sm_data->where('rv4_status', $reject6_status)
                ->orWhere('hod_status', [2])
                ->orWhere('cpmg_status', [2])
                ->orWhere('cto_status', [2])
                ->orWhere('ceo_nominee_status', [2])
                ->orWhere('ceo_nominee2_status', [2]);
        }elseif ($total6_status !== null) {
            $nv_sm_data = $nv_sm_data->where('rv4_status', $total6_status);
              
        }
        $nv_sm_data = $nv_sm_data->get();
        if($fiscal_year){
            $BRPLnv = NeedValidation::whereIn("department_id", $departmentIds)->where('company_id','6')->where('fiscal_year', $fiscal_year)->pluck("id");

        }else{
            $BRPLnv = NeedValidation::whereIn("department_id", $departmentIds)->where('company_id','6')->where('fiscal_year', $currentFinancialYear)->pluck("id");

        }
        if(!empty($dep_rew3)){
            $pendingAmountBRPL = DB::table('tbl_material')
               ->join('nvservicestatus', 'tbl_material.id', '=', 'nvservicestatus.material_id')
            ->whereIn('tbl_material.nv_id', $BRPLnv)
            ->where('nvservicestatus.rv4_status', 0)
            ->where('nvservicestatus.rv3_status', 1)
            ->sum('tbl_material.total_budget_both')+DB::table('tbl_service')
               ->join('nvservicestatus', 'tbl_service.id', '=', 'nvservicestatus.service_id')
            ->whereIn('tbl_service.nv_id', $BRPLnv)
            ->where('nvservicestatus.rv4_status', 0)
            ->where('nvservicestatus.rv3_status', 1)
            ->sum('tbl_service.total_buget');
        }elseif(!empty($dep_rew2)){
            $pendingAmountBRPL = DB::table('tbl_material')
               ->join('nvservicestatus', 'tbl_material.id', '=', 'nvservicestatus.material_id')
            ->whereIn('tbl_material.nv_id', $BRPLnv)
            ->where('nvservicestatus.rv4_status', 0)
            ->where('nvservicestatus.rv2_status', 1)
            ->sum('tbl_material.total_budget_both')+DB::table('tbl_service')
               ->join('nvservicestatus', 'tbl_service.id', '=', 'nvservicestatus.service_id')
            ->whereIn('tbl_service.nv_id', $BRPLnv)
            ->where('nvservicestatus.rv4_status', 0)
            ->where('nvservicestatus.rv2_status', 1)
            ->sum('tbl_service.total_buget');
        }elseif(!empty($dep_rew1)){
            $pendingAmountBRPL = DB::table('tbl_material')
               ->join('nvservicestatus', 'tbl_material.id', '=', 'nvservicestatus.material_id')
            ->whereIn('tbl_material.nv_id', $BRPLnv)
            ->where('nvservicestatus.rv4_status', 0)
            ->where('nvservicestatus.rv1_status', 1)
            ->sum('tbl_material.total_budget_both')+DB::table('tbl_service')
               ->join('nvservicestatus', 'tbl_service.id', '=', 'nvservicestatus.service_id')
            ->whereIn('tbl_service.nv_id', $BRPLnv)
            ->where('nvservicestatus.rv4_status', 0)
            ->where('nvservicestatus.rv1_status', 1)
            ->sum('tbl_service.total_buget');
        }else{
            $pendingAmountBRPL = DB::table('tbl_material')
           ->join('nvservicestatus', 'tbl_material.id', '=', 'nvservicestatus.material_id')
        ->whereIn('tbl_material.nv_id', $BRPLnv)
        ->where('nvservicestatus.rv4_status', 0)
        ->where('nvservicestatus.draft', 1)
        ->sum('tbl_material.total_budget_both')+DB::table('tbl_service')
           ->join('nvservicestatus', 'tbl_service.id', '=', 'nvservicestatus.service_id')
        ->whereIn('tbl_service.nv_id', $BRPLnv)
        ->where('nvservicestatus.rv4_status', 0)
        ->where('nvservicestatus.draft', 1)
        ->sum('tbl_service.total_buget');
        }
     

        $rejectedAmountBRPL = DB::table('tbl_material')
           ->join('nvservicestatus', 'tbl_material.id', '=', 'nvservicestatus.material_id')
        ->whereIn('tbl_material.nv_id', $BRPLnv)
        ->where(function ($query) {
            $query->where('nvservicestatus.rv4_status', 2);
        })
        ->sum('tbl_material.total_budget_both')+DB::table('tbl_service')
           ->join('nvservicestatus', 'tbl_service.id', '=', 'nvservicestatus.service_id')
        ->whereIn('tbl_service.nv_id', $BRPLnv)
        ->where(function ($query) {
            $query->where('nvservicestatus.rv4_status', 2);
        })
        ->sum('tbl_service.total_buget');

      $approvedAmountBRPL = DB::table('tbl_material')
           ->join('nvservicestatus', 'tbl_material.id', '=', 'nvservicestatus.material_id')
        ->whereIn('tbl_material.nv_id', $BRPLnv)->where('nvservicestatus.ceo_status', 1)
        ->sum('tbl_material.total_budget_both')+DB::table('tbl_service')
           ->join('nvservicestatus', 'tbl_service.id', '=', 'nvservicestatus.service_id')
        ->whereIn('tbl_service.nv_id', $BRPLnv)->where('nvservicestatus.ceo_status', 1)
        ->sum('tbl_service.total_buget');
        if(!empty($dep_rew3)){
            $fileDataBRPL =Nvsericestatus::
            select(
                DB::raw('MONTH(created_at) as month'),
                DB::raw('SUM(CASE WHEN ceo_status = "1" THEN 1 ELSE 0 END) as approved_count'),
                DB::raw('SUM(CASE WHEN ( rv4_status = "2")  THEN 1 ELSE 0 END ) as rejected_count'),
                DB::raw('SUM(CASE WHEN (rv3_status = "1" AND rv4_status = "0" ) THEN 1 ELSE 0 END) as pending_count'),
            )
         
            ->whereIn('nv_id', $BRPLnv)->with(['service', 'material', 'user'])
            ->whereYear('created_at', Carbon::now()->year)
            ->groupBy('month')
            ->orderBy('month')
            ->get();
        }elseif(!empty($dep_rew2)){
            $fileDataBRPL =Nvsericestatus::
        select(
            DB::raw('MONTH(created_at) as month'),
            DB::raw('SUM(CASE WHEN ceo_status = "1" THEN 1 ELSE 0 END) as approved_count'),
            DB::raw('SUM(CASE WHEN ( rv4_status = "2")  THEN 1 ELSE 0 END ) as rejected_count'),
            DB::raw('SUM(CASE WHEN (rv2_status = "1" AND rv4_status = "0" ) THEN 1 ELSE 0 END) as pending_count'),
        )
     
        ->whereIn('nv_id', $BRPLnv)->with(['service', 'material', 'user'])
        ->whereYear('created_at', Carbon::now()->year)
        ->groupBy('month')
        ->orderBy('month')
        ->get();
        }elseif(!empty($dep_rew1)){
            $fileDataBRPL =Nvsericestatus::
        select(
            DB::raw('MONTH(created_at) as month'),
            DB::raw('SUM(CASE WHEN ceo_status = "1" THEN 1 ELSE 0 END) as approved_count'),
            DB::raw('SUM(CASE WHEN ( rv4_status = "2")  THEN 1 ELSE 0 END ) as rejected_count'),
            DB::raw('SUM(CASE WHEN (rv1_status = "1" AND rv4_status = "0" ) THEN 1 ELSE 0 END) as pending_count'),
        )
     
        ->whereIn('nv_id', $BRPLnv)->with(['service', 'material', 'user'])
        ->whereYear('created_at', Carbon::now()->year)
        ->groupBy('month')
        ->orderBy('month')
        ->get();
        }else{
            $fileDataBRPL =Nvsericestatus::
            select(
                DB::raw('MONTH(created_at) as month'),
                DB::raw('SUM(CASE WHEN ceo_status = "1" THEN 1 ELSE 0 END) as approved_count'),
                DB::raw('SUM(CASE WHEN ( rv4_status = "2")  THEN 1 ELSE 0 END ) as rejected_count'),
                DB::raw('SUM(CASE WHEN (draft = "1" AND rv4_status = "0" ) THEN 1 ELSE 0 END) as pending_count'),
            )
         
            ->whereIn('nv_id', $BRPLnv)->with(['service', 'material', 'user'])
            ->whereYear('created_at', Carbon::now()->year)
            ->groupBy('month')
            ->orderBy('month')
            ->get();
        }
      
    $BRPLlabels = [];
    $BRPLapprovedData = [];
    $BRPLrejectedData = [];
    $BRPLpendingData = [];

    foreach ($fileDataBRPL as $dataPointBRPL) {
        $monthBRPL = Carbon::createFromFormat('!m', $dataPointBRPL->month)->format('F');

        $BRPLlabels[] = $monthBRPL;
        $BRPLapprovedData[] = $dataPointBRPL->approved_count;
        $BRPLrejectedData[] = $dataPointBRPL->rejected_count;
        $BRPLpendingData[] = $dataPointBRPL->pending_count;
    }
    if($fiscal_year){
        $BYPLnv = NeedValidation::whereIn("department_id", $departmentIds)->where('company_id','5')->where('fiscal_year', $fiscal_year)->pluck("id");

    }else{
        $BYPLnv = NeedValidation::whereIn("department_id", $departmentIds)->where('company_id','5')->where('fiscal_year', $currentFinancialYear)->pluck("id");

    }
    if(!empty($dep_rew3)){
        $pendingAmountBYPL = DB::table('tbl_material')
           ->join('nvservicestatus', 'tbl_material.id', '=', 'nvservicestatus.material_id')
        ->whereIn('tbl_material.nv_id', $BYPLnv)
        ->where('nvservicestatus.rv4_status', 0)
        ->where('nvservicestatus.rv3_status', 1)
        ->sum('tbl_material.total_budget_both')+DB::table('tbl_service')
           ->join('nvservicestatus', 'tbl_service.id', '=', 'nvservicestatus.service_id')
        ->whereIn('tbl_service.nv_id', $BYPLnv)
        ->where('nvservicestatus.rv4_status', 0)
        ->where('nvservicestatus.rv3_status', 1)
        ->sum('tbl_service.total_buget');
    }elseif(!empty($dep_rew2)){
        $pendingAmountBYPL = DB::table('tbl_material')
           ->join('nvservicestatus', 'tbl_material.id', '=', 'nvservicestatus.material_id')
        ->whereIn('tbl_material.nv_id', $BYPLnv)
        ->where('nvservicestatus.rv4_status', 0)
        ->where('nvservicestatus.rv2_status', 1)
        ->sum('tbl_material.total_budget_both')+DB::table('tbl_service')
           ->join('nvservicestatus', 'tbl_service.id', '=', 'nvservicestatus.service_id')
        ->whereIn('tbl_service.nv_id', $BYPLnv)
        ->where('nvservicestatus.rv4_status', 0)
        ->where('nvservicestatus.rv2_status', 1)
        ->sum('tbl_service.total_buget');
    }elseif(!empty($dep_rew1)){
        $pendingAmountBYPL = DB::table('tbl_material')
           ->join('nvservicestatus', 'tbl_material.id', '=', 'nvservicestatus.material_id')
        ->whereIn('tbl_material.nv_id', $BYPLnv)
        ->where('nvservicestatus.rv4_status', 0)
        ->where('nvservicestatus.rv1_status', 1)
        ->sum('tbl_material.total_budget_both')+DB::table('tbl_service')
           ->join('nvservicestatus', 'tbl_service.id', '=', 'nvservicestatus.service_id')
        ->whereIn('tbl_service.nv_id', $BYPLnv)
        ->where('nvservicestatus.rv4_status', 0)
        ->where('nvservicestatus.rv1_status', 1)
        ->sum('tbl_service.total_buget');
    }else{
        $pendingAmountBYPL = DB::table('tbl_material')
           ->join('nvservicestatus', 'tbl_material.id', '=', 'nvservicestatus.material_id')
        ->whereIn('tbl_material.nv_id', $BYPLnv)
        ->where('nvservicestatus.rv4_status', 0)
        ->where('nvservicestatus.draft', 1)
        ->sum('tbl_material.total_budget_both')+DB::table('tbl_service')
           ->join('nvservicestatus', 'tbl_service.id', '=', 'nvservicestatus.service_id')
        ->whereIn('tbl_service.nv_id', $BYPLnv)
        ->where('nvservicestatus.rv4_status', 0)
        ->where('nvservicestatus.draft', 1)
        ->sum('tbl_service.total_buget');
    }
   

        $rejectedAmountBYPL = DB::table('tbl_material')
           ->join('nvservicestatus', 'tbl_material.id', '=', 'nvservicestatus.material_id')
        ->whereIn('tbl_material.nv_id', $BYPLnv)
        ->where(function ($query) {
            $query->where('nvservicestatus.rv4_status', 2);
        })
        ->sum('tbl_material.total_budget_both')+DB::table('tbl_service')
           ->join('nvservicestatus', 'tbl_service.id', '=', 'nvservicestatus.service_id')
        ->whereIn('tbl_service.nv_id', $BYPLnv)
        ->where(function ($query) {
            $query->where('nvservicestatus.rv4_status', 2);
        })
        ->sum('tbl_service.total_buget');

      $approvedAmountBYPL = DB::table('tbl_material')
           ->join('nvservicestatus', 'tbl_material.id', '=', 'nvservicestatus.material_id')
        ->whereIn('tbl_material.nv_id', $BYPLnv)->where('nvservicestatus.ceo_status', 1)
        ->sum('tbl_material.total_budget_both')+DB::table('tbl_service')
           ->join('nvservicestatus', 'tbl_service.id', '=', 'nvservicestatus.service_id')
        ->whereIn('tbl_service.nv_id', $BYPLnv)->where('nvservicestatus.ceo_status', 1)
        ->sum('tbl_service.total_buget');
        if(!empty($dep_rew3)){
            $fileDataBYPL =Nvsericestatus::
            select(
                DB::raw('MONTH(created_at) as month'),
                DB::raw('SUM(CASE WHEN ceo_status = "1" THEN 1 ELSE 0 END) as approved_count'),
                DB::raw('SUM(CASE WHEN ( rv4_status = "2")  THEN 1 ELSE 0 END) as rejected_count'),
                DB::raw('SUM(CASE WHEN (rv3_status = "1" AND rv4_status = "0" )  THEN 1 ELSE 0 END) as pending_count'),
            ) 
            ->whereIn('nv_id', $BYPLnv)->with(['service', 'material', 'user'])
            ->whereYear('created_at', Carbon::now()->year)
            ->groupBy('month')
            ->orderBy('month')
            ->get();
        }elseif(!empty($dep_rew2)){
            $fileDataBYPL =Nvsericestatus::
    select(
        DB::raw('MONTH(created_at) as month'),
        DB::raw('SUM(CASE WHEN ceo_status = "1" THEN 1 ELSE 0 END) as approved_count'),
        DB::raw('SUM(CASE WHEN ( rv4_status = "2")  THEN 1 ELSE 0 END) as rejected_count'),
        DB::raw('SUM(CASE WHEN (rv2_status = "1" AND rv4_status = "0" )  THEN 1 ELSE 0 END) as pending_count'),
    ) 
    ->whereIn('nv_id', $BYPLnv)->with(['service', 'material', 'user'])
    ->whereYear('created_at', Carbon::now()->year)
    ->groupBy('month')
    ->orderBy('month')
    ->get();
        }elseif(!empty($dep_rew1)){
            $fileDataBYPL =Nvsericestatus::
            select(
                DB::raw('MONTH(created_at) as month'),
                DB::raw('SUM(CASE WHEN ceo_status = "1" THEN 1 ELSE 0 END) as approved_count'),
                DB::raw('SUM(CASE WHEN ( rv4_status = "2")  THEN 1 ELSE 0 END) as rejected_count'),
                DB::raw('SUM(CASE WHEN (rv1_status = "1" AND rv4_status = "0" )  THEN 1 ELSE 0 END) as pending_count'),
            ) 
            ->whereIn('nv_id', $BYPLnv)->with(['service', 'material', 'user'])
            ->whereYear('created_at', Carbon::now()->year)
            ->groupBy('month')
            ->orderBy('month')
            ->get();
        }else{
            $fileDataBYPL =Nvsericestatus::
            select(
                DB::raw('MONTH(created_at) as month'),
                DB::raw('SUM(CASE WHEN ceo_status = "1" THEN 1 ELSE 0 END) as approved_count'),
                DB::raw('SUM(CASE WHEN ( rv4_status = "2")  THEN 1 ELSE 0 END) as rejected_count'),
                DB::raw('SUM(CASE WHEN (draft = "1" AND rv4_status = "0" )  THEN 1 ELSE 0 END) as pending_count'),
            ) 
            ->whereIn('nv_id', $BYPLnv)->with(['service', 'material', 'user'])
            ->whereYear('created_at', Carbon::now()->year)
            ->groupBy('month')
            ->orderBy('month')
            ->get();
        }
   
$BYPLlabels = [];
$BYPLapprovedData = [];
$BYPLrejectedData = [];
$BYPLpendingData = [];

foreach ($fileDataBYPL as $dataPointBYPL) {
    $monthBYPL = Carbon::createFromFormat('!m', $dataPointBYPL->month)->format('F');

    $BYPLlabels[] = $monthBYPL;
    $BYPLapprovedData[] = $dataPointBYPL->approved_count;
    $BYPLrejectedData[] = $dataPointBYPL->rejected_count;
    $BYPLpendingData[] = $dataPointBYPL->pending_count;
 }
 
        return view("admin.dashboard", compact("approvedAmount","currentFinancialYear","nextFinancialYear","nextToNextFinancialYear", "rejectedAmount", "pendingAmount", "totalAmount","company", "company_id" , "nv_sm_data", "totalNV", "approvedNV", "rejectedNV", "pendingNV" , "hodApproval", "cpmgApproval", "btApproval", "ceoApproval"             ,'BRPLlabels','BRPLapprovedData','BRPLrejectedData','BRPLpendingData','BYPLlabels','BYPLapprovedData','BYPLrejectedData','BYPLpendingData','approvedAmountBYPL','approvedAmountBRPL','rejectedAmountBYPL','rejectedAmountBRPL','pendingAmountBYPL','pendingAmountBRPL'));
    } elseif ($hod == $user->id) {
        $Values = [$user->id, $dep_rew1, $dep_rew2, $dep_rew3, $dep_rew4];
      
        $departmentIds = explode(',', $user->department_id);
        if($fiscal_year){
            $totalId = NeedValidation::where('fiscal_year', $fiscal_year)
            ->where(function ($query) use ($Values, $allNormalUsers, $departmentIds) {
                $query->whereIn('user_id', $Values)
                      ->orWhereIn('user_id', $allNormalUsers)
                      ->orWhereIn('department_id', $departmentIds);
            })
            ->pluck('id');
        }else{
            $totalId = NeedValidation::where('fiscal_year', $currentFinancialYear)
            ->where(function ($query) use ($Values, $allNormalUsers, $departmentIds) {
                $query->whereIn('user_id', $Values)
                      ->orWhereIn('user_id', $allNormalUsers)
                      ->orWhereIn('department_id', $departmentIds);
            })
            ->pluck('id');
        }
        $totalAmount = DB::table('tbl_material')
        ->join('nvservicestatus', 'tbl_material.id', '=', 'nvservicestatus.material_id')
         ->whereIn('tbl_material.nv_id', $totalId)
         ->whereIn('nvservicestatus.nv_id', $latestData->pluck('nv_id'))
        ->where('nvservicestatus.hod_status', 1)
        ->sum('tbl_material.total_budget_both') + DB::table('tbl_service')
        ->join('nvservicestatus', 'tbl_service.id', '=', 'nvservicestatus.service_id')
         ->whereIn('tbl_service.nv_id', $totalId)
         ->whereIn('nvservicestatus.nv_id', $latestData->pluck('nv_id'))
        ->where('nvservicestatus.hod_status', 1)
        ->sum('tbl_service.total_buget');
      
        
        

        $rejectedAmount = DB::table('tbl_material')
        ->join('nvservicestatus', 'tbl_material.id', '=', 'nvservicestatus.material_id')
        ->whereIn('tbl_material.nv_id', $totalId)
        ->whereIn('nvservicestatus.nv_id', $latestData->pluck('nv_id'))
        ->where(function ($query) {
            $query->where('nvservicestatus.hod_status', 2);
        })
        ->sum('tbl_material.total_budget_both') + DB::table('tbl_service')
        ->join('nvservicestatus', 'tbl_service.id', '=', 'nvservicestatus.service_id')
        ->whereIn('tbl_service.nv_id', $totalId)
        ->whereIn('nvservicestatus.nv_id', $latestData->pluck('nv_id'))
        ->where(function ($query) {
            $query->where('nvservicestatus.hod_status', 2);
        })
        ->sum('tbl_service.total_buget');
   

        $approvedAmount = DB::table('tbl_material')
         ->whereIn('tbl_material.nv_id', $totalId)
         ->whereIn('nvservicestatus.nv_id', $latestData->pluck('nv_id'))
           ->join('nvservicestatus', 'tbl_material.id', '=', 'nvservicestatus.material_id')
        ->where('nvservicestatus.ceo_status', 1)
        ->sum('tbl_material.total_budget_both') + DB::table('tbl_service')
        ->whereIn('tbl_service.nv_id', $totalId)
        ->whereIn('nvservicestatus.nv_id', $latestData->pluck('nv_id'))
        ->join('nvservicestatus', 'tbl_service.id', '=', 'nvservicestatus.service_id')
       ->where('nvservicestatus.ceo_status', 1)
       ->sum('tbl_service.total_buget');

        if($company_id){
        $latestData = Nvsericestatus::whereIn("nv_id", $totalId)->where('company_id',$company_id)->get();
    }else {
        $latestData = Nvsericestatus::whereIn("nv_id", $totalId)->get();
    }
        if($company_id){
            $approvedNV = $latestData->where('ceo_status', 1)->where('company_id',$company_id)->count();
        }else{
            $approvedNV = $latestData->where('ceo_status', 1)->count();
        }
        $rejectedNV = $latestData->filter(function ($data) {
            return in_array($data->ceo_status, [2]) || 
            $data->hod_status == 2 ;
        })->count();
       
        if($company_id){
            $totalNV = Nvsericestatus::whereIn("nv_id", $totalId)->where('hod_status', 1)->where('company_id',$company_id)->count();
        } else {
            $totalNV = Nvsericestatus::whereIn("nv_id", $totalId)->where('hod_status', 1)->count();
        }
         
      
        $nvIds = NeedValidation::whereIn("user_id", $Values)->pluck("id");
   
       
        $nv_sm_data = array();
        $nv_statuses = array();
        $nv_id = [];
        $pendingAmount = 0;
        $pendingNV = 0;

       $depart = Department::whereIn('id', $departmentIds)->get();

       foreach ($depart as $depart) {
        $dep_rew4 = $depart->dep_rew4;
        $dep_rew3 = $depart->dep_rew3;
        $dep_rew2 = $depart->dep_rew2;
        $dep_rew1 = $depart->dep_rew1;
        $dep_hod = $depart->dep_hod;
        if(!empty($dep_hod)){
        if(!empty($dep_rew4)){
            $nv_status = Nvsericestatus::where(function ($query) {
                $query->where('rv4_status', 1);
                   
            })
                ->where(function ($query) {
                    $query
                        ->where('rv4_status', '!=', 2);
                })
                ->get();
                if(count($nv_status) > 0) {
                    $nv_statuses[] = $nv_status;
                   
                    
                }
       
      
    }elseif(!empty($dep_rew3)){
        $nv_status = Nvsericestatus::where(function ($query) {
            $query->where('rv3_status', 1);
               
        })
            ->where(function ($query) {
                $query
                    ->where('rv3_status', '!=', 2);
            })
            ->get();
            if(count($nv_status) > 0) {
                $nv_statuses[] = $nv_status;
                
                
            }
      
    }elseif(!empty($dep_rew2)){
       $nv_status = Nvsericestatus::where(function ($query) {
        $query->where('rv2_status', 1);
          
    })
        ->where(function ($query) {
            $query
                ->where('rv2_status', '!=', 2);
        })
        ->get();
        if(count($nv_status) > 0) {
            $nv_statuses[] = $nv_status;
            
            
        }
      

    }elseif(!empty($dep_rew1)){
         $nv_status = Nvsericestatus::where(function ($query) {
            $query->where('rv1_status', 1);
        })
            ->where(function ($query) {
                $query->where('rv1_status', '!=', 2);
            })
            ->get();  
        if(count($nv_status) > 0) {
            $nv_statuses[] = $nv_status;
            
        }
      
      
    }else{
        $nv_status = Nvsericestatus::join('needvalidations','needvalidations.id','=','nvservicestatus.nv_id')
        ->join('department','department.id','=','needvalidations.department_id')
        ->where('department.id',$depart->id)
        ->where('nvservicestatus.draft',1)
        ->get();
       
        if(count($nv_status) > 0) {
            $nv_statuses[] = $nv_status;
        }
       
      
    }
}
 
       }
       if(!empty($nv_statuses)) {
        foreach ($nv_statuses as $idx => $nv_status) {
            foreach($nv_status as $data) {
                array_push($nv_id, $data["nv_id"]);
            }
        }
        if($fiscal_year){
        $nv1 = NeedValidation::where('fiscal_year', $fiscal_year)->where('delete_draft',0)
        ->whereIn('department_id', $departmentIds)->whereIn("id", $nv_id)->pluck('id');
        }else{
        $nv1 = NeedValidation::where('fiscal_year', $currentFinancialYear)->where('delete_draft',0)
        ->whereIn('department_id', $departmentIds)->whereIn("id", $nv_id)->pluck('id'); 
        }
        $nv_sm_data = Nvsericestatus::whereIn('nv_id', $nv1)->with(['service', 'material', 'user'])->orderBy('id', 'asc');
        $total7_status = $request->total;
        $pending7_status = $request->pending;
        $reject7_status = $request->rejected;

        if ($ceo_status !== null) {
            $nv_sm_data = $nv_sm_data->where('ceo_status', $ceo_status);
        } elseif ($pending7_status !== null) {
            $nv_sm_data = $nv_sm_data->where('hod_status', $pending7_status);
                
        } elseif ($reject7_status !== null) {
            $nv_sm_data = $nv_sm_data->where('hod_status', $reject7_status)
                ->orWhere('hod_status', [2])
                ->orWhere('cpmg_status', [2])
                ->orWhere('cto_status', [2])
                ->orWhere('ceo_nominee_status', [2])
                ->orWhere('ceo_nominee2_status', [2]);
        }elseif ($total7_status !== null) {
            $nv_sm_data = $nv_sm_data->where('hod_status', $total7_status);
                
        }
        $nv_sm_data = $nv_sm_data->get();

        $pendingAmount = DB::table('tbl_material')
        ->join('nvservicestatus', 'tbl_material.id', '=', 'nvservicestatus.material_id')
         ->whereIn('tbl_material.nv_id', $nv_id)
         ->whereIn('nvservicestatus.nv_id', $nv_id)
        ->where('nvservicestatus.hod_status', 0)
         ->where('nvservicestatus.draft', 1)            
        ->sum('tbl_material.total_budget_both') + DB::table('tbl_service')
        ->join('nvservicestatus', 'tbl_service.id', '=', 'nvservicestatus.service_id')
         ->whereIn('tbl_service.nv_id', $nv_id)
         ->whereIn('nvservicestatus.nv_id', $nv_id)
        ->where('nvservicestatus.hod_status', 0)
         ->where('nvservicestatus.draft', 1)            
        ->sum('tbl_service.total_buget');
        $pendingNV = Nvsericestatus::whereIn("nv_id", $nv_id)->where('draft', 1)->where('hod_status', 0)->count();
      }
        
     
    
       if($fiscal_year){
        $BRPLnv = NeedValidation::whereIn("department_id", $departmentIds)->whereIn("id", $nv_id)->where('company_id','6')->where('fiscal_year', $fiscal_year)->pluck("id");

       }else{
        $BRPLnv = NeedValidation::whereIn("department_id", $departmentIds)->whereIn("id", $nv_id)->where('company_id','6')->where('fiscal_year', $currentFinancialYear)->pluck("id");

       }
       
            $pendingAmountBRPL = DB::table('tbl_material')
             ->join('nvservicestatus', 'tbl_material.id', '=', 'nvservicestatus.material_id')
            ->whereIn('tbl_material.nv_id', $BRPLnv)
            ->where('nvservicestatus.hod_status', 0)
            ->where('nvservicestatus.draft', 1)
            ->sum('tbl_material.total_budget_both')+DB::table('tbl_service')
                ->join('nvservicestatus', 'tbl_service.id', '=', 'nvservicestatus.service_id')
            ->whereIn('tbl_service.nv_id', $BRPLnv)
            ->where('nvservicestatus.hod_status', 0)
            ->where('nvservicestatus.draft', 1)
            ->sum('tbl_service.total_buget');
     
     

        $rejectedAmountBRPL = DB::table('tbl_material')
         ->join('nvservicestatus', 'tbl_material.id', '=', 'nvservicestatus.material_id')
        ->whereIn('tbl_material.nv_id', $BRPLnv)
        ->where(function ($query) {
            $query->where('nvservicestatus.hod_status', 2);
        })
        ->sum('tbl_material.total_budget_both')+DB::table('tbl_service')
            ->join('nvservicestatus', 'tbl_service.id', '=', 'nvservicestatus.service_id')
        ->whereIn('tbl_service.nv_id', $BRPLnv)
        ->where(function ($query) {
            $query->where('nvservicestatus.hod_status', 2);
        })
        ->sum('tbl_service.total_buget');

      $approvedAmountBRPL = DB::table('tbl_material')
         ->join('nvservicestatus', 'tbl_material.id', '=', 'nvservicestatus.material_id')
        ->whereIn('tbl_material.nv_id', $BRPLnv)->where('nvservicestatus.ceo_status', 1)
        ->sum('tbl_material.total_budget_both')+DB::table('tbl_service')
            ->join('nvservicestatus', 'tbl_service.id', '=', 'nvservicestatus.service_id')
        ->whereIn('tbl_service.nv_id', $BRPLnv)->where('nvservicestatus.ceo_status', 1)
        ->sum('tbl_service.total_buget');
      
            $fileDataBRPL =Nvsericestatus::
            select(
                DB::raw('MONTH(created_at) as month'),
                DB::raw('SUM(CASE WHEN ceo_status = "1" THEN 1 ELSE 0 END) as approved_count'),
                DB::raw('SUM(CASE WHEN ( hod_status = "2")  THEN 1 ELSE 0 END ) as rejected_count'),
                DB::raw('SUM(CASE WHEN (hod_status = "0" AND draft = "1" ) THEN 1 ELSE 0 END) as pending_count'),
            )
         
            ->whereIn('nv_id', $BRPLnv)->with(['service', 'material', 'user'])
            ->whereYear('created_at', Carbon::now()->year)
            ->groupBy('month')
            ->orderBy('month')
            ->get();
        
    $BRPLlabels = [];
    $BRPLapprovedData = [];
    $BRPLrejectedData = [];
    $BRPLpendingData = [];

    foreach ($fileDataBRPL as $dataPointBRPL) {
        $monthBRPL = Carbon::createFromFormat('!m', $dataPointBRPL->month)->format('F');

        $BRPLlabels[] = $monthBRPL;
        $BRPLapprovedData[] = $dataPointBRPL->approved_count;
        $BRPLrejectedData[] = $dataPointBRPL->rejected_count;
        $BRPLpendingData[] = $dataPointBRPL->pending_count;
    }
    if($fiscal_year){
        $BYPLnv = NeedValidation::whereIn("department_id", $departmentIds)->whereIn("id", $nv_id)->where('company_id','5')->where('fiscal_year', $fiscal_year)->pluck("id");

       }else{
        $BYPLnv = NeedValidation::whereIn("department_id", $departmentIds)->whereIn("id", $nv_id)->where('company_id','5')->where('fiscal_year', $currentFinancialYear)->pluck("id");

       }
   
        $pendingAmountBYPL = DB::table('tbl_material')
         ->join('nvservicestatus', 'tbl_material.id', '=', 'nvservicestatus.material_id')
        ->whereIn('tbl_material.nv_id', $BYPLnv)
        ->where('nvservicestatus.hod_status', 0)
        ->where('nvservicestatus.draft', 1)
        ->sum('tbl_material.total_budget_both')+DB::table('tbl_service')
            ->join('nvservicestatus', 'tbl_service.id', '=', 'nvservicestatus.service_id')
        ->whereIn('tbl_service.nv_id', $BYPLnv)
        ->where('nvservicestatus.hod_status', 0)
        ->where('nvservicestatus.draft', 1)
        ->sum('tbl_service.total_buget');
    
   

    $rejectedAmountBYPL = DB::table('tbl_material')
     ->join('nvservicestatus', 'tbl_material.id', '=', 'nvservicestatus.material_id')
    ->whereIn('tbl_material.nv_id', $BYPLnv)
    ->where(function ($query) {
        $query->where('nvservicestatus.hod_status', 2);
    })
    ->sum('tbl_material.total_budget_both')+DB::table('tbl_service')
        ->join('nvservicestatus', 'tbl_service.id', '=', 'nvservicestatus.service_id')
    ->whereIn('tbl_service.nv_id', $BYPLnv)
    ->where(function ($query) {
        $query->where('nvservicestatus.hod_status', 2);
    })
    ->sum('tbl_service.total_buget');

  $approvedAmountBYPL = DB::table('tbl_material')
  ->join('nvservicestatus', 'tbl_material.id', '=', 'nvservicestatus.material_id')
    ->whereIn('tbl_material.nv_id', $BYPLnv)->where('nvservicestatus.ceo_status', 1)
    ->sum('tbl_material.total_budget_both')+DB::table('tbl_service')
        ->join('nvservicestatus', 'tbl_service.id', '=', 'nvservicestatus.service_id')
    ->whereIn('tbl_service.nv_id', $BYPLnv)->where('nvservicestatus.ceo_status', 1)
    ->sum('tbl_service.total_buget');
  
        $fileDataBYPL =Nvsericestatus::
        select(
            DB::raw('MONTH(created_at) as month'),
            DB::raw('SUM(CASE WHEN ceo_status = "1" THEN 1 ELSE 0 END) as approved_count'),
            DB::raw('SUM(CASE WHEN ( hod_status = "2")  THEN 1 ELSE 0 END) as rejected_count'),
            DB::raw('SUM(CASE WHEN (hod_status = "0" AND draft = "1" )  THEN 1 ELSE 0 END) as pending_count'),
        ) 
        ->whereIn('nv_id', $BYPLnv)->with(['service', 'material', 'user'])
        ->whereYear('created_at', Carbon::now()->year)
        ->groupBy('month')
        ->orderBy('month')
        ->get();
    
$BYPLlabels = [];
$BYPLapprovedData = [];
$BYPLrejectedData = [];
$BYPLpendingData = [];

foreach ($fileDataBYPL as $dataPointBYPL) {
    $monthBYPL = Carbon::createFromFormat('!m', $dataPointBYPL->month)->format('F');

    $BYPLlabels[] = $monthBYPL;
    $BYPLapprovedData[] = $dataPointBYPL->approved_count;
    $BYPLrejectedData[] = $dataPointBYPL->rejected_count;
    $BYPLpendingData[] = $dataPointBYPL->pending_count;
 }
 

        return view("admin.dashboard", compact("approvedAmount" ,"currentFinancialYear","nextFinancialYear","nextToNextFinancialYear","rejectedAmount", "pendingAmount", "totalAmount","company", "company_id" ,"nv_sm_data", "totalNV", "approvedNV", "rejectedNV", "pendingNV" , "hodApproval", "cpmgApproval", "btApproval", "ceoApproval"             ,'BRPLlabels','BRPLapprovedData','BRPLrejectedData','BRPLpendingData','BYPLlabels','BYPLapprovedData','BYPLrejectedData','BYPLpendingData','approvedAmountBYPL','approvedAmountBRPL','rejectedAmountBYPL','rejectedAmountBRPL','pendingAmountBYPL','pendingAmountBRPL'));
    
        } elseif ($id0->work_rew1 == $user->id) {
            $Values = [$user->id,  $hod,$id0];
          

            $totalId = NeedValidation::where('fiscal_year', $currentFinancialYear)->pluck('id');
            $latestData = Nvsericestatus::whereIn("nv_id", $totalId)->get();

            $totalfiscalId = NeedValidation::where('fiscal_year', $fiscal_year)->pluck('id');
            $latestfiscalData = Nvsericestatus::whereIn("nv_id", $totalfiscalId)->get();
            // dd($totalId);
            if($fiscal_year){
                $totalAmount = DB::table('tbl_material')
                ->join('nvservicestatus', 'tbl_material.id', '=', 'nvservicestatus.material_id')
                 ->whereIn('tbl_material.nv_id', $totalfiscalId)
                 ->whereIn('nvservicestatus.nv_id', $latestfiscalData->pluck('nv_id'))
                ->where('nvservicestatus.ces_rew1_status', 1)
                ->sum('tbl_material.total_budget_both') + DB::table('tbl_service')
                ->join('nvservicestatus', 'tbl_service.id', '=', 'nvservicestatus.service_id')
                 ->whereIn('tbl_service.nv_id', $totalfiscalId)
                 ->whereIn('nvservicestatus.nv_id', $latestfiscalData->pluck('nv_id'))
                ->where('nvservicestatus.ces_rew1_status', 1)
                ->sum('tbl_service.total_buget');

            }else{
                $totalAmount = DB::table('tbl_material')
                ->join('nvservicestatus', 'tbl_material.id', '=', 'nvservicestatus.material_id')
                 ->whereIn('tbl_material.nv_id', $totalId)
                 ->whereIn('nvservicestatus.nv_id', $latestData->pluck('nv_id'))
                ->where('nvservicestatus.ces_rew1_status', 1)
                ->sum('tbl_material.total_budget_both') + DB::table('tbl_service')
                ->join('nvservicestatus', 'tbl_service.id', '=', 'nvservicestatus.service_id')
                 ->whereIn('tbl_service.nv_id', $totalId)
                 ->whereIn('nvservicestatus.nv_id', $latestData->pluck('nv_id'))
                ->where('nvservicestatus.ces_rew1_status', 1)
                ->sum('tbl_service.total_buget');
            }
        
         
            if($fiscal_year){
                $pendingAmount = DB::table('tbl_material')
                ->join('nvservicestatus', 'tbl_material.id', '=', 'nvservicestatus.material_id')
                 ->whereIn('tbl_material.nv_id', $totalfiscalId)
                 ->whereIn('nvservicestatus.nv_id', $latestfiscalData->pluck('nv_id'))
              
                ->where("nvservicestatus.hod_status", 1)
                ->where("nvservicestatus.derc_info", 1)
                ->where("nvservicestatus.ces_rew1_status", 0)->where("nvservicestatus.ces_rew2_status", 0)->where("nvservicestatus.ces_rew3_status", 0)->where("nvservicestatus.ces_rew4_status", 0)->where("nvservicestatus.ces_status", 0)
                ->sum('tbl_material.total_budget_both') +  DB::table('tbl_service')
                ->join('nvservicestatus', 'tbl_service.id', '=', 'nvservicestatus.service_id')
                 ->whereIn('tbl_service.nv_id', $totalfiscalId)
                 ->whereIn('nvservicestatus.nv_id', $latestfiscalData->pluck('nv_id'))
              
                ->where("nvservicestatus.hod_status", 1)
                ->where("nvservicestatus.derc_info", 1)
                ->where("nvservicestatus.ces_rew1_status", 0)->where("nvservicestatus.ces_rew2_status", 0)->where("nvservicestatus.ces_rew3_status", 0)->where("nvservicestatus.ces_rew4_status", 0)->where("nvservicestatus.ces_status", 0)
                ->sum('tbl_service.total_buget');
            }else{
                $pendingAmount = DB::table('tbl_material')
                ->join('nvservicestatus', 'tbl_material.id', '=', 'nvservicestatus.material_id')
                 ->whereIn('tbl_material.nv_id', $totalId)
                 ->whereIn('nvservicestatus.nv_id', $latestData->pluck('nv_id'))
              
                ->where("nvservicestatus.hod_status", 1)
                ->where("nvservicestatus.derc_info", 1)
                ->where("nvservicestatus.ces_rew1_status", 0)->where("nvservicestatus.ces_rew2_status", 0)->where("nvservicestatus.ces_rew3_status", 0)->where("nvservicestatus.ces_rew4_status", 0)->where("nvservicestatus.ces_status", 0)
                ->sum('tbl_material.total_budget_both') +  DB::table('tbl_service')
                ->join('nvservicestatus', 'tbl_service.id', '=', 'nvservicestatus.service_id')
                 ->whereIn('tbl_service.nv_id', $totalId)
                 ->whereIn('nvservicestatus.nv_id', $latestData->pluck('nv_id'))
              
                ->where("nvservicestatus.hod_status", 1)
                ->where("nvservicestatus.derc_info", 1)
                ->where("nvservicestatus.ces_rew1_status", 0)->where("nvservicestatus.ces_rew2_status", 0)->where("nvservicestatus.ces_rew3_status", 0)->where("nvservicestatus.ces_rew4_status", 0)->where("nvservicestatus.ces_status", 0)
                ->sum('tbl_service.total_buget');
            }
           
            if($fiscal_year){
                $rejectedAmount = DB::table('tbl_material')
                ->join('nvservicestatus', 'tbl_material.id', '=', 'nvservicestatus.material_id')
                ->whereIn('nvservicestatus.nv_id', $latestfiscalData->pluck('nv_id'))
                ->where(function ($query) {
                    $query->where('nvservicestatus.ces_rew1_status', 2);
                })
                ->sum('tbl_material.total_budget_both') + DB::table('tbl_service')
                ->join('nvservicestatus', 'tbl_service.id', '=', 'nvservicestatus.service_id')
                ->whereIn('nvservicestatus.nv_id', $latestfiscalData->pluck('nv_id'))
                ->where(function ($query) {
                    $query->where('nvservicestatus.ces_rew1_status', 2);
                })
                ->sum('tbl_service.total_buget');
            }else{
                $rejectedAmount = DB::table('tbl_material')
                ->join('nvservicestatus', 'tbl_material.id', '=', 'nvservicestatus.material_id')
                ->whereIn('nvservicestatus.nv_id', $latestData->pluck('nv_id'))
                ->where(function ($query) {
                    $query->where('nvservicestatus.ces_rew1_status', 2);
                })
                ->sum('tbl_material.total_budget_both') + DB::table('tbl_service')
                ->join('nvservicestatus', 'tbl_service.id', '=', 'nvservicestatus.service_id')
                ->whereIn('nvservicestatus.nv_id', $latestData->pluck('nv_id'))
                ->where(function ($query) {
                    $query->where('nvservicestatus.ces_rew1_status', 2);
                })
                ->sum('tbl_service.total_buget');
            }
           
            if($fiscal_year){
                $approvedAmount = DB::table('tbl_material')
             ->whereIn('tbl_material.nv_id', $totalfiscalId)
             ->whereIn('nvservicestatus.nv_id', $latestfiscalData->pluck('nv_id'))
            ->join('nvservicestatus', 'tbl_material.id', '=', 'nvservicestatus.material_id')
            ->where('nvservicestatus.ceo_status', 1)
            ->sum('tbl_material.total_budget_both') +  DB::table('tbl_service')
            ->whereIn('tbl_service.nv_id', $totalfiscalId)
            ->whereIn('nvservicestatus.nv_id', $latestfiscalData->pluck('nv_id'))
           ->join('nvservicestatus', 'tbl_service.id', '=', 'nvservicestatus.service_id')
           ->where('nvservicestatus.ceo_status', 1)
           ->sum('tbl_service.total_buget');
            }else{
                $approvedAmount = DB::table('tbl_material')
                ->whereIn('tbl_material.nv_id', $totalId)
                ->whereIn('nvservicestatus.nv_id', $latestData->pluck('nv_id'))
               ->join('nvservicestatus', 'tbl_material.id', '=', 'nvservicestatus.material_id')
               ->where('nvservicestatus.ceo_status', 1)
               ->sum('tbl_material.total_budget_both') +  DB::table('tbl_service')
               ->whereIn('tbl_service.nv_id', $totalId)
               ->whereIn('nvservicestatus.nv_id', $latestData->pluck('nv_id'))
              ->join('nvservicestatus', 'tbl_service.id', '=', 'nvservicestatus.service_id')
              ->where('nvservicestatus.ceo_status', 1)
              ->sum('tbl_service.total_buget');
            }
    
            

            if($fiscal_year){
                $totalNV = Nvsericestatus::whereIn("nv_id", $totalfiscalId)->where('ces_rew1_status', 1)->count();
            }else{
                $totalNV = Nvsericestatus::whereIn("nv_id", $totalId)->where('ces_rew1_status', 1)->count();
            }
           
            if($fiscal_year){
                $rejectedNV = Nvsericestatus::whereIn("nv_id", $totalfiscalId)->where('ces_rew1_status', 2)->count();
            }else{
                $rejectedNV = Nvsericestatus::whereIn("nv_id", $totalId)->where('ces_rew1_status', 2)->count();
            }
           
            if($fiscal_year){
                $approvedNV = $latestfiscalData->where('ceo_status', 1)->count();
            }else{
                $approvedNV = $latestData->where('ceo_status', 1)->count();
            }
        
            

            if($fiscal_year){
                $pendingNV = $latestfiscalData->filter(function ($data) {

                    return in_array($data->hod_status, [1]) &&
                        in_array($data->derc_info, [1]) &&
                        in_array($data->ces_rew1_status, [0])&&
                        in_array($data->ces_rew2_status, [0])&&
                        in_array($data->ces_rew3_status, [0])&&
                        in_array($data->ces_rew4_status, [0])&&
                        in_array($data->ces_status, [0]);
                })->count();
            }else{
                $pendingNV = $latestData->filter(function ($data) {

                    return in_array($data->hod_status, [1]) &&
                        in_array($data->derc_info, [1]) &&
                        in_array($data->ces_rew1_status, [0])&&
                        in_array($data->ces_rew2_status, [0])&&
                        in_array($data->ces_rew3_status, [0])&&
                        in_array($data->ces_rew4_status, [0])&&
                        in_array($data->ces_status, [0]);
                })->count();
            }


            $nvIds = NeedValidation::where("user_id", $user->id)->pluck("id");
            $nv = NeedValidation::whereHas('service')->select('id')->where('fiscal_year', $currentFinancialYear)
         
            ->get();
            $nv_ids = $nv->pluck('id');



            if($fiscal_year){
                $nv_sm_data = Nvsericestatus::whereIn('nv_id', $totalfiscalId)->with(['service', 'material', 'user'])
                ->where('derc_info',1)
                ->where('hod_status',1)
                ->orderBy('id', 'asc');
            }else{
                $nv_sm_data = Nvsericestatus::whereIn('nv_id', $nv_ids)->with(['service', 'material', 'user'])
                ->where('derc_info',1)
                ->where('hod_status',1)
                ->orderBy('id', 'asc');
            }
           
            $total0_status = $request->total;
            $pending0_status = $request->pending;
            $reject0_status = $request->rejected;

           
          
           
            if ($ceo_status !== null) {
                $nv_sm_data = $nv_sm_data->where('ceo_status', $ceo_status);
            } elseif ($pending0_status !== null) {
                $nv_sm_data = $nv_sm_data->where('ces_rew1_status', $pending0_status)
                   
                ->whereIn('hod_status', [ 1])
                          
                ->whereIn('derc_info', [ 1])
                ->where('ces_rew2_status', 0)
                ->where('ces_rew3_status', 0)
                ->where('ces_rew4_status', 0);
                   ;
            } elseif ($reject0_status !== null) {
                $nv_sm_data = $nv_sm_data->where('ces_rew1_status', $reject0_status);

            }elseif ($total0_status !== null) {
                $nv_sm_data = $nv_sm_data->where('ces_rew1_status', $total0_status);
                  
            }
            $nv_sm_data = $nv_sm_data->get();


           
        if ($fiscal_year) {
            $BRPLnv = NeedValidation::where('company_id', '6')
            ->where('fiscal_year', $fiscal_year)
            ->pluck("id");
        } else {
            $BRPLnv = NeedValidation::where('company_id', '6')
            ->where('fiscal_year', $currentFinancialYear)->pluck("id");
        }

          
           
            $pendingAmountBRPL = DB::table('tbl_material')
                ->join('nvservicestatus', 'tbl_material.id', '=', 'nvservicestatus.material_id')
                ->whereIn('tbl_material.nv_id', $BRPLnv)
                ->where("nvservicestatus.hod_status", 1)
                ->where("nvservicestatus.derc_info", 1)
                ->where("nvservicestatus.ces_rew1_status", 0)->where("nvservicestatus.ces_rew2_status", 0)->where("nvservicestatus.ces_rew3_status", 0)->where("nvservicestatus.ces_rew4_status", 0)->where("nvservicestatus.ces_status", 0)
                ->sum('tbl_material.total_budget_both')+DB::table('tbl_service')
                ->join('nvservicestatus', 'tbl_service.id', '=', 'nvservicestatus.service_id')
                ->whereIn('tbl_service.nv_id', $BRPLnv)
                ->where("nvservicestatus.hod_status", 1)
                ->where("nvservicestatus.derc_info", 1)
                ->where("nvservicestatus.ces_rew1_status", 0)->where("nvservicestatus.ces_rew2_status", 0)->where("nvservicestatus.ces_rew3_status", 0)->where("nvservicestatus.ces_rew4_status", 0)->where("nvservicestatus.ces_status", 0)
                ->sum('tbl_service.total_buget');
        
                $rejectedAmountBRPL = DB::table('tbl_material')
                ->join('nvservicestatus', 'tbl_material.id', '=', 'nvservicestatus.material_id')
                ->whereIn('tbl_material.nv_id', $BRPLnv)
                ->where(function ($query) {
                    $query->where('nvservicestatus.ces_rew1_status', 2);
                })
                ->sum('tbl_material.total_budget_both')+DB::table('tbl_service')
                ->join('nvservicestatus', 'tbl_service.id', '=', 'nvservicestatus.service_id')
                ->whereIn('tbl_service.nv_id', $BRPLnv)
                ->where(function ($query) {
                    $query->where('nvservicestatus.ces_rew1_status', 2);
                })
                ->sum('tbl_service.total_buget');

              $approvedAmountBRPL = DB::table('tbl_material')
                ->join('nvservicestatus', 'tbl_material.id', '=', 'nvservicestatus.material_id')
                ->whereIn('tbl_material.nv_id', $BRPLnv)->where('nvservicestatus.ceo_status', 1)
                ->sum('tbl_material.total_budget_both')+DB::table('tbl_service')
                ->join('nvservicestatus', 'tbl_service.id', '=', 'nvservicestatus.service_id')
                ->whereIn('tbl_service.nv_id', $BRPLnv)->where('nvservicestatus.ceo_status', 1)
                ->sum('tbl_service.total_buget');
            $fileDataBRPL =Nvsericestatus::
            select(
                DB::raw('MONTH(created_at) as month'),
                DB::raw('SUM(CASE WHEN ceo_status = "1" THEN 1 ELSE 0 END) as approved_count'),
                DB::raw('SUM(CASE WHEN ( ces_rew1_status = "2")  THEN 1 ELSE 0 END ) as rejected_count'),
                DB::raw('SUM(CASE WHEN (ces_rew1_status = "0" AND ces_rew2_status = "0" AND ces_rew3_status = "0" AND ces_rew4_status = "0" AND ces_status = "0") THEN 1 ELSE 0 END) as pending_count'),
              
            )
         
            ->whereIn('nv_id', $BRPLnv)->with(['service', 'material', 'user'])
            ->where('derc_info',1)
            ->where('hod_status',1)
            ->whereYear('created_at', Carbon::now()->year)
            ->groupBy('month')
            ->orderBy('month')
            ->get();
   
        $BRPLlabels = [];
        $BRPLapprovedData = [];
        $BRPLrejectedData = [];
        $BRPLpendingData = [];
    
        foreach ($fileDataBRPL as $dataPointBRPL) {
            $monthBRPL = Carbon::createFromFormat('!m', $dataPointBRPL->month)->format('F');
    
            $BRPLlabels[] = $monthBRPL;
            $BRPLapprovedData[] = $dataPointBRPL->approved_count;
            $BRPLrejectedData[] = $dataPointBRPL->rejected_count;
            $BRPLpendingData[] = $dataPointBRPL->pending_count;
           
        }

      
        if ($fiscal_year) {
            $BYPLnv = NeedValidation::where('company_id', '5')
            ->where('fiscal_year', $fiscal_year)
            ->pluck("id");
        } else {
            $BYPLnv = NeedValidation::where('company_id', '5')
            ->where('fiscal_year', $currentFinancialYear)->pluck("id");
        }

        $BYPLnv = NeedValidation::where('company_id','5')->where('fiscal_year', $currentFinancialYear)->pluck("id");
        $pendingAmountBYPL = DB::table('tbl_material')
        ->join('nvservicestatus', 'tbl_material.id', '=', 'nvservicestatus.material_id')
        ->whereIn('tbl_material.nv_id', $BYPLnv)
       
        ->where("nvservicestatus.hod_status", 1)
        ->where("nvservicestatus.derc_info", 1)
        ->where("nvservicestatus.ces_rew1_status", 0)->where("nvservicestatus.ces_rew2_status", 0)->where("nvservicestatus.ces_rew3_status", 0)->where("nvservicestatus.ces_rew4_status", 0)->where("nvservicestatus.ces_status", 0)
        ->sum('tbl_material.total_budget_both')+DB::table('tbl_service')
        ->join('nvservicestatus', 'tbl_service.id', '=', 'nvservicestatus.service_id')
        ->whereIn('tbl_service.nv_id', $BYPLnv)
      
        ->where("nvservicestatus.hod_status", 1)
        ->where("nvservicestatus.derc_info", 1)
        ->where("nvservicestatus.ces_rew1_status", 0)->where("nvservicestatus.ces_rew2_status", 0)->where("nvservicestatus.ces_rew3_status", 0)->where("nvservicestatus.ces_rew4_status", 0)->where("nvservicestatus.ces_status", 0)
        ->sum('tbl_service.total_buget');

        $rejectedAmountBYPL = DB::table('tbl_material')
        ->join('nvservicestatus', 'tbl_material.id', '=', 'nvservicestatus.material_id')
        ->whereIn('tbl_material.nv_id', $BYPLnv)
        ->where(function ($query) {
            $query->where('nvservicestatus.ces_rew1_status', 2);
        })
        ->sum('tbl_material.total_budget_both')+DB::table('tbl_service')
        ->join('nvservicestatus', 'tbl_service.id', '=', 'nvservicestatus.service_id')
        ->whereIn('tbl_service.nv_id', $BYPLnv)
        ->where(function ($query) {
            $query->where('nvservicestatus.ces_rew1_status', 2);
        })
        ->sum('tbl_service.total_buget');

      $approvedAmountBYPL = DB::table('tbl_material')
        ->join('nvservicestatus', 'tbl_material.id', '=', 'nvservicestatus.material_id')
        ->whereIn('tbl_material.nv_id', $BYPLnv)->where('nvservicestatus.ceo_status', 1)
        ->sum('tbl_material.total_budget_both')+DB::table('tbl_service')
        ->join('nvservicestatus', 'tbl_service.id', '=', 'nvservicestatus.service_id')
        ->whereIn('tbl_service.nv_id', $BYPLnv)->where('nvservicestatus.ceo_status', 1)
        ->sum('tbl_service.total_buget');
         $fileDataBYPL =Nvsericestatus::
        select(
            DB::raw('MONTH(created_at) as month'),
            DB::raw('SUM(CASE WHEN ceo_status = "1" THEN 1 ELSE 0 END) as approved_count'),
            DB::raw('SUM(CASE WHEN ( ces_rew1_status = "2")  THEN 1 ELSE 0 END) as rejected_count'),
            DB::raw('SUM(CASE WHEN (ces_rew1_status = "0" AND ces_rew2_status = "0" AND ces_rew3_status = "0" AND ces_rew4_status = "0" AND ces_status = "0")  THEN 1 ELSE 0 END) as pending_count'),
           
        ) 
        ->whereIn('nv_id', $BYPLnv)->with(['service', 'material', 'user'])
        ->where('derc_info',1)
        ->where('hod_status',1)
        ->whereYear('created_at', Carbon::now()->year)
        ->groupBy('month')
        ->orderBy('month')
        ->get();
       
    $BYPLlabels = [];
    $BYPLapprovedData = [];
    $BYPLrejectedData = [];
    $BYPLpendingData = [];

    foreach ($fileDataBYPL as $dataPointBYPL) {
        $monthBYPL = Carbon::createFromFormat('!m', $dataPointBYPL->month)->format('F');

        $BYPLlabels[] = $monthBYPL;
        $BYPLapprovedData[] = $dataPointBYPL->approved_count;
        $BYPLrejectedData[] = $dataPointBYPL->rejected_count;
        $BYPLpendingData[] = $dataPointBYPL->pending_count;
       
    }
            return view("admin.dashboard", compact("approvedAmount","currentFinancialYear","nextFinancialYear","nextToNextFinancialYear", "rejectedAmount", "pendingAmount", "totalAmount","company","company_id","nv_sm_data", "totalNV", "approvedNV", "rejectedNV", "pendingNV" , "hodApproval", "cpmgApproval", "btApproval", "ceoApproval"             ,'BRPLlabels','BRPLapprovedData','BRPLrejectedData','BRPLpendingData','BYPLlabels','BYPLapprovedData','BYPLrejectedData','BYPLpendingData','cesApproval','ceonominee1Approval','ceonominee2Approval','approvedAmountBYPL','approvedAmountBRPL','rejectedAmountBYPL','rejectedAmountBRPL','pendingAmountBYPL','pendingAmountBRPL','groupcioApproval'));
}
            elseif ($id0->work_rew2 == $user->id) {
                $Values = [$user->id, $id0->work_rew1];
              
                    if($fiscal_year){
                        $totalId = NeedValidation::where('fiscal_year', $fiscal_year)->pluck('id');
                    }else{
                        $totalId = NeedValidation::where('fiscal_year', $currentFinancialYear)->pluck('id');
                    }
               
                $totalAmount = DB::table('tbl_material')
                ->join('nvservicestatus', 'tbl_material.id', '=', 'nvservicestatus.material_id')
            
                 ->whereIn('tbl_material.nv_id', $totalId)
                 ->whereIn('nvservicestatus.nv_id', $latestData->pluck('nv_id'))
                ->where('nvservicestatus.ces_rew2_status', 1)
                ->sum('tbl_material.total_budget_both') + DB::table('tbl_service')
                ->join('nvservicestatus', 'tbl_service.id', '=', 'nvservicestatus.service_id')
              
                 ->whereIn('tbl_service.nv_id', $totalId)
                 ->whereIn('nvservicestatus.nv_id', $latestData->pluck('nv_id'))
                ->where('nvservicestatus.ces_rew2_status', 1)
                ->sum('tbl_service.total_buget');
              
                
                $pendingAmount = DB::table('tbl_material')
                ->join('nvservicestatus', 'tbl_material.id', '=', 'nvservicestatus.material_id')
          
                 ->whereIn('tbl_material.nv_id', $totalId)
                 ->whereIn('nvservicestatus.nv_id', $latestData->pluck('nv_id'))
           
                ->where("nvservicestatus.hod_status", 1)
                ->where("nvservicestatus.derc_info", 1)
                ->where("nvservicestatus.ces_rew1_status", 0)->where("nvservicestatus.ces_rew2_status", 0)->where("nvservicestatus.ces_rew3_status", 0)->where("nvservicestatus.ces_rew4_status", 0)->where("nvservicestatus.ces_status", 0)
                ->sum('tbl_material.total_budget_both') + DB::table('tbl_service')
                ->join('nvservicestatus', 'tbl_service.id', '=', 'nvservicestatus.service_id')
           
                 ->whereIn('tbl_service.nv_id', $totalId)
                 ->whereIn('nvservicestatus.nv_id', $latestData->pluck('nv_id'))
         
                ->where("nvservicestatus.hod_status", 1)
                ->where("nvservicestatus.derc_info", 1)
                ->where("nvservicestatus.ces_rew1_status", 0)->where("nvservicestatus.ces_rew2_status", 0)->where("nvservicestatus.ces_rew3_status", 0)->where("nvservicestatus.ces_rew4_status", 0)->where("nvservicestatus.ces_status", 0)
                ->sum('tbl_service.total_buget');
        
                $rejectedAmount = DB::table('tbl_material')
                ->join('nvservicestatus', 'tbl_material.id', '=', 'nvservicestatus.material_id')
                ->whereIn('nvservicestatus.nv_id', $latestData->pluck('nv_id'))
                ->where(function ($query) {
                    $query->where('nvservicestatus.ces_rew2_status', 2);
                })
                ->sum('tbl_material.total_budget_both') + DB::table('tbl_service')
                ->join('nvservicestatus', 'tbl_service.id', '=', 'nvservicestatus.service_id')
                ->whereIn('nvservicestatus.nv_id', $latestData->pluck('nv_id'))
                ->where(function ($query) {
                    $query->where('nvservicestatus.ces_rew2_status', 2);
                })
                ->sum('tbl_service.total_buget');
           
        
                $approvedAmount = DB::table('tbl_material')
              
                 ->whereIn('tbl_material.nv_id', $totalId)
                 ->whereIn('nvservicestatus.nv_id', $latestData->pluck('nv_id'))
                ->join('nvservicestatus', 'tbl_material.id', '=', 'nvservicestatus.material_id')
                ->where('nvservicestatus.ceo_status', 1)
                ->sum('tbl_material.total_budget_both') +  DB::table('tbl_service')
              
                ->whereIn('tbl_service.nv_id', $totalId)
                ->whereIn('nvservicestatus.nv_id', $latestData->pluck('nv_id'))
               ->join('nvservicestatus', 'tbl_service.id', '=', 'nvservicestatus.service_id')
               ->where('nvservicestatus.ceo_status', 1)
               ->sum('tbl_service.total_buget');

                $totalNV = Nvsericestatus::whereIn("nv_id", $totalId)->where('ces_rew2_status', 1)->count();
                $rejectedNV = Nvsericestatus::whereIn("nv_id", $totalId)->where('ces_rew2_status', 2)->count();
                $latestData = Nvsericestatus::whereIn("nv_id", $totalId)->get();
                $approvedNV = $latestData->where('ceo_status', 1)->count();
              
                
                $pendingNV = $latestData->filter(function ($data) {

                    return in_array($data->hod_status, [1]) &&
                        in_array($data->derc_info, [1]) &&
                        in_array($data->ces_rew1_status, [0])&&
                        in_array($data->ces_rew2_status, [0])&&
                        in_array($data->ces_rew3_status, [0])&&
                        in_array($data->ces_rew4_status, [0])&&
                        in_array($data->ces_status, [0]);
                })->count();
                
                $nvIds = NeedValidation::where("user_id", $user->id)->pluck("id");
               

                if($fiscal_year){
                    $nv = NeedValidation::whereHas('service')->where('fiscal_year', $fiscal_year)->select('id')
            
                    ->get();
                }else{
                    $nv = NeedValidation::whereHas('service')->where('fiscal_year', $currentFinancialYear)->select('id')
            
                    ->get();
                  
                }
                $nv_ids = $nv->pluck('id');
            
                $nv_sm_data = Nvsericestatus::whereIn('nv_id', $nv_ids)->with(['service', 'material', 'user'])
                ->where('derc_info',1)
                ->where('hod_status',1)
              
                ->orderBy('id', 'asc');
                $total01_status = $request->total;
                $pending01_status = $request->pending;
                $reject01_status = $request->rejected;

                if ($ceo_status !== null) {
                    $nv_sm_data = $nv_sm_data->where('ceo_status', $ceo_status);
                } elseif ($pending01_status !== null) {
                    $nv_sm_data = $nv_sm_data->where('ces_rew2_status', $pending01_status)
                       
                       
                        ->whereIn('hod_status', [ 1])
                          
                        ->whereIn('derc_info', [ 1])
                        ->where('ces_rew1_status', 0)
                        ->where('ces_rew3_status', 0)
                        ->where('ces_rew4_status', 0);
                } elseif ($reject01_status !== null) {
                    $nv_sm_data = $nv_sm_data->where('ces_rew2_status', $reject01_status);

                }elseif ($total01_status !== null) {
                    $nv_sm_data = $nv_sm_data->where('ces_rew2_status', $total01_status);
                    
                }
                $nv_sm_data = $nv_sm_data->get();

                if($fiscal_year){
                    $BRPLnv = NeedValidation::where('company_id','6')->where('fiscal_year', $fiscal_year)->pluck("id");
                }else{
                    $BRPLnv = NeedValidation::where('company_id','6')->where('fiscal_year', $currentFinancialYear)->pluck("id");
                  
                }

                
                $pendingAmountBRPL = DB::table('tbl_material')
                ->join('nvservicestatus', 'tbl_material.id', '=', 'nvservicestatus.material_id')
                ->whereIn('tbl_material.nv_id', $BRPLnv)
                ->where("nvservicestatus.hod_status", 1)
                ->where("nvservicestatus.derc_info", 1)
                ->where("nvservicestatus.ces_rew1_status", 0)->where("nvservicestatus.ces_rew2_status", 0)->where("nvservicestatus.ces_rew3_status", 0)->where("nvservicestatus.ces_rew4_status", 0)->where("nvservicestatus.ces_status", 0)
            
                ->sum('tbl_material.total_budget_both')+DB::table('tbl_service')
                ->join('nvservicestatus', 'tbl_service.id', '=', 'nvservicestatus.service_id')
                ->whereIn('tbl_service.nv_id', $BRPLnv)
                ->where("nvservicestatus.hod_status", 1)
                ->where("nvservicestatus.derc_info", 1)
                ->where("nvservicestatus.ces_rew1_status", 0)->where("nvservicestatus.ces_rew2_status", 0)->where("nvservicestatus.ces_rew3_status", 0)->where("nvservicestatus.ces_rew4_status", 0)->where("nvservicestatus.ces_status", 0)
            
                ->sum('tbl_service.total_buget');
        
                $rejectedAmountBRPL = DB::table('tbl_material')
                ->join('nvservicestatus', 'tbl_material.id', '=', 'nvservicestatus.material_id')
                ->whereIn('tbl_material.nv_id', $BRPLnv)
                ->where(function ($query) {
                    $query->where('nvservicestatus.ces_rew2_status', 2);
                })
                ->sum('tbl_material.total_budget_both')+DB::table('tbl_service')
                ->join('nvservicestatus', 'tbl_service.id', '=', 'nvservicestatus.service_id')
                ->whereIn('tbl_service.nv_id', $BRPLnv)
                ->where(function ($query) {
                    $query->where('nvservicestatus.ces_rew2_status', 2);
                })
                ->sum('tbl_service.total_buget');

              $approvedAmountBRPL = DB::table('tbl_material')
                ->join('nvservicestatus', 'tbl_material.id', '=', 'nvservicestatus.material_id')
                ->whereIn('tbl_material.nv_id', $BRPLnv)->where('nvservicestatus.ceo_status', 1)
                ->sum('tbl_material.total_budget_both')+DB::table('tbl_service')
                ->join('nvservicestatus', 'tbl_service.id', '=', 'nvservicestatus.service_id')
                ->whereIn('tbl_service.nv_id', $BRPLnv)->where('nvservicestatus.ceo_status', 1)
                ->sum('tbl_service.total_buget');
                $fileDataBRPL =Nvsericestatus::
                select(
                    DB::raw('MONTH(created_at) as month'),
                    DB::raw('SUM(CASE WHEN ceo_status = "1" THEN 1 ELSE 0 END) as approved_count'),
                    DB::raw('SUM(CASE WHEN ( ces_rew2_status = "2")  THEN 1 ELSE 0 END ) as rejected_count'),
                    DB::raw('SUM(CASE WHEN (ces_rew1_status = "0" AND ces_rew2_status = "0" AND ces_rew3_status = "0" AND ces_rew4_status = "0" AND ces_status = "0")  THEN 1 ELSE 0 END) as pending_count'),
                
                )
            
                ->whereIn('nv_id', $BRPLnv)->with(['service', 'material', 'user'])
                ->where('derc_info',1)
                ->where('hod_status',1)
           
                ->whereYear('created_at', Carbon::now()->year)
                ->groupBy('month')
                ->orderBy('month')
                ->get();

            $BRPLlabels = [];
            $BRPLapprovedData = [];
            $BRPLrejectedData = [];
            $BRPLpendingData = [];

            foreach ($fileDataBRPL as $dataPointBRPL) {
                $monthBRPL = Carbon::createFromFormat('!m', $dataPointBRPL->month)->format('F');

                $BRPLlabels[] = $monthBRPL;
                $BRPLapprovedData[] = $dataPointBRPL->approved_count;
                $BRPLrejectedData[] = $dataPointBRPL->rejected_count;
                $BRPLpendingData[] = $dataPointBRPL->pending_count;

            }
            if($fiscal_year){
                $BYPLnv = NeedValidation::where('company_id','5')->where('fiscal_year', $fiscal_year)->pluck("id");
            }else{
                $BYPLnv = NeedValidation::where('company_id','5')->where('fiscal_year', $currentFinancialYear)->pluck("id");
              
            }
           
            $pendingAmountBYPL = DB::table('tbl_material')
            ->join('nvservicestatus', 'tbl_material.id', '=', 'nvservicestatus.material_id')
            ->whereIn('tbl_material.nv_id', $BYPLnv)
            ->where("nvservicestatus.hod_status", 1)
            ->where("nvservicestatus.derc_info", 1)
            ->where("nvservicestatus.ces_rew1_status", 0)->where("nvservicestatus.ces_rew2_status", 0)->where("nvservicestatus.ces_rew3_status", 0)->where("nvservicestatus.ces_rew4_status", 0)->where("nvservicestatus.ces_status", 0)
  
            ->sum('tbl_material.total_budget_both')+DB::table('tbl_service')
            ->join('nvservicestatus', 'tbl_service.id', '=', 'nvservicestatus.service_id')
            ->whereIn('tbl_service.nv_id', $BYPLnv)
            ->where("nvservicestatus.hod_status", 1)
            ->where("nvservicestatus.derc_info", 1)
            ->where("nvservicestatus.ces_rew1_status", 0)->where("nvservicestatus.ces_rew2_status", 0)->where("nvservicestatus.ces_rew3_status", 0)->where("nvservicestatus.ces_rew4_status", 0)->where("nvservicestatus.ces_status", 0)
       
            ->sum('tbl_service.total_buget');
    
            $rejectedAmountBYPL = DB::table('tbl_material')
            ->join('nvservicestatus', 'tbl_material.id', '=', 'nvservicestatus.material_id')
            ->whereIn('tbl_material.nv_id', $BYPLnv)
            ->where(function ($query) {
                $query->where('nvservicestatus.ces_rew2_status', 2);
            })
            ->sum('tbl_material.total_budget_both')+DB::table('tbl_service')
            ->join('nvservicestatus', 'tbl_service.id', '=', 'nvservicestatus.service_id')
            ->whereIn('tbl_service.nv_id', $BYPLnv)
            ->where(function ($query) {
                $query->where('nvservicestatus.ces_rew2_status', 2);
            })
            ->sum('tbl_service.total_buget');

          $approvedAmountBYPL = DB::table('tbl_material')
            ->join('nvservicestatus', 'tbl_material.id', '=', 'nvservicestatus.material_id')
            ->whereIn('tbl_material.nv_id', $BYPLnv)->where('nvservicestatus.ceo_status', 1)
            ->sum('tbl_material.total_budget_both')+DB::table('tbl_service')
            ->join('nvservicestatus', 'tbl_service.id', '=', 'nvservicestatus.service_id')
            ->whereIn('tbl_service.nv_id', $BYPLnv)->where('nvservicestatus.ceo_status', 1)
            ->sum('tbl_service.total_buget');
            $fileDataBYPL =Nvsericestatus::
            select(
                DB::raw('MONTH(created_at) as month'),
                DB::raw('SUM(CASE WHEN ceo_status = "1" THEN 1 ELSE 0 END) as approved_count'),
                DB::raw('SUM(CASE WHEN ( ces_rew2_status = "2")  THEN 1 ELSE 0 END) as rejected_count'),
                DB::raw('SUM(CASE WHEN (ces_rew1_status = "0" AND ces_rew2_status = "0" AND ces_rew3_status = "0" AND ces_rew4_status = "0" AND ces_status = "0")  THEN 1 ELSE 0 END) as pending_count'),
            
            ) 
            ->whereIn('nv_id', $BYPLnv)->with(['service', 'material', 'user'])
            ->where('derc_info',1)
            ->where('hod_status',1)
         
            ->whereYear('created_at', Carbon::now()->year)
            ->groupBy('month')
            ->orderBy('month')
            ->get();

            $BYPLlabels = [];
            $BYPLapprovedData = [];
            $BYPLrejectedData = [];
            $BYPLpendingData = [];

            foreach ($fileDataBYPL as $dataPointBYPL) {
            $monthBYPL = Carbon::createFromFormat('!m', $dataPointBYPL->month)->format('F');

            $BYPLlabels[] = $monthBYPL;
            $BYPLapprovedData[] = $dataPointBYPL->approved_count;
            $BYPLrejectedData[] = $dataPointBYPL->rejected_count;
            $BYPLpendingData[] = $dataPointBYPL->pending_count;
        
            }
                return view("admin.dashboard", compact("approvedAmount","currentFinancialYear","nextFinancialYear","nextToNextFinancialYear", "rejectedAmount", "pendingAmount", "totalAmount","company","company_id","nv_sm_data", "totalNV", "approvedNV", "rejectedNV", "pendingNV" , "hodApproval", "cpmgApproval", "btApproval", "ceoApproval"             ,'BRPLlabels','BRPLapprovedData','BRPLrejectedData','BRPLpendingData','BYPLlabels','BYPLapprovedData','BYPLrejectedData','BYPLpendingData','cesApproval','ceonominee1Approval','ceonominee2Approval','approvedAmountBYPL','approvedAmountBRPL','rejectedAmountBYPL','rejectedAmountBRPL','pendingAmountBYPL','pendingAmountBRPL','groupcioApproval'));
            }
            elseif ($id0->work_rew3 == $user->id) {
                $Values = [$user->id, $id0->work_rew1,$id0->work_rew2];
               
                
                if($fiscal_year){
                    $totalId = NeedValidation::where('fiscal_year', $fiscal_year)->pluck('id');
                }else{
                    $totalId = NeedValidation::where('fiscal_year', $currentFinancialYear)->pluck('id');
                  
                }
                $totalAmount = DB::table('tbl_material')
                ->join('nvservicestatus', 'tbl_material.id', '=', 'nvservicestatus.material_id')
             
                 ->whereIn('tbl_material.nv_id', $totalId)
                 ->whereIn('nvservicestatus.nv_id', $latestData->pluck('nv_id'))
                ->where('nvservicestatus.ces_rew3_status', 1)
                ->sum('tbl_material.total_budget_both') + DB::table('tbl_service')
                ->join('nvservicestatus', 'tbl_service.id', '=', 'nvservicestatus.service_id')
           
                 ->whereIn('tbl_service.nv_id', $totalId)
                 ->whereIn('nvservicestatus.nv_id', $latestData->pluck('nv_id'))
                ->where('nvservicestatus.ces_rew3_status', 1)
                ->sum('tbl_service.total_buget');
              
                
                $pendingAmount = DB::table('tbl_material')
                ->join('nvservicestatus', 'tbl_material.id', '=', 'nvservicestatus.material_id')
         
                 ->whereIn('tbl_material.nv_id', $totalId)
                 ->whereIn('nvservicestatus.nv_id', $latestData->pluck('nv_id'))
                 ->where("nvservicestatus.hod_status", 1)
                 ->where("nvservicestatus.derc_info", 1)
                 ->where("nvservicestatus.ces_rew1_status", 0)->where("nvservicestatus.ces_rew2_status", 0)->where("nvservicestatus.ces_rew3_status", 0)->where("nvservicestatus.ces_rew4_status", 0)->where("nvservicestatus.ces_status", 0)
              
                ->sum('tbl_material.total_budget_both') +  DB::table('tbl_service')
                ->join('nvservicestatus', 'tbl_service.id', '=', 'nvservicestatus.service_id')
                
                 ->whereIn('tbl_service.nv_id', $totalId)
                 ->whereIn('nvservicestatus.nv_id', $latestData->pluck('nv_id'))
                 ->where("nvservicestatus.hod_status", 1)
                 ->where("nvservicestatus.derc_info", 1)
                 ->where("nvservicestatus.ces_rew1_status", 0)->where("nvservicestatus.ces_rew2_status", 0)->where("nvservicestatus.ces_rew3_status", 0)->where("nvservicestatus.ces_rew4_status", 0)->where("nvservicestatus.ces_status", 0)
              
                ->sum('tbl_service.total_buget');
        
                $rejectedAmount = DB::table('tbl_material')
                ->join('nvservicestatus', 'tbl_material.id', '=', 'nvservicestatus.material_id')
                ->whereIn('nvservicestatus.nv_id', $latestData->pluck('nv_id'))
                ->where(function ($query) {
                    $query->where('nvservicestatus.ces_rew3_status', 2);
                })
                ->sum('tbl_material.total_budget_both') + DB::table('tbl_service')
                ->join('nvservicestatus', 'tbl_service.id', '=', 'nvservicestatus.service_id')
                ->whereIn('nvservicestatus.nv_id', $latestData->pluck('nv_id'))
                ->where(function ($query) {
                    $query->where('nvservicestatus.ces_rew3_status', 2);
                })
                ->sum('tbl_service.total_buget');
           
        
                $approvedAmount = DB::table('tbl_material')
         
                 ->whereIn('tbl_material.nv_id', $totalId)
                 ->whereIn('nvservicestatus.nv_id', $latestData->pluck('nv_id'))
                ->join('nvservicestatus', 'tbl_material.id', '=', 'nvservicestatus.material_id')
                ->where('nvservicestatus.ceo_status', 1)
                ->sum('tbl_material.total_budget_both') +  DB::table('tbl_service')
              
                ->whereIn('tbl_service.nv_id', $totalId)
                ->whereIn('nvservicestatus.nv_id', $latestData->pluck('nv_id'))
               ->join('nvservicestatus', 'tbl_service.id', '=', 'nvservicestatus.service_id')
               ->where('nvservicestatus.ceo_status', 1)
               ->sum('tbl_service.total_buget');

                $totalNV = Nvsericestatus::whereIn("nv_id", $totalId)->where('ces_rew3_status', 1)->count();
                $rejectedNV = Nvsericestatus::whereIn("nv_id", $totalId)->where('ces_rew3_status', 2)->count();
                $latestData = Nvsericestatus::whereIn("nv_id", $totalId)->get();
                $approvedNV = $latestData->where('ceo_status', 1)->count();
                
            
                $pendingNV = $latestData->filter(function ($data) {

                    return in_array($data->hod_status, [1]) &&
                        in_array($data->derc_info, [1]) &&
                        in_array($data->ces_rew1_status, [0])&&
                        in_array($data->ces_rew2_status, [0])&&
                        in_array($data->ces_rew3_status, [0])&&
                        in_array($data->ces_rew4_status, [0])&&
                        in_array($data->ces_status, [0]);
                })->count();
                
                $nvIds = NeedValidation::where("user_id", $user->id)->pluck("id");
                
                if($fiscal_year){
                    $nv = NeedValidation::whereHas('service')->select('id')->where('fiscal_year', $fiscal_year)
            
                    ->get();
              }else{

                $nv = NeedValidation::whereHas('service')->select('id')->where('fiscal_year', $currentFinancialYear)
            
                ->get();
              }
                $nv_ids = $nv->pluck('id');
            
                $nv_sm_data = Nvsericestatus::whereIn('nv_id', $nv_ids)->with(['service', 'material', 'user'])
                ->where('derc_info',1)
                ->where('hod_status',1)
             
                ->orderBy('id', 'asc');
                $total02_status = $request->total;
                $pending02_status = $request->pending;
                $reject02_status = $request->rejected;

                if ($ceo_status !== null) {
                    $nv_sm_data = $nv_sm_data->where('ceo_status', $ceo_status);
                } elseif ($pending02_status !== null) {
                    $nv_sm_data = $nv_sm_data->where('ces_rew3_status', $pending02_status)
                       
                       
                        ->whereIn('hod_status', [ 1])
                         
                        ->whereIn('derc_info', [ 1])
                        ->where('ces_rew1_status',0)
                        ->where('ces_rew2_status',0)
                        ->where('ces_rew4_status',0);
                } elseif ($reject02_status !== null) {
                    $nv_sm_data = $nv_sm_data->where('ces_rew3_status', $reject02_status);

                }elseif ($total02_status !== null) {
                    $nv_sm_data = $nv_sm_data->where('ces_rew3_status', $total02_status);
                    
                }
                $nv_sm_data = $nv_sm_data->get();

           if($fiscal_year){
            $BRPLnv = NeedValidation::where('company_id','6')->where('fiscal_year', $fiscal_year)->pluck("id");
          }else{
            $BRPLnv = NeedValidation::where('company_id','6')->where('fiscal_year', $currentFinancialYear)->pluck("id");
            
          }

               
                $pendingAmountBRPL = DB::table('tbl_material')
                ->join('nvservicestatus', 'tbl_material.id', '=', 'nvservicestatus.material_id')
                ->whereIn('tbl_material.nv_id', $BRPLnv)
                ->where("nvservicestatus.hod_status", 1)
                ->where("nvservicestatus.derc_info", 1)
                ->where("nvservicestatus.ces_rew1_status", 0)->where("nvservicestatus.ces_rew2_status", 0)->where("nvservicestatus.ces_rew3_status", 0)->where("nvservicestatus.ces_rew4_status", 0)->where("nvservicestatus.ces_status", 0)
       
                ->sum('tbl_material.total_budget_both')+DB::table('tbl_service')
                ->join('nvservicestatus', 'tbl_service.id', '=', 'nvservicestatus.service_id')
                ->whereIn('tbl_service.nv_id', $BRPLnv)
                ->where("nvservicestatus.hod_status", 1)
                ->where("nvservicestatus.derc_info", 1)
                ->where("nvservicestatus.ces_rew1_status", 0)->where("nvservicestatus.ces_rew2_status", 0)->where("nvservicestatus.ces_rew3_status", 0)->where("nvservicestatus.ces_rew4_status", 0)->where("nvservicestatus.ces_status", 0)
          
                ->sum('tbl_service.total_buget');
        
                $rejectedAmountBRPL = DB::table('tbl_material')
                ->join('nvservicestatus', 'tbl_material.id', '=', 'nvservicestatus.material_id')
                ->whereIn('tbl_material.nv_id', $BRPLnv)
                ->where(function ($query) {
                    $query->where('nvservicestatus.ces_rew3_status', 2);
                })
                ->sum('tbl_material.total_budget_both')+DB::table('tbl_service')
                ->join('nvservicestatus', 'tbl_service.id', '=', 'nvservicestatus.service_id')
                ->whereIn('tbl_service.nv_id', $BRPLnv)
                ->where(function ($query) {
                    $query->where('nvservicestatus.ces_rew3_status', 2);
                })
                ->sum('tbl_service.total_buget');

              $approvedAmountBRPL = DB::table('tbl_material')
                ->join('nvservicestatus', 'tbl_material.id', '=', 'nvservicestatus.material_id')
                ->whereIn('tbl_material.nv_id', $BRPLnv)->where('nvservicestatus.ceo_status', 1)
                ->sum('tbl_material.total_budget_both')+DB::table('tbl_service')
                ->join('nvservicestatus', 'tbl_service.id', '=', 'nvservicestatus.service_id')
                ->whereIn('tbl_service.nv_id', $BRPLnv)->where('nvservicestatus.ceo_status', 1)
                ->sum('tbl_service.total_buget');
                $fileDataBRPL =Nvsericestatus::
                select(
                    DB::raw('MONTH(created_at) as month'),
                    DB::raw('SUM(CASE WHEN ceo_status = "1" THEN 1 ELSE 0 END) as approved_count'),
                    DB::raw('SUM(CASE WHEN ( ces_rew3_status = "2")  THEN 1 ELSE 0 END ) as rejected_count'),
                    DB::raw('SUM(CASE WHEN (ces_rew1_status = "0" AND ces_rew2_status = "0" AND ces_rew3_status = "0" AND ces_rew4_status = "0" AND ces_status = "0")  THEN 1 ELSE 0 END) as pending_count'),
                
                )
            
                ->whereIn('nv_id', $BRPLnv)->with(['service', 'material', 'user'])
                ->where('derc_info',1)
                ->where('hod_status',1)
             
                ->whereYear('created_at', Carbon::now()->year)
                ->groupBy('month')
                ->orderBy('month')
                ->get();

            $BRPLlabels = [];
            $BRPLapprovedData = [];
            $BRPLrejectedData = [];
            $BRPLpendingData = [];

            foreach ($fileDataBRPL as $dataPointBRPL) {
                $monthBRPL = Carbon::createFromFormat('!m', $dataPointBRPL->month)->format('F');

                $BRPLlabels[] = $monthBRPL;
                $BRPLapprovedData[] = $dataPointBRPL->approved_count;
                $BRPLrejectedData[] = $dataPointBRPL->rejected_count;
                $BRPLpendingData[] = $dataPointBRPL->pending_count;

            }
            if($fiscal_year){
                $BYPLnv = NeedValidation::where('company_id','5')->where('fiscal_year', $fiscal_year)->pluck("id");
            }else{
                $BYPLnv = NeedValidation::where('company_id','5')->where('fiscal_year', $currentFinancialYear)->pluck("id");
              
            }
            
            $pendingAmountBYPL = DB::table('tbl_material')
            ->join('nvservicestatus', 'tbl_material.id', '=', 'nvservicestatus.material_id')
            ->whereIn('tbl_material.nv_id', $BYPLnv)
            ->where("nvservicestatus.hod_status", 1)
            ->where("nvservicestatus.derc_info", 1)
            ->where("nvservicestatus.ces_rew1_status", 0)->where("nvservicestatus.ces_rew2_status", 0)->where("nvservicestatus.ces_rew3_status", 0)->where("nvservicestatus.ces_rew4_status", 0)->where("nvservicestatus.ces_status", 0)
       
            ->sum('tbl_material.total_budget_both')+DB::table('tbl_service')
            ->join('nvservicestatus', 'tbl_service.id', '=', 'nvservicestatus.service_id')
            ->whereIn('tbl_service.nv_id', $BYPLnv)
            ->where("nvservicestatus.hod_status", 1)
            ->where("nvservicestatus.derc_info", 1)
            ->where("nvservicestatus.ces_rew1_status", 0)->where("nvservicestatus.ces_rew2_status", 0)->where("nvservicestatus.ces_rew3_status", 0)->where("nvservicestatus.ces_rew4_status", 0)->where("nvservicestatus.ces_status", 0)
          
            ->sum('tbl_service.total_buget');
    
            $rejectedAmountBYPL = DB::table('tbl_material')
            ->join('nvservicestatus', 'tbl_material.id', '=', 'nvservicestatus.material_id')
            ->whereIn('tbl_material.nv_id', $BYPLnv)
            ->where(function ($query) {
                $query->where('nvservicestatus.ces_rew3_status', 2);
            })
            ->sum('tbl_material.total_budget_both')+DB::table('tbl_service')
            ->join('nvservicestatus', 'tbl_service.id', '=', 'nvservicestatus.service_id')
            ->whereIn('tbl_service.nv_id', $BYPLnv)
            ->where(function ($query) {
                $query->where('nvservicestatus.ces_rew3_status', 2);
            })
            ->sum('tbl_service.total_buget');

          $approvedAmountBYPL = DB::table('tbl_material')
            ->join('nvservicestatus', 'tbl_material.id', '=', 'nvservicestatus.material_id')
            ->whereIn('tbl_material.nv_id', $BYPLnv)->where('nvservicestatus.ceo_status', 1)
            ->sum('tbl_material.total_budget_both')+DB::table('tbl_service')
            ->join('nvservicestatus', 'tbl_service.id', '=', 'nvservicestatus.service_id')
            ->whereIn('tbl_service.nv_id', $BYPLnv)->where('nvservicestatus.ceo_status', 1)
            ->sum('tbl_service.total_buget');
            $fileDataBYPL =Nvsericestatus::
            select(
                DB::raw('MONTH(created_at) as month'),
                DB::raw('SUM(CASE WHEN ceo_status = "1" THEN 1 ELSE 0 END) as approved_count'),
                DB::raw('SUM(CASE WHEN ( ces_rew3_status = "2")  THEN 1 ELSE 0 END) as rejected_count'),
                DB::raw('SUM(CASE WHEN (ces_rew1_status = "0" AND ces_rew2_status = "0" AND ces_rew3_status = "0" AND ces_rew4_status = "0" AND ces_status = "0")  THEN 1 ELSE 0 END) as pending_count'),
            
            ) 
            ->whereIn('nv_id', $BYPLnv)->with(['service', 'material', 'user'])
            ->where('derc_info',1)
            ->where('hod_status',1)
        
            ->whereYear('created_at', Carbon::now()->year)
            ->groupBy('month')
            ->orderBy('month')
            ->get();

            $BYPLlabels = [];
            $BYPLapprovedData = [];
            $BYPLrejectedData = [];
            $BYPLpendingData = [];

            foreach ($fileDataBYPL as $dataPointBYPL) {
            $monthBYPL = Carbon::createFromFormat('!m', $dataPointBYPL->month)->format('F');

            $BYPLlabels[] = $monthBYPL;
            $BYPLapprovedData[] = $dataPointBYPL->approved_count;
            $BYPLrejectedData[] = $dataPointBYPL->rejected_count;
            $BYPLpendingData[] = $dataPointBYPL->pending_count;
  
            }
                return view("admin.dashboard", compact("approvedAmount","currentFinancialYear","nextFinancialYear","nextToNextFinancialYear", "rejectedAmount", "pendingAmount", "totalAmount","company","company_id","nv_sm_data", "totalNV", "approvedNV", "rejectedNV", "pendingNV" , "hodApproval", "cpmgApproval", "btApproval", "ceoApproval"             ,'BRPLlabels','BRPLapprovedData','BRPLrejectedData','BRPLpendingData','BYPLlabels','BYPLapprovedData','BYPLrejectedData','BYPLpendingData','cesApproval','ceonominee1Approval','ceonominee2Approval','approvedAmountBYPL','approvedAmountBRPL','rejectedAmountBYPL','rejectedAmountBRPL','pendingAmountBYPL','pendingAmountBRPL','groupcioApproval'));
            }
            elseif ($id0->work_rew4 == $user->id) {
                $Values = [$user->id, $id0->work_rew1,$id0->work_rew2,$id0->work_rew3];
              if($fiscal_year){
                    $totalId = NeedValidation::where('fiscal_year', $fiscal_year)->pluck('id');
                }else{
                    $totalId = NeedValidation::where('fiscal_year', $currentFinancialYear)->pluck('id');
                  
                }
                $totalAmount = DB::table('tbl_material')
                ->join('nvservicestatus', 'tbl_material.id', '=', 'nvservicestatus.material_id')
         
                 ->whereIn('tbl_material.nv_id', $totalId)
                 ->whereIn('nvservicestatus.nv_id', $latestData->pluck('nv_id'))
                ->where('nvservicestatus.ces_rew4_status', 1)
                ->sum('tbl_material.total_budget_both') +  DB::table('tbl_service')
                ->join('nvservicestatus', 'tbl_service.id', '=', 'nvservicestatus.service_id')
               
                 ->whereIn('tbl_service.nv_id', $totalId)
                 ->whereIn('nvservicestatus.nv_id', $latestData->pluck('nv_id'))
                ->where('nvservicestatus.ces_rew4_status', 1)
                ->sum('tbl_service.total_buget');
              
                
                $pendingAmount = DB::table('tbl_material')
                ->join('nvservicestatus', 'tbl_material.id', '=', 'nvservicestatus.material_id')
           
                 ->whereIn('tbl_material.nv_id', $totalId)
                 ->whereIn('nvservicestatus.nv_id', $latestData->pluck('nv_id'))
                 ->where("nvservicestatus.hod_status", 1)
                 ->where("nvservicestatus.derc_info", 1)
                 ->where("nvservicestatus.ces_rew1_status", 0)->where("nvservicestatus.ces_rew2_status", 0)->where("nvservicestatus.ces_rew3_status", 0)->where("nvservicestatus.ces_rew4_status", 0)->where("nvservicestatus.ces_status", 0)
          
                ->sum('tbl_material.total_budget_both') +  DB::table('tbl_service')
                ->join('nvservicestatus', 'tbl_service.id', '=', 'nvservicestatus.service_id')
               
                 ->whereIn('tbl_service.nv_id', $totalId)
                 ->whereIn('nvservicestatus.nv_id', $latestData->pluck('nv_id'))
                 ->where("nvservicestatus.hod_status", 1)
                 ->where("nvservicestatus.derc_info", 1)
                 ->where("nvservicestatus.ces_rew1_status", 0)->where("nvservicestatus.ces_rew2_status", 0)->where("nvservicestatus.ces_rew3_status", 0)->where("nvservicestatus.ces_rew4_status", 0)->where("nvservicestatus.ces_status", 0)
              
                ->sum('tbl_service.total_buget');
        
                $rejectedAmount = DB::table('tbl_material')
                ->join('nvservicestatus', 'tbl_material.id', '=', 'nvservicestatus.material_id')
                ->whereIn('nvservicestatus.nv_id', $latestData->pluck('nv_id'))
                ->where(function ($query) {
                    $query->where('nvservicestatus.ces_rew4_status', 2);
                })
                ->sum('tbl_material.total_budget_both') + DB::table('tbl_service')
                ->join('nvservicestatus', 'tbl_service.id', '=', 'nvservicestatus.service_id')
                ->whereIn('nvservicestatus.nv_id', $latestData->pluck('nv_id'))
                ->where(function ($query) {
                    $query->where('nvservicestatus.ces_rew4_status', 2);
                })
                ->sum('tbl_service.total_buget');
           
        
                $approvedAmount = DB::table('tbl_material')
               
                 ->whereIn('tbl_material.nv_id', $totalId)
                 ->whereIn('nvservicestatus.nv_id', $latestData->pluck('nv_id'))
                ->join('nvservicestatus', 'tbl_material.id', '=', 'nvservicestatus.material_id')
                ->where('nvservicestatus.ceo_status', 1)
                ->sum('tbl_material.total_budget_both') + DB::table('tbl_service')
          
                ->whereIn('tbl_service.nv_id', $totalId)
                ->whereIn('nvservicestatus.nv_id', $latestData->pluck('nv_id'))
               ->join('nvservicestatus', 'tbl_service.id', '=', 'nvservicestatus.service_id')
               ->where('nvservicestatus.ceo_status', 1)
               ->sum('tbl_service.total_buget');
                $totalNV = Nvsericestatus::whereIn("nv_id", $totalId)->where('ces_rew4_status', 1)->count();
                $rejectedNV = Nvsericestatus::whereIn("nv_id", $totalId)->where('ces_rew4_status', 2)->count();
                $latestData = Nvsericestatus::whereIn("nv_id", $totalId)->get();
                $approvedNV = $latestData->where('ceo_status', 1)->count();
                
              
                $pendingNV = $latestData->filter(function ($data) {

                    return in_array($data->hod_status, [1]) &&
                        in_array($data->derc_info, [1]) &&
                        in_array($data->ces_rew1_status, [0])&&
                        in_array($data->ces_rew2_status, [0])&&
                        in_array($data->ces_rew3_status, [0])&&
                        in_array($data->ces_rew4_status, [0])&&
                        in_array($data->ces_status, [0]);
                })->count();
            
                $nvIds = NeedValidation::where("user_id", $user->id)->pluck("id");
                
                if($fiscal_year){
                    $nv = NeedValidation::whereHas('service')->select('id')->where('fiscal_year', $fiscal_year)
            
                    ->get();
                }else{
                    $nv = NeedValidation::whereHas('service')->select('id')->where('fiscal_year', $currentFinancialYear)
            
                    ->get();
                  
                }
                $nv_ids = $nv->pluck('id');
            
                $nv_sm_data = Nvsericestatus::whereIn('nv_id', $nv_ids)->with(['service', 'material', 'user'])
                ->where('derc_info',1)
                ->where('hod_status',1)
                ->orderBy('id', 'asc');
                $total03_status = $request->total;
                $pending03_status = $request->pending;
                $reject03_status = $request->rejected;

                if ($ceo_status !== null) {
                    $nv_sm_data = $nv_sm_data->where('ceo_status', $ceo_status);
                } elseif ($pending03_status !== null) {
                    $nv_sm_data = $nv_sm_data->where('ces_rew4_status', $pending03_status)
                       
                      
                        ->whereIn('hod_status', [ 1])
                         
                        ->whereIn('derc_info', [ 1])
                        ->where('ces_rew1_status',0)
                        ->where('ces_rew2_status',0)
                        ->where('ces_rew3_status',0);
                } elseif ($reject03_status !== null) {
                    $nv_sm_data = $nv_sm_data->where('ces_rew4_status', $reject03_status);

                }elseif ($total03_status !== null) {
                    $nv_sm_data = $nv_sm_data->where('ces_rew4_status', $total03_status);
                    
                }
                $nv_sm_data = $nv_sm_data->get();

                if($fiscal_year){
                    $BRPLnv = NeedValidation::where('company_id','6')->where('fiscal_year', $fiscal_year)->pluck("id");
                }else{
                    $BRPLnv = NeedValidation::where('company_id','6')->where('fiscal_year', $currentFinancialYear)->pluck("id");
                    
                }
                
                $pendingAmountBRPL = DB::table('tbl_material')
                ->join('nvservicestatus', 'tbl_material.id', '=', 'nvservicestatus.material_id')
                ->whereIn('tbl_material.nv_id', $BRPLnv)
                ->where("nvservicestatus.hod_status", 1)
                ->where("nvservicestatus.derc_info", 1)
                ->where("nvservicestatus.ces_rew1_status", 0)->where("nvservicestatus.ces_rew2_status", 0)->where("nvservicestatus.ces_rew3_status", 0)->where("nvservicestatus.ces_rew4_status", 0)->where("nvservicestatus.ces_status", 0)
            
                ->sum('tbl_material.total_budget_both')+DB::table('tbl_service')
                ->join('nvservicestatus', 'tbl_service.id', '=', 'nvservicestatus.service_id')
                ->whereIn('tbl_service.nv_id', $BRPLnv)
                ->where("nvservicestatus.hod_status", 1)
                ->where("nvservicestatus.derc_info", 1)
                ->where("nvservicestatus.ces_rew1_status", 0)->where("nvservicestatus.ces_rew2_status", 0)->where("nvservicestatus.ces_rew3_status", 0)->where("nvservicestatus.ces_rew4_status", 0)->where("nvservicestatus.ces_status", 0)
            
                ->sum('tbl_service.total_buget');
        
                $rejectedAmountBRPL = DB::table('tbl_material')
                ->join('nvservicestatus', 'tbl_material.id', '=', 'nvservicestatus.material_id')
                ->whereIn('tbl_material.nv_id', $BRPLnv)
                ->where(function ($query) {
                    $query->where('nvservicestatus.ces_rew4_status', 2);
                })
                ->sum('tbl_material.total_budget_both')+DB::table('tbl_service')
                ->join('nvservicestatus', 'tbl_service.id', '=', 'nvservicestatus.service_id')
                ->whereIn('tbl_service.nv_id', $BRPLnv)
                ->where(function ($query) {
                    $query->where('nvservicestatus.ces_rew4_status', 2);
                })
                ->sum('tbl_service.total_buget');

              $approvedAmountBRPL = DB::table('tbl_material')
                ->join('nvservicestatus', 'tbl_material.id', '=', 'nvservicestatus.material_id')
                ->whereIn('tbl_material.nv_id', $BRPLnv)->where('nvservicestatus.ceo_status', 1)
                ->sum('tbl_material.total_budget_both')+DB::table('tbl_service')
                ->join('nvservicestatus', 'tbl_service.id', '=', 'nvservicestatus.service_id')
                ->whereIn('tbl_service.nv_id', $BRPLnv)->where('nvservicestatus.ceo_status', 1)
                ->sum('tbl_service.total_buget');

                $fileDataBRPL =Nvsericestatus::
                select(
                    DB::raw('MONTH(created_at) as month'),
                    DB::raw('SUM(CASE WHEN ceo_status = "1" THEN 1 ELSE 0 END) as approved_count'),
                    DB::raw('SUM(CASE WHEN ( ces_rew4_status = "2")  THEN 1 ELSE 0 END ) as rejected_count'),
                    DB::raw('SUM(CASE WHEN (ces_rew1_status = "0" AND ces_rew2_status = "0" AND ces_rew3_status = "0" AND ces_rew4_status = "0" AND ces_status = "0")  THEN 1 ELSE 0 END) as pending_count'),
                
                )
            
                ->whereIn('nv_id', $BRPLnv)->with(['service', 'material', 'user'])
                ->where('derc_info',1)
                ->where('hod_status',1)
                ->whereYear('created_at', Carbon::now()->year)
                ->groupBy('month')
                ->orderBy('month')
                ->get();

            $BRPLlabels = [];
            $BRPLapprovedData = [];
            $BRPLrejectedData = [];
            $BRPLpendingData = [];

            foreach ($fileDataBRPL as $dataPointBRPL) {
                $monthBRPL = Carbon::createFromFormat('!m', $dataPointBRPL->month)->format('F');

                $BRPLlabels[] = $monthBRPL;
                $BRPLapprovedData[] = $dataPointBRPL->approved_count;
                $BRPLrejectedData[] = $dataPointBRPL->rejected_count;
                $BRPLpendingData[] = $dataPointBRPL->pending_count;

            }

            if($fiscal_year){
                $BYPLnv = NeedValidation::where('company_id','5')->where('fiscal_year', $fiscal_year)->pluck("id");
            }else{
                $BYPLnv = NeedValidation::where('company_id','5')->where('fiscal_year', $currentFinancialYear)->pluck("id");
              
            }
            
            $pendingAmountBYPL = DB::table('tbl_material')
            ->join('nvservicestatus', 'tbl_material.id', '=', 'nvservicestatus.material_id')
            ->whereIn('tbl_material.nv_id', $BYPLnv)
            ->where("nvservicestatus.hod_status", 1)
            ->where("nvservicestatus.derc_info", 1)
            ->where("nvservicestatus.ces_rew1_status", 0)->where("nvservicestatus.ces_rew2_status", 0)->where("nvservicestatus.ces_rew3_status", 0)->where("nvservicestatus.ces_rew4_status", 0)->where("nvservicestatus.ces_status", 0)
           
            ->sum('tbl_material.total_budget_both')+DB::table('tbl_service')
            ->join('nvservicestatus', 'tbl_service.id', '=', 'nvservicestatus.service_id')
            ->whereIn('tbl_service.nv_id', $BYPLnv)
            ->where("nvservicestatus.hod_status", 1)
            ->where("nvservicestatus.derc_info", 1)
            ->where("nvservicestatus.ces_rew1_status", 0)->where("nvservicestatus.ces_rew2_status", 0)->where("nvservicestatus.ces_rew3_status", 0)->where("nvservicestatus.ces_rew4_status", 0)->where("nvservicestatus.ces_status", 0)
         
            ->sum('tbl_service.total_buget');
    
            $rejectedAmountBYPL = DB::table('tbl_material')
            ->join('nvservicestatus', 'tbl_material.id', '=', 'nvservicestatus.material_id')
            ->whereIn('tbl_material.nv_id', $BYPLnv)
            ->where(function ($query) {
                $query->where('nvservicestatus.ces_rew4_status', 2);
            })
            ->sum('tbl_material.total_budget_both')+DB::table('tbl_service')
            ->join('nvservicestatus', 'tbl_service.id', '=', 'nvservicestatus.service_id')
            ->whereIn('tbl_service.nv_id', $BYPLnv)
            ->where(function ($query) {
                $query->where('nvservicestatus.ces_rew4_status', 2);
            })
            ->sum('tbl_service.total_buget');

          $approvedAmountBYPL = DB::table('tbl_material')
            ->join('nvservicestatus', 'tbl_material.id', '=', 'nvservicestatus.material_id')
            ->whereIn('tbl_material.nv_id', $BYPLnv)->where('nvservicestatus.ceo_status', 1)
            ->sum('tbl_material.total_budget_both')+DB::table('tbl_service')
            ->join('nvservicestatus', 'tbl_service.id', '=', 'nvservicestatus.service_id')
            ->whereIn('tbl_service.nv_id', $BYPLnv)->where('nvservicestatus.ceo_status', 1)
            ->sum('tbl_service.total_buget');
            $fileDataBYPL =Nvsericestatus::
            select(
                DB::raw('MONTH(created_at) as month'),
                DB::raw('SUM(CASE WHEN ceo_status = "1" THEN 1 ELSE 0 END) as approved_count'),
                DB::raw('SUM(CASE WHEN ( ces_rew4_status = "2")  THEN 1 ELSE 0 END) as rejected_count'),
                DB::raw('SUM(CASE WHEN (ces_rew1_status = "0" AND ces_rew2_status = "0" AND ces_rew3_status = "0" AND ces_rew4_status = "0" AND ces_status = "0")  THEN 1 ELSE 0 END) as pending_count'),
            
            ) 
            ->whereIn('nv_id', $BYPLnv)->with(['service', 'material', 'user'])
            ->where('derc_info',1)
            ->where('hod_status',1)
            ->whereYear('created_at', Carbon::now()->year)
            ->groupBy('month')
            ->orderBy('month')
            ->get();

            $BYPLlabels = [];
            $BYPLapprovedData = [];
            $BYPLrejectedData = [];
            $BYPLpendingData = [];

            foreach ($fileDataBYPL as $dataPointBYPL) {
            $monthBYPL = Carbon::createFromFormat('!m', $dataPointBYPL->month)->format('F');

            $BYPLlabels[] = $monthBYPL;
            $BYPLapprovedData[] = $dataPointBYPL->approved_count;
            $BYPLrejectedData[] = $dataPointBYPL->rejected_count;
            $BYPLpendingData[] = $dataPointBYPL->pending_count;
            // dd( $dataPointBYPL->rejected_count);
            }
                return view("admin.dashboard", compact("approvedAmount","currentFinancialYear","nextFinancialYear","nextToNextFinancialYear", "rejectedAmount", 
                "pendingAmount", "totalAmount","company","company_id","nv_sm_data", "totalNV", "approvedNV", 
                "rejectedNV", "pendingNV" , "hodApproval", "cpmgApproval", "btApproval", "ceoApproval", 
                "dpnv"           ,
                'BRPLlabels','BRPLapprovedData','BRPLrejectedData','BRPLpendingData','BYPLlabels','BYPLapprovedData','BYPLrejectedData','BYPLpendingData','cesApproval','ceonominee1Approval','ceonominee2Approval','approvedAmountBYPL','approvedAmountBRPL','rejectedAmountBYPL','rejectedAmountBRPL','pendingAmountBYPL','pendingAmountBRPL','groupcioApproval'));
            }
         
            elseif ($id0->approver == $user->id) {
                
                $Values = [$user->id, $id0->work_rew1,$id0->work_rew2,$id0->work_rew3,$id0->work_rew4];
              

                if($fiscal_year){
                    $totalId = NeedValidation::where('fiscal_year', $fiscal_year)->pluck('id');
                }else{
                    $totalId = NeedValidation::where('fiscal_year', $currentFinancialYear)->pluck('id');
                }
               
                
                $totalAmount = DB::table('tbl_material')
                ->join('nvservicestatus', 'tbl_material.id', '=', 'nvservicestatus.material_id')
                
                 ->whereIn('tbl_material.nv_id', $totalId)
                 ->whereIn('nvservicestatus.nv_id', $latestData->pluck('nv_id'))
                ->where('nvservicestatus.ces_status', 1)
                ->sum('tbl_material.total_budget_both') +  DB::table('tbl_service')
                ->join('nvservicestatus', 'tbl_service.id', '=', 'nvservicestatus.service_id')
              
                 ->whereIn('tbl_service.nv_id', $totalId)
                 ->whereIn('nvservicestatus.nv_id', $latestData->pluck('nv_id'))
                ->where('nvservicestatus.ces_status', 1)
                ->sum('tbl_service.total_buget');
                if(!empty($id0->work_rew1) || !empty($id0->work_rew2)|| !empty($id0->work_rew3)|| !empty($id0->work_rew4)){
             
                    $pen_amt = $latestData->filter(function ($data) {
                    return in_array($data->hod_status, [1]) &&
                       ( in_array($data->ces_rew1_status, [1])||in_array($data->ces_rew2_status, [1])||in_array($data->ces_rew3_status, [1])||in_array($data->ces_rew4_status, [1])) &&
                        in_array($data->derc_info, [1]) &&
                        in_array($data->ces_status, [0]);
                });
               }elseif(empty($id0->work_rew1) && empty($id0->work_rew2) && empty($id0->work_rew3) && empty($id0->work_rew4)){
                $pen_amt = $latestData->filter(function ($data) {
                    return in_array($data->hod_status, [1]) &&
                       
                        in_array($data->derc_info, [1]) &&
                        in_array($data->ces_status, [0]);
                });
               }
                $pendingAmount = DB::table('tbl_material')
                ->join('nvservicestatus', 'tbl_material.id', '=', 'nvservicestatus.material_id')
             
                 ->whereIn('tbl_material.nv_id', $totalId)
                 ->whereIn('nvservicestatus.nv_id', $pen_amt->pluck('nv_id'))       
                ->sum('tbl_material.total_budget_both') + DB::table('tbl_service')
                ->join('nvservicestatus', 'tbl_service.id', '=', 'nvservicestatus.service_id')
                 ->whereIn('tbl_service.nv_id', $totalId)
                 ->whereIn('nvservicestatus.nv_id', $pen_amt->pluck('nv_id'))
                ->sum('tbl_service.total_buget');
                
        
                $rejectedAmount = DB::table('tbl_material')
                ->join('nvservicestatus', 'tbl_material.id', '=', 'nvservicestatus.material_id')
                ->whereIn('nvservicestatus.nv_id', $latestData->pluck('nv_id'))
                ->where(function ($query) {
                    $query->where('nvservicestatus.ces_status', 2);
                })
                ->sum('tbl_material.total_budget_both') + DB::table('tbl_service')
                ->join('nvservicestatus', 'tbl_service.id', '=', 'nvservicestatus.service_id')
                ->whereIn('nvservicestatus.nv_id', $latestData->pluck('nv_id'))
                ->where(function ($query) {
                    $query->where('nvservicestatus.ces_status', 2);
                })
                ->sum('tbl_service.total_buget');
           
        
                $approvedAmount = DB::table('tbl_material')
           
                 ->whereIn('tbl_material.nv_id', $totalId)
                 ->whereIn('nvservicestatus.nv_id', $latestData->pluck('nv_id'))
                ->join('nvservicestatus', 'tbl_material.id', '=', 'nvservicestatus.material_id')
                ->where('nvservicestatus.ceo_status', 1)
                ->sum('tbl_material.total_budget_both') + DB::table('tbl_service')
             
                ->whereIn('tbl_service.nv_id', $totalId)
                ->whereIn('nvservicestatus.nv_id', $latestData->pluck('nv_id'))
               ->join('nvservicestatus', 'tbl_service.id', '=', 'nvservicestatus.service_id')
               ->where('nvservicestatus.ceo_status', 1)
               ->sum('tbl_service.total_buget');

                $totalNV = Nvsericestatus::whereIn("nv_id", $totalId)->where('ces_status', 1)->count();
                $rejectedNV = Nvsericestatus::whereIn("nv_id", $totalId)->where('ces_status', 2)->count();
                $latestData = Nvsericestatus::whereIn("nv_id", $totalId)->get();
                $approvedNV = $latestData->where('ceo_status', 1)->count();
                
               

                if(!empty($id0->work_rew1) || !empty($id0->work_rew2)|| !empty($id0->work_rew3)|| !empty($id0->work_rew4)){
                    $pendingNV = $latestData->filter(function ($data) {
                        return in_array($data->hod_status, [1]) &&
                           ( in_array($data->ces_rew1_status, [1])||in_array($data->ces_rew2_status, [1])||in_array($data->ces_rew3_status, [1])||in_array($data->ces_rew4_status, [1])) &&
                            in_array($data->derc_info, [1]) &&
                            in_array($data->ces_status, [0]);
                    })->count();
                   }elseif(empty($id0->work_rew1) && empty($id0->work_rew2) && empty($id0->work_rew3) && empty($id0->work_rew4)){
                    $pendingNV = $latestData->filter(function ($data) {
                        return in_array($data->hod_status, [1]) &&
                          
                            in_array($data->derc_info, [1]) &&
                            in_array($data->ces_status, [0]);
                    })->count();
                   }

                 $nvIds = NeedValidation::where("user_id", $user->id)->pluck("id");
                
                if($fiscal_year){
                    $nv = NeedValidation::whereHas('service')->select('id')->where('fiscal_year', $fiscal_year)
            
                    ->get();
                }else{
                    $nv = NeedValidation::whereHas('service')->select('id')->where('fiscal_year', $currentFinancialYear)
            
                    ->get();
                }

                $nv_ids = $nv->pluck('id');

                
              
                if(!empty($id0->work_rew1) || !empty($id0->work_rew2)|| !empty($id0->work_rew3)|| !empty($id0->work_rew4)){
                $nv_sm_data = Nvsericestatus::whereIn('nv_id', $nv_ids)->with(['service', 'material', 'user'])->where(function ($query) {
                    $query->orWhere('ces_rew1_status', 1)
                        ->orWhere('ces_rew2_status', 1)
                        ->orWhere('ces_rew3_status', 1)
                        ->orWhere('ces_rew4_status', 1);
                })
                    ->where(function ($query) {
                        $query->where('ces_rew1_status', '!=', 2)
                            ->where('ces_rew2_status', '!=', 2)
                            ->where('ces_rew3_status', '!=', 2)
                            ->where('ces_rew4_status', '!=', 2);
                    })
                    ->where(function ($query) {
                        $query->where('derc_info', 1);
                    })
                    ->orderBy('id', 'asc');
                }elseif(empty($id0->work_rew1) && empty($id0->work_rew2) && empty($id0->work_rew3) && empty($id0->work_rew4)){
                    $nv_sm_data = Nvsericestatus::whereIn('nv_id', $nv_ids)->with(['service', 'material', 'user'])->where(function ($query) {
                        $query
                        ->where('hod_status', 1);
                    })
                    ->where(function ($query) {
                        $query ->where('hod_status', '!=', 2) ;
                    })
                    ->where(function ($query) {
                        $query->where('derc_info', 1);
                    })
                        ->orderBy('id', 'asc');
                }
           
                $total04_status = $request->total;
                $pending04_status = $request->pending;
                $reject04_status = $request->rejected;

                if ($ceo_status !== null) {
                    $nv_sm_data = $nv_sm_data->where('ceo_status', $ceo_status);
                } elseif ($pending04_status !== null) {
                    $nv_sm_data = $nv_sm_data->where('ces_status', $pending04_status)
                        
                        ->whereIn('hod_status', [ 1]);
                           
                } elseif ($reject04_status !== null) {
                    $nv_sm_data = $nv_sm_data->where('ces_status', $reject04_status);

                }elseif ($total04_status !== null) {
                    $nv_sm_data = $nv_sm_data->where('ces_status', $total04_status);
                    
                }
                $nv_sm_data = $nv_sm_data->get();

                if($fiscal_year){
                    $BRPLnv = NeedValidation::where('company_id','6')->where('fiscal_year', $fiscal_year)->pluck("id");
                }else{
                    $BRPLnv = NeedValidation::where('company_id','6')->where('fiscal_year', $currentFinancialYear)->pluck("id");
                }

                $BRPLnv = NeedValidation::where('company_id','6')->where('fiscal_year', $currentFinancialYear)->pluck("id");
                $pendingAmountBRPL = DB::table('tbl_material')
                ->join('nvservicestatus', 'tbl_material.id', '=', 'nvservicestatus.material_id')
                ->whereIn('tbl_material.nv_id', $BRPLnv)
                ->whereIn('nvservicestatus.nv_id', $pen_amt->pluck('nv_id'))
            
                ->sum('tbl_material.total_budget_both')+DB::table('tbl_service')
                ->join('nvservicestatus', 'tbl_service.id', '=', 'nvservicestatus.service_id')
                ->whereIn('tbl_service.nv_id', $BRPLnv)
                ->whereIn('nvservicestatus.nv_id', $pen_amt->pluck('nv_id'))
             
                ->sum('tbl_service.total_buget');
        
                $rejectedAmountBRPL = DB::table('tbl_material')
                ->join('nvservicestatus', 'tbl_material.id', '=', 'nvservicestatus.material_id')
                ->whereIn('tbl_material.nv_id', $BRPLnv)
                ->where(function ($query) {
                    $query->where('nvservicestatus.ces_status', 2);
                })
                ->sum('tbl_material.total_budget_both')+DB::table('tbl_service')
                ->join('nvservicestatus', 'tbl_service.id', '=', 'nvservicestatus.service_id')
                ->whereIn('tbl_service.nv_id', $BRPLnv)
                ->where(function ($query) {
                    $query->where('nvservicestatus.ces_status', 2);
                })
                ->sum('tbl_service.total_buget');

              $approvedAmountBRPL = DB::table('tbl_material')
                ->join('nvservicestatus', 'tbl_material.id', '=', 'nvservicestatus.material_id')
                ->whereIn('tbl_material.nv_id', $BRPLnv)->where('nvservicestatus.ceo_status', 1)
                ->sum('tbl_material.total_budget_both')+DB::table('tbl_service')
                ->join('nvservicestatus', 'tbl_service.id', '=', 'nvservicestatus.service_id')
                ->whereIn('tbl_service.nv_id', $BRPLnv)->where('nvservicestatus.ceo_status', 1)
                ->sum('tbl_service.total_buget');
               

                if(!empty($id0->work_rew1) || !empty($id0->work_rew2)|| !empty($id0->work_rew3)|| !empty($id0->work_rew4)){
                    $fileDataBRPL =Nvsericestatus::
                    select(
                        DB::raw('MONTH(created_at) as month'),
                        DB::raw('SUM(CASE WHEN ceo_status = "1" THEN 1 ELSE 0 END) as approved_count'),
                        DB::raw('SUM(CASE WHEN ( ces_status = "2")  THEN 1 ELSE 0 END ) as rejected_count'),
                        DB::raw('SUM(CASE WHEN (ces_status = "0" AND (ces_rew1_status = "1" OR ces_rew2_status = "1" OR ces_rew3_status = "1" OR ces_rew4_status = "1") ) THEN 1 ELSE 0 END) as pending_count'),
                    
                    )
                
                    ->whereIn('nv_id', $BRPLnv)->with(['service', 'material', 'user'])
                    ->where('derc_info',1)
                   ->where('hod_status',1)
                    ->whereYear('created_at', Carbon::now()->year)
                    ->groupBy('month')
                    ->orderBy('month')
                    ->get();
                   }elseif(empty($id0->work_rew1) && empty($id0->work_rew2) && empty($id0->work_rew3) && empty($id0->work_rew4)){
                    $fileDataBRPL =Nvsericestatus::
                select(
                    DB::raw('MONTH(created_at) as month'),
                    DB::raw('SUM(CASE WHEN ceo_status = "1" THEN 1 ELSE 0 END) as approved_count'),
                    DB::raw('SUM(CASE WHEN ( ces_status = "2")  THEN 1 ELSE 0 END ) as rejected_count'),
                    DB::raw('SUM(CASE WHEN (ces_status = "0"  ) THEN 1 ELSE 0 END) as pending_count'),
                
                )
            
                ->whereIn('nv_id', $BRPLnv)->with(['service', 'material', 'user'])
                ->where('derc_info',1)
               ->where('hod_status',1)
                ->whereYear('created_at', Carbon::now()->year)
                ->groupBy('month')
                ->orderBy('month')
                ->get();
                   }


            $BRPLlabels = [];
            $BRPLapprovedData = [];
            $BRPLrejectedData = [];
            $BRPLpendingData = [];

            foreach ($fileDataBRPL as $dataPointBRPL) {
                $monthBRPL = Carbon::createFromFormat('!m', $dataPointBRPL->month)->format('F');

                $BRPLlabels[] = $monthBRPL;
                $BRPLapprovedData[] = $dataPointBRPL->approved_count;
                $BRPLrejectedData[] = $dataPointBRPL->rejected_count;
                $BRPLpendingData[] = $dataPointBRPL->pending_count;

            }
            if($fiscal_year){
                $BYPLnv = NeedValidation::where('company_id','5')->where('fiscal_year', $fiscal_year)->pluck("id");
            }else{
                $BYPLnv = NeedValidation::where('company_id','5')->where('fiscal_year', $currentFinancialYear)->pluck("id");
              
            }
           
            $pendingAmountBYPL = DB::table('tbl_material')
            ->join('nvservicestatus', 'tbl_material.id', '=', 'nvservicestatus.material_id')
            ->whereIn('tbl_material.nv_id', $BYPLnv)
            ->whereIn('nvservicestatus.nv_id', $pen_amt->pluck('nv_id'))
        
            ->sum('tbl_material.total_budget_both')+DB::table('tbl_service')
            ->join('nvservicestatus', 'tbl_service.id', '=', 'nvservicestatus.service_id')
            ->whereIn('tbl_service.nv_id', $BYPLnv)
            ->whereIn('nvservicestatus.nv_id', $pen_amt->pluck('nv_id'))
          
            ->sum('tbl_service.total_buget');
    
            $rejectedAmountBYPL = DB::table('tbl_material')
            ->join('nvservicestatus', 'tbl_material.id', '=', 'nvservicestatus.material_id')
            ->whereIn('tbl_material.nv_id', $BYPLnv)
            ->where(function ($query) {
                $query->where('nvservicestatus.ces_status', 2);
            })
            ->sum('tbl_material.total_budget_both')+DB::table('tbl_service')
            ->join('nvservicestatus', 'tbl_service.id', '=', 'nvservicestatus.service_id')
            ->whereIn('tbl_service.nv_id', $BYPLnv)
            ->where(function ($query) {
                $query->where('nvservicestatus.ces_status', 2);
            })
            ->sum('tbl_service.total_buget');

          $approvedAmountBYPL = DB::table('tbl_material')
            ->join('nvservicestatus', 'tbl_material.id', '=', 'nvservicestatus.material_id')
            ->whereIn('tbl_material.nv_id', $BYPLnv)->where('nvservicestatus.ceo_status', 1)
            ->sum('tbl_material.total_budget_both')+DB::table('tbl_service')
            ->join('nvservicestatus', 'tbl_service.id', '=', 'nvservicestatus.service_id')
            ->whereIn('tbl_service.nv_id', $BYPLnv)->where('nvservicestatus.ceo_status', 1)
            ->sum('tbl_service.total_buget');
           

            if(!empty($id0->work_rew1) || !empty($id0->work_rew2)|| !empty($id0->work_rew3)|| !empty($id0->work_rew4)){
                $fileDataBYPL =Nvsericestatus::
            select(
                DB::raw('MONTH(created_at) as month'),
                DB::raw('SUM(CASE WHEN ceo_status = "1" THEN 1 ELSE 0 END) as approved_count'),
                DB::raw('SUM(CASE WHEN ( ces_status = "2")  THEN 1 ELSE 0 END) as rejected_count'),
                DB::raw('SUM(CASE WHEN (ces_status = "0" AND (ces_rew1_status = "1" OR ces_rew2_status = "1" OR ces_rew3_status = "1" OR ces_rew4_status = "1") ) THEN 1 ELSE 0 END) as pending_count'),
            
            ) 
            ->whereIn('nv_id', $BYPLnv)->with(['service', 'material', 'user'])
            ->where('derc_info',1)
            ->where('hod_status',1)
         
            ->whereYear('created_at', Carbon::now()->year)
            ->groupBy('month')
            ->orderBy('month')
            ->get();
               }elseif(empty($id0->work_rew1) && empty($id0->work_rew2) && empty($id0->work_rew3) && empty($id0->work_rew4)){
                $fileDataBYPL =Nvsericestatus::
            select(
                DB::raw('MONTH(created_at) as month'),
                DB::raw('SUM(CASE WHEN ceo_status = "1" THEN 1 ELSE 0 END) as approved_count'),
                DB::raw('SUM(CASE WHEN ( ces_status = "2")  THEN 1 ELSE 0 END) as rejected_count'),
                DB::raw('SUM(CASE WHEN (ces_status = "0"  ) THEN 1 ELSE 0 END) as pending_count'),
            
            ) 
            ->whereIn('nv_id', $BYPLnv)->with(['service', 'material', 'user'])
            ->where('derc_info',1)
            ->where('hod_status',1)
          
            ->whereYear('created_at', Carbon::now()->year)
            ->groupBy('month')
            ->orderBy('month')
            ->get();
               }


            $BYPLlabels = [];
            $BYPLapprovedData = [];
            $BYPLrejectedData = [];
            $BYPLpendingData = [];

            foreach ($fileDataBYPL as $dataPointBYPL) {
            $monthBYPL = Carbon::createFromFormat('!m', $dataPointBYPL->month)->format('F');

            $BYPLlabels[] = $monthBYPL;
            $BYPLapprovedData[] = $dataPointBYPL->approved_count;
            $BYPLrejectedData[] = $dataPointBYPL->rejected_count;
            $BYPLpendingData[] = $dataPointBYPL->pending_count;
      
            }
                return view("admin.dashboard", compact("approvedAmount","currentFinancialYear","nextFinancialYear","nextToNextFinancialYear", "rejectedAmount", "pendingAmount", "totalAmount","company","company_id","nv_sm_data", "totalNV", "approvedNV", "rejectedNV", "pendingNV" , "hodApproval", "cpmgApproval", "btApproval", "ceoApproval"             ,'BRPLlabels','BRPLapprovedData','BRPLrejectedData','BRPLpendingData','BYPLlabels','BYPLapprovedData','BYPLrejectedData','BYPLpendingData','cesApproval','ceonominee1Approval','ceonominee2Approval','approvedAmountBYPL','approvedAmountBRPL','rejectedAmountBYPL','rejectedAmountBRPL','pendingAmountBYPL','pendingAmountBRPL','groupcioApproval'));
            }
            elseif ($id1->work_rew1 == $user->id) {
               
                $Values = [$user->id, $hod, $id1  ];
               
                if($fiscal_year){
                    $totalId = NeedValidation::where('fiscal_year', $fiscal_year)->pluck('id');
                }else{
      
                    $totalId = NeedValidation::where('fiscal_year', $currentFinancialYear)->pluck('id');
                }

                $totalAmount = DB::table('tbl_material')
                ->join('nvservicestatus', 'tbl_material.id', '=', 'nvservicestatus.material_id')
               
                 ->whereIn('tbl_material.nv_id', $totalId)
                 ->whereIn('nvservicestatus.nv_id', $latestData->pluck('nv_id'))
                ->where('nvservicestatus.work_rew1_status', 1)
                ->sum('tbl_material.total_budget_both') + DB::table('tbl_service')
                ->join('nvservicestatus', 'tbl_service.id', '=', 'nvservicestatus.service_id')
               
                 ->whereIn('tbl_service.nv_id', $totalId)
                 ->whereIn('nvservicestatus.nv_id', $latestData->pluck('nv_id'))
                ->where('nvservicestatus.work_rew1_status', 1)
                ->sum('tbl_service.total_buget');
              
                $pen_amt = $latestData->filter(function ($data) {
                    return in_array($data->hod_status, [1]) &&
                        in_array($data->work_rew1_status, [0]) &&    
                        in_array($data->work_rew2_status, [0])&&
                        in_array($data->work_rew3_status, [0])&&
                        in_array($data->work_rew4_status, [0])&&
                        in_array($data->cpmg_status, [0])&&
                        (
                            (in_array($data->derc_info, [0]) && in_array($data->ces_status, [0]))
                            ||
                            (in_array($data->derc_info, [1]) && in_array($data->ces_status, [1]))
                        );
                });
                $pendingAmount = DB::table('tbl_material')
                ->join('nvservicestatus', 'tbl_material.id', '=', 'nvservicestatus.material_id')
                ->whereIn('tbl_material.nv_id', $totalId)
                ->whereIn('nvservicestatus.nv_id', $pen_amt->pluck('nv_id'))
              
               
                ->sum('tbl_material.total_budget_both') + 
                DB::table('tbl_service')
                ->join('nvservicestatus', 'tbl_service.id', '=', 'nvservicestatus.service_id')
                ->whereIn('tbl_service.nv_id', $totalId)
                ->whereIn('nvservicestatus.nv_id', $pen_amt->pluck('nv_id'))
              
               
                ->sum('tbl_service.total_buget');

              
        
                $rejectedAmount = DB::table('tbl_material')
                ->join('nvservicestatus', 'tbl_material.id', '=', 'nvservicestatus.material_id')
                ->whereIn('nvservicestatus.nv_id', $latestData->pluck('nv_id'))
                ->where(function ($query) {
                    $query->where('nvservicestatus.work_rew1_status', 2);
                })
                ->sum('tbl_material.total_budget_both') + DB::table('tbl_service')
                ->join('nvservicestatus', 'tbl_service.id', '=', 'nvservicestatus.service_id')
                ->whereIn('nvservicestatus.nv_id', $latestData->pluck('nv_id'))
                ->where(function ($query) {
                    $query->where('nvservicestatus.work_rew1_status', 2);
                })
                ->sum('tbl_service.total_buget');
           
        
                $approvedAmount = DB::table('tbl_material')
                
                 ->whereIn('tbl_material.nv_id', $totalId)
                 ->whereIn('nvservicestatus.nv_id', $latestData->pluck('nv_id'))
                ->join('nvservicestatus', 'tbl_material.id', '=', 'nvservicestatus.material_id')
                ->where('nvservicestatus.ceo_status', 1)
                ->sum('tbl_material.total_budget_both') + DB::table('tbl_service')
             
                ->whereIn('tbl_service.nv_id', $totalId)
                ->whereIn('nvservicestatus.nv_id', $latestData->pluck('nv_id'))
               ->join('nvservicestatus', 'tbl_service.id', '=', 'nvservicestatus.service_id')
               ->where('nvservicestatus.ceo_status', 1)
               ->sum('tbl_service.total_buget');
                if($company_id){
                    $totalNV = Nvsericestatus::whereIn("nv_id", $totalId)->where('work_rew1_status', 1)->where('company_id',$company_id)->count();
                } else {
                    $totalNV = Nvsericestatus::whereIn("nv_id", $totalId)->where('work_rew1_status', 1)->count();
                }
                if($company_id){
                    $rejectedNV = Nvsericestatus::whereIn("nv_id", $totalId)->where('work_rew1_status', 2)->where('company_id',$company_id)->count();
                } else {
                    $rejectedNV = Nvsericestatus::whereIn("nv_id", $totalId)->where('work_rew1_status', 2)->count();
                }
            
                if($company_id){
                $latestData = Nvsericestatus::whereIn("nv_id", $totalId)->where('company_id',$company_id)->get();
            }else {
                $latestData = Nvsericestatus::whereIn("nv_id", $totalId)->get();
            }
                if($company_id){
                    $approvedNV = $latestData->where('ceo_status', 1)->where('company_id',$company_id)->count();
                }else{
                    $approvedNV = $latestData->where('ceo_status', 1)->count();
                }
            
                $filteredPendingNV = $latestData->filter(function ($data) {
                    return in_array($data->hod_status, [1]) &&
                        in_array($data->work_rew1_status, [0]) &&    
                        in_array($data->work_rew2_status, [0])&&
                        in_array($data->work_rew3_status, [0])&&
                        in_array($data->work_rew4_status, [0])&&
                        in_array($data->cpmg_status, [0])&&
                        (
                            (in_array($data->derc_info, [0]) && in_array($data->ces_status, [0]))
                            ||
                            (in_array($data->derc_info, [1]) && in_array($data->ces_status, [1]))
                        );
                });
                
                $pendingNV = $filteredPendingNV->count();                
               
              
                $nvIds = NeedValidation::where("user_id", $user->id)->pluck("id");
               
                if($fiscal_year){
                    $nv = NeedValidation::whereHas('service')->select('id')->where('fiscal_year', $fiscal_year)
                
                    ->get();
                }else{
      
                    $nv = NeedValidation::whereHas('service')->select('id')->where('fiscal_year', $currentFinancialYear)
                
                    ->get();
                }
                $nv_ids = $nv->pluck('id');
             
            
                $nv_sm_data = Nvsericestatus::whereIn('nv_id', $nv_ids)
                ->with(['service', 'material', 'user'])
                ->where(function ($query) {
                    $query->where('ces_status', 1)
                        ->orWhere('hod_status', 1)
                        ->where('derc_info', 0);
                })
                ->orderBy('id', 'asc')
                ->get();

                $total8_status = $request->total;
                $pending8_status = $request->pending;
                $reject8_status = $request->rejected;

                if ($ceo_status !== null) {
                    $nv_sm_data = $nv_sm_data->where('ceo_status', $ceo_status);
                    
                } elseif ($pending8_status !== null) {
                  
                    $nv_sm_data = $nv_sm_data->where('work_rew1_status', $ceo_status)
                    ->where('work_rew2_status', $ceo_status)
                    ->where('work_rew3_status', $ceo_status)
                    ->where('work_rew4_status', $ceo_status);
                     
                       
                } elseif ($reject8_status !== null) {
                    $nv_sm_data =  $nv_sm_data->where('work_rew1_status', $reject8_status)
                        ;  
                }elseif ($total8_status !== null) {
                    $nv_sm_data =  $nv_sm_data->where('work_rew1_status', $total8_status);
                      
                }
        
                if($fiscal_year){
                    $BRPLnv = NeedValidation::where('company_id',6)->where('fiscal_year', $fiscal_year)->pluck("id");
                }else{
      
                    $BRPLnv = NeedValidation::where('company_id',6)->where('fiscal_year', $currentFinancialYear)->pluck("id");
                }
                
             
                $pendingAmountBRPL = DB::table('tbl_material')
                ->join('nvservicestatus', 'tbl_material.id', '=', 'nvservicestatus.material_id')
                ->whereIn('tbl_material.nv_id', $BRPLnv)
                ->whereIn('nvservicestatus.nv_id', $pen_amt->pluck('nv_id'))
              
                ->sum('tbl_material.total_budget_both')+DB::table('tbl_service')
                ->join('nvservicestatus', 'tbl_service.id', '=', 'nvservicestatus.service_id')
                ->whereIn('tbl_service.nv_id', $BRPLnv)
                ->whereIn('nvservicestatus.nv_id', $pen_amt->pluck('nv_id'))
              
                ->sum('tbl_service.total_buget');
        
                $rejectedAmountBRPL = DB::table('tbl_material')
                ->join('nvservicestatus', 'tbl_material.id', '=', 'nvservicestatus.material_id')
                ->whereIn('tbl_material.nv_id', $BRPLnv)
                ->where(function ($query) {
                    $query->where('nvservicestatus.work_rew1_status', 2);
                })
                ->sum('tbl_material.total_budget_both')+DB::table('tbl_service')
                ->join('nvservicestatus', 'tbl_service.id', '=', 'nvservicestatus.service_id')
                ->whereIn('tbl_service.nv_id', $BRPLnv)
                ->where(function ($query) {
                    $query->where('nvservicestatus.work_rew1_status', 2);
                })
                ->sum('tbl_service.total_buget');

              $approvedAmountBRPL = DB::table('tbl_material')
                ->join('nvservicestatus', 'tbl_material.id', '=', 'nvservicestatus.material_id')
                ->whereIn('tbl_material.nv_id', $BRPLnv)->where('nvservicestatus.ceo_status', 1)
                ->sum('tbl_material.total_budget_both')+DB::table('tbl_service')
                ->join('nvservicestatus', 'tbl_service.id', '=', 'nvservicestatus.service_id')
                ->whereIn('tbl_service.nv_id', $BRPLnv)->where('nvservicestatus.ceo_status', 1)
                ->sum('tbl_service.total_buget');
                $fileDataBRPL =Nvsericestatus::
                select(
                    DB::raw('MONTH(created_at) as month'),
                    DB::raw('SUM(CASE WHEN ceo_status = "1" THEN 1 ELSE 0 END) as approved_count'),
                    DB::raw('SUM(CASE WHEN ( work_rew1_status = "2")  THEN 1 ELSE 0 END ) as rejected_count'),
                    DB::raw('SUM(CASE WHEN (work_rew1_status = "0" AND work_rew2_status = "0" AND work_rew3_status = "0" AND work_rew4_status = "0" AND cpmg_status = "0") THEN 1 ELSE 0 END) as pending_count'),
                  
                )
             
                ->whereIn('nv_id', $BRPLnv)->with(['service', 'material', 'user'])
                ->where(function ($query) {
                    $query->where('ces_status', 1)
                        ->orWhere('hod_status', 1)
                        ->where('derc_info', 0);
                })
            ->whereYear('created_at', Carbon::now()->year)
            ->groupBy('month')
            ->orderBy('month')
            ->get();
       
            $BRPLlabels = [];
            $BRPLapprovedData = [];
            $BRPLrejectedData = [];
            $BRPLpendingData = [];
        
            foreach ($fileDataBRPL as $dataPointBRPL) {
                $monthBRPL = Carbon::createFromFormat('!m', $dataPointBRPL->month)->format('F');
        
                $BRPLlabels[] = $monthBRPL;
                $BRPLapprovedData[] = $dataPointBRPL->approved_count;
                $BRPLrejectedData[] = $dataPointBRPL->rejected_count;
                $BRPLpendingData[] = $dataPointBRPL->pending_count;
               
            }

            if($fiscal_year){
                $BYPLnv = NeedValidation::where('company_id',5)->where('fiscal_year', $fiscal_year)->pluck("id");
            }else{
                $BYPLnv = NeedValidation::where('company_id',5)->where('fiscal_year', $currentFinancialYear)->pluck("id");
              
            }
           
            $pendingAmountBYPL = DB::table('tbl_material')
            ->join('nvservicestatus', 'tbl_material.id', '=', 'nvservicestatus.material_id')
            ->whereIn('tbl_material.nv_id', $BYPLnv)
            ->whereIn('nvservicestatus.nv_id', $pen_amt->pluck('nv_id'))
       
            ->sum('tbl_material.total_budget_both')+DB::table('tbl_service')
            ->join('nvservicestatus', 'tbl_service.id', '=', 'nvservicestatus.service_id')
            ->whereIn('tbl_service.nv_id', $BYPLnv)
            ->whereIn('nvservicestatus.nv_id', $pen_amt->pluck('nv_id'))
         
            ->sum('tbl_service.total_buget');

            $rejectedAmountBYPL = DB::table('tbl_material')
            ->join('nvservicestatus', 'tbl_material.id', '=', 'nvservicestatus.material_id')
            ->whereIn('tbl_material.nv_id', $BYPLnv)
            ->where(function ($query) {
                $query->where('nvservicestatus.work_rew1_status', 2);
            })
            ->sum('tbl_material.total_budget_both')+DB::table('tbl_service')
            ->join('nvservicestatus', 'tbl_service.id', '=', 'nvservicestatus.service_id')
            ->whereIn('tbl_service.nv_id', $BYPLnv)
            ->where(function ($query) {
                $query->where('nvservicestatus.work_rew1_status', 2);
            })
            ->sum('tbl_service.total_buget');

          $approvedAmountBYPL = DB::table('tbl_material')
            ->join('nvservicestatus', 'tbl_material.id', '=', 'nvservicestatus.material_id')
            ->whereIn('tbl_material.nv_id', $BYPLnv)->where('nvservicestatus.ceo_status', 1)
            ->sum('tbl_material.total_budget_both')+DB::table('tbl_service')
            ->join('nvservicestatus', 'tbl_service.id', '=', 'nvservicestatus.service_id')
            ->whereIn('tbl_service.nv_id', $BYPLnv)->where('nvservicestatus.ceo_status', 1)
            ->sum('tbl_service.total_buget');
             $fileDataBYPL =Nvsericestatus::
            select(
                DB::raw('MONTH(created_at) as month'),
                DB::raw('SUM(CASE WHEN ceo_status = "1" THEN 1 ELSE 0 END) as approved_count'),
                DB::raw('SUM(CASE WHEN ( work_rew1_status = "2")  THEN 1 ELSE 0 END) as rejected_count'),
                DB::raw('SUM(CASE WHEN (work_rew1_status = "0" AND work_rew2_status = "0" AND work_rew3_status = "0" AND work_rew4_status = "0" AND cpmg_status = "0" ) THEN 1 ELSE 0 END) as pending_count'),
            
            ) 
            ->whereIn('nv_id', $BYPLnv)->with(['service', 'material', 'user'])
            ->where(function ($query) {
                $query->where('ces_status', 1)
                    ->orWhere('hod_status', 1)
                    ->where('derc_info', 0);
            })
            ->whereYear('created_at', Carbon::now()->year)
            ->groupBy('month')
            ->orderBy('month')
            ->get();
          
        $BYPLlabels = [];
        $BYPLapprovedData = [];
        $BYPLrejectedData = [];
        $BYPLpendingData = [];
    
        foreach ($fileDataBYPL as $dataPointBYPL) {
            $monthBYPL = Carbon::createFromFormat('!m', $dataPointBYPL->month)->format('F');
    
            $BYPLlabels[] = $monthBYPL;
            $BYPLapprovedData[] = $dataPointBYPL->approved_count;
            $BYPLrejectedData[] = $dataPointBYPL->rejected_count;
            $BYPLpendingData[] = $dataPointBYPL->pending_count;
            
         }
         
           return view("admin.dashboard", compact("approvedAmount","currentFinancialYear","nextFinancialYear","nextToNextFinancialYear", "rejectedAmount", "pendingAmount", "totalAmount","company", "company_id" , "nv_sm_data", "totalNV", "approvedNV", "rejectedNV", "pendingNV" , "hodApproval", "cpmgApproval", "btApproval", "ceoApproval"             ,'BRPLlabels','BRPLapprovedData','BRPLrejectedData','BRPLpendingData','BYPLlabels','BYPLapprovedData','BYPLrejectedData','BYPLpendingData','cesApproval','ceonominee1Approval','ceonominee2Approval','approvedAmountBYPL','approvedAmountBRPL','rejectedAmountBYPL','rejectedAmountBRPL','pendingAmountBYPL','pendingAmountBRPL','groupcioApproval'));
            } elseif ($id1->work_rew2 == $user->id) {
                $Values = [$user->id, $id1->work_rew1];
              
                if($fiscal_year){
                    $totalId = NeedValidation::where('fiscal_year', $fiscal_year)->pluck('id');
                }else{
                  $totalId = NeedValidation::where('fiscal_year', $currentFinancialYear)->pluck('id');
                  
                }
             
                $totalAmount = DB::table('tbl_material')
                ->join('nvservicestatus', 'tbl_material.id', '=', 'nvservicestatus.material_id')
             
                 ->whereIn('tbl_material.nv_id', $totalId)
                 ->whereIn('nvservicestatus.nv_id', $latestData->pluck('nv_id'))
                ->where('nvservicestatus.work_rew2_status', 1)
                ->sum('tbl_material.total_budget_both') + DB::table('tbl_service')
                ->join('nvservicestatus', 'tbl_service.id', '=', 'nvservicestatus.service_id')
             
                 ->whereIn('tbl_service.nv_id', $totalId)
                 ->whereIn('nvservicestatus.nv_id', $latestData->pluck('nv_id'))
                ->where('nvservicestatus.work_rew2_status', 1)
                ->sum('tbl_service.total_buget');
              
                $pen_amt = $latestData->filter(function ($data) {
                    return in_array($data->hod_status, [1]) &&
                        in_array($data->work_rew1_status, [0]) &&    
                        in_array($data->work_rew2_status, [0])&&
                        in_array($data->work_rew3_status, [0])&&
                        in_array($data->work_rew4_status, [0])&&
                        in_array($data->cpmg_status, [0])&&
                        (
                            (in_array($data->derc_info, [0]) && in_array($data->ces_status, [0]))
                            ||
                            (in_array($data->derc_info, [1]) && in_array($data->ces_status, [1]))
                        );
                });
                $pendingAmount = DB::table('tbl_material')
                ->join('nvservicestatus', 'tbl_material.id', '=', 'nvservicestatus.material_id')
               
                 ->whereIn('tbl_material.nv_id', $totalId)
                 ->whereIn('nvservicestatus.nv_id', $pen_amt->pluck('nv_id'))
                  
                ->sum('tbl_material.total_budget_both') +  DB::table('tbl_service')
                ->join('nvservicestatus', 'tbl_service.id', '=', 'nvservicestatus.service_id')
               
                 ->whereIn('tbl_service.nv_id', $totalId)
                 ->whereIn('nvservicestatus.nv_id', $pen_amt->pluck('nv_id'))
                  
                ->sum('tbl_service.total_buget');
        
                $rejectedAmount = DB::table('tbl_material')
                ->join('nvservicestatus', 'tbl_material.id', '=', 'nvservicestatus.material_id')
                ->whereIn('nvservicestatus.nv_id', $latestData->pluck('nv_id'))
                ->where(function ($query) {
                    $query->where('nvservicestatus.work_rew2_status', 2);
                })
                ->sum('tbl_material.total_budget_both') + DB::table('tbl_service')
                ->join('nvservicestatus', 'tbl_service.id', '=', 'nvservicestatus.service_id')
                ->whereIn('nvservicestatus.nv_id', $latestData->pluck('nv_id'))
                ->where(function ($query) {
                    $query->where('nvservicestatus.work_rew2_status', 2);
                })
                ->sum('tbl_service.total_buget');
           
        
                $approvedAmount = DB::table('tbl_material')
                
                 ->whereIn('tbl_material.nv_id', $totalId)
                 ->whereIn('nvservicestatus.nv_id', $latestData->pluck('nv_id'))
                ->join('nvservicestatus', 'tbl_material.id', '=', 'nvservicestatus.material_id')
                ->where('nvservicestatus.ceo_status', 1)
                ->sum('tbl_material.total_budget_both') + DB::table('tbl_service')
               
                ->whereIn('tbl_service.nv_id', $totalId)
                ->whereIn('nvservicestatus.nv_id', $latestData->pluck('nv_id'))
               ->join('nvservicestatus', 'tbl_service.id', '=', 'nvservicestatus.service_id')
               ->where('nvservicestatus.ceo_status', 1)
               ->sum('tbl_service.total_buget');

                if($company_id){
                    $totalNV = Nvsericestatus::whereIn("nv_id", $totalId)->where('work_rew2_status', 1)->where('company_id',$company_id)->count();
                } else {
                    $totalNV = Nvsericestatus::whereIn("nv_id", $totalId)->where('work_rew2_status', 1)->count();
                }
                if($company_id){
                    $rejectedNV = Nvsericestatus::whereIn("nv_id", $totalId)->where('work_rew2_status', 2)->where('company_id',$company_id)->count();
                } else {
                    $rejectedNV = Nvsericestatus::whereIn("nv_id", $totalId)->where('work_rew2_status', 2)->count();
                }
            
                if($company_id){
                $latestData = Nvsericestatus::whereIn("nv_id", $totalId)->where('company_id',$company_id)->get();
            }else {
                $latestData = Nvsericestatus::whereIn("nv_id", $totalId)->get();
            }
                if($company_id){
                    $approvedNV = $latestData->where('ceo_status', 1)->where('company_id',$company_id)->count();
                }else{
                    $approvedNV = $latestData->where('ceo_status', 1)->count();
                }
              
                $pendingNV = $latestData->filter(function ($data) {
                    return in_array($data->hod_status, [1]) &&
                        in_array($data->work_rew1_status, [0]) &&    
                        in_array($data->work_rew2_status, [0])&&
                        in_array($data->work_rew3_status, [0])&&
                        in_array($data->work_rew4_status, [0])&&
                        in_array($data->cpmg_status, [0])&&
                        (
                            (in_array($data->derc_info, [0]) && in_array($data->ces_status, [0]))
                            ||
                            (in_array($data->derc_info, [1]) && in_array($data->ces_status, [1]))
                        );
                })->count();
                
               
                $nvIds = NeedValidation::where("user_id", $Values)->pluck("id");
               
                if($fiscal_year){
                    $nv = NeedValidation::whereHas('service')->select('id')->where('fiscal_year', $fiscal_year)
               
                    ->get();
                }else{
                    $nv = NeedValidation::whereHas('service')->select('id')->where('fiscal_year', $currentFinancialYear)
               
                    ->get();
                  
                }
                $nv_ids = $nv->pluck('id');
                    $nv_sm_data = Nvsericestatus::whereIn('nv_id', $nv_ids)->with(['service', 'material', 'user'])
                    ->where(function ($query) {
                        $query->where('ces_status', 1)
                            ->orWhere('hod_status', 1)
                            ->where('derc_info', 0);
                    })
                ->orderBy('id', 'asc')->get();

                $total9_status = $request->total;
                $pending9_status = $request->pending;
                $reject9_status = $request->rejected;
              if ($ceo_status !== null) {
                    $nv_sm_data = $nv_sm_data->where('ceo_status', $ceo_status);
                } elseif ($pending9_status !== null) {
                    $nv_sm_data = $nv_sm_data->where('work_rew2_status', $pending9_status)
                    ->whereIn('work_rew1_status', [ 0])
                    ->whereIn('work_rew3_status', [ 0])
                    ->whereIn('work_rew4_status', [ 0]);

                } elseif ($reject9_status !== null) {
                    $nv_sm_data = $nv_sm_data->where('work_rew2_status', $reject9_status)  ;

                }elseif ($total9_status !== null) {
                    $nv_sm_data = $nv_sm_data->where('work_rew2_status', $total9_status);
                      
                }
               
                if($fiscal_year){
                    $BRPLnv = NeedValidation::where('company_id','6')->where('fiscal_year', $fiscal_year)->pluck("id");
                }else{
                    $BRPLnv = NeedValidation::where('company_id','6')->where('fiscal_year', $currentFinancialYear)->pluck("id");
                  
                }
                
                $pendingAmountBRPL = DB::table('tbl_material')
                ->join('nvservicestatus', 'tbl_material.id', '=', 'nvservicestatus.material_id')
                ->whereIn('tbl_material.nv_id', $BRPLnv)
                ->whereIn('nvservicestatus.nv_id', $pen_amt->pluck('nv_id'))
                ->sum('tbl_material.total_budget_both')+DB::table('tbl_service')
                ->join('nvservicestatus', 'tbl_service.id', '=', 'nvservicestatus.service_id')
                ->whereIn('tbl_service.nv_id', $BRPLnv)
                ->whereIn('nvservicestatus.nv_id', $pen_amt->pluck('nv_id'))
                ->sum('tbl_service.total_buget');
        
                $rejectedAmountBRPL = DB::table('tbl_material')
                ->join('nvservicestatus', 'tbl_material.id', '=', 'nvservicestatus.material_id')
                ->whereIn('tbl_material.nv_id', $BRPLnv)
                ->where(function ($query) {
                    $query->where('nvservicestatus.work_rew2_status', 2);
                })
                ->sum('tbl_material.total_budget_both')+DB::table('tbl_service')
                ->join('nvservicestatus', 'tbl_service.id', '=', 'nvservicestatus.service_id')
                ->whereIn('tbl_service.nv_id', $BRPLnv)
                ->where(function ($query) {
                    $query->where('nvservicestatus.work_rew2_status', 2);
                })
                ->sum('tbl_service.total_buget');

              $approvedAmountBRPL = DB::table('tbl_material')
                ->join('nvservicestatus', 'tbl_material.id', '=', 'nvservicestatus.material_id')
                ->whereIn('tbl_material.nv_id', $BRPLnv)->where('nvservicestatus.ceo_status', 1)
                ->sum('tbl_material.total_budget_both')+DB::table('tbl_service')
                ->join('nvservicestatus', 'tbl_service.id', '=', 'nvservicestatus.service_id')
                ->whereIn('tbl_service.nv_id', $BRPLnv)->where('nvservicestatus.ceo_status', 1)
                ->sum('tbl_service.total_buget');
                $fileDataBRPL =Nvsericestatus::
                select(
                    DB::raw('MONTH(created_at) as month'),
                    DB::raw('SUM(CASE WHEN ceo_status = "1" THEN 1 ELSE 0 END) as approved_count'),
                    DB::raw('SUM(CASE WHEN ( work_rew2_status = "2")  THEN 1 ELSE 0 END ) as rejected_count'),
                    DB::raw('SUM(CASE WHEN (work_rew1_status = "0" AND work_rew2_status = "0" AND work_rew3_status = "0" AND work_rew4_status = "0" AND cpmg_status = "0") THEN 1 ELSE 0 END) as pending_count'),
                 
                )
             
                ->whereIn('nv_id', $BRPLnv)->with(['service', 'material', 'user'])
                ->where(function ($query) {
                    $query->where('ces_status', 1)
                        ->orWhere('hod_status', 1)
                        ->where('derc_info', 0);
                })
             
                ->whereYear('created_at', Carbon::now()->year)
                ->groupBy('month')
                ->orderBy('month')
                ->get();
     
            $BRPLlabels = [];
            $BRPLapprovedData = [];
            $BRPLrejectedData = [];
            $BRPLpendingData = [];
        
            foreach ($fileDataBRPL as $dataPointBRPL) {
                $monthBRPL = Carbon::createFromFormat('!m', $dataPointBRPL->month)->format('F');
        
                $BRPLlabels[] = $monthBRPL;
                $BRPLapprovedData[] = $dataPointBRPL->approved_count;
                $BRPLrejectedData[] = $dataPointBRPL->rejected_count;
                $BRPLpendingData[] = $dataPointBRPL->pending_count;
                
            }

            if($fiscal_year){
                $BYPLnv = NeedValidation::where('company_id','5')->where('fiscal_year', $fiscal_year)->pluck("id");
            }else{
                $BYPLnv = NeedValidation::where('company_id','5')->where('fiscal_year', $currentFinancialYear)->pluck("id");
              
            }
            
            $pendingAmountBYPL = DB::table('tbl_material')
            ->join('nvservicestatus', 'tbl_material.id', '=', 'nvservicestatus.material_id')
            ->whereIn('tbl_material.nv_id', $BYPLnv)
            ->whereIn('nvservicestatus.nv_id', $pen_amt->pluck('nv_id'))
          
            ->sum('tbl_material.total_budget_both')+DB::table('tbl_service')
            ->join('nvservicestatus', 'tbl_service.id', '=', 'nvservicestatus.service_id')
            ->whereIn('tbl_service.nv_id', $BYPLnv)
            ->whereIn('nvservicestatus.nv_id', $pen_amt->pluck('nv_id'))
          
            ->sum('tbl_service.total_buget');
    
            $rejectedAmountBYPL = DB::table('tbl_material')
            ->join('nvservicestatus', 'tbl_material.id', '=', 'nvservicestatus.material_id')
            ->whereIn('tbl_material.nv_id', $BYPLnv)
            ->where(function ($query) {
                $query->where('nvservicestatus.work_rew2_status', 2);
            })
            ->sum('tbl_material.total_budget_both')+DB::table('tbl_service')
            ->join('nvservicestatus', 'tbl_service.id', '=', 'nvservicestatus.service_id')
            ->whereIn('tbl_service.nv_id', $BYPLnv)
            ->where(function ($query) {
                $query->where('nvservicestatus.work_rew2_status', 2);
            })
            ->sum('tbl_service.total_buget');

          $approvedAmountBYPL = DB::table('tbl_material')
            ->join('nvservicestatus', 'tbl_material.id', '=', 'nvservicestatus.material_id')
            ->whereIn('tbl_material.nv_id', $BYPLnv)->where('nvservicestatus.ceo_status', 1)
            ->sum('tbl_material.total_budget_both')+DB::table('tbl_service')
            ->join('nvservicestatus', 'tbl_service.id', '=', 'nvservicestatus.service_id')
            ->whereIn('tbl_service.nv_id', $BYPLnv)->where('nvservicestatus.ceo_status', 1)
            ->sum('tbl_service.total_buget');
             $fileDataBYPL =Nvsericestatus::
            select(
                DB::raw('MONTH(created_at) as month'),
                DB::raw('SUM(CASE WHEN ceo_status = "1" THEN 1 ELSE 0 END) as approved_count'),
                DB::raw('SUM(CASE WHEN ( work_rew2_status = "2")  THEN 1 ELSE 0 END) as rejected_count'),
                DB::raw('SUM(CASE WHEN (work_rew1_status = "0" AND work_rew2_status = "0" AND work_rew3_status = "0" AND work_rew4_status = "0" AND cpmg_status = "0") THEN 1 ELSE 0 END) as pending_count'),
               
            ) 
            ->whereIn('nv_id', $BYPLnv)->with(['service', 'material', 'user'])
            ->where(function ($query) {
                $query->where('ces_status', 1)
                    ->orWhere('hod_status', 1)
                    ->where('derc_info', 0);
            })
           
            ->whereYear('created_at', Carbon::now()->year)
            ->groupBy('month')
            ->orderBy('month')
            ->get();
     
        $BYPLlabels = [];
        $BYPLapprovedData = [];
        $BYPLrejectedData = [];
        $BYPLpendingData = [];
    
        foreach ($fileDataBYPL as $dataPointBYPL) {
            $monthBYPL = Carbon::createFromFormat('!m', $dataPointBYPL->month)->format('F');
    
            $BYPLlabels[] = $monthBYPL;
            $BYPLapprovedData[] = $dataPointBYPL->approved_count;
            $BYPLrejectedData[] = $dataPointBYPL->rejected_count;
            $BYPLpendingData[] = $dataPointBYPL->pending_count;
           
         }

                return view("admin.dashboard", compact("approvedAmount","currentFinancialYear","nextFinancialYear","nextToNextFinancialYear", "rejectedAmount", "pendingAmount", "totalAmount","company", "company_id" , "nv_sm_data", "totalNV", "approvedNV", "rejectedNV", "pendingNV" , "hodApproval", "cpmgApproval", "btApproval",  "dpnv"           ,'BRPLlabels','BRPLapprovedData','BRPLrejectedData','BRPLpendingData','BYPLlabels','BYPLapprovedData','BYPLrejectedData','BYPLpendingData','cesApproval','ceonominee1Approval','ceonominee2Approval','approvedAmountBYPL','approvedAmountBRPL','rejectedAmountBRPL','rejectedAmountBYPL','pendingAmountBYPL','pendingAmountBRPL','ceoApproval','groupcioApproval'));
            } elseif ($id1->work_rew3 == $user->id) {
                $Values = [$user->id, $id1->work_rew1, $id1->work_rew2];
              
                  if($fiscal_year){
                    $totalId = NeedValidation::where('fiscal_year', $fiscal_year)->pluck('id');
                    }else{

                        $totalId = NeedValidation::where('fiscal_year', $currentFinancialYear)->pluck('id');                        
                    }
                
                $totalAmount = DB::table('tbl_material')
                ->join('nvservicestatus', 'tbl_material.id', '=', 'nvservicestatus.material_id')
             
                 ->whereIn('tbl_material.nv_id', $totalId)
                 ->whereIn('nvservicestatus.nv_id', $latestData->pluck('nv_id'))
                ->where('nvservicestatus.work_rew3_status', 1)
                ->sum('tbl_material.total_budget_both') +  DB::table('tbl_service')
                ->join('nvservicestatus', 'tbl_service.id', '=', 'nvservicestatus.service_id')
             
                 ->whereIn('tbl_service.nv_id', $totalId)
                 ->whereIn('nvservicestatus.nv_id', $latestData->pluck('nv_id'))
                ->where('nvservicestatus.work_rew3_status', 1)
                ->sum('tbl_service.total_buget');
              
                $pen_amt = $latestData->filter(function ($data) {
                    return in_array($data->hod_status, [1]) &&
                        in_array($data->work_rew1_status, [0]) &&    
                        in_array($data->work_rew2_status, [0])&&
                        in_array($data->work_rew3_status, [0])&&
                        in_array($data->work_rew4_status, [0])&&
                        in_array($data->cpmg_status, [0])&&
                        (
                            (in_array($data->derc_info, [0]) && in_array($data->ces_status, [0]))
                            ||
                            (in_array($data->derc_info, [1]) && in_array($data->ces_status, [1]))
                        );
                });
                $pendingAmount = DB::table('tbl_material')
                ->join('nvservicestatus', 'tbl_material.id', '=', 'nvservicestatus.material_id')
                
                 ->whereIn('tbl_material.nv_id', $totalId)
                 ->whereIn('nvservicestatus.nv_id', $pen_amt->pluck('nv_id'))
                  
                ->sum('tbl_material.total_budget_both') +  DB::table('tbl_service')
                ->join('nvservicestatus', 'tbl_service.id', '=', 'nvservicestatus.service_id')
                
                 ->whereIn('tbl_service.nv_id', $totalId)
                 ->whereIn('nvservicestatus.nv_id', $pen_amt->pluck('nv_id'))
                  
                ->sum('tbl_service.total_buget');
        
                $rejectedAmount = DB::table('tbl_material')
                ->join('nvservicestatus', 'tbl_material.id', '=', 'nvservicestatus.material_id')
                ->whereIn('nvservicestatus.nv_id', $latestData->pluck('nv_id'))
                ->where(function ($query) {
                    $query->where('nvservicestatus.work_rew3_status', 2);
                })
                ->sum('tbl_material.total_budget_both') + DB::table('tbl_service')
                ->join('nvservicestatus', 'tbl_service.id', '=', 'nvservicestatus.service_id')
                ->whereIn('nvservicestatus.nv_id', $latestData->pluck('nv_id'))
                ->where(function ($query) {
                    $query->where('nvservicestatus.work_rew3_status', 2);
                })
                ->sum('tbl_service.total_buget');
           
        
                $approvedAmount = DB::table('tbl_material')
                
                 ->whereIn('tbl_material.nv_id', $totalId)
                 ->whereIn('nvservicestatus.nv_id', $latestData->pluck('nv_id'))
                ->join('nvservicestatus', 'tbl_material.id', '=', 'nvservicestatus.material_id')
                ->where('nvservicestatus.ceo_status', 1)
                ->sum('tbl_material.total_budget_both') + DB::table('tbl_service')
               
                ->whereIn('tbl_service.nv_id', $totalId)
                ->whereIn('nvservicestatus.nv_id', $latestData->pluck('nv_id'))
               ->join('nvservicestatus', 'tbl_service.id', '=', 'nvservicestatus.service_id')
               ->where('nvservicestatus.ceo_status', 1)
               ->sum('tbl_service.total_buget');

                if($company_id){
                    $totalNV = Nvsericestatus::whereIn("nv_id", $totalId)->where('work_rew3_status', 1)->where('company_id',$company_id)->count();
                } else {
                    $totalNV = Nvsericestatus::whereIn("nv_id", $totalId)->where('work_rew3_status', 1)->count();
                }
                if($company_id){
                    $rejectedNV = Nvsericestatus::whereIn("nv_id", $totalId)->where('work_rew3_status', 2)->where('company_id',$company_id)->count();
                } else {
                    $rejectedNV = Nvsericestatus::whereIn("nv_id", $totalId)->where('work_rew3_status', 2)->count();
                }
              
                if($company_id){
                $latestData = Nvsericestatus::whereIn("nv_id", $totalId)->where('company_id',$company_id)->get();
            }else {
                $latestData = Nvsericestatus::whereIn("nv_id", $totalId)->get();
            }
                if($company_id){
                    $approvedNV = $latestData->where('ceo_status', 1)->where('company_id',$company_id)->count();
                }else{
                    $approvedNV = $latestData->where('ceo_status', 1)->count();
                }
            
                $pendingNV = $latestData->filter(function ($data) {
                    return in_array($data->hod_status, [1]) &&
                        in_array($data->work_rew1_status, [0]) &&    
                        in_array($data->work_rew2_status, [0])&&
                        in_array($data->work_rew3_status, [0])&&
                        in_array($data->work_rew4_status, [0])&&
                        in_array($data->cpmg_status, [0])&&
                        (
                            (in_array($data->derc_info, [0]) && in_array($data->ces_status, [0]))
                            ||
                            (in_array($data->derc_info, [1]) && in_array($data->ces_status, [1]))
                        );
                })->count();
          
              
                $nvIds = NeedValidation::where("user_id", $Values)->pluck("id");
               
                if($fiscal_year){
                    $nv = NeedValidation::whereHas('service')->select('id')->where('fiscal_year', $fiscal_year)
              
                    ->get();
                }else{
                    $nv = NeedValidation::whereHas('service')->select('id')->where('fiscal_year', $currentFinancialYear)
              
                    ->get();
                  
                }
                $nv_ids = $nv->pluck('id');
        
                    $nv_sm_data = Nvsericestatus::whereIn('nv_id', $nv_ids)->with(['service', 'material', 'user'])
                    ->where(function ($query) {
                        $query->where('ces_status', 1)
                            ->orWhere('hod_status', 1)
                            ->where('derc_info', 0);
                    })

                ->orderBy('id', 'asc')->get();
                $total10_status = $request->total;
                $pending10_status = $request->pending;
                $reject10_status = $request->rejected;

                if ($ceo_status !== null) {
                    $nv_sm_data = $nv_sm_data->where('ceo_status', $ceo_status);
                } elseif ($pending10_status !== null) {
                    $nv_sm_data = $nv_sm_data->where('work_rew3_status', $pending10_status)
                    ->whereIn('work_rew1_status', [ 0])
                    ->whereIn('work_rew2_status', [ 0])
                        ->whereIn('work_rew4_status', [ 0]);

                } elseif ($reject10_status !== null) {
                    $nv_sm_data = $nv_sm_data->where('work_rew3_status', $reject10_status) ;

                }elseif ($total10_status !== null) {
                    $nv_sm_data = $nv_sm_data->where('work_rew3_status', $total10_status);
                      
                }
            
                if($fiscal_year){
                    $BRPLnv = NeedValidation::where('company_id','6')->where('fiscal_year', $fiscal_year)->pluck("id");
                }else{
                    $BRPLnv = NeedValidation::where('company_id','6')->where('fiscal_year', $currentFinancialYear)->pluck("id");
                  
                }
               
               
                $pendingAmountBRPL = DB::table('tbl_material')
                ->join('nvservicestatus', 'tbl_material.id', '=', 'nvservicestatus.material_id')
                ->whereIn('tbl_material.nv_id', $BRPLnv)
                ->whereIn('nvservicestatus.nv_id', $pen_amt->pluck('nv_id'))
                ->sum('tbl_material.total_budget_both')+DB::table('tbl_service')
                ->join('nvservicestatus', 'tbl_service.id', '=', 'nvservicestatus.service_id')
                ->whereIn('tbl_service.nv_id', $BRPLnv)
                ->whereIn('nvservicestatus.nv_id', $pen_amt->pluck('nv_id'))
                ->sum('tbl_service.total_buget');
        
                $rejectedAmountBRPL = DB::table('tbl_material')
                ->join('nvservicestatus', 'tbl_material.id', '=', 'nvservicestatus.material_id')
                ->whereIn('tbl_material.nv_id', $BRPLnv)
                ->where(function ($query) {
                    $query->where('nvservicestatus.work_rew3_status', 2);
                })
                ->sum('tbl_material.total_budget_both')+DB::table('tbl_service')
                ->join('nvservicestatus', 'tbl_service.id', '=', 'nvservicestatus.service_id')
                ->whereIn('tbl_service.nv_id', $BRPLnv)
                ->where(function ($query) {
                    $query->where('nvservicestatus.work_rew3_status', 2);
                })
                ->sum('tbl_service.total_buget');

              $approvedAmountBRPL = DB::table('tbl_material')
                ->join('nvservicestatus', 'tbl_material.id', '=', 'nvservicestatus.material_id')
                ->whereIn('tbl_material.nv_id', $BRPLnv)->where('nvservicestatus.ceo_status', 1)
                ->sum('tbl_material.total_budget_both')+DB::table('tbl_service')
                ->join('nvservicestatus', 'tbl_service.id', '=', 'nvservicestatus.service_id')
                ->whereIn('tbl_service.nv_id', $BRPLnv)->where('nvservicestatus.ceo_status', 1)
                ->sum('tbl_service.total_buget');
                $fileDataBRPL =Nvsericestatus::
                select(
                    DB::raw('MONTH(created_at) as month'),
                    DB::raw('SUM(CASE WHEN ceo_status = "1" THEN 1 ELSE 0 END) as approved_count'),
                    DB::raw('SUM(CASE WHEN ( work_rew3_status = "2")  THEN 1 ELSE 0 END ) as rejected_count'),
                    DB::raw('SUM(CASE WHEN (work_rew1_status = "0" AND work_rew2_status = "0" AND work_rew3_status = "0" AND work_rew4_status = "0" AND cpmg_status = "0") THEN 1 ELSE 0 END) as pending_count'),
                 
                )
             
                ->whereIn('nv_id', $BRPLnv)->with(['service', 'material', 'user'])
                ->where(function ($query) {
                    $query->where('ces_status', 1)
                        ->orWhere('hod_status', 1)
                        ->where('derc_info', 0);
                })
            
                ->whereYear('created_at', Carbon::now()->year)
                ->groupBy('month')
                ->orderBy('month')
                ->get();
       
            $BRPLlabels = [];
            $BRPLapprovedData = [];
            $BRPLrejectedData = [];
            $BRPLpendingData = [];
        
            foreach ($fileDataBRPL as $dataPointBRPL) {
                $monthBRPL = Carbon::createFromFormat('!m', $dataPointBRPL->month)->format('F');
        
                $BRPLlabels[] = $monthBRPL;
                $BRPLapprovedData[] = $dataPointBRPL->approved_count;
                $BRPLrejectedData[] = $dataPointBRPL->rejected_count;
                $BRPLpendingData[] = $dataPointBRPL->pending_count;
                
            }
            if($fiscal_year){
                $BYPLnv = NeedValidation::where('company_id','5')->where('fiscal_year', $fiscal_year)->pluck("id");
            }else{
                $BYPLnv = NeedValidation::where('company_id','5')->where('fiscal_year', $currentFinancialYear)->pluck("id");
              
            }
            
            $pendingAmountBYPL = DB::table('tbl_material')
            ->join('nvservicestatus', 'tbl_material.id', '=', 'nvservicestatus.material_id')
            ->whereIn('tbl_material.nv_id', $BYPLnv)
            ->whereIn('nvservicestatus.nv_id', $pen_amt->pluck('nv_id'))
           
            ->sum('tbl_material.total_budget_both')+DB::table('tbl_service')
            ->join('nvservicestatus', 'tbl_service.id', '=', 'nvservicestatus.service_id')
            ->whereIn('tbl_service.nv_id', $BYPLnv)
            ->whereIn('nvservicestatus.nv_id', $pen_amt->pluck('nv_id'))
           
            ->sum('tbl_service.total_buget');
    
            $rejectedAmountBYPL = DB::table('tbl_material')
            ->join('nvservicestatus', 'tbl_material.id', '=', 'nvservicestatus.material_id')
            ->whereIn('tbl_material.nv_id', $BYPLnv)
            ->where(function ($query) {
                $query->where('nvservicestatus.work_rew3_status', 2);
            })
            ->sum('tbl_material.total_budget_both')+DB::table('tbl_service')
            ->join('nvservicestatus', 'tbl_service.id', '=', 'nvservicestatus.service_id')
            ->whereIn('tbl_service.nv_id', $BYPLnv)
            ->where(function ($query) {
                $query->where('nvservicestatus.work_rew3_status', 2);
            })
            ->sum('tbl_service.total_buget');

          $approvedAmountBYPL = DB::table('tbl_material')
            ->join('nvservicestatus', 'tbl_material.id', '=', 'nvservicestatus.material_id')
            ->whereIn('tbl_material.nv_id', $BYPLnv)->where('nvservicestatus.ceo_status', 1)
            ->sum('tbl_material.total_budget_both')+DB::table('tbl_service')
            ->join('nvservicestatus', 'tbl_service.id', '=', 'nvservicestatus.service_id')
            ->whereIn('tbl_service.nv_id', $BYPLnv)->where('nvservicestatus.ceo_status', 1)
            ->sum('tbl_service.total_buget');
             $fileDataBYPL =Nvsericestatus::
            select(
                DB::raw('MONTH(created_at) as month'),
                DB::raw('SUM(CASE WHEN ceo_status = "1" THEN 1 ELSE 0 END) as approved_count'),
                DB::raw('SUM(CASE WHEN ( work_rew3_status = "2")  THEN 1 ELSE 0 END) as rejected_count'),
                DB::raw('SUM(CASE WHEN (work_rew1_status = "0" AND work_rew2_status = "0" AND work_rew3_status = "0" AND work_rew4_status = "0" AND cpmg_status = "0") THEN 1 ELSE 0 END) as pending_count'),
               
            ) 
            ->whereIn('nv_id', $BYPLnv)->with(['service', 'material', 'user'])
            ->where(function ($query) {
                $query->where('ces_status', 1)
                    ->orWhere('hod_status', 1)
                    ->where('derc_info', 0);
            })
           
            ->whereYear('created_at', Carbon::now()->year)
            ->groupBy('month')
            ->orderBy('month')
            ->get();
         
        $BYPLlabels = [];
        $BYPLapprovedData = [];
        $BYPLrejectedData = [];
        $BYPLpendingData = [];
    
        foreach ($fileDataBYPL as $dataPointBYPL) {
            $monthBYPL = Carbon::createFromFormat('!m', $dataPointBYPL->month)->format('F');
    
            $BYPLlabels[] = $monthBYPL;
            $BYPLapprovedData[] = $dataPointBYPL->approved_count;
            $BYPLrejectedData[] = $dataPointBYPL->rejected_count;
            $BYPLpendingData[] = $dataPointBYPL->pending_count;
             
         }

                return view("admin.dashboard", compact("approvedAmount","currentFinancialYear","nextFinancialYear","nextToNextFinancialYear", "rejectedAmount", "pendingAmount", "totalAmount","company", "company_id" , "nv_sm_data", "totalNV", "approvedNV", "rejectedNV", "pendingNV" , "hodApproval", "cpmgApproval", "btApproval", "ceoApproval"             ,'BRPLlabels','BRPLapprovedData','BRPLrejectedData','BRPLpendingData','BYPLlabels','BYPLapprovedData','BYPLrejectedData','BYPLpendingData','cesApproval','ceonominee1Approval','ceonominee2Approval','approvedAmountBYPL','approvedAmountBRPL','pendingAmountBYPL','pendingAmountBRPL','rejectedAmountBYPL','rejectedAmountBRPL','groupcioApproval'));
            } elseif ($id1->work_rew4 == $user->id) {
                $Values = [$user->id, $id1->work_rew1, $id1->work_rew2, $id1->work_rew3];
               

                if($fiscal_year){
                    $totalId = NeedValidation::where('fiscal_year', $fiscal_year)->pluck('id');
                }else{
      
                    $totalId = NeedValidation::where('fiscal_year', $currentFinancialYear)->pluck('id');
                }
               
                $totalAmount = DB::table('tbl_material')
                ->join('nvservicestatus', 'tbl_material.id', '=', 'nvservicestatus.material_id')
                
                 ->whereIn('tbl_material.nv_id', $totalId)
                 ->whereIn('nvservicestatus.nv_id', $latestData->pluck('nv_id'))
                ->where('nvservicestatus.work_rew4_status', 1)
                ->sum('tbl_material.total_budget_both') + DB::table('tbl_service')
                ->join('nvservicestatus', 'tbl_service.id', '=', 'nvservicestatus.service_id')
                
                 ->whereIn('tbl_service.nv_id', $totalId)
                 ->whereIn('nvservicestatus.nv_id', $latestData->pluck('nv_id'))
                ->where('nvservicestatus.work_rew4_status', 1)
                ->sum('tbl_service.total_buget');

                $pen_amt = $latestData->filter(function ($data) {
                    return in_array($data->hod_status, [1]) &&
                        in_array($data->work_rew1_status, [0]) &&    
                        in_array($data->work_rew2_status, [0])&&
                        in_array($data->work_rew3_status, [0])&&
                        in_array($data->work_rew4_status, [0])&&
                        in_array($data->cpmg_status, [0])&&
                        (
                            (in_array($data->derc_info, [0]) && in_array($data->ces_status, [0]))
                            ||
                            (in_array($data->derc_info, [1]) && in_array($data->ces_status, [1]))
                        );
                });
                
                $pendingAmount = DB::table('tbl_material')
                ->join('nvservicestatus', 'tbl_material.id', '=', 'nvservicestatus.material_id')
               
                 ->whereIn('tbl_material.nv_id', $totalId)
                 ->whereIn('nvservicestatus.nv_id', $pen_amt->pluck('nv_id'))
                   
                ->sum('tbl_material.total_budget_both') +  DB::table('tbl_service')
                ->join('nvservicestatus', 'tbl_service.id', '=', 'nvservicestatus.service_id')
               
                 ->whereIn('tbl_service.nv_id', $totalId)
                 ->whereIn('nvservicestatus.nv_id', $pen_amt->pluck('nv_id'))
                   
                ->sum('tbl_service.total_buget');
        
                $rejectedAmount = DB::table('tbl_material')
                ->join('nvservicestatus', 'tbl_material.id', '=', 'nvservicestatus.material_id')
                ->whereIn('nvservicestatus.nv_id', $latestData->pluck('nv_id'))
                ->where(function ($query) {
                    $query->where('nvservicestatus.work_rew4_status', 2);
                })
                ->sum('tbl_material.total_budget_both') + DB::table('tbl_service')
                ->join('nvservicestatus', 'tbl_service.id', '=', 'nvservicestatus.service_id')
                ->whereIn('nvservicestatus.nv_id', $latestData->pluck('nv_id'))
                ->where(function ($query) {
                    $query->where('nvservicestatus.work_rew4_status', 2);
                })
                ->sum('tbl_service.total_buget');
           
        
                $approvedAmount = DB::table('tbl_material')
                
                 ->whereIn('tbl_material.nv_id', $totalId)
                 ->whereIn('nvservicestatus.nv_id', $latestData->pluck('nv_id'))
                ->join('nvservicestatus', 'tbl_material.id', '=', 'nvservicestatus.material_id')
                ->where('nvservicestatus.ceo_status', 1)
                ->sum('tbl_material.total_budget_both') + DB::table('tbl_service')
               
                ->whereIn('tbl_service.nv_id', $totalId)
                ->whereIn('nvservicestatus.nv_id', $latestData->pluck('nv_id'))
               ->join('nvservicestatus', 'tbl_service.id', '=', 'nvservicestatus.service_id')
               ->where('nvservicestatus.ceo_status', 1)
               ->sum('tbl_service.total_buget') ;

                if($company_id){
                    $totalNV = Nvsericestatus::whereIn("nv_id", $totalId)->where('work_rew4_status', 1)->where('company_id',$company_id)->count();
                } else {
                    $totalNV = Nvsericestatus::whereIn("nv_id", $totalId)->where('work_rew4_status', 1)->count();
                }
                if($company_id){
                    $rejectedNV = Nvsericestatus::whereIn("nv_id", $totalId)->where('work_rew4_status', 2)->where('company_id',$company_id)->count();
                } else {
                    $rejectedNV = Nvsericestatus::whereIn("nv_id", $totalId)->where('work_rew4_status', 2)->count();
                }
                
                if($company_id){
                $latestData = Nvsericestatus::whereIn("nv_id", $totalId)->where('company_id',$company_id)->get();
            }else {
                $latestData = Nvsericestatus::whereIn("nv_id", $totalId)->get();
            }
                if($company_id){
                    $approvedNV = $latestData->where('ceo_status', 1)->where('company_id',$company_id)->count();
                }else{
                    $approvedNV = $latestData->where('ceo_status', 1)->count();
                }
              
                $pendingNV = $latestData->filter(function ($data) {
                    return in_array($data->hod_status, [1]) &&
                        in_array($data->work_rew1_status, [0]) &&    
                        in_array($data->work_rew2_status, [0])&&
                        in_array($data->work_rew3_status, [0])&&
                        in_array($data->work_rew4_status, [0])&&
                        in_array($data->cpmg_status, [0])&&
                        (
                            (in_array($data->derc_info, [0]) && in_array($data->ces_status, [0]))
                            ||
                            (in_array($data->derc_info, [1]) && in_array($data->ces_status, [1]))
                        );
                })->count();
              
                $nvIds = NeedValidation::where("user_id", $Values)->pluck("id");
                

                if($fiscal_year){
                    $nv = NeedValidation::whereHas('service')->select('id')->where('fiscal_year', $fiscal_year)
                
                    ->get();
                }else{
                    $nv = NeedValidation::whereHas('service')->select('id')->where('fiscal_year', $currentFinancialYear)
                
                ->get();
                  
                }
                $nv_ids = $nv->pluck('id');
                $nv_sm_data = Nvsericestatus::whereIn('nv_id', $nv_ids)->with(['service', 'material', 'user'])
                ->where(function ($query) {
                    $query->where('ces_status', 1)
                        ->orWhere('hod_status', 1)
                        ->where('derc_info', 0);
                })
                ->orderBy('id', 'asc')->get();

                $total11_status = $request->total;
                $pending11_status = $request->pending;
                $reject11_status = $request->rejected;
                 if ($ceo_status !== null) {
                    $nv_sm_data = $nv_sm_data->where('ceo_status', $ceo_status);
                } elseif ($pending11_status !== null) {
                    $nv_sm_data = $nv_sm_data->where('work_rew4_status', $pending11_status)
                    ->whereIn('work_rew1_status', [0])
                    ->whereIn('work_rew2_status', [0])
                     ->whereIn('work_rew3_status', [0]);

                } elseif ($reject11_status !== null) {
                    $nv_sm_data = $nv_sm_data->where('work_rew4_status', $reject11_status);

                }elseif ($total11_status !== null) {
                    $nv_sm_data = $nv_sm_data->where('work_rew3_status', $total11_status);
                      
                }
             
                if($fiscal_year){
                    $BRPLnv = NeedValidation::where('company_id','6')->where('fiscal_year', $fiscal_year)->pluck("id");
                }else{
                    $BRPLnv = NeedValidation::where('company_id','6')->where('fiscal_year', $currentFinancialYear)->pluck("id");
                  
                }
             
                
                $pendingAmountBRPL = DB::table('tbl_material')
                ->join('nvservicestatus', 'tbl_material.id', '=', 'nvservicestatus.material_id')
                ->whereIn('tbl_material.nv_id', $BRPLnv)
                ->whereIn('nvservicestatus.nv_id', $pen_amt->pluck('nv_id'))
               
                ->sum('tbl_material.total_budget_both')+DB::table('tbl_service')
                ->join('nvservicestatus', 'tbl_service.id', '=', 'nvservicestatus.service_id')
                ->whereIn('tbl_service.nv_id', $BRPLnv)
                ->whereIn('nvservicestatus.nv_id', $pen_amt->pluck('nv_id'))
               
                ->sum('tbl_service.total_buget');
        
                $rejectedAmountBRPL = DB::table('tbl_material')
                ->join('nvservicestatus', 'tbl_material.id', '=', 'nvservicestatus.material_id')
                ->whereIn('tbl_material.nv_id', $BRPLnv)
                ->where(function ($query) {
                    $query->where('nvservicestatus.work_rew4_status', 2);
                })
                ->sum('tbl_material.total_budget_both')+DB::table('tbl_service')
                ->join('nvservicestatus', 'tbl_service.id', '=', 'nvservicestatus.service_id')
                ->whereIn('tbl_service.nv_id', $BRPLnv)
                ->where(function ($query) {
                    $query->where('nvservicestatus.work_rew4_status', 2);
                })
                ->sum('tbl_service.total_buget');

              $approvedAmountBRPL = DB::table('tbl_material')
                ->join('nvservicestatus', 'tbl_material.id', '=', 'nvservicestatus.material_id')
                ->whereIn('tbl_material.nv_id', $BRPLnv)->where('nvservicestatus.ceo_status', 1)
                ->sum('tbl_material.total_budget_both')+DB::table('tbl_service')
                ->join('nvservicestatus', 'tbl_service.id', '=', 'nvservicestatus.service_id')
                ->whereIn('tbl_service.nv_id', $BRPLnv)->where('nvservicestatus.ceo_status', 1)
                ->sum('tbl_service.total_buget');

                $fileDataBRPL =Nvsericestatus::
                select(
                    DB::raw('MONTH(created_at) as month'),
                    DB::raw('SUM(CASE WHEN ceo_status = "1" THEN 1 ELSE 0 END) as approved_count'),
                    DB::raw('SUM(CASE WHEN ( work_rew4_status = "2")  THEN 1 ELSE 0 END ) as rejected_count'),
                    DB::raw('SUM(CASE WHEN (work_rew1_status = "0" AND work_rew2_status = "0" AND work_rew3_status = "0" AND work_rew4_status = "0" AND cpmg_status = "0") THEN 1 ELSE 0 END) as pending_count'),
                   
                )
             
                ->whereIn('nv_id', $BRPLnv)->with(['service', 'material', 'user'])
              
                ->where(function ($query) {
                    $query->where('ces_status', 1)
                        ->orWhere('hod_status', 1)
                        ->where('derc_info', 0);
                })
                ->whereYear('created_at', Carbon::now()->year)
                ->groupBy('month')
                ->orderBy('month')
                ->get();
        
            $BRPLlabels = [];
            $BRPLapprovedData = [];
            $BRPLrejectedData = [];
            $BRPLpendingData = [];
        
            foreach ($fileDataBRPL as $dataPointBRPL) {
                $monthBRPL = Carbon::createFromFormat('!m', $dataPointBRPL->month)->format('F');
        
                $BRPLlabels[] = $monthBRPL;
                $BRPLapprovedData[] = $dataPointBRPL->approved_count;
                $BRPLrejectedData[] = $dataPointBRPL->rejected_count;
                $BRPLpendingData[] = $dataPointBRPL->pending_count;
                 
            }
            if($fiscal_year){
                $BYPLnv = NeedValidation::where('company_id','5')->where('fiscal_year', $fiscal_year)->pluck("id");
            }else{
  
                $BYPLnv = NeedValidation::where('company_id','5')->where('fiscal_year', $currentFinancialYear)->pluck("id");
            }
           
            $pendingAmountBYPL = DB::table('tbl_material')
            ->join('nvservicestatus', 'tbl_material.id', '=', 'nvservicestatus.material_id')
            ->whereIn('tbl_material.nv_id', $BYPLnv)
            ->whereIn('nvservicestatus.nv_id', $pen_amt->pluck('nv_id'))
        
            ->sum('tbl_material.total_budget_both')+DB::table('tbl_service')
            ->join('nvservicestatus', 'tbl_service.id', '=', 'nvservicestatus.service_id')
            ->whereIn('tbl_service.nv_id', $BYPLnv)
            ->whereIn('nvservicestatus.nv_id', $pen_amt->pluck('nv_id'))
        
            ->sum('tbl_service.total_buget');
    
            $rejectedAmountBYPL = DB::table('tbl_material')
            ->join('nvservicestatus', 'tbl_material.id', '=', 'nvservicestatus.material_id')
            ->whereIn('tbl_material.nv_id', $BYPLnv)
            ->where(function ($query) {
                $query->where('nvservicestatus.work_rew4_status', 2);
            })
            ->sum('tbl_material.total_budget_both')+DB::table('tbl_service')
            ->join('nvservicestatus', 'tbl_service.id', '=', 'nvservicestatus.service_id')
            ->whereIn('tbl_service.nv_id', $BYPLnv)
            ->where(function ($query) {
                $query->where('nvservicestatus.work_rew4_status', 2);
            })
            ->sum('tbl_service.total_buget');

          $approvedAmountBYPL = DB::table('tbl_material')
            ->join('nvservicestatus', 'tbl_material.id', '=', 'nvservicestatus.material_id')
            ->whereIn('tbl_material.nv_id', $BYPLnv)->where('nvservicestatus.ceo_status', 1)
            ->sum('tbl_material.total_budget_both')+DB::table('tbl_service')
            ->join('nvservicestatus', 'tbl_service.id', '=', 'nvservicestatus.service_id')
            ->whereIn('tbl_service.nv_id', $BYPLnv)->where('nvservicestatus.ceo_status', 1)
            ->sum('tbl_service.total_buget');
             $fileDataBYPL =Nvsericestatus::
            select(
                DB::raw('MONTH(created_at) as month'),
                DB::raw('SUM(CASE WHEN ceo_status = "1" THEN 1 ELSE 0 END) as approved_count'),
                DB::raw('SUM(CASE WHEN ( work_rew1dep2_status = "2")  THEN 1 ELSE 0 END) as rejected_count'),
                DB::raw('SUM(CASE WHEN (work_rew1_status = "0" AND work_rew2_status = "0" AND work_rew3_status = "0" AND work_rew4_status = "0" AND cpmg_status = "0") THEN 1 ELSE 0 END) as pending_count'),
            
            ) 
            ->whereIn('nv_id', $BYPLnv)->with(['service', 'material', 'user'])
            ->where(function ($query) {
                $query->where('ces_status', 1)
                    ->orWhere('hod_status', 1)
                    ->where('derc_info', 0);
            })
         
            ->whereYear('created_at', Carbon::now()->year)
            ->groupBy('month')
            ->orderBy('month')
            ->get();
           
        $BYPLlabels = [];
        $BYPLapprovedData = [];
        $BYPLrejectedData = [];
        $BYPLpendingData = [];
    
        foreach ($fileDataBYPL as $dataPointBYPL) {
            $monthBYPL = Carbon::createFromFormat('!m', $dataPointBYPL->month)->format('F');
    
            $BYPLlabels[] = $monthBYPL;
            $BYPLapprovedData[] = $dataPointBYPL->approved_count;
            $BYPLrejectedData[] = $dataPointBYPL->rejected_count;
            $BYPLpendingData[] = $dataPointBYPL->pending_count;
           
         }
                return view("admin.dashboard", compact("approvedAmount","currentFinancialYear","nextFinancialYear","nextToNextFinancialYear", "rejectedAmount", "pendingAmount", "totalAmount","company", "company_id" , "nv_sm_data", "totalNV", "approvedNV", "rejectedNV", "pendingNV" , "hodApproval", "cpmgApproval", "btApproval", "ceoApproval"             ,'BRPLlabels','BRPLapprovedData','BRPLrejectedData','BRPLpendingData','BYPLlabels','BYPLapprovedData','BYPLrejectedData','BYPLpendingData','cesApproval','ceonominee1Approval','ceonominee2Approval','approvedAmountBYPL','approvedAmountBRPL','rejectedAmountBYPL','rejectedAmountBRPL','pendingAmountBYPL','pendingAmountBRPL','groupcioApproval'));
            } elseif ($id1->approver == $user->id) {
                $Values = [$user->id, $id1->work_rew1, $id1->work_rew2, $id1->work_rew3, $id1->work_rew4];
               
                if($fiscal_year){
                 $totalId = NeedValidation::where('fiscal_year', $fiscal_year)->pluck('id');
                }else{
                 $totalId = NeedValidation::where('fiscal_year', $currentFinancialYear)->pluck('id');
                  
                }
                $totalAmount = DB::table('tbl_material')
                ->join('nvservicestatus', 'tbl_material.id', '=', 'nvservicestatus.material_id')
               
                 ->whereIn('tbl_material.nv_id', $totalId)
                 ->whereIn('nvservicestatus.nv_id', $latestData->pluck('nv_id'))
                ->where('nvservicestatus.approver_status', 1)
                ->sum('tbl_material.total_budget_both') + DB::table('tbl_service')
                ->join('nvservicestatus', 'tbl_service.id', '=', 'nvservicestatus.service_id')
               
                 ->whereIn('tbl_service.nv_id', $totalId)
                 ->whereIn('nvservicestatus.nv_id', $latestData->pluck('nv_id'))
                ->where('nvservicestatus.approver_status', 1)
                ->sum('tbl_service.total_buget');

               
                
                if(!empty($id1->work_rew1) || !empty($id1->work_rew2)|| !empty($id1->work_rew3)|| !empty($id1->work_rew4)){
                    $pen_amt = $latestData->filter(function ($data) {
                        return in_array($data->hod_status, [1]) &&
                           ( in_array($data->work_rew1_status, [1])||in_array($data->work_rew2_status, [1])||in_array($data->work_rew3_status, [1])||in_array($data->work_rew4_status, [1])) &&
                            in_array($data->approver_status, [0]);
                    });
                   }elseif(empty($id1->work_rew1) && empty($id1->work_rew2) && empty($id1->work_rew3) && empty($id1->work_rew4)){
                    $pen_amt = $latestData->filter(function ($data) {
                        return in_array($data->hod_status, [1]) &&
                        in_array($data->approver_status, [0]) &&
                            (
                                (in_array($data->derc_info, [0]) && in_array($data->ces_status, [0]))
                                ||
                                (in_array($data->derc_info, [1]) && in_array($data->ces_status, [1]))
                            );
                    });
                   
                   }

                $pendingAmount = DB::table('tbl_material')
                ->join('nvservicestatus', 'tbl_material.id', '=', 'nvservicestatus.material_id')
               
                 ->whereIn('tbl_material.nv_id', $totalId)
                 ->whereIn('nvservicestatus.nv_id', $pen_amt->pluck('nv_id'))
              
               
                ->sum('tbl_material.total_budget_both') + DB::table('tbl_service')
                ->join('nvservicestatus', 'tbl_service.id', '=', 'nvservicestatus.service_id')
               
                 ->whereIn('tbl_service.nv_id', $totalId)
                 ->whereIn('nvservicestatus.nv_id', $pen_amt->pluck('nv_id'))
              
               
                ->sum('tbl_service.total_buget');
        
                $rejectedAmount = DB::table('tbl_material')
                ->join('nvservicestatus', 'tbl_material.id', '=', 'nvservicestatus.material_id')
                ->whereIn('nvservicestatus.nv_id', $latestData->pluck('nv_id'))
                ->where(function ($query) {
                    $query->where('nvservicestatus.approver_status', 2);
                })
                ->sum('tbl_material.total_budget_both') + DB::table('tbl_service')
                ->join('nvservicestatus', 'tbl_service.id', '=', 'nvservicestatus.service_id')
                ->whereIn('nvservicestatus.nv_id', $latestData->pluck('nv_id'))
                ->where(function ($query) {
                    $query->where('nvservicestatus.approver_status', 2);
                })
                ->sum('tbl_service.total_buget');
           
        
                $approvedAmount = DB::table('tbl_material')
              
                 ->whereIn('tbl_material.nv_id', $totalId)
                 ->whereIn('nvservicestatus.nv_id', $latestData->pluck('nv_id'))
                ->join('nvservicestatus', 'tbl_material.id', '=', 'nvservicestatus.material_id')
                ->where('nvservicestatus.ceo_status', 1)
                ->sum('tbl_material.total_budget_both') + DB::table('tbl_service')
             
                ->whereIn('tbl_service.nv_id', $totalId)
                ->whereIn('nvservicestatus.nv_id', $latestData->pluck('nv_id'))
               ->join('nvservicestatus', 'tbl_service.id', '=', 'nvservicestatus.service_id')
               ->where('nvservicestatus.ceo_status', 1)
               ->sum('tbl_service.total_buget');
                if($company_id){
                    $totalNV = Nvsericestatus::whereIn("nv_id", $totalId)->where('approver_status', 1)->where('company_id',$company_id)->count();
                } else{
                    $totalNV = Nvsericestatus::whereIn("nv_id", $totalId)->where('approver_status', 1)->count();
                }
                if($company_id){
                    $rejectedNV = Nvsericestatus::whereIn("nv_id", $totalId)->where('approver_status', 2)->where('company_id',$company_id)->count();
                } else {
                    $rejectedNV = Nvsericestatus::whereIn("nv_id", $totalId)->where('approver_status', 2)->count();
                }
                
                if($company_id){
                $latestData = Nvsericestatus::whereIn("nv_id", $totalId)->where('company_id',$company_id)->get();
            }else {
                $latestData = Nvsericestatus::whereIn("nv_id", $totalId)->get();
            }
                if($company_id){
                    $approvedNV = $latestData->where('ceo_status', 1)->where('company_id',$company_id)->count();
                }else{
                    $approvedNV = $latestData->where('ceo_status', 1)->count();
                }
                
                    if(!empty($id1->work_rew1) || !empty($id1->work_rew2)|| !empty($id1->work_rew3)|| !empty($id1->work_rew4)){
                        $pendingNV = $latestData->filter(function ($data) {
                            return in_array($data->hod_status, [1]) &&
                               ( in_array($data->work_rew1_status, [1])||in_array($data->work_rew2_status, [1])||in_array($data->work_rew3_status, [1])||in_array($data->work_rew4_status, [1])) &&
                                in_array($data->approver_status, [0]);
                        })->count();
                       }elseif(empty($id1->work_rew1) && empty($id1->work_rew2) && empty($id1->work_rew3) && empty($id1->work_rew4)){
                        $pendingNV = $latestData->filter(function ($data) {
                            return in_array($data->hod_status, [1]) &&
                            in_array($data->approver_status, [0]) &&
                                (
                                    (in_array($data->derc_info, [0]) && in_array($data->ces_status, [0]))
                                    ||
                                    (in_array($data->derc_info, [1]) && in_array($data->ces_status, [1]))
                                );
                        })->count();
                       }

               
                $nvIds = NeedValidation::where("user_id", $Values)->pluck("id");
               
                if($fiscal_year){
                    $nv = NeedValidation::whereHas('service')->select('id')->where('fiscal_year', $fiscal_year)
                
                    ->get();
    
                }else{
                    $nv = NeedValidation::whereHas('service')->select('id')->where('fiscal_year', $currentFinancialYear)
                
                    ->get();
    
                  
                }                
                $nv_ids = $nv->pluck('id');
             
                if(!empty($id1->work_rew1) || !empty($id1->work_rew2)|| !empty($id1->work_rew3)|| !empty($id1->work_rew4)){
                    $nv_sm_data = Nvsericestatus::whereIn('nv_id', $nv_ids)
                    ->with(['service', 'material', 'user'])
                    ->where(function ($query) {
                        $query->where(function ($subQuery) {
                            $subQuery->orWhere('work_rew1_status', 1)
                                ->orWhere('work_rew2_status', 1)
                                ->orWhere('work_rew3_status', 1)
                                ->orWhere('work_rew4_status', 1);
                        })->where(function ($subQuery) {
                            $subQuery->where('work_rew1_status', '!=', 2)
                                ->where('work_rew2_status', '!=', 2)
                                ->where('work_rew3_status', '!=', 2)
                                ->where('work_rew4_status', '!=', 2);
                        })->whereIn('derc_info', [0, 1]);
                    })
                    ->orderBy('id', 'asc')
                    ->get();
                    }elseif(empty($id1->work_rew1) && empty($id1->work_rew2) && empty($id1->work_rew3) && empty($id1->work_rew4)){
                        $nv_sm_data = Nvsericestatus::whereIn('nv_id', $nv_ids)
                        ->with(['service', 'material', 'user'])
                        ->where(function ($query) {
                            $query->where('ces_status', 1)
                                ->orWhere('hod_status', 1)
                                ->where('derc_info', 0);
                        })
                        ->orderBy('id', 'asc')
                        ->get();
                    }

              
                $total12_status = $request->total;
                $pending12_status = $request->pending;
                $reject12_status = $request->rejected;

              if ($ceo_status !== null) {
                    $nv_sm_data = $nv_sm_data->where('ceo_status', $ceo_status);
                } elseif ($pending12_status !== null) {
                    $nv_sm_data =  $nv_sm_data->where('approver_status', $pending12_status)
                    ->where('hod_status',1);
                   
                } elseif ($reject12_status !== null) {
                    $nv_sm_data =  $nv_sm_data->where('approver_status', $reject12_status)
                    ; 
                        
                }elseif ($total12_status !== null) {
                    $nv_sm_data =  $nv_sm_data->where('approver_status', $total12_status);
                      
                }
             
                if($fiscal_year){
                    $BRPLnv = NeedValidation::where('company_id','6')->where('fiscal_year', $fiscal_year)->pluck("id");
                }else{
                    $BRPLnv = NeedValidation::where('company_id','6')->where('fiscal_year', $currentFinancialYear)->pluck("id");
                  
                }
               
                $pendingAmountBRPL = DB::table('tbl_material')
                ->join('nvservicestatus', 'tbl_material.id', '=', 'nvservicestatus.material_id')
                ->whereIn('tbl_material.nv_id', $BRPLnv)
                ->whereIn('nvservicestatus.nv_id', $pen_amt->pluck('nv_id'))
                ->sum('tbl_material.total_budget_both')+DB::table('tbl_service')
                ->join('nvservicestatus', 'tbl_service.id', '=', 'nvservicestatus.service_id')
                ->whereIn('tbl_service.nv_id', $BRPLnv)
                ->whereIn('nvservicestatus.nv_id', $pen_amt->pluck('nv_id'))
                ->sum('tbl_service.total_buget');
        
                $rejectedAmountBRPL = DB::table('tbl_material')
                ->join('nvservicestatus', 'tbl_material.id', '=', 'nvservicestatus.material_id')
                ->whereIn('tbl_material.nv_id', $BRPLnv)
                ->where(function ($query) {
                    $query->where('nvservicestatus.approver_status', 2);
                })
                ->sum('tbl_material.total_budget_both')+DB::table('tbl_service')
                ->join('nvservicestatus', 'tbl_service.id', '=', 'nvservicestatus.service_id')
                ->whereIn('tbl_service.nv_id', $BRPLnv)
                ->where(function ($query) {
                    $query->where('nvservicestatus.approver_status', 2);
                })
                ->sum('tbl_service.total_buget');

              $approvedAmountBRPL = DB::table('tbl_material')
                ->join('nvservicestatus', 'tbl_material.id', '=', 'nvservicestatus.material_id')
                ->whereIn('tbl_material.nv_id', $BRPLnv)->where('nvservicestatus.ceo_status', 1)
                ->sum('tbl_material.total_budget_both')+DB::table('tbl_service')
                ->join('nvservicestatus', 'tbl_service.id', '=', 'nvservicestatus.service_id')
                ->whereIn('tbl_service.nv_id', $BRPLnv)->where('nvservicestatus.ceo_status', 1)
                ->sum('tbl_service.total_buget');

                

                if(!empty($id1->work_rew1) || !empty($id1->work_rew2)|| !empty($id1->work_rew3)|| !empty($id1->work_rew4)){
                    $fileDataBRPL =Nvsericestatus::
                    select(
                        DB::raw('MONTH(created_at) as month'),
                        DB::raw('SUM(CASE WHEN ceo_status = "1" THEN 1 ELSE 0 END) as approved_count'),
                        DB::raw('SUM(CASE WHEN ( approver_status = "2")  THEN 1 ELSE 0 END ) as rejected_count'),
                        DB::raw('SUM(CASE WHEN (approver_status = "0" AND (work_rew1_status = "1" OR work_rew2_status = "1" OR work_rew3_status = "1" OR work_rew4_status = "1")  ) THEN 1 ELSE 0 END) as pending_count'),
                    )
                 
                    ->whereIn('nv_id', $BRPLnv)->with(['service', 'material', 'user'])
                    ->whereIn('derc_info', [0,1])
                    ->where('hod_status',1)
                    ->whereYear('created_at', Carbon::now()->year)
                    ->groupBy('month')
                    ->orderBy('month')
                    ->get();
                   }elseif(empty($id1->work_rew1) && empty($id1->work_rew2) && empty($id1->work_rew3) && empty($id1->work_rew4)){
                    $fileDataBRPL =Nvsericestatus::
                select(
                    DB::raw('MONTH(created_at) as month'),
                    DB::raw('SUM(CASE WHEN ceo_status = "1" THEN 1 ELSE 0 END) as approved_count'),
                    DB::raw('SUM(CASE WHEN ( approver_status = "2")  THEN 1 ELSE 0 END ) as rejected_count'),
                    DB::raw('SUM(CASE WHEN approver_status = "0" AND hod_status = "1" AND 
                    (derc_info = "0" AND ces_status = "0" OR derc_info = "1" AND ces_status = "1")
               THEN 1 ELSE 0 END) as pending_count'),
                )
             
                ->whereIn('nv_id', $BRPLnv)->with(['service', 'material', 'user'])
                ->whereIn('derc_info', [0,1])
                ->where('hod_status',1)
                ->whereYear('created_at', Carbon::now()->year)
                ->groupBy('month')
                ->orderBy('month')
                ->get();
                   }
            $BRPLlabels = [];
            $BRPLapprovedData = [];
            $BRPLrejectedData = [];
            $BRPLpendingData = [];
        
            foreach ($fileDataBRPL as $dataPointBRPL) {
                $monthBRPL = Carbon::createFromFormat('!m', $dataPointBRPL->month)->format('F');
        
                $BRPLlabels[] = $monthBRPL;
                $BRPLapprovedData[] = $dataPointBRPL->approved_count;
                $BRPLrejectedData[] = $dataPointBRPL->rejected_count;
                $BRPLpendingData[] = $dataPointBRPL->pending_count;
            }
            if($fiscal_year){
                $BYPLnv = NeedValidation::where('company_id','5')->where('fiscal_year', $fiscal_year)->pluck("id");
            }else{
                $BYPLnv = NeedValidation::where('company_id','5')->where('fiscal_year', $currentFinancialYear)->pluck("id");
              
            }
          
            $pendingAmountBYPL = DB::table('tbl_material')
            ->join('nvservicestatus', 'tbl_material.id', '=', 'nvservicestatus.material_id')
            ->whereIn('tbl_material.nv_id', $BYPLnv)
            ->whereIn('nvservicestatus.nv_id', $pen_amt->pluck('nv_id'))
      
            ->sum('tbl_material.total_budget_both')+DB::table('tbl_service')
            ->join('nvservicestatus', 'tbl_service.id', '=', 'nvservicestatus.service_id')
            ->whereIn('tbl_service.nv_id', $BYPLnv)
            ->whereIn('nvservicestatus.nv_id', $pen_amt->pluck('nv_id'))
      
            ->sum('tbl_service.total_buget');
    
            $rejectedAmountBYPL = DB::table('tbl_material')
            ->join('nvservicestatus', 'tbl_material.id', '=', 'nvservicestatus.material_id')
            ->whereIn('tbl_material.nv_id', $BYPLnv)
            ->where(function ($query) {
                $query->where('nvservicestatus.approver_status', 2);
            })
            ->sum('tbl_material.total_budget_both')+DB::table('tbl_service')
            ->join('nvservicestatus', 'tbl_service.id', '=', 'nvservicestatus.service_id')
            ->whereIn('tbl_service.nv_id', $BYPLnv)
            ->where(function ($query) {
                $query->where('nvservicestatus.approver_status', 2);
            })
            ->sum('tbl_service.total_buget');

          $approvedAmountBYPL = DB::table('tbl_material')
            ->join('nvservicestatus', 'tbl_material.id', '=', 'nvservicestatus.material_id')
            ->whereIn('tbl_material.nv_id', $BYPLnv)->where('nvservicestatus.ceo_status', 1)
            ->sum('tbl_material.total_budget_both')+DB::table('tbl_service')
            ->join('nvservicestatus', 'tbl_service.id', '=', 'nvservicestatus.service_id')
            ->whereIn('tbl_service.nv_id', $BYPLnv)->where('nvservicestatus.ceo_status', 1)
            ->sum('tbl_service.total_buget');

            


            if(!empty($id1->work_rew1) || !empty($id1->work_rew2)|| !empty($id1->work_rew3)|| !empty($id1->work_rew4)){
                $fileDataBYPL =Nvsericestatus::
            select(
                DB::raw('MONTH(created_at) as month'),
                DB::raw('SUM(CASE WHEN ceo_status = "1" THEN 1 ELSE 0 END) as approved_count'),
                DB::raw('SUM(CASE WHEN ( approver_status = "2")  THEN 1 ELSE 0 END) as rejected_count'),
                DB::raw('SUM(CASE WHEN (approver_status = "0" AND (work_rew1_status = "1" OR work_rew2_status = "1" OR work_rew3_status = "1" OR work_rew4_status = "1")  ) THEN 1 ELSE 0 END) as pending_count'),
             
            ) 
            ->whereIn('nv_id', $BYPLnv)->with(['service', 'material', 'user'])
          
            ->where('hod_status',1)
            ->whereIn('derc_info',[0,1])
            ->whereYear('created_at', Carbon::now()->year)
            ->groupBy('month')
            ->orderBy('month')
            ->get();
               }elseif(empty($id1->work_rew1) && empty($id1->work_rew2) && empty($id1->work_rew3) && empty($id1->work_rew4)){
                $fileDataBYPL =Nvsericestatus::
            select(
                DB::raw('MONTH(created_at) as month'),
                DB::raw('SUM(CASE WHEN ceo_status = "1" THEN 1 ELSE 0 END) as approved_count'),
                DB::raw('SUM(CASE WHEN ( approver_status = "2")  THEN 1 ELSE 0 END) as rejected_count'),
                DB::raw('SUM(CASE WHEN approver_status = "0" AND hod_status = "1" AND 
                           (derc_info = "0" AND ces_status = "0" OR derc_info = "1" AND ces_status = "1")
                      THEN 1 ELSE 0 END) as pending_count'),
               
            ) 
            ->whereIn('nv_id', $BYPLnv)->with(['service', 'material', 'user'])
           
            ->where('hod_status',1)
            ->whereIn('derc_info',[0,1])
            ->whereYear('created_at', Carbon::now()->year)
            ->groupBy('month')
            ->orderBy('month')
            ->get();
               }

        $BYPLlabels = [];
        $BYPLapprovedData = [];
        $BYPLrejectedData = [];
        $BYPLpendingData = [];
    
        foreach ($fileDataBYPL as $dataPointBYPL) {
            $monthBYPL = Carbon::createFromFormat('!m', $dataPointBYPL->month)->format('F');
    
            $BYPLlabels[] = $monthBYPL;
            $BYPLapprovedData[] = $dataPointBYPL->approved_count;
            $BYPLrejectedData[] = $dataPointBYPL->rejected_count;
            $BYPLpendingData[] = $dataPointBYPL->pending_count;
           
         }
                return view("admin.dashboard", compact("approvedAmount","currentFinancialYear","nextFinancialYear","nextToNextFinancialYear", "rejectedAmount", "pendingAmount", "totalAmount","company", "company_id" , "nv_sm_data", "totalNV", "approvedNV", "rejectedNV", "pendingNV" , "hodApproval", "cpmgApproval", "btApproval", "ceoApproval"             ,'BRPLlabels','BRPLapprovedData','BRPLrejectedData','BRPLpendingData','BYPLlabels','BYPLapprovedData','BYPLrejectedData','BYPLpendingData','cesApproval','ceonominee1Approval','ceonominee2Approval','approvedAmountBYPL','approvedAmountBRPL','rejectedAmountBYPL','rejectedAmountBRPL','pendingAmountBYPL','pendingAmountBRPL','groupcioApproval'));
            } elseif ($id2->work_rew1 == $user->id) {
                $Values = [$user->id, $id2];
               
                if($fiscal_year){
                    $totalId = NeedValidation::where('fiscal_year', $fiscal_year)->pluck('id');
                }else{
                    $totalId = NeedValidation::where('fiscal_year', $currentFinancialYear)->pluck('id');
                  
                }
                $totalAmount = DB::table('tbl_material')
                ->join('nvservicestatus', 'tbl_material.id', '=', 'nvservicestatus.material_id')
               
                 ->whereIn('tbl_material.nv_id', $totalId)
                 ->whereIn('nvservicestatus.nv_id', $latestData->pluck('nv_id'))
                ->where('nvservicestatus.work_rew1dep2_status', 1)       
                ->sum('tbl_material.total_budget_both') + DB::table('tbl_service')
                ->join('nvservicestatus', 'tbl_service.id', '=', 'nvservicestatus.service_id')
               
                 ->whereIn('tbl_service.nv_id', $totalId)
                 ->whereIn('nvservicestatus.nv_id', $latestData->pluck('nv_id'))
                ->where('nvservicestatus.work_rew1dep2_status', 1)       
                ->sum('tbl_service.total_buget');
              
                $pen_amt = $latestData->filter(function ($data) {

                    return in_array($data->approver_status, [1]) &&
                        in_array($data->check_technology, [1]) &&
                        in_array($data->work_rew1dep2_status, [0])&&
                        in_array($data->work_rew2dep2_status, [0])&&
                        in_array($data->work_rew3dep2_status, [0])&&
                        in_array($data->work_rew4dep2_status, [0])&&
                        in_array($data->approverdep2_status, [0]);
                });
                $pendingAmount = DB::table('tbl_material')
                ->join('nvservicestatus', 'tbl_material.id', '=', 'nvservicestatus.material_id')
               
                 ->whereIn('tbl_material.nv_id', $totalId)
                 ->whereIn('nvservicestatus.nv_id', $pen_amt->pluck('nv_id'))
                         
                ->sum('tbl_material.total_budget_both') +  DB::table('tbl_service')
                ->join('nvservicestatus', 'tbl_service.id', '=', 'nvservicestatus.service_id')
                
                 ->whereIn('tbl_service.nv_id', $totalId)
                 ->whereIn('nvservicestatus.nv_id', $pen_amt->pluck('nv_id'))
                         
                ->sum('tbl_service.total_buget');
        
                $rejectedAmount = DB::table('tbl_material')
                ->join('nvservicestatus', 'tbl_material.id', '=', 'nvservicestatus.material_id')
                ->whereIn('nvservicestatus.nv_id', $latestData->pluck('nv_id'))
                ->where(function ($query) {
                    $query->where('nvservicestatus.work_rew1dep2_status', 2);
                })
                ->sum('tbl_material.total_budget_both') + DB::table('tbl_service')
                ->join('nvservicestatus', 'tbl_service.id', '=', 'nvservicestatus.service_id')
                ->whereIn('nvservicestatus.nv_id', $latestData->pluck('nv_id'))
                ->where(function ($query) {
                    $query->where('nvservicestatus.work_rew2dep2_status', 2);
                })
                ->sum('tbl_service.total_buget');

                $approvedAmount = DB::table('tbl_material')
               
                 ->whereIn('tbl_material.nv_id', $totalId)
                 ->whereIn('nvservicestatus.nv_id', $latestData->pluck('nv_id'))
                ->join('nvservicestatus', 'tbl_material.id', '=', 'nvservicestatus.material_id')
                ->where('nvservicestatus.ceo_status', 1)
                ->sum('tbl_material.total_budget_both') + DB::table('tbl_service')
              
                ->whereIn('tbl_service.nv_id', $totalId)
                ->whereIn('nvservicestatus.nv_id', $latestData->pluck('nv_id'))
               ->join('nvservicestatus', 'tbl_service.id', '=', 'nvservicestatus.service_id')
               ->where('nvservicestatus.ceo_status', 1)
               ->sum('tbl_service.total_buget');

                if($company_id){
                    $totalNV = Nvsericestatus::whereIn("nv_id", $totalId)->where('work_rew1dep2_status', 1)->where('company_id',$company_id)->count();
                } else {
                    $totalNV = Nvsericestatus::whereIn("nv_id", $totalId)->where('work_rew1dep2_status', 1)->count();
                }
                if($company_id){
                    $rejectedNV = Nvsericestatus::whereIn("nv_id", $totalId)->where('work_rew1dep2_status', 2)->where('company_id',$company_id)->count();
                } else {
                    $rejectedNV = Nvsericestatus::whereIn("nv_id", $totalId)->where('work_rew1dep2_status', 2)->count();
                }
               
                if($company_id){
                $latestData = Nvsericestatus::whereIn("nv_id", $totalId)->where('company_id',$company_id)->get();
            }else {
                $latestData = Nvsericestatus::whereIn("nv_id", $totalId)->get();
            }
                if($company_id){
                    $approvedNV = $latestData->where('ceo_status', 1)->where('company_id',$company_id)->count();
                }else{
                    $approvedNV = $latestData->where('ceo_status', 1)->count();
                }
               
                    $pendingNV = $latestData->filter(function ($data) {

                        return in_array($data->approver_status, [1]) &&
                            in_array($data->check_technology, [1]) &&
                            in_array($data->work_rew1dep2_status, [0])&&
                            in_array($data->work_rew2dep2_status, [0])&&
                            in_array($data->work_rew3dep2_status, [0])&&
                            in_array($data->work_rew4dep2_status, [0])&&
                            in_array($data->approverdep2_status, [0]);
                    })->count();

                $nvIds = NeedValidation::where("user_id", $user->id)->pluck("id");
               
                if($fiscal_year){
                    $nv = NeedValidation::whereHas('service')->select('id')->where('fiscal_year', $fiscal_year)
                
                    ->get();
                }else{
                    $nv = NeedValidation::whereHas('service')->select('id')->where('fiscal_year', $currentFinancialYear)
                
                    ->get();
                  
                }
                $nv_ids = $nv->pluck('id');
              
                    $nv_sm_data = Nvsericestatus::whereIn('nv_id', $nv_ids)->with(['service', 'material', 'user'])->where(function ($query) {
                        $query->where('check_technology', 1);
                    })->where(function ($query) {
                        $query->where('approver_status', 1);
                    })
                ->orderBy('id', 'asc')->get();
             
                $total13_status = $request->total;
                $pending13_status = $request->pending;
                $reject13_status = $request->rejected;

                if ($ceo_status !== null) {
                    $nv_sm_data = $nv_sm_data->where('ceo_status', $ceo_status);
                } elseif ($pending13_status !== null) {
                    $nv_sm_data = $nv_sm_data->where('work_rew1dep2_status', $pending13_status)
                        ->where('work_rew3dep2_status',0)
                        ->where('work_rew2dep2_status',0)
                        ->where('work_rew4dep2_status',0)
                        ->where('hod_status', 1);
                } elseif ($reject13_status !== null) {
                    $nv_sm_data = $nv_sm_data->where('work_rew1dep2_status', $reject13_status);

                }elseif ($total13_status !== null) {
                    $nv_sm_data = $nv_sm_data->where('work_rew1dep2_status', $total13_status);
                      
                }

                if($fiscal_year){
                    $BRPLnv = NeedValidation::where('company_id','6')->where('fiscal_year', $fiscal_year)->pluck("id");
                }else{
                    $BRPLnv = NeedValidation::where('company_id','6')->where('fiscal_year', $currentFinancialYear)->pluck("id");
                  
                }
               
                $pendingAmountBRPL = DB::table('tbl_material')
                ->join('nvservicestatus', 'tbl_material.id', '=', 'nvservicestatus.material_id')
                ->whereIn('tbl_material.nv_id', $BRPLnv)
                ->whereIn('nvservicestatus.nv_id', $pen_amt->pluck('nv_id'))
                ->sum('tbl_material.total_budget_both')+DB::table('tbl_service')
                ->join('nvservicestatus', 'tbl_service.id', '=', 'nvservicestatus.service_id')
                ->whereIn('tbl_service.nv_id', $BRPLnv)
                ->whereIn('nvservicestatus.nv_id', $pen_amt->pluck('nv_id'))
                ->sum('tbl_service.total_buget');
        
                $rejectedAmountBRPL = DB::table('tbl_material')
                ->join('nvservicestatus', 'tbl_material.id', '=', 'nvservicestatus.material_id')
                ->whereIn('tbl_material.nv_id', $BRPLnv)
                ->where(function ($query) {
                    $query->where('nvservicestatus.work_rew1dep2_status', 2);
                })
                ->sum('tbl_material.total_budget_both')+DB::table('tbl_service')
                ->join('nvservicestatus', 'tbl_service.id', '=', 'nvservicestatus.service_id')
                ->whereIn('tbl_service.nv_id', $BRPLnv)
                ->where(function ($query) {
                    $query->where('nvservicestatus.work_rew1dep2_status', 2);
                })
                ->sum('tbl_service.total_buget');

              $approvedAmountBRPL = DB::table('tbl_material')
                ->join('nvservicestatus', 'tbl_material.id', '=', 'nvservicestatus.material_id')
                ->whereIn('tbl_material.nv_id', $BRPLnv)->where('nvservicestatus.ceo_status', 1)
                ->sum('tbl_material.total_budget_both')+DB::table('tbl_service')
                ->join('nvservicestatus', 'tbl_service.id', '=', 'nvservicestatus.service_id')
                ->whereIn('tbl_service.nv_id', $BRPLnv)->where('nvservicestatus.ceo_status', 1)
                ->sum('tbl_service.total_buget');

                $fileDataBRPL =Nvsericestatus::
                select(
                    DB::raw('MONTH(created_at) as month'),
                    DB::raw('SUM(CASE WHEN ceo_status = "1" THEN 1 ELSE 0 END) as approved_count'),
                    DB::raw('SUM(CASE WHEN ( work_rew1dep2_status = "2")  THEN 1 ELSE 0 END ) as rejected_count'),
                    DB::raw('SUM(CASE WHEN (check_technology="1" AND work_rew1dep2_status = "0" AND work_rew2dep2_status = "0" AND work_rew3dep2_status = "0" AND work_rew4dep2_status = "0" AND approverdep2_status = "0") THEN 1 ELSE 0 END) as pending_count'),
                   
                )
             
                ->whereIn('nv_id', $BRPLnv)->with(['service', 'material', 'user'])
                ->where(function ($query) {
                    $query->where('check_technology', 1);
                })->where(function ($query) {
                    $query->where('approver_status', 1);
                })
             
                ->whereYear('created_at', Carbon::now()->year)
                ->groupBy('month')
                ->orderBy('month')
                ->get();
     
            $BRPLlabels = [];
            $BRPLapprovedData = [];
            $BRPLrejectedData = [];
            $BRPLpendingData = [];
        
            foreach ($fileDataBRPL as $dataPointBRPL) {
                $monthBRPL = Carbon::createFromFormat('!m', $dataPointBRPL->month)->format('F');
        
                $BRPLlabels[] = $monthBRPL;
                $BRPLapprovedData[] = $dataPointBRPL->approved_count;
                $BRPLrejectedData[] = $dataPointBRPL->rejected_count;
                $BRPLpendingData[] = $dataPointBRPL->pending_count;
                 
            }
            if($fiscal_year){
                $BYPLnv = NeedValidation::where('company_id','5')->where('fiscal_year', $fiscal_year)->pluck("id");
            }else{
                $BYPLnv = NeedValidation::where('company_id','5')->where('fiscal_year', $currentFinancialYear)->pluck("id");
              
            }
          
            $pendingAmountBYPL = DB::table('tbl_material')
            ->join('nvservicestatus', 'tbl_material.id', '=', 'nvservicestatus.material_id')
            ->whereIn('tbl_material.nv_id', $BYPLnv)
            ->whereIn('nvservicestatus.nv_id', $pen_amt->pluck('nv_id'))
            
            ->sum('tbl_material.total_budget_both')+DB::table('tbl_service')
            ->join('nvservicestatus', 'tbl_service.id', '=', 'nvservicestatus.service_id')
            ->whereIn('tbl_service.nv_id', $BYPLnv)
            ->whereIn('nvservicestatus.nv_id', $pen_amt->pluck('nv_id'))
            
            ->sum('tbl_service.total_buget');
    
            $rejectedAmountBYPL = DB::table('tbl_material')
            ->join('nvservicestatus', 'tbl_material.id', '=', 'nvservicestatus.material_id')
            ->whereIn('tbl_material.nv_id', $BYPLnv)
            ->where(function ($query) {
                $query->where('nvservicestatus.work_rew1dep2_status', 2);
            })
            ->sum('tbl_material.total_budget_both')+DB::table('tbl_service')
            ->join('nvservicestatus', 'tbl_service.id', '=', 'nvservicestatus.service_id')
            ->whereIn('tbl_service.nv_id', $BYPLnv)
            ->where(function ($query) {
                $query->where('nvservicestatus.work_rew1dep2_status', 2);
            })
            ->sum('tbl_service.total_buget');

          $approvedAmountBYPL = DB::table('tbl_material')
            ->join('nvservicestatus', 'tbl_material.id', '=', 'nvservicestatus.material_id')
            ->whereIn('tbl_material.nv_id', $BYPLnv)->where('nvservicestatus.ceo_status', 1)
            ->sum('tbl_material.total_budget_both')+DB::table('tbl_service')
            ->join('nvservicestatus', 'tbl_service.id', '=', 'nvservicestatus.service_id')
            ->whereIn('tbl_service.nv_id', $BYPLnv)->where('nvservicestatus.ceo_status', 1)
            ->sum('tbl_service.total_buget');
             $fileDataBYPL =Nvsericestatus::
            select(
                DB::raw('MONTH(created_at) as month'),
                DB::raw('SUM(CASE WHEN ceo_status = "1" THEN 1 ELSE 0 END) as approved_count'),
                DB::raw('SUM(CASE WHEN ( work_rew1dep2_status = "2")  THEN 1 ELSE 0 END) as rejected_count'),
                DB::raw('SUM(CASE WHEN (check_technology="1" AND work_rew1dep2_status = "0" AND work_rew2dep2_status = "0" AND work_rew3dep2_status = "0" AND work_rew4dep2_status = "0" AND approverdep2_status = "0") THEN 1 ELSE 0 END) as pending_count'),
              
            ) 
            ->whereIn('nv_id', $BYPLnv)->with(['service', 'material', 'user'])
            ->where(function ($query) {
                $query->where('check_technology', 1);
            })->where(function ($query) {
                $query->where('approver_status', 1);
            })
       
            ->whereYear('created_at', Carbon::now()->year)
            ->groupBy('month')
            ->orderBy('month')
            ->get();
          
        $BYPLlabels = [];
        $BYPLapprovedData = [];
        $BYPLrejectedData = [];
        $BYPLpendingData = [];
    
        foreach ($fileDataBYPL as $dataPointBYPL) {
            $monthBYPL = Carbon::createFromFormat('!m', $dataPointBYPL->month)->format('F');
    
            $BYPLlabels[] = $monthBYPL;
            $BYPLapprovedData[] = $dataPointBYPL->approved_count;
            $BYPLrejectedData[] = $dataPointBYPL->rejected_count;
            $BYPLpendingData[] = $dataPointBYPL->pending_count;
             
        }
                return view("admin.dashboard", compact("approvedAmount","currentFinancialYear","nextFinancialYear","nextToNextFinancialYear", "rejectedAmount", "pendingAmount", "totalAmount","company", "company_id" , "nv_sm_data", "totalNV", "approvedNV", "rejectedNV", "pendingNV" , "hodApproval", "cpmgApproval", "btApproval", "ceoApproval"             ,'BRPLlabels','BRPLapprovedData','BRPLrejectedData','BRPLpendingData','BYPLlabels','BYPLapprovedData','BYPLrejectedData','BYPLpendingData','cesApproval','ceonominee1Approval','ceonominee2Approval','approvedAmountBYPL','approvedAmountBRPL','rejectedAmountBYPL','rejectedAmountBRPL','pendingAmountBYPL','pendingAmountBRPL','groupcioApproval'));
            } elseif ($id2->work_rew2 == $user->id) {
                $Values = [$user->id, $id2->work_rew1];
               
                if($fiscal_year){
                    $totalId = NeedValidation::where('fiscal_year', $fiscal_year)->pluck('id');
                }else{
      
                    $totalId = NeedValidation::where('fiscal_year', $currentFinancialYear)->pluck('id');
                }
                $totalAmount = DB::table('tbl_material')
                ->join('nvservicestatus', 'tbl_material.id', '=', 'nvservicestatus.material_id')
                
                 ->whereIn('tbl_material.nv_id', $totalId)
                 ->whereIn('nvservicestatus.nv_id', $latestData->pluck('nv_id'))
                ->where('nvservicestatus.work_rew2dep2_status', 1)
                ->sum('tbl_material.total_budget_both') + DB::table('tbl_service')
                ->join('nvservicestatus', 'tbl_service.id', '=', 'nvservicestatus.service_id')
                
                 ->whereIn('tbl_service.nv_id', $totalId)
                 ->whereIn('nvservicestatus.nv_id', $latestData->pluck('nv_id'))
                ->where('nvservicestatus.work_rew2dep2_status', 1)
                ->sum('tbl_service.total_buget');
              
                $pen_amt = $latestData->filter(function ($data) {

                    return in_array($data->approver_status, [1]) &&
                        in_array($data->check_technology, [1]) &&
                        in_array($data->work_rew1dep2_status, [0])&&
                        in_array($data->work_rew2dep2_status, [0])&&
                        in_array($data->work_rew3dep2_status, [0])&&
                        in_array($data->work_rew4dep2_status, [0])&&
                        in_array($data->approverdep2_status, [0]);
                });
                $pendingAmount = DB::table('tbl_material')
                ->join('nvservicestatus', 'tbl_material.id', '=', 'nvservicestatus.material_id')
                
                 ->whereIn('tbl_material.nv_id', $totalId)
                 ->whereIn('nvservicestatus.nv_id', $pen_amt->pluck('nv_id'))
                        
                ->sum('tbl_material.total_budget_both') + DB::table('tbl_service')
                ->join('nvservicestatus', 'tbl_service.id', '=', 'nvservicestatus.service_id')
                
                 ->whereIn('tbl_service.nv_id', $totalId)
                 ->whereIn('nvservicestatus.nv_id', $pen_amt->pluck('nv_id'))
                        
                ->sum('tbl_service.total_buget');
        
                $rejectedAmount = DB::table('tbl_material')
                ->join('nvservicestatus', 'tbl_material.id', '=', 'nvservicestatus.material_id')
                ->whereIn('nvservicestatus.nv_id', $latestData->pluck('nv_id'))
                ->where(function ($query) {
                    $query->where('nvservicestatus.work_rew2dep2_status', 2);
                })
                ->sum('tbl_material.total_budget_both') + DB::table('tbl_service')
                ->join('nvservicestatus', 'tbl_service.id', '=', 'nvservicestatus.service_id')
                ->whereIn('nvservicestatus.nv_id', $latestData->pluck('nv_id'))
                ->where(function ($query) {
                    $query->where('nvservicestatus.work_rew2dep2_status', 2);
                })
                ->sum('tbl_service.total_buget');
           
        
                $approvedAmount = DB::table('tbl_material')
              
                 ->whereIn('tbl_material.nv_id', $totalId)
                 ->whereIn('nvservicestatus.nv_id', $latestData->pluck('nv_id'))
                ->join('nvservicestatus', 'tbl_material.id', '=', 'nvservicestatus.material_id')
                ->where('nvservicestatus.ceo_status', 1)
                ->sum('tbl_material.total_budget_both') + DB::table('tbl_service')
             
                ->whereIn('tbl_service.nv_id', $totalId)
                ->whereIn('nvservicestatus.nv_id', $latestData->pluck('nv_id'))
               ->join('nvservicestatus', 'tbl_service.id', '=', 'nvservicestatus.service_id')
               ->where('nvservicestatus.ceo_status', 1)
               ->sum('tbl_service.total_buget');

                if($company_id){
                    $totalNV = Nvsericestatus::whereIn("nv_id", $totalId)->where('work_rew2dep2_status', 1)->where('company_id',$company_id)->count();
                } else {
                    $totalNV = Nvsericestatus::whereIn("nv_id", $totalId)->where('work_rew2dep2_status', 1)->count();
                }
                if($company_id){
                    $rejectedNV = Nvsericestatus::whereIn("nv_id", $totalId)->where('work_rew2dep2_status', 2)->where('company_id',$company_id)->count();
                } else {
                    $rejectedNV = Nvsericestatus::whereIn("nv_id", $totalId)->where('work_rew2dep2_status', 2)->count();
                }
             
                if($company_id){
                $latestData = Nvsericestatus::whereIn("nv_id", $totalId)->where('company_id',$company_id)->get();
            }else {
                $latestData = Nvsericestatus::whereIn("nv_id", $totalId)->get();
            }
                if($company_id){
                    $approvedNV = $latestData->where('ceo_status', 1)->where('company_id',$company_id)->count();
                }else{
                    $approvedNV = $latestData->where('ceo_status', 1)->count();
                }
             
                $pendingNV = $latestData->filter(function ($data) {

                    return in_array($data->approver_status, [1]) &&
                        in_array($data->check_technology, [1]) &&
                        in_array($data->work_rew1dep2_status, [0])&&
                        in_array($data->work_rew2dep2_status, [0])&&
                        in_array($data->work_rew3dep2_status, [0])&&
                        in_array($data->work_rew4dep2_status, [0])&&
                        in_array($data->approverdep2_status, [0]);
                })->count();
               
                $nvIds = NeedValidation::where("user_id", $Values)->pluck("id");
               
                if($fiscal_year){
                    $nv = NeedValidation::whereHas('service')->select('id')->where('fiscal_year', $fiscal_year)
                
                    ->get();
                }else{
                  $nv = NeedValidation::whereHas('service')->select('id')->where('fiscal_year', $currentFinancialYear)
                
                ->get();
                  
                }
                $nv_ids = $nv->pluck('id');
              
             
                    $nv_sm_data = Nvsericestatus::whereIn('nv_id', $nv_ids)->with(['service', 'material', 'user'])
                    ->where(function ($query) {
                        $query->where('check_technology', 1);
                    })->where(function ($query) {
                        $query->where('approver_status', 1);
                    })
                
                ->orderBy('id', 'asc')->get();
          
                $total14_status = $request->total;
                $pending14_status = $request->pending;
                $reject14_status = $request->rejected;

              if ($ceo_status !== null) {
                    $nv_sm_data = $nv_sm_data->where('ceo_status', $ceo_status);
                } elseif ($pending14_status !== null) {
                    $nv_sm_data = $nv_sm_data->where('work_rew2dep2_status', $pending14_status)
                    ->where('work_rew3dep2_status',0)
                    ->where('work_rew1dep2_status',0)
                    ->where('work_rew4dep2_status',0)
                    ->where('hod_status', 1);
                      
                } elseif ($reject14_status !== null) {
                    $nv_sm_data = $nv_sm_data->where('work_rew2dep2_status', $reject14_status);
                    
                }elseif ($total14_status !== null) {
                    $nv_sm_data = $nv_sm_data->where('work_rew2dep2_status', $total14_status);
                      
                }
        
                if($fiscal_year){
                    $BRPLnv = NeedValidation::where('company_id','6')->where('fiscal_year', $fiscal_year)->pluck("id");
                }else{
                    $BRPLnv = NeedValidation::where('company_id','6')->where('fiscal_year', $currentFinancialYear)->pluck("id");
                  
                }
               
                $pendingAmountBRPL = DB::table('tbl_material')
                ->join('nvservicestatus', 'tbl_material.id', '=', 'nvservicestatus.material_id')
                ->whereIn('tbl_material.nv_id', $BRPLnv)
                ->whereIn('nvservicestatus.nv_id', $pen_amt->pluck('nv_id'))
             
                ->sum('tbl_material.total_budget_both')+DB::table('tbl_service')
                ->join('nvservicestatus', 'tbl_service.id', '=', 'nvservicestatus.service_id')
                ->whereIn('tbl_service.nv_id', $BRPLnv)
                ->whereIn('nvservicestatus.nv_id', $pen_amt->pluck('nv_id'))
             
                ->sum('tbl_service.total_buget');
        
                $rejectedAmountBRPL = DB::table('tbl_material')
                ->join('nvservicestatus', 'tbl_material.id', '=', 'nvservicestatus.material_id')
                ->whereIn('tbl_material.nv_id', $BRPLnv)
                ->where(function ($query) {
                    $query->where('nvservicestatus.work_rew2dep2_status', 2);
                })
                ->sum('tbl_material.total_budget_both')+DB::table('tbl_service')
                ->join('nvservicestatus', 'tbl_service.id', '=', 'nvservicestatus.service_id')
                ->whereIn('tbl_service.nv_id', $BRPLnv)
                ->where(function ($query) {
                    $query->where('nvservicestatus.work_rew2dep2_status', 2);
                })
                ->sum('tbl_service.total_buget');

              $approvedAmountBRPL = DB::table('tbl_material')
                ->join('nvservicestatus', 'tbl_material.id', '=', 'nvservicestatus.material_id')
                ->whereIn('tbl_material.nv_id', $BRPLnv)->where('nvservicestatus.ceo_status', 1)
                ->sum('tbl_material.total_budget_both')+DB::table('tbl_service')
                ->join('nvservicestatus', 'tbl_service.id', '=', 'nvservicestatus.service_id')
                ->whereIn('tbl_service.nv_id', $BRPLnv)->where('nvservicestatus.ceo_status', 1)
                ->sum('tbl_service.total_buget');

                $fileDataBRPL =Nvsericestatus::
                select(
                    DB::raw('MONTH(created_at) as month'),
                    DB::raw('SUM(CASE WHEN ceo_status = "1" THEN 1 ELSE 0 END) as approved_count'),
                    DB::raw('SUM(CASE WHEN ( work_rew2dep2_status = "2")  THEN 1 ELSE 0 END ) as rejected_count'),
                    DB::raw('SUM(CASE WHEN (check_technology="1" AND work_rew1dep2_status = "0" AND work_rew2dep2_status = "0" AND work_rew3dep2_status = "0" AND work_rew4dep2_status = "0" AND approverdep2_status = "0") THEN 1 ELSE 0 END) as pending_count'),
               
                )
               
                ->whereIn('nv_id', $BRPLnv)->with(['service', 'material', 'user'])
                ->where(function ($query) {
                    $query->where('check_technology', 1);
                })->where(function ($query) {
                    $query->where('approver_status', 1);
                })
               
                ->whereYear('created_at', Carbon::now()->year)
                ->groupBy('month')
                ->orderBy('month')
                ->get();
        
            $BRPLlabels = [];
            $BRPLapprovedData = [];
            $BRPLrejectedData = [];
            $BRPLpendingData = [];
        
            foreach ($fileDataBRPL as $dataPointBRPL) {
                $monthBRPL = Carbon::createFromFormat('!m', $dataPointBRPL->month)->format('F');
        
                $BRPLlabels[] = $monthBRPL;
                $BRPLapprovedData[] = $dataPointBRPL->approved_count;
                $BRPLrejectedData[] = $dataPointBRPL->rejected_count;
                $BRPLpendingData[] = $dataPointBRPL->pending_count;
               
            }
    
            if($fiscal_year){
                $BYPLnv = NeedValidation::where('company_id','5')->where('fiscal_year', $fiscal_year)->pluck("id");
            }else{
                $BYPLnv = NeedValidation::where('company_id','5')->where('fiscal_year', $currentFinancialYear)->pluck("id");
              
            }
           
            $pendingAmountBYPL = DB::table('tbl_material')
            ->join('nvservicestatus', 'tbl_material.id', '=', 'nvservicestatus.material_id')
            ->whereIn('tbl_material.nv_id', $BYPLnv)
            ->whereIn('nvservicestatus.nv_id', $pen_amt->pluck('nv_id'))
         
            ->sum('tbl_material.total_budget_both')+DB::table('tbl_service')
            ->join('nvservicestatus', 'tbl_service.id', '=', 'nvservicestatus.service_id')
            ->whereIn('tbl_service.nv_id', $BYPLnv)
            ->whereIn('nvservicestatus.nv_id', $pen_amt->pluck('nv_id'))
         
            ->sum('tbl_service.total_buget');
    
            $rejectedAmountBYPL = DB::table('tbl_material')
            ->join('nvservicestatus', 'tbl_material.id', '=', 'nvservicestatus.material_id')
            ->whereIn('tbl_material.nv_id', $BYPLnv)
            ->where(function ($query) {
                $query->where('nvservicestatus.work_rew2dep2_status', 2);
            })
            ->sum('tbl_material.total_budget_both')+DB::table('tbl_service')
            ->join('nvservicestatus', 'tbl_service.id', '=', 'nvservicestatus.service_id')
            ->whereIn('tbl_service.nv_id', $BYPLnv)
            ->where(function ($query) {
                $query->where('nvservicestatus.work_rew2dep2_status', 2);
            })
            ->sum('tbl_service.total_buget');

          $approvedAmountBYPL = DB::table('tbl_material')
            ->join('nvservicestatus', 'tbl_material.id', '=', 'nvservicestatus.material_id')
            ->whereIn('tbl_material.nv_id', $BYPLnv)->where('nvservicestatus.ceo_status', 1)
            ->sum('tbl_material.total_budget_both')+DB::table('tbl_service')
            ->join('nvservicestatus', 'tbl_service.id', '=', 'nvservicestatus.service_id')
            ->whereIn('tbl_service.nv_id', $BYPLnv)->where('nvservicestatus.ceo_status', 1)
            ->sum('tbl_service.total_buget');
             $fileDataBYPL =Nvsericestatus::
            select(
                DB::raw('MONTH(created_at) as month'),
                DB::raw('SUM(CASE WHEN ceo_status = "1" THEN 1 ELSE 0 END) as approved_count'),
                DB::raw('SUM(CASE WHEN ( work_rew2dep2_status = "2")  THEN 1 ELSE 0 END) as rejected_count'),
                DB::raw('SUM(CASE WHEN (check_technology="1" AND work_rew1dep2_status = "0" AND work_rew2dep2_status = "0" AND work_rew3dep2_status = "0" AND work_rew4dep2_status = "0" AND approverdep2_status = "0") THEN 1 ELSE 0 END) as pending_count'),
              
            ) 
            ->whereIn('nv_id', $BYPLnv)->with(['service', 'material', 'user'])
            ->where(function ($query) {
                $query->where('check_technology', 1);
            })->where(function ($query) {
                $query->where('approver_status', 1);
            })
           
            ->whereYear('created_at', Carbon::now()->year)
            ->groupBy('month')
            ->orderBy('month')
            ->get();
        
        $BYPLlabels = [];
        $BYPLapprovedData = [];
        $BYPLrejectedData = [];
        $BYPLpendingData = [];
    
        foreach ($fileDataBYPL as $dataPointBYPL) {
            $monthBYPL = Carbon::createFromFormat('!m', $dataPointBYPL->month)->format('F');
    
            $BYPLlabels[] = $monthBYPL;
            $BYPLapprovedData[] = $dataPointBYPL->approved_count;
            $BYPLrejectedData[] = $dataPointBYPL->rejected_count;
            $BYPLpendingData[] = $dataPointBYPL->pending_count;
            
        }
                return view("admin.dashboard", compact("approvedAmount","currentFinancialYear","nextFinancialYear","nextToNextFinancialYear", "rejectedAmount", "pendingAmount", "totalAmount","company", "company_id" , "nv_sm_data", "totalNV", "approvedNV", "rejectedNV", "pendingNV" , "hodApproval", "cpmgApproval", "btApproval", "ceoApproval"             ,'BRPLlabels','BRPLapprovedData','BRPLrejectedData','BRPLpendingData','BYPLlabels','BYPLapprovedData','BYPLrejectedData','BYPLpendingData','cesApproval','ceonominee1Approval','ceonominee2Approval','approvedAmountBYPL','approvedAmountBRPL','rejectedAmountBYPL','rejectedAmountBRPL','pendingAmountBYPL','pendingAmountBRPL','groupcioApproval'));
            } elseif ($id2->work_rew3 == $user->id) {
                $Values = [$user->id, $id2->work_rew1, $id2->work_rew2];
              
                if($fiscal_year){
                    $totalId = NeedValidation::where('fiscal_year', $fiscal_year)->pluck('id');
                }else{
                    $totalId = NeedValidation::where('fiscal_year', $currentFinancialYear)->pluck('id');
                  
                }
                $totalAmount = DB::table('tbl_material')
                ->join('nvservicestatus', 'tbl_material.id', '=', 'nvservicestatus.material_id')
              
                 ->whereIn('tbl_material.nv_id', $totalId)
                 ->whereIn('nvservicestatus.nv_id', $latestData->pluck('nv_id'))
                ->where('nvservicestatus.work_rew3dep2_status', 1)
                ->sum('tbl_material.total_budget_both') + DB::table('tbl_service')
                ->join('nvservicestatus', 'tbl_service.id', '=', 'nvservicestatus.service_id')
              
                 ->whereIn('tbl_service.nv_id', $totalId)
                 ->whereIn('nvservicestatus.nv_id', $latestData->pluck('nv_id'))
                ->where('nvservicestatus.work_rew3dep2_status', 1)
                ->sum('tbl_service.total_buget');
              
                $pen_amt = $latestData->filter(function ($data) {

                    return in_array($data->approver_status, [1]) &&
                        in_array($data->check_technology, [1]) &&
                        in_array($data->work_rew1dep2_status, [0])&&
                        in_array($data->work_rew2dep2_status, [0])&&
                        in_array($data->work_rew3dep2_status, [0])&&
                        in_array($data->work_rew4dep2_status, [0])&&
                        in_array($data->approverdep2_status, [0]);
                });
                $pendingAmount = DB::table('tbl_material')
                ->join('nvservicestatus', 'tbl_material.id', '=', 'nvservicestatus.material_id')
                
                 ->whereIn('tbl_material.nv_id', $totalId)
                 ->whereIn('nvservicestatus.nv_id', $pen_amt->pluck('nv_id'))
                          
                ->sum('tbl_material.total_budget_both') +  DB::table('tbl_service')
                ->join('nvservicestatus', 'tbl_service.id', '=', 'nvservicestatus.service_id')
                
                 ->whereIn('tbl_service.nv_id', $totalId)
                 ->whereIn('nvservicestatus.nv_id', $pen_amt->pluck('nv_id'))
                          
                ->sum('tbl_service.total_buget');
        
                $rejectedAmount = DB::table('tbl_material')
                ->join('nvservicestatus', 'tbl_material.id', '=', 'nvservicestatus.material_id')
                ->whereIn('nvservicestatus.nv_id', $latestData->pluck('nv_id'))
                ->where(function ($query) {
                    $query->where('nvservicestatus.work_rew3dep2_status', 2);
                })
                ->sum('tbl_material.total_budget_both') + DB::table('tbl_service')
                ->join('nvservicestatus', 'tbl_service.id', '=', 'nvservicestatus.service_id')
                ->whereIn('nvservicestatus.nv_id', $latestData->pluck('nv_id'))
                ->where(function ($query) {
                    $query->where('nvservicestatus.work_rew3dep2_status', 2);
                })
                ->sum('tbl_service.total_buget');
           
        
                $approvedAmount = DB::table('tbl_material')
               
                 ->whereIn('tbl_material.nv_id', $totalId)
                 ->whereIn('nvservicestatus.nv_id', $latestData->pluck('nv_id')) 
                ->join('nvservicestatus', 'tbl_material.id', '=', 'nvservicestatus.material_id')
                ->where('nvservicestatus.ceo_status', 1)
                ->sum('tbl_material.total_budget_both') + DB::table('tbl_service')
              
                ->whereIn('tbl_service.nv_id', $totalId)
                ->whereIn('nvservicestatus.nv_id', $latestData->pluck('nv_id')) 
               ->join('nvservicestatus', 'tbl_service.id', '=', 'nvservicestatus.service_id')
               ->where('nvservicestatus.ceo_status', 1)
               ->sum('tbl_service.total_buget');
                if($company_id){
                    $totalNV = Nvsericestatus::whereIn("nv_id", $totalId)->where('work_rew3dep2_status', 1)->where('company_id',$company_id)->count();
                } else {
                    $totalNV = Nvsericestatus::whereIn("nv_id", $totalId)->where('work_rew3dep2_status', 1)->count();
                }
                if($company_id){
                    $rejectedNV = Nvsericestatus::whereIn("nv_id", $totalId)->where('work_rew3dep2_status', 2)->where('company_id',$company_id)->count();
                } else {
                    $rejectedNV = Nvsericestatus::whereIn("nv_id", $totalId)->where('work_rew3dep2_status', 2)->count();
                }
               
                if($company_id){
                $latestData = Nvsericestatus::whereIn("nv_id", $totalId)->where('company_id',$company_id)->get();
            }else {
                $latestData = Nvsericestatus::whereIn("nv_id", $totalId)->get();
            }
                if($company_id){
                    $approvedNV = $latestData->where('ceo_status', 1)->where('company_id',$company_id)->count();
                }else{
                    $approvedNV = $latestData->where('ceo_status', 1)->count();
                }
              
                $pendingNV = $latestData->filter(function ($data) {

                    return in_array($data->approver_status, [1]) &&
                        in_array($data->check_technology, [1]) &&
                        in_array($data->work_rew1dep2_status, [0])&&
                        in_array($data->work_rew2dep2_status, [0])&&
                        in_array($data->work_rew3dep2_status, [0])&&
                        in_array($data->work_rew4dep2_status, [0])&&
                        in_array($data->approverdep2_status, [0]);
                    })->count();
              
                $nvIds = NeedValidation::where("user_id", $Values)->pluck("id");
                
                if($fiscal_year){
                    $nv = NeedValidation::whereHas('service')->select('id')->where('fiscal_year', $fiscal_year)
               
               
                    ->get();
                }else{
                    $nv = NeedValidation::whereHas('service')->select('id')->where('fiscal_year', $currentFinancialYear)
               
               
                    ->get();
                  
                }
                $nv_ids = $nv->pluck('id');
              
             
                  $nv_sm_data = Nvsericestatus::whereIn('nv_id', $nv_ids)->with(['service', 'material', 'user'])
                    ->where(function ($query) {
                        $query->where('check_technology', 1);
                    })->where(function ($query) {
                        $query->where('approver_status', 1);
                    })
               
                ->orderBy('id', 'asc')->get();
          
                $total15_status = $request->total;
                $pending15_status = $request->pending;
                $reject15_status = $request->rejected;

               if ($ceo_status !== null) {
                    $nv_sm_data = $nv_sm_data->where('ceo_status', $ceo_status);
                } elseif ($pending15_status !== null) {
                    $nv_sm_data = $nv_sm_data->where('work_rew3dep2_status', $pending15_status)
                    ->where('work_rew1dep2_status',0)
                    ->where('work_rew2dep2_status',0)
                    ->where('work_rew4dep2_status',0)
                    ->where('hod_status', 1);
                      
                } elseif ($reject15_status !== null) {
                    $nv_sm_data = $nv_sm_data->where('work_rew3dep2_status', $reject15_status);
                    
                }elseif ($total15_status !== null) {
                    $nv_sm_data = $nv_sm_data->where('work_rew3dep2_status', $total15_status);
                      
                }

                if($fiscal_year){
                    $BRPLnv = NeedValidation::where('company_id','6')->where('fiscal_year', $fiscal_year)->pluck("id");
                }else{

                    $BRPLnv = NeedValidation::where('company_id','6')->where('fiscal_year', $currentFinancialYear)->pluck("id");
                }
                    
               
                $pendingAmountBRPL = DB::table('tbl_material')
                ->join('nvservicestatus', 'tbl_material.id', '=', 'nvservicestatus.material_id')
                ->whereIn('tbl_material.nv_id', $BRPLnv)
                ->whereIn('nvservicestatus.nv_id', $pen_amt->pluck('nv_id'))
        
                ->sum('tbl_material.total_budget_both')+DB::table('tbl_service')
                ->join('nvservicestatus', 'tbl_service.id', '=', 'nvservicestatus.service_id')
                ->whereIn('tbl_service.nv_id', $BRPLnv)
                ->whereIn('nvservicestatus.nv_id', $pen_amt->pluck('nv_id'))
        
                ->sum('tbl_service.total_buget');
        
                $rejectedAmountBRPL = DB::table('tbl_material')
                ->join('nvservicestatus', 'tbl_material.id', '=', 'nvservicestatus.material_id')
                ->whereIn('tbl_material.nv_id', $BRPLnv)
                ->where(function ($query) {
                    $query->where('nvservicestatus.work_rew3dep2_status', 2);
                })
                ->sum('tbl_material.total_budget_both')+DB::table('tbl_service')
                ->join('nvservicestatus', 'tbl_service.id', '=', 'nvservicestatus.service_id')
                ->whereIn('tbl_service.nv_id', $BRPLnv)
                ->where(function ($query) {
                    $query->where('nvservicestatus.work_rew3dep2_status', 2);
                })
                ->sum('tbl_service.total_buget');

              $approvedAmountBRPL = DB::table('tbl_material')
                ->join('nvservicestatus', 'tbl_material.id', '=', 'nvservicestatus.material_id')
                ->whereIn('tbl_material.nv_id', $BRPLnv)->where('nvservicestatus.ceo_status', 1)
                ->sum('tbl_material.total_budget_both')+DB::table('tbl_service')
                ->join('nvservicestatus', 'tbl_service.id', '=', 'nvservicestatus.service_id')
                ->whereIn('tbl_service.nv_id', $BRPLnv)->where('nvservicestatus.ceo_status', 1)
                ->sum('tbl_service.total_buget');

                $fileDataBRPL =Nvsericestatus::
                select(
                    DB::raw('MONTH(created_at) as month'),
                    DB::raw('SUM(CASE WHEN ceo_status = "1" THEN 1 ELSE 0 END) as approved_count'),
                    DB::raw('SUM(CASE WHEN ( work_rew3dep2_status = "2")  THEN 1 ELSE 0 END ) as rejected_count'),
                    DB::raw('SUM(CASE WHEN (check_technology="1" AND work_rew1dep2_status = "0" AND work_rew2dep2_status = "0" AND work_rew3dep2_status = "0" AND work_rew4dep2_status = "0" AND approverdep2_status = "0") THEN 1 ELSE 0 END) as pending_count'),
                 
                )
         
                ->whereIn('nv_id', $BRPLnv)->with(['service', 'material', 'user'])
                ->where(function ($query) {
                    $query->where('check_technology', 1);
                })->where(function ($query) {
                    $query->where('approver_status', 1);
                })
              
                ->whereYear('created_at', Carbon::now()->year)
                ->groupBy('month')
                ->orderBy('month')
                ->get();
     
            $BRPLlabels = [];
            $BRPLapprovedData = [];
            $BRPLrejectedData = [];
            $BRPLpendingData = [];
        
            foreach ($fileDataBRPL as $dataPointBRPL) {
                $monthBRPL = Carbon::createFromFormat('!m', $dataPointBRPL->month)->format('F');
        
                $BRPLlabels[] = $monthBRPL;
                $BRPLapprovedData[] = $dataPointBRPL->approved_count;
                $BRPLrejectedData[] = $dataPointBRPL->rejected_count;
                $BRPLpendingData[] = $dataPointBRPL->pending_count;
                
            }
    
            if($fiscal_year){
                $BYPLnv = NeedValidation::where('company_id','5')->where('fiscal_year', $fiscal_year)->pluck("id");
            }else{
  
                $BYPLnv = NeedValidation::where('company_id','5')->where('fiscal_year', $currentFinancialYear)->pluck("id");
            }
            
            $pendingAmountBYPL = DB::table('tbl_material')
            ->join('nvservicestatus', 'tbl_material.id', '=', 'nvservicestatus.material_id')
            ->whereIn('tbl_material.nv_id', $BYPLnv)
            ->whereIn('nvservicestatus.nv_id', $pen_amt->pluck('nv_id'))
       
            ->sum('tbl_material.total_budget_both')+DB::table('tbl_service')
            ->join('nvservicestatus', 'tbl_service.id', '=', 'nvservicestatus.service_id')
            ->whereIn('tbl_service.nv_id', $BYPLnv)
            ->whereIn('nvservicestatus.nv_id', $pen_amt->pluck('nv_id'))
       
            ->sum('tbl_service.total_buget');
    
            $rejectedAmountBYPL = DB::table('tbl_material')
            ->join('nvservicestatus', 'tbl_material.id', '=', 'nvservicestatus.material_id')
            ->whereIn('tbl_material.nv_id', $BYPLnv)
            ->where(function ($query) {
                $query->where('nvservicestatus.work_rew3dep2_status', 2);
            })
            ->sum('tbl_material.total_budget_both')+DB::table('tbl_service')
            ->join('nvservicestatus', 'tbl_service.id', '=', 'nvservicestatus.service_id')
            ->whereIn('tbl_service.nv_id', $BYPLnv)
            ->where(function ($query) {
                $query->where('nvservicestatus.work_rew3dep2_status', 2);
            })
            ->sum('tbl_service.total_buget');

          $approvedAmountBYPL = DB::table('tbl_material')
            ->join('nvservicestatus', 'tbl_material.id', '=', 'nvservicestatus.material_id')
            ->whereIn('tbl_material.nv_id', $BYPLnv)->where('nvservicestatus.ceo_status', 1)
            ->sum('tbl_material.total_budget_both')+DB::table('tbl_service')
            ->join('nvservicestatus', 'tbl_service.id', '=', 'nvservicestatus.service_id')
            ->whereIn('tbl_service.nv_id', $BYPLnv)->where('nvservicestatus.ceo_status', 1)
            ->sum('tbl_service.total_buget');
             $fileDataBYPL =Nvsericestatus::
            select(
                DB::raw('MONTH(created_at) as month'),
                DB::raw('SUM(CASE WHEN ceo_status = "1" THEN 1 ELSE 0 END) as approved_count'),
                DB::raw('SUM(CASE WHEN ( work_rew3dep2_status = "2")  THEN 1 ELSE 0 END) as rejected_count'),
                DB::raw('SUM(CASE WHEN (check_technology="1" AND work_rew1dep2_status = "0" AND work_rew2dep2_status = "0" AND work_rew3dep2_status = "0" AND work_rew4dep2_status = "0" AND approverdep2_status = "0") THEN 1 ELSE 0 END) as pending_count'),
               
            ) 
            ->whereIn('nv_id', $BYPLnv)->with(['service', 'material', 'user'])
            ->where(function ($query) {
                $query->where('check_technology', 1);
            })->where(function ($query) {
                $query->where('approver_status', 1);
            })
            ->whereYear('created_at', Carbon::now()->year)
            ->groupBy('month')
            ->orderBy('month')
            ->get();
           
        $BYPLlabels = [];
        $BYPLapprovedData = [];
        $BYPLrejectedData = [];
        $BYPLpendingData = [];
    
        foreach ($fileDataBYPL as $dataPointBYPL) {
            $monthBYPL = Carbon::createFromFormat('!m', $dataPointBYPL->month)->format('F');
    
            $BYPLlabels[] = $monthBYPL;
            $BYPLapprovedData[] = $dataPointBYPL->approved_count;
            $BYPLrejectedData[] = $dataPointBYPL->rejected_count;
            $BYPLpendingData[] = $dataPointBYPL->pending_count;
           
        }
                return view("admin.dashboard", compact("approvedAmount","currentFinancialYear","nextFinancialYear","nextToNextFinancialYear", "rejectedAmount", "pendingAmount", "totalAmount","company", "company_id" , "nv_sm_data", "totalNV", "approvedNV", "rejectedNV", "pendingNV" , "hodApproval", "cpmgApproval", "btApproval", "ceoApproval"             ,'BRPLlabels','BRPLapprovedData','BRPLrejectedData','BRPLpendingData','BYPLlabels','BYPLapprovedData','BYPLrejectedData','BYPLpendingData','cesApproval','ceonominee1Approval','ceonominee2Approval','approvedAmountBYPL','approvedAmountBRPL','pendingAmountBYPL','pendingAmountBRPL','rejectedAmountBYPL','rejectedAmountBRPL','groupcioApproval'));
            } elseif ($id2->work_rew4 == $user->id) {
                $Values = [$user->id, $id2->work_rew1, $id2->work_rew2, $id2->work_rew3];
              
                
                if($fiscal_year){
                    $totalId = NeedValidation::where('fiscal_year', $fiscal_year)->pluck('id');
                }else{
                   $totalId = NeedValidation::where('fiscal_year', $currentFinancialYear)->pluck('id');
                  
                }
              
                $totalAmount = DB::table('tbl_material')
                ->join('nvservicestatus', 'tbl_material.id', '=', 'nvservicestatus.material_id')
                
                 ->whereIn('tbl_material.nv_id', $totalId)
                 ->whereIn('nvservicestatus.nv_id', $latestData->pluck('nv_id'))
                ->where('nvservicestatus.work_rew4dep2_status', 1)
                ->sum('tbl_material.total_budget_both') + DB::table('tbl_service')
                ->join('nvservicestatus', 'tbl_service.id', '=', 'nvservicestatus.service_id')
                
                 ->whereIn('tbl_service.nv_id', $totalId)
                 ->whereIn('nvservicestatus.nv_id', $latestData->pluck('nv_id'))
                ->where('nvservicestatus.work_rew4dep2_status', 1)
                ->sum('tbl_service.total_buget');
              
                $pen_amt = $latestData->filter(function ($data) {

                    return in_array($data->approver_status, [1]) &&
                        in_array($data->check_technology, [1]) &&
                        in_array($data->work_rew1dep2_status, [0])&&
                        in_array($data->work_rew2dep2_status, [0])&&
                        in_array($data->work_rew3dep2_status, [0])&&
                        in_array($data->work_rew4dep2_status, [0])&&
                        in_array($data->approverdep2_status, [0]);
                });
                $pendingAmount = DB::table('tbl_material')
                ->join('nvservicestatus', 'tbl_material.id', '=', 'nvservicestatus.material_id')
                
                 ->whereIn('tbl_material.nv_id', $totalId)
                 ->whereIn('nvservicestatus.nv_id', $pen_amt->pluck('nv_id'))
                         
                ->sum('tbl_material.total_budget_both') + DB::table('tbl_service')
                ->join('nvservicestatus', 'tbl_service.id', '=', 'nvservicestatus.service_id')
                
                 ->whereIn('tbl_service.nv_id', $totalId)
                 ->whereIn('nvservicestatus.nv_id', $pen_amt->pluck('nv_id'))
                         
                ->sum('tbl_service.total_buget');
        
                $rejectedAmount = DB::table('tbl_material')
                ->join('nvservicestatus', 'tbl_material.id', '=', 'nvservicestatus.material_id')
                ->whereIn('nvservicestatus.nv_id', $latestData->pluck('nv_id'))
                ->where(function ($query) {
                    $query->where('nvservicestatus.work_rew4dep2_status', 2);
                })
                ->sum('tbl_material.total_budget_both') + DB::table('tbl_service')
                ->join('nvservicestatus', 'tbl_service.id', '=', 'nvservicestatus.service_id')
                ->whereIn('nvservicestatus.nv_id', $latestData->pluck('nv_id'))
                ->where(function ($query) {
                    $query->where('nvservicestatus.work_rew4dep2_status', 2);
                })
                ->sum('tbl_service.total_buget');
           
        
                $approvedAmount = DB::table('tbl_material')
                
                 ->whereIn('tbl_material.nv_id', $totalId)
                 ->whereIn('nvservicestatus.nv_id', $latestData->pluck('nv_id'))
                ->join('nvservicestatus', 'tbl_material.id', '=', 'nvservicestatus.material_id')
                ->where('nvservicestatus.ceo_status', 1)
                ->sum('tbl_material.total_budget_both') + DB::table('tbl_service')
               
                ->whereIn('tbl_service.nv_id', $totalId)
                ->whereIn('nvservicestatus.nv_id', $latestData->pluck('nv_id'))
               ->join('nvservicestatus', 'tbl_service.id', '=', 'nvservicestatus.service_id')
               ->where('nvservicestatus.ceo_status', 1)
               ->sum('tbl_service.total_buget');

                if($company_id){
                    $totalNV = Nvsericestatus::whereIn("nv_id", $totalId)->where('work_rew4dep2_status', 1)->where('company_id',$company_id)->count();
                } else {
                    $totalNV = Nvsericestatus::whereIn("nv_id", $totalId)->where('work_rew4dep2_status', 1)->count();
                }
                if($company_id){
                    $rejectedNV = Nvsericestatus::whereIn("nv_id", $totalId)->where('work_rew4dep2_status', 2)->where('company_id',$company_id)->count();
                } else {
                    $rejectedNV = Nvsericestatus::whereIn("nv_id", $totalId)->where('work_rew4dep2_status', 2)->count();
                }
              
                if($company_id){
                $latestData = Nvsericestatus::whereIn("nv_id", $totalId)->where('company_id',$company_id)->get();
            }else {
                $latestData = Nvsericestatus::whereIn("nv_id", $totalId)->get();
            }
                if($company_id){
                    $approvedNV = $latestData->where('ceo_status', 1)->where('company_id',$company_id)->count();
                }else{
                    $approvedNV = $latestData->where('ceo_status', 1)->count();
                }
             
                $pendingNV = $latestData->filter(function ($data) {

                    return in_array($data->approver_status, [1]) &&
                        in_array($data->check_technology, [1]) &&
                        in_array($data->work_rew1dep2_status, [0])&&
                        in_array($data->work_rew2dep2_status, [0])&&
                        in_array($data->work_rew3dep2_status, [0])&&
                        in_array($data->work_rew4dep2_status, [0])&&
                        in_array($data->approverdep2_status, [0]);
                    })->count();
              
                $nvIds = NeedValidation::where("user_id", $Values)->pluck("id");
               
                if($fiscal_year){
                    $nv = NeedValidation::whereHas('service')->select('id')->where('fiscal_year', $fiscal_year)
                
              
                    ->get();
                    }else{
                        $nv = NeedValidation::whereHas('service')->select('id')->where('fiscal_year', $currentFinancialYear)
                            
                        
                        ->get();
                        
                    }
                $nv_ids = $nv->pluck('id');
            
           $nv_sm_data = Nvsericestatus::whereIn('nv_id', $nv_ids)->with(['service', 'material', 'user'])
                    ->where(function ($query) {
                        $query->where('check_technology', 1);
                    })->where(function ($query) {
                        $query->where('approver_status', 1);
                    })
           
                ->orderBy('id', 'asc')->get();
              
                $total16_status = $request->total;
                $pending16_status = $request->pending;
                $reject16_status = $request->rejected;
                 if ($ceo_status !== null) {
                    $nv_sm_data = $nv_sm_data->where('ceo_status', $ceo_status);
                } elseif ($pending16_status !== null) {
                    $nv_sm_data = $nv_sm_data->where('work_rew4dep2_status', $pending16_status)
                    ->where('work_rew1dep2_status',0)
                    ->where('work_rew2dep2_status',0)
                    ->where('work_rew3dep2_status',0)
                    ->where('hod_status', 1);
                      
                } elseif ($reject16_status !== null) {
                    $nv_sm_data = $nv_sm_data->where('work_rew4dep2_status', $reject16_status);
                    
                }elseif ($total16_status !== null) {
                    $nv_sm_data = $nv_sm_data->where('work_rew4dep2_status', $total16_status);
                      
                }
                if($fiscal_year){
                    $BRPLnv = NeedValidation::where('company_id','6')->where('fiscal_year', $fiscal_year)->pluck("id");
                }else{
                    $BRPLnv = NeedValidation::where('company_id','6')->where('fiscal_year', $currentFinancialYear)->pluck("id");
                  
                }

                

                $pendingAmountBRPL = DB::table('tbl_material')
                ->join('nvservicestatus', 'tbl_material.id', '=', 'nvservicestatus.material_id')
                ->whereIn('tbl_material.nv_id', $BRPLnv)
                ->whereIn('nvservicestatus.nv_id', $pen_amt->pluck('nv_id'))
              
                ->sum('tbl_material.total_budget_both')+DB::table('tbl_service')
                ->join('nvservicestatus', 'tbl_service.id', '=', 'nvservicestatus.service_id')
                ->whereIn('tbl_service.nv_id', $BRPLnv)
                ->whereIn('nvservicestatus.nv_id', $pen_amt->pluck('nv_id'))
              
                ->sum('tbl_service.total_buget');
        
                $rejectedAmountBRPL = DB::table('tbl_material')
                ->join('nvservicestatus', 'tbl_material.id', '=', 'nvservicestatus.material_id')
                ->whereIn('tbl_material.nv_id', $BRPLnv)
                ->where(function ($query) {
                    $query->where('nvservicestatus.work_rew4dep2_status', 2);
                })
                ->sum('tbl_material.total_budget_both')+DB::table('tbl_service')
                ->join('nvservicestatus', 'tbl_service.id', '=', 'nvservicestatus.service_id')
                ->whereIn('tbl_service.nv_id', $BRPLnv)
                ->where(function ($query) {
                    $query->where('nvservicestatus.work_rew4dep2_status', 2);
                })
                ->sum('tbl_service.total_buget');

              $approvedAmountBRPL = DB::table('tbl_material')
                ->join('nvservicestatus', 'tbl_material.id', '=', 'nvservicestatus.material_id')
                ->whereIn('tbl_material.nv_id', $BRPLnv)->where('nvservicestatus.ceo_status', 1)
                ->sum('tbl_material.total_budget_both')+DB::table('tbl_service')
                ->join('nvservicestatus', 'tbl_service.id', '=', 'nvservicestatus.service_id')
                ->whereIn('tbl_service.nv_id', $BRPLnv)->where('nvservicestatus.ceo_status', 1)
                ->sum('tbl_service.total_buget');

                $fileDataBRPL =Nvsericestatus::
                select(
                    DB::raw('MONTH(created_at) as month'),
                    DB::raw('SUM(CASE WHEN ceo_status = "1" THEN 1 ELSE 0 END) as approved_count'),
                    DB::raw('SUM(CASE WHEN ( work_rew4dep2_status = "2")  THEN 1 ELSE 0 END ) as rejected_count'),
                    DB::raw('SUM(CASE WHEN (check_technology="1" AND work_rew1dep2_status = "0" AND work_rew2dep2_status = "0" AND work_rew3dep2_status = "0" AND work_rew4dep2_status = "0" AND approverdep2_status = "0") THEN 1 ELSE 0 END) as pending_count'),
                
                )
          
                ->whereIn('nv_id', $BRPLnv)->with(['service', 'material', 'user'])
                ->where(function ($query) {
                    $query->where('check_technology', 1);
                })->where(function ($query) {
                    $query->where('approver_status', 1);
                })
             
                ->whereYear('created_at', Carbon::now()->year)
                ->groupBy('month')
                ->orderBy('month')
                ->get();
    
            $BRPLlabels = [];
            $BRPLapprovedData = [];
            $BRPLrejectedData = [];
            $BRPLpendingData = [];
        
            foreach ($fileDataBRPL as $dataPointBRPL) {
                $monthBRPL = Carbon::createFromFormat('!m', $dataPointBRPL->month)->format('F');
        
                $BRPLlabels[] = $monthBRPL;
                $BRPLapprovedData[] = $dataPointBRPL->approved_count;
                $BRPLrejectedData[] = $dataPointBRPL->rejected_count;
                $BRPLpendingData[] = $dataPointBRPL->pending_count;
                 
            }
            if($fiscal_year){
           $BYPLnv = NeedValidation::where('company_id','5')->where('fiscal_year', $fiscal_year)->pluck("id");
            }else{
   $BYPLnv = NeedValidation::where('company_id','5')->where('fiscal_year', $currentFinancialYear)->pluck("id");
              
            }
           
            $pendingAmountBYPL = DB::table('tbl_material')
            ->join('nvservicestatus', 'tbl_material.id', '=', 'nvservicestatus.material_id')
            ->whereIn('tbl_material.nv_id', $BYPLnv)
            ->whereIn('nvservicestatus.nv_id', $pen_amt->pluck('nv_id'))
           
            ->sum('tbl_material.total_budget_both')+DB::table('tbl_service')
            ->join('nvservicestatus', 'tbl_service.id', '=', 'nvservicestatus.service_id')
            ->whereIn('tbl_service.nv_id', $BYPLnv)
            ->whereIn('nvservicestatus.nv_id', $pen_amt->pluck('nv_id'))
           
            ->sum('tbl_service.total_buget');
    
            $rejectedAmountBYPL = DB::table('tbl_material')
            ->join('nvservicestatus', 'tbl_material.id', '=', 'nvservicestatus.material_id')
            ->whereIn('tbl_material.nv_id', $BYPLnv)
            ->where(function ($query) {
                $query->where('nvservicestatus.work_rew4dep2_status', 2);
            })
            ->sum('tbl_material.total_budget_both')+DB::table('tbl_service')
            ->join('nvservicestatus', 'tbl_service.id', '=', 'nvservicestatus.service_id')
            ->whereIn('tbl_service.nv_id', $BYPLnv)
            ->where(function ($query) {
                $query->where('nvservicestatus.work_rew4dep2_status', 2);
            })
            ->sum('tbl_service.total_buget');

          $approvedAmountBYPL = DB::table('tbl_material')
            ->join('nvservicestatus', 'tbl_material.id', '=', 'nvservicestatus.material_id')
            ->whereIn('tbl_material.nv_id', $BYPLnv)->where('nvservicestatus.ceo_status', 1)
            ->sum('tbl_material.total_budget_both')+DB::table('tbl_service')
            ->join('nvservicestatus', 'tbl_service.id', '=', 'nvservicestatus.service_id')
            ->whereIn('tbl_service.nv_id', $BYPLnv)->where('nvservicestatus.ceo_status', 1)
            ->sum('tbl_service.total_buget');
             $fileDataBYPL =Nvsericestatus::
            select(
                DB::raw('MONTH(created_at) as month'),
                DB::raw('SUM(CASE WHEN ceo_status = "1" THEN 1 ELSE 0 END) as approved_count'),
                DB::raw('SUM(CASE WHEN ( work_rew4dep2_status = "2")  THEN 1 ELSE 0 END) as rejected_count'),
                DB::raw('SUM(CASE WHEN (check_technology="1" AND work_rew1dep2_status = "0" AND work_rew2dep2_status = "0" AND work_rew3dep2_status = "0" AND work_rew4dep2_status = "0" AND approverdep2_status = "0") THEN 1 ELSE 0 END) as pending_count'),
               
            ) 
            ->whereIn('nv_id', $BYPLnv)->with(['service', 'material', 'user'])
            ->where(function ($query) {
                $query->where('check_technology', 1);
            })->where(function ($query) {
                $query->where('approver_status', 1);
            })
          
            ->whereYear('created_at', Carbon::now()->year)
            ->groupBy('month')
            ->orderBy('month')
            ->get();
          
        $BYPLlabels = [];
        $BYPLapprovedData = [];
        $BYPLrejectedData = [];
        $BYPLpendingData = [];
    
        foreach ($fileDataBYPL as $dataPointBYPL) {
            $monthBYPL = Carbon::createFromFormat('!m', $dataPointBYPL->month)->format('F');
    
            $BYPLlabels[] = $monthBYPL;
            $BYPLapprovedData[] = $dataPointBYPL->approved_count;
            $BYPLrejectedData[] = $dataPointBYPL->rejected_count;
            $BYPLpendingData[] = $dataPointBYPL->pending_count;
      
        }
                return view("admin.dashboard", compact("approvedAmount","currentFinancialYear","nextFinancialYear","nextToNextFinancialYear", "rejectedAmount", "pendingAmount", "totalAmount","company", "company_id" , "nv_sm_data", "totalNV", "approvedNV", "rejectedNV", "pendingNV" , "hodApproval", "cpmgApproval", "btApproval", "ceoApproval"             ,'BRPLlabels','BRPLapprovedData','BRPLrejectedData','BRPLpendingData','BYPLlabels','BYPLapprovedData','BYPLrejectedData','BYPLpendingData','cesApproval','ceonominee1Approval','ceonominee2Approval','approvedAmountBYPL','approvedAmountBRPL','pendingAmountBYPL','pendingAmountBRPL','rejectedAmountBYPL','rejectedAmountBRPL','groupcioApproval'));
            } elseif ($id2->approver == $user->id) {
                $Values = [$user->id, $id2->work_rew1, $id2->work_rew2, $id2->work_rew3, $id2->work_rew4];
               
                if($fiscal_year){
                    $totalId = NeedValidation::where('fiscal_year', $fiscal_year)->pluck('id');
                }else{
       $totalId = NeedValidation::where('fiscal_year', $currentFinancialYear)->pluck('id');
                  
                }
       
                $totalAmount = DB::table('tbl_material')
                ->join('nvservicestatus', 'tbl_material.id', '=', 'nvservicestatus.material_id')
              
                 ->whereIn('tbl_material.nv_id', $totalId)
                 ->whereIn('nvservicestatus.nv_id', $latestData->pluck('nv_id'))
                ->where('nvservicestatus.cto_status', 1)
                ->sum('tbl_material.total_budget_both') + DB::table('tbl_service')
                ->join('nvservicestatus', 'tbl_service.id', '=', 'nvservicestatus.service_id')
               
                 ->whereIn('tbl_service.nv_id', $totalId)
                 ->whereIn('nvservicestatus.nv_id', $latestData->pluck('nv_id'))
                ->where('nvservicestatus.cto_status', 1)
                ->sum('tbl_service.total_buget');

                if(!empty($id2->work_rew1) || !empty($id2->work_rew2)|| !empty($id2->work_rew3)|| !empty($id2->work_rew4)){
                $pen_amt = $latestData->filter(function ($data) {
                    return in_array($data->approver_status, [1]) &&
                       ( in_array($data->work_rew1dep2_status, [1])||in_array($data->work_rew2dep2_status, [1])||in_array($data->work_rew3dep2_status, [1])||in_array($data->work_rew4dep2_status, [1])) &&
                        in_array($data->check_technology, [1]) &&
                        in_array($data->cto_status, [0]);
                });
            }elseif(empty($id2->work_rew1) && empty($id2->work_rew2) && empty($id2->work_rew3) && empty($id2->work_rew4)){
                $pen_amt = $latestData->filter(function ($data) {
    
                    return in_array($data->approver_status, [1]) &&
                        in_array($data->check_technology, [1]) &&
                        in_array($data->cto_status, [0]) &&
                        in_array($data->work_rew1dep2_status, [0])&&
                        in_array($data->work_rew2dep2_status, [0])&&
                        in_array($data->work_rew3dep2_status, [0])&&
                        in_array($data->work_rew4dep2_status, [0]);
                });
            }
                $pendingAmount = DB::table('tbl_material')
                ->join('nvservicestatus', 'tbl_material.id', '=', 'nvservicestatus.material_id')
                 
                 ->whereIn('tbl_material.nv_id', $totalId)
                 ->whereIn('nvservicestatus.nv_id', $pen_amt->pluck('nv_id'))
               
                ->sum('tbl_material.total_budget_both') + DB::table('tbl_service')
                ->join('nvservicestatus', 'tbl_service.id', '=', 'nvservicestatus.service_id')
                 
                 ->whereIn('tbl_service.nv_id', $totalId)
                 ->whereIn('nvservicestatus.nv_id', $pen_amt->pluck('nv_id'))
               
                ->sum('tbl_service.total_buget');
        
                $rejectedAmount = DB::table('tbl_material')
                ->join('nvservicestatus', 'tbl_material.id', '=', 'nvservicestatus.material_id')
                ->whereIn('nvservicestatus.nv_id', $latestData->pluck('nv_id'))
                ->where(function ($query) {
                    $query->where('nvservicestatus.cto_status', 2);
                })
                ->sum('tbl_material.total_budget_both') + DB::table('tbl_service')
                ->join('nvservicestatus', 'tbl_service.id', '=', 'nvservicestatus.service_id')
                ->whereIn('nvservicestatus.nv_id', $latestData->pluck('nv_id'))
                ->where(function ($query) {
                    $query->where('nvservicestatus.cto_status', 2);
                })
                ->sum('tbl_service.total_buget');
           
        
                $approvedAmount = DB::table('tbl_material')
                
                 ->whereIn('tbl_material.nv_id', $totalId)
                 ->whereIn('nvservicestatus.nv_id', $latestData->pluck('nv_id'))
                ->join('nvservicestatus', 'tbl_material.id', '=', 'nvservicestatus.material_id')
                ->where('nvservicestatus.ceo_status', 1)
                ->sum('tbl_material.total_budget_both') + DB::table('tbl_service')
               
                ->whereIn('tbl_service.nv_id', $totalId)
                ->whereIn('nvservicestatus.nv_id', $latestData->pluck('nv_id'))
               ->join('nvservicestatus', 'tbl_service.id', '=', 'nvservicestatus.service_id')
               ->where('nvservicestatus.ceo_status', 1)
               ->sum('tbl_service.total_buget');

                if($company_id){
                    $totalNV = Nvsericestatus::whereIn("nv_id", $totalId)->where('cto_status', 1)->where('company_id',$company_id)->count();
                } else {
                    $totalNV = Nvsericestatus::whereIn("nv_id", $totalId)->where('cto_status', 1)->count();
                }
                if($company_id){
                    $rejectedNV = Nvsericestatus::whereIn("nv_id", $totalId)->where('cto_status', 2)->where('company_id',$company_id)->count();
                } else {
                    $rejectedNV = Nvsericestatus::whereIn("nv_id", $totalId)->where('cto_status', 2)->count();
                }
              
                if($company_id){
                $latestData = Nvsericestatus::whereIn("nv_id", $totalId)->where('company_id',$company_id)->get();
            }else {
                $latestData = Nvsericestatus::whereIn("nv_id", $totalId)->get();
            }
                if($company_id){
                    $approvedNV = $latestData->where('ceo_status', 1)->where('company_id',$company_id)->count();
                }else{
                    $approvedNV = $latestData->where('ceo_status', 1)->count();
                }
              
                if(!empty($id2->work_rew1) || !empty($id2->work_rew2)|| !empty($id2->work_rew3)|| !empty($id2->work_rew4)){
                    $pendingNV = $latestData->filter(function ($data) {
                        return in_array($data->approver_status, [1]) &&
                           ( in_array($data->work_rew1dep2_status, [1])||in_array($data->work_rew2dep2_status, [1])||in_array($data->work_rew3dep2_status, [1])||in_array($data->work_rew4dep2_status, [1])) &&
                            in_array($data->check_technology, [1]) &&
                            in_array($data->cto_status, [0]);
                    })->count();
                }elseif(empty($id2->work_rew1) && empty($id2->work_rew2) && empty($id2->work_rew3) && empty($id2->work_rew4)){
                    $pendingNV = $latestData->filter(function ($data) {
    
                        return in_array($data->approver_status, [1]) &&
                            in_array($data->check_technology, [1]) &&
                            in_array($data->cto_status, [0]) &&
                            in_array($data->work_rew1dep2_status, [0])&&
                            in_array($data->work_rew2dep2_status, [0])&&
                            in_array($data->work_rew3dep2_status, [0])&&
                            in_array($data->work_rew4dep2_status, [0]);
                    })->count();
                }

                $nvIds = NeedValidation::where("user_id", $Values)->pluck("id");
               

                if($fiscal_year){
                    $nv = NeedValidation::whereHas('service')->select('id')->where('fiscal_year', $fiscal_year)
               
                    ->get();
                }else{
      
                   $nv = NeedValidation::whereHas('service')->select('id')->where('fiscal_year', $currentFinancialYear)
               
                ->get();
                }
                $nv_ids = $nv->pluck('id');


                if(!empty($id2->work_rew1) || !empty($id2->work_rew2)|| !empty($id2->work_rew3)|| !empty($id2->work_rew4)){
                    $nv_sm_data = Nvsericestatus::whereIn('nv_id', $nv_ids)->with(['service', 'material', 'user'])
                    ->where(function ($query) {
                        $query->orWhere('work_rew1dep2_status', 1)
                            ->orWhere('work_rew2dep2_status', 1)
                            ->orWhere('work_rew3dep2_status', 1)
                            ->orWhere('work_rew4dep2_status', 1);
                    })
                        ->where(function ($query) {
                            $query->where('work_rew1dep2_status', '!=', 2)
                                ->where('work_rew2dep2_status', '!=', 2)
                                ->where('work_rew3dep2_status', '!=', 2)
                                ->where('work_rew4dep2_status', '!=', 2);
                        })
                        ->where(function ($query) {
                            $query->where('check_technology', 1);
                        })
                        ->orderBy('id', 'asc')->get();
                    }elseif(empty($id2->work_rew1) && empty($id2->work_rew2) && empty($id2->work_rew3) && empty($id2->work_rew4)){
                        $nv_sm_data = Nvsericestatus::whereIn('nv_id', $nv_ids)->with(['service', 'material', 'user'])
                    ->where(function ($query) {
                        $query->where('check_technology', 1);
                    })->where(function ($query) {
                        $query->where('approver_status', 1);
                    })
                            ->orderBy('id', 'asc')->get();
                    }
            
                $total17_status = $request->total;
                $pending17_status = $request->pending;
                $reject17_status = $request->rejected;

              if ($ceo_status !== null) {
                    $nv_sm_data = $nv_sm_data->where('ceo_status', $ceo_status);
                } elseif ($pending17_status !== null) {
                    $nv_sm_data = $nv_sm_data->where('cto_status', $pending17_status)
                    ->where('approver_status', 1);
                     
                } elseif ($reject17_status !== null) {
                    $nv_sm_data = $nv_sm_data->where('cto_status', $reject17_status);
                    
                }elseif ($total17_status !== null) {
                    $nv_sm_data = $nv_sm_data->where('cto_status', $total17_status);
                      
                }
   
                if($fiscal_year){
                    $BRPLnv = NeedValidation::where('company_id','6')->where('fiscal_year', $fiscal_year)->pluck("id");
                }else{
                    $BRPLnv = NeedValidation::where('company_id','6')->where('fiscal_year', $currentFinancialYear)->pluck("id");
                  
                }
               
                $pendingAmountBRPL = DB::table('tbl_material')
                ->join('nvservicestatus', 'tbl_material.id', '=', 'nvservicestatus.material_id')
                ->whereIn('tbl_material.nv_id', $BRPLnv)
                ->whereIn('nvservicestatus.nv_id', $pen_amt->pluck('nv_id'))
              
                ->sum('tbl_material.total_budget_both')+DB::table('tbl_service')
                ->join('nvservicestatus', 'tbl_service.id', '=', 'nvservicestatus.service_id')
                ->whereIn('tbl_service.nv_id', $BRPLnv)
                ->whereIn('nvservicestatus.nv_id', $pen_amt->pluck('nv_id'))
               
                ->sum('tbl_service.total_buget');
        
                $rejectedAmountBRPL = DB::table('tbl_material')
                ->join('nvservicestatus', 'tbl_material.id', '=', 'nvservicestatus.material_id')
                ->whereIn('tbl_material.nv_id', $BRPLnv)
                ->where(function ($query) {
                    $query->where('nvservicestatus.cto_status', 2);
                })
                ->sum('tbl_material.total_budget_both')+DB::table('tbl_service')
                ->join('nvservicestatus', 'tbl_service.id', '=', 'nvservicestatus.service_id')
                ->whereIn('tbl_service.nv_id', $BRPLnv)
                ->where(function ($query) {
                    $query->where('nvservicestatus.cto_status', 2);
                })
                ->sum('tbl_service.total_buget');
           
        
                $approvedAmountBRPL = DB::table('tbl_material')
                ->join('nvservicestatus', 'tbl_material.id', '=', 'nvservicestatus.material_id')
                ->whereIn('tbl_material.nv_id', $BRPLnv)->where('nvservicestatus.ceo_status', 1)
                ->sum('tbl_material.total_budget_both')+DB::table('tbl_service')
                ->join('nvservicestatus', 'tbl_service.id', '=', 'nvservicestatus.service_id')
                ->whereIn('tbl_service.nv_id', $BRPLnv)->where('nvservicestatus.ceo_status', 1)
                ->sum('tbl_service.total_buget');
                 
                if(!empty($id2->work_rew1) || !empty($id2->work_rew2)|| !empty($id2->work_rew3)|| !empty($id2->work_rew4)){
                   
                $fileDataBRPL =Nvsericestatus::
                select(
                    DB::raw('MONTH(created_at) as month'),
                    DB::raw('SUM(CASE WHEN ceo_status = "1" THEN 1 ELSE 0 END) as approved_count'),
                    DB::raw('SUM(CASE WHEN ( cto_status = "2")  THEN 1 ELSE 0 END ) as rejected_count'),
                    DB::raw('SUM(CASE WHEN (cto_status = "0" AND (work_rew1dep2_status = "1" OR work_rew2dep2_status = "1" OR work_rew3dep2_status = "1" OR work_rew4dep2_status = "1")) THEN 1 ELSE 0 END) as pending_count'),
                  
                )
          
                ->whereIn('nv_id', $BRPLnv)->with(['service', 'material', 'user'])
                ->where('approver_status',1)
               
                ->whereYear('created_at', Carbon::now()->year)
                ->groupBy('month')
                ->orderBy('month')
                ->get();
                    }elseif(empty($id2->work_rew1) && empty($id2->work_rew2) && empty($id2->work_rew3) && empty($id2->work_rew4)){
                       
                $fileDataBRPL =Nvsericestatus::
                select(
                    DB::raw('MONTH(created_at) as month'),
                    DB::raw('SUM(CASE WHEN ceo_status = "1" THEN 1 ELSE 0 END) as approved_count'),
                    DB::raw('SUM(CASE WHEN ( cto_status = "2")  THEN 1 ELSE 0 END ) as rejected_count'),
                    DB::raw('SUM(CASE WHEN (check_technology="1" AND cto_status = "0" AND work_rew1dep2_status = "0" AND work_rew2dep2_status = "0" AND work_rew3dep2_status = "0" AND work_rew4dep2_status = "0") THEN 1 ELSE 0 END) as pending_count'),
                  
                )
          
                ->whereIn('nv_id', $BRPLnv)->with(['service', 'material', 'user'])
                ->where('approver_status',1)
               
                ->whereYear('created_at', Carbon::now()->year)
                ->groupBy('month')
                ->orderBy('month')
                ->get();
                    }
              
       
            $BRPLlabels = [];
            $BRPLapprovedData = [];
            $BRPLrejectedData = [];
            $BRPLpendingData = [];
        
            foreach ($fileDataBRPL as $dataPointBRPL) {
                $monthBRPL = Carbon::createFromFormat('!m', $dataPointBRPL->month)->format('F');
        
                $BRPLlabels[] = $monthBRPL;
                $BRPLapprovedData[] = $dataPointBRPL->approved_count;
                $BRPLrejectedData[] = $dataPointBRPL->rejected_count;
                $BRPLpendingData[] = $dataPointBRPL->pending_count;
                
            }
    
            if($fiscal_year){
                $BYPLnv = NeedValidation::where('company_id','5')->where('fiscal_year', $fiscal_year)->pluck("id");
          }else{
            $BYPLnv = NeedValidation::where('company_id','5')->where('fiscal_year', $currentFinancialYear)->pluck("id");
            
          }
           
            $pendingAmountBYPL = DB::table('tbl_material')
            ->join('nvservicestatus', 'tbl_material.id', '=', 'nvservicestatus.material_id')
            ->whereIn('tbl_material.nv_id', $BYPLnv)
            ->whereIn('nvservicestatus.nv_id', $pen_amt->pluck('nv_id'))
          
            ->sum('tbl_material.total_budget_both')+DB::table('tbl_service')
            ->join('nvservicestatus', 'tbl_service.id', '=', 'nvservicestatus.service_id')
            ->whereIn('tbl_service.nv_id', $BYPLnv)
            ->whereIn('nvservicestatus.nv_id', $pen_amt->pluck('nv_id'))
          
            ->sum('tbl_service.total_buget');
    
            $rejectedAmountBYPL = DB::table('tbl_material')
            ->join('nvservicestatus', 'tbl_material.id', '=', 'nvservicestatus.material_id')
            ->whereIn('tbl_material.nv_id', $BYPLnv)
            ->where(function ($query) {
                $query->where('nvservicestatus.cto_status', 2);
            })
            ->sum('tbl_material.total_budget_both')+DB::table('tbl_service')
            ->join('nvservicestatus', 'tbl_service.id', '=', 'nvservicestatus.service_id')
            ->whereIn('tbl_service.nv_id', $BYPLnv)
            ->where(function ($query) {
                $query->where('nvservicestatus.cto_status', 2);
            })
            ->sum('tbl_service.total_buget');
       
    
            $approvedAmountBYPL = DB::table('tbl_material')
            ->join('nvservicestatus', 'tbl_material.id', '=', 'nvservicestatus.material_id')
            ->whereIn('tbl_material.nv_id', $BYPLnv)->where('nvservicestatus.ceo_status', 1)
            ->sum('tbl_material.total_budget_both')+DB::table('tbl_service')
            ->join('nvservicestatus', 'tbl_service.id', '=', 'nvservicestatus.service_id')
            ->whereIn('tbl_service.nv_id', $BYPLnv)->where('nvservicestatus.ceo_status', 1)
            ->sum('tbl_service.total_buget');
            
            if(!empty($id2->work_rew1) || !empty($id2->work_rew2)|| !empty($id2->work_rew3)|| !empty($id2->work_rew4)){

                $fileDataBYPL =Nvsericestatus::
                select(
                    DB::raw('MONTH(created_at) as month'),
                    DB::raw('SUM(CASE WHEN ceo_status = "1" THEN 1 ELSE 0 END) as approved_count'),
                    DB::raw('SUM(CASE WHEN ( cto_status = "2")  THEN 1 ELSE 0 END) as rejected_count'),
                    DB::raw('SUM(CASE WHEN (cto_status = "0" AND (work_rew1dep2_status = "1" OR work_rew2dep2_status = "1" OR work_rew3dep2_status = "1" OR work_rew4dep2_status = "1")) THEN 1 ELSE 0 END) as pending_count'),
                  
                ) 
                ->whereIn('nv_id', $BYPLnv)->with(['service', 'material', 'user'])
            
                ->where('approver_status',1)
               
                ->whereYear('created_at', Carbon::now()->year)
                ->groupBy('month')
                ->orderBy('month')
                ->get();

            }elseif(empty($id2->work_rew1) && empty($id2->work_rew2) && empty($id2->work_rew3) && empty($id2->work_rew4)){

             $fileDataBYPL =Nvsericestatus::
            select(
                DB::raw('MONTH(created_at) as month'),
                DB::raw('SUM(CASE WHEN ceo_status = "1" THEN 1 ELSE 0 END) as approved_count'),
                DB::raw('SUM(CASE WHEN ( cto_status = "2")  THEN 1 ELSE 0 END) as rejected_count'),
                DB::raw('SUM(CASE WHEN (check_technology="1" AND cto_status = "0" AND work_rew1dep2_status = "0" AND work_rew2dep2_status = "0" AND work_rew3dep2_status = "0" AND work_rew4dep2_status = "0") THEN 1 ELSE 0 END) as pending_count'),
              
            ) 
            ->whereIn('nv_id', $BYPLnv)->with(['service', 'material', 'user'])
        
            ->where('approver_status',1)
          
            ->whereYear('created_at', Carbon::now()->year)
            ->groupBy('month')
            ->orderBy('month')
            ->get();
            }

        $BYPLlabels = [];
        $BYPLapprovedData = [];
        $BYPLrejectedData = [];
        $BYPLpendingData = [];
    
        foreach ($fileDataBYPL as $dataPointBYPL) {
            $monthBYPL = Carbon::createFromFormat('!m', $dataPointBYPL->month)->format('F');
    
            $BYPLlabels[] = $monthBYPL;
            $BYPLapprovedData[] = $dataPointBYPL->approved_count;
            $BYPLrejectedData[] = $dataPointBYPL->rejected_count;
            $BYPLpendingData[] = $dataPointBYPL->pending_count;
         
        }
                return view("admin.dashboard", compact("approvedAmount","currentFinancialYear","nextFinancialYear","nextToNextFinancialYear", "rejectedAmount", "pendingAmount", "totalAmount","company", "company_id" , "nv_sm_data", "totalNV", "approvedNV", "rejectedNV", "pendingNV" , "hodApproval", "cpmgApproval", "btApproval", "ceoApproval"             ,'BRPLlabels','BRPLapprovedData','BRPLrejectedData','BRPLpendingData','BYPLlabels','BYPLapprovedData','BYPLrejectedData','BYPLpendingData','cesApproval','ceonominee1Approval','ceonominee2Approval','approvedAmountBYPL','approvedAmountBRPL','pendingAmountBYPL','pendingAmountBRPL','rejectedAmountBRPL','rejectedAmountBYPL','groupcioApproval'));
            } elseif ($id3->work_rew1 == $user->id) {
                $Values = [$user->id, $id3->work_rew1];
              
                if($fiscal_year){
                    $totalId = NeedValidation::where('fiscal_year', $fiscal_year)->pluck('id');
                }else{
                    $totalId = NeedValidation::where('fiscal_year', $currentFinancialYear)->pluck('id');
                }
               
                $totalAmount = DB::table('tbl_material')
                ->join('nvservicestatus', 'tbl_material.id', '=', 'nvservicestatus.material_id')
                
                 ->whereIn('tbl_material.nv_id', $totalId)
                 ->whereIn('nvservicestatus.nv_id', $latestData->pluck('nv_id'))
                ->where('nvservicestatus.work_rew1dep3_status', 1)
                ->sum('tbl_material.total_budget_both') + DB::table('tbl_service')
                ->join('nvservicestatus', 'tbl_service.id', '=', 'nvservicestatus.service_id')
                
                 ->whereIn('tbl_service.nv_id', $totalId)
                 ->whereIn('nvservicestatus.nv_id', $latestData->pluck('nv_id'))
                ->where('nvservicestatus.work_rew1dep3_status', 1)
                ->sum('tbl_service.total_buget');
              
            
            $pen_amt= $latestData->filter(function ($data) {
                return in_array($data->approver_status, [1]) &&
                    in_array($data->work_rew1dep3_status, [0]) &&
                    in_array($data->work_rew2dep3_status, [0]) &&
                    in_array($data->work_rew3dep3_status, [0]) &&
                    in_array($data->work_rew4dep3_status, [0]) &&
                    in_array($data->approverdep3_status, [0]) &&
                    (
                        (in_array($data->check_technology, [0]) && in_array($data->approverdep2_status, [0]))
                        ||
                        (in_array($data->check_technology, [1]) && in_array($data->approverdep2_status, [1]))
                    );
            });
            $pendingAmount = DB::table('tbl_material')
            ->join('nvservicestatus', 'tbl_material.id', '=', 'nvservicestatus.material_id')
            ->whereIn('tbl_material.nv_id', $totalId)
            ->whereIn('nvservicestatus.nv_id', $pen_amt->pluck('nv_id'))
           
           
            ->sum('tbl_material.total_budget_both') + 
            DB::table('tbl_service')
            ->join('nvservicestatus', 'tbl_service.id', '=', 'nvservicestatus.service_id')
            ->whereIn('tbl_service.nv_id', $totalId)
            ->whereIn('nvservicestatus.nv_id', $pen_amt->pluck('nv_id'))
           
           
            ->sum('tbl_service.total_buget');
        
                $rejectedAmount = DB::table('tbl_material')
                ->join('nvservicestatus', 'tbl_material.id', '=', 'nvservicestatus.material_id')
                ->whereIn('nvservicestatus.nv_id', $latestData->pluck('nv_id'))
                ->where(function ($query) {
                    $query->where('nvservicestatus.work_rew2dep3_status', 2);
                })
                ->sum('tbl_material.total_budget_both') + DB::table('tbl_service')
                ->join('nvservicestatus', 'tbl_service.id', '=', 'nvservicestatus.service_id')
                ->whereIn('nvservicestatus.nv_id', $latestData->pluck('nv_id'))
                ->where(function ($query) {
                    $query->where('nvservicestatus.work_rew2dep3_status', 2);
                })
                ->sum('tbl_service.total_buget');
           
        
                $approvedAmount = DB::table('tbl_material')
                
                 ->whereIn('tbl_material.nv_id', $totalId)
                 ->whereIn('nvservicestatus.nv_id', $latestData->pluck('nv_id'))
                ->join('nvservicestatus', 'tbl_material.id', '=', 'nvservicestatus.material_id')
                ->where('nvservicestatus.ceo_status', 1)
                ->sum('tbl_material.total_budget_both') + DB::table('tbl_service')
               
                ->whereIn('tbl_service.nv_id', $totalId)
                ->whereIn('nvservicestatus.nv_id', $latestData->pluck('nv_id'))
               ->join('nvservicestatus', 'tbl_service.id', '=', 'nvservicestatus.service_id')
               ->where('nvservicestatus.ceo_status', 1)
               ->sum('tbl_service.total_buget');

                if($company_id){
                    $totalNV = Nvsericestatus::whereIn("nv_id", $totalId)->where('work_rew1dep3_status', 1)->where('company_id',$company_id)->count();
                } else {
                    $totalNV = Nvsericestatus::whereIn("nv_id", $totalId)->where('work_rew1dep3_status', 1)->count();
                }
                if($company_id){
                    $rejectedNV = Nvsericestatus::whereIn("nv_id", $totalId)->where('work_rew1dep3_status', 2)->where('company_id',$company_id)->count();
                } else {
                    $rejectedNV = Nvsericestatus::whereIn("nv_id", $totalId)->where('work_rew1dep3_status', 2)->count();
                }
          
                if($company_id){
                $latestData = Nvsericestatus::whereIn("nv_id", $totalId)->where('company_id',$company_id)->get();
            }else {
                $latestData = Nvsericestatus::whereIn("nv_id", $totalId)->get();
            }
                if($company_id){
                    $approvedNV = $latestData->where('ceo_status', 1)->where('company_id',$company_id)->count();
                }else{
                    $approvedNV = $latestData->where('ceo_status', 1)->count();
                }
              
                $pendingNV = $latestData->filter(function ($data) {
                    return in_array($data->approver_status, [1]) &&
                        in_array($data->work_rew1dep3_status, [0]) &&
                        in_array($data->work_rew2dep3_status, [0]) &&
                        in_array($data->work_rew3dep3_status, [0]) &&
                        in_array($data->work_rew4dep3_status, [0]) &&
                        in_array($data->approverdep3_status, [0]) &&
                        (
                            (in_array($data->check_technology, [0]) && in_array($data->approverdep2_status, [0]))
                            ||
                            (in_array($data->check_technology, [1]) && in_array($data->approverdep2_status, [1]))
                        );
                })->count();
            

                $nvIds = NeedValidation::where("user_id", $user->id)->pluck("id");
                if($fiscal_year){
                    $nv = NeedValidation::whereHas('service')->select('id')->where('fiscal_year', $fiscal_year)
                   
                    ->get();
                }else{
                    $nv = NeedValidation::whereHas('service')->select('id')->where('fiscal_year', $currentFinancialYear)
                   
                    ->get();
                }
              
                $nv_ids = $nv->pluck('id');
             
             
                    $nv_sm_data = Nvsericestatus::whereIn('nv_id', $nv_ids)->with(['service', 'material', 'user'])
                    ->where(function ($query) {
                        $query->where('approverdep2_status', 1)
                            ->orWhere('approver_status', 1)
                            ->where('check_technology', 0);
                    })
                 ->orderBy('id', 'asc')->get();
               
                $total8_status = $request->total;
                $pending8_status = $request->pending;
                $reject8_status = $request->rejected;

             if ($ceo_status !== null) {
                    $nv_sm_data = $nv_sm_data->where('ceo_status', $ceo_status);
                } elseif ($pending8_status !== null) {
                    $nv_sm_data =  $nv_sm_data->where('work_rew1dep3_status', $pending8_status)
                    ->where('work_rew2dep3_status', 0)
                    ->where('work_rew3dep3_status', 0)
                    ->where('work_rew4dep3_status', 0);
                       
                } elseif ($reject8_status !== null) {
                    $nv_sm_data =  $nv_sm_data->where('work_rew1dep3_status', $reject8_status)
                        ;  
                }elseif ($total8_status !== null) {
                    $nv_sm_data =  $nv_sm_data->where('work_rew1dep3_status', $total8_status);
                      
                }
            
                if($fiscal_year){
                    $BRPLnv = NeedValidation::where('company_id','6')->where('fiscal_year', $fiscal_year)->pluck("id");
                }else{
                    $BRPLnv = NeedValidation::where('company_id','6')->where('fiscal_year', $currentFinancialYear)->pluck("id");
                }
                
                $pendingAmountBRPL = DB::table('tbl_material')
                ->join('nvservicestatus', 'tbl_material.id', '=', 'nvservicestatus.material_id')
                ->whereIn('tbl_material.nv_id', $BRPLnv)
                ->whereIn('nvservicestatus.nv_id', $pen_amt->pluck('nv_id'))
               
                ->sum('tbl_material.total_budget_both')+DB::table('tbl_service')
                ->join('nvservicestatus', 'tbl_service.id', '=', 'nvservicestatus.service_id')
                ->whereIn('tbl_service.nv_id', $BRPLnv)
                ->whereIn('nvservicestatus.nv_id', $pen_amt->pluck('nv_id'))
               
                ->sum('tbl_service.total_buget');
        
                $rejectedAmountBRPL = DB::table('tbl_material')
                ->join('nvservicestatus', 'tbl_material.id', '=', 'nvservicestatus.material_id')
                ->whereIn('tbl_material.nv_id', $BRPLnv)
                ->where(function ($query) {
                    $query->where('nvservicestatus.work_rew1dep3_status', 2);
                })
                ->sum('tbl_material.total_budget_both')+DB::table('tbl_service')
                ->join('nvservicestatus', 'tbl_service.id', '=', 'nvservicestatus.service_id')
                ->whereIn('tbl_service.nv_id', $BRPLnv)
                ->where(function ($query) {
                    $query->where('nvservicestatus.work_rew1dep3_status', 2);
                })
                ->sum('tbl_service.total_buget');
           
        
                $approvedAmountBRPL = DB::table('tbl_material')
                ->join('nvservicestatus', 'tbl_material.id', '=', 'nvservicestatus.material_id')
                ->whereIn('tbl_material.nv_id', $BRPLnv)->where('nvservicestatus.ceo_status', 1)
                ->sum('tbl_material.total_budget_both')+DB::table('tbl_service')
                ->join('nvservicestatus', 'tbl_service.id', '=', 'nvservicestatus.service_id')
                ->whereIn('tbl_service.nv_id', $BRPLnv)->where('nvservicestatus.ceo_status', 1)
                ->sum('tbl_service.total_buget');
                $fileDataBRPL =Nvsericestatus::
                select(
                    DB::raw('MONTH(created_at) as month'),
                    DB::raw('SUM(CASE WHEN ceo_status = "1" THEN 1 ELSE 0 END) as approved_count'),
                    DB::raw('SUM(CASE WHEN ( work_rew1dep3_status = "2")  THEN 1 ELSE 0 END ) as rejected_count'),
                    DB::raw('SUM(CASE WHEN ( work_rew1dep3_status = "0" AND work_rew2dep3_status = "0" AND work_rew3dep3_status = "0" AND work_rew4dep3_status = "0" AND approverdep3_status = "0")  THEN 1 ELSE 0 END) as pending_count'),
                 
                )
             
                ->whereIn('nv_id', $BRPLnv)->with(['service', 'material', 'user'])
                ->where(function ($query) {
                    $query->where('approverdep2_status', 1)
                        ->orWhere('approver_status', 1)
                        ->where('check_technology', 0);
                })

                ->whereYear('created_at', Carbon::now()->year)
                ->groupBy('month')
                ->orderBy('month')
                ->get();
      
            $BRPLlabels = [];
            $BRPLapprovedData = [];
            $BRPLrejectedData = [];
            $BRPLpendingData = [];
        
            foreach ($fileDataBRPL as $dataPointBRPL) {
                $monthBRPL = Carbon::createFromFormat('!m', $dataPointBRPL->month)->format('F');
        
                $BRPLlabels[] = $monthBRPL;
                $BRPLapprovedData[] = $dataPointBRPL->approved_count;
                $BRPLrejectedData[] = $dataPointBRPL->rejected_count;
                $BRPLpendingData[] = $dataPointBRPL->pending_count;
                
            }
            if($fiscal_year){
            $BYPLnv = NeedValidation::where('company_id','5')->where('fiscal_year', $fiscal_year)->pluck("id");
            }else{
             $BYPLnv = NeedValidation::where('company_id','5')->where('fiscal_year', $currentFinancialYear)->pluck("id");
            }
            $pendingAmountBYPL = DB::table('tbl_material')
            ->join('nvservicestatus', 'tbl_material.id', '=', 'nvservicestatus.material_id')
            ->whereIn('tbl_material.nv_id', $BYPLnv)
            ->whereIn('nvservicestatus.nv_id', $pen_amt->pluck('nv_id'))
          
            ->sum('tbl_material.total_budget_both')+DB::table('tbl_service')
            ->join('nvservicestatus', 'tbl_service.id', '=', 'nvservicestatus.service_id')
            ->whereIn('tbl_service.nv_id', $BYPLnv)
            ->whereIn('nvservicestatus.nv_id', $pen_amt->pluck('nv_id'))
           
            ->sum('tbl_service.total_buget');
    
            $rejectedAmountBYPL = DB::table('tbl_material')
            ->join('nvservicestatus', 'tbl_material.id', '=', 'nvservicestatus.material_id')
            ->whereIn('tbl_material.nv_id', $BYPLnv)
            ->where(function ($query) {
                $query->where('nvservicestatus.work_rew1dep3_status', 2);
            })
            ->sum('tbl_material.total_budget_both')+DB::table('tbl_service')
            ->join('nvservicestatus', 'tbl_service.id', '=', 'nvservicestatus.service_id')
            ->whereIn('tbl_service.nv_id', $BYPLnv)
            ->where(function ($query) {
                $query->where('nvservicestatus.work_rew1dep3_status', 2);
            })
            ->sum('tbl_service.total_buget');
       
    
            $approvedAmountBYPL = DB::table('tbl_material')
            ->join('nvservicestatus', 'tbl_material.id', '=', 'nvservicestatus.material_id')
            ->whereIn('tbl_material.nv_id', $BYPLnv)->where('nvservicestatus.ceo_status', 1)
            ->sum('tbl_material.total_budget_both')+DB::table('tbl_service')
            ->join('nvservicestatus', 'tbl_service.id', '=', 'nvservicestatus.service_id')
            ->whereIn('tbl_service.nv_id', $BYPLnv)->where('nvservicestatus.ceo_status', 1)
            ->sum('tbl_service.total_buget');
             $fileDataBYPL =Nvsericestatus::
            select(
                DB::raw('MONTH(created_at) as month'),
                DB::raw('SUM(CASE WHEN ceo_status = "1" THEN 1 ELSE 0 END) as approved_count'),
                DB::raw('SUM(CASE WHEN ( work_rew1dep3_status = "2")  THEN 1 ELSE 0 END) as rejected_count'),
                DB::raw('SUM(CASE WHEN ( work_rew1dep3_status = "0" AND work_rew2dep3_status = "0" AND work_rew3dep3_status = "0" AND work_rew4dep3_status = "0" AND approverdep3_status = "0")  THEN 1 ELSE 0 END) as pending_count'),
               
            ) 
            ->whereIn('nv_id', $BYPLnv)->with(['service', 'material', 'user'])
            ->where(function ($query) {
                $query->where('approverdep2_status', 1)
                    ->orWhere('approver_status', 1)
                    ->where('check_technology', 0);
            })

            ->whereYear('created_at', Carbon::now()->year)
            ->groupBy('month')
            ->orderBy('month')
            ->get();
           
        $BYPLlabels = [];
        $BYPLapprovedData = [];
        $BYPLrejectedData = [];
        $BYPLpendingData = [];
    
        foreach ($fileDataBYPL as $dataPointBYPL) {
            $monthBYPL = Carbon::createFromFormat('!m', $dataPointBYPL->month)->format('F');
    
            $BYPLlabels[] = $monthBYPL;
            $BYPLapprovedData[] = $dataPointBYPL->approved_count;
            $BYPLrejectedData[] = $dataPointBYPL->rejected_count;
            $BYPLpendingData[] = $dataPointBYPL->pending_count;
            
        }
             
                return view("admin.dashboard", compact("approvedAmount","currentFinancialYear","nextFinancialYear","nextToNextFinancialYear", "rejectedAmount", "pendingAmount", "totalAmount","company", "company_id" , "nv_sm_data", "totalNV", "approvedNV", "rejectedNV", "pendingNV", "hodApproval", "cpmgApproval", "btApproval", "ceoApproval"             ,'BRPLlabels','BRPLapprovedData','BRPLrejectedData','BRPLpendingData','BYPLlabels','BYPLapprovedData','BYPLrejectedData','BYPLpendingData','cesApproval','ceonominee1Approval','ceonominee2Approval','approvedAmountBYPL','approvedAmountBRPL','rejectedAmountBYPL','rejectedAmountBRPL','pendingAmountBYPL','pendingAmountBRPL','groupcioApproval'));
            } elseif ($id3->work_rew2 == $user->id) {
                $Values = [$user->id, $id3->work_rew1];
              
                if($fiscal_year){
                    $totalId = NeedValidation::where('fiscal_year', $fiscal_year)->pluck('id');
                }else{
                    $totalId = NeedValidation::where('fiscal_year', $currentFinancialYear)->pluck('id');
                }
               
               
                $totalAmount = DB::table('tbl_material')
                ->join('nvservicestatus', 'tbl_material.id', '=', 'nvservicestatus.material_id')
                
                 ->whereIn('tbl_material.nv_id', $totalId)
                 ->whereIn('nvservicestatus.nv_id', $latestData->pluck('nv_id'))
                ->where('nvservicestatus.work_rew2dep3_status', 1)
                ->sum('tbl_material.total_budget_both') + DB::table('tbl_service')
                ->join('nvservicestatus', 'tbl_service.id', '=', 'nvservicestatus.service_id')
                
                 ->whereIn('tbl_service.nv_id', $totalId)
                 ->whereIn('nvservicestatus.nv_id', $latestData->pluck('nv_id'))
                ->where('nvservicestatus.work_rew2dep3_status', 1)
                ->sum('tbl_service.total_buget');
               
                $pen_amt= $latestData->filter(function ($data) {
                    return in_array($data->approver_status, [1]) &&
                        in_array($data->work_rew1dep3_status, [0]) &&
                        in_array($data->work_rew2dep3_status, [0]) &&
                        in_array($data->work_rew3dep3_status, [0]) &&
                        in_array($data->work_rew4dep3_status, [0]) &&
                        in_array($data->approverdep3_status, [0]) &&
                        (
                            (in_array($data->check_technology, [0]) && in_array($data->approverdep2_status, [0]))
                            ||
                            (in_array($data->check_technology, [1]) && in_array($data->approverdep2_status, [1]))
                        );
                });
                $pendingAmount = DB::table('tbl_material')
                ->join('nvservicestatus', 'tbl_material.id', '=', 'nvservicestatus.material_id')
                
                 ->whereIn('tbl_material.nv_id', $totalId)
                 ->whereIn('nvservicestatus.nv_id', $pen_amt->pluck('nv_id'))
             
                ->sum('tbl_material.total_budget_both') +  DB::table('tbl_service')
                ->join('nvservicestatus', 'tbl_service.id', '=', 'nvservicestatus.service_id')
                
                 ->whereIn('tbl_service.nv_id', $totalId)
                 ->whereIn('nvservicestatus.nv_id', $pen_amt->pluck('nv_id'))
             
                ->sum('tbl_service.total_buget');
        
                $rejectedAmount = DB::table('tbl_material')
                ->join('nvservicestatus', 'tbl_material.id', '=', 'nvservicestatus.material_id')
                ->whereIn('nvservicestatus.nv_id', $latestData->pluck('nv_id'))
                ->where(function ($query) {
                    $query->where('nvservicestatus.work_rew2dep3_status', 2);
                })
                ->sum('tbl_material.total_budget_both') + DB::table('tbl_service')
                ->join('nvservicestatus', 'tbl_service.id', '=', 'nvservicestatus.service_id')
                ->whereIn('nvservicestatus.nv_id', $latestData->pluck('nv_id'))
                ->where(function ($query) {
                    $query->where('nvservicestatus.work_rew2dep3_status', 2);
                })
                ->sum('tbl_service.total_buget');
           
        
                $approvedAmount = DB::table('tbl_material')
               
                 ->whereIn('tbl_material.nv_id', $totalId)
                 ->whereIn('nvservicestatus.nv_id', $latestData->pluck('nv_id'))
                ->join('nvservicestatus', 'tbl_material.id', '=', 'nvservicestatus.material_id')
                ->where('nvservicestatus.ceo_status', 1)
                ->sum('tbl_material.total_budget_both') +  DB::table('tbl_service')
              
                ->whereIn('tbl_service.nv_id', $totalId)
                ->whereIn('nvservicestatus.nv_id', $latestData->pluck('nv_id'))
               ->join('nvservicestatus', 'tbl_service.id', '=', 'nvservicestatus.service_id')
               ->where('nvservicestatus.ceo_status', 1)
               ->sum('tbl_service.total_buget');

                if($company_id){
                    $totalNV = Nvsericestatus::whereIn("nv_id", $totalId)->where('work_rew2dep3_status', 1)->where('company_id',$company_id)->count();
                } else {
                    $totalNV = Nvsericestatus::whereIn("nv_id", $totalId)->where('work_rew2dep3_status', 1)->count();
                }
                if($company_id){
                    $rejectedNV = Nvsericestatus::whereIn("nv_id", $totalId)->where('work_rew2dep3_status', 2)->where('company_id',$company_id)->count();
                } else {
                    $rejectedNV = Nvsericestatus::whereIn("nv_id", $totalId)->where('work_rew2dep3_status', 2)->count();
                }
               
                if($company_id){
                $latestData = Nvsericestatus::whereIn("nv_id", $totalId)->where('company_id',$company_id)->get();
            }else {
                $latestData = Nvsericestatus::whereIn("nv_id", $totalId)->get();
            }
                if($company_id){
                    $approvedNV = $latestData->where('ceo_status', 1)->where('company_id',$company_id)->count();
                }else{
                    $approvedNV = $latestData->where('ceo_status', 1)->count();
                }
              
                $pendingNV = $latestData->filter(function ($data) {
                    return in_array($data->approver_status, [1]) &&
                        in_array($data->work_rew1dep3_status, [0]) &&
                        in_array($data->work_rew2dep3_status, [0]) &&
                        in_array($data->work_rew3dep3_status, [0]) &&
                        in_array($data->work_rew4dep3_status, [0]) &&
                        in_array($data->approverdep3_status, [0]) &&
                        (
                            (in_array($data->check_technology, [0]) && in_array($data->approverdep2_status, [0]))
                            ||
                            (in_array($data->check_technology, [1]) && in_array($data->approverdep2_status, [1]))
                        );
                })->count();
               
                $nvIds = NeedValidation::where("user_id", $Values)->pluck("id");
                if($fiscal_year){
                    $nv = NeedValidation::whereHas('service')->select('id')->where('fiscal_year', $fiscal_year)
              
                    ->get();
                }else{
                    $nv = NeedValidation::whereHas('service')->select('id')->where('fiscal_year', $currentFinancialYear)
             
                    ->get();
                }
               
                $nv_ids = $nv->pluck('id');
             
                    $nv_sm_data = Nvsericestatus::whereIn('nv_id', $nv_ids)->with(['service', 'material', 'user'])
                    ->where(function ($query) {
                        $query->where('approverdep2_status', 1)
                            ->orWhere('approver_status', 1)
                            ->where('check_technology', 0);
                    })
                ->orderBy('id', 'asc')->get();
           
                $total19_status = $request->total;
                $pending19_status = $request->pending;
                $reject19_status = $request->rejected;
                if ($ceo_status !== null) {
                    $nv_sm_data = $nv_sm_data->where('ceo_status', $ceo_status);
                } elseif ($pending19_status !== null) {
                    $nv_sm_data = $nv_sm_data->where('work_rew2dep3_status', $pending19_status)
                    
                    ->where('work_rew1dep3_status', 0)
                    ->where('work_rew3dep3_status', 0)
                    ->where('work_rew4dep3_status', 0);

                } elseif ($reject19_status !== null) {
                    $nv_sm_data = $nv_sm_data->where('work_rew2dep3_status', $reject19_status);
                    
                }elseif ($total19_status !== null) {
                    $nv_sm_data = $nv_sm_data->where('work_rew2dep3_status', $total19_status);
                      
                }
                if($fiscal_year){
                    $BRPLnv = NeedValidation::where('company_id','6')->where('fiscal_year', $fiscal_year)->pluck("id");
                }else{
                    $BRPLnv = NeedValidation::where('company_id','6')->where('fiscal_year', $currentFinancialYear)->pluck("id");
                }
                $pendingAmountBRPL = DB::table('tbl_material')
                ->join('nvservicestatus', 'tbl_material.id', '=', 'nvservicestatus.material_id')
                ->whereIn('tbl_material.nv_id', $BRPLnv)
                ->whereIn('nvservicestatus.nv_id', $pen_amt->pluck('nv_id'))
                
                ->sum('tbl_material.total_budget_both')+DB::table('tbl_service')
                ->join('nvservicestatus', 'tbl_service.id', '=', 'nvservicestatus.service_id')
                ->whereIn('tbl_service.nv_id', $BRPLnv)
                ->whereIn('nvservicestatus.nv_id', $pen_amt->pluck('nv_id'))
                
                ->sum('tbl_service.total_buget');
        
                $rejectedAmountBRPL = DB::table('tbl_material')
                ->join('nvservicestatus', 'tbl_material.id', '=', 'nvservicestatus.material_id')
                ->whereIn('tbl_material.nv_id', $BRPLnv)
                ->where(function ($query) {
                    $query->where('nvservicestatus.work_rew2dep3_status', 2);
                })
                ->sum('tbl_material.total_budget_both')+DB::table('tbl_service')
                ->join('nvservicestatus', 'tbl_service.id', '=', 'nvservicestatus.service_id')
                ->whereIn('tbl_service.nv_id', $BRPLnv)
                ->where(function ($query) {
                    $query->where('nvservicestatus.work_rew2dep3_status', 2);
                })
                ->sum('tbl_service.total_buget');
           
        
                $approvedAmountBRPL = DB::table('tbl_material')
                ->join('nvservicestatus', 'tbl_material.id', '=', 'nvservicestatus.material_id')
                ->whereIn('tbl_material.nv_id', $BRPLnv)->where('nvservicestatus.ceo_status', 1)
                ->sum('tbl_material.total_budget_both')+DB::table('tbl_service')
                ->join('nvservicestatus', 'tbl_service.id', '=', 'nvservicestatus.service_id')
                ->whereIn('tbl_service.nv_id', $BRPLnv)->where('nvservicestatus.ceo_status', 1)
                ->sum('tbl_service.total_buget');
                $fileDataBRPL =Nvsericestatus::
                select(
                    DB::raw('MONTH(created_at) as month'),
                    DB::raw('SUM(CASE WHEN ceo_status = "1" THEN 1 ELSE 0 END) as approved_count'),
                    DB::raw('SUM(CASE WHEN ( work_rew2dep3_status = "2")  THEN 1 ELSE 0 END ) as rejected_count'),
                    DB::raw('SUM(CASE WHEN ( work_rew1dep3_status = "0" AND work_rew2dep3_status = "0" AND work_rew3dep3_status = "0" AND work_rew4dep3_status = "0" AND approverdep3_status = "0")  THEN 1 ELSE 0 END) as pending_count'),
                  
                )
               
                ->whereIn('nv_id', $BRPLnv)->with(['service', 'material', 'user'])
                ->where(function ($query) {
                    $query->where('approverdep2_status', 1)
                        ->orWhere('approver_status', 1)
                        ->where('check_technology', 0);
                })
             
                ->whereYear('created_at', Carbon::now()->year)
                ->groupBy('month')
                ->orderBy('month')
                ->get();
       
            $BRPLlabels = [];
            $BRPLapprovedData = [];
            $BRPLrejectedData = [];
            $BRPLpendingData = [];
        
            foreach ($fileDataBRPL as $dataPointBRPL) {
                $monthBRPL = Carbon::createFromFormat('!m', $dataPointBRPL->month)->format('F');
        
                $BRPLlabels[] = $monthBRPL;
                $BRPLapprovedData[] = $dataPointBRPL->approved_count;
                $BRPLrejectedData[] = $dataPointBRPL->rejected_count;
                $BRPLpendingData[] = $dataPointBRPL->pending_count;
               
            }
            if($fiscal_year){
                $BYPLnv = NeedValidation::where('company_id','5')->where('fiscal_year', $fiscal_year)->pluck("id");
            }else{
                $BYPLnv = NeedValidation::where('company_id','5')->where('fiscal_year', $currentFinancialYear)->pluck("id");
            }
           
            $pendingAmountBYPL = DB::table('tbl_material')
            ->join('nvservicestatus', 'tbl_material.id', '=', 'nvservicestatus.material_id')
            ->whereIn('tbl_material.nv_id', $BYPLnv)
            ->whereIn('nvservicestatus.nv_id', $pen_amt->pluck('nv_id'))
         
            ->sum('tbl_material.total_budget_both')+DB::table('tbl_service')
            ->join('nvservicestatus', 'tbl_service.id', '=', 'nvservicestatus.service_id')
            ->whereIn('tbl_service.nv_id', $BYPLnv)
            ->whereIn('nvservicestatus.nv_id', $pen_amt->pluck('nv_id'))
         
            ->sum('tbl_service.total_buget');
    
            $rejectedAmountBYPL = DB::table('tbl_material')
            ->join('nvservicestatus', 'tbl_material.id', '=', 'nvservicestatus.material_id')
            ->whereIn('tbl_material.nv_id', $BYPLnv)
            ->where(function ($query) {
                $query->where('nvservicestatus.work_rew2dep3_status', 2);
            })
            ->sum('tbl_material.total_budget_both')+DB::table('tbl_service')
            ->join('nvservicestatus', 'tbl_service.id', '=', 'nvservicestatus.service_id')
            ->whereIn('tbl_service.nv_id', $BYPLnv)
            ->where(function ($query) {
                $query->where('nvservicestatus.work_rew2dep3_status', 2);
            })
            ->sum('tbl_service.total_buget');
       
    
            $approvedAmountBYPL = DB::table('tbl_material')
            ->join('nvservicestatus', 'tbl_material.id', '=', 'nvservicestatus.material_id')
            ->whereIn('tbl_material.nv_id', $BYPLnv)->where('nvservicestatus.ceo_status', 1)
            ->sum('tbl_material.total_budget_both')+DB::table('tbl_service')
            ->join('nvservicestatus', 'tbl_service.id', '=', 'nvservicestatus.service_id')
            ->whereIn('tbl_service.nv_id', $BYPLnv)->where('nvservicestatus.ceo_status', 1)
            ->sum('tbl_service.total_buget');
             $fileDataBYPL =Nvsericestatus::
            select(
                DB::raw('MONTH(created_at) as month'),
                DB::raw('SUM(CASE WHEN ceo_status = "1" THEN 1 ELSE 0 END) as approved_count'),
                DB::raw('SUM(CASE WHEN ( work_rew2dep3_status = "2")  THEN 1 ELSE 0 END) as rejected_count'),
                DB::raw('SUM(CASE WHEN ( work_rew1dep3_status = "0" AND work_rew2dep3_status = "0" AND work_rew3dep3_status = "0" AND work_rew4dep3_status = "0" AND approverdep3_status = "0")  THEN 1 ELSE 0 END) as pending_count'),
               
            ) 
            ->whereIn('nv_id', $BYPLnv)->with(['service', 'material', 'user'])
            ->where(function ($query) {
                $query->where('approverdep2_status', 1)
                    ->orWhere('approver_status', 1)
                    ->where('check_technology', 0);
            })
          
            ->whereYear('created_at', Carbon::now()->year)
            ->groupBy('month')
            ->orderBy('month')
            ->get();
          
        $BYPLlabels = [];
        $BYPLapprovedData = [];
        $BYPLrejectedData = [];
        $BYPLpendingData = [];
    
        foreach ($fileDataBYPL as $dataPointBYPL) {
            $monthBYPL = Carbon::createFromFormat('!m', $dataPointBYPL->month)->format('F');
    
            $BYPLlabels[] = $monthBYPL;
            $BYPLapprovedData[] = $dataPointBYPL->approved_count;
            $BYPLrejectedData[] = $dataPointBYPL->rejected_count;
            $BYPLpendingData[] = $dataPointBYPL->pending_count;
            
        }
                return view("admin.dashboard", compact("approvedAmount","currentFinancialYear","nextFinancialYear","nextToNextFinancialYear", "rejectedAmount", "pendingAmount", "totalAmount","company", "company_id" , "nv_sm_data", "totalNV", "approvedNV", "rejectedNV", "pendingNV" , "hodApproval", "cpmgApproval", "btApproval", "ceoApproval"             ,'BRPLlabels','BRPLapprovedData','BRPLrejectedData','BRPLpendingData','BYPLlabels','BYPLapprovedData','BYPLrejectedData','BYPLpendingData','cesApproval','ceonominee1Approval','ceonominee2Approval','approvedAmountBYPL','approvedAmountBRPL','rejectedAmountBYPL','rejectedAmountBRPL','pendingAmountBYPL','pendingAmountBRPL','groupcioApproval'));
            } elseif ($id3->work_rew3 == $user->id) {
                $Values = [$user->id, $id3->work_rew1, $id3->work_rew2];
               
                if($fiscal_year){
                    $totalId = NeedValidation::where('fiscal_year', $fiscal_year)->pluck('id');
                }else{
                    $totalId = NeedValidation::where('fiscal_year', $currentFinancialYear)->pluck('id');
                }
               
               
                $totalAmount = DB::table('tbl_material')
                ->join('nvservicestatus', 'tbl_material.id', '=', 'nvservicestatus.material_id')
              
                 ->whereIn('tbl_material.nv_id', $totalId)
                 ->whereIn('nvservicestatus.nv_id', $latestData->pluck('nv_id'))
                ->where('nvservicestatus.work_rew3dep3_status', 1)
                ->sum('tbl_material.total_budget_both') + DB::table('tbl_service')
                ->join('nvservicestatus', 'tbl_service.id', '=', 'nvservicestatus.service_id')
              
                 ->whereIn('tbl_service.nv_id', $totalId)
                 ->whereIn('nvservicestatus.nv_id', $latestData->pluck('nv_id'))
                ->where('nvservicestatus.work_rew3dep3_status', 1)
                ->sum('tbl_service.total_buget');
             
                $pen_amt= $latestData->filter(function ($data) {
                    return in_array($data->approver_status, [1]) &&
                        in_array($data->work_rew1dep3_status, [0]) &&
                        in_array($data->work_rew2dep3_status, [0]) &&
                        in_array($data->work_rew3dep3_status, [0]) &&
                        in_array($data->work_rew4dep3_status, [0]) &&
                        in_array($data->approverdep3_status, [0]) &&
                        (
                            (in_array($data->check_technology, [0]) && in_array($data->approverdep2_status, [0]))
                            ||
                            (in_array($data->check_technology, [1]) && in_array($data->approverdep2_status, [1]))
                        );
                });
                $pendingAmount = DB::table('tbl_material')
                ->join('nvservicestatus', 'tbl_material.id', '=', 'nvservicestatus.material_id')
               
                 ->whereIn('tbl_material.nv_id', $totalId)
                 ->whereIn('nvservicestatus.nv_id', $pen_amt->pluck('nv_id'))
            
                ->sum('tbl_material.total_budget_both') +  DB::table('tbl_service')
                ->join('nvservicestatus', 'tbl_service.id', '=', 'nvservicestatus.service_id')
               
                 ->whereIn('tbl_service.nv_id', $totalId)
                 ->whereIn('nvservicestatus.nv_id', $pen_amt->pluck('nv_id'))
            
                ->sum('tbl_service.total_buget');
        
                $rejectedAmount = DB::table('tbl_material')
                ->join('nvservicestatus', 'tbl_material.id', '=', 'nvservicestatus.material_id')
                ->whereIn('nvservicestatus.nv_id', $latestData->pluck('nv_id'))
                ->where(function ($query) {
                    $query->where('nvservicestatus.work_rew3dep3_status', 2);
                })
                ->sum('tbl_material.total_budget_both') + DB::table('tbl_service')
                ->join('nvservicestatus', 'tbl_service.id', '=', 'nvservicestatus.service_id')
                ->whereIn('nvservicestatus.nv_id', $latestData->pluck('nv_id'))
                ->where(function ($query) {
                    $query->where('nvservicestatus.work_rew3dep3_status', 2);
                })
                ->sum('tbl_service.total_buget');
           
        
                $approvedAmount = DB::table('tbl_material')
               
                 ->whereIn('tbl_material.nv_id', $totalId)
                 ->whereIn('nvservicestatus.nv_id', $latestData->pluck('nv_id'))
                ->join('nvservicestatus', 'tbl_material.id', '=', 'nvservicestatus.material_id')
                ->where('nvservicestatus.ceo_status', 1)
                ->sum('tbl_material.total_budget_both') +  DB::table('tbl_service')
              
                ->whereIn('tbl_service.nv_id', $totalId)
                ->whereIn('nvservicestatus.nv_id', $latestData->pluck('nv_id'))
               ->join('nvservicestatus', 'tbl_service.id', '=', 'nvservicestatus.service_id')
               ->where('nvservicestatus.ceo_status', 1)
               ->sum('tbl_service.total_buget');

                if($company_id){
                    $totalNV = Nvsericestatus::whereIn("nv_id", $totalId)->where('work_rew3dep3_status', 1)->where('company_id',$company_id)->count();
                } else {
                    $totalNV = Nvsericestatus::whereIn("nv_id", $totalId)->where('work_rew3dep3_status', 1)->count();
                }
                if($company_id){
                    $rejectedNV = Nvsericestatus::whereIn("nv_id", $totalId)->where('work_rew3dep3_status', 2)->where('company_id',$company_id)->count();
                } else {
                    $rejectedNV = Nvsericestatus::whereIn("nv_id", $totalId)->where('work_rew3dep3_status', 2)->count();
                }
             
                if($company_id){
                $latestData = Nvsericestatus::whereIn("nv_id", $totalId)->where('company_id',$company_id)->get();
            }else {
                $latestData = Nvsericestatus::whereIn("nv_id", $totalId)->get();
            }
                if($company_id){
                    $approvedNV = $latestData->where('ceo_status', 1)->where('company_id',$company_id)->count();
                }else{
                    $approvedNV = $latestData->where('ceo_status', 1)->count();
                }
               
                $pendingNV = $latestData->filter(function ($data) {
                    return in_array($data->approver_status, [1]) &&
                        in_array($data->work_rew1dep3_status, [0]) &&
                        in_array($data->work_rew2dep3_status, [0]) &&
                        in_array($data->work_rew3dep3_status, [0]) &&
                        in_array($data->work_rew4dep3_status, [0]) &&
                        in_array($data->approverdep3_status, [0]) &&
                        (
                            (in_array($data->check_technology, [0]) && in_array($data->approverdep2_status, [0]))
                            ||
                            (in_array($data->check_technology, [1]) && in_array($data->approverdep2_status, [1]))
                        );
                })->count();
               
                $nvIds = NeedValidation::where("user_id", $Values)->pluck("id");
                if($fiscal_year){
                    $nv = NeedValidation::whereHas('service')->select('id')->where('fiscal_year', $fiscal_year)
                 
                    ->get();
                }else{
                    $nv = NeedValidation::whereHas('service')->select('id')->where('fiscal_year', $currentFinancialYear)
         
                ->get();
                }
               
                $nv_ids = $nv->pluck('id');
             
                 $nv_sm_data = Nvsericestatus::whereIn('nv_id', $nv_ids)->with(['service', 'material', 'user'])
                 ->where(function ($query) {
                    $query->where('approverdep2_status', 1)
                        ->orWhere('approver_status', 1)
                        ->where('check_technology', 0);
                })
                ->orderBy('id', 'asc')->get();
            
                $total20_status = $request->total;
                $pending20_status = $request->pending;
                $reject20_status = $request->rejected;

                if ($ceo_status !== null) {
                    $nv_sm_data = $nv_sm_data->where('ceo_status', $ceo_status);
                } elseif ($pending20_status !== null) {
                    $nv_sm_data = $nv_sm_data->where('work_rew3dep3_status', $pending20_status)
                    ->where('work_rew1dep3_status', 0)
                    ->where('work_rew2dep3_status', 0)
                    ->where('work_rew4dep3_status', 0);;

                } elseif ($reject20_status !== null) {
                    $nv_sm_data = $nv_sm_data->where('work_rew3dep3_status', $reject20_status);
                    
                }elseif ($total20_status !== null) {
                    $nv_sm_data = $nv_sm_data->where('work_rew3dep3_status', $total20_status);
                      
                }
                if($fiscal_year){
                    $BRPLnv = NeedValidation::where('company_id','6')->where('fiscal_year', $fiscal_year)->pluck("id");

                }else{
                    $BRPLnv = NeedValidation::where('company_id','6')->where('fiscal_year', $currentFinancialYear)->pluck("id");

                }
                $pendingAmountBRPL = DB::table('tbl_material')
                ->join('nvservicestatus', 'tbl_material.id', '=', 'nvservicestatus.material_id')
                ->whereIn('tbl_material.nv_id', $BRPLnv)
                ->whereIn('nvservicestatus.nv_id', $pen_amt->pluck('nv_id'))
              
                ->sum('tbl_material.total_budget_both')+DB::table('tbl_service')
                ->join('nvservicestatus', 'tbl_service.id', '=', 'nvservicestatus.service_id')
                ->whereIn('tbl_service.nv_id', $BRPLnv)
                ->whereIn('nvservicestatus.nv_id', $pen_amt->pluck('nv_id'))
             
                ->sum('tbl_service.total_buget');
              
        
                $rejectedAmountBRPL = DB::table('tbl_material')
                ->join('nvservicestatus', 'tbl_material.id', '=', 'nvservicestatus.material_id')
                ->whereIn('tbl_material.nv_id', $BRPLnv)
                ->where(function ($query) {
                    $query->where('nvservicestatus.work_rew3dep3_status', 2);
                })
                ->sum('tbl_material.total_budget_both')+DB::table('tbl_service')
                ->join('nvservicestatus', 'tbl_service.id', '=', 'nvservicestatus.service_id')
                ->whereIn('tbl_service.nv_id', $BRPLnv)
                ->where(function ($query) {
                    $query->where('nvservicestatus.work_rew3dep3_status', 2);
                })
                ->sum('tbl_service.total_buget');
           
        
                $approvedAmountBRPL = DB::table('tbl_material')
                ->join('nvservicestatus', 'tbl_material.id', '=', 'nvservicestatus.material_id')
                ->whereIn('tbl_material.nv_id', $BRPLnv)->where('nvservicestatus.ceo_status', 1)
                ->sum('tbl_material.total_budget_both')+DB::table('tbl_service')
                ->join('nvservicestatus', 'tbl_service.id', '=', 'nvservicestatus.service_id')
                ->whereIn('tbl_service.nv_id', $BRPLnv)->where('nvservicestatus.ceo_status', 1)
                ->sum('tbl_service.total_buget');
                $fileDataBRPL =Nvsericestatus::
                select(
                    DB::raw('MONTH(created_at) as month'),
                    DB::raw('SUM(CASE WHEN ceo_status = "1" THEN 1 ELSE 0 END) as approved_count'),
                    DB::raw('SUM(CASE WHEN ( work_rew3dep3_status = "2")  THEN 1 ELSE 0 END ) as rejected_count'),
                    DB::raw('SUM(CASE WHEN ( work_rew1dep3_status = "0" AND work_rew2dep3_status = "0" AND work_rew3dep3_status = "0" AND work_rew4dep3_status = "0" AND approverdep3_status = "0")  THEN 1 ELSE 0 END) as pending_count'),
                  
                )
                ->whereIn('nv_id', $BRPLnv)->with(['service', 'material', 'user'])
                ->where(function ($query) {
                    $query->where('approverdep2_status', 1)
                        ->orWhere('approver_status', 1)
                        ->where('check_technology', 0);
                })
             
                ->whereYear('created_at', Carbon::now()->year)
                ->groupBy('month')
                ->orderBy('month')
                ->get();
        
            $BRPLlabels = [];
            $BRPLapprovedData = [];
            $BRPLrejectedData = [];
            $BRPLpendingData = [];
        
            foreach ($fileDataBRPL as $dataPointBRPL) {
                $monthBRPL = Carbon::createFromFormat('!m', $dataPointBRPL->month)->format('F');
        
                $BRPLlabels[] = $monthBRPL;
                $BRPLapprovedData[] = $dataPointBRPL->approved_count;
                $BRPLrejectedData[] = $dataPointBRPL->rejected_count;
                $BRPLpendingData[] = $dataPointBRPL->pending_count;
                
            }
            if($fiscal_year){
                $BYPLnv = NeedValidation::where('company_id','5')->where('fiscal_year', $fiscal_year)->pluck("id");

            }else{
                $BYPLnv = NeedValidation::where('company_id','5')->where('fiscal_year', $currentFinancialYear)->pluck("id");

            }
          
            $pendingAmountBYPL = DB::table('tbl_material')
            ->join('nvservicestatus', 'tbl_material.id', '=', 'nvservicestatus.material_id')
            ->whereIn('tbl_material.nv_id', $BYPLnv)
            ->whereIn('nvservicestatus.nv_id', $pen_amt->pluck('nv_id'))
           
            ->sum('tbl_material.total_budget_both')+DB::table('tbl_service')
            ->join('nvservicestatus', 'tbl_service.id', '=', 'nvservicestatus.service_id')
            ->whereIn('tbl_service.nv_id', $BYPLnv)
            ->whereIn('nvservicestatus.nv_id', $pen_amt->pluck('nv_id'))
           
            ->sum('tbl_service.total_buget');
           
    
            $rejectedAmountBYPL = DB::table('tbl_material')
            ->join('nvservicestatus', 'tbl_material.id', '=', 'nvservicestatus.material_id')
            ->whereIn('tbl_material.nv_id', $BYPLnv)
            ->where(function ($query) {
                $query->where('nvservicestatus.work_rew3dep3_status', 2);
            })
            ->sum('tbl_material.total_budget_both')+DB::table('tbl_service')
            ->join('nvservicestatus', 'tbl_service.id', '=', 'nvservicestatus.service_id')
            ->whereIn('tbl_service.nv_id', $BYPLnv)
            ->where(function ($query) {
                $query->where('nvservicestatus.work_rew3dep3_status', 2);
            })
            ->sum('tbl_service.total_buget');
       
    
            $approvedAmountBYPL = DB::table('tbl_material')
            ->join('nvservicestatus', 'tbl_material.id', '=', 'nvservicestatus.material_id')
            ->whereIn('tbl_material.nv_id', $BYPLnv)->where('nvservicestatus.ceo_status', 1)
            ->sum('tbl_material.total_budget_both')+DB::table('tbl_service')
            ->join('nvservicestatus', 'tbl_service.id', '=', 'nvservicestatus.service_id')
            ->whereIn('tbl_service.nv_id', $BYPLnv)->where('nvservicestatus.ceo_status', 1)
            ->sum('tbl_service.total_buget');
             $fileDataBYPL =Nvsericestatus::
            select(
                DB::raw('MONTH(created_at) as month'),
                DB::raw('SUM(CASE WHEN ceo_status = "1" THEN 1 ELSE 0 END) as approved_count'),
                DB::raw('SUM(CASE WHEN ( work_rew3dep3_status = "2")  THEN 1 ELSE 0 END) as rejected_count'),
                DB::raw('SUM(CASE WHEN ( work_rew1dep3_status = "0" AND work_rew2dep3_status = "0" AND work_rew3dep3_status = "0" AND work_rew4dep3_status = "0" AND approverdep3_status = "0")  THEN 1 ELSE 0 END) as pending_count'),
                
            ) 
            ->whereIn('nv_id', $BYPLnv)->with(['service', 'material', 'user'])
            ->where(function ($query) {
                $query->where('approverdep2_status', 1)
                    ->orWhere('approver_status', 1)
                    ->where('check_technology', 0);
            })
         
            ->whereYear('created_at', Carbon::now()->year)
            ->groupBy('month')
            ->orderBy('month')
            ->get();
           
        $BYPLlabels = [];
        $BYPLapprovedData = [];
        $BYPLrejectedData = [];
        $BYPLpendingData = [];
    
        foreach ($fileDataBYPL as $dataPointBYPL) {
            $monthBYPL = Carbon::createFromFormat('!m', $dataPointBYPL->month)->format('F');
    
            $BYPLlabels[] = $monthBYPL;
            $BYPLapprovedData[] = $dataPointBYPL->approved_count;
            $BYPLrejectedData[] = $dataPointBYPL->rejected_count;
            $BYPLpendingData[] = $dataPointBYPL->pending_count;
            
        }
                return view("admin.dashboard", compact("approvedAmount","currentFinancialYear","nextFinancialYear","nextToNextFinancialYear", "rejectedAmount", "pendingAmount", "totalAmount","company", "company_id" , "nv_sm_data", "totalNV", "approvedNV", "rejectedNV", "pendingNV" , "hodApproval", "cpmgApproval", "btApproval", "ceoApproval"             ,'BRPLlabels','BRPLapprovedData','BRPLrejectedData','BRPLpendingData','BYPLlabels','BYPLapprovedData','BYPLrejectedData','BYPLpendingData','cesApproval','ceonominee1Approval','ceonominee2Approval','approvedAmountBYPL','approvedAmountBRPL','pendingAmountBYPL','pendingAmountBRPL','rejectedAmountBYPL','rejectedAmountBRPL','groupcioApproval'));
            } elseif ($id3->work_rew4 == $user->id) {
                $Values = [$user->id, $id3->work_rew1, $id3->work_rew2, $id3->work_rew3];
               
                if($fiscal_year){
                    $totalId = NeedValidation::where('fiscal_year', $fiscal_year)->pluck('id');

                }else{
                    $totalId = NeedValidation::where('fiscal_year', $currentFinancialYear)->pluck('id');

                }
               
                $totalAmount = DB::table('tbl_material')
                ->join('nvservicestatus', 'tbl_material.id', '=', 'nvservicestatus.material_id')
              
                 ->whereIn('tbl_material.nv_id', $totalId)
                 ->whereIn('nvservicestatus.nv_id', $latestData->pluck('nv_id'))
                ->where('nvservicestatus.work_rew4dep3_status', 1)
                ->sum('tbl_material.total_budget_both') + DB::table('tbl_service')
                ->join('nvservicestatus', 'tbl_service.id', '=', 'nvservicestatus.service_id')
               
                 ->whereIn('tbl_service.nv_id', $totalId)
                 ->whereIn('nvservicestatus.nv_id', $latestData->pluck('nv_id'))
                ->where('nvservicestatus.work_rew4dep3_status', 1)
                ->sum('tbl_service.total_buget');
              
                $pen_amt= $latestData->filter(function ($data) {
                    return in_array($data->approver_status, [1]) &&
                        in_array($data->work_rew1dep3_status, [0]) &&
                        in_array($data->work_rew2dep3_status, [0]) &&
                        in_array($data->work_rew3dep3_status, [0]) &&
                        in_array($data->work_rew4dep3_status, [0]) &&
                        in_array($data->approverdep3_status, [0]) &&
                        (
                            (in_array($data->check_technology, [0]) && in_array($data->approverdep2_status, [0]))
                            ||
                            (in_array($data->check_technology, [1]) && in_array($data->approverdep2_status, [1]))
                        );
                });
    
                $pendingAmount = DB::table('tbl_material')
                ->join('nvservicestatus', 'tbl_material.id', '=', 'nvservicestatus.material_id')
               
                 ->whereIn('tbl_material.nv_id', $totalId)
                 ->whereIn('nvservicestatus.nv_id', $pen_amt->pluck('nv_id'))
               
                ->sum('tbl_material.total_budget_both') + DB::table('tbl_service')
                ->join('nvservicestatus', 'tbl_service.id', '=', 'nvservicestatus.service_id')
                
                 ->whereIn('tbl_service.nv_id', $totalId)
                 ->whereIn('nvservicestatus.nv_id', $pen_amt->pluck('nv_id'))
              
                ->sum('tbl_service.total_buget');
        
                $rejectedAmount = DB::table('tbl_material')
                ->join('nvservicestatus', 'tbl_material.id', '=', 'nvservicestatus.material_id')
                ->whereIn('nvservicestatus.nv_id', $latestData->pluck('nv_id'))
                ->where(function ($query) {
                    $query->where('nvservicestatus.work_rew4dep3_status', 2);
                })
                ->sum('tbl_material.total_budget_both') + DB::table('tbl_service')
                ->join('nvservicestatus', 'tbl_service.id', '=', 'nvservicestatus.service_id')
                ->whereIn('nvservicestatus.nv_id', $latestData->pluck('nv_id'))
                ->where(function ($query) {
                    $query->where('nvservicestatus.work_rew4dep3_status', 2);
                })
                ->sum('tbl_service.total_buget');
           
        
                $approvedAmount = DB::table('tbl_material')
                
                 ->whereIn('tbl_material.nv_id', $totalId)
                 ->whereIn('nvservicestatus.nv_id', $latestData->pluck('nv_id'))
                ->join('nvservicestatus', 'tbl_material.id', '=', 'nvservicestatus.material_id')
                ->where('nvservicestatus.ceo_status', 1)
                ->sum('tbl_material.total_budget_both') + DB::table('tbl_service')
               
                ->whereIn('tbl_service.nv_id', $totalId)
                ->whereIn('nvservicestatus.nv_id', $latestData->pluck('nv_id'))
               ->join('nvservicestatus', 'tbl_service.id', '=', 'nvservicestatus.service_id')
               ->where('nvservicestatus.ceo_status', 1)
               ->sum('tbl_service.total_buget');

                if($company_id){
                    $totalNV = Nvsericestatus::whereIn("nv_id", $totalId)->where('work_rew4dep3_status', 1)->where('company_id',$company_id)->count();
                } else {
                    $totalNV = Nvsericestatus::whereIn("nv_id", $totalId)->where('work_rew4dep3_status', 1)->count();
                }
                if($company_id){
                    $rejectedNV = Nvsericestatus::whereIn("nv_id", $totalId)->where('work_rew4dep3_status', 2)->where('company_id',$company_id)->count();
                } else {
                    $rejectedNV = Nvsericestatus::whereIn("nv_id", $totalId)->where('work_rew4dep3_status', 2)->count();
                }
          
                if($company_id){
                $latestData = Nvsericestatus::whereIn("nv_id", $totalId)->where('company_id',$company_id)->get();
            }else {
                $latestData = Nvsericestatus::whereIn("nv_id", $totalId)->get();
            }
                if($company_id){
                    $approvedNV = $latestData->where('ceo_status', 1)->where('company_id',$company_id)->count();
                }else{
                    $approvedNV = $latestData->where('ceo_status', 1)->count();
                }
             
                $pendingNV = $latestData->filter(function ($data) {
                    return in_array($data->approver_status, [1]) &&
                        in_array($data->work_rew1dep3_status, [0]) &&
                        in_array($data->work_rew2dep3_status, [0]) &&
                        in_array($data->work_rew3dep3_status, [0]) &&
                        in_array($data->work_rew4dep3_status, [0]) &&
                        in_array($data->approverdep3_status, [0]) &&
                        (
                            (in_array($data->check_technology, [0]) && in_array($data->approverdep2_status, [0]))
                            ||
                            (in_array($data->check_technology, [1]) && in_array($data->approverdep2_status, [1]))
                        );
                })->count();
             
                $nvIds = NeedValidation::where("user_id", $Values)->pluck("id");
                if($fiscal_year){
                    $nv = NeedValidation::whereHas('service')->select('id')->where('fiscal_year', $fiscal_year)
                  
                    ->get();

                }else{
                    $nv = NeedValidation::whereHas('service')->select('id')->where('fiscal_year', $currentFinancialYear)
              
                ->get();

                }
               
                $nv_ids = $nv->pluck('id');
            
                    $nv_sm_data = Nvsericestatus::whereIn('nv_id', $nv_ids)->with(['service', 'material', 'user'])
                    ->where(function ($query) {
                        $query->where('approverdep2_status', 1)
                            ->orWhere('approver_status', 1)
                            ->where('check_technology', 0);
                    })
                ->orderBy('id', 'asc')->get();
         
                $total21_status = $request->total;
                $pending21_status = $request->pending;
                $reject21_status = $request->rejected;
                if ($ceo_status !== null) {
                    $nv_sm_data = $nv_sm_data->where('ceo_status', $ceo_status);
                } elseif ($pending21_status !== null) {
                    $nv_sm_data = $nv_sm_data->where('work_rew4dep3_status', $pending21_status)
                    ->where('work_rew2dep3_status', 0)
                    ->where('work_rew3dep3_status', 0)
                    ->where('work_rew1dep3_status', 0);

                } elseif ($reject21_status !== null) {
                    $nv_sm_data = $nv_sm_data->where('work_rew4dep3_status', $reject21_status);
                    
                }elseif ($total21_status !== null) {
                    $nv_sm_data = $nv_sm_data->where('work_rew4dep3_status', $total21_status);
                      
                }
                if($fiscal_year){
                    $BRPLnv = NeedValidation::where('company_id','6')->where('fiscal_year', $fiscal_year)->pluck("id");

                }else{
                    $BRPLnv = NeedValidation::where('company_id','6')->where('fiscal_year', $currentFinancialYear)->pluck("id");

                }
              
                $pendingAmountBRPL = DB::table('tbl_material')
                ->join('nvservicestatus', 'tbl_material.id', '=', 'nvservicestatus.material_id')
                ->whereIn('tbl_material.nv_id', $BRPLnv)
                ->whereIn('nvservicestatus.nv_id', $pen_amt->pluck('nv_id'))
              
                ->sum('tbl_material.total_budget_both')+DB::table('tbl_service')
                ->join('nvservicestatus', 'tbl_service.id', '=', 'nvservicestatus.service_id')
                ->whereIn('tbl_service.nv_id', $BRPLnv)
                ->whereIn('nvservicestatus.nv_id', $pen_amt->pluck('nv_id'))
              
                ->sum('tbl_service.total_buget');
               
        
                $rejectedAmountBRPL = DB::table('tbl_material')
                ->join('nvservicestatus', 'tbl_material.id', '=', 'nvservicestatus.material_id')
                ->whereIn('tbl_material.nv_id', $BRPLnv)
                ->where(function ($query) {
                    $query->where('nvservicestatus.work_rew4dep3_status', 2);
                })
                ->sum('tbl_material.total_budget_both')+DB::table('tbl_service')
                ->join('nvservicestatus', 'tbl_service.id', '=', 'nvservicestatus.service_id')
                ->whereIn('tbl_service.nv_id', $BRPLnv)
                ->where(function ($query) {
                    $query->where('nvservicestatus.work_rew4dep3_status', 2);
                })
                ->sum('tbl_service.total_buget');
           
        
                $approvedAmountBRPL = DB::table('tbl_material')
                ->join('nvservicestatus', 'tbl_material.id', '=', 'nvservicestatus.material_id')
                ->whereIn('tbl_material.nv_id', $BRPLnv)->where('nvservicestatus.ceo_status', 1)
                ->sum('tbl_material.total_budget_both')+DB::table('tbl_service')
                ->join('nvservicestatus', 'tbl_service.id', '=', 'nvservicestatus.service_id')
                ->whereIn('tbl_service.nv_id', $BRPLnv)->where('nvservicestatus.ceo_status', 1)
                ->sum('tbl_service.total_buget');
                $fileDataBRPL =Nvsericestatus::
                select(
                    DB::raw('MONTH(created_at) as month'),
                    DB::raw('SUM(CASE WHEN ceo_status = "1" THEN 1 ELSE 0 END) as approved_count'),
                    DB::raw('SUM(CASE WHEN ( work_rew4dep3_status = "2")  THEN 1 ELSE 0 END ) as rejected_count'),
                    DB::raw('SUM(CASE WHEN ( work_rew1dep3_status = "0" AND work_rew2dep3_status = "0" AND work_rew3dep3_status = "0" AND work_rew4dep3_status = "0" AND approverdep3_status = "0")  THEN 1 ELSE 0 END) as pending_count'),
                  
                )
                ->whereIn('nv_id', $BRPLnv)->with(['service', 'material', 'user'])
                ->where(function ($query) {
                    $query->where('approverdep2_status', 1)
                        ->orWhere('approver_status', 1)
                        ->where('check_technology', 0);
                })
              
                ->whereYear('created_at', Carbon::now()->year)
                ->groupBy('month')
                ->orderBy('month')
                ->get();
       
            $BRPLlabels = [];
            $BRPLapprovedData = [];
            $BRPLrejectedData = [];
            $BRPLpendingData = [];
        
            foreach ($fileDataBRPL as $dataPointBRPL) {
                $monthBRPL = Carbon::createFromFormat('!m', $dataPointBRPL->month)->format('F');
        
                $BRPLlabels[] = $monthBRPL;
                $BRPLapprovedData[] = $dataPointBRPL->approved_count;
                $BRPLrejectedData[] = $dataPointBRPL->rejected_count;
                $BRPLpendingData[] = $dataPointBRPL->pending_count;
                 
            }
            if($fiscal_year){
                $BYPLnv = NeedValidation::where('company_id','5')->where('fiscal_year', $fiscal_year)->pluck("id");

            }else{
                $BYPLnv = NeedValidation::where('company_id','5')->where('fiscal_year', $currentFinancialYear)->pluck("id");

            }
            $pendingAmountBYPL = DB::table('tbl_material')
            ->join('nvservicestatus', 'tbl_material.id', '=', 'nvservicestatus.material_id')
            ->whereIn('tbl_material.nv_id', $BYPLnv)
            ->whereIn('nvservicestatus.nv_id', $pen_amt->pluck('nv_id'))
          
            ->sum('tbl_material.total_budget_both')+DB::table('tbl_service')
            ->join('nvservicestatus', 'tbl_service.id', '=', 'nvservicestatus.service_id')
            ->whereIn('tbl_service.nv_id', $BYPLnv)
            ->whereIn('nvservicestatus.nv_id', $pen_amt->pluck('nv_id'))
         
            ->sum('tbl_service.total_buget');
           
    
            $rejectedAmountBYPL = DB::table('tbl_material')
            ->join('nvservicestatus', 'tbl_material.id', '=', 'nvservicestatus.material_id')
            ->whereIn('tbl_material.nv_id', $BYPLnv)
            ->where(function ($query) {
                $query->where('nvservicestatus.work_rew4dep3_status', 2);
            })
            ->sum('tbl_material.total_budget_both')+DB::table('tbl_service')
            ->join('nvservicestatus', 'tbl_service.id', '=', 'nvservicestatus.service_id')
            ->whereIn('tbl_service.nv_id', $BYPLnv)
            ->where(function ($query) {
                $query->where('nvservicestatus.work_rew4dep3_status', 2);
            })
            ->sum('tbl_service.total_buget');
       
    
            $approvedAmountBYPL = DB::table('tbl_material')
            ->join('nvservicestatus', 'tbl_material.id', '=', 'nvservicestatus.material_id')
            ->whereIn('tbl_material.nv_id', $BYPLnv)->where('nvservicestatus.ceo_status', 1)
            ->sum('tbl_material.total_budget_both')+DB::table('tbl_service')
            ->join('nvservicestatus', 'tbl_service.id', '=', 'nvservicestatus.service_id')
            ->whereIn('tbl_service.nv_id', $BYPLnv)->where('nvservicestatus.ceo_status', 1)
            ->sum('tbl_service.total_buget');
             $fileDataBYPL =Nvsericestatus::
            select(
                DB::raw('MONTH(created_at) as month'),
                DB::raw('SUM(CASE WHEN ceo_status = "1" THEN 1 ELSE 0 END) as approved_count'),
                DB::raw('SUM(CASE WHEN ( work_rew4dep3_status = "2")  THEN 1 ELSE 0 END) as rejected_count'),
                DB::raw('SUM(CASE WHEN ( work_rew1dep3_status = "0" AND work_rew2dep3_status = "0" AND work_rew3dep3_status = "0" AND work_rew4dep3_status = "0" AND approverdep3_status = "0" )  THEN 1 ELSE 0 END) as pending_count'),
               
            ) 
            ->whereIn('nv_id', $BYPLnv)->with(['service', 'material', 'user'])
            ->where(function ($query) {
                $query->where('approverdep2_status', 1)
                    ->orWhere('approver_status', 1)
                    ->where('check_technology', 0);
            })
      
            ->whereYear('created_at', Carbon::now()->year)
            ->groupBy('month')
            ->orderBy('month')
            ->get();
      
        $BYPLlabels = [];
        $BYPLapprovedData = [];
        $BYPLrejectedData = [];
        $BYPLpendingData = [];
    
        foreach ($fileDataBYPL as $dataPointBYPL) {
            $monthBYPL = Carbon::createFromFormat('!m', $dataPointBYPL->month)->format('F');
    
            $BYPLlabels[] = $monthBYPL;
            $BYPLapprovedData[] = $dataPointBYPL->approved_count;
            $BYPLrejectedData[] = $dataPointBYPL->rejected_count;
            $BYPLpendingData[] = $dataPointBYPL->pending_count;
          
        }
                return view("admin.dashboard", compact("approvedAmount","currentFinancialYear","nextFinancialYear","nextToNextFinancialYear", "rejectedAmount", "pendingAmount", "totalAmount","company", "company_id" , "nv_sm_data", "totalNV", "approvedNV", "rejectedNV", "pendingNV" , "hodApproval", "cpmgApproval", "btApproval", "ceoApproval"             ,'BRPLlabels','BRPLapprovedData','BRPLrejectedData','BRPLpendingData','BYPLlabels','BYPLapprovedData','BYPLrejectedData','BYPLpendingData','cesApproval','ceonominee1Approval','ceonominee2Approval','approvedAmountBYPL','approvedAmountBRPL','pendingAmountBYPL','pendingAmountBRPL','rejectedAmountBYPL','rejectedAmountBRPL','groupcioApproval'));
            } elseif ($id3->approver == $user->id) {
                $Values = [$user->id, $id3->work_rew1, $id3->work_rew2, $id3->work_rew3, $id3->work_rew4];
              
                    if($fiscal_year){
                        $totalId = NeedValidation::where('fiscal_year', $fiscal_year)->pluck('id');

                    }else{
                        $totalId = NeedValidation::where('fiscal_year', $currentFinancialYear)->pluck('id');

                    }
               
                $totalAmount = DB::table('tbl_material')
                ->join('nvservicestatus', 'tbl_material.id', '=', 'nvservicestatus.material_id')
              
                 ->whereIn('tbl_material.nv_id', $totalId)
                 ->whereIn('nvservicestatus.nv_id', $latestData->pluck('nv_id'))
                ->where('nvservicestatus.approverdep3_status', 1)
                ->sum('tbl_material.total_budget_both') +  DB::table('tbl_service')
                ->join('nvservicestatus', 'tbl_service.id', '=', 'nvservicestatus.service_id')
              
                 ->whereIn('tbl_service.nv_id', $totalId)
                 ->whereIn('nvservicestatus.nv_id', $latestData->pluck('nv_id'))
                ->where('nvservicestatus.approverdep3_status', 1)
                ->sum('tbl_service.total_buget');
              
                if(!empty($id3->work_rew1) || !empty($id3->work_rew2)|| !empty($id3->work_rew3)|| !empty($id3->work_rew4)){
                $pen_amt = $latestData->filter(function ($data) {
                    return in_array($data->cpmg_status, [1]) &&
                          in_array($data->cto_status, [0,1]) &&
                       ( in_array($data->work_rew1dep3_status, [1])||in_array($data->work_rew2dep3_status, [1])||in_array($data->work_rew3dep3_status, [1])||in_array($data->work_rew4dep3_status, [1])) &&
                        in_array($data->approverdep3_status, [0]);
                });
            }elseif(empty($id3->work_rew1) && empty($id3->work_rew2) && empty($id3->work_rew3) && empty($id3->work_rew4)){
                $pen_amt = $latestData->filter(function ($data) {
                    return in_array($data->approver_status, [1]) &&
                       in_array($data->approverdep3_status, [0]) &&
                        in_array($data->work_rew1dep3_status, [0]) &&
                        in_array($data->work_rew2dep3_status, [0]) &&
                        in_array($data->work_rew3dep3_status, [0]) &&
                        in_array($data->work_rew4dep3_status, [0]) &&
                        (
                            (in_array($data->check_technology, [0]) && in_array($data->approverdep2_status, [0]))
                            ||
                            (in_array($data->check_technology, [1]) && in_array($data->approverdep2_status, [1]))
                        );
                });
            }
                $pendingAmount = DB::table('tbl_material')
                ->join('nvservicestatus', 'tbl_material.id', '=', 'nvservicestatus.material_id')
              
                 ->whereIn('tbl_material.nv_id', $totalId)
                 ->whereIn('nvservicestatus.nv_id', $pen_amt->pluck('nv_id'))
              
                ->sum('tbl_material.total_budget_both') + DB::table('tbl_service')
                ->join('nvservicestatus', 'tbl_service.id', '=', 'nvservicestatus.service_id')
              
                 ->whereIn('tbl_service.nv_id', $totalId)
                 ->whereIn('nvservicestatus.nv_id', $pen_amt->pluck('nv_id'))
              
                ->sum('tbl_service.total_buget');
        
                $rejectedAmount = DB::table('tbl_material')
                ->join('nvservicestatus', 'tbl_material.id', '=', 'nvservicestatus.material_id')
                ->whereIn('nvservicestatus.nv_id', $latestData->pluck('nv_id'))
                ->where(function ($query) {
                    $query->where('nvservicestatus.approverdep3_status', 2);
                })
                ->sum('tbl_material.total_budget_both') + DB::table('tbl_service')
                ->join('nvservicestatus', 'tbl_service.id', '=', 'nvservicestatus.service_id')
                ->whereIn('nvservicestatus.nv_id', $latestData->pluck('nv_id'))
                ->where(function ($query) {
                    $query->where('nvservicestatus.approverdep3_status', 2);
                })
                ->sum('tbl_service.total_buget');
           
        
                $approvedAmount = DB::table('tbl_material')
               
                 ->whereIn('tbl_material.nv_id', $totalId)
                 ->whereIn('nvservicestatus.nv_id', $latestData->pluck('nv_id'))
                ->join('nvservicestatus', 'tbl_material.id', '=', 'nvservicestatus.material_id')
                ->where('nvservicestatus.ceo_status', 1)
                ->sum('tbl_material.total_budget_both') +  DB::table('tbl_service')
              
                ->whereIn('tbl_service.nv_id', $totalId)
                ->whereIn('nvservicestatus.nv_id', $latestData->pluck('nv_id'))
               ->join('nvservicestatus', 'tbl_service.id', '=', 'nvservicestatus.service_id')
               ->where('nvservicestatus.ceo_status', 1)
               ->sum('tbl_service.total_buget');


                if($company_id){
                    $totalNV = Nvsericestatus::whereIn("nv_id", $totalId)->where('approverdep3_status', 1)->where('company_id',$company_id)->count();
                } else {
                    $totalNV = Nvsericestatus::whereIn("nv_id", $totalId)->where('approverdep3_status', 1)->count();
                }
                if($company_id){
                    $rejectedNV = Nvsericestatus::whereIn("nv_id", $totalId)->where('approverdep3_status', 2)->where('company_id',$company_id)->count();
                } else {
                    $rejectedNV = Nvsericestatus::whereIn("nv_id", $totalId)->where('approverdep3_status', 2)->count();
                }
            
                if($company_id){
                $latestData = Nvsericestatus::whereIn("nv_id", $totalId)->where('company_id',$company_id)->get();
            }else {
                $latestData = Nvsericestatus::whereIn("nv_id", $totalId)->get();
            }
                if($company_id){
                    $approvedNV = $latestData->where('ceo_status', 1)->where('company_id',$company_id)->count();
                }else{
                    $approvedNV = $latestData->where('ceo_status', 1)->count();
                }
            


                if(!empty($id3->work_rew1) || !empty($id3->work_rew2)|| !empty($id3->work_rew3)|| !empty($id3->work_rew4)){
                    $pendingNV = $latestData->filter(function ($data) {
                        return in_array($data->cpmg_status, [1]) &&
                              in_array($data->cto_status, [0,1]) &&
                           ( in_array($data->work_rew1dep3_status, [1])||in_array($data->work_rew2dep3_status, [1])||in_array($data->work_rew3dep3_status, [1])||in_array($data->work_rew4dep3_status, [1])) &&
                            in_array($data->approverdep3_status, [0]);
                    })->count();
                }elseif(empty($id3->work_rew1) && empty($id3->work_rew2) && empty($id3->work_rew3) && empty($id3->work_rew4)){
                    $pendingNV = $latestData->filter(function ($data) {
                        return in_array($data->approver_status, [1]) &&
                           in_array($data->approverdep3_status, [0]) &&
                            in_array($data->work_rew1dep3_status, [0]) &&
                            in_array($data->work_rew2dep3_status, [0]) &&
                            in_array($data->work_rew3dep3_status, [0]) &&
                            in_array($data->work_rew4dep3_status, [0]) &&
                            (
                                (in_array($data->check_technology, [0]) && in_array($data->approverdep2_status, [0]))
                                ||
                                (in_array($data->check_technology, [1]) && in_array($data->approverdep2_status, [1]))
                            );
                    })->count();
                }
             
               
                $nvIds = NeedValidation::where("user_id", $Values)->pluck("id");
                if($fiscal_year){
                    $nv = NeedValidation::whereHas('service')->select('id')->where('fiscal_year', $fiscal_year)
                  
                    ->get();

                }else{
                    $nv = NeedValidation::whereHas('service')->select('id')->where('fiscal_year', $currentFinancialYear)
            
                    ->get();

                }
              
                $nv_ids = $nv->pluck('id');
             
                if(!empty($id3->work_rew1) || !empty($id3->work_rew2)|| !empty($id3->work_rew3)|| !empty($id3->work_rew4)){
                    $nv_sm_data = Nvsericestatus::whereIn('nv_id', $nv_ids)->with(['service', 'material', 'user'])
                    ->where(function ($query) {
                        $query->where(function ($subQuery) {
                            $subQuery->orWhere('work_rew1dep3_status', 1)
                                ->orWhere('work_rew2dep3_status', 1)
                                ->orWhere('work_rew3dep3_status', 1)
                                ->orWhere('work_rew4dep3_status', 1);
                        })->where(function ($subQuery) {
                            $subQuery->where('work_rew1dep3_status', '!=', 2)
                                ->orWhere('work_rew2dep3_status', '!=', 2)
                                ->orWhere('work_rew3dep3_status', '!=', 2)
                                ->orWhere('work_rew4dep3_status', '!=', 2);
                        })->whereIn('check_technology', [0, 1]);
                    })
                ->orderBy('id', 'asc')->get();
                    }elseif(empty($id3->work_rew1) && empty($id3->work_rew2) && empty($id3->work_rew3) && empty($id3->work_rew4)){
                        $nv_sm_data = Nvsericestatus::whereIn('nv_id', $nv_ids)->with(['service', 'material', 'user'])
                        ->where(function ($query) {
                            $query->where('approverdep2_status', 1)
                                ->orWhere('approver_status', 1)
                                ->where('check_technology', 0);
                        })
                    ->orderBy('id', 'asc')->get();
                    }
             
                $total22_status = $request->total;
                $pending22_status = $request->pending;
                $reject22_status = $request->rejected;

             if ($ceo_status !== null) {
                    $nv_sm_data = $nv_sm_data->where('ceo_status', $ceo_status);
                } elseif ($pending22_status !== null) {
                    $nv_sm_data = $nv_sm_data->where('approverdep3_status', $pending22_status)
                    
                    
                        ->whereIn('hod_status', [ 1])
                        ->whereIn('approver_status', [ 1]) ;   
                       

                } elseif ($reject22_status !== null) {
                    $nv_sm_data = $nv_sm_data->where('approverdep3_status', $reject22_status)
             
                    ;
                    
                }elseif ($total22_status !== null) {
                    $nv_sm_data = $nv_sm_data->where('approverdep3_status', $total22_status);
                      
                }
                if($fiscal_year){
                    $BRPLnv = NeedValidation::where('company_id','6')->where('fiscal_year', $fiscal_year)->pluck("id");


                }else{
                    $BRPLnv = NeedValidation::where('company_id','6')->where('fiscal_year', $currentFinancialYear)->pluck("id");


                }
                $pendingAmountBRPL = DB::table('tbl_material')
                ->join('nvservicestatus', 'tbl_material.id', '=', 'nvservicestatus.material_id')
                ->whereIn('tbl_material.nv_id', $BRPLnv)
                ->whereIn('nvservicestatus.nv_id', $pen_amt->pluck('nv_id'))
             
                ->sum('tbl_material.total_budget_both')+DB::table('tbl_service')
                ->join('nvservicestatus', 'tbl_service.id', '=', 'nvservicestatus.service_id')
                ->whereIn('tbl_service.nv_id', $BRPLnv)
                ->whereIn('nvservicestatus.nv_id', $pen_amt->pluck('nv_id'))
      
                ->sum('tbl_service.total_buget');
            
        
                $rejectedAmountBRPL = DB::table('tbl_material')
                ->join('nvservicestatus', 'tbl_material.id', '=', 'nvservicestatus.material_id')
                ->whereIn('tbl_material.nv_id', $BRPLnv)
                ->where(function ($query) {
                    $query->where('nvservicestatus.approverdep3_status', 2);
                })
                ->sum('tbl_material.total_budget_both')+DB::table('tbl_service')
                ->join('nvservicestatus', 'tbl_service.id', '=', 'nvservicestatus.service_id')
                ->whereIn('tbl_service.nv_id', $BRPLnv)
                ->where(function ($query) {
                    $query->where('nvservicestatus.approverdep3_status', 2);
                })
                ->sum('tbl_service.total_buget');
           
        
                $approvedAmountBRPL = DB::table('tbl_material')
                ->join('nvservicestatus', 'tbl_material.id', '=', 'nvservicestatus.material_id')
                ->whereIn('tbl_material.nv_id', $BRPLnv)->where('nvservicestatus.ceo_status', 1)
                ->sum('tbl_material.total_budget_both')+DB::table('tbl_service')
                ->join('nvservicestatus', 'tbl_service.id', '=', 'nvservicestatus.service_id')
                ->whereIn('tbl_service.nv_id', $BRPLnv)->where('nvservicestatus.ceo_status', 1)
                ->sum('tbl_service.total_buget');


                if(!empty($id3->work_rew1) || !empty($id3->work_rew2)|| !empty($id3->work_rew3)|| !empty($id3->work_rew4)){
                    
                $fileDataBRPL =Nvsericestatus::
                select(
                    DB::raw('MONTH(created_at) as month'),
                    DB::raw('SUM(CASE WHEN ceo_status = "1" THEN 1 ELSE 0 END) as approved_count'),
                    DB::raw('SUM(CASE WHEN ( approverdep3_status = "2")  THEN 1 ELSE 0 END ) as rejected_count'),
                    DB::raw('SUM(CASE WHEN ( approverdep3_status = "0" AND (work_rew1dep3_status = "1" OR work_rew2dep3_status = "1" OR work_rew3dep3_status = "1" OR work_rew4dep3_status = "1")) THEN 1 ELSE 0 END) as pending_count'),
                   
                )
                ->whereIn('nv_id', $BRPLnv)->with(['service', 'material', 'user'])
                ->where('approver_status', 1)
                ->whereIn('check_technology', [0, 1])
                ->whereYear('created_at', Carbon::now()->year)
                ->groupBy('month')
                ->orderBy('month')
                ->get();
                }elseif(empty($id3->work_rew1) && empty($id3->work_rew2) && empty($id3->work_rew3) && empty($id3->work_rew4)){
                   
                $fileDataBRPL =Nvsericestatus::
                select(
                    DB::raw('MONTH(created_at) as month'),
                    DB::raw('SUM(CASE WHEN ceo_status = "1" THEN 1 ELSE 0 END) as approved_count'),
                    DB::raw('SUM(CASE WHEN ( approverdep3_status = "2")  THEN 1 ELSE 0 END ) as rejected_count'),
                    DB::raw('SUM(CASE WHEN (approverdep3_status = "0" AND work_rew1dep3_status = "0" AND work_rew2dep3_status = "0" AND work_rew3dep3_status = "0" AND work_rew4dep3_status = "0" )  THEN 1 ELSE 0 END) as pending_count'),
                    
                )
                ->whereIn('nv_id', $BRPLnv)->with(['service', 'material', 'user'])
                ->where('approver_status', 1)
                ->whereIn('check_technology', [0, 1])
                ->whereYear('created_at', Carbon::now()->year)
                ->groupBy('month')
                ->orderBy('month')
                ->get();
                }

       
            $BRPLlabels = [];
            $BRPLapprovedData = [];
            $BRPLrejectedData = [];
            $BRPLpendingData = [];
        
            foreach ($fileDataBRPL as $dataPointBRPL) {
                $monthBRPL = Carbon::createFromFormat('!m', $dataPointBRPL->month)->format('F');
        
                $BRPLlabels[] = $monthBRPL;
                $BRPLapprovedData[] = $dataPointBRPL->approved_count;
                $BRPLrejectedData[] = $dataPointBRPL->rejected_count;
                $BRPLpendingData[] = $dataPointBRPL->pending_count;
                
            }
            if($fiscal_year){
                $BYPLnv = NeedValidation::where('company_id','5')->where('fiscal_year', $fiscal_year)->pluck("id");



            }else{
                $BYPLnv = NeedValidation::where('company_id','5')->where('fiscal_year', $currentFinancialYear)->pluck("id");



            }
            $pendingAmountBYPL = DB::table('tbl_material')
            ->join('nvservicestatus', 'tbl_material.id', '=', 'nvservicestatus.material_id')
            ->whereIn('tbl_material.nv_id', $BYPLnv)
            ->whereIn('nvservicestatus.nv_id', $pen_amt->pluck('nv_id'))
         
            ->sum('tbl_material.total_budget_both')+DB::table('tbl_service')
            ->join('nvservicestatus', 'tbl_service.id', '=', 'nvservicestatus.service_id')
            ->whereIn('tbl_service.nv_id', $BYPLnv)
            ->whereIn('nvservicestatus.nv_id', $pen_amt->pluck('nv_id'))
         
            ->sum('tbl_service.total_buget');
          
    
            $rejectedAmountBYPL = DB::table('tbl_material')
            ->join('nvservicestatus', 'tbl_material.id', '=', 'nvservicestatus.material_id')
            ->whereIn('tbl_material.nv_id', $BYPLnv)
            ->where(function ($query) {
                $query->where('nvservicestatus.approverdep3_status', 2);
            })
            ->sum('tbl_material.total_budget_both')+DB::table('tbl_service')
            ->join('nvservicestatus', 'tbl_service.id', '=', 'nvservicestatus.service_id')
            ->whereIn('tbl_service.nv_id', $BYPLnv)
            ->where(function ($query) {
                $query->where('nvservicestatus.approverdep3_status', 2);
            })
            ->sum('tbl_service.total_buget');
       
    
            $approvedAmountBYPL = DB::table('tbl_material')
            ->join('nvservicestatus', 'tbl_material.id', '=', 'nvservicestatus.material_id')
            ->whereIn('tbl_material.nv_id', $BYPLnv)->where('nvservicestatus.ceo_status', 1)
            ->sum('tbl_material.total_budget_both')+DB::table('tbl_service')
            ->join('nvservicestatus', 'tbl_service.id', '=', 'nvservicestatus.service_id')
            ->whereIn('tbl_service.nv_id', $BYPLnv)->where('nvservicestatus.ceo_status', 1)
            ->sum('tbl_service.total_buget');

            if(!empty($id3->work_rew1) || !empty($id3->work_rew2)|| !empty($id3->work_rew3)|| !empty($id3->work_rew4)){
                    
                $fileDataBYPL =Nvsericestatus::
            select(
                DB::raw('MONTH(created_at) as month'),
                DB::raw('SUM(CASE WHEN ceo_status = "1" THEN 1 ELSE 0 END) as approved_count'),
                DB::raw('SUM(CASE WHEN ( approverdep3_status = "2")  THEN 1 ELSE 0 END) as rejected_count'),
                DB::raw('SUM(CASE WHEN ( approverdep3_status = "0" AND (work_rew1dep3_status = "1" OR work_rew2dep3_status = "1" OR work_rew3dep3_status = "1" OR work_rew4dep3_status = "1")) THEN 1 ELSE 0 END) as pending_count'),
               
            ) 
            ->whereIn('nv_id', $BYPLnv)->with(['service', 'material', 'user'])
            ->where('approver_status', 1)
            ->whereIn('check_technology', [0, 1])
          
            ->whereYear('created_at', Carbon::now()->year)
            ->groupBy('month')
            ->orderBy('month')
            ->get();
            }elseif(empty($id3->work_rew1) && empty($id3->work_rew2) && empty($id3->work_rew3) && empty($id3->work_rew4)){
            
            $fileDataBYPL =Nvsericestatus::
            select(
                DB::raw('MONTH(created_at) as month'),
                DB::raw('SUM(CASE WHEN ceo_status = "1" THEN 1 ELSE 0 END) as approved_count'),
                DB::raw('SUM(CASE WHEN ( approverdep3_status = "2")  THEN 1 ELSE 0 END ) as rejected_count'),
                DB::raw('SUM(CASE WHEN ( approverdep3_status = "0" AND work_rew1dep3_status = "0" AND work_rew2dep3_status = "0" AND work_rew3dep3_status = "0" AND work_rew4dep3_status = "0" )  THEN 1 ELSE 0 END) as pending_count'),
              
            )
            ->whereIn('nv_id', $BYPLnv)->with(['service', 'material', 'user'])
            ->where('approver_status', 1)
            ->whereIn('check_technology', [0, 1])
            ->whereYear('created_at', Carbon::now()->year)
            ->groupBy('month')
            ->orderBy('month')
            ->get();
            }


        $BYPLlabels = [];
        $BYPLapprovedData = [];
        $BYPLrejectedData = [];
        $BYPLpendingData = [];
    
        foreach ($fileDataBYPL as $dataPointBYPL) {
            $monthBYPL = Carbon::createFromFormat('!m', $dataPointBYPL->month)->format('F');
    
            $BYPLlabels[] = $monthBYPL;
            $BYPLapprovedData[] = $dataPointBYPL->approved_count;
            $BYPLrejectedData[] = $dataPointBYPL->rejected_count;
            $BYPLpendingData[] = $dataPointBYPL->pending_count;
           
        }
                return view("admin.dashboard", compact("approvedAmount","currentFinancialYear","nextFinancialYear","nextToNextFinancialYear", "rejectedAmount", "pendingAmount", "totalAmount","company", "company_id" , "nv_sm_data", "totalNV", "approvedNV", "rejectedNV", "pendingNV" , "hodApproval", "cpmgApproval", "btApproval", "ceoApproval"             ,'BRPLlabels','BRPLapprovedData','BRPLrejectedData','BRPLpendingData','BYPLlabels','BYPLapprovedData','BYPLrejectedData','BYPLpendingData','cesApproval','ceonominee1Approval','ceonominee2Approval','approvedAmountBYPL','approvedAmountBRPL','pendingAmountBYPL','pendingAmountBRPL','rejectedAmountBYPL','rejectedAmountBRPL','groupcioApproval'));
            } elseif (!empty($group_cio) && $group_cio == $user->id) {
              
                $departmentIds = explode(',', $user->department_id);
              
            if($fiscal_year){
                $totalId = NeedValidation::whereIn('department_id', $departmentIds)->where('fiscal_year', $fiscal_year)->pluck('id');
            }else{
                $totalId = NeedValidation::whereIn('department_id', $departmentIds)->where('fiscal_year', $currentFinancialYear)->pluck('id');
            }

            if($company_id){
                $latestData = Nvsericestatus::whereIn("nv_id", $totalId)->where('company_id',$company_id)->get();
            }else {
                $latestData = Nvsericestatus::whereIn("nv_id", $totalId)->get();
            }
              
                $totalAmount = DB::table('tbl_material')
                ->join('nvservicestatus', 'tbl_material.id', '=', 'nvservicestatus.material_id')
                ->whereIn('tbl_material.nv_id', $totalId)
                ->where('nvservicestatus.groupcio_status', 1)
                ->sum('tbl_material.total_budget_both') + DB::table('tbl_service')
                ->join('nvservicestatus', 'tbl_service.id', '=', 'nvservicestatus.service_id')
                ->whereIn('tbl_service.nv_id', $totalId)
                ->where('nvservicestatus.groupcio_status', 1)
                ->sum('tbl_service.total_buget');
             
                $pen_amt = $latestData->filter(function ($data) {
                    return in_array($data->approverdep3_status, [1]) &&
                        in_array($data->groupcio_status, [0]);
                });
                $pendingAmount = DB::table('tbl_material')
                ->join('nvservicestatus', 'tbl_material.id', '=', 'nvservicestatus.material_id')
                 ->whereIn('tbl_material.nv_id', $totalId)
                 ->whereIn('nvservicestatus.nv_id', $pen_amt->pluck('nv_id'))
              
                ->sum('tbl_material.total_budget_both') + DB::table('tbl_service')
                ->join('nvservicestatus', 'tbl_service.id', '=', 'nvservicestatus.service_id')
                 ->whereIn('tbl_service.nv_id', $totalId)
                 ->whereIn('nvservicestatus.nv_id', $pen_amt->pluck('nv_id'))
              
                ->sum('tbl_service.total_buget');
        
                $rejectedAmount = DB::table('tbl_material')
                ->join('nvservicestatus', 'tbl_material.id', '=', 'nvservicestatus.material_id')
                ->whereIn('nvservicestatus.nv_id', $latestData->pluck('nv_id'))
                ->where(function ($query) {
                    $query->orWhere('nvservicestatus.groupcio_status', 2);
                })
                ->sum('tbl_material.total_budget_both') + DB::table('tbl_service')
                ->join('nvservicestatus', 'tbl_service.id', '=', 'nvservicestatus.service_id')
                ->whereIn('nvservicestatus.nv_id', $latestData->pluck('nv_id'))
                ->where(function ($query) {
                    $query->orWhere('nvservicestatus.groupcio_status', 2);
                })
                ->sum('tbl_service.total_buget');
           
        
                $approvedAmount = DB::table('tbl_material')
                ->join('nvservicestatus', 'tbl_material.id', '=', 'nvservicestatus.material_id')
                ->whereIn('tbl_material.nv_id', $totalId)
                ->where('nvservicestatus.ceo_status', 1)
                ->sum('tbl_material.total_budget_both') + DB::table('tbl_service')
                ->join('nvservicestatus', 'tbl_service.id', '=', 'nvservicestatus.service_id')
                ->whereIn('tbl_service.nv_id', $totalId)
                ->where('nvservicestatus.ceo_status', 1)
                ->sum('tbl_service.total_buget');


                if($company_id){
                    $totalNV = Nvsericestatus::whereIn("nv_id", $totalId)->where('groupcio_status', 1)->where('company_id',$company_id)->count();
                } else {
                    $totalNV = Nvsericestatus::whereIn("nv_id", $totalId)->where('groupcio_status', 1)->count();
                }
                if($company_id){
                    $rejectedNV = Nvsericestatus::whereIn("nv_id", $totalId)->where('groupcio_status', 2)->where('company_id',$company_id)->count();
                } else {
                    $rejectedNV = Nvsericestatus::whereIn("nv_id", $totalId)->where('groupcio_status', 2)->count();
                }
               
            
                if($company_id){
                    $approvedNV = $latestData->where('ceo_status', 1)->where('company_id',$company_id)->count();
                }else{
                    $approvedNV = $latestData->where('ceo_status', 1)->count();
                }
             
                    $pendingNV = $latestData->filter(function ($data) {
                        return in_array($data->approverdep3_status, [1]) &&
                            in_array($data->groupcio_status, [0]);
                    })->count();
                 
                
                $nvIds = NeedValidation::where("user_id", $Values)->pluck("id");
                if($fiscal_year){
                    $nv = NeedValidation::whereHas('service')->select('id')->whereIn('department_id', $departmentIds)->where('fiscal_year', $fiscal_year)
                
                    ->get();
                }else{
                    $nv = NeedValidation::whereHas('service')->select('id')->whereIn('department_id', $departmentIds)->where('fiscal_year', $currentFinancialYear)
                
                ->get();
                }
               
                $nv_ids = $nv->pluck('id');
            
                 $nv_sm_data = Nvsericestatus::whereIn('nv_id', $nv_ids)->with(['service', 'material', 'user'])
                ->where('approverdep3_status', 1)
                ->orderBy('id', 'asc')->get();
              
                $total32_status = $request->total;
                $pending32_status = $request->pending;
                $reject32_status = $request->rejected;

                if ($ceo_status !== null) {
                    $nv_sm_data = $nv_sm_data->where('groupcio_status', $ceo_status);
                } elseif ($pending32_status !== null) {
                    $nv_sm_data = $nv_sm_data->where('groupcio_status', $pending32_status);
                      
                } elseif ($reject32_status !== null) {
                    $nv_sm_data = $nv_sm_data->where('groupcio_status', $reject32_status);
                    
                }elseif ($total32_status !== null) {
                    $nv_sm_data = $nv_sm_data->where('groupcio_status', $total32_status);
                      
                }
                if($fiscal_year){
                    $BRPLnv = NeedValidation::where('company_id','6')->whereIn('department_id', $departmentIds)->where('fiscal_year', $fiscal_year)->pluck("id");

                }else{
                    $BRPLnv = NeedValidation::where('company_id','6')->whereIn('department_id', $departmentIds)->where('fiscal_year', $currentFinancialYear)->pluck("id");

                }

                $pendingAmountBRPL = DB::table('tbl_material')
                ->join('nvservicestatus', 'tbl_material.id', '=', 'nvservicestatus.material_id')
                ->whereIn('tbl_material.nv_id', $BRPLnv)
                ->whereIn('nvservicestatus.nv_id', $pen_amt->pluck('nv_id'))
              
                ->sum('tbl_material.total_budget_both')+DB::table('tbl_service')
                ->join('nvservicestatus', 'tbl_service.id', '=', 'nvservicestatus.service_id')
                ->whereIn('tbl_service.nv_id', $BRPLnv)
                ->whereIn('nvservicestatus.nv_id', $pen_amt->pluck('nv_id'))
            
                ->sum('tbl_service.total_buget');
           
        
                $rejectedAmountBRPL = DB::table('tbl_material')
                ->join('nvservicestatus', 'tbl_material.id', '=', 'nvservicestatus.material_id')
                ->whereIn('tbl_material.nv_id', $BRPLnv)
                ->where(function ($query) {
                    $query->where('nvservicestatus.groupcio_status', 2);
                })
                ->sum('tbl_material.total_budget_both')+DB::table('tbl_service')
                ->join('nvservicestatus', 'tbl_service.id', '=', 'nvservicestatus.service_id')
                ->whereIn('tbl_service.nv_id', $BRPLnv)
                ->where(function ($query) {
                    $query->where('nvservicestatus.groupcio_status', 2);
                })
                ->sum('tbl_service.total_buget');
           
        
                $approvedAmountBRPL = DB::table('tbl_material')
                ->join('nvservicestatus', 'tbl_material.id', '=', 'nvservicestatus.material_id')
                ->whereIn('tbl_material.nv_id', $BRPLnv)->where('nvservicestatus.ceo_status', 1)
                ->sum('tbl_material.total_budget_both')+DB::table('tbl_service')
                ->join('nvservicestatus', 'tbl_service.id', '=', 'nvservicestatus.service_id')
                ->whereIn('tbl_service.nv_id', $BRPLnv)->where('nvservicestatus.ceo_status', 1)
                ->sum('tbl_service.total_buget');
                
                $fileDataBRPL =Nvsericestatus::
                select(
                    DB::raw('MONTH(created_at) as month'),
                    DB::raw('SUM(CASE WHEN (ceo_status = "1") THEN 1 ELSE 0 END) as approved_count'),
                    DB::raw('SUM(CASE WHEN ( groupcio_status = "2")  THEN 1 ELSE 0 END ) as rejected_count'),
                    DB::raw('SUM(CASE WHEN (groupcio_status = "0" ) THEN 1 ELSE 0 END) as pending_count'),
                   
                )
                ->whereIn('nv_id', $BRPLnv)->with(['service', 'material', 'user'])
                ->where('approverdep3_status',1)
                ->whereYear('created_at', Carbon::now()->year)
                ->groupBy('month')
                ->orderBy('month')
                ->get();
      
            $BRPLlabels = [];
            $BRPLapprovedData = [];
            $BRPLrejectedData = [];
            $BRPLpendingData = [];
        
            foreach ($fileDataBRPL as $dataPointBRPL) {
                $monthBRPL = Carbon::createFromFormat('!m', $dataPointBRPL->month)->format('F');
        
                $BRPLlabels[] = $monthBRPL;
                $BRPLapprovedData[] = $dataPointBRPL->approved_count;
                $BRPLrejectedData[] = $dataPointBRPL->rejected_count;
                $BRPLpendingData[] = $dataPointBRPL->pending_count;
                
            }
            if($fiscal_year){
                $BYPLnv = NeedValidation::where('company_id','5')->whereIn('department_id', $departmentIds)->where('fiscal_year', $fiscal_year)->pluck("id");


            }else{
                $BYPLnv = NeedValidation::where('company_id','5')->whereIn('department_id', $departmentIds)->where('fiscal_year', $currentFinancialYear)->pluck("id");


            }
            $pendingAmountBYPL = DB::table('tbl_material')
            ->join('nvservicestatus', 'tbl_material.id', '=', 'nvservicestatus.material_id')
            ->whereIn('tbl_material.nv_id', $BYPLnv)
            ->whereIn('nvservicestatus.nv_id', $pen_amt->pluck('nv_id'))
          
            ->sum('tbl_material.total_budget_both')+DB::table('tbl_service')
            ->join('nvservicestatus', 'tbl_service.id', '=', 'nvservicestatus.service_id')
            ->whereIn('tbl_service.nv_id', $BYPLnv)
            ->whereIn('nvservicestatus.nv_id', $pen_amt->pluck('nv_id'))
          
            ->sum('tbl_service.total_buget');
            
    
            $rejectedAmountBYPL = DB::table('tbl_material')
            ->join('nvservicestatus', 'tbl_material.id', '=', 'nvservicestatus.material_id')
            ->whereIn('tbl_material.nv_id', $BYPLnv)
            ->where(function ($query) {
                $query->where('nvservicestatus.groupcio_status', 2);
            })
            ->sum('tbl_material.total_budget_both')+DB::table('tbl_service')
            ->join('nvservicestatus', 'tbl_service.id', '=', 'nvservicestatus.service_id')
            ->whereIn('tbl_service.nv_id', $BYPLnv)
            ->where(function ($query) {
                $query->where('nvservicestatus.groupcio_status', 2);
            })
            ->sum('tbl_service.total_buget');
       
    
            $approvedAmountBYPL = DB::table('tbl_material')
            ->join('nvservicestatus', 'tbl_material.id', '=', 'nvservicestatus.material_id')
            ->whereIn('tbl_material.nv_id', $BYPLnv)->where('nvservicestatus.ceo_status', 1)
            ->sum('tbl_material.total_budget_both')+DB::table('tbl_service')
            ->join('nvservicestatus', 'tbl_service.id', '=', 'nvservicestatus.service_id')
            ->whereIn('tbl_service.nv_id', $BYPLnv)->where('nvservicestatus.ceo_status', 1)
            ->sum('tbl_service.total_buget');
     
             $fileDataBYPL =Nvsericestatus::
            select(
                DB::raw('MONTH(created_at) as month'),
                DB::raw('SUM(CASE WHEN (ceo_status = "1") THEN 1 ELSE 0 END) as approved_count'),
                DB::raw('SUM(CASE WHEN (groupcio_status = "2") THEN 1 ELSE 0 END) as rejected_count'),
                DB::raw('SUM(CASE WHEN (groupcio_status = "0") THEN 1 ELSE 0 END) as pending_count'),
               
            ) 
            ->whereIn('nv_id', $BYPLnv)->with(['service', 'material', 'user'])
            ->where('approverdep3_status',1)
            ->whereYear('created_at', Carbon::now()->year)
            ->groupBy('month')
            ->orderBy('month')
            ->get();
           
        $BYPLlabels = [];
        $BYPLapprovedData = [];
        $BYPLrejectedData = [];
        $BYPLpendingData = [];
    
        foreach ($fileDataBYPL as $dataPointBYPL) {
            $monthBYPL = Carbon::createFromFormat('!m', $dataPointBYPL->month)->format('F');
    
            $BYPLlabels[] = $monthBYPL;
            $BYPLapprovedData[] = $dataPointBYPL->approved_count;
            $BYPLrejectedData[] = $dataPointBYPL->rejected_count;
            $BYPLpendingData[] = $dataPointBYPL->pending_count;
                     
        }
                return view("admin.dashboard", compact("approvedAmount","currentFinancialYear","nextFinancialYear" ,"nextToNextFinancialYear","rejectedAmount", "pendingAmount", "totalAmount","company", "company_id" , "nv_sm_data", "totalNV", "approvedNV", "rejectedNV", "pendingNV" , "hodApproval", "cpmgApproval", "btApproval", "ceoApproval"             ,'BRPLlabels','BRPLapprovedData','BRPLrejectedData','BRPLpendingData','BYPLlabels','BYPLapprovedData','BYPLrejectedData','BYPLpendingData','cesApproval','ceonominee1Approval','ceonominee2Approval','approvedAmountBYPL','rejectedAmountBYPL','pendingAmountBYPL','approvedAmountBRPL','rejectedAmountBRPL','pendingAmountBRPL','groupcioApproval'));
            
            } elseif ($id4->work_rew1 == $user->id) {
                $Values = [$user->id, $id4];
                $nvid = [];
                $nvstatus = Nvsericestatus::get();
                foreach ($nvstatus as $nvstatus) {
                    array_push($nvid, $nvstatus["nv_id"]);
                }
                $user_nv = NeedValidation::with("division", "service")
                    ->whereIn("id", $nvid)
                    ->orderBy("id", "desc")
                    ->first();
                    if(!empty( $user_nv)){
                $employees = Employee::where("user_id", $user_nv->user_id)->first();
                $department = Department::where("id", $employees->department_id)->first();
                $group_cio = $department->group_cio;}
                if($fiscal_year){
                    $totalId = NeedValidation::where('fiscal_year', $fiscal_year)->pluck('id');
                }else{
                    $totalId = NeedValidation::where('fiscal_year', $currentFinancialYear)->pluck('id');
                }
               
                // dd($totalId);

                $totalAmount = DB::table('tbl_material')
                ->join('nvservicestatus', 'tbl_material.id', '=', 'nvservicestatus.material_id')
                 // -> whereIn("nv_id", $totalId)
                 ->whereIn('tbl_material.nv_id', $totalId)
                 ->whereIn('nvservicestatus.nv_id', $latestData->pluck('nv_id'))
                ->where('nvservicestatus.work_rew1dep4_status', 1)
                ->sum('tbl_material.total_budget_both') + DB::table('tbl_service')
                ->join('nvservicestatus', 'tbl_service.id', '=', 'nvservicestatus.service_id')
                 // -> whereIn("nv_id", $totalId)
                 ->whereIn('tbl_service.nv_id', $totalId)
                 ->whereIn('nvservicestatus.nv_id', $latestData->pluck('nv_id'))
                ->where('nvservicestatus.work_rew1dep4_status', 1)
                ->sum('tbl_service.total_buget');
                // dd($totalAmount);
                if(!empty( $group_cio)){
                $pen_amt = $latestData->filter(function ($data) {
                    return in_array($data->groupcio_status, [1]) &&
                        in_array($data->work_rew1dep4_status, [0]) &&
                        in_array($data->work_rew2dep4_status, [0]) &&
                        in_array($data->work_rew3dep4_status, [0]) &&
                        in_array($data->work_rew4dep4_status, [0]) &&
                        in_array($data->approverdep4_status, [0]) ;
                });
                }else{
                    $pen_amt = $latestData->filter(function ($data) {
                        return in_array($data->approverdep3_status, [1]) &&
                            in_array($data->work_rew1dep4_status, [0]) &&
                            in_array($data->work_rew2dep4_status, [0]) &&
                            in_array($data->work_rew3dep4_status, [0]) &&
                            in_array($data->work_rew4dep4_status, [0]) &&
                            in_array($data->approverdep4_status, [0]) ;
                    });   
                }
                $pendingAmount = DB::table('tbl_material')
                ->join('nvservicestatus', 'tbl_material.id', '=', 'nvservicestatus.material_id')
                 // -> whereIn("nv_id", $totalId)
                 ->whereIn('tbl_material.nv_id', $totalId)
                 ->whereIn('nvservicestatus.nv_id', $pen_amt->pluck('nv_id'))
                // ->where('nvservicestatus.work_rew1dep4_status', 0)
                // ->where('nvservicestatus.ceo_nominee_status', 1)
                ->sum('tbl_material.total_budget_both') + DB::table('tbl_service')
                ->join('nvservicestatus', 'tbl_service.id', '=', 'nvservicestatus.service_id')
                 // -> whereIn("nv_id", $totalId)
                 ->whereIn('tbl_service.nv_id', $totalId)
                 ->whereIn('nvservicestatus.nv_id', $pen_amt->pluck('nv_id'))
                // ->where('nvservicestatus.work_rew1dep4_status', 0)
                // ->where('nvservicestatus.ceo_nominee_status', 1)
                ->sum('tbl_service.total_buget');
        
                $rejectedAmount = DB::table('tbl_material')
                ->join('nvservicestatus', 'tbl_material.id', '=', 'nvservicestatus.material_id')
                ->whereIn('nvservicestatus.nv_id', $latestData->pluck('nv_id'))
                ->where(function ($query) {
                    $query->where('nvservicestatus.work_rew1dep4_status', 2);
                })
                ->sum('tbl_material.total_budget_both') + DB::table('tbl_service')
                ->join('nvservicestatus', 'tbl_service.id', '=', 'nvservicestatus.service_id')
                ->whereIn('nvservicestatus.nv_id', $latestData->pluck('nv_id'))
                ->where(function ($query) {
                    $query->where('nvservicestatus.work_rew1dep4_status', 2);
                })
                ->sum('tbl_service.total_buget');
           
        
                $approvedAmount = DB::table('tbl_material')
                 // -> whereIn("nv_id", $totalId)
                 ->whereIn('tbl_material.nv_id', $totalId)
                 ->whereIn('nvservicestatus.nv_id', $latestData->pluck('nv_id'))
                ->join('nvservicestatus', 'tbl_material.id', '=', 'nvservicestatus.material_id')
                ->where('nvservicestatus.ceo_status', 1)
                ->sum('tbl_material.total_budget_both') +  DB::table('tbl_service')
                // -> whereIn("nv_id", $totalId)
                ->whereIn('tbl_service.nv_id', $totalId)
                ->whereIn('nvservicestatus.nv_id', $latestData->pluck('nv_id'))
               ->join('nvservicestatus', 'tbl_service.id', '=', 'nvservicestatus.service_id')
               ->where('nvservicestatus.ceo_status', 1)
               ->sum('tbl_service.total_buget');
                if($company_id){
                    $totalNV = Nvsericestatus::whereIn("nv_id", $totalId)->where('work_rew1dep4_status', 1)->where('company_id',$company_id)->count();
                } else {
                    $totalNV = Nvsericestatus::whereIn("nv_id", $totalId)->where('work_rew1dep4_status', 1)->count();
                }
                if($company_id){
                    $rejectedNV = Nvsericestatus::whereIn("nv_id", $totalId)->where('work_rew1dep4_status', 2)->where('company_id',$company_id)->count();
                } else {
                    $rejectedNV = Nvsericestatus::whereIn("nv_id", $totalId)->where('work_rew1dep4_status', 2)->count();
                }
                // dd($totalId);
                if($company_id){
                $latestData = Nvsericestatus::whereIn("nv_id", $totalId)->where('company_id',$company_id)->get();
            }else {
                $latestData = Nvsericestatus::whereIn("nv_id", $totalId)->get();
            }
                if($company_id){
                    $approvedNV = $latestData->where('ceo_status', 1)->where('company_id',$company_id)->count();
                }else{
                    $approvedNV = $latestData->where('ceo_status', 1)->count();
                }
                // $rejectedNV = $latestData->filter(function ($data) {
                //     return in_array($data->ceo_status, [2]) || 
                //     $data->work_rew1_status == 2 ;
                //     // $data->hod_status == 2 || 
                //     // $data->cpmg_status == 2 || 
                //     // $data->ces_status == 2 || 
                //     // $data->cto_status == 2 || 
                //     // $data->ceo_nominee_status == 2 || 
                //     // $data->ceo_nominee2_status == 2 || 
                //     // $data->ceo_nominee2_status == 2;
                // })->count();
                // $pendingNV = $latestData->filter(function ($data) {
                //     return in_array($data->approverdep3_status, [ 1]) &&
                //     in_array($data->work_rew1dep4_status, [0]);
                  
                //     })->count();
                if(!empty( $group_cio)){
                $pendingNV = $latestData->filter(function ($data) {
                    return in_array($data->groupcio_status, [1]) &&
                        in_array($data->work_rew1dep4_status, [0]) &&
                        in_array($data->work_rew2dep4_status, [0]) &&
                        in_array($data->work_rew3dep4_status, [0]) &&
                        in_array($data->work_rew4dep4_status, [0]) &&
                        in_array($data->approverdep4_status, [0]) ;
                })->count();
            }else{
                $pendingNV = $latestData->filter(function ($data) {
                    return in_array($data->approverdep3_status, [1]) &&
                        in_array($data->work_rew1dep4_status, [0]) &&
                        in_array($data->work_rew2dep4_status, [0]) &&
                        in_array($data->work_rew3dep4_status, [0]) &&
                        in_array($data->work_rew4dep4_status, [0]) &&
                        in_array($data->approverdep4_status, [0]) ;
                })->count();  
            }
                 

                 $hodApproval = Nvsericestatus::whereIn("nv_id", $totalId)->where("hod_status", 0)->count();
           
                $cesApproval = Nvsericestatus::whereIn("nv_id", $totalId)->where("hod_status", 1)->where("ces_status", 0)->where("derc_info", 1)->count();
                $cpmg = Nvsericestatus::whereIn("nv_id", $totalId)->where('hod_status', 1)->where('cpmg_status', 0)->get();
                $Pendingcpmg = $cpmg->filter(function ($data) {
                 return in_array($data->hod_status, [1]) &&
                     in_array($data->cpmg_status, [0]) &&
                     (
                         (in_array($data->derc_info, [0]) && in_array($data->ces_status, [0]))
                         ||
                         (in_array($data->derc_info, [1]) && in_array($data->ces_status, [1]))
                     );
             });
             $cpmgApproval = $Pendingcpmg->count(); 

                $btApproval = Nvsericestatus::whereIn("nv_id", $totalId)->where("cpmg_status", 1)->where("check_technology", 1)->where('cto_status', 0)->count();
              
                $ceonominee1 = Nvsericestatus::whereIn("nv_id", $totalId)->where('cpmg_status', 1)->where('ceo_nominee_status', 0)->get();
                 $Pendingnominee1 = $ceonominee1->filter(function ($data) {
                 return in_array($data->cpmg_status, [1]) &&
                     in_array($data->ceo_nominee_status, [0]) &&
                     (
                         (in_array($data->check_technology, [0]) && in_array($data->cto_status, [0]))
                         ||
                         (in_array($data->check_technology, [1]) && in_array($data->cto_status, [1]))
                     );
             });
             $ceonominee1Approval = $Pendingnominee1->count(); 
                // $cpmgApproval = Nvsericestatus::whereIn("nv_id", $totalId)->where(function ($query) {
                //     $query->whereIn('ces_status', [0,1]);
                // })
                //     ->Where(function ($query) {
                //         $query->where('hod_status', 1);
                //     })
                //     ->where(function ($query) {
                //         $query->where('derc_info', 0);
                //     })
                //     ->where(function ($query) {
                //         $query->where('cpmg_status', 0);
                //     })
                //        ->count();
              
                // $btApproval = Nvsericestatus::whereIn("nv_id", $totalId)->where("cpmg_status", 1)->where("check_technology", 1)->where('cto_status', 0)->count();
                // $ceonominee1Approval = Nvsericestatus::whereIn("nv_id", $totalId)->where("cpmg_status", 1)->whereIn('cto_status', [0,1])->whereIn("check_technology", [0,1])->where('ceo_nominee_status', 0)->count();
                if(!empty( $group_cio)){
                    $ceonominee2Approval = Nvsericestatus::where('groupcio_status', 1)->where('ceo_nominee2_status', 0)->whereIn("nv_id", $totalId)->count();
                    }else{
                        $ceonominee2Approval = Nvsericestatus::where('ceo_nominee_status', 1)->where('ceo_nominee2_status', 0)->whereIn("nv_id", $totalId)->count();
         
                    }
                if(!empty( $group_cio)){
                    $groupcioApproval = Nvsericestatus::where('ceo_nominee_status', 1)->where('groupcio_status', 0)->where('ceo_nominee2_status', 0)->whereIn("nv_id", $totalId)->count();
                    }else{
                        $groupcioApproval =0;  
                    }
        
                        // if(!empty( $group_cio)){
                        // $ceoApproval = Nvsericestatus::where('groupcio_status', 1)->where('ceo_status', 0)->whereIn("nv_id", $totalId)->count();
                        // }else{
                        $ceoApproval = Nvsericestatus::where('ceo_nominee2_status', 1)->where('ceo_status', 0)->whereIn("nv_id", $totalId)->count();
                        // }

              
                $nvIds = NeedValidation::where("user_id", $user->id)->pluck("id");
                if($fiscal_year){
                    $nv = NeedValidation::whereHas('service')->select('id')->where('fiscal_year', $fiscal_year)
                    // ->whereIn('user_id', $Values)
                    // ->orWhere('user_id',$allNormalUsers)
                    ->get();
                }else{
                    $nv = NeedValidation::whereHas('service')->select('id')->where('fiscal_year', $currentFinancialYear)
                    // ->whereIn('user_id', $Values)
                    // ->orWhere('user_id',$allNormalUsers)
                    ->get();
                }
              
                $nv_ids = $nv->pluck('id');
                // $nv_sm_data = Nvsericestatus::whereIn('nv_id', $nv_ids)
                // ->with(['service', 'material', 'user'])
                // ->where('hod_status',1)
                // ->orderBy('id', 'desc')
                // ->get();
                if(!empty( $group_cio)){
                    $nv_sm_data = Nvsericestatus::whereIn('nv_id', $nv_ids)->with(['service', 'material', 'user'])
                    ->where('groupcio_status', 1)
                    ->orderBy('id', 'asc')->get();
                }else{
                    $nv_sm_data = Nvsericestatus::whereIn('nv_id', $nv_ids)->with(['service', 'material', 'user'])
                    ->where('approverdep3_status', 1)
                    ->orderBy('id', 'asc')->get();
                }
              
          
                $total23_status = $request->total;
                $pending23_status = $request->pending;
                $reject23_status = $request->rejected;

                if ($ceo_status !== null) {
                    $nv_sm_data = $nv_sm_data->where('ceo_status', $ceo_status);
                } elseif ($pending23_status !== null) {
                    $nv_sm_data = $nv_sm_data->where('work_rew1dep4_status', $pending23_status)
                    ->where('work_rew2dep4_status', 0)
                    ->where('work_rew3dep4_status', 0)
                    ->where('work_rew4dep4_status', 0);
                        
                        // ->whereIn('hod_status', [ 1])
                        // ->whereIn('approver_status', [ 1])    
                        // ->whereIn('cto_status', [0, 1])  
                        
                        // ->whereIn('approverdep3_status', [ 1]);
               
                } elseif ($reject23_status !== null) {
                    $nv_sm_data = $nv_sm_data->where('work_rew1dep4_status', $reject23_status);
                    
                }elseif ($total23_status !== null) {
                    $nv_sm_data = $nv_sm_data->where('work_rew1dep4_status', $total23_status);
                      
                }
                if($fiscal_year){
                    $BRPLnv = NeedValidation::where('company_id','6')->where('fiscal_year', $fiscal_year)->pluck("id");
    
                }else{
                    $BRPLnv = NeedValidation::where('company_id','6')->where('fiscal_year', $currentFinancialYear)->pluck("id");
    
                }
                $pendingAmountBRPL = DB::table('tbl_material')
                ->join('nvservicestatus', 'tbl_material.id', '=', 'nvservicestatus.material_id')
                ->whereIn('tbl_material.nv_id', $BRPLnv)
                ->whereIn('nvservicestatus.nv_id', $pen_amt->pluck('nv_id'))
                // ->where('nvservicestatus.work_rew1dep4_status', 0)
                // ->where('nvservicestatus.ceo_nominee_status', 1)
                ->sum('tbl_material.total_budget_both')+DB::table('tbl_service')
                ->join('nvservicestatus', 'tbl_service.id', '=', 'nvservicestatus.service_id')
                ->whereIn('tbl_service.nv_id', $BRPLnv)
                ->whereIn('nvservicestatus.nv_id', $pen_amt->pluck('nv_id'))
                // ->where('nvservicestatus.work_rew1dep4_status', 0)
                // ->where('nvservicestatus.ceo_nominee_status', 1)
                ->sum('tbl_service.total_buget');
                // dd($pendingAmountBRPL);
        
                $rejectedAmountBRPL = DB::table('tbl_material')
                ->join('nvservicestatus', 'tbl_material.id', '=', 'nvservicestatus.material_id')
                ->whereIn('tbl_material.nv_id', $BRPLnv)
                ->where(function ($query) {
                    $query->where('nvservicestatus.work_rew1dep4_status', 2);
                })
                ->sum('tbl_material.total_budget_both')+DB::table('tbl_service')
                ->join('nvservicestatus', 'tbl_service.id', '=', 'nvservicestatus.service_id')
                ->whereIn('tbl_service.nv_id', $BRPLnv)
                ->where(function ($query) {
                    $query->where('nvservicestatus.work_rew1dep4_status', 2);
                })
                ->sum('tbl_service.total_buget');
           
        
                $approvedAmountBRPL = DB::table('tbl_material')
                ->join('nvservicestatus', 'tbl_material.id', '=', 'nvservicestatus.material_id')
                ->whereIn('tbl_material.nv_id', $BRPLnv)->where('nvservicestatus.ceo_status', 1)
                ->sum('tbl_material.total_budget_both')+DB::table('tbl_service')
                ->join('nvservicestatus', 'tbl_service.id', '=', 'nvservicestatus.service_id')
                ->whereIn('tbl_service.nv_id', $BRPLnv)->where('nvservicestatus.ceo_status', 1)
                ->sum('tbl_service.total_buget');
                $DataBRPL =Nvsericestatus::
                select(
                    DB::raw('MONTH(created_at) as month'),
                    DB::raw('SUM(CASE WHEN ceo_status = "1" THEN 1 ELSE 0 END) as approved_count'),
                    DB::raw('SUM(CASE WHEN ( work_rew3dep4_status = "2")  THEN 1 ELSE 0 END ) as rejected_count'),
                    DB::raw('SUM(CASE WHEN (work_rew1dep4_status = "0" AND work_rew2dep4_status = "0" AND work_rew3dep4_status = "0" AND work_rew4dep4_status = "0" AND approverdep4_status = "0")  THEN 1 ELSE 0 END) as pending_count'),
                    // DB::raw('SUM(CASE WHEN (approverdep3_status = "1" AND work_rew1dep4_status = "1" AND work_rew3dep4_status = "0") THEN 1 ELSE 0 END) as pending_count'),
                    // DB::raw('SUM(CASE WHEN status = "pending" THEN 1 ELSE 0 END) as rejected_count')
                )
                // -> where('ceo_nominee_status',1)
                ->whereIn('nv_id', $BRPLnv)->with(['service', 'material', 'user'])
                // ->where('approverdep3_status',1)
                ->whereYear('created_at', Carbon::now()->year)
                ->groupBy('month')
                ->orderBy('month');
                // ->get();
                if (!empty($group_cio)) {
                    $DataBRPL->where('groupcio_status', 1);
                } else {
                    $DataBRPL->where('approverdep3_status', 1);
                }
                $fileDataBRPL = $DataBRPL->get();
        // dd($fileDataBRPL);
            $BRPLlabels = [];
            $BRPLapprovedData = [];
            $BRPLrejectedData = [];
            $BRPLpendingData = [];
        
            foreach ($fileDataBRPL as $dataPointBRPL) {
                $monthBRPL = Carbon::createFromFormat('!m', $dataPointBRPL->month)->format('F');
        
                $BRPLlabels[] = $monthBRPL;
                $BRPLapprovedData[] = $dataPointBRPL->approved_count;
                $BRPLrejectedData[] = $dataPointBRPL->rejected_count;
                $BRPLpendingData[] = $dataPointBRPL->pending_count;
                 // dd( $dataPoint->rejected_count);
            }
            if($fiscal_year){
                $BYPLnv = NeedValidation::where('company_id','5')->where('fiscal_year', $fiscal_year)->pluck("id");

            }else{
                $BYPLnv = NeedValidation::where('company_id','5')->where('fiscal_year', $currentFinancialYear)->pluck("id");

            }
            $pendingAmountBYPL = DB::table('tbl_material')
            ->join('nvservicestatus', 'tbl_material.id', '=', 'nvservicestatus.material_id')
            ->whereIn('tbl_material.nv_id', $BYPLnv)
            ->whereIn('nvservicestatus.nv_id', $pen_amt->pluck('nv_id'))
            // ->where('nvservicestatus.work_rew1dep4_status', 0)
            // ->where('nvservicestatus.ceo_nominee_status', 1)
            ->sum('tbl_material.total_budget_both')+DB::table('tbl_service')
            ->join('nvservicestatus', 'tbl_service.id', '=', 'nvservicestatus.service_id')
            ->whereIn('tbl_service.nv_id', $BYPLnv)
            ->whereIn('nvservicestatus.nv_id', $pen_amt->pluck('nv_id'))
            // ->where('nvservicestatus.work_rew1dep4_status', 0)
            // ->where('nvservicestatus.ceo_nominee_status', 1)
            ->sum('tbl_service.total_buget');
            // dd($pendingAmountBYPL);
    
            $rejectedAmountBYPL = DB::table('tbl_material')
            ->join('nvservicestatus', 'tbl_material.id', '=', 'nvservicestatus.material_id')
            ->whereIn('tbl_material.nv_id', $BYPLnv)
            ->where(function ($query) {
                $query->where('nvservicestatus.work_rew1dep4_status', 2);
            })
            ->sum('tbl_material.total_budget_both')+DB::table('tbl_service')
            ->join('nvservicestatus', 'tbl_service.id', '=', 'nvservicestatus.service_id')
            ->whereIn('tbl_service.nv_id', $BYPLnv)
            ->where(function ($query) {
                $query->where('nvservicestatus.work_rew1dep4_status', 2);
            })
            ->sum('tbl_service.total_buget');
       
    
            $approvedAmountBYPL = DB::table('tbl_material')
            ->join('nvservicestatus', 'tbl_material.id', '=', 'nvservicestatus.material_id')
            ->whereIn('tbl_material.nv_id', $BYPLnv)->where('nvservicestatus.ceo_status', 1)
            ->sum('tbl_material.total_budget_both')+DB::table('tbl_service')
            ->join('nvservicestatus', 'tbl_service.id', '=', 'nvservicestatus.service_id')
            ->whereIn('tbl_service.nv_id', $BYPLnv)->where('nvservicestatus.ceo_status', 1)
            ->sum('tbl_service.total_buget');
            $DataBYPL =Nvsericestatus::
            select(
                DB::raw('MONTH(created_at) as month'),
                DB::raw('SUM(CASE WHEN ceo_status = "1" THEN 1 ELSE 0 END) as approved_count'),
                DB::raw('SUM(CASE WHEN ( work_rew3dep4_status = "2")  THEN 1 ELSE 0 END) as rejected_count'),
                DB::raw('SUM(CASE WHEN (work_rew1dep4_status = "0" AND work_rew2dep4_status = "0" AND work_rew3dep4_status = "0" AND work_rew4dep4_status = "0" AND approverdep4_status = "0")  THEN 1 ELSE 0 END) as pending_count'),
                // DB::raw('SUM(CASE WHEN (approverdep3_status = "1" AND work_rew1dep4_status = "1" AND work_rew3dep4_status = "0")  THEN 1 ELSE 0 END) as pending_count'),
                // DB::raw('SUM(CASE WHEN status = "pending" THEN 1 ELSE 0 END) as rejected_count')
            ) 
            ->whereIn('nv_id', $BYPLnv)->with(['service', 'material', 'user'])
            // ->where('approverdep3_status',1)
            ->whereYear('created_at', Carbon::now()->year)
            ->groupBy('month')
            ->orderBy('month');
            // ->get();
            if (!empty($group_cio)) {
                $DataBYPL->where('groupcio_status', 1);
            } else {
                $DataBYPL->where('approverdep3_status', 1);
            }
            
            // Execute the query
            $fileDataBYPL = $DataBYPL->get();
            // dd($fileDataBYPL);
        $BYPLlabels = [];
        $BYPLapprovedData = [];
        $BYPLrejectedData = [];
        $BYPLpendingData = [];
    
        foreach ($fileDataBYPL as $dataPointBYPL) {
            $monthBYPL = Carbon::createFromFormat('!m', $dataPointBYPL->month)->format('F');
    
            $BYPLlabels[] = $monthBYPL;
            $BYPLapprovedData[] = $dataPointBYPL->approved_count;
            $BYPLrejectedData[] = $dataPointBYPL->rejected_count;
            $BYPLpendingData[] = $dataPointBYPL->pending_count;
             // dd( $dataPointBYPL->rejected_count);
        }
                return view("admin.dashboard", compact("approvedAmount","currentFinancialYear","nextFinancialYear","nextToNextFinancialYear", "rejectedAmount", "pendingAmount", "totalAmount","company", "company_id" , "nv_sm_data", "totalNV", "approvedNV", "rejectedNV", "pendingNV" , "hodApproval", "cpmgApproval", "btApproval", "ceoApproval"             ,'BRPLlabels','BRPLapprovedData','BRPLrejectedData','BRPLpendingData','BYPLlabels','BYPLapprovedData','BYPLrejectedData','BYPLpendingData','cesApproval','ceonominee1Approval','ceonominee2Approval','approvedAmountBYPL','approvedAmountBRPL','rejectedAmountBYPL','rejectedAmountBRPL','pendingAmountBYPL','pendingAmountBRPL','groupcioApproval'));
            } elseif ($id4->work_rew2 == $user->id) {
                $Values = [$user->id, $id4->work_rew1];
                $nvid = [];
                $nvstatus = Nvsericestatus::get();
                foreach ($nvstatus as $nvstatus) {
                    array_push($nvid, $nvstatus["nv_id"]);
                }
                $user_nv = NeedValidation::with("division", "service")
                    ->whereIn("id", $nvid)
                    ->orderBy("id", "desc")
                    ->first();
                    if(!empty( $user_nv)){
                $employees = Employee::where("user_id", $user_nv->user_id)->first();
                $department = Department::where("id", $employees->department_id)->first();
                $group_cio = $department->group_cio;}
                if($fiscal_year){
                    $totalId = NeedValidation::where('fiscal_year', $fiscal_year)->pluck('id');

                }else{
                    $totalId = NeedValidation::where('fiscal_year', $currentFinancialYear)->pluck('id');

                }
                // dd($totalId);
                $totalAmount = DB::table('tbl_material')
                ->join('nvservicestatus', 'tbl_material.id', '=', 'nvservicestatus.material_id')
                 // -> whereIn("nv_id", $totalId)
                 ->whereIn('tbl_material.nv_id', $totalId)
                 ->whereIn('nvservicestatus.nv_id', $latestData->pluck('nv_id'))
                ->where('nvservicestatus.work_rew2dep4_status', 1)
                ->sum('tbl_material.total_budget_both') + DB::table('tbl_service')
                ->join('nvservicestatus', 'tbl_service.id', '=', 'nvservicestatus.service_id')
                 // -> whereIn("nv_id", $totalId)
                 ->whereIn('tbl_service.nv_id', $totalId)
                 ->whereIn('nvservicestatus.nv_id', $latestData->pluck('nv_id'))
                ->where('nvservicestatus.work_rew2dep4_status', 1)
                ->sum('tbl_service.total_buget');
                // dd($totalAmount);
                if(!empty( $group_cio)){
                    $pen_amt = $latestData->filter(function ($data) {
                        return in_array($data->groupcio_status, [1]) &&
                            in_array($data->work_rew1dep4_status, [0]) &&
                            in_array($data->work_rew2dep4_status, [0]) &&
                            in_array($data->work_rew3dep4_status, [0]) &&
                            in_array($data->work_rew4dep4_status, [0]) &&
                            in_array($data->approverdep4_status, [0]) ;
                    });
                    }else{
                        $pen_amt = $latestData->filter(function ($data) {
                            return in_array($data->approverdep3_status, [1]) &&
                                in_array($data->work_rew1dep4_status, [0]) &&
                                in_array($data->work_rew2dep4_status, [0]) &&
                                in_array($data->work_rew3dep4_status, [0]) &&
                                in_array($data->work_rew4dep4_status, [0]) &&
                                in_array($data->approverdep4_status, [0]) ;
                        });   
                    }
                $pendingAmount = DB::table('tbl_material')
                ->join('nvservicestatus', 'tbl_material.id', '=', 'nvservicestatus.material_id')
                 // -> whereIn("nv_id", $totalId)
                 ->whereIn('tbl_material.nv_id', $totalId)
                  ->whereIn('nvservicestatus.nv_id', $pen_amt->pluck('nv_id'))
                // ->where('nvservicestatus.work_rew2dep4_status', 0)
                // ->where('nvservicestatus.work_rew1dep4_status', 1)
                ->sum('tbl_material.total_budget_both') + DB::table('tbl_service')
                ->join('nvservicestatus', 'tbl_service.id', '=', 'nvservicestatus.service_id')
                 // -> whereIn("nv_id", $totalId)
                 ->whereIn('tbl_service.nv_id', $totalId)
                  ->whereIn('nvservicestatus.nv_id', $pen_amt->pluck('nv_id'))
                // ->where('nvservicestatus.work_rew2dep4_status', 0)
                // ->where('nvservicestatus.work_rew1dep4_status', 1)
                ->sum('tbl_service.total_buget');
        
                $rejectedAmount = DB::table('tbl_material')
                ->join('nvservicestatus', 'tbl_material.id', '=', 'nvservicestatus.material_id')
                ->whereIn('nvservicestatus.nv_id', $latestData->pluck('nv_id'))
                ->where(function ($query) {
                    $query->where('nvservicestatus.work_rew2dep4_status', 2);
                })
                ->sum('tbl_material.total_budget_both') + DB::table('tbl_service')
                ->join('nvservicestatus', 'tbl_service.id', '=', 'nvservicestatus.service_id')
                ->whereIn('nvservicestatus.nv_id', $latestData->pluck('nv_id'))
                ->where(function ($query) {
                    $query->where('nvservicestatus.work_rew2dep4_status', 2);
                })
                ->sum('tbl_service.total_buget');
           
        
                $approvedAmount = DB::table('tbl_material')
                 // -> whereIn("nv_id", $totalId)
                 ->whereIn('tbl_material.nv_id', $totalId)
                 ->whereIn('nvservicestatus.nv_id', $latestData->pluck('nv_id'))
                ->join('nvservicestatus', 'tbl_material.id', '=', 'nvservicestatus.material_id')
                ->where('nvservicestatus.ceo_status', 1)
                ->sum('tbl_material.total_budget_both') + DB::table('tbl_service')
                // -> whereIn("nv_id", $totalId)
                ->whereIn('tbl_service.nv_id', $totalId)
                ->whereIn('nvservicestatus.nv_id', $latestData->pluck('nv_id'))
               ->join('nvservicestatus', 'tbl_service.id', '=', 'nvservicestatus.service_id')
               ->where('nvservicestatus.ceo_status', 1)
               ->sum('tbl_service.total_buget');

                if($company_id){
                    $totalNV = Nvsericestatus::whereIn("nv_id", $totalId)->where('work_rew2dep4_status', 1)->where('company_id',$company_id)->count();
                } else {
                    $totalNV = Nvsericestatus::whereIn("nv_id", $totalId)->where('work_rew2dep4_status', 1)->count();
                }
                if($company_id){
                    $rejectedNV = Nvsericestatus::whereIn("nv_id", $totalId)->where('work_rew2dep4_status', 2)->where('company_id',$company_id)->count();
                } else {
                    $rejectedNV = Nvsericestatus::whereIn("nv_id", $totalId)->where('work_rew2dep4_status', 2)->count();
                }
                // dd($totalId);
                if($company_id){
                $latestData = Nvsericestatus::whereIn("nv_id", $totalId)->where('company_id',$company_id)->get();
            }else {
                $latestData = Nvsericestatus::whereIn("nv_id", $totalId)->get();
            }
                if($company_id){
                    $approvedNV = $latestData->where('ceo_status', 1)->where('company_id',$company_id)->count();
                }else{
                    $approvedNV = $latestData->where('ceo_status', 1)->count();
                }
                // $rejectedNV = $latestData->filter(function ($data) {
                //     return in_array($data->ceo_status, [2]) || 
                //     $data->work_rew1_status == 2 ;
                //     // $data->hod_status == 2 || 
                //     // $data->cpmg_status == 2 || 
                //     // $data->ces_status == 2 || 
                //     // $data->cto_status == 2 || 
                //     // $data->ceo_nominee_status == 2 || 
                //     // $data->ceo_nominee2_status == 2 || 
                //     // $data->ceo_nominee2_status == 2;
                // })->count();
                if(!empty($group_cio)){
                $pendingNV = $latestData->filter(function ($data) {
                    return in_array($data->groupcio_status, [1]) &&
                        in_array($data->work_rew1dep4_status, [0]) &&
                        in_array($data->work_rew2dep4_status, [0]) &&
                        in_array($data->work_rew3dep4_status, [0]) &&
                        in_array($data->work_rew4dep4_status, [0]) &&
                        in_array($data->approverdep4_status, [0]) ;
                })->count();
                }else{
                    $pendingNV = $latestData->filter(function ($data) {
                        return in_array($data->approverdep3_status, [1]) &&
                            in_array($data->work_rew1dep4_status, [0]) &&
                            in_array($data->work_rew2dep4_status, [0]) &&
                            in_array($data->work_rew3dep4_status, [0]) &&
                            in_array($data->work_rew4dep4_status, [0]) &&
                            in_array($data->approverdep4_status, [0]) ;
                    })->count(); 
                }
                // $pendingNV = $latestData->filter(function ($data) {
                //     return in_array($data->approverdep3_status, [ 1]) &&
                //     in_array($data->work_rew1dep4_status, [1])&&
                //     in_array($data->work_rew2dep4_status, [0]);
                  
                //     })->count();
                 

                 $hodApproval = Nvsericestatus::whereIn("nv_id", $totalId)->where("hod_status", 0)->count();
                $cesApproval = Nvsericestatus::whereIn("nv_id", $totalId)->where("hod_status", 1)->where("ces_status", 0)->where("derc_info", 1)->count();
                $cpmg = Nvsericestatus::whereIn("nv_id", $totalId)->where('hod_status', 1)->where('cpmg_status', 0)->get();
                $Pendingcpmg = $cpmg->filter(function ($data) {
                 return in_array($data->hod_status, [1]) &&
                     in_array($data->cpmg_status, [0]) &&
                     (
                         (in_array($data->derc_info, [0]) && in_array($data->ces_status, [0]))
                         ||
                         (in_array($data->derc_info, [1]) && in_array($data->ces_status, [1]))
                     );
             });
             $cpmgApproval = $Pendingcpmg->count(); 

                $btApproval = Nvsericestatus::whereIn("nv_id", $totalId)->where("cpmg_status", 1)->where("check_technology", 1)->where('cto_status', 0)->count();
              
                $ceonominee1 = Nvsericestatus::whereIn("nv_id", $totalId)->where('cpmg_status', 1)->where('ceo_nominee_status', 0)->get();
                 $Pendingnominee1 = $ceonominee1->filter(function ($data) {
                 return in_array($data->cpmg_status, [1]) &&
                     in_array($data->ceo_nominee_status, [0]) &&
                     (
                         (in_array($data->check_technology, [0]) && in_array($data->cto_status, [0]))
                         ||
                         (in_array($data->check_technology, [1]) && in_array($data->cto_status, [1]))
                     );
             });
             $ceonominee1Approval = $Pendingnominee1->count(); 
                // $cpmgApproval = Nvsericestatus::whereIn("nv_id", $totalId)->where(function ($query) {
                //     $query->whereIn('ces_status', [0,1]);
                // })
                //     ->Where(function ($query) {
                //         $query->where('hod_status', 1);
                //     })
                //     ->where(function ($query) {
                //         $query->where('derc_info', 0);
                //     })
                //     ->where(function ($query) {
                //         $query->where('cpmg_status', 0);
                //     })
                //        ->count();
             
                // $btApproval = Nvsericestatus::whereIn("nv_id", $totalId)->where("cpmg_status", 1)->where("check_technology", 1)->where('cto_status', 0)->count();
                // $ceonominee1Approval = Nvsericestatus::whereIn("nv_id", $totalId)->where("cpmg_status", 1)->whereIn('cto_status', [0,1])->whereIn("check_technology", [0,1])->where('ceo_nominee_status', 0)->count();
                if(!empty( $group_cio)){
                    $ceonominee2Approval = Nvsericestatus::where('groupcio_status', 1)->where('ceo_nominee2_status', 0)->whereIn("nv_id", $totalId)->count();
                    }else{
                        $ceonominee2Approval = Nvsericestatus::where('ceo_nominee_status', 1)->where('ceo_nominee2_status', 0)->whereIn("nv_id", $totalId)->count();
         
                    }
                if(!empty( $group_cio)){
                    $groupcioApproval = Nvsericestatus::where('ceo_nominee_status', 1)->where('groupcio_status', 0)->where('ceo_nominee2_status', 0)->whereIn("nv_id", $totalId)->count();
                    }else{
                        $groupcioApproval =0;  
                    }
        
                        // if(!empty( $group_cio)){
                        // $ceoApproval = Nvsericestatus::where('groupcio_status', 1)->where('ceo_status', 0)->whereIn("nv_id", $totalId)->count();
                        // }else{
                        $ceoApproval = Nvsericestatus::where('ceo_nominee2_status', 1)->where('ceo_status', 0)->whereIn("nv_id", $totalId)->count();
                        // }

             
                $nvIds = NeedValidation::where("user_id", $Values)->pluck("id");
                if($fiscal_year){
                    $nv = NeedValidation::whereHas('service')->select('id')->where('fiscal_year', $fiscal_year)
                    // ->whereIn('user_id', $Values)
                    // ->orWhereIn('user_id',$allNormalUsers)
                    ->get();

                }else{
                    $nv = NeedValidation::whereHas('service')->select('id')->where('fiscal_year', $currentFinancialYear)
                    // ->whereIn('user_id', $Values)
                    // ->orWhereIn('user_id',$allNormalUsers)
                    ->get();

                }
             
                $nv_ids = $nv->pluck('id');
                // $nv_sm_data = Nvsericestatus::whereIn('nv_id', $nv_ids)
                // ->with(['service', 'material', 'user'])
                // ->where('hod_status',1)
                // ->orderBy('id', 'desc')
                // ->get();
                if(!empty( $group_cio)){
                $nv_sm_data = Nvsericestatus::whereIn('nv_id', $nv_ids)->with(['service', 'material', 'user'])
                ->where('groupcio_status', 1)
               ->orderBy('id', 'asc')->get();
                }else{
                    $nv_sm_data = Nvsericestatus::whereIn('nv_id', $nv_ids)->with(['service', 'material', 'user'])
                ->where('approverdep3_status', 1)
               ->orderBy('id', 'asc')->get();
                }

                $total24_status = $request->total;
                $pending24_status = $request->pending;
                $reject24_status = $request->rejected;

                if ($ceo_status !== null) {
                    $nv_sm_data = $nv_sm_data->where('ceo_status', $ceo_status);
                } elseif ($pending24_status !== null) {
                    $nv_sm_data = $nv_sm_data->where('work_rew2dep4_status', $pending24_status)
                    ->where('work_rew1dep4_status', 0)
                    ->where('work_rew3dep4_status', 0)
                    ->where('work_rew4dep4_status', 0);
                       
                  } elseif ($reject24_status !== null) {
                    $nv_sm_data = $nv_sm_data->where('work_rew2dep4_status', $reject24_status);
                    
                }elseif ($total24_status !== null) {
                    $nv_sm_data = $nv_sm_data->where('work_rew2dep4_status', $total24_status);
                      
                }
                if($fiscal_year){
                    $BRPLnv = NeedValidation::where('company_id','6')->where('fiscal_year', $fiscal_year)->pluck("id");
    
                }else{
                    $BRPLnv = NeedValidation::where('company_id','6')->where('fiscal_year', $currentFinancialYear)->pluck("id");
    
                }

                $pendingAmountBRPL = DB::table('tbl_material')
                ->join('nvservicestatus', 'tbl_material.id', '=', 'nvservicestatus.material_id')
                ->whereIn('tbl_material.nv_id', $BRPLnv)
                ->whereIn('nvservicestatus.nv_id', $pen_amt->pluck('nv_id'))
                // ->where('nvservicestatus.work_rew2dep4_status', 0)
                // ->where('nvservicestatus.work_rew1dep4_status', 1)
                ->sum('tbl_material.total_budget_both')+DB::table('tbl_service')
                ->join('nvservicestatus', 'tbl_service.id', '=', 'nvservicestatus.service_id')
                ->whereIn('tbl_service.nv_id', $BRPLnv)
                ->whereIn('nvservicestatus.nv_id', $pen_amt->pluck('nv_id'))
                // ->where('nvservicestatus.work_rew2dep4_status', 0)
                // ->where('nvservicestatus.work_rew1dep4_status', 1)
                ->sum('tbl_service.total_buget');
                // dd($pendingAmountBRPL);
        
                $rejectedAmountBRPL = DB::table('tbl_material')
                ->join('nvservicestatus', 'tbl_material.id', '=', 'nvservicestatus.material_id')
                ->whereIn('tbl_material.nv_id', $BRPLnv)
                ->where(function ($query) {
                    $query->where('nvservicestatus.work_rew2dep4_status', 2);
                })
                ->sum('tbl_material.total_budget_both')+DB::table('tbl_service')
                ->join('nvservicestatus', 'tbl_service.id', '=', 'nvservicestatus.service_id')
                ->whereIn('tbl_service.nv_id', $BRPLnv)
                ->where(function ($query) {
                    $query->where('nvservicestatus.work_rew2dep4_status', 2);
                })
                ->sum('tbl_service.total_buget');
           
        
                $approvedAmountBRPL = DB::table('tbl_material')
                ->join('nvservicestatus', 'tbl_material.id', '=', 'nvservicestatus.material_id')
                ->whereIn('tbl_material.nv_id', $BRPLnv)->where('nvservicestatus.ceo_status', 1)
                ->sum('tbl_material.total_budget_both')+DB::table('tbl_service')
                ->join('nvservicestatus', 'tbl_service.id', '=', 'nvservicestatus.service_id')
                ->whereIn('tbl_service.nv_id', $BRPLnv)->where('nvservicestatus.ceo_status', 1)
                ->sum('tbl_service.total_buget');
                $DataBRPL =Nvsericestatus::
                select(
                    DB::raw('MONTH(created_at) as month'),
                    DB::raw('SUM(CASE WHEN ceo_status = "1" THEN 1 ELSE 0 END) as approved_count'),
                    DB::raw('SUM(CASE WHEN ( work_rew3dep4_status = "2")  THEN 1 ELSE 0 END ) as rejected_count'),
                    DB::raw('SUM(CASE WHEN (work_rew1dep4_status = "0" AND work_rew2dep4_status = "0" AND work_rew3dep4_status = "0" AND work_rew4dep4_status = "0" AND approverdep4_status = "0")  THEN 1 ELSE 0 END) as pending_count'),
                    // DB::raw('SUM(CASE WHEN (approverdep3_status = "1" AND work_rew1dep4_status = "1" AND work_rew3dep4_status = "0") THEN 1 ELSE 0 END) as pending_count'),
                    // DB::raw('SUM(CASE WHEN status = "pending" THEN 1 ELSE 0 END) as rejected_count')
                )
                // -> where('ceo_nominee_status',1)
                ->whereIn('nv_id', $BRPLnv)->with(['service', 'material', 'user'])
                // ->where('approverdep3_status',1)
                ->whereYear('created_at', Carbon::now()->year)
                ->groupBy('month')
                ->orderBy('month');
                // ->get();
                if (!empty($group_cio)) {
                    $DataBRPL->where('groupcio_status', 1);
                } else {
                    $DataBRPL->where('approverdep3_status', 1);
                }
                $fileDataBRPL = $DataBRPL->get();
        // dd($fileDataBRPL);
            $BRPLlabels = [];
            $BRPLapprovedData = [];
            $BRPLrejectedData = [];
            $BRPLpendingData = [];
        
            foreach ($fileDataBRPL as $dataPointBRPL) {
                $monthBRPL = Carbon::createFromFormat('!m', $dataPointBRPL->month)->format('F');
        
                $BRPLlabels[] = $monthBRPL;
                $BRPLapprovedData[] = $dataPointBRPL->approved_count;
                $BRPLrejectedData[] = $dataPointBRPL->rejected_count;
                $BRPLpendingData[] = $dataPointBRPL->pending_count;
                 // dd( $dataPoint->rejected_count);
            }
            if($fiscal_year){
                $BYPLnv = NeedValidation::where('company_id','5')->where('fiscal_year', $fiscal_year)->pluck("id");

            }else{
                $BYPLnv = NeedValidation::where('company_id','5')->where('fiscal_year', $currentFinancialYear)->pluck("id");

            }
            $pendingAmountBYPL = DB::table('tbl_material')
            ->join('nvservicestatus', 'tbl_material.id', '=', 'nvservicestatus.material_id')
            ->whereIn('tbl_material.nv_id', $BYPLnv)
            ->whereIn('nvservicestatus.nv_id', $pen_amt->pluck('nv_id'))
            // ->where('nvservicestatus.work_rew2dep4_status', 0)
            // ->where('nvservicestatus.work_rew1dep4_status', 1)
            ->sum('tbl_material.total_budget_both')+DB::table('tbl_service')
            ->join('nvservicestatus', 'tbl_service.id', '=', 'nvservicestatus.service_id')
            ->whereIn('tbl_service.nv_id', $BYPLnv)
            ->whereIn('nvservicestatus.nv_id', $pen_amt->pluck('nv_id'))
            // ->where('nvservicestatus.work_rew2dep4_status', 0)
            // ->where('nvservicestatus.work_rew1dep4_status', 1)
            ->sum('tbl_service.total_buget');
            // dd($pendingAmountBYPL);
    
            $rejectedAmountBYPL = DB::table('tbl_material')
            ->join('nvservicestatus', 'tbl_material.id', '=', 'nvservicestatus.material_id')
            ->whereIn('tbl_material.nv_id', $BYPLnv)
            ->where(function ($query) {
                $query->where('nvservicestatus.work_rew2dep4_status', 2);
            })
            ->sum('tbl_material.total_budget_both')+DB::table('tbl_service')
            ->join('nvservicestatus', 'tbl_service.id', '=', 'nvservicestatus.service_id')
            ->whereIn('tbl_service.nv_id', $BYPLnv)
            ->where(function ($query) {
                $query->where('nvservicestatus.work_rew2dep4_status', 2);
            })
            ->sum('tbl_service.total_buget');
       
    
            $approvedAmountBYPL = DB::table('tbl_material')
            ->join('nvservicestatus', 'tbl_material.id', '=', 'nvservicestatus.material_id')
            ->whereIn('tbl_material.nv_id', $BYPLnv)->where('nvservicestatus.ceo_status', 1)
            ->sum('tbl_material.total_budget_both')+DB::table('tbl_service')
            ->join('nvservicestatus', 'tbl_service.id', '=', 'nvservicestatus.service_id')
            ->whereIn('tbl_service.nv_id', $BYPLnv)->where('nvservicestatus.ceo_status', 1)
            ->sum('tbl_service.total_buget');
            $DataBYPL =Nvsericestatus::
            select(
                DB::raw('MONTH(created_at) as month'),
                DB::raw('SUM(CASE WHEN ceo_status = "1" THEN 1 ELSE 0 END) as approved_count'),
                DB::raw('SUM(CASE WHEN ( work_rew3dep4_status = "2")  THEN 1 ELSE 0 END) as rejected_count'),
                DB::raw('SUM(CASE WHEN (work_rew1dep4_status = "0" AND work_rew2dep4_status = "0" AND work_rew3dep4_status = "0" AND work_rew4dep4_status = "0" AND approverdep4_status = "0")  THEN 1 ELSE 0 END) as pending_count'),
                // DB::raw('SUM(CASE WHEN (approverdep3_status = "1" AND work_rew1dep4_status = "1" AND work_rew3dep4_status = "0")  THEN 1 ELSE 0 END) as pending_count'),
                // DB::raw('SUM(CASE WHEN status = "pending" THEN 1 ELSE 0 END) as rejected_count')
            ) 
            ->whereIn('nv_id', $BYPLnv)->with(['service', 'material', 'user'])
            // ->where('approverdep3_status',1)
            ->whereYear('created_at', Carbon::now()->year)
            ->groupBy('month')
            ->orderBy('month');
            // ->get();
            if (!empty($group_cio)) {
                $DataBYPL->where('groupcio_status', 1);
            } else {
                $DataBYPL->where('approverdep3_status', 1);
            }
            
            // Execute the query
            $fileDataBYPL = $DataBYPL->get();
            // dd($fileDataBYPL);
        $BYPLlabels = [];
        $BYPLapprovedData = [];
        $BYPLrejectedData = [];
        $BYPLpendingData = [];
    
        foreach ($fileDataBYPL as $dataPointBYPL) {
            $monthBYPL = Carbon::createFromFormat('!m', $dataPointBYPL->month)->format('F');
    
            $BYPLlabels[] = $monthBYPL;
            $BYPLapprovedData[] = $dataPointBYPL->approved_count;
            $BYPLrejectedData[] = $dataPointBYPL->rejected_count;
            $BYPLpendingData[] = $dataPointBYPL->pending_count;
             // dd( $dataPointBYPL->rejected_count);
        }
                return view("admin.dashboard", compact("approvedAmount","currentFinancialYear","nextFinancialYear","nextToNextFinancialYear", "rejectedAmount", "pendingAmount", "totalAmount","company", "company_id" , "nv_sm_data", "totalNV", "approvedNV", "rejectedNV", "pendingNV" , "hodApproval", "cpmgApproval", "btApproval", "ceoApproval"             ,'BRPLlabels','BRPLapprovedData','BRPLrejectedData','BRPLpendingData','BYPLlabels','BYPLapprovedData','BYPLrejectedData','BYPLpendingData','cesApproval','ceonominee1Approval','ceonominee2Approval','approvedAmountBYPL','approvedAmountBRPL','pendingAmountBYPL','pendingAmountBRPL','rejectedAmountBYPL','rejectedAmountBRPL','groupcioApproval'));
            } elseif ($id4->work_rew3 == $user->id) {
                $Values = [$user->id, $id4->work_rew1, $id4->work_rew2];
                $nvid = [];
                $nvstatus = Nvsericestatus::get();
                foreach ($nvstatus as $nvstatus) {
                    array_push($nvid, $nvstatus["nv_id"]);
                }
                $user_nv = NeedValidation::with("division", "service")
                    ->whereIn("id", $nvid)
                    ->orderBy("id", "desc")
                    ->first();
                    if(!empty( $user_nv)){
                $employees = Employee::where("user_id", $user_nv->user_id)->first();
                $department = Department::where("id", $employees->department_id)->first();
                $group_cio = $department->group_cio;}
                if($fiscal_year){
                    $totalId = NeedValidation::where('fiscal_year', $fiscal_year)->pluck('id');
                }else{
                    $totalId = NeedValidation::where('fiscal_year', $currentFinancialYear)->pluck('id');
                }
                 
                // dd($totalId);
                $totalAmount = DB::table('tbl_material')
                ->join('nvservicestatus', 'tbl_material.id', '=', 'nvservicestatus.material_id')
                 // -> whereIn("nv_id", $totalId)
                 ->whereIn('tbl_material.nv_id', $totalId)
                 ->whereIn('nvservicestatus.nv_id', $latestData->pluck('nv_id'))
                ->where('nvservicestatus.work_rew3dep4_status', 1)
                ->sum('tbl_material.total_budget_both') + DB::table('tbl_service')
                ->join('nvservicestatus', 'tbl_service.id', '=', 'nvservicestatus.service_id')
                 // -> whereIn("nv_id", $totalId)
                 ->whereIn('tbl_service.nv_id', $totalId)
                 ->whereIn('nvservicestatus.nv_id', $latestData->pluck('nv_id'))
                ->where('nvservicestatus.work_rew3dep4_status', 1)
                ->sum('tbl_service.total_buget');
                // dd($totalAmount);
                 if(!empty( $group_cio)){
                $pen_amt = $latestData->filter(function ($data) {
                    return in_array($data->groupcio_status, [1]) &&
                        in_array($data->work_rew1dep4_status, [0]) &&
                        in_array($data->work_rew2dep4_status, [0]) &&
                        in_array($data->work_rew3dep4_status, [0]) &&
                        in_array($data->work_rew4dep4_status, [0]) &&
                        in_array($data->approverdep4_status, [0]) ;
                });
                }else{
                    $pen_amt = $latestData->filter(function ($data) {
                        return in_array($data->approverdep3_status, [1]) &&
                            in_array($data->work_rew1dep4_status, [0]) &&
                            in_array($data->work_rew2dep4_status, [0]) &&
                            in_array($data->work_rew3dep4_status, [0]) &&
                            in_array($data->work_rew4dep4_status, [0]) &&
                            in_array($data->approverdep4_status, [0]) ;
                    });
                }
                $pendingAmount = DB::table('tbl_material')
                ->join('nvservicestatus', 'tbl_material.id', '=', 'nvservicestatus.material_id')
                 // -> whereIn("nv_id", $totalId)
                 ->whereIn('tbl_material.nv_id', $totalId)
                 ->whereIn('nvservicestatus.nv_id', $pen_amt->pluck('nv_id'))
                // ->where('nvservicestatus.work_rew3dep4_status', 0)
                // ->where('nvservicestatus.work_rew2dep4_status', 1)
                ->sum('tbl_material.total_budget_both') + DB::table('tbl_service')
                ->join('nvservicestatus', 'tbl_service.id', '=', 'nvservicestatus.service_id')
                 // -> whereIn("nv_id", $totalId)
                 ->whereIn('tbl_service.nv_id', $totalId)
                 ->whereIn('nvservicestatus.nv_id', $pen_amt->pluck('nv_id'))
                // ->where('nvservicestatus.work_rew3dep4_status', 0)
                // ->where('nvservicestatus.work_rew2dep4_status', 1)
                ->sum('tbl_service.total_buget');
        
                $rejectedAmount = DB::table('tbl_material')
                ->join('nvservicestatus', 'tbl_material.id', '=', 'nvservicestatus.material_id')
                ->whereIn('nvservicestatus.nv_id', $latestData->pluck('nv_id'))
                ->where(function ($query) {
                    $query->where('nvservicestatus.work_rew3dep4_status', 2);
                })
                ->sum('tbl_material.total_budget_both') + DB::table('tbl_service')
                ->join('nvservicestatus', 'tbl_service.id', '=', 'nvservicestatus.service_id')
                ->whereIn('nvservicestatus.nv_id', $latestData->pluck('nv_id'))
                ->where(function ($query) {
                    $query->where('nvservicestatus.work_rew3dep4_status', 2);
                })
                ->sum('tbl_service.total_buget');
           
        
                $approvedAmount = DB::table('tbl_material')
                 // -> whereIn("nv_id", $totalId)
                 ->whereIn('tbl_material.nv_id', $totalId)
                 ->whereIn('nvservicestatus.nv_id', $latestData->pluck('nv_id'))
                ->join('nvservicestatus', 'tbl_material.id', '=', 'nvservicestatus.material_id')
                ->where('nvservicestatus.ceo_status', 1)
                ->sum('tbl_material.total_budget_both') + DB::table('tbl_service')
                // -> whereIn("nv_id", $totalId)
                ->whereIn('tbl_service.nv_id', $totalId)
                ->whereIn('nvservicestatus.nv_id', $latestData->pluck('nv_id'))
               ->join('nvservicestatus', 'tbl_service.id', '=', 'nvservicestatus.service_id')
               ->where('nvservicestatus.ceo_status', 1)
               ->sum('tbl_service.total_buget');

                if($company_id){
                    $totalNV = Nvsericestatus::whereIn("nv_id", $totalId)->where('work_rew3dep4_status', 1)->where('company_id',$company_id)->count();
                } else {
                    $totalNV = Nvsericestatus::whereIn("nv_id", $totalId)->where('work_rew3dep4_status', 1)->count();
                }
                if($company_id){
                    $rejectedNV = Nvsericestatus::whereIn("nv_id", $totalId)->where('work_rew3dep4_status', 2)->where('company_id',$company_id)->count();
                } else {
                    $rejectedNV = Nvsericestatus::whereIn("nv_id", $totalId)->where('work_rew3dep4_status', 2)->count();
                }
                // dd($totalId);
                if($company_id){
                $latestData = Nvsericestatus::whereIn("nv_id", $totalId)->where('company_id',$company_id)->get();
            }else {
                $latestData = Nvsericestatus::whereIn("nv_id", $totalId)->get();
            }
                if($company_id){
                    $approvedNV = $latestData->where('ceo_status', 1)->where('company_id',$company_id)->count();
                }else{
                    $approvedNV = $latestData->where('ceo_status', 1)->count();
                }
                // $rejectedNV = $latestData->filter(function ($data) {
                //     return in_array($data->ceo_status, [2]) || 
                //     $data->work_rew1_status == 2 ;
                //     // $data->hod_status == 2 || 
                //     // $data->cpmg_status == 2 || 
                //     // $data->ces_status == 2 || 
                //     // $data->cto_status == 2 || 
                //     // $data->ceo_nominee_status == 2 || 
                //     // $data->ceo_nominee2_status == 2 || 
                //     // $data->ceo_nominee2_status == 2;
                // })->count();
                if(!empty( $group_cio)){
                $pendingNV = $latestData->filter(function ($data) {
                    return in_array($data->groupcio_status, [1]) &&
                        in_array($data->work_rew1dep4_status, [0]) &&
                        in_array($data->work_rew2dep4_status, [0]) &&
                        in_array($data->work_rew3dep4_status, [0]) &&
                        in_array($data->work_rew4dep4_status, [0]) &&
                        in_array($data->approverdep4_status, [0]) ;
                })->count();
            }else{
                $pendingNV = $latestData->filter(function ($data) {
                    return in_array($data->approverdep3_status, [1]) &&
                        in_array($data->work_rew1dep4_status, [0]) &&
                        in_array($data->work_rew2dep4_status, [0]) &&
                        in_array($data->work_rew3dep4_status, [0]) &&
                        in_array($data->work_rew4dep4_status, [0]) &&
                        in_array($data->approverdep4_status, [0]) ;
                })->count(); 
            }
                // $pendingNV = $latestData->filter(function ($data) {
                //     return in_array($data->approverdep3_status, [ 1]) &&
                //     in_array($data->work_rew1dep4_status, [1])&&
                //     in_array($data->work_rew3dep4_status, [0]);
                //     })->count();
                 

                $hodApproval = Nvsericestatus::whereIn("nv_id", $totalId)->where("hod_status", 0)->count();
                $cesApproval = Nvsericestatus::whereIn("nv_id", $totalId)->where("hod_status", 1)->where("ces_status", 0)->where("derc_info", 1)->count();
                $cpmg = Nvsericestatus::whereIn("nv_id", $totalId)->where('hod_status', 1)->where('cpmg_status', 0)->get();
                $Pendingcpmg = $cpmg->filter(function ($data) {
                 return in_array($data->hod_status, [1]) &&
                     in_array($data->cpmg_status, [0]) &&
                     (
                         (in_array($data->derc_info, [0]) && in_array($data->ces_status, [0]))
                         ||
                         (in_array($data->derc_info, [1]) && in_array($data->ces_status, [1]))
                     );
             });
             $cpmgApproval = $Pendingcpmg->count(); 

                $btApproval = Nvsericestatus::whereIn("nv_id", $totalId)->where("cpmg_status", 1)->where("check_technology", 1)->where('cto_status', 0)->count();
              
                $ceonominee1 = Nvsericestatus::whereIn("nv_id", $totalId)->where('cpmg_status', 1)->where('ceo_nominee_status', 0)->get();
                 $Pendingnominee1 = $ceonominee1->filter(function ($data) {
                 return in_array($data->cpmg_status, [1]) &&
                     in_array($data->ceo_nominee_status, [0]) &&
                     (
                         (in_array($data->check_technology, [0]) && in_array($data->cto_status, [0]))
                         ||
                         (in_array($data->check_technology, [1]) && in_array($data->cto_status, [1]))
                     );
             });
             $ceonominee1Approval = $Pendingnominee1->count(); 
             if(!empty( $group_cio)){
                $ceonominee2Approval = Nvsericestatus::where('groupcio_status', 1)->where('ceo_nominee2_status', 0)->whereIn("nv_id", $totalId)->count();
                }else{
                    $ceonominee2Approval = Nvsericestatus::where('ceo_nominee_status', 1)->where('ceo_nominee2_status', 0)->whereIn("nv_id", $totalId)->count();
     
                }
            if(!empty( $group_cio)){
                $groupcioApproval = Nvsericestatus::where('ceo_nominee_status', 1)->where('groupcio_status', 0)->where('ceo_nominee2_status', 0)->whereIn("nv_id", $totalId)->count();
                }else{
                    $groupcioApproval =0;  
                }
    
                    // if(!empty( $group_cio)){
                    // $ceoApproval = Nvsericestatus::where('groupcio_status', 1)->where('ceo_status', 0)->whereIn("nv_id", $totalId)->count();
                    // }else{
                    $ceoApproval = Nvsericestatus::where('ceo_nominee2_status', 1)->where('ceo_status', 0)->whereIn("nv_id", $totalId)->count();
                    // }

               
                $nvIds = NeedValidation::where("user_id", $Values)->pluck("id");
                if($fiscal_year){
                    $nv = NeedValidation::whereHas('service')->select('id')->where('fiscal_year', $fiscal_year)
                // ->whereIn('user_id', $Values)
                // ->orWhereIn('user_id',$allNormalUsers)
                ->get();
                }else{
                    $nv = NeedValidation::whereHas('service')->select('id')->where('fiscal_year', $currentFinancialYear)
                    // ->whereIn('user_id', $Values)
                    // ->orWhereIn('user_id',$allNormalUsers)
                    ->get();
                }
             
                $nv_ids = $nv->pluck('id');
                // $nv_sm_data = Nvsericestatus::whereIn('nv_id', $nv_ids)
                // ->with(['service', 'material', 'user'])
                // ->where('hod_status',1)
                // ->orderBy('id', 'desc')
                // ->get();
                if(!empty( $group_cio)){
                $nv_sm_data = Nvsericestatus::whereIn('nv_id', $nv_ids)->with(['service', 'material', 'user'])
                 ->where('groupcio_status', 1)
                ->orderBy('id', 'asc')->get();
                }else{
                    $nv_sm_data = Nvsericestatus::whereIn('nv_id', $nv_ids)->with(['service', 'material', 'user'])
                    ->where('approverdep3_status', 1)
                   ->orderBy('id', 'asc')->get();
                }

                $total25_status = $request->total;
                $pending25_status = $request->pending;
                $reject25_status = $request->rejected;

              if ($ceo_status !== null) {
                    $nv_sm_data = $nv_sm_data->where('ceo_status', $ceo_status);
                } elseif ($pending25_status !== null) {
                    $nv_sm_data = $nv_sm_data->where('work_rew3dep4_status', $pending25_status)
                    ->where('work_rew1dep4_status', 0)
                    ->where('work_rew2dep4_status', 0)
                    ->where('work_rew4dep4_status', 0);
                } elseif ($reject25_status !== null) {
                    $nv_sm_data = $nv_sm_data->where('work_rew3dep4_status', $reject25_status);
                    
                }elseif ($total25_status !== null) {
                    $nv_sm_data = $nv_sm_data->where('work_rew3dep4_status', $total25_status);
                      
                }
                if($fiscal_year){
                    $BRPLnv = NeedValidation::where('company_id','6')->where('fiscal_year', $fiscal_year)->pluck("id");
    
                }else{
                    $BRPLnv = NeedValidation::where('company_id','6')->where('fiscal_year', $currentFinancialYear)->pluck("id");
    
                }

                $pendingAmountBRPL = DB::table('tbl_material')
                ->join('nvservicestatus', 'tbl_material.id', '=', 'nvservicestatus.material_id')
                ->whereIn('tbl_material.nv_id', $BRPLnv)
                ->whereIn('nvservicestatus.nv_id', $pen_amt->pluck('nv_id'))
                // ->where('nvservicestatus.work_rew3dep4_status', 0)
                // ->where('nvservicestatus.work_rew2dep4_status', 1)
                ->sum('tbl_material.total_budget_both')+DB::table('tbl_service')
                ->join('nvservicestatus', 'tbl_service.id', '=', 'nvservicestatus.service_id')
                ->whereIn('tbl_service.nv_id', $BRPLnv)
                ->whereIn('nvservicestatus.nv_id', $pen_amt->pluck('nv_id'))
                // ->where('nvservicestatus.work_rew3dep4_status', 0)
                // ->where('nvservicestatus.work_rew2dep4_status', 1)
                ->sum('tbl_service.total_buget');
                // dd($pendingAmountBRPL);
        
                $rejectedAmountBRPL = DB::table('tbl_material')
                ->join('nvservicestatus', 'tbl_material.id', '=', 'nvservicestatus.material_id')
                ->whereIn('tbl_material.nv_id', $BRPLnv)
                ->where(function ($query) {
                    $query->where('nvservicestatus.work_rew3dep4_status', 2);
                })
                ->sum('tbl_material.total_budget_both')+DB::table('tbl_service')
                ->join('nvservicestatus', 'tbl_service.id', '=', 'nvservicestatus.service_id')
                ->whereIn('tbl_service.nv_id', $BRPLnv)
                ->where(function ($query) {
                    $query->where('nvservicestatus.work_rew3dep4_status', 2);
                })
                ->sum('tbl_service.total_buget');
           
        
                $approvedAmountBRPL = DB::table('tbl_material')
                ->join('nvservicestatus', 'tbl_material.id', '=', 'nvservicestatus.material_id')
                ->whereIn('tbl_material.nv_id', $BRPLnv)->where('nvservicestatus.ceo_status', 1)
                ->sum('tbl_material.total_budget_both')+DB::table('tbl_service')
                ->join('nvservicestatus', 'tbl_service.id', '=', 'nvservicestatus.service_id')
                ->whereIn('tbl_service.nv_id', $BRPLnv)->where('nvservicestatus.ceo_status', 1)
                ->sum('tbl_service.total_buget');
                
                $DataBRPL =Nvsericestatus::
                select(
                    DB::raw('MONTH(created_at) as month'),
                    DB::raw('SUM(CASE WHEN ceo_status = "1" THEN 1 ELSE 0 END) as approved_count'),
                    DB::raw('SUM(CASE WHEN ( work_rew3dep4_status = "2")  THEN 1 ELSE 0 END ) as rejected_count'),
                    DB::raw('SUM(CASE WHEN (work_rew1dep4_status = "0" AND work_rew2dep4_status = "0" AND work_rew3dep4_status = "0" AND work_rew4dep4_status = "0" AND approverdep4_status = "0")  THEN 1 ELSE 0 END) as pending_count'),
                    // DB::raw('SUM(CASE WHEN (approverdep3_status = "1" AND work_rew1dep4_status = "1" AND work_rew3dep4_status = "0") THEN 1 ELSE 0 END) as pending_count'),
                    // DB::raw('SUM(CASE WHEN status = "pending" THEN 1 ELSE 0 END) as rejected_count')
                )
                // -> where('ceo_nominee_status',1)
                ->whereIn('nv_id', $BRPLnv)->with(['service', 'material', 'user'])
                // ->where('approverdep3_status',1)
                ->whereYear('created_at', Carbon::now()->year)
                ->groupBy('month')
                ->orderBy('month');
                // ->get();
                if (!empty($group_cio)) {
                    $DataBRPL->where('groupcio_status', 1);
                } else {
                    $DataBRPL->where('approverdep3_status', 1);
                }
                $fileDataBRPL = $DataBRPL->get();
        // dd($fileDataBRPL);
            $BRPLlabels = [];
            $BRPLapprovedData = [];
            $BRPLrejectedData = [];
            $BRPLpendingData = [];
        
            foreach ($fileDataBRPL as $dataPointBRPL) {
                $monthBRPL = Carbon::createFromFormat('!m', $dataPointBRPL->month)->format('F');
        
                $BRPLlabels[] = $monthBRPL;
                $BRPLapprovedData[] = $dataPointBRPL->approved_count;
                $BRPLrejectedData[] = $dataPointBRPL->rejected_count;
                $BRPLpendingData[] = $dataPointBRPL->pending_count;
                 // dd( $dataPoint->rejected_count);
            }
            if($fiscal_year){
                $BYPLnv = NeedValidation::where('company_id','5')->where('fiscal_year', $fiscal_year)->pluck("id");

            }else{
                $BYPLnv = NeedValidation::where('company_id','5')->where('fiscal_year', $currentFinancialYear)->pluck("id");

            }
            $pendingAmountBYPL = DB::table('tbl_material')
            ->join('nvservicestatus', 'tbl_material.id', '=', 'nvservicestatus.material_id')
            ->whereIn('tbl_material.nv_id', $BYPLnv)
            ->whereIn('nvservicestatus.nv_id', $pen_amt->pluck('nv_id'))
            // ->where('nvservicestatus.work_rew3dep4_status', 0)
            // ->where('nvservicestatus.work_rew2dep4_status', 1)
            ->sum('tbl_material.total_budget_both')+DB::table('tbl_service')
            ->join('nvservicestatus', 'tbl_service.id', '=', 'nvservicestatus.service_id')
            ->whereIn('tbl_service.nv_id', $BYPLnv)
            ->whereIn('nvservicestatus.nv_id', $pen_amt->pluck('nv_id'))
            // ->where('nvservicestatus.work_rew3dep4_status', 0)
            // ->where('nvservicestatus.work_rew2dep4_status', 1)
            ->sum('tbl_service.total_buget');
            // dd($pendingAmountBYPL);
    
            $rejectedAmountBYPL = DB::table('tbl_material')
            ->join('nvservicestatus', 'tbl_material.id', '=', 'nvservicestatus.material_id')
            ->whereIn('tbl_material.nv_id', $BYPLnv)
            ->where(function ($query) {
                $query->where('nvservicestatus.work_rew3dep4_status', 2);
            })
            ->sum('tbl_material.total_budget_both')+DB::table('tbl_service')
            ->join('nvservicestatus', 'tbl_service.id', '=', 'nvservicestatus.service_id')
            ->whereIn('tbl_service.nv_id', $BYPLnv)
            ->where(function ($query) {
                $query->where('nvservicestatus.work_rew3dep4_status', 2);
            })
            ->sum('tbl_service.total_buget');
       
    
            $approvedAmountBYPL = DB::table('tbl_material')
            ->join('nvservicestatus', 'tbl_material.id', '=', 'nvservicestatus.material_id')
            ->whereIn('tbl_material.nv_id', $BYPLnv)->where('nvservicestatus.ceo_status', 1)
            ->sum('tbl_material.total_budget_both')+DB::table('tbl_service')
            ->join('nvservicestatus', 'tbl_service.id', '=', 'nvservicestatus.service_id')
            ->whereIn('tbl_service.nv_id', $BYPLnv)->where('nvservicestatus.ceo_status', 1)
            ->sum('tbl_service.total_buget');
             $DataBYPL =Nvsericestatus::
            select(
                DB::raw('MONTH(created_at) as month'),
                DB::raw('SUM(CASE WHEN ceo_status = "1" THEN 1 ELSE 0 END) as approved_count'),
                DB::raw('SUM(CASE WHEN ( work_rew3dep4_status = "2")  THEN 1 ELSE 0 END) as rejected_count'),
                DB::raw('SUM(CASE WHEN (work_rew1dep4_status = "0" AND work_rew2dep4_status = "0" AND work_rew3dep4_status = "0" AND work_rew4dep4_status = "0" AND approverdep4_status = "0")  THEN 1 ELSE 0 END) as pending_count'),
                // DB::raw('SUM(CASE WHEN (approverdep3_status = "1" AND work_rew1dep4_status = "1" AND work_rew3dep4_status = "0")  THEN 1 ELSE 0 END) as pending_count'),
                // DB::raw('SUM(CASE WHEN status = "pending" THEN 1 ELSE 0 END) as rejected_count')
            ) 
            ->whereIn('nv_id', $BYPLnv)->with(['service', 'material', 'user'])
            // ->where('approverdep3_status',1)
            ->whereYear('created_at', Carbon::now()->year)
            ->groupBy('month')
            ->orderBy('month');
            // ->get();
            if (!empty($group_cio)) {
                $DataBYPL->where('groupcio_status', 1);
            } else {
                $DataBYPL->where('approverdep3_status', 1);
            }
            
            // Execute the query
            $fileDataBYPL = $DataBYPL->get();
            // dd($fileDataBYPL);
        $BYPLlabels = [];
        $BYPLapprovedData = [];
        $BYPLrejectedData = [];
        $BYPLpendingData = [];
    
        foreach ($fileDataBYPL as $dataPointBYPL) {
            $monthBYPL = Carbon::createFromFormat('!m', $dataPointBYPL->month)->format('F');
    
            $BYPLlabels[] = $monthBYPL;
            $BYPLapprovedData[] = $dataPointBYPL->approved_count;
            $BYPLrejectedData[] = $dataPointBYPL->rejected_count;
            $BYPLpendingData[] = $dataPointBYPL->pending_count;
             // dd( $dataPointBYPL->rejected_count);
        }
                return view("admin.dashboard", compact("approvedAmount","currentFinancialYear","nextFinancialYear","nextToNextFinancialYear", "rejectedAmount", "pendingAmount", "totalAmount","company", "company_id" , "nv_sm_data", "totalNV", "approvedNV", "rejectedNV", "pendingNV" , "hodApproval", "cpmgApproval", "btApproval", "ceoApproval"             ,'BRPLlabels','BRPLapprovedData','BRPLrejectedData','BRPLpendingData','BYPLlabels','BYPLapprovedData','BYPLrejectedData','BYPLpendingData','cesApproval','ceonominee1Approval','ceonominee2Approval','pendingAmountBYPL','pendingAmountBRPL','rejectedAmountBYPL','rejectedAmountBRPL','approvedAmountBYPL','approvedAmountBRPL','groupcioApproval'));
            } elseif ($id4->work_rew4 == $user->id) {
                $Values = [$user->id, $id4->work_rew1, $id4->work_rew2, $id4->work_rew3];
                $nvid = [];
                $nvstatus = Nvsericestatus::get();
                foreach ($nvstatus as $nvstatus) {
                    array_push($nvid, $nvstatus["nv_id"]);
                }
                $user_nv = NeedValidation::with("division", "service")
                    ->whereIn("id", $nvid)
                    ->orderBy("id", "desc")
                    ->first();
                    if(!empty( $user_nv)){
                $employees = Employee::where("user_id", $user_nv->user_id)->first();
                $department = Department::where("id", $employees->department_id)->first();
                $group_cio = $department->group_cio;}
                if($fiscal_year){
                    $totalId = NeedValidation::where('fiscal_year', $fiscal_year)->pluck('id');

                }else{
                    $totalId = NeedValidation::where('fiscal_year', $currentFinancialYear)->pluck('id');

                }
                 
                // dd($totalId);
                $totalAmount = DB::table('tbl_material')
                ->join('nvservicestatus', 'tbl_material.id', '=', 'nvservicestatus.material_id')
                 // -> whereIn("nv_id", $totalId)
                 ->whereIn('tbl_material.nv_id', $totalId)
                 ->whereIn('nvservicestatus.nv_id', $latestData->pluck('nv_id'))
                ->where('nvservicestatus.work_rew4dep4_status', 1)
                ->sum('tbl_material.total_budget_both') + DB::table('tbl_service')
                ->join('nvservicestatus', 'tbl_service.id', '=', 'nvservicestatus.service_id')
                 // -> whereIn("nv_id", $totalId)
                 ->whereIn('tbl_service.nv_id', $totalId)
                 ->whereIn('nvservicestatus.nv_id', $latestData->pluck('nv_id'))
                ->where('nvservicestatus.work_rew4dep4_status', 1)
                ->sum('tbl_service.total_buget');
                // dd($totalAmount);
                if(!empty( $group_cio)){
                $pen_amt = $latestData->filter(function ($data) {
                    return in_array($data->groupcio_status, [1]) &&
                        in_array($data->work_rew1dep4_status, [0]) &&
                        in_array($data->work_rew2dep4_status, [0]) &&
                        in_array($data->work_rew3dep4_status, [0]) &&
                        in_array($data->work_rew4dep4_status, [0]) &&
                        in_array($data->approverdep4_status, [0]) ;
                });
                }else{
                    $pen_amt = $latestData->filter(function ($data) {
                        return in_array($data->approverdep3_status, [1]) &&
                            in_array($data->work_rew1dep4_status, [0]) &&
                            in_array($data->work_rew2dep4_status, [0]) &&
                            in_array($data->work_rew3dep4_status, [0]) &&
                            in_array($data->work_rew4dep4_status, [0]) &&
                            in_array($data->approverdep4_status, [0]) ;
                    });  
                }
                $pendingAmount = DB::table('tbl_material')
                ->join('nvservicestatus', 'tbl_material.id', '=', 'nvservicestatus.material_id')
                 // -> whereIn("nv_id", $totalId)
                 ->whereIn('tbl_material.nv_id', $totalId)
                 ->whereIn('nvservicestatus.nv_id', $pen_amt->pluck('nv_id'))
                // ->where('nvservicestatus.work_rew4dep4_status', 0)
                // ->where('nvservicestatus.work_rew3dep4_status', 1)
                ->sum('tbl_material.total_budget_both') +  DB::table('tbl_service')
                ->join('nvservicestatus', 'tbl_service.id', '=', 'nvservicestatus.service_id')
                 // -> whereIn("nv_id", $totalId)
                 ->whereIn('tbl_service.nv_id', $totalId)
                 ->whereIn('nvservicestatus.nv_id', $pen_amt->pluck('nv_id'))
                // ->where('nvservicestatus.work_rew4dep4_status', 0)
                // ->where('nvservicestatus.work_rew3dep4_status', 1)
                ->sum('tbl_service.total_buget');
        
                $rejectedAmount = DB::table('tbl_material')
                ->join('nvservicestatus', 'tbl_material.id', '=', 'nvservicestatus.material_id')
                ->whereIn('nvservicestatus.nv_id', $latestData->pluck('nv_id'))
                ->where(function ($query) {
                    $query->where('nvservicestatus.work_rew4dep4_status', 2);
                })
                ->sum('tbl_material.total_budget_both') + DB::table('tbl_service')
                ->join('nvservicestatus', 'tbl_service.id', '=', 'nvservicestatus.service_id')
                ->whereIn('nvservicestatus.nv_id', $latestData->pluck('nv_id'))
                ->where(function ($query) {
                    $query->where('nvservicestatus.work_rew4dep4_status', 2);
                })
                ->sum('tbl_service.total_buget');
           
        
                $approvedAmount = DB::table('tbl_material')
                 // -> whereIn("nv_id", $totalId)
                 ->whereIn('tbl_material.nv_id', $totalId)
                 ->whereIn('nvservicestatus.nv_id', $latestData->pluck('nv_id'))
                ->join('nvservicestatus', 'tbl_material.id', '=', 'nvservicestatus.material_id')
                ->where('nvservicestatus.ceo_status', 1)
                ->sum('tbl_material.total_budget_both') +  DB::table('tbl_service')
                // -> whereIn("nv_id", $totalId)
                ->whereIn('tbl_service.nv_id', $totalId)
                ->whereIn('nvservicestatus.nv_id', $latestData->pluck('nv_id'))
               ->join('nvservicestatus', 'tbl_service.id', '=', 'nvservicestatus.service_id')
               ->where('nvservicestatus.ceo_status', 1)
               ->sum('tbl_service.total_buget');
                
                if($company_id){
                    $totalNV = Nvsericestatus::whereIn("nv_id", $totalId)->where('work_rew4dep4_status', 1)->where('company_id',$company_id)->count();
                } else{
                    $totalNV = Nvsericestatus::whereIn("nv_id", $totalId)->where('work_rew4dep4_status', 1)->count();
                }
                if($company_id){
                    $rejectedNV = Nvsericestatus::whereIn("nv_id", $totalId)->where('work_rew4dep4_status', 2)->where('company_id',$company_id)->count();
                } else {
                    $rejectedNV = Nvsericestatus::whereIn("nv_id", $totalId)->where('work_rew4dep4_status', 2)->count();
                }
                // dd($totalId);
                if($company_id){
                $latestData = Nvsericestatus::whereIn("nv_id", $totalId)->where('company_id',$company_id)->get();
            }else {
                $latestData = Nvsericestatus::whereIn("nv_id", $totalId)->get();
            }
                if($company_id){
                    $approvedNV = $latestData->where('ceo_status', 1)->where('company_id',$company_id)->count();
                }else{
                    $approvedNV = $latestData->where('ceo_status', 1)->count();
                }
                // $rejectedNV = $latestData->filter(function ($data) {
                //     return in_array($data->ceo_status, [2]) || 
                //     $data->work_rew1_status == 2 ;
                //     // $data->hod_status == 2 || 
                //     // $data->cpmg_status == 2 || 
                //     // $data->ces_status == 2 || 
                //     // $data->cto_status == 2 || 
                //     // $data->ceo_nominee_status == 2 || 
                //     // $data->ceo_nominee2_status == 2 || 
                //     // $data->ceo_nominee2_status == 2;
                // })->count();
                if(!empty( $group_cio)){
                $pendingNV = $latestData->filter(function ($data) {
                    return in_array($data->groupcio_status, [1]) &&
                        in_array($data->work_rew1dep4_status, [0]) &&
                        in_array($data->work_rew2dep4_status, [0]) &&
                        in_array($data->work_rew3dep4_status, [0]) &&
                        in_array($data->work_rew4dep4_status, [0]) &&
                        in_array($data->approverdep4_status, [0]) ;
                })->count();
                }else{
                    $pendingNV = $latestData->filter(function ($data) {
                        return in_array($data->approverdep3_status, [1]) &&
                            in_array($data->work_rew1dep4_status, [0]) &&
                            in_array($data->work_rew2dep4_status, [0]) &&
                            in_array($data->work_rew3dep4_status, [0]) &&
                            in_array($data->work_rew4dep4_status, [0]) &&
                            in_array($data->approverdep4_status, [0]) ;
                    })->count(); 
                }
                // $pendingNV = $latestData->filter(function ($data) {
                //     return in_array($data->approverdep3_status, [ 1]) &&
                //     in_array($data->work_rew1dep4_status, [1])&&
                //     in_array($data->work_rew4dep4_status, [0]);
                //     })->count();
                 

                 $hodApproval = Nvsericestatus::whereIn("nv_id", $totalId)->where("hod_status", 0)->count();
                $cesApproval = Nvsericestatus::whereIn("nv_id", $totalId)->where("hod_status", 1)->where("ces_status", 0)->where("derc_info", 1)->count();
                $cpmg = Nvsericestatus::whereIn("nv_id", $totalId)->where('hod_status', 1)->where('cpmg_status', 0)->get();
                $Pendingcpmg = $cpmg->filter(function ($data) {
                 return in_array($data->hod_status, [1]) &&
                     in_array($data->cpmg_status, [0]) &&
                     (
                         (in_array($data->derc_info, [0]) && in_array($data->ces_status, [0]))
                         ||
                         (in_array($data->derc_info, [1]) && in_array($data->ces_status, [1]))
                     );
             });
             $cpmgApproval = $Pendingcpmg->count(); 

                $btApproval = Nvsericestatus::whereIn("nv_id", $totalId)->where("cpmg_status", 1)->where("check_technology", 1)->where('cto_status', 0)->count();
              
                $ceonominee1 = Nvsericestatus::whereIn("nv_id", $totalId)->where('cpmg_status', 1)->where('ceo_nominee_status', 0)->get();
                 $Pendingnominee1 = $ceonominee1->filter(function ($data) {
                 return in_array($data->cpmg_status, [1]) &&
                     in_array($data->ceo_nominee_status, [0]) &&
                     (
                         (in_array($data->check_technology, [0]) && in_array($data->cto_status, [0]))
                         ||
                         (in_array($data->check_technology, [1]) && in_array($data->cto_status, [1]))
                     );
             });
             $ceonominee1Approval = $Pendingnominee1->count(); 
             if(!empty( $group_cio)){
                $ceonominee2Approval = Nvsericestatus::where('groupcio_status', 1)->where('ceo_nominee2_status', 0)->whereIn("nv_id", $totalId)->count();
                }else{
                    $ceonominee2Approval = Nvsericestatus::where('ceo_nominee_status', 1)->where('ceo_nominee2_status', 0)->whereIn("nv_id", $totalId)->count();
     
                }
            if(!empty( $group_cio)){
                $groupcioApproval = Nvsericestatus::where('ceo_nominee_status', 1)->where('groupcio_status', 0)->where('ceo_nominee2_status', 0)->whereIn("nv_id", $totalId)->count();
                }else{
                    $groupcioApproval =0;  
                }
    
                    // if(!empty( $group_cio)){
                    // $ceoApproval = Nvsericestatus::where('groupcio_status', 1)->where('ceo_status', 0)->whereIn("nv_id", $totalId)->count();
                    // }else{
                    $ceoApproval = Nvsericestatus::where('ceo_nominee2_status', 1)->where('ceo_status', 0)->whereIn("nv_id", $totalId)->count();
                    // }

              
                $nvIds = NeedValidation::where("user_id", $Values)->pluck("id");
                if($fiscal_year){
                    $nv = NeedValidation::whereHas('service')->select('id')->where('fiscal_year', $fiscal_year)
                    // ->whereIn('user_id', $Values)
                    // ->orWhereIn('user_id',$allNormalUsers)
                    ->get();

                }else{
                    $nv = NeedValidation::whereHas('service')->select('id')->where('fiscal_year', $currentFinancialYear)
                    // ->whereIn('user_id', $Values)
                    // ->orWhereIn('user_id',$allNormalUsers)
                    ->get();

                }
               
                $nv_ids = $nv->pluck('id');
                // $nv_sm_data = Nvsericestatus::whereIn('nv_id', $nv_ids)
                // ->with(['service', 'material', 'user'])
                // ->where('hod_status',1)
                // ->orderBy('id', 'desc')
                // ->get();
                 if(!empty( $group_cio)){
                $nv_sm_data = Nvsericestatus::whereIn('nv_id', $nv_ids)->with(['service', 'material', 'user'])
                 ->where('groupcio_status', 1)
                ->orderBy('id', 'asc')->get();
                }else{
                $nv_sm_data = Nvsericestatus::whereIn('nv_id', $nv_ids)->with(['service', 'material', 'user'])
                ->where('approverdep3_status', 1)
                ->orderBy('id', 'asc')->get();  
                }

                $total26_status = $request->total;
                $pending26_status = $request->pending;
                $reject26_status = $request->rejected;

                if ($ceo_status !== null) {
                    $nv_sm_data = $nv_sm_data->where('ceo_status', $ceo_status);
                } elseif ($pending26_status !== null) {
                    $nv_sm_data = $nv_sm_data->where('work_rew4dep4_status', $pending26_status)
                        
                            ->where('work_rew1dep4_status', 0)
                            ->where('work_rew2dep4_status', 0)
                            ->where('work_rew3dep4_status', 0);
                } elseif ($reject26_status !== null) {
                    $nv_sm_data = $nv_sm_data->where('work_rew4dep4_status', $reject26_status);
                    
                }elseif ($total26_status !== null) {
                    $nv_sm_data = $nv_sm_data->where('work_rew4dep4_status', $total26_status);
                      
                }
                if($fiscal_year){
                    $BRPLnv = NeedValidation::where('company_id','6')->where('fiscal_year', $fiscal_year)->pluck("id");


                }else{
                    $BRPLnv = NeedValidation::where('company_id','6')->where('fiscal_year', $currentFinancialYear)->pluck("id");


                }

                $pendingAmountBRPL = DB::table('tbl_material')
                ->join('nvservicestatus', 'tbl_material.id', '=', 'nvservicestatus.material_id')
                ->whereIn('tbl_material.nv_id', $BRPLnv)
                ->whereIn('nvservicestatus.nv_id', $pen_amt->pluck('nv_id'))
                // ->where('nvservicestatus.work_rew4dep4_status', 0)
                // ->where('nvservicestatus.work_rew3dep4_status', 1)
                ->sum('tbl_material.total_budget_both')+DB::table('tbl_service')
                ->join('nvservicestatus', 'tbl_service.id', '=', 'nvservicestatus.service_id')
                ->whereIn('tbl_service.nv_id', $BRPLnv)
                ->whereIn('nvservicestatus.nv_id', $pen_amt->pluck('nv_id'))
                // ->where('nvservicestatus.work_rew4dep4_status', 0)
                // ->where('nvservicestatus.work_rew3dep4_status', 1)
                ->sum('tbl_service.total_buget');
                // dd($pendingAmountBRPL);
        
                $rejectedAmountBRPL = DB::table('tbl_material')
                ->join('nvservicestatus', 'tbl_material.id', '=', 'nvservicestatus.material_id')
                ->whereIn('tbl_material.nv_id', $BRPLnv)
                ->where(function ($query) {
                    $query->where('nvservicestatus.work_rew4dep4_status', 2);
                })
                ->sum('tbl_material.total_budget_both')+DB::table('tbl_service')
                ->join('nvservicestatus', 'tbl_service.id', '=', 'nvservicestatus.service_id')
                ->whereIn('tbl_service.nv_id', $BRPLnv)
                ->where(function ($query) {
                    $query->where('nvservicestatus.work_rew4dep4_status', 2);
                })
                ->sum('tbl_service.total_buget');
           
        
                $approvedAmountBRPL = DB::table('tbl_material')
                ->join('nvservicestatus', 'tbl_material.id', '=', 'nvservicestatus.material_id')
                ->whereIn('tbl_material.nv_id', $BRPLnv)->where('nvservicestatus.ceo_status', 1)
                ->sum('tbl_material.total_budget_both')+DB::table('tbl_service')
                ->join('nvservicestatus', 'tbl_service.id', '=', 'nvservicestatus.service_id')
                ->whereIn('tbl_service.nv_id', $BRPLnv)->where('nvservicestatus.ceo_status', 1)
                ->sum('tbl_service.total_buget');

                $DataBRPL =Nvsericestatus::
                select(
                    DB::raw('MONTH(created_at) as month'),
                    DB::raw('SUM(CASE WHEN ceo_status = "1" THEN 1 ELSE 0 END) as approved_count'),
                    DB::raw('SUM(CASE WHEN ( work_rew3dep4_status = "2")  THEN 1 ELSE 0 END ) as rejected_count'),
                    DB::raw('SUM(CASE WHEN (work_rew1dep4_status = "0" AND work_rew2dep4_status = "0" AND work_rew3dep4_status = "0" AND work_rew4dep4_status = "0" AND approverdep4_status = "0")  THEN 1 ELSE 0 END) as pending_count'),
                    // DB::raw('SUM(CASE WHEN (approverdep3_status = "1" AND work_rew1dep4_status = "1" AND work_rew3dep4_status = "0") THEN 1 ELSE 0 END) as pending_count'),
                    // DB::raw('SUM(CASE WHEN status = "pending" THEN 1 ELSE 0 END) as rejected_count')
                )
                // -> where('ceo_nominee_status',1)
                ->whereIn('nv_id', $BRPLnv)->with(['service', 'material', 'user'])
                // ->where('approverdep3_status',1)
                ->whereYear('created_at', Carbon::now()->year)
                ->groupBy('month')
                ->orderBy('month');
                // ->get();
                if (!empty($group_cio)) {
                    $DataBRPL->where('groupcio_status', 1);
                } else {
                    $DataBRPL->where('approverdep3_status', 1);
                }
                $fileDataBRPL = $DataBRPL->get();
        // dd($fileDataBRPL);
            $BRPLlabels = [];
            $BRPLapprovedData = [];
            $BRPLrejectedData = [];
            $BRPLpendingData = [];
        
            foreach ($fileDataBRPL as $dataPointBRPL) {
                $monthBRPL = Carbon::createFromFormat('!m', $dataPointBRPL->month)->format('F');
        
                $BRPLlabels[] = $monthBRPL;
                $BRPLapprovedData[] = $dataPointBRPL->approved_count;
                $BRPLrejectedData[] = $dataPointBRPL->rejected_count;
                $BRPLpendingData[] = $dataPointBRPL->pending_count;
                 // dd( $dataPoint->rejected_count);
            }
            if($fiscal_year){
                $BYPLnv = NeedValidation::where('company_id','5')->where('fiscal_year', $fiscal_year)->pluck("id");



            }else{
                $BYPLnv = NeedValidation::where('company_id','5')->where('fiscal_year', $currentFinancialYear)->pluck("id");



            }
            $pendingAmountBYPL = DB::table('tbl_material')
            ->join('nvservicestatus', 'tbl_material.id', '=', 'nvservicestatus.material_id')
            ->whereIn('tbl_material.nv_id', $BYPLnv)
            ->whereIn('nvservicestatus.nv_id', $pen_amt->pluck('nv_id'))
            // ->where('nvservicestatus.work_rew4dep4_status', 0)
            // ->where('nvservicestatus.work_rew3dep4_status', 1)
            ->sum('tbl_material.total_budget_both')+DB::table('tbl_service')
            ->join('nvservicestatus', 'tbl_service.id', '=', 'nvservicestatus.service_id')
            ->whereIn('tbl_service.nv_id', $BYPLnv)
            ->whereIn('nvservicestatus.nv_id', $pen_amt->pluck('nv_id'))
            // ->where('nvservicestatus.work_rew4dep4_status', 0)
            // ->where('nvservicestatus.work_rew3dep4_status', 1)
            ->sum('tbl_service.total_buget');
            // dd($pendingAmountBYPL);
    
            $rejectedAmountBYPL = DB::table('tbl_material')
            ->join('nvservicestatus', 'tbl_material.id', '=', 'nvservicestatus.material_id')
            ->whereIn('tbl_material.nv_id', $BYPLnv)
            ->where(function ($query) {
                $query->where('nvservicestatus.work_rew4dep4_status', 2);
            })
            ->sum('tbl_material.total_budget_both')+DB::table('tbl_service')
            ->join('nvservicestatus', 'tbl_service.id', '=', 'nvservicestatus.service_id')
            ->whereIn('tbl_service.nv_id', $BYPLnv)
            ->where(function ($query) {
                $query->where('nvservicestatus.work_rew4dep4_status', 2);
            })
            ->sum('tbl_service.total_buget');
       
    
            $approvedAmountBYPL = DB::table('tbl_material')
            ->join('nvservicestatus', 'tbl_material.id', '=', 'nvservicestatus.material_id')
            ->whereIn('tbl_material.nv_id', $BYPLnv)->where('nvservicestatus.ceo_status', 1)
            ->sum('tbl_material.total_budget_both')+DB::table('tbl_service')
            ->join('nvservicestatus', 'tbl_service.id', '=', 'nvservicestatus.service_id')
            ->whereIn('tbl_service.nv_id', $BYPLnv)->where('nvservicestatus.ceo_status', 1)
            ->sum('tbl_service.total_buget');
            $DataBYPL =Nvsericestatus::
            select(
                DB::raw('MONTH(created_at) as month'),
                DB::raw('SUM(CASE WHEN ceo_status = "1" THEN 1 ELSE 0 END) as approved_count'),
                DB::raw('SUM(CASE WHEN ( work_rew3dep4_status = "2")  THEN 1 ELSE 0 END) as rejected_count'),
                DB::raw('SUM(CASE WHEN (work_rew1dep4_status = "0" AND work_rew2dep4_status = "0" AND work_rew3dep4_status = "0" AND work_rew4dep4_status = "0" AND approverdep4_status = "0")  THEN 1 ELSE 0 END) as pending_count'),
                // DB::raw('SUM(CASE WHEN (approverdep3_status = "1" AND work_rew1dep4_status = "1" AND work_rew3dep4_status = "0")  THEN 1 ELSE 0 END) as pending_count'),
                // DB::raw('SUM(CASE WHEN status = "pending" THEN 1 ELSE 0 END) as rejected_count')
            ) 
            ->whereIn('nv_id', $BYPLnv)->with(['service', 'material', 'user'])
            // ->where('approverdep3_status',1)
            ->whereYear('created_at', Carbon::now()->year)
            ->groupBy('month')
            ->orderBy('month');
            // ->get();
            if (!empty($group_cio)) {
                $DataBYPL->where('groupcio_status', 1);
            } else {
                $DataBYPL->where('approverdep3_status', 1);
            }
            
            // Execute the query
            $fileDataBYPL = $DataBYPL->get();
            // dd($fileDataBYPL);
        $BYPLlabels = [];
        $BYPLapprovedData = [];
        $BYPLrejectedData = [];
        $BYPLpendingData = [];
    
        foreach ($fileDataBYPL as $dataPointBYPL) {
            $monthBYPL = Carbon::createFromFormat('!m', $dataPointBYPL->month)->format('F');
    
            $BYPLlabels[] = $monthBYPL;
            $BYPLapprovedData[] = $dataPointBYPL->approved_count;
            $BYPLrejectedData[] = $dataPointBYPL->rejected_count;
            $BYPLpendingData[] = $dataPointBYPL->pending_count;
             // dd( $dataPointBYPL->rejected_count);
        }
                return view("admin.dashboard", compact("approvedAmount","currentFinancialYear","nextFinancialYear","nextToNextFinancialYear", "rejectedAmount", "pendingAmount", "totalAmount","company", "company_id" , "nv_sm_data", "totalNV", "approvedNV", "rejectedNV", "pendingNV" , "hodApproval", "cpmgApproval", "btApproval", "ceoApproval"             ,'BRPLlabels','BRPLapprovedData','BRPLrejectedData','BRPLpendingData','BYPLlabels','BYPLapprovedData','BYPLrejectedData','BYPLpendingData','cesApproval','ceonominee1Approval','ceonominee2Approval','approvedAmountBYPL','approvedAmountBRPL','rejectedAmountBYPL','rejectedAmountBRPL','pendingAmountBYPL','pendingAmountBRPL','groupcioApproval'));
            } elseif ($id4->approver == $user->id) {
                $Values = [$user->id, $id4->work_rew1, $id4->work_rew2, $id4->work_rew3, $id4->work_rew4];
                $nvid = [];
                $nvstatus = Nvsericestatus::get();
                foreach ($nvstatus as $nvstatus) {
                    array_push($nvid, $nvstatus["nv_id"]);
                }
                $user_nv = NeedValidation::with("division", "service")
                    ->whereIn("id", $nvid)
                    ->orderBy("id", "desc")
                    ->first();
                    if(!empty( $user_nv)){
                $employees = Employee::where("user_id", $user_nv->user_id)->first();
                $department = Department::where("id", $employees->department_id)->first();
                $group_cio = $department->group_cio;}
                if($fiscal_year){
                    $totalId = NeedValidation::where('fiscal_year', $fiscal_year)->pluck('id');
                }else{
                    $totalId = NeedValidation::where('fiscal_year', $currentFinancialYear)->pluck('id');
                }
               
                // dd($totalId);
                $totalAmount = DB::table('tbl_material')
                ->join('nvservicestatus', 'tbl_material.id', '=', 'nvservicestatus.material_id')
                 // -> whereIn("nv_id", $totalId)
                 ->whereIn('tbl_material.nv_id', $totalId)
                 ->whereIn('nvservicestatus.nv_id', $latestData->pluck('nv_id'))
                ->where('nvservicestatus.approverdep4_status', 1)
                ->sum('tbl_material.total_budget_both') + DB::table('tbl_service')
                ->join('nvservicestatus', 'tbl_service.id', '=', 'nvservicestatus.service_id')
                 // -> whereIn("nv_id", $totalId)
                 ->whereIn('tbl_service.nv_id', $totalId)
                 ->whereIn('nvservicestatus.nv_id', $latestData->pluck('nv_id'))
                ->where('nvservicestatus.approverdep4_status', 1)
                ->sum('tbl_service.total_buget');
                // dd($totalAmount);
                if(!empty($id4->work_rew1) || !empty($id4->work_rew2)|| !empty($id4->work_rew3)|| !empty($id4->work_rew4)){
                if(!empty( $group_cio)){
                $pen_amt = $latestData->filter(function ($data) {
                    return in_array($data->groupcio_status, [1]) &&
                       (in_array($data->work_rew1dep4_status, [1])||in_array($data->work_rew2dep4_status, [1])||in_array($data->work_rew3dep4_status, [1])||in_array($data->work_rew4dep4_status, [1]))  &&
                        in_array($data->approverdep4_status, [0]);
                    });
                }else{
                    $pen_amt = $latestData->filter(function ($data) {
                        return in_array($data->approverdep3_status, [1]) &&
                           (in_array($data->work_rew1dep4_status, [1])||in_array($data->work_rew2dep4_status, [1])||in_array($data->work_rew3dep4_status, [1])||in_array($data->work_rew4dep4_status, [1]))  &&
                            in_array($data->approverdep4_status, [0]);
                        });   
                }
                }elseif(empty($id4->work_rew1) && empty($id4->work_rew2) && empty($id4->work_rew3) && empty($id4->work_rew4)){
                    if(!empty( $group_cio)){
                    $pen_amt = $latestData->filter(function ($data) {
                        return in_array($data->groupcio_status, [1]) &&
                           in_array($data->approverdep4_status, [0]);
                        });
                    }else{
                        $pen_amt = $latestData->filter(function ($data) {
                            return in_array($data->approverdep3_status, [1]) &&
                               in_array($data->approverdep4_status, [0]);
                            });
                    }
                }

                $pendingAmount = DB::table('tbl_material')
                ->join('nvservicestatus', 'tbl_material.id', '=', 'nvservicestatus.material_id')
                 // -> whereIn("nv_id", $totalId)
                 ->whereIn('tbl_material.nv_id', $totalId)
                 ->whereIn('nvservicestatus.nv_id', $pen_amt->pluck('nv_id'))
                // ->where('nvservicestatus.approverdep4_status', 0)
                // ->where('nvservicestatus.work_rew1dep4_status', 1)
                ->sum('tbl_material.total_budget_both') + DB::table('tbl_service')
                ->join('nvservicestatus', 'tbl_service.id', '=', 'nvservicestatus.service_id')
                 // -> whereIn("nv_id", $totalId)
                 ->whereIn('tbl_service.nv_id', $totalId)
                 ->whereIn('nvservicestatus.nv_id', $pen_amt->pluck('nv_id'))
                // ->where('nvservicestatus.approverdep4_status', 0)
                // ->where('nvservicestatus.work_rew1dep4_status', 1)
                ->sum('tbl_service.total_buget');
        
                $rejectedAmount = DB::table('tbl_material')
                ->join('nvservicestatus', 'tbl_material.id', '=', 'nvservicestatus.material_id')
                ->whereIn('nvservicestatus.nv_id', $latestData->pluck('nv_id'))
                ->where(function ($query) {
                    $query->where('nvservicestatus.approverdep4_status', 2);
                })
                ->sum('tbl_material.total_budget_both') + DB::table('tbl_service')
                ->join('nvservicestatus', 'tbl_service.id', '=', 'nvservicestatus.service_id')
                ->whereIn('nvservicestatus.nv_id', $latestData->pluck('nv_id'))
                ->where(function ($query) {
                    $query->where('nvservicestatus.approverdep4_status', 2);
                })
                ->sum('tbl_service.total_buget');
           
        
                $approvedAmount = DB::table('tbl_material')
                 // -> whereIn("nv_id", $totalId)
                 ->whereIn('tbl_material.nv_id', $totalId)
                 ->whereIn('nvservicestatus.nv_id', $latestData->pluck('nv_id'))
                ->join('nvservicestatus', 'tbl_material.id', '=', 'nvservicestatus.material_id')
                ->where('nvservicestatus.ceo_status', 1)
                ->sum('tbl_material.total_budget_both') + DB::table('tbl_service')
                // -> whereIn("nv_id", $totalId)
                ->whereIn('tbl_service.nv_id', $totalId)
                ->whereIn('nvservicestatus.nv_id', $latestData->pluck('nv_id'))
               ->join('nvservicestatus', 'tbl_service.id', '=', 'nvservicestatus.service_id')
               ->where('nvservicestatus.ceo_status', 1)
               ->sum('tbl_service.total_buget');

                if($company_id){
                    $totalNV = Nvsericestatus::whereIn("nv_id", $totalId)->where('approverdep4_status', 1)->where('company_id',$company_id)->count();
                } else {
                    $totalNV = Nvsericestatus::whereIn("nv_id", $totalId)->where('approverdep4_status', 1)->count();
                }
                if($company_id){
                    $rejectedNV = Nvsericestatus::whereIn("nv_id", $totalId)->where('approverdep4_status', 2)->where('company_id',$company_id)->count();
                } else {
                    $rejectedNV = Nvsericestatus::whereIn("nv_id", $totalId)->where('approverdep4_status', 2)->count();
                }
                // dd($totalId);
                if($company_id){
                $latestData = Nvsericestatus::whereIn("nv_id", $totalId)->where('company_id',$company_id)->get();
            }else {
                $latestData = Nvsericestatus::whereIn("nv_id", $totalId)->get();
            }
                if($company_id){
                    $approvedNV = $latestData->where('ceo_status', 1)->where('company_id',$company_id)->count();
                }else{
                    $approvedNV = $latestData->where('ceo_status', 1)->count();
                }
                // $rejectedNV = $latestData->filter(function ($data) {
                //     return in_array($data->ceo_status, [2]) || 
                //     $data->work_rew1_status == 2 ;
                //     // $data->hod_status == 2 || 
                //     // $data->cpmg_status == 2 || 
                //     // $data->ces_status == 2 || 
                //     // $data->cto_status == 2 || 
                //     // $data->ceo_nominee_status == 2 || 
                //     // $data->ceo_nominee2_status == 2 || 
                //     // $data->ceo_nominee2_status == 2;
                // })->count();
                if(!empty($id4->work_rew1) || !empty($id4->work_rew2)|| !empty($id4->work_rew3)|| !empty($id4->work_rew4)){
                     if(!empty( $group_cio)){
                        $pendingNV = $latestData->filter(function ($data) {
                            return in_array($data->groupcio_status, [1]) &&
                            (in_array($data->work_rew1dep4_status, [1])||in_array($data->work_rew2dep4_status, [1])||in_array($data->work_rew3dep4_status, [1])||in_array($data->work_rew4dep4_status, [1]))  &&
                                in_array($data->approverdep4_status, [0]);
                            })->count();
                    }else{
                        $pendingNV = $latestData->filter(function ($data) {
                            return in_array($data->approverdep3_status, [1]) &&
                               (in_array($data->work_rew1dep4_status, [1])||in_array($data->work_rew2dep4_status, [1])||in_array($data->work_rew3dep4_status, [1])||in_array($data->work_rew4dep4_status, [1]))  &&
                                in_array($data->approverdep4_status, [0]);
                            })->count();   
                    }
                }elseif(empty($id4->work_rew1) && empty($id4->work_rew2) && empty($id4->work_rew3) && empty($id4->work_rew4)){
                    if(!empty( $group_cio)){
                    $pendingNV = $latestData->filter(function ($data) {
                        return in_array($data->groupcio_status, [1]) &&
                           in_array($data->approverdep4_status, [0]);
                        })->count();
                    }else{
                        $pendingNV = $latestData->filter(function ($data) {
                            return in_array($data->approverdep3_status, [1]) &&
                               in_array($data->approverdep4_status, [0]);
                            })->count();
                    }
                }
                // $pendingNV = $latestData->filter(function ($data) {
                //     return in_array($data->approverdep3_status, [ 1]) &&
                //     in_array($data->work_rew1dep4_status, [1])&&
                //     in_array($data->approverdep4_status, [0]);
                  
                //     })->count();
                 

               $hodApproval = Nvsericestatus::whereIn("nv_id", $totalId)->where("hod_status", 0)->count();
                $cesApproval = Nvsericestatus::whereIn("nv_id", $totalId)->where("hod_status", 1)->where("ces_status", 0)->where("derc_info", 1)->count();
                $cpmg = Nvsericestatus::whereIn("nv_id", $totalId)->where('hod_status', 1)->where('cpmg_status', 0)->get();
                $Pendingcpmg = $cpmg->filter(function ($data) {
                 return in_array($data->hod_status, [1]) &&
                     in_array($data->cpmg_status, [0]) &&
                     (
                         (in_array($data->derc_info, [0]) && in_array($data->ces_status, [0]))
                         ||
                         (in_array($data->derc_info, [1]) && in_array($data->ces_status, [1]))
                     );
             });
             $cpmgApproval = $Pendingcpmg->count(); 

                $btApproval = Nvsericestatus::whereIn("nv_id", $totalId)->where("cpmg_status", 1)->where("check_technology", 1)->where('cto_status', 0)->count();
              
                $ceonominee1 = Nvsericestatus::whereIn("nv_id", $totalId)->where('cpmg_status', 1)->where('ceo_nominee_status', 0)->get();
                 $Pendingnominee1 = $ceonominee1->filter(function ($data) {
                 return in_array($data->cpmg_status, [1]) &&
                     in_array($data->ceo_nominee_status, [0]) &&
                     (
                         (in_array($data->check_technology, [0]) && in_array($data->cto_status, [0]))
                         ||
                         (in_array($data->check_technology, [1]) && in_array($data->cto_status, [1]))
                     );
             });
             $ceonominee1Approval = $Pendingnominee1->count(); 
             if(!empty( $group_cio)){
                $ceonominee2Approval = Nvsericestatus::where('groupcio_status', 1)->where('ceo_nominee2_status', 0)->whereIn("nv_id", $totalId)->count();
                }else{
                    $ceonominee2Approval = Nvsericestatus::where('ceo_nominee_status', 1)->where('ceo_nominee2_status', 0)->whereIn("nv_id", $totalId)->count();
     
                }
            if(!empty( $group_cio)){
                $groupcioApproval = Nvsericestatus::where('ceo_nominee_status', 1)->where('groupcio_status', 0)->where('ceo_nominee2_status', 0)->whereIn("nv_id", $totalId)->count();
                }else{
                    $groupcioApproval =0;  
                }
    
                    // if(!empty( $group_cio)){
                    // $ceoApproval = Nvsericestatus::where('groupcio_status', 1)->where('ceo_status', 0)->whereIn("nv_id", $totalId)->count();
                    // }else{
                    $ceoApproval = Nvsericestatus::where('ceo_nominee2_status', 1)->where('ceo_status', 0)->whereIn("nv_id", $totalId)->count();
                    // }

              
                $nvIds = NeedValidation::where("user_id", $Values)->pluck("id");
                if($fiscal_year){
                    $nv = NeedValidation::whereHas('service')->select('id')->where('fiscal_year', $fiscal_year)
                // ->whereIn('user_id', $Values)
                // ->orWhereIn('user_id',$allNormalUsers)
                ->get();
                }else{
                    $nv = NeedValidation::whereHas('service')->select('id')->where('fiscal_year', $currentFinancialYear)
                // ->whereIn('user_id', $Values)
                // ->orWhereIn('user_id',$allNormalUsers)
                ->get();
                }
              
                $nv_ids = $nv->pluck('id');
                // $nv_sm_data = Nvsericestatus::whereIn('nv_id', $nv_ids)
                // ->with(['service', 'material', 'user'])
                // ->where('hod_status',1)
                // ->orderBy('id', 'desc')
                // ->get();
                if(!empty($id4->work_rew1) || !empty($id4->work_rew2)|| !empty($id4->work_rew3)|| !empty($id4->work_rew4)){
                if(!empty( $group_cio)){
                 $nv_sm_data = Nvsericestatus::whereIn('nv_id', $nv_ids)->with(['service', 'material', 'user'])
                 ->where('groupcio_status', 1)
                 ->where(function ($query) {
                    $query->orWhere('work_rew1dep4_status', 1)
                        ->orWhere('work_rew2dep4_status', 1)
                        ->orWhere('work_rew3dep4_status', 1)
                        ->orWhere('work_rew4dep4_status', 1);
                })
                    ->where(function ($query) {
                        $query->where('work_rew1dep4_status', '!=', 2)
                            ->where('work_rew2dep4_status', '!=', 2)
                            ->where('work_rew3dep4_status', '!=', 2)
                            ->where('work_rew4dep4_status', '!=', 2);
                    })
                
                ->orderBy('id', 'asc')->get();
                }else{
                    $nv_sm_data = Nvsericestatus::whereIn('nv_id', $nv_ids)->with(['service', 'material', 'user'])
                 ->where('approverdep3_status', 1)
                 ->where(function ($query) {
                    $query->orWhere('work_rew1dep4_status', 1)
                        ->orWhere('work_rew2dep4_status', 1)
                        ->orWhere('work_rew3dep4_status', 1)
                        ->orWhere('work_rew4dep4_status', 1);
                })
                    ->where(function ($query) {
                        $query->where('work_rew1dep4_status', '!=', 2)
                            ->where('work_rew2dep4_status', '!=', 2)
                            ->where('work_rew3dep4_status', '!=', 2)
                            ->where('work_rew4dep4_status', '!=', 2);
                    })
                
                ->orderBy('id', 'asc')->get();

                }
                }elseif(empty($id4->work_rew1) && empty($id4->work_rew2) && empty($id4->work_rew3) && empty($id4->work_rew4)){
                    if(!empty( $group_cio)){
                    $nv_sm_data = Nvsericestatus::whereIn('nv_id', $nv_ids)->with(['service', 'material', 'user'])
                    ->where('groupcio_status', 1)
                    
                       ->where(function ($query) {
                           $query->where('groupcio_status', '!=', 2) ;
                       })
                  
                   ->orderBy('id', 'asc')->get();
                    }else{
                        $nv_sm_data = Nvsericestatus::whereIn('nv_id', $nv_ids)->with(['service', 'material', 'user'])
                    ->where('approverdep3_status', 1)
                    
                       ->where(function ($query) {
                           $query->where('approverdep3_status', '!=', 2) ;
                       })
                  
                   ->orderBy('id', 'asc')->get();  
                    }
                }
             
                $total27_status = $request->total;
                $pending27_status = $request->pending;
                $reject27_status = $request->rejected;

               if ($ceo_status !== null) {
                    $nv_sm_data = $nv_sm_data->where('ceo_status', $ceo_status);
                } elseif ($pending27_status !== null) {
                    $nv_sm_data = $nv_sm_data->where('approverdep4_status', $pending27_status);
                } elseif ($reject27_status !== null) {
                    $nv_sm_data = $nv_sm_data->where('approverdep4_status', $reject27_status);
                    
                }elseif ($total27_status !== null) {
                    $nv_sm_data = $nv_sm_data->where('approverdep4_status', $total27_status);
                      
                }

                $BRPLnv = NeedValidation::where('company_id','6')->where('fiscal_year', $currentFinancialYear)->pluck("id");

                $pendingAmountBRPL = DB::table('tbl_material')
                ->join('nvservicestatus', 'tbl_material.id', '=', 'nvservicestatus.material_id')
                ->whereIn('tbl_material.nv_id', $BRPLnv)
                ->whereIn('nvservicestatus.nv_id', $pen_amt->pluck('nv_id'))
                // ->where('nvservicestatus.approverdep4_status', 0)
                // ->where('nvservicestatus.work_rew1dep4_status', 1)
                ->sum('tbl_material.total_budget_both')+DB::table('tbl_service')
                ->join('nvservicestatus', 'tbl_service.id', '=', 'nvservicestatus.service_id')
                ->whereIn('tbl_service.nv_id', $BRPLnv)
                ->whereIn('nvservicestatus.nv_id', $pen_amt->pluck('nv_id'))
                // ->where('nvservicestatus.approverdep4_status', 0)
                // ->where('nvservicestatus.work_rew1dep4_status', 1)
                ->sum('tbl_service.total_buget');
                // dd($pendingAmountBRPL);
        
                $rejectedAmountBRPL = DB::table('tbl_material')
                ->join('nvservicestatus', 'tbl_material.id', '=', 'nvservicestatus.material_id')
                ->whereIn('tbl_material.nv_id', $BRPLnv)
                ->where(function ($query) {
                    $query->where('nvservicestatus.approverdep4_status', 2);
                })
                ->sum('tbl_material.total_budget_both')+DB::table('tbl_service')
                ->join('nvservicestatus', 'tbl_service.id', '=', 'nvservicestatus.service_id')
                ->whereIn('tbl_service.nv_id', $BRPLnv)
                ->where(function ($query) {
                    $query->where('nvservicestatus.approverdep4_status', 2);
                })
                ->sum('tbl_service.total_buget');
           
        
                $approvedAmountBRPL = DB::table('tbl_material')
                ->join('nvservicestatus', 'tbl_material.id', '=', 'nvservicestatus.material_id')
                ->whereIn('tbl_material.nv_id', $BRPLnv)->where('nvservicestatus.ceo_status', 1)
                ->sum('tbl_material.total_budget_both')+DB::table('tbl_service')
                ->join('nvservicestatus', 'tbl_service.id', '=', 'nvservicestatus.service_id')
                ->whereIn('tbl_service.nv_id', $BRPLnv)->where('nvservicestatus.ceo_status', 1)
                ->sum('tbl_service.total_buget');
                

                if(!empty($id4->work_rew1) || !empty($id4->work_rew2)|| !empty($id4->work_rew3)|| !empty($id4->work_rew4)){
                    $DataBRPL =Nvsericestatus::
                select(
                    DB::raw('MONTH(created_at) as month'),
                    DB::raw('SUM(CASE WHEN ceo_status = "1" THEN 1 ELSE 0 END) as approved_count'),
                    DB::raw('SUM(CASE WHEN ( approverdep4_status = "2")  THEN 1 ELSE 0 END ) as rejected_count'),
                    DB::raw('SUM(CASE WHEN (approverdep4_status = "0" AND (work_rew1dep4_status = "1" OR work_rew2dep4_status = "1" OR work_rew3dep4_status = "1" OR work_rew4dep4_status = "1") ) THEN 1 ELSE 0 END) as pending_count'),
                    // DB::raw('SUM(CASE WHEN status = "pending" THEN 1 ELSE 0 END) as rejected_count')
                )
                ->whereIn('nv_id', $BRPLnv)->with(['service', 'material', 'user'])
                // ->where('approverdep3_status',1)
                ->whereYear('created_at', Carbon::now()->year)
                ->groupBy('month')
                ->orderBy('month');
                // ->get();
                if (!empty($group_cio)) {
                    $DataBRPL->where('groupcio_status', 1);
                } else {
                    $DataBRPL->where('approverdep3_status', 1);
                }
                
                // Execute the DataBRPL
                $fileDataBRPL = $DataBRPL->get();
                }elseif(empty($id4->work_rew1) && empty($id4->work_rew2) && empty($id4->work_rew3) && empty($id4->work_rew4)){
                        $DataBRPL =Nvsericestatus::
                select(
                    DB::raw('MONTH(created_at) as month'),
                    DB::raw('SUM(CASE WHEN ceo_status = "1" THEN 1 ELSE 0 END) as approved_count'),
                    DB::raw('SUM(CASE WHEN ( approverdep4_status = "2")  THEN 1 ELSE 0 END ) as rejected_count'),
                    DB::raw('SUM(CASE WHEN (approverdep4_status = "0"  ) THEN 1 ELSE 0 END) as pending_count'),
                    // DB::raw('SUM(CASE WHEN status = "pending" THEN 1 ELSE 0 END) as rejected_count')
                )
                ->whereIn('nv_id', $BRPLnv)->with(['service', 'material', 'user'])
                // ->where('approverdep3_status',1)
                ->whereYear('created_at', Carbon::now()->year)
                ->groupBy('month')
                ->orderBy('month');
                // ->get();
                if (!empty($group_cio)) {
                    $DataBRPL->where('groupcio_status', 1);
                } else {
                    $DataBRPL->where('approverdep3_status', 1);
                }
                
                // Execute the DataBRPL
                $fileDataBRPL = $DataBRPL->get();
                    }


               
        // dd($fileDataBRPL);
            $BRPLlabels = [];
            $BRPLapprovedData = [];
            $BRPLrejectedData = [];
            $BRPLpendingData = [];
        
            foreach ($fileDataBRPL as $dataPointBRPL) {
                $monthBRPL = Carbon::createFromFormat('!m', $dataPointBRPL->month)->format('F');
        
                $BRPLlabels[] = $monthBRPL;
                $BRPLapprovedData[] = $dataPointBRPL->approved_count;
                $BRPLrejectedData[] = $dataPointBRPL->rejected_count;
                $BRPLpendingData[] = $dataPointBRPL->pending_count;
                 // dd( $dataPoint->rejected_count);
            }
            if($fiscal_year){
                $BYPLnv = NeedValidation::where('company_id','5')->where('fiscal_year', $fiscal_year)->pluck("id");

            }else{
                $BYPLnv = NeedValidation::where('company_id','5')->where('fiscal_year', $currentFinancialYear)->pluck("id");

            }
            $pendingAmountBYPL = DB::table('tbl_material')
            ->join('nvservicestatus', 'tbl_material.id', '=', 'nvservicestatus.material_id')
            ->whereIn('tbl_material.nv_id', $BYPLnv)
            ->whereIn('nvservicestatus.nv_id', $pen_amt->pluck('nv_id'))
            // ->where('nvservicestatus.approverdep4_status', 0)
            // ->where('nvservicestatus.work_rew1dep4_status', 1)
            ->sum('tbl_material.total_budget_both')+DB::table('tbl_service')
            ->join('nvservicestatus', 'tbl_service.id', '=', 'nvservicestatus.service_id')
            ->whereIn('tbl_service.nv_id', $BYPLnv)
            ->whereIn('nvservicestatus.nv_id', $pen_amt->pluck('nv_id'))
            // ->where('nvservicestatus.approverdep4_status', 0)
            // ->where('nvservicestatus.work_rew1dep4_status', 1)
            ->sum('tbl_service.total_buget');
            // dd($pendingAmountBYPL);
    
            $rejectedAmountBYPL = DB::table('tbl_material')
            ->join('nvservicestatus', 'tbl_material.id', '=', 'nvservicestatus.material_id')
            ->whereIn('tbl_material.nv_id', $BYPLnv)
            ->where(function ($query) {
                $query->where('nvservicestatus.approverdep4_status', 2);
            })
            ->sum('tbl_material.total_budget_both')+DB::table('tbl_service')
            ->join('nvservicestatus', 'tbl_service.id', '=', 'nvservicestatus.service_id')
            ->whereIn('tbl_service.nv_id', $BYPLnv)
            ->where(function ($query) {
                $query->where('nvservicestatus.approverdep4_status', 2);
            })
            ->sum('tbl_service.total_buget');
       
    
            $approvedAmountBYPL = DB::table('tbl_material')
            ->join('nvservicestatus', 'tbl_material.id', '=', 'nvservicestatus.material_id')
            ->whereIn('tbl_material.nv_id', $BYPLnv)->where('nvservicestatus.ceo_status', 1)
            ->sum('tbl_material.total_budget_both')+DB::table('tbl_service')
            ->join('nvservicestatus', 'tbl_service.id', '=', 'nvservicestatus.service_id')
            ->whereIn('tbl_service.nv_id', $BYPLnv)->where('nvservicestatus.ceo_status', 1)
            ->sum('tbl_service.total_buget');


            
            if(!empty($id4->work_rew1) || !empty($id4->work_rew2)|| !empty($id4->work_rew3)|| !empty($id4->work_rew4)){
                $DataBYPL =Nvsericestatus::
                select(
                    DB::raw('MONTH(created_at) as month'),
                    DB::raw('SUM(CASE WHEN ceo_status = "1" THEN 1 ELSE 0 END) as approved_count'),
                    DB::raw('SUM(CASE WHEN ( approverdep4_status = "2")  THEN 1 ELSE 0 END) as rejected_count'),
                    DB::raw('SUM(CASE WHEN (approverdep4_status = "0" AND (work_rew1dep4_status = "1" OR work_rew2dep4_status = "1" OR work_rew3dep4_status = "1" OR work_rew4dep4_status = "1") ) THEN 1 ELSE 0 END) as pending_count'),
                    // DB::raw('SUM(CASE WHEN (approverdep3_status = "1" AND work_rew1dep4_status = "1" AND approverdep4_status = "0" )  THEN 1 ELSE 0 END) as pending_count'),
                    // DB::raw('SUM(CASE WHEN status = "pending" THEN 1 ELSE 0 END) as rejected_count')
                ) 
                ->whereIn('nv_id', $BYPLnv)->with(['service', 'material', 'user'])
                // ->where('approverdep3_status',1)
                ->whereYear('created_at', Carbon::now()->year)
                ->groupBy('month')
                ->orderBy('month');
                // ->get();
                if (!empty($group_cio)) {
                    $DataBYPL->where('groupcio_status', 1);
                } else {
                    $DataBYPL->where('approverdep3_status', 1);
                }
                $fileDataBYPL = $DataBYPL->get();
    
            }elseif(empty($id4->work_rew1) && empty($id4->work_rew2) && empty($id4->work_rew3) && empty($id4->work_rew4)){
                $DataBYPL =Nvsericestatus::
                select(
                    DB::raw('MONTH(created_at) as month'),
                    DB::raw('SUM(CASE WHEN ceo_status = "1" THEN 1 ELSE 0 END) as approved_count'),
                    DB::raw('SUM(CASE WHEN ( approverdep4_status = "2")  THEN 1 ELSE 0 END) as rejected_count'),
                    DB::raw('SUM(CASE WHEN (approverdep4_status = "0"  ) THEN 1 ELSE 0 END) as pending_count'),
                    // DB::raw('SUM(CASE WHEN (approverdep3_status = "1" AND work_rew1dep4_status = "1" AND approverdep4_status = "0" )  THEN 1 ELSE 0 END) as pending_count'),
                    // DB::raw('SUM(CASE WHEN status = "pending" THEN 1 ELSE 0 END) as rejected_count')
                ) 
                ->whereIn('nv_id', $BYPLnv)->with(['service', 'material', 'user'])
                // ->where('approverdep3_status',1)
                ->whereYear('created_at', Carbon::now()->year)
                ->groupBy('month')
                ->orderBy('month');
                // ->get();
                if (!empty($group_cio)) {
                    $DataBYPL->where('groupcio_status', 1);
                } else {
                    $DataBYPL->where('approverdep3_status', 1);
                }
                $fileDataBYPL = $DataBYPL->get();
    
                }
            // dd($fileDataBYPL);
        $BYPLlabels = [];
        $BYPLapprovedData = [];
        $BYPLrejectedData = [];
        $BYPLpendingData = [];
    
        foreach ($fileDataBYPL as $dataPointBYPL) {
            $monthBYPL = Carbon::createFromFormat('!m', $dataPointBYPL->month)->format('F');
    
            $BYPLlabels[] = $monthBYPL;
            $BYPLapprovedData[] = $dataPointBYPL->approved_count;
            $BYPLrejectedData[] = $dataPointBYPL->rejected_count;
            $BYPLpendingData[] = $dataPointBYPL->pending_count;
             // dd( $dataPointBYPL->rejected_count);
        }
                return view("admin.dashboard", compact("approvedAmount","currentFinancialYear","nextFinancialYear","nextToNextFinancialYear", "rejectedAmount", "pendingAmount", "totalAmount","company", "company_id" , "nv_sm_data", "totalNV", "approvedNV", "rejectedNV", "pendingNV" , "hodApproval", "cpmgApproval", "btApproval", "ceoApproval"             ,'BRPLlabels','BRPLapprovedData','BRPLrejectedData','BRPLpendingData','BYPLlabels','BYPLapprovedData','BYPLrejectedData','BYPLpendingData','cesApproval','ceonominee1Approval','ceonominee2Approval','approvedAmountBYPL','approvedAmountBRPL','rejectedAmountBYPL','rejectedAmountBRPL','pendingAmountBYPL','pendingAmountBRPL','groupcioApproval'));
            } elseif ($id5->work_rew1 == $user->id) {
                $Values = [$user->id, $id5];
                $nvid = [];
                $nvstatus = Nvsericestatus::get();
                foreach ($nvstatus as $nvstatus) {
                    array_push($nvid, $nvstatus["nv_id"]);
                }
                $user_nv = NeedValidation::with("division", "service")
                    ->whereIn("id", $nvid)
                    ->orderBy("id", "desc")
                    ->first();
                    if(!empty( $user_nv)){
                $employees = Employee::where("user_id", $user_nv->user_id)->first();
                $department = Department::where("id", $employees->department_id)->first();
                $group_cio = $department->group_cio;}
               
                $totalId = NeedValidation::pluck('id');
                // dd($totalId);
                $totalAmount = DB::table('tbl_material')
                ->join('nvservicestatus', 'tbl_material.id', '=', 'nvservicestatus.material_id')
                 // -> whereIn("nv_id", $totalId)
                 ->whereIn('tbl_material.nv_id', $totalId)
                 ->whereIn('nvservicestatus.nv_id', $latestData->pluck('nv_id'))
                ->where('nvservicestatus.work_rew1dep5_status', 1)
                ->sum('tbl_material.total_budget_both') + DB::table('tbl_service')
                ->join('nvservicestatus', 'tbl_service.id', '=', 'nvservicestatus.service_id')
                 // -> whereIn("nv_id", $totalId)
                 ->whereIn('tbl_service.nv_id', $totalId)
                 ->whereIn('nvservicestatus.nv_id', $latestData->pluck('nv_id'))
                ->where('nvservicestatus.work_rew1dep5_status', 1)
                ->sum('tbl_service.total_buget');
                // dd($totalAmount);
        
                $pendingAmount = DB::table('tbl_material')
                ->join('nvservicestatus', 'tbl_material.id', '=', 'nvservicestatus.material_id')
                 // -> whereIn("nv_id", $totalId)
                 ->whereIn('tbl_material.nv_id', $totalId)
                 ->whereIn('nvservicestatus.nv_id', $latestData->pluck('nv_id'))
                ->where('nvservicestatus.work_rew1dep5_status', 0)
                ->where('nvservicestatus.ceo_nominee2_status', 1)
                ->sum('tbl_material.total_budget_both') + DB::table('tbl_service')
                ->join('nvservicestatus', 'tbl_service.id', '=', 'nvservicestatus.service_id')
                 // -> whereIn("nv_id", $totalId)
                 ->whereIn('tbl_service.nv_id', $totalId)
                 ->whereIn('nvservicestatus.nv_id', $latestData->pluck('nv_id'))
                ->where('nvservicestatus.work_rew1dep5_status', 0)
                ->where('nvservicestatus.ceo_nominee2_status', 1)
                ->sum('tbl_service.total_buget');
        
                $rejectedAmount = DB::table('tbl_material')
                ->join('nvservicestatus', 'tbl_material.id', '=', 'nvservicestatus.material_id')
                ->whereIn('nvservicestatus.nv_id', $latestData->pluck('nv_id'))
                ->where(function ($query) {
                    $query->where('nvservicestatus.work_rew1dep5_status', 2);
                })
                ->sum('tbl_material.total_budget_both') + DB::table('tbl_service')
                ->join('nvservicestatus', 'tbl_service.id', '=', 'nvservicestatus.service_id')
                ->whereIn('nvservicestatus.nv_id', $latestData->pluck('nv_id'))
                ->where(function ($query) {
                    $query->where('nvservicestatus.work_rew2dep5_status', 2);
                })
                ->sum('tbl_service.total_buget');
           
        
                $approvedAmount = DB::table('tbl_material')
                 // -> whereIn("nv_id", $totalId)
                 ->whereIn('tbl_material.nv_id', $totalId)
                 ->whereIn('nvservicestatus.nv_id', $latestData->pluck('nv_id'))
                ->join('nvservicestatus', 'tbl_material.id', '=', 'nvservicestatus.material_id')
                ->where('nvservicestatus.ceo_status', 1)
                ->sum('tbl_material.total_budget_both') + DB::table('tbl_service')
                // -> whereIn("nv_id", $totalId)
                ->whereIn('tbl_service.nv_id', $totalId)
                ->whereIn('nvservicestatus.nv_id', $latestData->pluck('nv_id'))
               ->join('nvservicestatus', 'tbl_service.id', '=', 'nvservicestatus.service_id')
               ->where('nvservicestatus.ceo_status', 1)
               ->sum('tbl_service.total_buget');

                if($company_id){
                    $totalNV = Nvsericestatus::whereIn("nv_id", $totalId)->where('work_rew1dep5_status', 1)->where('company_id',$company_id)->count();
                } else {
                    $totalNV = Nvsericestatus::whereIn("nv_id", $totalId)->where('work_rew1dep5_status', 1)->count();
                }
                if($company_id){
                    $rejectedNV = Nvsericestatus::whereIn("nv_id", $totalId)->where('work_rew1dep5_status', 2)->where('company_id',$company_id)->count();
                } else{
                    $rejectedNV = Nvsericestatus::whereIn("nv_id", $totalId)->where('work_rew1dep5_status', 2)->count();
                }
                // dd($totalId);
                if($company_id){
                $latestData = Nvsericestatus::whereIn("nv_id", $totalId)->where('company_id',$company_id)->get();
            }else {
                $latestData = Nvsericestatus::whereIn("nv_id", $totalId)->get();
            }
                if($company_id){
                    $approvedNV = $latestData->where('ceo_status', 1)->where('company_id',$company_id)->count();
                }else{
                    $approvedNV = $latestData->where('ceo_status', 1)->count();
                }
                // $rejectedNV = $latestData->filter(function ($data) {
                //     return in_array($data->ceo_status, [2]) || 
                //     $data->work_rew1_status == 2 ;
                //     // $data->hod_status == 2 || 
                //     // $data->cpmg_status == 2 || 
                //     // $data->ces_status == 2 || 
                //     // $data->cto_status == 2 || 
                //     // $data->ceo_nominee_status == 2 || 
                //     // $data->ceo_nominee2_status == 2 || 
                //     // $data->ceo_nominee2_status == 2;
                // })->count();
                $pendingNV = $latestData->filter(function ($data) {
                    return in_array($data->approverdep4_status, [ 1]) &&
                    in_array($data->work_rew1dep5_status, [0]);
                    
                  
                   
                   
                    // in_array($data->ces_status, [0]) &&
                    // in_array($data->cto_status, [0]) &&
                    // in_array($data->ceo_nominee_status, [0]) &&
                    // in_array($data->ceo_status, [0]);
                    })->count();
                 

                $hodApproval = Nvsericestatus::where("hod_status", 0)->count();
                $cesApproval = Nvsericestatus::where("hod_status", 1)->where("ces_status", 0)->where("derc_info", 1)->count();
                $cpmg = Nvsericestatus::where('hod_status', 1)->where('cpmg_status', 0)->get();
                $Pendingcpmg = $cpmg->filter(function ($data) {
                 return in_array($data->hod_status, [1]) &&
                     in_array($data->cpmg_status, [0]) &&
                     (
                         (in_array($data->derc_info, [0]) && in_array($data->ces_status, [0]))
                         ||
                         (in_array($data->derc_info, [1]) && in_array($data->ces_status, [1]))
                     );
             });
             $cpmgApproval = $Pendingcpmg->count(); 

                $btApproval = Nvsericestatus::where("cpmg_status", 1)->where("check_technology", 1)->where('cto_status', 0)->count();
              
                $ceonominee1 = Nvsericestatus::where('cpmg_status', 1)->where('ceo_nominee_status', 0)->get();
                 $Pendingnominee1 = $ceonominee1->filter(function ($data) {
                 return in_array($data->cpmg_status, [1]) &&
                     in_array($data->ceo_nominee_status, [0]) &&
                     (
                         (in_array($data->check_technology, [0]) && in_array($data->cto_status, [0]))
                         ||
                         (in_array($data->check_technology, [1]) && in_array($data->cto_status, [1]))
                     );
             });
             $ceonominee1Approval = $Pendingnominee1->count(); 
             if(!empty( $group_cio)){
                $ceonominee2Approval = Nvsericestatus::where('groupcio_status', 1)->where('ceo_nominee2_status', 0)->whereIn("nv_id", $totalId)->count();
                }else{
                    $ceonominee2Approval = Nvsericestatus::where('ceo_nominee_status', 1)->where('ceo_nominee2_status', 0)->whereIn("nv_id", $totalId)->count();
     
                }
            if(!empty( $group_cio)){
                $groupcioApproval = Nvsericestatus::where('ceo_nominee_status', 1)->where('groupcio_status', 0)->where('ceo_nominee2_status', 0)->whereIn("nv_id", $totalId)->count();
                }else{
                    $groupcioApproval =0;  
                }
    
                    // if(!empty( $group_cio)){
                    // $ceoApproval = Nvsericestatus::where('groupcio_status', 1)->where('ceo_status', 0)->whereIn("nv_id", $totalId)->count();
                    // }else{
                    $ceoApproval = Nvsericestatus::where('ceo_nominee2_status', 1)->where('ceo_status', 0)->whereIn("nv_id", $totalId)->count();
                    // }

             
                $nvIds = NeedValidation::where("user_id", $user->id)->pluck("id");
                $nv = NeedValidation::whereHas('service')->select('id')
                // ->whereIn('user_id', $Values)
                // ->orWhere('user_id',$allNormalUsers)
                ->get();
                $nv_ids = $nv->pluck('id');
                // $nv_sm_data = Nvsericestatus::whereIn('nv_id', $nv_ids)
                // ->with(['service', 'material', 'user'])
                // ->where('hod_status',1)
                // ->orderBy('id', 'desc')
                // ->get();
                if($company_id){
                    $nv_sm_data = Nvsericestatus::whereIn('nv_id', $nv_ids)->with(['service', 'material', 'user'])
                ->where('ceo_nominee2_status',1)->where('company_id',$company_id)
                ->orderBy('id', 'desc');
                } else {
                    $nv_sm_data = Nvsericestatus::whereIn('nv_id', $nv_ids)->with(['service', 'material', 'user'])
                ->where('ceo_nominee2_status',1)
                ->orderBy('id', 'desc');
                }
                $total28_status = $request->total;
                $pending28_status = $request->pending;
                $reject28_status = $request->rejected;

               
              
               
                if ($ceo_status !== null) {
                    $nv_sm_data = $nv_sm_data->where('ceo_status', $ceo_status);
                } elseif ($pending28_status !== null) {
                    $nv_sm_data = $nv_sm_data->where('work_rew1dep5_status', $pending28_status)
                       
                        ->whereIn('hod_status', [ 1])
                        ->whereIn('approver_status', [ 1])    
                       
                        ->whereIn('cto_status', [0, 1])
                        ->whereIn('approverdep3_status', [ 1])
                        ->whereIn('work_rew1dep4_status', [ 1])
                        ->whereIn('approverdep4_status', [ 1]);
                     
                       

                } elseif ($reject28_status !== null) {
                    $nv_sm_data = $nv_sm_data->where('work_rew1dep5_status', $reject28_status);
                    
                }elseif ($total28_status !== null) {
                    $nv_sm_data = $nv_sm_data->where('work_rew1dep5_status', $total28_status);
                      
                }
                $nv_sm_data = $nv_sm_data->get();

                $BRPLnv = NeedValidation::where('company_id','6')->pluck("id");

                $pendingAmountBRPL = DB::table('tbl_material')
                ->join('nvservicestatus', 'tbl_material.id', '=', 'nvservicestatus.material_id')
                ->whereIn('tbl_material.nv_id', $BRPLnv)->where('nvservicestatus.work_rew1dep5_status', 0)
                ->where('nvservicestatus.ceo_nominee2_status', 1)
                ->sum('tbl_material.total_budget_both')+DB::table('tbl_service')
                ->join('nvservicestatus', 'tbl_service.id', '=', 'nvservicestatus.service_id')
                ->whereIn('tbl_service.nv_id', $BRPLnv)->where('nvservicestatus.work_rew1dep5_status', 0)
                ->where('nvservicestatus.ceo_nominee2_status', 1)
                ->sum('tbl_service.total_buget');
                // dd($pendingAmountBRPL);
        
                $rejectedAmountBRPL = DB::table('tbl_material')
                ->join('nvservicestatus', 'tbl_material.id', '=', 'nvservicestatus.material_id')
                ->whereIn('tbl_material.nv_id', $BRPLnv)
                ->where(function ($query) {
                    $query->where('nvservicestatus.work_rew1dep5_status', 2);
                })
                ->sum('tbl_material.total_budget_both')+DB::table('tbl_service')
                ->join('nvservicestatus', 'tbl_service.id', '=', 'nvservicestatus.service_id')
                ->whereIn('tbl_service.nv_id', $BRPLnv)
                ->where(function ($query) {
                    $query->where('nvservicestatus.work_rew1dep5_status', 2);
                })
                ->sum('tbl_service.total_buget');
           
        
                $approvedAmountBRPL = DB::table('tbl_material')
                ->join('nvservicestatus', 'tbl_material.id', '=', 'nvservicestatus.material_id')
                ->whereIn('tbl_material.nv_id', $BRPLnv)->where('nvservicestatus.ceo_status', 1)
                ->sum('tbl_material.total_budget_both')+DB::table('tbl_service')
                ->join('nvservicestatus', 'tbl_service.id', '=', 'nvservicestatus.service_id')
                ->whereIn('tbl_service.nv_id', $BRPLnv)->where('nvservicestatus.ceo_status', 1)
                ->sum('tbl_service.total_buget');


                $fileDataBRPL =Nvsericestatus::
                select(
                    DB::raw('MONTH(created_at) as month'),
                    DB::raw('SUM(CASE WHEN ceo_status = "1" THEN 1 ELSE 0 END) as approved_count'),
                    DB::raw('SUM(CASE WHEN ( work_rew1dep5_status = "2")  THEN 1 ELSE 0 END ) as rejected_count'),
                    DB::raw('SUM(CASE WHEN (approverdep4_status = "1" AND work_rew1dep5_status = "0" ) THEN 1 ELSE 0 END) as pending_count'),
                    // DB::raw('SUM(CASE WHEN status = "pending" THEN 1 ELSE 0 END) as rejected_count')
                )-> where('ceo_nominee2_status',1)
                ->whereIn('nv_id', $BRPLnv)->with(['service', 'material', 'user'])
                ->whereYear('created_at', Carbon::now()->year)
                ->groupBy('month')
                ->orderBy('month')
                ->get();
        // dd($fileDataBRPL);
            $BRPLlabels = [];
            $BRPLapprovedData = [];
            $BRPLrejectedData = [];
            $BRPLpendingData = [];
        
            foreach ($fileDataBRPL as $dataPointBRPL) {
                $monthBRPL = Carbon::createFromFormat('!m', $dataPointBRPL->month)->format('F');
        
                $BRPLlabels[] = $monthBRPL;
                $BRPLapprovedData[] = $dataPointBRPL->approved_count;
                $BRPLrejectedData[] = $dataPointBRPL->rejected_count;
                $BRPLpendingData[] = $dataPointBRPL->pending_count;
                 // dd( $dataPoint->rejected_count);
            }
    
            $BYPLnv = NeedValidation::where('company_id','5')->pluck("id");
            $pendingAmountBYPL = DB::table('tbl_material')
                ->join('nvservicestatus', 'tbl_material.id', '=', 'nvservicestatus.material_id')
                ->whereIn('tbl_material.nv_id', $BYPLnv)->where('nvservicestatus.work_rew1dep5_status', 0)
                ->where('nvservicestatus.ceo_nominee2_status', 1)
                ->sum('tbl_material.total_budget_both')+DB::table('tbl_service')
                ->join('nvservicestatus', 'tbl_service.id', '=', 'nvservicestatus.service_id')
                ->whereIn('tbl_service.nv_id', $BYPLnv)->where('nvservicestatus.work_rew1dep5_status', 0)
                ->where('nvservicestatus.ceo_nominee2_status', 1)
                ->sum('tbl_service.total_buget');
                // dd($pendingAmountBYPL);
        
                $rejectedAmountBYPL = DB::table('tbl_material')
                ->join('nvservicestatus', 'tbl_material.id', '=', 'nvservicestatus.material_id')
                ->whereIn('tbl_material.nv_id', $BYPLnv)
                ->where(function ($query) {
                    $query->where('nvservicestatus.work_rew1dep5_status', 2);
                })
                ->sum('tbl_material.total_budget_both')+DB::table('tbl_service')
                ->join('nvservicestatus', 'tbl_service.id', '=', 'nvservicestatus.service_id')
                ->whereIn('tbl_service.nv_id', $BYPLnv)
                ->where(function ($query) {
                    $query->where('nvservicestatus.work_rew1dep5_status', 2);
                })
                ->sum('tbl_service.total_buget');
           
        
                $approvedAmountBYPL = DB::table('tbl_material')
                ->join('nvservicestatus', 'tbl_material.id', '=', 'nvservicestatus.material_id')
                ->whereIn('tbl_material.nv_id', $BYPLnv)->where('nvservicestatus.ceo_status', 1)
                ->sum('tbl_material.total_budget_both')+DB::table('tbl_service')
                ->join('nvservicestatus', 'tbl_service.id', '=', 'nvservicestatus.service_id')
                ->whereIn('tbl_service.nv_id', $BYPLnv)->where('nvservicestatus.ceo_status', 1)
                ->sum('tbl_service.total_buget');
             $fileDataBYPL =Nvsericestatus::
            select(
                DB::raw('MONTH(created_at) as month'),
                DB::raw('SUM(CASE WHEN ceo_status = "1" THEN 1 ELSE 0 END) as approved_count'),
                DB::raw('SUM(CASE WHEN ( work_rew1dep5_status = "2")  THEN 1 ELSE 0 END) as rejected_count'),
                DB::raw('SUM(CASE WHEN (approverdep4_status = "1" AND work_rew1dep5_status = "0" )  THEN 1 ELSE 0 END) as pending_count'),
                // DB::raw('SUM(CASE WHEN status = "pending" THEN 1 ELSE 0 END) as rejected_count')
            ) 
            ->whereIn('nv_id', $BYPLnv)->with(['service', 'material', 'user'])
            -> where('ceo_nominee2_status',1)
            ->whereYear('created_at', Carbon::now()->year)
            ->groupBy('month')
            ->orderBy('month')
            ->get();
            // dd($fileDataBYPL);
        $BYPLlabels = [];
        $BYPLapprovedData = [];
        $BYPLrejectedData = [];
        $BYPLpendingData = [];
    
        foreach ($fileDataBYPL as $dataPointBYPL) {
            $monthBYPL = Carbon::createFromFormat('!m', $dataPointBYPL->month)->format('F');
    
            $BYPLlabels[] = $monthBYPL;
            $BYPLapprovedData[] = $dataPointBYPL->approved_count;
            $BYPLrejectedData[] = $dataPointBYPL->rejected_count;
            $BYPLpendingData[] = $dataPointBYPL->pending_count;
             // dd( $dataPointBYPL->rejected_count);
        }
                return view("admin.dashboard", compact("approvedAmount", "rejectedAmount", "pendingAmount", "totalAmount","company", "company_id" , "nv_sm_data", "totalNV", "approvedNV", "rejectedNV", "pendingNV" , "hodApproval", "cpmgApproval", "btApproval", "ceoApproval"             ,'BRPLlabels','BRPLapprovedData','BRPLrejectedData','BRPLpendingData','BYPLlabels','BYPLapprovedData','BYPLrejectedData','BYPLpendingData','cesApproval','ceonominee1Approval','ceonominee2Approval','approvedAmountBYPL','approvedAmountBRPL','rejectedAmountBYPL','rejectedAmountBRPL','pendingAmountBYPL','pendingAmountBRPL','groupcioApproval'));
            } elseif ($id5->work_rew2 == $user->id) {
                $Values = [$user->id, $id5->work_rew1];
                $nvid = [];
                $nvstatus = Nvsericestatus::get();
                foreach ($nvstatus as $nvstatus) {
                    array_push($nvid, $nvstatus["nv_id"]);
                }
                $user_nv = NeedValidation::with("division", "service")
                    ->whereIn("id", $nvid)
                    ->orderBy("id", "desc")
                    ->first();
                    if(!empty( $user_nv)){
                $employees = Employee::where("user_id", $user_nv->user_id)->first();
                $department = Department::where("id", $employees->department_id)->first();
                $group_cio = $department->group_cio;}
               
              $totalId = NeedValidation::pluck('id');
                // dd($totalId);
                $totalAmount = DB::table('tbl_material')
                ->join('nvservicestatus', 'tbl_material.id', '=', 'nvservicestatus.material_id')
                 // -> whereIn("nv_id", $totalId)
                 ->whereIn('tbl_material.nv_id', $totalId)
                 ->whereIn('nvservicestatus.nv_id', $latestData->pluck('nv_id'))
                ->where('nvservicestatus.work_rew2dep5_status', 1)
                ->sum('tbl_material.total_budget_both') + DB::table('tbl_service')
                ->join('nvservicestatus', 'tbl_service.id', '=', 'nvservicestatus.service_id')
                 // -> whereIn("nv_id", $totalId)
                 ->whereIn('tbl_service.nv_id', $totalId)
                 ->whereIn('nvservicestatus.nv_id', $latestData->pluck('nv_id'))
                ->where('nvservicestatus.work_rew2dep5_status', 1)
                ->sum('tbl_service.total_buget');
                // dd($totalAmount);
        
                $pendingAmount = DB::table('tbl_material')
                ->join('nvservicestatus', 'tbl_material.id', '=', 'nvservicestatus.material_id')
                 // -> whereIn("nv_id", $totalId)
                 ->whereIn('tbl_material.nv_id', $totalId)
                 ->whereIn('nvservicestatus.nv_id', $latestData->pluck('nv_id'))
                ->where('nvservicestatus.work_rew2dep5_status', 0)
                ->where('nvservicestatus.work_rew1dep5_status', 1)
                ->sum('tbl_material.total_budget_both') + DB::table('tbl_service')
                ->join('nvservicestatus', 'tbl_service.id', '=', 'nvservicestatus.service_id')
                 // -> whereIn("nv_id", $totalId)
                 ->whereIn('tbl_service.nv_id', $totalId)
                 ->whereIn('nvservicestatus.nv_id', $latestData->pluck('nv_id'))
                ->where('nvservicestatus.work_rew2dep5_status', 0)
                ->where('nvservicestatus.work_rew1dep5_status', 1)
                ->sum('tbl_service.total_buget');
        
                $rejectedAmount = DB::table('tbl_material')
                ->join('nvservicestatus', 'tbl_material.id', '=', 'nvservicestatus.material_id')
                ->whereIn('nvservicestatus.nv_id', $latestData->pluck('nv_id'))
                ->where(function ($query) {
                    $query->where('nvservicestatus.work_rew2dep5_status', 2);
                })
                ->sum('tbl_material.total_budget_both') + DB::table('tbl_service')
                ->join('nvservicestatus', 'tbl_service.id', '=', 'nvservicestatus.service_id')
                ->whereIn('nvservicestatus.nv_id', $latestData->pluck('nv_id'))
                ->where(function ($query) {
                    $query->where('nvservicestatus.work_rew2dep5_status', 2);
                })
                ->sum('tbl_service.total_buget');
           
        
                $approvedAmount = DB::table('tbl_material')
                 // -> whereIn("nv_id", $totalId)
                 ->whereIn('tbl_material.nv_id', $totalId)
                 ->whereIn('nvservicestatus.nv_id', $latestData->pluck('nv_id'))
                ->join('nvservicestatus', 'tbl_material.id', '=', 'nvservicestatus.material_id')
                ->where('nvservicestatus.ceo_status', 1)
                ->sum('tbl_material.total_budget_both') + DB::table('tbl_service')
                // -> whereIn("nv_id", $totalId)
                ->whereIn('tbl_service.nv_id', $totalId)
                ->whereIn('nvservicestatus.nv_id', $latestData->pluck('nv_id'))
               ->join('nvservicestatus', 'tbl_service.id', '=', 'nvservicestatus.service_id')
               ->where('nvservicestatus.ceo_status', 1)
               ->sum('tbl_service.total_buget');


                if($company_id){
                    $totalNV = Nvsericestatus::whereIn("nv_id", $totalId)->where('work_rew2dep5_status', 1)->where('company_id',$company_id)->count();
                } else {
                    $totalNV = Nvsericestatus::whereIn("nv_id", $totalId)->where('work_rew2dep5_status', 1)->count();
                }
                if($company_id){
                    $rejectedNV = Nvsericestatus::whereIn("nv_id", $totalId)->where('work_rew2dep5_status', 2)->where('company_id',$company_id)->count();
                } else {
                    $rejectedNV = Nvsericestatus::whereIn("nv_id", $totalId)->where('work_rew2dep5_status', 2)->count();
                }
                // dd($totalId);
                if($company_id){
                $latestData = Nvsericestatus::whereIn("nv_id", $totalId)->where('company_id',$company_id)->get();
            }else {
                $latestData = Nvsericestatus::whereIn("nv_id", $totalId)->get();
            }
                if($company_id){
                    $approvedNV = $latestData->where('ceo_status', 1)->where('company_id',$company_id)->count();
                }else{
                    $approvedNV = $latestData->where('ceo_status', 1)->count();
                }
                // $rejectedNV = $latestData->filter(function ($data) {
                //     return in_array($data->ceo_status, [2]) || 
                //     $data->work_rew1_status == 2 ;
                //     // $data->hod_status == 2 || 
                //     // $data->cpmg_status == 2 || 
                //     // $data->ces_status == 2 || 
                //     // $data->cto_status == 2 || 
                //     // $data->ceo_nominee_status == 2 || 
                //     // $data->ceo_nominee2_status == 2 || 
                //     // $data->ceo_nominee2_status == 2;
                // })->count();
                $pendingNV = $latestData->filter(function ($data) {
                    return in_array($data->approverdep4_status, [ 1]) &&
                    in_array($data->work_rew1dep5_status, [1])&&
                    in_array($data->work_rew2dep5_status, [0]);
                    
                  
                   
                   
                    // in_array($data->ces_status, [0]) &&
                    // in_array($data->cto_status, [0]) &&
                    // in_array($data->ceo_nominee_status, [0]) &&
                    // in_array($data->ceo_status, [0]);
                    })->count();
                 

                $hodApproval = Nvsericestatus::where("hod_status", 0)->count();
                $cesApproval = Nvsericestatus::where("hod_status", 1)->where("ces_status", 0)->where("derc_info", 1)->count();
                $cpmg = Nvsericestatus::where('hod_status', 1)->where('cpmg_status', 0)->get();
                $Pendingcpmg = $cpmg->filter(function ($data) {
                 return in_array($data->hod_status, [1]) &&
                     in_array($data->cpmg_status, [0]) &&
                     (
                         (in_array($data->derc_info, [0]) && in_array($data->ces_status, [0]))
                         ||
                         (in_array($data->derc_info, [1]) && in_array($data->ces_status, [1]))
                     );
             });
             $cpmgApproval = $Pendingcpmg->count(); 

                $btApproval = Nvsericestatus::where("cpmg_status", 1)->where("check_technology", 1)->where('cto_status', 0)->count();
              
                $ceonominee1 = Nvsericestatus::where('cpmg_status', 1)->where('ceo_nominee_status', 0)->get();
                 $Pendingnominee1 = $ceonominee1->filter(function ($data) {
                 return in_array($data->cpmg_status, [1]) &&
                     in_array($data->ceo_nominee_status, [0]) &&
                     (
                         (in_array($data->check_technology, [0]) && in_array($data->cto_status, [0]))
                         ||
                         (in_array($data->check_technology, [1]) && in_array($data->cto_status, [1]))
                     );
             });
             $ceonominee1Approval = $Pendingnominee1->count(); 
             if(!empty( $group_cio)){
                $ceonominee2Approval = Nvsericestatus::where('groupcio_status', 1)->where('ceo_nominee2_status', 0)->whereIn("nv_id", $totalId)->count();
                }else{
                    $ceonominee2Approval = Nvsericestatus::where('ceo_nominee_status', 1)->where('ceo_nominee2_status', 0)->whereIn("nv_id", $totalId)->count();
     
                }
            if(!empty( $group_cio)){
                $groupcioApproval = Nvsericestatus::where('ceo_nominee_status', 1)->where('groupcio_status', 0)->where('ceo_nominee2_status', 0)->whereIn("nv_id", $totalId)->count();
                }else{
                    $groupcioApproval =0;  
                }
    
                    // if(!empty( $group_cio)){
                    // $ceoApproval = Nvsericestatus::where('groupcio_status', 1)->where('ceo_status', 0)->whereIn("nv_id", $totalId)->count();
                    // }else{
                    $ceoApproval = Nvsericestatus::where('ceo_nominee2_status', 1)->where('ceo_status', 0)->whereIn("nv_id", $totalId)->count();
                    // }

               
                $nvIds = NeedValidation::where("user_id", $Values)->pluck("id");
                $nv = NeedValidation::whereHas('service')->select('id')
                // ->whereIn('user_id', $Values)
                // ->orWhereIn('user_id',$allNormalUsers)
                ->get();
                $nv_ids = $nv->pluck('id');
                // $nv_sm_data = Nvsericestatus::whereIn('nv_id', $nv_ids)
                // ->with(['service', 'material', 'user'])
                // ->where('hod_status',1)
                // ->orderBy('id', 'desc')
                // ->get();
                if($company_id){
                    $nv_sm_data = Nvsericestatus::whereIn('nv_id', $nv_ids)->with(['service', 'material', 'user'])
                ->where('ceo_nominee2_status',1)
                ->where('company_id',$company_id)
                ->orderBy('id', 'desc');
                } else {
                    $nv_sm_data = Nvsericestatus::whereIn('nv_id', $nv_ids)->with(['service', 'material', 'user'])
                ->where('ceo_nominee2_status',1)
                ->orderBy('id', 'desc');
                }
                $total29_status = $request->total;
                $pending29_status = $request->pending;
                $reject29_status = $request->rejected;

               
              
               
                if ($ceo_status !== null) {
                    $nv_sm_data = $nv_sm_data->where('ceo_status', $ceo_status);
                } elseif ($pending29_status !== null) {
                    $nv_sm_data = $nv_sm_data->where('work_rew2dep5_status', $pending29_status)
                       
                        ->whereIn('hod_status', [ 1])
                        ->whereIn('approver_status', [ 1])    
                       
                        ->whereIn('cto_status', [0, 1])
                        ->whereIn('approverdep3_status', [ 1])
                        ->whereIn('work_rew1dep4_status', [ 1])
                        ->whereIn('approverdep4_status', [ 1])
                        ->whereIn('work_rew1dep5_status', [ 1]);
                     
                       

                } elseif ($reject29_status !== null) {
                    $nv_sm_data = $nv_sm_data->where('work_rew2dep5_status', $reject29_status);
                    
                }elseif ($total29_status !== null) {
                    $nv_sm_data = $nv_sm_data->where('work_rew2dep5_status', $total29_status);
                      
                }
                $nv_sm_data = $nv_sm_data->get();

                $BRPLnv = NeedValidation::where('company_id','6')->pluck("id");

                $pendingAmountBRPL = DB::table('tbl_material')
                ->join('nvservicestatus', 'tbl_material.id', '=', 'nvservicestatus.material_id')
                ->whereIn('tbl_material.nv_id', $BRPLnv)->where('nvservicestatus.work_rew2dep5_status', 0)
                ->where('nvservicestatus.work_rew1dep5_status', 1)
                ->sum('tbl_material.total_budget_both')+DB::table('tbl_service')
                ->join('nvservicestatus', 'tbl_service.id', '=', 'nvservicestatus.service_id')
                ->whereIn('tbl_service.nv_id', $BRPLnv)->where('nvservicestatus.work_rew2dep5_status', 0)
                ->where('nvservicestatus.work_rew1dep5_status', 1)
                ->sum('tbl_service.total_buget');
                // dd($pendingAmountBRPL);
        
                $rejectedAmountBRPL = DB::table('tbl_material')
                ->join('nvservicestatus', 'tbl_material.id', '=', 'nvservicestatus.material_id')
                ->whereIn('tbl_material.nv_id', $BRPLnv)
                ->where(function ($query) {
                    $query->where('nvservicestatus.work_rew2dep5_status', 2);
                })
                ->sum('tbl_material.total_budget_both')+DB::table('tbl_service')
                ->join('nvservicestatus', 'tbl_service.id', '=', 'nvservicestatus.service_id')
                ->whereIn('tbl_service.nv_id', $BRPLnv)
                ->where(function ($query) {
                    $query->where('nvservicestatus.work_rew2dep5_status', 2);
                })
                ->sum('tbl_service.total_buget');
           
        
                $approvedAmountBRPL = DB::table('tbl_material')
                ->join('nvservicestatus', 'tbl_material.id', '=', 'nvservicestatus.material_id')
                ->whereIn('tbl_material.nv_id', $BRPLnv)->where('nvservicestatus.ceo_status', 1)
                ->sum('tbl_material.total_budget_both')+DB::table('tbl_service')
                ->join('nvservicestatus', 'tbl_service.id', '=', 'nvservicestatus.service_id')
                ->whereIn('tbl_service.nv_id', $BRPLnv)->where('nvservicestatus.ceo_status', 1)
                ->sum('tbl_service.total_buget');


                $fileDataBRPL =Nvsericestatus::
                select(
                    DB::raw('MONTH(created_at) as month'),
                    DB::raw('SUM(CASE WHEN ceo_status = "1" THEN 1 ELSE 0 END) as approved_count'),
                    DB::raw('SUM(CASE WHEN ( work_rew2dep5_status = "2")  THEN 1 ELSE 0 END ) as rejected_count'),
                    DB::raw('SUM(CASE WHEN (approverdep4_status = "1" AND work_rew1dep5_status = "1" AND work_rew2dep5_status = "0" ) THEN 1 ELSE 0 END) as pending_count'),
                    // DB::raw('SUM(CASE WHEN status = "pending" THEN 1 ELSE 0 END) as rejected_count')
                )-> where('ceo_nominee2_status',1)
                ->whereIn('nv_id', $BRPLnv)->with(['service', 'material', 'user'])
                ->whereYear('created_at', Carbon::now()->year)
                ->groupBy('month')
                ->orderBy('month')
                ->get();
        // dd($fileDataBRPL);
            $BRPLlabels = [];
            $BRPLapprovedData = [];
            $BRPLrejectedData = [];
            $BRPLpendingData = [];
        
            foreach ($fileDataBRPL as $dataPointBRPL) {
                $monthBRPL = Carbon::createFromFormat('!m', $dataPointBRPL->month)->format('F');
        
                $BRPLlabels[] = $monthBRPL;
                $BRPLapprovedData[] = $dataPointBRPL->approved_count;
                $BRPLrejectedData[] = $dataPointBRPL->rejected_count;
                $BRPLpendingData[] = $dataPointBRPL->pending_count;
                 // dd( $dataPoint->rejected_count);
            }
    
            $BYPLnv = NeedValidation::where('company_id','5')->pluck("id");

            $pendingAmountBYPL = DB::table('tbl_material')
            ->join('nvservicestatus', 'tbl_material.id', '=', 'nvservicestatus.material_id')
            ->whereIn('tbl_material.nv_id', $BYPLnv)->where('nvservicestatus.work_rew2dep5_status', 0)
            ->where('nvservicestatus.work_rew1dep5_status', 1)
            ->sum('tbl_material.total_budget_both')+DB::table('tbl_service')
            ->join('nvservicestatus', 'tbl_service.id', '=', 'nvservicestatus.service_id')
            ->whereIn('tbl_service.nv_id', $BYPLnv)->where('nvservicestatus.work_rew2dep5_status', 0)
            ->where('nvservicestatus.work_rew1dep5_status', 1)
            ->sum('tbl_service.total_buget');
            // dd($pendingAmountBYPL);
    
            $rejectedAmountBYPL = DB::table('tbl_material')
            ->join('nvservicestatus', 'tbl_material.id', '=', 'nvservicestatus.material_id')
            ->whereIn('tbl_material.nv_id', $BYPLnv)
            ->where(function ($query) {
                $query->where('nvservicestatus.work_rew2dep5_status', 2);
            })
            ->sum('tbl_material.total_budget_both')+DB::table('tbl_service')
            ->join('nvservicestatus', 'tbl_service.id', '=', 'nvservicestatus.service_id')
            ->whereIn('tbl_service.nv_id', $BYPLnv)
            ->where(function ($query) {
                $query->where('nvservicestatus.work_rew2dep5_status', 2);
            })
            ->sum('tbl_service.total_buget');
       
    
            $approvedAmountBYPL = DB::table('tbl_material')
            ->join('nvservicestatus', 'tbl_material.id', '=', 'nvservicestatus.material_id')
            ->whereIn('tbl_material.nv_id', $BYPLnv)->where('nvservicestatus.ceo_status', 1)
            ->sum('tbl_material.total_budget_both')+DB::table('tbl_service')
            ->join('nvservicestatus', 'tbl_service.id', '=', 'nvservicestatus.service_id')
            ->whereIn('tbl_service.nv_id', $BYPLnv)->where('nvservicestatus.ceo_status', 1)
            ->sum('tbl_service.total_buget');
          
             $fileDataBYPL =Nvsericestatus::
            select(
                DB::raw('MONTH(created_at) as month'),
                DB::raw('SUM(CASE WHEN ceo_status = "1" THEN 1 ELSE 0 END) as approved_count'),
                DB::raw('SUM(CASE WHEN ( work_rew2dep5_status = "2")  THEN 1 ELSE 0 END) as rejected_count'),
                DB::raw('SUM(CASE WHEN (approverdep4_status = "1" AND work_rew1dep5_status = "1" AND work_rew2dep5_status = "0" )  THEN 1 ELSE 0 END) as pending_count'),
                // DB::raw('SUM(CASE WHEN status = "pending" THEN 1 ELSE 0 END) as rejected_count')
            ) 
            ->whereIn('nv_id', $BYPLnv)->with(['service', 'material', 'user'])
            -> where('ceo_nominee2_status',1)
            ->whereYear('created_at', Carbon::now()->year)
            ->groupBy('month')
            ->orderBy('month')
            ->get();
            // dd($fileDataBYPL);
        $BYPLlabels = [];
        $BYPLapprovedData = [];
        $BYPLrejectedData = [];
        $BYPLpendingData = [];
    
        foreach ($fileDataBYPL as $dataPointBYPL) {
            $monthBYPL = Carbon::createFromFormat('!m', $dataPointBYPL->month)->format('F');
    
            $BYPLlabels[] = $monthBYPL;
            $BYPLapprovedData[] = $dataPointBYPL->approved_count;
            $BYPLrejectedData[] = $dataPointBYPL->rejected_count;
            $BYPLpendingData[] = $dataPointBYPL->pending_count;
             // dd( $dataPointBYPL->rejected_count);
        }
                return view("admin.dashboard", compact("approvedAmount", "rejectedAmount", "pendingAmount", "totalAmount","company", "company_id" , "nv_sm_data", "totalNV", "approvedNV", "rejectedNV", "pendingNV" , "hodApproval", "cpmgApproval", "btApproval", "ceoApproval"             ,'BRPLlabels','BRPLapprovedData','BRPLrejectedData','BRPLpendingData','BYPLlabels','BYPLapprovedData','BYPLrejectedData','BYPLpendingData','cesApproval','ceonominee1Approval','ceonominee2Approval','pendingAmountBYPL','pendingAmountBRPL','rejectedAmountBYPL','rejectedAmountBRPL','approvedAmountBYPL','approvedAmountBRPL','groupcioApproval'));
            } elseif ($id5->work_rew3 == $user->id) {
                $Values = [$user->id, $id5->work_rew1, $id5->work_rew2];
                $nvid = [];
                $nvstatus = Nvsericestatus::get();
                foreach ($nvstatus as $nvstatus) {
                    array_push($nvid, $nvstatus["nv_id"]);
                }
                $user_nv = NeedValidation::with("division", "service")
                    ->whereIn("id", $nvid)
                    ->orderBy("id", "desc")
                    ->first();
                    if(!empty( $user_nv)){
                $employees = Employee::where("user_id", $user_nv->user_id)->first();
                $department = Department::where("id", $employees->department_id)->first();
                $group_cio = $department->group_cio;}
              $totalId = NeedValidation::pluck('id');
                // dd($totalId);
                $totalAmount = DB::table('tbl_material')
                ->join('nvservicestatus', 'tbl_material.id', '=', 'nvservicestatus.material_id')
                 // -> whereIn("nv_id", $totalId)
                 ->whereIn('tbl_material.nv_id', $totalId)
                 ->whereIn('nvservicestatus.nv_id', $latestData->pluck('nv_id'))
                ->where('nvservicestatus.work_rew3dep5_status', 1)
                ->sum('tbl_material.total_budget_both') +  DB::table('tbl_service')
                ->join('nvservicestatus', 'tbl_service.id', '=', 'nvservicestatus.service_id')
                 // -> whereIn("nv_id", $totalId)
                 ->whereIn('tbl_service.nv_id', $totalId)
                 ->whereIn('nvservicestatus.nv_id', $latestData->pluck('nv_id'))
                ->where('nvservicestatus.work_rew3dep5_status', 1)
                ->sum('tbl_service.total_buget');
                // dd($totalAmount);
        
                $pendingAmount = DB::table('tbl_material')
                ->join('nvservicestatus', 'tbl_material.id', '=', 'nvservicestatus.material_id')
                 // -> whereIn("nv_id", $totalId)
                 ->whereIn('tbl_material.nv_id', $totalId)
                 ->whereIn('nvservicestatus.nv_id', $latestData->pluck('nv_id'))
                ->where('nvservicestatus.work_rew3dep5_status', 0)
                ->where('nvservicestatus.work_rew2dep5_status', 1)
                ->sum('tbl_material.total_budget_both') + DB::table('tbl_service')
                ->join('nvservicestatus', 'tbl_service.id', '=', 'nvservicestatus.service_id')
                 // -> whereIn("nv_id", $totalId)
                 ->whereIn('tbl_service.nv_id', $totalId)
                 ->whereIn('nvservicestatus.nv_id', $latestData->pluck('nv_id'))
                ->where('nvservicestatus.work_rew3dep5_status', 0)
                ->where('nvservicestatus.work_rew2dep5_status', 1)
                ->sum('tbl_service.total_buget');
        
                $rejectedAmount = DB::table('tbl_material')
                ->join('nvservicestatus', 'tbl_material.id', '=', 'nvservicestatus.material_id')
                ->whereIn('nvservicestatus.nv_id', $latestData->pluck('nv_id'))
                ->where(function ($query) {
                    $query->where('nvservicestatus.work_rew3dep5_status', 2);
                })
                ->sum('tbl_material.total_budget_both') + DB::table('tbl_service')
                ->join('nvservicestatus', 'tbl_service.id', '=', 'nvservicestatus.service_id')
                ->whereIn('nvservicestatus.nv_id', $latestData->pluck('nv_id'))
                ->where(function ($query) {
                    $query->where('nvservicestatus.work_rew3dep5_status', 2);
                })
                ->sum('tbl_service.total_buget');
           
        
                $approvedAmount = DB::table('tbl_material')
                 // -> whereIn("nv_id", $totalId)
                 ->whereIn('tbl_material.nv_id', $totalId)
                 ->whereIn('nvservicestatus.nv_id', $latestData->pluck('nv_id'))
                ->join('nvservicestatus', 'tbl_material.id', '=', 'nvservicestatus.material_id')
                ->where('nvservicestatus.ceo_status', 1)
                ->sum('tbl_material.total_budget_both') +  DB::table('tbl_service')
                // -> whereIn("nv_id", $totalId)
                ->whereIn('tbl_service.nv_id', $totalId)
                ->whereIn('nvservicestatus.nv_id', $latestData->pluck('nv_id'))
               ->join('nvservicestatus', 'tbl_service.id', '=', 'nvservicestatus.service_id')
               ->where('nvservicestatus.ceo_status', 1)
               ->sum('tbl_service.total_buget');


                if($company_id){
                    $totalNV = Nvsericestatus::whereIn("nv_id", $totalId)->where('work_rew3dep5_status', 1)->where('company_id',$company_id)->count();
                } else {
                    $totalNV = Nvsericestatus::whereIn("nv_id", $totalId)->where('work_rew3dep5_status', 1)->count();
                }
                if($company_id){
                    $rejectedNV = Nvsericestatus::whereIn("nv_id", $totalId)->where('work_rew3dep5_status', 2)->where('company_id',$company_id)->count();
                } else {
                    $rejectedNV = Nvsericestatus::whereIn("nv_id", $totalId)->where('work_rew3dep5_status', 2)->count();
                }
                // dd($totalId);
                if($company_id){
                $latestData = Nvsericestatus::whereIn("nv_id", $totalId)->where('company_id',$company_id)->get();
            }else {
                $latestData = Nvsericestatus::whereIn("nv_id", $totalId)->get();
            }
                if($company_id){
                    $approvedNV = $latestData->where('ceo_status', 1)->where('company_id',$company_id)->count();
                }else{
                    $approvedNV = $latestData->where('ceo_status', 1)->count();
                }
                // $rejectedNV = $latestData->filter(function ($data) {
                //     return in_array($data->ceo_status, [2]) || 
                //     $data->work_rew1_status == 2 ;
                //     // $data->hod_status == 2 || 
                //     // $data->cpmg_status == 2 || 
                //     // $data->ces_status == 2 || 
                //     // $data->cto_status == 2 || 
                //     // $data->ceo_nominee_status == 2 || 
                //     // $data->ceo_nominee2_status == 2 || 
                //     // $data->ceo_nominee2_status == 2;
                // })->count();
                $pendingNV = $latestData->filter(function ($data) {
                    return in_array($data->approverdep4_status, [ 1]) &&
                    in_array($data->work_rew1dep5_status, [1])&&
                    in_array($data->work_rew3dep5_status, [0]);
                    
                  
                   
                   
                    // in_array($data->ces_status, [0]) &&
                    // in_array($data->cto_status, [0]) &&
                    // in_array($data->ceo_nominee_status, [0]) &&
                    // in_array($data->ceo_status, [0]);
                    })->count();
                 

                 $hodApproval = Nvsericestatus::where("hod_status", 0)->count();
                $cesApproval = Nvsericestatus::where("hod_status", 1)->where("ces_status", 0)->where("derc_info", 1)->count();
                $cpmg = Nvsericestatus::where('hod_status', 1)->where('cpmg_status', 0)->get();
                $Pendingcpmg = $cpmg->filter(function ($data) {
                 return in_array($data->hod_status, [1]) &&
                     in_array($data->cpmg_status, [0]) &&
                     (
                         (in_array($data->derc_info, [0]) && in_array($data->ces_status, [0]))
                         ||
                         (in_array($data->derc_info, [1]) && in_array($data->ces_status, [1]))
                     );
             });
             $cpmgApproval = $Pendingcpmg->count(); 

                $btApproval = Nvsericestatus::where("cpmg_status", 1)->where("check_technology", 1)->where('cto_status', 0)->count();
              
                $ceonominee1 = Nvsericestatus::where('cpmg_status', 1)->where('ceo_nominee_status', 0)->get();
                 $Pendingnominee1 = $ceonominee1->filter(function ($data) {
                 return in_array($data->cpmg_status, [1]) &&
                     in_array($data->ceo_nominee_status, [0]) &&
                     (
                         (in_array($data->check_technology, [0]) && in_array($data->cto_status, [0]))
                         ||
                         (in_array($data->check_technology, [1]) && in_array($data->cto_status, [1]))
                     );
             });
             $ceonominee1Approval = $Pendingnominee1->count(); 
             if(!empty( $group_cio)){
                $ceonominee2Approval = Nvsericestatus::where('groupcio_status', 1)->where('ceo_nominee2_status', 0)->whereIn("nv_id", $totalId)->count();
                }else{
                    $ceonominee2Approval = Nvsericestatus::where('ceo_nominee_status', 1)->where('ceo_nominee2_status', 0)->whereIn("nv_id", $totalId)->count();
     
                }
            if(!empty( $group_cio)){
                $groupcioApproval = Nvsericestatus::where('ceo_nominee_status', 1)->where('groupcio_status', 0)->where('ceo_nominee2_status', 0)->whereIn("nv_id", $totalId)->count();
                }else{
                    $groupcioApproval =0;  
                }
    
                    // if(!empty( $group_cio)){
                    // $ceoApproval = Nvsericestatus::where('groupcio_status', 1)->where('ceo_status', 0)->whereIn("nv_id", $totalId)->count();
                    // }else{
                    $ceoApproval = Nvsericestatus::where('ceo_nominee2_status', 1)->where('ceo_status', 0)->whereIn("nv_id", $totalId)->count();
                    // }

              
                $nvIds = NeedValidation::where("user_id", $Values)->pluck("id");
                $nv = NeedValidation::whereHas('service')->select('id')
                // ->whereIn('user_id', $Values)
                // ->orWhereIn('user_id',$allNormalUsers)
                ->get();
                $nv_ids = $nv->pluck('id');
                // $nv_sm_data = Nvsericestatus::whereIn('nv_id', $nv_ids)
                // ->with(['service', 'material', 'user'])
                // ->where('hod_status',1)
                // ->orderBy('id', 'desc')
                // ->get();
                if($company_id){
                    $nv_sm_data = Nvsericestatus::whereIn('nv_id', $nv_ids)->with(['service', 'material', 'user'])
                ->where('ceo_nominee2_status',1)
                ->where('company_id',$company_id)
                ->orderBy('id', 'desc');
                } else {
                    $nv_sm_data = Nvsericestatus::whereIn('nv_id', $nv_ids)->with(['service', 'material', 'user'])
                ->where('ceo_nominee2_status',1)
                ->orderBy('id', 'desc');
                }
                $total30_status = $request->total;
                $pending30_status = $request->pending;
                $reject30_status = $request->rejected;

               
              
               
                if ($ceo_status !== null) {
                    $nv_sm_data = $nv_sm_data->where('ceo_status', $ceo_status);
                } elseif ($pending30_status !== null) {
                    $nv_sm_data = $nv_sm_data->where('work_rew3dep5_status', $pending30_status)
                       
                        ->whereIn('hod_status', [ 1])
                        ->whereIn('approver_status', [ 1])    
                       
                        ->whereIn('cto_status', [0, 1])
                        ->whereIn('approverdep3_status', [ 1])
                        ->whereIn('work_rew1dep4_status', [ 1])
                        ->whereIn('approverdep4_status', [ 1])
                        ->whereIn('work_rew1dep5_status', [ 1])
                        ->whereIn('work_rew2dep5_status', [ 1]);
                     
                       

                } elseif ($reject30_status !== null) {
                    $nv_sm_data = $nv_sm_data->where('work_rew3dep5_status', $reject30_status);
                    
                }elseif ($total30_status !== null) {
                    $nv_sm_data = $nv_sm_data->where('work_rew3dep5_status', $total30_status);
                      
                }
                $nv_sm_data = $nv_sm_data->get();
                $BRPLnv = NeedValidation::where('company_id','6')->pluck("id");

                $pendingAmountBRPL = DB::table('tbl_material')
                ->join('nvservicestatus', 'tbl_material.id', '=', 'nvservicestatus.material_id')
                ->whereIn('tbl_material.nv_id', $BRPLnv)->where('nvservicestatus.work_rew3dep5_status', 0)
                ->where('nvservicestatus.work_rew2dep5_status', 1)
                ->sum('tbl_material.total_budget_both')+DB::table('tbl_service')
                ->join('nvservicestatus', 'tbl_service.id', '=', 'nvservicestatus.service_id')
                ->whereIn('tbl_service.nv_id', $BRPLnv)->where('nvservicestatus.work_rew3dep5_status', 0)
                ->where('nvservicestatus.work_rew2dep5_status', 1)
                ->sum('tbl_service.total_buget');
                // dd($pendingAmountBRPL);
        
                $rejectedAmountBRPL = DB::table('tbl_material')
                ->join('nvservicestatus', 'tbl_material.id', '=', 'nvservicestatus.material_id')
                ->whereIn('tbl_material.nv_id', $BRPLnv)
                ->where(function ($query) {
                    $query->where('nvservicestatus.work_rew3dep5_status', 2);
                })
                ->sum('tbl_material.total_budget_both')+DB::table('tbl_service')
                ->join('nvservicestatus', 'tbl_service.id', '=', 'nvservicestatus.service_id')
                ->whereIn('tbl_service.nv_id', $BRPLnv)
                ->where(function ($query) {
                    $query->where('nvservicestatus.work_rew3dep5_status', 2);
                })
                ->sum('tbl_service.total_buget');
           
        
                $approvedAmountBRPL = DB::table('tbl_material')
                ->join('nvservicestatus', 'tbl_material.id', '=', 'nvservicestatus.material_id')
                ->whereIn('tbl_material.nv_id', $BRPLnv)->where('nvservicestatus.ceo_status', 1)
                ->sum('tbl_material.total_budget_both')+DB::table('tbl_service')
                ->join('nvservicestatus', 'tbl_service.id', '=', 'nvservicestatus.service_id')
                ->whereIn('tbl_service.nv_id', $BRPLnv)->where('nvservicestatus.ceo_status', 1)
                ->sum('tbl_service.total_buget');

                $fileDataBRPL =Nvsericestatus::
                select(
                    DB::raw('MONTH(created_at) as month'),
                    DB::raw('SUM(CASE WHEN ceo_status = "1" THEN 1 ELSE 0 END) as approved_count'),
                    DB::raw('SUM(CASE WHEN ( work_rew3dep5_status = "2")  THEN 1 ELSE 0 END ) as rejected_count'),
                    DB::raw('SUM(CASE WHEN (approverdep4_status = "1" AND work_rew1dep5_status = "1" AND work_rew3dep5_status = "0" ) THEN 1 ELSE 0 END) as pending_count'),
                    // DB::raw('SUM(CASE WHEN status = "pending" THEN 1 ELSE 0 END) as rejected_count')
                )-> where('ceo_nominee2_status',1)
                ->whereIn('nv_id', $BRPLnv)->with(['service', 'material', 'user'])
                ->whereYear('created_at', Carbon::now()->year)
                ->groupBy('month')
                ->orderBy('month')
                ->get();
        // dd($fileDataBRPL);
            $BRPLlabels = [];
            $BRPLapprovedData = [];
            $BRPLrejectedData = [];
            $BRPLpendingData = [];
        
            foreach ($fileDataBRPL as $dataPointBRPL) {
                $monthBRPL = Carbon::createFromFormat('!m', $dataPointBRPL->month)->format('F');
        
                $BRPLlabels[] = $monthBRPL;
                $BRPLapprovedData[] = $dataPointBRPL->approved_count;
                $BRPLrejectedData[] = $dataPointBRPL->rejected_count;
                $BRPLpendingData[] = $dataPointBRPL->pending_count;
                 // dd( $dataPoint->rejected_count);
            }
    
            $BYPLnv = NeedValidation::where('company_id','5')->pluck("id");

            $pendingAmountBYPL = DB::table('tbl_material')
                ->join('nvservicestatus', 'tbl_material.id', '=', 'nvservicestatus.material_id')
                ->whereIn('tbl_material.nv_id', $BYPLnv)->where('nvservicestatus.work_rew3dep5_status', 0)
                ->where('nvservicestatus.work_rew2dep5_status', 1)
                ->sum('tbl_material.total_budget_both')+DB::table('tbl_service')
                ->join('nvservicestatus', 'tbl_service.id', '=', 'nvservicestatus.service_id')
                ->whereIn('tbl_service.nv_id', $BYPLnv)->where('nvservicestatus.work_rew3dep5_status', 0)
                ->where('nvservicestatus.work_rew2dep5_status', 1)
                ->sum('tbl_service.total_buget');
                // dd($pendingAmountBYPL);
        
                $rejectedAmountBYPL = DB::table('tbl_material')
                ->join('nvservicestatus', 'tbl_material.id', '=', 'nvservicestatus.material_id')
                ->whereIn('tbl_material.nv_id', $BYPLnv)
                ->where(function ($query) {
                    $query->where('nvservicestatus.work_rew3dep5_status', 2);
                })
                ->sum('tbl_material.total_budget_both')+DB::table('tbl_service')
                ->join('nvservicestatus', 'tbl_service.id', '=', 'nvservicestatus.service_id')
                ->whereIn('tbl_service.nv_id', $BYPLnv)
                ->where(function ($query) {
                    $query->where('nvservicestatus.work_rew3dep5_status', 2);
                })
                ->sum('tbl_service.total_buget');
           
        
                $approvedAmountBYPL = DB::table('tbl_material')
                ->join('nvservicestatus', 'tbl_material.id', '=', 'nvservicestatus.material_id')
                ->whereIn('tbl_material.nv_id', $BYPLnv)->where('nvservicestatus.ceo_status', 1)
                ->sum('tbl_material.total_budget_both')+DB::table('tbl_service')
                ->join('nvservicestatus', 'tbl_service.id', '=', 'nvservicestatus.service_id')
                ->whereIn('tbl_service.nv_id', $BYPLnv)->where('nvservicestatus.ceo_status', 1)
                ->sum('tbl_service.total_buget');
            
             $fileDataBYPL =Nvsericestatus::
            select(
                DB::raw('MONTH(created_at) as month'),
                DB::raw('SUM(CASE WHEN ceo_status = "1" THEN 1 ELSE 0 END) as approved_count'),
                DB::raw('SUM(CASE WHEN ( work_rew3dep5_status = "2")  THEN 1 ELSE 0 END) as rejected_count'),
                DB::raw('SUM(CASE WHEN (approverdep4_status = "1" AND work_rew1dep5_status = "1" AND work_rew3dep5_status = "0" )  THEN 1 ELSE 0 END) as pending_count'),
                // DB::raw('SUM(CASE WHEN status = "pending" THEN 1 ELSE 0 END) as rejected_count')
            ) 
            ->whereIn('nv_id', $BYPLnv)->with(['service', 'material', 'user'])
            -> where('ceo_nominee2_status',1)
            ->whereYear('created_at', Carbon::now()->year)
            ->groupBy('month')
            ->orderBy('month')
            ->get();
            // dd($fileDataBYPL);
        $BYPLlabels = [];
        $BYPLapprovedData = [];
        $BYPLrejectedData = [];
        $BYPLpendingData = [];
    
        foreach ($fileDataBYPL as $dataPointBYPL) {
            $monthBYPL = Carbon::createFromFormat('!m', $dataPointBYPL->month)->format('F');
    
            $BYPLlabels[] = $monthBYPL;
            $BYPLapprovedData[] = $dataPointBYPL->approved_count;
            $BYPLrejectedData[] = $dataPointBYPL->rejected_count;
            $BYPLpendingData[] = $dataPointBYPL->pending_count;
             // dd( $dataPointBYPL->rejected_count);
        }
                return view("admin.dashboard", compact("approvedAmount", "rejectedAmount", "pendingAmount", "totalAmount","company", "company_id" , "nv_sm_data", "totalNV", "approvedNV", "rejectedNV", "pendingNV" , "hodApproval", "cpmgApproval", "btApproval", "ceoApproval"             ,'BRPLlabels','BRPLapprovedData','BRPLrejectedData','BRPLpendingData','BYPLlabels','BYPLapprovedData','BYPLrejectedData','BYPLpendingData','cesApproval','ceonominee1Approval','ceonominee2Approval','pendingAmountBYPL','pendingAmountBRPL','approvedAmountBYPL','approvedAmountBRPL','rejectedAmountBYPL','rejectedAmountBRPL','groupcioApproval'));
            } elseif ($id5->work_rew4 == $user->id) {
                $Values = [$user->id, $id5->work_rew1, $id5->work_rew2, $id5->work_rew3];
                $nvid = [];
                $nvstatus = Nvsericestatus::get();
                foreach ($nvstatus as $nvstatus) {
                    array_push($nvid, $nvstatus["nv_id"]);
                }
                $user_nv = NeedValidation::with("division", "service")
                    ->whereIn("id", $nvid)
                    ->orderBy("id", "desc")
                    ->first();
                    if(!empty( $user_nv)){
                $employees = Employee::where("user_id", $user_nv->user_id)->first();
                $department = Department::where("id", $employees->department_id)->first();
                $group_cio = $department->group_cio;}
              $totalId = NeedValidation::pluck('id');
                // dd($totalId);
                $totalAmount = DB::table('tbl_material')
                ->join('nvservicestatus', 'tbl_material.id', '=', 'nvservicestatus.material_id')
                ->whereIn("tbl_material.nv_id", $totalId)
                ->where('nvservicestatus.work_rew4dep5_status', 1)
                ->sum('tbl_material.total_budget_both') + DB::table('tbl_service')
                ->join('nvservicestatus', 'tbl_service.id', '=', 'nvservicestatus.service_id')
                ->whereIn("nv_id", $totalId)
                ->where('nvservicestatus.work_rew4dep5_status', 1)
                ->sum('tbl_service.total_buget');
                // dd($totalAmount);
        
                $pendingAmount = DB::table('tbl_material')
                ->join('nvservicestatus', 'tbl_material.id', '=', 'nvservicestatus.material_id')
                ->whereIn("tbl_material.nv_id", $totalId)
                ->where('nvservicestatus.work_rew4dep5_status', 0)
                ->where('nvservicestatus.work_rew1dep5_status', 1)
                ->sum('tbl_material.total_budget_both') + DB::table('tbl_service')
                ->join('nvservicestatus', 'tbl_service.id', '=', 'nvservicestatus.service_id')
                ->whereIn("nv_id", $totalId)
                ->where('nvservicestatus.work_rew4dep5_status', 0)
                ->where('nvservicestatus.work_rew1dep5_status', 1)
                ->sum('tbl_service.total_buget');
        
                $rejectedAmount = DB::table('tbl_material')
                ->join('nvservicestatus', 'tbl_material.id', '=', 'nvservicestatus.material_id')
                ->whereIn('nvservicestatus.nv_id', $latestData->pluck('nv_id'))
                ->where(function ($query) {
                    $query->where('nvservicestatus.work_rew4dep5_status', 2);
                })
                ->sum('tbl_material.total_budget_both') + DB::table('tbl_service')
                ->join('nvservicestatus', 'tbl_service.id', '=', 'nvservicestatus.service_id')
                ->whereIn('nvservicestatus.nv_id', $latestData->pluck('nv_id'))
                ->where(function ($query) {
                    $query->where('nvservicestatus.work_rew4dep5_status', 2);
                })
                ->sum('tbl_service.total_buget');
           
        
                $approvedAmount = DB::table('tbl_material')
              
                ->join('nvservicestatus', 'tbl_material.id', '=', 'nvservicestatus.material_id')
                ->whereIn("tbl_material.nv_id", $totalId)
                ->where('nvservicestatus.ceo_status', 1)
                ->sum('tbl_material.total_budget_both') +  DB::table('tbl_service')
              
                ->join('nvservicestatus', 'tbl_service.id', '=', 'nvservicestatus.service_id')
                ->whereIn("nv_id", $totalId)
                ->where('nvservicestatus.ceo_status', 1)
                ->sum('tbl_service.total_buget');
  

                if($company_id){
                    $totalNV = Nvsericestatus::whereIn("nv_id", $totalId)->where('work_rew4dep5_status', 1)->where('company_id',$company_id)->count();
                } else {
                    $totalNV = Nvsericestatus::whereIn("nv_id", $totalId)->where('work_rew4dep5_status', 1)->count();
                }
                if($company_id){
                    $rejectedNV = Nvsericestatus::whereIn("nv_id", $totalId)->where('work_rew4dep5_status', 2)->where('company_id',$company_id)->count();
                } else {
                    $rejectedNV = Nvsericestatus::whereIn("nv_id", $totalId)->where('work_rew4dep5_status', 2)->count();
                }
                // dd($totalId);
                if($company_id){
                $latestData = Nvsericestatus::whereIn("nv_id", $totalId)->where('company_id',$company_id)->get();
            }else {
                $latestData = Nvsericestatus::whereIn("nv_id", $totalId)->get();
            }
                if($company_id){
                    $approvedNV = $latestData->where('ceo_status', 1)->where('company_id',$company_id)->count();
                }else{
                    $approvedNV = $latestData->where('ceo_status', 1)->count();
                }
                // $rejectedNV = $latestData->filter(function ($data) {
                //     return in_array($data->ceo_status, [2]) || 
                //     $data->work_rew1_status == 2 ;
                //     // $data->hod_status == 2 || 
                //     // $data->cpmg_status == 2 || 
                //     // $data->ces_status == 2 || 
                //     // $data->cto_status == 2 || 
                //     // $data->ceo_nominee_status == 2 || 
                //     // $data->ceo_nominee2_status == 2 || 
                //     // $data->ceo_nominee2_status == 2;
                // })->count();
                $pendingNV = $latestData->filter(function ($data) {
                    return in_array($data->approverdep4_status, [ 1]) &&
                    in_array($data->work_rew1dep5_status, [1])&&
                    in_array($data->work_rew4dep5_status, [0]);
                    
                  
                   
                   
                    // in_array($data->ces_status, [0]) &&
                    // in_array($data->cto_status, [0]) &&
                    // in_array($data->ceo_nominee_status, [0]) &&
                    // in_array($data->ceo_status, [0]);
                    })->count();
                 

                $hodApproval = Nvsericestatus::where("hod_status", 0)->count();
                $cesApproval = Nvsericestatus::where("hod_status", 1)->where("ces_status", 0)->where("derc_info", 1)->count();
                $cpmg = Nvsericestatus::where('hod_status', 1)->where('cpmg_status', 0)->get();
                $Pendingcpmg = $cpmg->filter(function ($data) {
                 return in_array($data->hod_status, [1]) &&
                     in_array($data->cpmg_status, [0]) &&
                     (
                         (in_array($data->derc_info, [0]) && in_array($data->ces_status, [0]))
                         ||
                         (in_array($data->derc_info, [1]) && in_array($data->ces_status, [1]))
                     );
             });
             $cpmgApproval = $Pendingcpmg->count(); 

                $btApproval = Nvsericestatus::where("cpmg_status", 1)->where("check_technology", 1)->where('cto_status', 0)->count();
              
                $ceonominee1 = Nvsericestatus::where('cpmg_status', 1)->where('ceo_nominee_status', 0)->get();
                 $Pendingnominee1 = $ceonominee1->filter(function ($data) {
                 return in_array($data->cpmg_status, [1]) &&
                     in_array($data->ceo_nominee_status, [0]) &&
                     (
                         (in_array($data->check_technology, [0]) && in_array($data->cto_status, [0]))
                         ||
                         (in_array($data->check_technology, [1]) && in_array($data->cto_status, [1]))
                     );
             });
             $ceonominee1Approval = $Pendingnominee1->count(); 
             if(!empty( $group_cio)){
                $ceonominee2Approval = Nvsericestatus::where('groupcio_status', 1)->where('ceo_nominee2_status', 0)->whereIn("nv_id", $totalId)->count();
                }else{
                    $ceonominee2Approval = Nvsericestatus::where('ceo_nominee_status', 1)->where('ceo_nominee2_status', 0)->whereIn("nv_id", $totalId)->count();
     
                }
            if(!empty( $group_cio)){
                $groupcioApproval = Nvsericestatus::where('ceo_nominee_status', 1)->where('groupcio_status', 0)->where('ceo_nominee2_status', 0)->whereIn("nv_id", $totalId)->count();
                }else{
                    $groupcioApproval =0;  
                }
    
                    // if(!empty( $group_cio)){
                    // $ceoApproval = Nvsericestatus::where('groupcio_status', 1)->where('ceo_status', 0)->whereIn("nv_id", $totalId)->count();
                    // }else{
                    $ceoApproval = Nvsericestatus::where('ceo_nominee2_status', 1)->where('ceo_status', 0)->whereIn("nv_id", $totalId)->count();
                    // }

            
                $nvIds = NeedValidation::where("user_id", $Values)->pluck("id");
                $nv = NeedValidation::whereHas('service')->select('id')
                // ->whereIn('user_id', $Values)
                // ->orWhereIn('user_id',$allNormalUsers)
                ->get();
                $nv_ids = $nv->pluck('id');
                // $nv_sm_data = Nvsericestatus::whereIn('nv_id', $nv_ids)
                // ->with(['service', 'material', 'user'])
                // ->where('hod_status',1)
                // ->orderBy('id', 'desc')
                // ->get();
                if($company_id){
                    $nv_sm_data = Nvsericestatus::whereIn('nv_id', $nv_ids)->with(['service', 'material', 'user'])
                ->where('ceo_nominee2_status',1)->where('company_id',$company_id)
                ->orderBy('id', 'desc');
                } else {
                    $nv_sm_data = Nvsericestatus::whereIn('nv_id', $nv_ids)->with(['service', 'material', 'user'])
                ->where('ceo_nominee2_status',1)
                ->orderBy('id', 'desc');
                }
                $total31_status = $request->total;
                $pending31_status = $request->pending;
                $reject31_status = $request->rejected;

               
              
               
                if ($ceo_status !== null) {
                    $nv_sm_data = $nv_sm_data->where('ceo_status', $ceo_status);
                } elseif ($pending31_status !== null) {
                    $nv_sm_data = $nv_sm_data->where('work_rew4dep5_status', $pending31_status)
                       
                        ->whereIn('hod_status', [ 1])
                        ->whereIn('approver_status', [ 1])    
                       
                        ->whereIn('cto_status', [0, 1])
                        ->whereIn('approverdep3_status', [ 1])
                        ->whereIn('work_rew1dep4_status', [ 1])
                        ->whereIn('approverdep4_status', [ 1])
                        ->whereIn('work_rew1dep5_status', [ 1])
                        ->whereIn('work_rew2dep5_status', [ 1])
                        ->whereIn('work_rew3dep5_status', [ 1]);
                     
                       

                } elseif ($reject31_status !== null) {
                    $nv_sm_data = $nv_sm_data->where('work_rew4dep5_status', $reject31_status);
                    
                }elseif ($total31_status !== null) {
                    $nv_sm_data = $nv_sm_data->where('work_rew4dep5_status', $total31_status);
                      
                }
                $nv_sm_data = $nv_sm_data->get();

                $BRPLnv = NeedValidation::where('company_id','6')->pluck("id");

                $pendingAmountBRPL = DB::table('tbl_material')
                ->join('nvservicestatus', 'tbl_material.id', '=', 'nvservicestatus.material_id')
                ->whereIn('tbl_material.nv_id', $BRPLnv)->where('nvservicestatus.work_rew4dep5_status', 0)
                ->where('nvservicestatus.work_rew1dep5_status', 1)
                ->sum('tbl_material.total_budget_both')+DB::table('tbl_service')
                ->join('nvservicestatus', 'tbl_service.id', '=', 'nvservicestatus.service_id')
                ->whereIn('tbl_service.nv_id', $BRPLnv)->where('nvservicestatus.work_rew4dep5_status', 0)
                ->where('nvservicestatus.work_rew1dep5_status', 1)
                ->sum('tbl_service.total_buget');
                // dd($pendingAmountBRPL);
        
                $rejectedAmountBRPL = DB::table('tbl_material')
                ->join('nvservicestatus', 'tbl_material.id', '=', 'nvservicestatus.material_id')
                ->whereIn('tbl_material.nv_id', $BRPLnv)
                ->where(function ($query) {
                    $query->where('nvservicestatus.work_rew4dep5_status', 2);
                })
                ->sum('tbl_material.total_budget_both')+DB::table('tbl_service')
                ->join('nvservicestatus', 'tbl_service.id', '=', 'nvservicestatus.service_id')
                ->whereIn('tbl_service.nv_id', $BRPLnv)
                ->where(function ($query) {
                    $query->where('nvservicestatus.work_rew4dep5_status', 2);
                })
                ->sum('tbl_service.total_buget');
           
        
                $approvedAmountBRPL = DB::table('tbl_material')
                ->join('nvservicestatus', 'tbl_material.id', '=', 'nvservicestatus.material_id')
                ->whereIn('tbl_material.nv_id', $BRPLnv)->where('nvservicestatus.ceo_status', 1)
                ->sum('tbl_material.total_budget_both')+DB::table('tbl_service')
                ->join('nvservicestatus', 'tbl_service.id', '=', 'nvservicestatus.service_id')
                ->whereIn('tbl_service.nv_id', $BRPLnv)->where('nvservicestatus.ceo_status', 1)
                ->sum('tbl_service.total_buget');

                $fileDataBRPL =Nvsericestatus::
                select(
                    DB::raw('MONTH(created_at) as month'),
                    DB::raw('SUM(CASE WHEN ceo_status = "1" THEN 1 ELSE 0 END) as approved_count'),
                    DB::raw('SUM(CASE WHEN ( work_rew4dep5_status = "2")  THEN 1 ELSE 0 END ) as rejected_count'),
                    DB::raw('SUM(CASE WHEN (approverdep4_status = "1" AND work_rew1dep5_status = "1" AND work_rew4dep5_status = "0" ) THEN 1 ELSE 0 END) as pending_count'),
                    // DB::raw('SUM(CASE WHEN status = "pending" THEN 1 ELSE 0 END) as rejected_count')
                )-> where('ceo_nominee2_status',1)
                ->whereIn('nv_id', $BRPLnv)->with(['service', 'material', 'user'])
                ->whereYear('created_at', Carbon::now()->year)
                ->groupBy('month')
                ->orderBy('month')
                ->get();
        // dd($fileDataBRPL);
            $BRPLlabels = [];
            $BRPLapprovedData = [];
            $BRPLrejectedData = [];
            $BRPLpendingData = [];
        
            foreach ($fileDataBRPL as $dataPointBRPL) {
                $monthBRPL = Carbon::createFromFormat('!m', $dataPointBRPL->month)->format('F');
        
                $BRPLlabels[] = $monthBRPL;
                $BRPLapprovedData[] = $dataPointBRPL->approved_count;
                $BRPLrejectedData[] = $dataPointBRPL->rejected_count;
                $BRPLpendingData[] = $dataPointBRPL->pending_count;
                 // dd( $dataPoint->rejected_count);
            }
    
            $BYPLnv = NeedValidation::where('company_id','5')->pluck("id");

            $pendingAmountBYPL = DB::table('tbl_material')
            ->join('nvservicestatus', 'tbl_material.id', '=', 'nvservicestatus.material_id')
            ->whereIn('tbl_material.nv_id', $BYPLnv)->where('nvservicestatus.work_rew4dep5_status', 0)
            ->where('nvservicestatus.work_rew1dep5_status', 1)
            ->sum('tbl_material.total_budget_both')+DB::table('tbl_service')
            ->join('nvservicestatus', 'tbl_service.id', '=', 'nvservicestatus.service_id')
            ->whereIn('tbl_service.nv_id', $BYPLnv)->where('nvservicestatus.work_rew4dep5_status', 0)
            ->where('nvservicestatus.work_rew1dep5_status', 1)
            ->sum('tbl_service.total_buget');
            //dd($pendingAmountBYPL);
    
            $rejectedAmountBYPL = DB::table('tbl_material')
            ->join('nvservicestatus', 'tbl_material.id', '=', 'nvservicestatus.material_id')
            ->whereIn('tbl_material.nv_id', $BYPLnv)
            ->where(function ($query) {
                $query->where('nvservicestatus.work_rew4dep5_status', 2);
            })
            ->sum('tbl_material.total_budget_both')+DB::table('tbl_service')
            ->join('nvservicestatus', 'tbl_service.id', '=', 'nvservicestatus.service_id')
            ->whereIn('tbl_service.nv_id', $BYPLnv)
            ->where(function ($query) {
                $query->where('nvservicestatus.work_rew4dep5_status', 2);
            })
            ->sum('tbl_service.total_buget');
       
    
            $approvedAmountBYPL = DB::table('tbl_material')
            ->join('nvservicestatus', 'tbl_material.id', '=', 'nvservicestatus.material_id')
            ->whereIn('tbl_material.nv_id', $BYPLnv)->where('nvservicestatus.ceo_status', 1)
            ->sum('tbl_material.total_budget_both')+DB::table('tbl_service')
            ->join('nvservicestatus', 'tbl_service.id', '=', 'nvservicestatus.service_id')
            ->whereIn('tbl_service.nv_id', $BYPLnv)->where('nvservicestatus.ceo_status', 1)
            ->sum('tbl_service.total_buget');
            // dd( $BYPLnv );
             $fileDataBYPL =Nvsericestatus::
            select(
                DB::raw('MONTH(created_at) as month'),
                DB::raw('SUM(CASE WHEN ceo_status = "1" THEN 1 ELSE 0 END) as approved_count'),
                DB::raw('SUM(CASE WHEN ( work_rew4dep5_status = "2")  THEN 1 ELSE 0 END) as rejected_count'),
                DB::raw('SUM(CASE WHEN (approverdep4_status = "1" AND work_rew1dep5_status = "1" AND work_rew4dep5_status = "0" )  THEN 1 ELSE 0 END) as pending_count'),
                // DB::raw('SUM(CASE WHEN status = "pending" THEN 1 ELSE 0 END) as rejected_count')
            ) 
            ->whereIn('nv_id', $BYPLnv)->with(['service', 'material', 'user'])
            -> where('ceo_nominee2_status',1)
            ->whereYear('created_at', Carbon::now()->year)
            ->groupBy('month')
            ->orderBy('month')
            ->get();
            // dd($fileDataBYPL);
        $BYPLlabels = [];
        $BYPLapprovedData = [];
        $BYPLrejectedData = [];
        $BYPLpendingData = [];
    
        foreach ($fileDataBYPL as $dataPointBYPL) {
            $monthBYPL = Carbon::createFromFormat('!m', $dataPointBYPL->month)->format('F');
    
            $BYPLlabels[] = $monthBYPL;
            $BYPLapprovedData[] = $dataPointBYPL->approved_count;
            $BYPLrejectedData[] = $dataPointBYPL->rejected_count;
            $BYPLpendingData[] = $dataPointBYPL->pending_count;
             // dd( $dataPointBYPL->rejected_count);
        }
                return view("admin.dashboard", compact("approvedAmount", "rejectedAmount", "pendingAmount", "totalAmount","company", "company_id" , "nv_sm_data", "totalNV", "approvedNV", "rejectedNV", "pendingNV" , "hodApproval", "cpmgApproval", "btApproval", "ceoApproval"             ,'BRPLlabels','BRPLapprovedData','BRPLrejectedData','BRPLpendingData','BYPLlabels','BYPLapprovedData','BYPLrejectedData','BYPLpendingData','cesApproval','ceonominee1Approval','ceonominee2Approval','pendingAmountBYPL','pendingAmountBRPL','rejectedAmountBYPL','rejectedAmountBRPL','approvedAmountBYPL','approvedAmountBRPL','groupcioApproval'));
            
            } elseif ($id5->approver == $user->id) {
                $Values = [$user->id, $id5->work_rew1, $id5->work_rew2, $id5->work_rew3, $id5->work_rew4];
                $nvid = [];
                $nvstatus = Nvsericestatus::get();
                foreach ($nvstatus as $nvstatus) {
                    array_push($nvid, $nvstatus["nv_id"]);
                }
                $user_nv = NeedValidation::with("division", "service")
                    ->whereIn("id", $nvid)
                    ->orderBy("id", "desc")
                    ->first();
                    if(!empty( $user_nv)){
                $employees = Employee::where("user_id", $user_nv->user_id)->first();
                $department = Department::where("id", $employees->department_id)->first();
                $group_cio = $department->group_cio;}
                 if($fiscal_year) {
                    $totalId = NeedValidation::where('fiscal_year', $fiscal_year)->pluck('id');

                 }else{
                    $totalId = NeedValidation::where('fiscal_year', $currentFinancialYear)->pluck('id');

                 }

                 if($company_id){
                    $latestData = Nvsericestatus::whereIn("nv_id", $totalId)->where('company_id',$company_id)->get();
                }else {
                    $latestData = Nvsericestatus::whereIn("nv_id", $totalId)->get();
                }
                // dd($totalId);
                $totalAmount = DB::table('tbl_material')
                ->join('nvservicestatus', 'tbl_material.id', '=', 'nvservicestatus.material_id')
                ->whereIn('tbl_material.nv_id', $totalId)
                ->where('nvservicestatus.ceo_status', 1)
                ->sum('tbl_material.total_budget_both') + DB::table('tbl_service')
                ->join('nvservicestatus', 'tbl_service.id', '=', 'nvservicestatus.service_id')
                ->whereIn('tbl_service.nv_id', $totalId)
                ->where('nvservicestatus.ceo_status', 1)
                ->sum('tbl_service.total_buget');
                // dd($totalAmount);
                // if(!empty( $group_cio)){
                //     $pen_amt = $latestData->filter(function ($data) {
                //         return in_array($data->groupcio_status, [1]) &&
                //             in_array($data->approverdep5_status, [0]);
                //     });
                // }else{
                    $pen_amt = $latestData->filter(function ($data) {
                        return in_array($data->approverdep4_status, [1]) &&
                            in_array($data->approverdep5_status, [0]);
                    });
                    // }
                
                $pendingAmount = DB::table('tbl_material')
                ->join('nvservicestatus', 'tbl_material.id', '=', 'nvservicestatus.material_id')
                 ->whereIn('tbl_material.nv_id', $totalId)
                 ->whereIn('nvservicestatus.nv_id', $pen_amt->pluck('nv_id'))
                //  ->where('nvservicestatus.ceo_status', 0)
                //  ->where('nvservicestatus.ceo_nominee2_status', 1)
                ->sum('tbl_material.total_budget_both') + DB::table('tbl_service')
                ->join('nvservicestatus', 'tbl_service.id', '=', 'nvservicestatus.service_id')
                 ->whereIn('tbl_service.nv_id', $totalId)
                 ->whereIn('nvservicestatus.nv_id', $pen_amt->pluck('nv_id'))
                //  ->where('nvservicestatus.ceo_status', 0)
                //  ->where('nvservicestatus.ceo_nominee2_status', 1)
                ->sum('tbl_service.total_buget');
        
                $rejectedAmount = DB::table('tbl_material')
                ->join('nvservicestatus', 'tbl_material.id', '=', 'nvservicestatus.material_id')
                ->whereIn('nvservicestatus.nv_id', $latestData->pluck('nv_id'))
                ->where(function ($query) {
                    $query->orWhere('nvservicestatus.ceo_status', 2);
                })
                ->sum('tbl_material.total_budget_both') + DB::table('tbl_service')
                ->join('nvservicestatus', 'tbl_service.id', '=', 'nvservicestatus.service_id')
                ->whereIn('nvservicestatus.nv_id', $latestData->pluck('nv_id'))
                ->where(function ($query) {
                    $query->orWhere('nvservicestatus.ceo_status', 2);
                })
                ->sum('tbl_service.total_buget');
           
        
                $approvedAmount = DB::table('tbl_material')
                ->join('nvservicestatus', 'tbl_material.id', '=', 'nvservicestatus.material_id')
                ->whereIn('tbl_material.nv_id', $totalId)
                ->where('nvservicestatus.ceo_status', 1)
                ->sum('tbl_material.total_budget_both') + DB::table('tbl_service')
                ->join('nvservicestatus', 'tbl_service.id', '=', 'nvservicestatus.service_id')
                ->whereIn('tbl_service.nv_id', $totalId)
                ->where('nvservicestatus.ceo_status', 1)
                ->sum('tbl_service.total_buget');


                if($company_id){
                    $totalNV = Nvsericestatus::whereIn("nv_id", $totalId)->where('approverdep5_status', 1)->where('company_id',$company_id)->count();
                } else {
                    $totalNV = Nvsericestatus::whereIn("nv_id", $totalId)->where('approverdep5_status', 1)->count();
                }
                if($company_id){
                    $rejectedNV = Nvsericestatus::whereIn("nv_id", $totalId)->where('approverdep5_status', 2)->where('company_id',$company_id)->count();
                } else {
                    $rejectedNV = Nvsericestatus::whereIn("nv_id", $totalId)->where('approverdep5_status', 2)->count();
                }
                // dd($totalId);
           
                if($company_id){
                    $approvedNV = $latestData->where('ceo_status', 1)->where('company_id',$company_id)->count();
                }else{
                    $approvedNV = $latestData->where('ceo_status', 1)->count();
                }
                // $rejectedNV = $latestData->filter(function ($data) {
                //     return in_array($data->ceo_status, [2]) || 
                //     $data->work_rew1_status == 2 ;
                //     // $data->hod_status == 2 || 
                //     // $data->cpmg_status == 2 || 
                //     // $data->ces_status == 2 || 
                //     // $data->cto_status == 2 || 
                //     // $data->ceo_nominee_status == 2 || 
                //     // $data->ceo_nominee2_status == 2 || 
                //     // $data->ceo_nominee2_status == 2;
                // })->count();
                // $pendingNV = $latestData->filter(function ($data) {
                //     return in_array($data->approverdep4_status, [ 1]) &&
                //     in_array($data->work_rew1dep5_status, [1])&&
                //     in_array($data->approverdep5_status, [0]);
                    
                //     })->count();
                // if(!empty( $group_cio)){
                 
                //     $pendingNV = $latestData->filter(function ($data) {
                //         return in_array($data->groupcio_status, [1]) &&
                //             in_array($data->approverdep5_status, [0]);
                //     })->count();
                   
                // }else{
                    $pendingNV = $latestData->filter(function ($data) {
                        return in_array($data->approverdep4_status, [1]) &&
                            in_array($data->approverdep5_status, [0]);
                    })->count();
            
                    // }
                    // $pendingNV = $latestData->filter(function ($data) {
                    //     return in_array($data->approverdep4_status, [1]) &&
                    //         in_array($data->approverdep5_status, [0]);
                    // })->count();
                 
                
              $hodApproval = Nvsericestatus::whereIn("nv_id", $totalId)->where("hod_status", 0)->count();
                $cesApproval = Nvsericestatus::whereIn("nv_id", $totalId)->where("hod_status", 1)->where("ces_status", 0)->where("derc_info", 1)->count();
                $cpmg = Nvsericestatus::whereIn("nv_id", $totalId)->where('hod_status', 1)->where('cpmg_status', 0)->get();
                $Pendingcpmg = $cpmg->filter(function ($data) {
                 return in_array($data->hod_status, [1]) &&
                     in_array($data->cpmg_status, [0]) &&
                     (
                         (in_array($data->derc_info, [0]) && in_array($data->ces_status, [0]))
                         ||
                         (in_array($data->derc_info, [1]) && in_array($data->ces_status, [1]))
                     );
             });
             $cpmgApproval = $Pendingcpmg->count(); 

                $btApproval = Nvsericestatus::whereIn("nv_id", $totalId)->where("cpmg_status", 1)->where("check_technology", 1)->where('cto_status', 0)->count();
              
                $ceonominee1 = Nvsericestatus::whereIn("nv_id", $totalId)->where('cpmg_status', 1)->where('ceo_nominee_status', 0)->get();
                 $Pendingnominee1 = $ceonominee1->filter(function ($data) {
                 return in_array($data->cpmg_status, [1]) &&
                     in_array($data->ceo_nominee_status, [0]) &&
                     (
                         (in_array($data->check_technology, [0]) && in_array($data->cto_status, [0]))
                         ||
                         (in_array($data->check_technology, [1]) && in_array($data->cto_status, [1]))
                     );
             });
             $ceonominee1Approval = $Pendingnominee1->count(); 
                // $cpmgApproval = Nvsericestatus::whereIn("nv_id", $totalId)->where(function ($query) {
                //     $query->whereIn('ces_status', [0,1]);
                // })
                //     ->Where(function ($query) {
                //         $query->where('hod_status', 1);
                //     })
                //     ->where(function ($query) {
                //         $query->where('derc_info', 0);
                //     })
                //     ->where(function ($query) {
                //         $query->where('cpmg_status', 0);
                //     })
                //        ->count();
          
                // $btApproval = Nvsericestatus::whereIn("nv_id", $totalId)->where("cpmg_status", 1)->where("check_technology", 1)->where('cto_status', 0)->count();
                // $ceonominee1Approval = Nvsericestatus::whereIn("nv_id", $totalId)->where("cpmg_status", 1)->whereIn('cto_status', [0,1])->whereIn("check_technology", [0,1])->where('ceo_nominee_status', 0)->count();
                if(!empty( $group_cio)){
                    $ceonominee2Approval = Nvsericestatus::where('groupcio_status', 1)->where('ceo_nominee2_status', 0)->whereIn("nv_id", $totalId)->count();
                    }else{
                        $ceonominee2Approval = Nvsericestatus::where('ceo_nominee_status', 1)->where('ceo_nominee2_status', 0)->whereIn("nv_id", $totalId)->count();
         
                    }
                if(!empty( $group_cio)){
                    $groupcioApproval = Nvsericestatus::where('ceo_nominee_status', 1)->where('groupcio_status', 0)->where('ceo_nominee2_status', 0)->whereIn("nv_id", $totalId)->count();
                    }else{
                        $groupcioApproval =0;  
                    }
        
                        // if(!empty( $group_cio)){
                        // $ceoApproval = Nvsericestatus::where('groupcio_status', 1)->where('ceo_status', 0)->whereIn("nv_id", $totalId)->count();
                        // }else{
                        $ceoApproval = Nvsericestatus::where('ceo_nominee2_status', 1)->where('ceo_status', 0)->whereIn("nv_id", $totalId)->count();
                        // }
              

              
                $nvIds = NeedValidation::where("user_id", $Values)->pluck("id");
                if($fiscal_year) {
                    $nv = NeedValidation::whereHas('service')->select('id')->where('fiscal_year', $fiscal_year)
                    // ->whereIn('user_id', $Values)
                    // ->orWhereIn('user_id',$allNormalUsers)
                    ->get();

                 }else{
                    $nv = NeedValidation::whereHas('service')->select('id')->where('fiscal_year', $currentFinancialYear)
                    // ->whereIn('user_id', $Values)
                    // ->orWhereIn('user_id',$allNormalUsers)
                    ->get();

                 }
             
                $nv_ids = $nv->pluck('id');
                // $nv_sm_data = Nvsericestatus::whereIn('nv_id', $nv_ids)
                // ->with(['service', 'material', 'user'])
                // ->where('hod_status',1)
                // ->orderBy('id', 'desc')
                // ->get();
                // if(!empty( $group_cio)){
                //     $nv_sm_data = Nvsericestatus::whereIn('nv_id', $nv_ids)->with(['service', 'material', 'user'])
                //     ->where('groupcio_status', 1)
                //     ->orderBy('id', 'asc')->get();
                // }else{
                    $nv_sm_data = Nvsericestatus::whereIn('nv_id', $nv_ids)->with(['service', 'material', 'user'])
                    ->where('approverdep4_status', 1)
                    ->orderBy('id', 'asc')->get();
                // }
              
                $total32_status = $request->total;
                $pending32_status = $request->pending;
                $reject32_status = $request->rejected;

                if ($ceo_status !== null) {
                    $nv_sm_data = $nv_sm_data->where('ceo_status', $ceo_status);
                } elseif ($pending32_status !== null) {
                    $nv_sm_data = $nv_sm_data->where('ceo_status', $pending32_status);
                      
                } elseif ($reject32_status !== null) {
                    $nv_sm_data = $nv_sm_data->where('ceo_status', $reject32_status);
                    
                }elseif ($total32_status !== null) {
                    $nv_sm_data = $nv_sm_data->where('ceo_status', $total32_status);
                      
                }
                if($fiscal_year) {
                    $BRPLnv = NeedValidation::where('company_id','6')->where('fiscal_year', $fiscal_year)->pluck("id");


                 }else{
                    $BRPLnv = NeedValidation::where('company_id','6')->where('fiscal_year', $currentFinancialYear)->pluck("id");


                 }

                $pendingAmountBRPL = DB::table('tbl_material')
                ->join('nvservicestatus', 'tbl_material.id', '=', 'nvservicestatus.material_id')
                ->whereIn('tbl_material.nv_id', $BRPLnv)
                ->whereIn('nvservicestatus.nv_id', $pen_amt->pluck('nv_id'))
                // ->where('nvservicestatus.ceo_status', 0)
                // ->where('nvservicestatus.ceo_nominee2_status', 1)
                ->sum('tbl_material.total_budget_both')+DB::table('tbl_service')
                ->join('nvservicestatus', 'tbl_service.id', '=', 'nvservicestatus.service_id')
                ->whereIn('tbl_service.nv_id', $BRPLnv)
                ->whereIn('nvservicestatus.nv_id', $pen_amt->pluck('nv_id'))
                // ->where('nvservicestatus.ceo_status', 0)
                // ->where('nvservicestatus.ceo_nominee2_status', 1)
                ->sum('tbl_service.total_buget');
                // dd($pendingAmountBRPL);
        
                $rejectedAmountBRPL = DB::table('tbl_material')
                ->join('nvservicestatus', 'tbl_material.id', '=', 'nvservicestatus.material_id')
                ->whereIn('tbl_material.nv_id', $BRPLnv)
                ->where(function ($query) {
                    $query->where('nvservicestatus.ceo_status', 2);
                })
                ->sum('tbl_material.total_budget_both')+DB::table('tbl_service')
                ->join('nvservicestatus', 'tbl_service.id', '=', 'nvservicestatus.service_id')
                ->whereIn('tbl_service.nv_id', $BRPLnv)
                ->where(function ($query) {
                    $query->where('nvservicestatus.ceo_status', 2);
                })
                ->sum('tbl_service.total_buget');
           
        
                $approvedAmountBRPL = DB::table('tbl_material')
                ->join('nvservicestatus', 'tbl_material.id', '=', 'nvservicestatus.material_id')
                ->whereIn('tbl_material.nv_id', $BRPLnv)->where('nvservicestatus.ceo_status', 1)
                ->sum('tbl_material.total_budget_both')+DB::table('tbl_service')
                ->join('nvservicestatus', 'tbl_service.id', '=', 'nvservicestatus.service_id')
                ->whereIn('tbl_service.nv_id', $BRPLnv)->where('nvservicestatus.ceo_status', 1)
                ->sum('tbl_service.total_buget');
                // if(!empty( $group_cio)){
                //     $fileDataBRPL =Nvsericestatus::
                //     select(
                //         DB::raw('MONTH(created_at) as month'),
                //         DB::raw('SUM(CASE WHEN (ceo_status = "1") THEN 1 ELSE 0 END) as approved_count'),
                //         DB::raw('SUM(CASE WHEN ( ceo_status = "2")  THEN 1 ELSE 0 END ) as rejected_count'),
                //         DB::raw('SUM(CASE WHEN (approverdep5_status = "0" ) THEN 1 ELSE 0 END) as pending_count'),
                //         // DB::raw('SUM(CASE WHEN status = "pending" THEN 1 ELSE 0 END) as rejected_count')
                //     )
                //     ->whereIn('nv_id', $BRPLnv)->with(['service', 'material', 'user'])
                //     ->where('groupcio_status',1)
                //     ->whereYear('created_at', Carbon::now()->year)
                //     ->groupBy('month')
                //     ->orderBy('month')
                //     ->get();

                // }else{
                    $fileDataBRPL =Nvsericestatus::
                    select(
                        DB::raw('MONTH(created_at) as month'),
                        DB::raw('SUM(CASE WHEN (ceo_status = "1") THEN 1 ELSE 0 END) as approved_count'),
                        DB::raw('SUM(CASE WHEN ( ceo_status = "2")  THEN 1 ELSE 0 END ) as rejected_count'),
                        DB::raw('SUM(CASE WHEN (approverdep5_status = "0" ) THEN 1 ELSE 0 END) as pending_count'),
                        // DB::raw('SUM(CASE WHEN status = "pending" THEN 1 ELSE 0 END) as rejected_count')
                    )
                    ->whereIn('nv_id', $BRPLnv)->with(['service', 'material', 'user'])
                    ->where('approverdep4_status',1)
                    ->whereYear('created_at', Carbon::now()->year)
                    ->groupBy('month')
                    ->orderBy('month')
                    ->get();

                // }
               
        // dd($fileDataBRPL);
            $BRPLlabels = [];
            $BRPLapprovedData = [];
            $BRPLrejectedData = [];
            $BRPLpendingData = [];
        
            foreach ($fileDataBRPL as $dataPointBRPL) {
                $monthBRPL = Carbon::createFromFormat('!m', $dataPointBRPL->month)->format('F');
        
                $BRPLlabels[] = $monthBRPL;
                $BRPLapprovedData[] = $dataPointBRPL->approved_count;
                $BRPLrejectedData[] = $dataPointBRPL->rejected_count;
                $BRPLpendingData[] = $dataPointBRPL->pending_count;
                 // dd( $dataPoint->rejected_count);
            }
            if($fiscal_year) {
                $BYPLnv = NeedValidation::where('company_id','5')->where('fiscal_year', $fiscal_year)->pluck("id");



             }else{
                $BYPLnv = NeedValidation::where('company_id','5')->where('fiscal_year', $currentFinancialYear)->pluck("id");



             }
            $pendingAmountBYPL = DB::table('tbl_material')
            ->join('nvservicestatus', 'tbl_material.id', '=', 'nvservicestatus.material_id')
            ->whereIn('tbl_material.nv_id', $BYPLnv)
            ->whereIn('nvservicestatus.nv_id', $pen_amt->pluck('nv_id'))
            // ->where('nvservicestatus.ceo_status', 0)
            // ->where('nvservicestatus.ceo_nominee2_status', 1)
            ->sum('tbl_material.total_budget_both')+DB::table('tbl_service')
            ->join('nvservicestatus', 'tbl_service.id', '=', 'nvservicestatus.service_id')
            ->whereIn('tbl_service.nv_id', $BYPLnv)
            ->whereIn('nvservicestatus.nv_id', $pen_amt->pluck('nv_id'))
            // ->where('nvservicestatus.ceo_status', 0)
            // ->where('nvservicestatus.ceo_nominee2_status', 1)
            ->sum('tbl_service.total_buget');
            // dd($pendingAmountBYPL);
    
            $rejectedAmountBYPL = DB::table('tbl_material')
            ->join('nvservicestatus', 'tbl_material.id', '=', 'nvservicestatus.material_id')
            ->whereIn('tbl_material.nv_id', $BYPLnv)
            ->where(function ($query) {
                $query->where('nvservicestatus.ceo_status', 2);
            })
            ->sum('tbl_material.total_budget_both')+DB::table('tbl_service')
            ->join('nvservicestatus', 'tbl_service.id', '=', 'nvservicestatus.service_id')
            ->whereIn('tbl_service.nv_id', $BYPLnv)
            ->where(function ($query) {
                $query->where('nvservicestatus.ceo_status', 2);
            })
            ->sum('tbl_service.total_buget');
       
    
            $approvedAmountBYPL = DB::table('tbl_material')
            ->join('nvservicestatus', 'tbl_material.id', '=', 'nvservicestatus.material_id')
            ->whereIn('tbl_material.nv_id', $BYPLnv)->where('nvservicestatus.ceo_status', 1)
            ->sum('tbl_material.total_budget_both')+DB::table('tbl_service')
            ->join('nvservicestatus', 'tbl_service.id', '=', 'nvservicestatus.service_id')
            ->whereIn('tbl_service.nv_id', $BYPLnv)->where('nvservicestatus.ceo_status', 1)
            ->sum('tbl_service.total_buget');
            // if(!empty( $group_cio)){
            //     $fileDataBYPL =Nvsericestatus::
            //     select(
            //         DB::raw('MONTH(created_at) as month'),
            //         DB::raw('SUM(CASE WHEN (ceo_status = "1") THEN 1 ELSE 0 END) as approved_count'),
            //         DB::raw('SUM(CASE WHEN (ceo_status = "2") THEN 1 ELSE 0 END) as rejected_count'),
            //         DB::raw('SUM(CASE WHEN (approverdep5_status = "0") THEN 1 ELSE 0 END) as pending_count'),
            //         // DB::raw('SUM(CASE WHEN status = "pending" THEN 1 ELSE 0 END) as rejected_count')
            //     ) 
            //     ->whereIn('nv_id', $BYPLnv)->with(['service', 'material', 'user'])
            //     ->where('groupcio_status',1)
            //     ->whereYear('created_at', Carbon::now()->year)
            //     ->groupBy('month')
            //     ->orderBy('month')
            //     ->get();
            // }else{
                $fileDataBYPL =Nvsericestatus::
                select(
                    DB::raw('MONTH(created_at) as month'),
                    DB::raw('SUM(CASE WHEN (ceo_status = "1") THEN 1 ELSE 0 END) as approved_count'),
                    DB::raw('SUM(CASE WHEN (ceo_status = "2") THEN 1 ELSE 0 END) as rejected_count'),
                    DB::raw('SUM(CASE WHEN (approverdep5_status = "0") THEN 1 ELSE 0 END) as pending_count'),
                    // DB::raw('SUM(CASE WHEN status = "pending" THEN 1 ELSE 0 END) as rejected_count')
                ) 
                ->whereIn('nv_id', $BYPLnv)->with(['service', 'material', 'user'])
                ->where('approverdep4_status',1)
                ->whereYear('created_at', Carbon::now()->year)
                ->groupBy('month')
                ->orderBy('month')
                ->get();
            // }
          
            // dd($fileDataBYPL);
        $BYPLlabels = [];
        $BYPLapprovedData = [];
        $BYPLrejectedData = [];
        $BYPLpendingData = [];
    
        foreach ($fileDataBYPL as $dataPointBYPL) {
            $monthBYPL = Carbon::createFromFormat('!m', $dataPointBYPL->month)->format('F');
    
            $BYPLlabels[] = $monthBYPL;
            $BYPLapprovedData[] = $dataPointBYPL->approved_count;
            $BYPLrejectedData[] = $dataPointBYPL->rejected_count;
            $BYPLpendingData[] = $dataPointBYPL->pending_count;
                      // dd( $dataPointBYPL->rejected_count);
        }
                return view("admin.dashboard", compact("approvedAmount","currentFinancialYear","nextFinancialYear" ,"nextToNextFinancialYear", "rejectedAmount", "pendingAmount", "totalAmount","company", "company_id" , "nv_sm_data", "totalNV", "approvedNV", "rejectedNV", "pendingNV" , "hodApproval", "cpmgApproval", "btApproval", "ceoApproval"             ,'BRPLlabels','BRPLapprovedData','BRPLrejectedData','BRPLpendingData','BYPLlabels','BYPLapprovedData','BYPLrejectedData','BYPLpendingData','cesApproval','ceonominee1Approval','ceonominee2Approval','approvedAmountBYPL','rejectedAmountBYPL','pendingAmountBYPL','approvedAmountBRPL','rejectedAmountBRPL','pendingAmountBRPL','groupcioApproval'));
            } 
            elseif($user->role_id == 9) {
                // dd($user->role_id);
                // $totalNV = NeedValidation::where("user_id", $user->id)->count();
                if($fiscal_year){
                    $totalId = NeedValidation::where("user_id", $user->id)->where('fiscal_year',$fiscal_year)->where("delete_draft", 0)->pluck('id');

                }else{
                    $totalId = NeedValidation::where("user_id", $user->id)->where('fiscal_year',$currentFinancialYear)->where("delete_draft", 0)->pluck('id');

                }
        //    $sumtotal = Nvsericestatus::whereIn('nv_id', $totalId)->where('ceo_status', 1)->get('nv_id');

        //     $totalAmount = NVMaterial::whereIn('nv_id', $sumtotal)->sum('total_budget_both')+
        //     NVService::whereIn('nv_id', $sumtotal)->sum('total_buget');

                $totalAmount = DB::table('tbl_material')
                ->join('nvservicestatus', 'tbl_material.id', '=', 'nvservicestatus.material_id')
               
                ->whereIn('tbl_material.nv_id', $totalId)
                ->whereIn('nvservicestatus.nv_id', $latestData->pluck('nv_id'))

                ->where('nvservicestatus.ceo_status', 1)
                ->sum('tbl_material.total_budget_both')
                +DB::table('tbl_service')
                ->join('nvservicestatus', 'tbl_service.id', '=', 'nvservicestatus.service_id')
               
                ->whereIn('tbl_service.nv_id', $totalId)
                ->whereIn('nvservicestatus.nv_id', $latestData->pluck('nv_id'))


                ->where('nvservicestatus.ceo_status', 1)
                ->sum('tbl_service.total_buget');
                // dd($totalAmount);
       
                $pendingAmount = DB::table('tbl_material')
                ->join('nvservicestatus', 'tbl_material.id', '=', 'nvservicestatus.material_id')
                 // -> whereIn("nv_id", $totalId)
                 ->whereIn('tbl_material.nv_id', $totalId)
                 ->whereIn('nvservicestatus.nv_id', $latestData->pluck('nv_id'))

                ->where('nvservicestatus.ceo_status', 0)
                ->whereIn('nvservicestatus.rv1_status', [0,1])
                ->whereIn('nvservicestatus.rv2_status', [0,1])
                ->whereIn('nvservicestatus.rv3_status', [0,1])
                ->whereIn('nvservicestatus.rv4_status', [0,1])
                ->whereIn('nvservicestatus.hod_status', [0,1])
                ->whereIn('nvservicestatus.ces_rew1_status', [0,1])
                ->whereIn('nvservicestatus.ces_rew2_status', [0,1])
                ->whereIn('nvservicestatus.ces_rew3_status', [0,1])
                ->whereIn('nvservicestatus.ces_rew4_status', [0,1])
                ->whereIn('nvservicestatus.ces_status', [0,1])
                ->whereIn('nvservicestatus.cpmg_status', [0,1])
                ->whereIn('nvservicestatus.work_rew1_status', [0,1])
                ->whereIn('nvservicestatus.work_rew2_status', [0,1])
                ->whereIn('nvservicestatus.work_rew3_status', [0,1])
                ->whereIn('nvservicestatus.work_rew4_status', [0,1])
                ->whereIn('nvservicestatus.cto_status', [0,1])
                ->whereIn('nvservicestatus.work_rew1dep2_status', [0,1])
                ->whereIn('nvservicestatus.work_rew2dep2_status', [0,1])
                ->whereIn('nvservicestatus.work_rew3dep2_status', [0,1])
                ->whereIn('nvservicestatus.work_rew4dep2_status', [0,1])
                ->whereIn('nvservicestatus.ceo_nominee_status', [0,1])
                ->whereIn('nvservicestatus.work_rew1dep3_status', [0,1])
                ->whereIn('nvservicestatus.work_rew2dep3_status', [0,1])
                ->whereIn('nvservicestatus.work_rew3dep3_status', [0,1])
                ->whereIn('nvservicestatus.work_rew4dep3_status', [0,1])
                ->whereIn('nvservicestatus.ceo_nominee2_status', [0,1])
                ->whereIn('nvservicestatus.work_rew1dep4_status', [0,1])
                ->whereIn('nvservicestatus.work_rew2dep4_status', [0,1])
                ->whereIn('nvservicestatus.work_rew3dep4_status', [0,1])
                ->whereIn('nvservicestatus.work_rew4dep4_status', [0,1])
                ->whereIn('nvservicestatus.groupcio_status', [0,1])
                ->sum('tbl_material.total_budget_both') +DB::table('tbl_service')
                ->join('nvservicestatus', 'tbl_service.id', '=', 'nvservicestatus.service_id')
               
                ->whereIn('tbl_service.nv_id', $totalId)
                ->whereIn('nvservicestatus.nv_id', $latestData->pluck('nv_id'))
               
               
                ->where('nvservicestatus.ceo_status', 0)
                ->whereIn('nvservicestatus.rv1_status', [0,1])
                ->whereIn('nvservicestatus.rv2_status', [0,1])
                ->whereIn('nvservicestatus.rv3_status', [0,1])
                ->whereIn('nvservicestatus.rv4_status', [0,1])
                ->whereIn('nvservicestatus.hod_status', [0,1])
                ->whereIn('nvservicestatus.ces_rew1_status', [0,1])
                ->whereIn('nvservicestatus.ces_rew2_status', [0,1])
                ->whereIn('nvservicestatus.ces_rew3_status', [0,1])
                ->whereIn('nvservicestatus.ces_rew4_status', [0,1])
                ->whereIn('nvservicestatus.ces_status', [0,1])
                ->whereIn('nvservicestatus.cpmg_status', [0,1])
                ->whereIn('nvservicestatus.work_rew1_status', [0,1])
                ->whereIn('nvservicestatus.work_rew2_status', [0,1])
                ->whereIn('nvservicestatus.work_rew3_status', [0,1])
                ->whereIn('nvservicestatus.work_rew4_status', [0,1])
                ->whereIn('nvservicestatus.cto_status', [0,1])
                ->whereIn('nvservicestatus.work_rew1dep2_status', [0,1])
                ->whereIn('nvservicestatus.work_rew2dep2_status', [0,1])
                ->whereIn('nvservicestatus.work_rew3dep2_status', [0,1])
                ->whereIn('nvservicestatus.work_rew4dep2_status', [0,1])
                ->whereIn('nvservicestatus.ceo_nominee_status', [0,1])
                ->whereIn('nvservicestatus.work_rew1dep3_status', [0,1])
                ->whereIn('nvservicestatus.work_rew2dep3_status', [0,1])
                ->whereIn('nvservicestatus.work_rew3dep3_status', [0,1])
                ->whereIn('nvservicestatus.work_rew4dep3_status', [0,1])
                ->whereIn('nvservicestatus.ceo_nominee2_status', [0,1])
                ->whereIn('nvservicestatus.work_rew1dep4_status', [0,1])
                ->whereIn('nvservicestatus.work_rew2dep4_status', [0,1])
                ->whereIn('nvservicestatus.work_rew3dep4_status', [0,1])
                ->whereIn('nvservicestatus.work_rew4dep4_status', [0,1])
                ->whereIn('nvservicestatus.groupcio_status', [0,1])
                ->sum('tbl_service.total_buget');
    //    dd($pendingAmount);
                $rejectedAmount = DB::table('tbl_material')
                ->join('nvservicestatus', 'tbl_material.id', '=', 'nvservicestatus.material_id')
                ->whereIn('tbl_material.nv_id', $totalId)
                ->whereIn('nvservicestatus.nv_id', $latestData->pluck('nv_id'))
                ->where(function ($query) {
                    $query->orWhere('nvservicestatus.ceo_status', 2)
                        ->orWhere('nvservicestatus.rv1_status', 2)
                        ->orWhere('nvservicestatus.rv2_status', 2)
                        ->orWhere('nvservicestatus.rv3_status', 2)
                        ->orWhere('nvservicestatus.rv4_status', 2)
                        ->orWhere('nvservicestatus.hod_status', 2)
                        ->orWhere('nvservicestatus.ces_rew1_status', 2)
                        ->orWhere('nvservicestatus.ces_rew2_status', 2)
                        ->orWhere('nvservicestatus.ces_rew3_status', 2)
                        ->orWhere('nvservicestatus.ces_rew4_status', 2)
                        ->orWhere('nvservicestatus.cpmg_status', 2)
                        ->orWhere('nvservicestatus.work_rew1_status', 2)
                        ->orWhere('nvservicestatus.work_rew2_status', 2)
                        ->orWhere('nvservicestatus.work_rew3_status', 2)
                        ->orWhere('nvservicestatus.work_rew4_status', 2)
                        ->orWhere('nvservicestatus.ces_status', 2)
                        ->orWhere('nvservicestatus.work_rew1dep2_status', 2)
                        ->orWhere('nvservicestatus.work_rew2dep2_status', 2)
                        ->orWhere('nvservicestatus.work_rew3dep2_status', 2)
                        ->orWhere('nvservicestatus.work_rew4dep2_status', 2)
                        ->orWhere('nvservicestatus.cto_status', 2)
                        ->orWhere('nvservicestatus.work_rew1dep3_status', 2)
                        ->orWhere('nvservicestatus.work_rew2dep3_status', 2)
                        ->orWhere('nvservicestatus.work_rew3dep3_status', 2)
                        ->orWhere('nvservicestatus.work_rew4dep3_status', 2)
                        ->orWhere('nvservicestatus.ceo_nominee_status', 2)
                        ->orWhere('nvservicestatus.work_rew1dep4_status', 2)
                        ->orWhere('nvservicestatus.work_rew2dep4_status', 2)
                        ->orWhere('nvservicestatus.work_rew3dep4_status', 2)
                        ->orWhere('nvservicestatus.work_rew4dep4_status', 2)
                        ->orWhere('nvservicestatus.ceo_nominee2_status', 2)
                        ->orWhere('nvservicestatus.groupcio_status', 2);
                })
                ->sum('tbl_material.total_budget_both')+ DB::table('tbl_service')
                ->join('nvservicestatus', 'tbl_service.id', '=', 'nvservicestatus.service_id')
                ->whereIn('tbl_service.nv_id', $totalId)
                ->whereIn('nvservicestatus.nv_id', $latestData->pluck('nv_id'))
                ->where(function ($query) {
                    $query->orWhere('nvservicestatus.ceo_status', 2)
                    ->orWhere('nvservicestatus.rv1_status', 2)
                    ->orWhere('nvservicestatus.rv2_status', 2)
                    ->orWhere('nvservicestatus.rv3_status', 2)
                    ->orWhere('nvservicestatus.rv4_status', 2)
                    ->orWhere('nvservicestatus.hod_status', 2)
                    ->orWhere('nvservicestatus.ces_rew1_status', 2)
                    ->orWhere('nvservicestatus.ces_rew2_status', 2)
                    ->orWhere('nvservicestatus.ces_rew3_status', 2)
                    ->orWhere('nvservicestatus.ces_rew4_status', 2)
                    ->orWhere('nvservicestatus.cpmg_status', 2)
                    ->orWhere('nvservicestatus.work_rew1_status', 2)
                    ->orWhere('nvservicestatus.work_rew2_status', 2)
                    ->orWhere('nvservicestatus.work_rew3_status', 2)
                    ->orWhere('nvservicestatus.work_rew4_status', 2)
                    ->orWhere('nvservicestatus.ces_status', 2)
                    ->orWhere('nvservicestatus.work_rew1dep2_status', 2)
                    ->orWhere('nvservicestatus.work_rew2dep2_status', 2)
                    ->orWhere('nvservicestatus.work_rew3dep2_status', 2)
                    ->orWhere('nvservicestatus.work_rew4dep2_status', 2)
                    ->orWhere('nvservicestatus.cto_status', 2)
                    ->orWhere('nvservicestatus.work_rew1dep3_status', 2)
                    ->orWhere('nvservicestatus.work_rew2dep3_status', 2)
                    ->orWhere('nvservicestatus.work_rew3dep3_status', 2)
                    ->orWhere('nvservicestatus.work_rew4dep3_status', 2)
                    ->orWhere('nvservicestatus.ceo_nominee_status', 2)
                    ->orWhere('nvservicestatus.work_rew1dep4_status', 2)
                    ->orWhere('nvservicestatus.work_rew2dep4_status', 2)
                    ->orWhere('nvservicestatus.work_rew3dep4_status', 2)
                    ->orWhere('nvservicestatus.work_rew4dep4_status', 2)
                    ->orWhere('nvservicestatus.ceo_nominee2_status', 2)
                    ->orWhere('nvservicestatus.groupcio_status', 2);
                })
                ->sum('tbl_service.total_buget');
           
       
                $approvedAmount = DB::table('tbl_material')
                ->join('nvservicestatus', 'tbl_material.id', '=', 'nvservicestatus.material_id')
                // ->where('tbl_material.user_id',$user_id)
               
                ->whereIn('tbl_material.nv_id', $totalId)
                ->whereIn('nvservicestatus.nv_id', $latestData->pluck('nv_id'))->where('nvservicestatus.ceo_status', 1)
                ->sum('tbl_material.total_budget_both') + DB::table('tbl_service')
                ->join('nvservicestatus', 'tbl_service.id', '=', 'nvservicestatus.service_id')
             
                ->whereIn('tbl_service.nv_id', $totalId)
                ->whereIn('nvservicestatus.nv_id', $latestData->pluck('nv_id'))->where('nvservicestatus.ceo_status', 1)
                ->sum('tbl_service.total_buget');
   
    // dd($approvedAmount);

                if($company_id){
                $latestData = Nvsericestatus::whereIn("nv_id", $totalId)->where('company_id',$company_id)->get();
            }else {
                $latestData = Nvsericestatus::whereIn("nv_id", $totalId)->get();
            }
                if($company_id){
                    $approvedNV = $latestData->where('ceo_status', 1)->where('company_id',$company_id)->count();
                }else{
                    $approvedNV = $latestData->where('ceo_status', 1)->count();
                }
                $rejectedNV = $latestData->filter(function ($data) {
                    return in_array($data->ceo_status, [2]) ||
                    $data->rv1_status == 2 ||
                    $data->rv2_status == 2 ||
                    $data->rv3_status == 2 ||
                    $data->rv4_status == 2 ||
                    $data->hod_status == 2 ||
                    $data->ces_rew1_status == 2 ||
                    $data->ces_rew2_status == 2 ||
                    $data->ces_rew3_status == 2 ||
                    $data->ces_rew4_status == 2 ||
                    $data->ces_status == 2 ||
                    $data->work_rew1_status == 2 ||
                    $data->work_rew2_status == 2 ||
                    $data->work_rew3_status == 2 ||
                    $data->work_rew4_status == 2 ||
                    $data->cpmg_status == 2 ||
                    $data->work_rew1dep2_status == 2 ||
                    $data->work_rew2dep2_status == 2 ||
                    $data->work_rew3dep2_status == 2 ||
                    $data->work_rew4dep2_status == 2 ||
                    $data->cto_status == 2 ||
                    $data->work_rew1dep3_status == 2 ||
                    $data->work_rew2dep3_status == 2 ||
                    $data->work_rew3dep3_status == 2 ||
                    $data->work_rew4dep3_status == 2 ||
                    $data->ceo_nominee_status == 2 ||
                    $data->work_rew1dep4_status == 2 ||
                    $data->work_rew2dep4_status == 2 ||
                    $data->work_rew3dep4_status == 2 ||
                    $data->work_rew4dep4_status == 2 ||
                    $data->ceo_nominee2_status == 2 ||
                    $data->groupcio_status == 2;
                })->count();

               

                $pendingNV = $latestData->filter(function ($data) {
                    return in_array($data->ceo_status, [0])
                    && in_array($data->rv1_status, [0,1])
                    && in_array($data->rv2_status, [0,1])
                    && in_array($data->rv3_status, [0,1])
                    && in_array($data->rv4_status, [0,1])
                    && in_array($data->hod_status, [0, 1])
                    && in_array($data->ces_rew1_status, [0,1])
                    && in_array($data->ces_rew2_status, [0,1])
                    && in_array($data->ces_rew3_status, [0,1])
                    && in_array($data->ces_rew4_status, [0,1])
                    && in_array($data->cpmg_status, [0, 1])
                    && in_array($data->work_rew1_status, [0,1])
                    && in_array($data->work_rew2_status, [0,1])
                    && in_array($data->work_rew3_status, [0,1])
                    && in_array($data->work_rew4_status, [0,1])
                    && in_array($data->ces_status, [0, 1])
                    && in_array($data->work_rew1dep2_status, [0,1])
                    && in_array($data->work_rew2dep2_status, [0,1])
                    && in_array($data->work_rew3dep2_status, [0,1])
                    && in_array($data->work_rew4dep2_status, [0,1])
                    && in_array($data->cto_status, [0, 1])
                    && in_array($data->work_rew1dep3_status, [0,1])
                    && in_array($data->work_rew2dep3_status, [0,1])
                    && in_array($data->work_rew3dep3_status, [0,1])
                    && in_array($data->work_rew4dep3_status, [0,1])
                    && in_array($data->ceo_nominee_status, [0, 1])
                    && in_array($data->work_rew1dep4_status, [0,1])
                    && in_array($data->work_rew2dep4_status, [0,1])
                    && in_array($data->work_rew3dep4_status, [0,1])
                    && in_array($data->work_rew4dep4_status, [0,1])
                    && in_array($data->ceo_nominee2_status, [0, 1])
                    && in_array($data->groupcio_status, [0, 1]) ;
                })->count();
                // $pendingNV=$pendingNV-$approvedNV-$rejectedNV ;
                 if($company_id){
                    $totalNV = Nvsericestatus::whereIn("nv_id", $totalId)->where('ceo_status', 1)->where('company_id',$company_id)->count();
                } else {
                    $totalNV = Nvsericestatus::whereIn("nv_id", $totalId)->where('ceo_status', 1)->count();
                }
                 
                if($company_id){
                $hodApproval = Nvsericestatus::where("hod_status", 0)->where('company_id',$company_id)->count();
            }else{
                $hodApproval = Nvsericestatus::where("hod_status", 0)->count();
            }
                if($company_id){
                $cpmgApproval = Nvsericestatus::where('company_id',$company_id)->where(function ($query) {
                    $query->whereIn('ces_status', [0,1]);
                })
                    ->Where(function ($query) {
                        $query->where('hod_status', 1);
                    })
                    ->where(function ($query) {
                        $query->where('derc_info', 0);
                    })
                    ->where(function ($query) {
                        $query->where('cpmg_status', 0);
                    })
                       ->count();
            }else{
                $cpmgApproval = Nvsericestatus::where(function ($query) {
                    $query->whereIn('ces_status', [0,1]);
                })
                    ->Where(function ($query) {
                        $query->where('hod_status', 1);
                    })
                    ->where(function ($query) {
                        $query->where('derc_info', 0);
                    })
                    ->where(function ($query) {
                        $query->where('cpmg_status', 0);
                    })
                       ->count();
            }
                if($company_id){
                $btApproval = Nvsericestatus::where("hod_status", 1)->where('cto_status', 0)->where('company_id',$company_id)->count();
            }else{
                $btApproval = Nvsericestatus::where("hod_status", 1)->where('cto_status', 0)->count();
            }
                if($company_id){
                    $ceonominee1Approval = Nvsericestatus::where("cpmg_status", 1)->whereIn('cto_status', [0,1])->whereIn("check_technology", [0,1])->where('ceo_nominee_status', 0)->where('company_id',$company_id)->count();
                } else {
                    $ceonominee1Approval = Nvsericestatus::where("cpmg_status", 1)->whereIn('cto_status', [0,1])->whereIn("check_technology", [0,1])->where('ceo_nominee_status', 0)->count();
                }
                if($company_id){
                    $ceonominee2Approval = Nvsericestatus::where('ceo_nominee_status', 1)->where('ceo_nominee2_status', 0)->where('company_id',$company_id)->count();
                } else {
                    $ceonominee2Approval = Nvsericestatus::where('ceo_nominee_status', 1)->where('ceo_nominee2_status', 0)->count();
                }
                // $ceonominee2A2pproval=Nvsericestatus::where('ceo_nominee2_status', 1)->where('ceo_nominee2_status', 0)->count();
                 if($company_id){
                $ceoApproval = Nvsericestatus::where('ceo_nominee2_status', 1)->where('ceo_status', 0)->where('company_id',$company_id)->count();
            }else{
                $ceoApproval = Nvsericestatus::where('ceo_nominee2_status', 1)->where('ceo_status', 0)->count();
            }
               
                $nvIds = NeedValidation::whereIn("user_id", $user_id)->where("delete_draft", 0)->pluck("id");
                if($fiscal_year){
                    $nv = NeedValidation::whereHas('service')->select('id')->where('user_id', $user->id)->where('fiscal_year',$fiscal_year)->where("delete_draft", 0)->get();

                }else{
                    $nv = NeedValidation::whereHas('service')->select('id')->where('user_id', $user->id)->where('fiscal_year',$currentFinancialYear)->where("delete_draft", 0)->get();

                }
                $nv_ids = $nv->pluck('id');
                if($company_id){
                    $nv_sm_data = Nvsericestatus::whereIn('nv_id', $nv_ids)->where('company_id',$company_id)->with(['service', 'material', 'user'])->orderBy('id', 'asc');
                } else {
                    $nv_sm_data = Nvsericestatus::whereIn('nv_id', $nv_ids)->with(['service', 'material', 'user'])->orderBy('id', 'asc');
                }
                $total_status = $request->total;
                $pending2_status = $request->pending;
                $reject2_status = $request->rejected;

               
                // $nv_ids = $nv->pluck('id');
                // if($company_id){
                //     $nv_sm_data = Nvsericestatus::whereIn('nv_id', $nv_ids)->where('company_id',$company_id)->with(['service', 'material', 'user'])->orderBy('id', 'asc');
                // } else {
                //     $nv_sm_data = Nvsericestatus::whereIn('nv_id', $nv_ids)->with(['service', 'material', 'user'])->orderBy('id', 'asc');
                // }
                if ($ceo_status !== null) {
                    $nv_sm_data = $nv_sm_data->where('ceo_status', $ceo_status);
                } elseif ($pending_status !== null) {
                    $nv_sm_data = $nv_sm_data->where('ceo_status', $pending_status)
                    ->whereIn('rv1_status', [0,1])
                    ->whereIn('rv2_status', [0,1])
                    ->whereIn('rv3_status', [0,1])
                    ->whereIn('rv4_status', [0,1])
                    ->whereIn('hod_status', [0,1])
                    ->whereIn('ces_rew1_status', [0,1])
                    ->whereIn('ces_rew2_status', [0,1])
                    ->whereIn('ces_rew3_status', [0,1])
                    ->whereIn('ces_rew4_status', [0,1])
                    ->whereIn('ces_status', [0,1])
                    ->whereIn('cpmg_status', [0,1])
                    ->whereIn('work_rew1_status', [0,1])
                    ->whereIn('work_rew2_status', [0,1])
                    ->whereIn('work_rew3_status', [0,1])
                    ->whereIn('work_rew4_status', [0,1])
                    ->whereIn('cto_status', [0,1])
                    ->whereIn('work_rew1dep2_status', [0,1])
                    ->whereIn('work_rew2dep2_status', [0,1])
                    ->whereIn('work_rew3dep2_status', [0,1])
                    ->whereIn('work_rew4dep2_status', [0,1])
                    ->whereIn('ceo_nominee_status', [0,1])
                    ->whereIn('work_rew1dep3_status', [0,1])
                    ->whereIn('work_rew2dep3_status', [0,1])
                    ->whereIn('work_rew3dep3_status', [0,1])
                    ->whereIn('work_rew4dep3_status', [0,1])
                    ->whereIn('ceo_nominee2_status', [0,1])
                    ->whereIn('work_rew1dep4_status', [0,1])
                    ->whereIn('work_rew2dep4_status', [0,1])
                    ->whereIn('work_rew3dep4_status', [0,1])
                    ->whereIn('work_rew4dep4_status', [0,1])
                    ->whereIn('groupcio_status', [0,1]);
                } elseif ($reject2_status !== null) {
                    $nv_sm_data = $nv_sm_data->where('ceo_status', $reject2_status)
                    ->orWhere('rv1_status', 2)
                    ->orWhere('rv2_status', 2)
                    ->orWhere('rv3_status', 2)
                    ->orWhere('rv4_status', 2)
                    ->orWhere('hod_status', 2)
                    ->orWhere('ces_rew1_status', 2)
                    ->orWhere('ces_rew2_status', 2)
                    ->orWhere('ces_rew3_status', 2)
                    ->orWhere('ces_rew4_status', 2)
                    ->orWhere('cpmg_status', 2)
                    ->orWhere('work_rew1_status', 2)
                    ->orWhere('work_rew2_status', 2)
                    ->orWhere('work_rew3_status', 2)
                    ->orWhere('work_rew4_status', 2)
                    ->orWhere('ces_status', 2)
                    ->orWhere('work_rew1dep2_status', 2)
                    ->orWhere('work_rew2dep2_status', 2)
                    ->orWhere('work_rew3dep2_status', 2)
                    ->orWhere('work_rew4dep2_status', 2)
                    ->orWhere('cto_status', 2)
                    ->orWhere('work_rew1dep3_status', 2)
                    ->orWhere('work_rew2dep3_status', 2)
                    ->orWhere('work_rew3dep3_status', 2)
                    ->orWhere('work_rew4dep3_status', 2)
                    ->orWhere('ceo_nominee_status', 2)
                    ->orWhere('work_rew1dep4_status', 2)
                    ->orWhere('work_rew2dep4_status', 2)
                    ->orWhere('work_rew3dep4_status', 2)
                    ->orWhere('work_rew4dep4_status', 2)
                    ->orWhere('ceo_nominee2_status', 2)
                    ->orWhere('groupcio_status', 2);
                }elseif ($total_status !== null) {
                    $nv_sm_data = $nv_sm_data->where('ceo_status', $total_status);
                        // ->orWhere('hod_status', [2])
                        // ->orWhere('cpmg_status', [2])
                        // ->orWhere('cto_status', [2])
                        // ->orWhere('ceo_nominee_status', [2])
                        // ->orWhere('ceo_nominee2_status', [2]);
                }
                $nv_sm_data = $nv_sm_data->get();
                if($fiscal_year){
                    $BRPLnv = NeedValidation::where('company_id','6')->where('user_id', $user->id)->where('fiscal_year',$fiscal_year)->where("delete_draft", 0)->pluck("id");

                }else{
                    $BRPLnv = NeedValidation::where('company_id','6')->where('user_id', $user->id)->where('fiscal_year',$currentFinancialYear)->where("delete_draft", 0)->pluck("id");

                }
                $pendingAmountBRPL = DB::table('tbl_material')
                ->join('nvservicestatus', 'tbl_material.id', '=', 'nvservicestatus.material_id')
                ->whereIn('tbl_material.nv_id', $BRPLnv)
                 ->where('nvservicestatus.ceo_status', 0)
                 ->whereIn('nvservicestatus.rv1_status', [0,1])
                 ->whereIn('nvservicestatus.rv2_status', [0,1])
                 ->whereIn('nvservicestatus.rv3_status', [0,1])
                 ->whereIn('nvservicestatus.rv4_status', [0,1])
                 ->whereIn('nvservicestatus.hod_status', [0,1])
                 ->whereIn('nvservicestatus.ces_rew1_status', [0,1])
                 ->whereIn('nvservicestatus.ces_rew2_status', [0,1])
                 ->whereIn('nvservicestatus.ces_rew3_status', [0,1])
                 ->whereIn('nvservicestatus.ces_rew4_status', [0,1])
                 ->whereIn('nvservicestatus.ces_status', [0,1])
                 ->whereIn('nvservicestatus.cpmg_status', [0,1])
                 ->whereIn('nvservicestatus.work_rew1_status', [0,1])
                 ->whereIn('nvservicestatus.work_rew2_status', [0,1])
                 ->whereIn('nvservicestatus.work_rew3_status', [0,1])
                 ->whereIn('nvservicestatus.work_rew4_status', [0,1])
                 ->whereIn('nvservicestatus.cto_status', [0,1])
                 ->whereIn('nvservicestatus.work_rew1dep2_status', [0,1])
                 ->whereIn('nvservicestatus.work_rew2dep2_status', [0,1])
                 ->whereIn('nvservicestatus.work_rew3dep2_status', [0,1])
                 ->whereIn('nvservicestatus.work_rew4dep2_status', [0,1])
                 ->whereIn('nvservicestatus.ceo_nominee_status', [0,1])
                 ->whereIn('nvservicestatus.work_rew1dep3_status', [0,1])
                 ->whereIn('nvservicestatus.work_rew2dep3_status', [0,1])
                 ->whereIn('nvservicestatus.work_rew3dep3_status', [0,1])
                 ->whereIn('nvservicestatus.work_rew4dep3_status', [0,1])
                 ->whereIn('nvservicestatus.ceo_nominee2_status', [0,1])
                 ->whereIn('nvservicestatus.work_rew1dep4_status', [0,1])
                 ->whereIn('nvservicestatus.work_rew2dep4_status', [0,1])
                 ->whereIn('nvservicestatus.work_rew3dep4_status', [0,1])
                 ->whereIn('nvservicestatus.work_rew4dep4_status', [0,1])
                 ->whereIn('nvservicestatus.groupcio_status', [0,1])
                ->sum('tbl_material.total_budget_both')+DB::table('tbl_service')
                ->join('nvservicestatus', 'tbl_service.id', '=', 'nvservicestatus.service_id')
                ->whereIn('tbl_service.nv_id', $BRPLnv)
                ->where('nvservicestatus.ceo_status', 0)
                ->whereIn('nvservicestatus.rv1_status', [0,1])
                ->whereIn('nvservicestatus.rv2_status', [0,1])
                ->whereIn('nvservicestatus.rv3_status', [0,1])
                ->whereIn('nvservicestatus.rv4_status', [0,1])
                ->whereIn('nvservicestatus.hod_status', [0,1])
                ->whereIn('nvservicestatus.ces_rew1_status', [0,1])
                ->whereIn('nvservicestatus.ces_rew2_status', [0,1])
                ->whereIn('nvservicestatus.ces_rew3_status', [0,1])
                ->whereIn('nvservicestatus.ces_rew4_status', [0,1])
                ->whereIn('nvservicestatus.ces_status', [0,1])
                ->whereIn('nvservicestatus.cpmg_status', [0,1])
                ->whereIn('nvservicestatus.work_rew1_status', [0,1])
                ->whereIn('nvservicestatus.work_rew2_status', [0,1])
                ->whereIn('nvservicestatus.work_rew3_status', [0,1])
                ->whereIn('nvservicestatus.work_rew4_status', [0,1])
                ->whereIn('nvservicestatus.cto_status', [0,1])
                ->whereIn('nvservicestatus.work_rew1dep2_status', [0,1])
                ->whereIn('nvservicestatus.work_rew2dep2_status', [0,1])
                ->whereIn('nvservicestatus.work_rew3dep2_status', [0,1])
                ->whereIn('nvservicestatus.work_rew4dep2_status', [0,1])
                ->whereIn('nvservicestatus.ceo_nominee_status', [0,1])
                ->whereIn('nvservicestatus.work_rew1dep3_status', [0,1])
                ->whereIn('nvservicestatus.work_rew2dep3_status', [0,1])
                ->whereIn('nvservicestatus.work_rew3dep3_status', [0,1])
                ->whereIn('nvservicestatus.work_rew4dep3_status', [0,1])
                ->whereIn('nvservicestatus.ceo_nominee2_status', [0,1])
                ->whereIn('nvservicestatus.work_rew1dep4_status', [0,1])
                ->whereIn('nvservicestatus.work_rew2dep4_status', [0,1])
                ->whereIn('nvservicestatus.work_rew3dep4_status', [0,1])
                ->whereIn('nvservicestatus.work_rew4dep4_status', [0,1])
                ->whereIn('nvservicestatus.groupcio_status', [0,1])
                ->sum('tbl_service.total_buget');
             
       
                $rejectedAmountBRPL = DB::table('tbl_material')
                ->join('nvservicestatus', 'tbl_material.id', '=', 'nvservicestatus.material_id')
                ->whereIn('tbl_material.nv_id', $BRPLnv)
                ->where(function ($query) {
                    $query->orWhere('nvservicestatus.ceo_status', 2)
                    ->orWhere('nvservicestatus.rv1_status', 2)
                    ->orWhere('nvservicestatus.rv2_status', 2)
                    ->orWhere('nvservicestatus.rv3_status', 2)
                    ->orWhere('nvservicestatus.rv4_status', 2)
                    ->orWhere('nvservicestatus.hod_status', 2)
                    ->orWhere('nvservicestatus.ces_rew1_status', 2)
                    ->orWhere('nvservicestatus.ces_rew2_status', 2)
                    ->orWhere('nvservicestatus.ces_rew3_status', 2)
                    ->orWhere('nvservicestatus.ces_rew4_status', 2)
                    ->orWhere('nvservicestatus.cpmg_status', 2)
                    ->orWhere('nvservicestatus.work_rew1_status', 2)
                    ->orWhere('nvservicestatus.work_rew2_status', 2)
                    ->orWhere('nvservicestatus.work_rew3_status', 2)
                    ->orWhere('nvservicestatus.work_rew4_status', 2)
                    ->orWhere('nvservicestatus.ces_status', 2)
                    ->orWhere('nvservicestatus.work_rew1dep2_status', 2)
                    ->orWhere('nvservicestatus.work_rew2dep2_status', 2)
                    ->orWhere('nvservicestatus.work_rew3dep2_status', 2)
                    ->orWhere('nvservicestatus.work_rew4dep2_status', 2)
                    ->orWhere('nvservicestatus.cto_status', 2)
                    ->orWhere('nvservicestatus.work_rew1dep3_status', 2)
                    ->orWhere('nvservicestatus.work_rew2dep3_status', 2)
                    ->orWhere('nvservicestatus.work_rew3dep3_status', 2)
                    ->orWhere('nvservicestatus.work_rew4dep3_status', 2)
                    ->orWhere('nvservicestatus.ceo_nominee_status', 2)
                    ->orWhere('nvservicestatus.work_rew1dep4_status', 2)
                    ->orWhere('nvservicestatus.work_rew2dep4_status', 2)
                    ->orWhere('nvservicestatus.work_rew3dep4_status', 2)
                    ->orWhere('nvservicestatus.work_rew4dep4_status', 2)
                    ->orWhere('nvservicestatus.ceo_nominee2_status', 2)
                    ->orWhere('nvservicestatus.groupcio_status', 2);
                })
                ->sum('tbl_material.total_budget_both')+DB::table('tbl_service')
                ->join('nvservicestatus', 'tbl_service.id', '=', 'nvservicestatus.service_id')
                ->whereIn('tbl_service.nv_id', $BRPLnv)
                ->where(function ($query) {
                    $query->orWhere('nvservicestatus.ceo_status', 2)
                    ->orWhere('nvservicestatus.rv1_status', 2)
                    ->orWhere('nvservicestatus.rv2_status', 2)
                    ->orWhere('nvservicestatus.rv3_status', 2)
                    ->orWhere('nvservicestatus.rv4_status', 2)
                    ->orWhere('nvservicestatus.hod_status', 2)
                    ->orWhere('nvservicestatus.ces_rew1_status', 2)
                    ->orWhere('nvservicestatus.ces_rew2_status', 2)
                    ->orWhere('nvservicestatus.ces_rew3_status', 2)
                    ->orWhere('nvservicestatus.ces_rew4_status', 2)
                    ->orWhere('nvservicestatus.cpmg_status', 2)
                    ->orWhere('nvservicestatus.work_rew1_status', 2)
                    ->orWhere('nvservicestatus.work_rew2_status', 2)
                    ->orWhere('nvservicestatus.work_rew3_status', 2)
                    ->orWhere('nvservicestatus.work_rew4_status', 2)
                    ->orWhere('nvservicestatus.ces_status', 2)
                    ->orWhere('nvservicestatus.work_rew1dep2_status', 2)
                    ->orWhere('nvservicestatus.work_rew2dep2_status', 2)
                    ->orWhere('nvservicestatus.work_rew3dep2_status', 2)
                    ->orWhere('nvservicestatus.work_rew4dep2_status', 2)
                    ->orWhere('nvservicestatus.cto_status', 2)
                    ->orWhere('nvservicestatus.work_rew1dep3_status', 2)
                    ->orWhere('nvservicestatus.work_rew2dep3_status', 2)
                    ->orWhere('nvservicestatus.work_rew3dep3_status', 2)
                    ->orWhere('nvservicestatus.work_rew4dep3_status', 2)
                    ->orWhere('nvservicestatus.ceo_nominee_status', 2)
                    ->orWhere('nvservicestatus.work_rew1dep4_status', 2)
                    ->orWhere('nvservicestatus.work_rew2dep4_status', 2)
                    ->orWhere('nvservicestatus.work_rew3dep4_status', 2)
                    ->orWhere('nvservicestatus.work_rew4dep4_status', 2)
                    ->orWhere('nvservicestatus.ceo_nominee2_status', 2)
                    ->orWhere('nvservicestatus.groupcio_status', 2);
                })
                ->sum('tbl_service.total_buget');
           
       
                $approvedAmountBRPL = DB::table('tbl_material')
                ->join('nvservicestatus', 'tbl_material.id', '=', 'nvservicestatus.material_id')
                ->whereIn('tbl_material.nv_id', $BRPLnv)->where('nvservicestatus.ceo_status', 1)
                ->sum('tbl_material.total_budget_both')+DB::table('tbl_service')
                ->join('nvservicestatus', 'tbl_service.id', '=', 'nvservicestatus.service_id')
                ->whereIn('tbl_service.nv_id', $BRPLnv)->where('nvservicestatus.ceo_status', 1)
                ->sum('tbl_service.total_buget');
   
                $fileDataBRPL =Nvsericestatus::
                select(
                    DB::raw('MONTH(created_at) as month'),
                    DB::raw('SUM(CASE WHEN ceo_status = "1" THEN 1 ELSE 0 END) as approved_count'),

                    DB::raw('SUM(CASE WHEN (rv1_status = "2" OR rv2_status = "2" OR rv3_status = "2" OR rv4_status = "2" OR hod_status = "2" OR
                    ces_rew1_status = "2" OR ces_rew2_status = "2" OR ces_rew3_status = "2" OR ces_rew4_status = "2" OR cpmg_status = "2" OR
                    work_rew1_status = "2" OR work_rew2_status = "2" OR work_rew3_status = "2" OR work_rew4_status = "2" OR cto_status = "2" OR
                    work_rew1dep2_status = "2" OR work_rew2dep2_status = "2" OR work_rew3dep2_status = "2" OR work_rew4dep2_status = "2" OR ceo_nominee_status = "2" OR
                    work_rew1dep3_status = "2" OR work_rew2dep3_status = "2" OR work_rew3dep3_status = "2" OR work_rew4dep3_status = "2" OR ceo_nominee2_status = "2" OR
                    work_rew1dep4_status = "2" OR work_rew2dep4_status = "2" OR work_rew3dep4_status = "2" OR work_rew4dep4_status = "2" OR
                    ceo_status = "2" OR groupcio_status = "2")  THEN 1 ELSE 0 END) as rejected_count'),

                    DB::raw('SUM(CASE WHEN (ceo_status = "0" AND
                    rv1_status IN ("0", "1") AND rv2_status IN ("0", "1") AND rv3_status IN ("0", "1") AND rv4_status IN ("0", "1") AND hod_status IN ("0", "1") AND
                    ces_rew1_status IN ("0", "1") AND ces_rew2_status IN ("0", "1") AND ces_rew3_status IN ("0", "1") AND ces_rew4_status IN ("0", "1") AND cpmg_status IN ("0", "1") AND
                    work_rew1_status IN ("0", "1") AND work_rew2_status IN ("0", "1") AND work_rew3_status IN ("0", "1") AND work_rew4_status IN ("0", "1") AND cto_status IN ("0", "1") AND
                    work_rew1dep2_status IN ("0", "1") AND work_rew2dep2_status IN ("0", "1") AND work_rew3dep2_status IN ("0", "1") AND work_rew4dep2_status IN ("0", "1") AND ceo_nominee_status IN ("0", "1") AND
                    work_rew1dep3_status IN ("0", "1") AND work_rew2dep3_status IN ("0", "1") AND work_rew3dep3_status IN ("0", "1") AND work_rew4dep3_status IN ("0", "1") AND ceo_nominee2_status IN ("0", "1") AND
                    work_rew1dep4_status IN ("0", "1") AND work_rew2dep4_status IN ("0", "1") AND work_rew3dep4_status IN ("0", "1") AND work_rew4dep4_status IN ("0", "1") AND
                    ceo_status IN ("0", "1") AND groupcio_status IN ("0", "1"))  THEN 1 ELSE 0 END) as pending_count'),

                    // DB::raw('SUM(CASE WHEN ceo_status = "0" AND hod_status IN ("0", "1") AND ces_status IN ("0", "1")
                    // AND cpmg_status IN ("0", "1") AND cto_status IN ("0", "1") AND ceo_nominee_status IN ("0", "1")
                    //  AND ceo_nominee2_status IN ("0", "1")  AND groupcio_status IN ("0", "1") THEN 1 ELSE 0 END) as pending_count'),  
                    // DB::raw('SUM(CASE WHEN ceo_status = "0" THEN 1 ELSE 0 END) as pending_count'),
                    // DB::raw('SUM(CASE WHEN status = "pending" THEN 1 ELSE 0 END) as rejected_count')
                )
                ->whereIn('nv_id', $BRPLnv)->with(['service', 'material', 'user'])
                ->whereYear('created_at', Carbon::now()->year)
                ->groupBy('month')
                ->orderBy('month')
                ->get();
        // dd($fileDataBRPL);
            $BRPLlabels = [];
            $BRPLapprovedData = [];
            $BRPLrejectedData = [];
            $BRPLpendingData = [];
       
            foreach ($fileDataBRPL as $dataPointBRPL) {
                $monthBRPL = Carbon::createFromFormat('!m', $dataPointBRPL->month)->format('F');
       
                $BRPLlabels[] = $monthBRPL;
                $BRPLapprovedData[] = $dataPointBRPL->approved_count;
                $BRPLrejectedData[] = $dataPointBRPL->rejected_count;
                $BRPLpendingData[] = $dataPointBRPL->pending_count;
                 // dd( $dataPoint->rejected_count);
            }
            if($fiscal_year){
                $BYPLnv = NeedValidation::where('company_id','5')->where('user_id', $user->id)->where('fiscal_year',$fiscal_year)->where('delete_draft',0)->pluck("id");

            }else{
                $BYPLnv = NeedValidation::where('company_id','5')->where('user_id', $user->id)->where('fiscal_year',$currentFinancialYear)->where('delete_draft',0)->pluck("id");

            }

            $pendingAmountBYPL = DB::table('tbl_material')
            ->join('nvservicestatus', 'tbl_material.id', '=', 'nvservicestatus.material_id')
            ->whereIn('tbl_material.nv_id', $BYPLnv)
             ->where('nvservicestatus.ceo_status', 0)
             ->whereIn('nvservicestatus.rv1_status', [0,1])
             ->whereIn('nvservicestatus.rv2_status', [0,1])
             ->whereIn('nvservicestatus.rv3_status', [0,1])
             ->whereIn('nvservicestatus.rv4_status', [0,1])
             ->whereIn('nvservicestatus.hod_status', [0,1])
             ->whereIn('nvservicestatus.ces_rew1_status', [0,1])
             ->whereIn('nvservicestatus.ces_rew2_status', [0,1])
             ->whereIn('nvservicestatus.ces_rew3_status', [0,1])
             ->whereIn('nvservicestatus.ces_rew4_status', [0,1])
             ->whereIn('nvservicestatus.ces_status', [0,1])
             ->whereIn('nvservicestatus.cpmg_status', [0,1])
             ->whereIn('nvservicestatus.work_rew1_status', [0,1])
             ->whereIn('nvservicestatus.work_rew2_status', [0,1])
             ->whereIn('nvservicestatus.work_rew3_status', [0,1])
             ->whereIn('nvservicestatus.work_rew4_status', [0,1])
             ->whereIn('nvservicestatus.cto_status', [0,1])
             ->whereIn('nvservicestatus.work_rew1dep2_status', [0,1])
             ->whereIn('nvservicestatus.work_rew2dep2_status', [0,1])
             ->whereIn('nvservicestatus.work_rew3dep2_status', [0,1])
             ->whereIn('nvservicestatus.work_rew4dep2_status', [0,1])
             ->whereIn('nvservicestatus.ceo_nominee_status', [0,1])
             ->whereIn('nvservicestatus.work_rew1dep3_status', [0,1])
             ->whereIn('nvservicestatus.work_rew2dep3_status', [0,1])
             ->whereIn('nvservicestatus.work_rew3dep3_status', [0,1])
             ->whereIn('nvservicestatus.work_rew4dep3_status', [0,1])
             ->whereIn('nvservicestatus.ceo_nominee2_status', [0,1])
             ->whereIn('nvservicestatus.work_rew1dep4_status', [0,1])
             ->whereIn('nvservicestatus.work_rew2dep4_status', [0,1])
             ->whereIn('nvservicestatus.work_rew3dep4_status', [0,1])
             ->whereIn('nvservicestatus.work_rew4dep4_status', [0,1])
             ->whereIn('nvservicestatus.groupcio_status', [0,1])
            ->sum('tbl_material.total_budget_both')+DB::table('tbl_service')
            ->join('nvservicestatus', 'tbl_service.id', '=', 'nvservicestatus.service_id')
            ->whereIn('tbl_service.nv_id', $BYPLnv)
             ->where('nvservicestatus.ceo_status', 0)
             ->whereIn('nvservicestatus.rv1_status', [0,1])
             ->whereIn('nvservicestatus.rv2_status', [0,1])
             ->whereIn('nvservicestatus.rv3_status', [0,1])
             ->whereIn('nvservicestatus.rv4_status', [0,1])
             ->whereIn('nvservicestatus.hod_status', [0,1])
             ->whereIn('nvservicestatus.ces_rew1_status', [0,1])
             ->whereIn('nvservicestatus.ces_rew2_status', [0,1])
             ->whereIn('nvservicestatus.ces_rew3_status', [0,1])
             ->whereIn('nvservicestatus.ces_rew4_status', [0,1])
             ->whereIn('nvservicestatus.ces_status', [0,1])
             ->whereIn('nvservicestatus.cpmg_status', [0,1])
             ->whereIn('nvservicestatus.work_rew1_status', [0,1])
             ->whereIn('nvservicestatus.work_rew2_status', [0,1])
             ->whereIn('nvservicestatus.work_rew3_status', [0,1])
             ->whereIn('nvservicestatus.work_rew4_status', [0,1])
             ->whereIn('nvservicestatus.cto_status', [0,1])
             ->whereIn('nvservicestatus.work_rew1dep2_status', [0,1])
             ->whereIn('nvservicestatus.work_rew2dep2_status', [0,1])
             ->whereIn('nvservicestatus.work_rew3dep2_status', [0,1])
             ->whereIn('nvservicestatus.work_rew4dep2_status', [0,1])
             ->whereIn('nvservicestatus.ceo_nominee_status', [0,1])
             ->whereIn('nvservicestatus.work_rew1dep3_status', [0,1])
             ->whereIn('nvservicestatus.work_rew2dep3_status', [0,1])
             ->whereIn('nvservicestatus.work_rew3dep3_status', [0,1])
             ->whereIn('nvservicestatus.work_rew4dep3_status', [0,1])
             ->whereIn('nvservicestatus.ceo_nominee2_status', [0,1])
             ->whereIn('nvservicestatus.work_rew1dep4_status', [0,1])
             ->whereIn('nvservicestatus.work_rew2dep4_status', [0,1])
             ->whereIn('nvservicestatus.work_rew3dep4_status', [0,1])
             ->whereIn('nvservicestatus.work_rew4dep4_status', [0,1])
             ->whereIn('nvservicestatus.groupcio_status', [0,1])
            ->sum('tbl_service.total_buget');

            // dd($pendingAmountBYPL);
           
         
   
            $rejectedAmountBYPL = DB::table('tbl_material')
            ->join('nvservicestatus', 'tbl_material.id', '=', 'nvservicestatus.material_id')
            ->whereIn('tbl_material.nv_id', $BYPLnv)
            ->where(function ($query) {
                $query->orWhere('nvservicestatus.ceo_status', 2)
                ->orWhere('nvservicestatus.rv1_status', 2)
                ->orWhere('nvservicestatus.rv2_status', 2)
                ->orWhere('nvservicestatus.rv3_status', 2)
                ->orWhere('nvservicestatus.rv4_status', 2)
                ->orWhere('nvservicestatus.hod_status', 2)
                ->orWhere('nvservicestatus.ces_rew1_status', 2)
                ->orWhere('nvservicestatus.ces_rew2_status', 2)
                ->orWhere('nvservicestatus.ces_rew3_status', 2)
                ->orWhere('nvservicestatus.ces_rew4_status', 2)
                ->orWhere('nvservicestatus.cpmg_status', 2)
                ->orWhere('nvservicestatus.work_rew1_status', 2)
                ->orWhere('nvservicestatus.work_rew2_status', 2)
                ->orWhere('nvservicestatus.work_rew3_status', 2)
                ->orWhere('nvservicestatus.work_rew4_status', 2)
                ->orWhere('nvservicestatus.ces_status', 2)
                ->orWhere('nvservicestatus.work_rew1dep2_status', 2)
                ->orWhere('nvservicestatus.work_rew2dep2_status', 2)
                ->orWhere('nvservicestatus.work_rew3dep2_status', 2)
                ->orWhere('nvservicestatus.work_rew4dep2_status', 2)
                ->orWhere('nvservicestatus.cto_status', 2)
                ->orWhere('nvservicestatus.work_rew1dep3_status', 2)
                ->orWhere('nvservicestatus.work_rew2dep3_status', 2)
                ->orWhere('nvservicestatus.work_rew3dep3_status', 2)
                ->orWhere('nvservicestatus.work_rew4dep3_status', 2)
                ->orWhere('nvservicestatus.ceo_nominee_status', 2)
                ->orWhere('nvservicestatus.work_rew1dep4_status', 2)
                ->orWhere('nvservicestatus.work_rew2dep4_status', 2)
                ->orWhere('nvservicestatus.work_rew3dep4_status', 2)
                ->orWhere('nvservicestatus.work_rew4dep4_status', 2)
                ->orWhere('nvservicestatus.ceo_nominee2_status', 2)
                ->orWhere('nvservicestatus.groupcio_status', 2);
            })
            ->sum('tbl_material.total_budget_both')+DB::table('tbl_service')
            ->join('nvservicestatus', 'tbl_service.id', '=', 'nvservicestatus.service_id')
            ->whereIn('tbl_service.nv_id', $BYPLnv)
            ->where(function ($query) {
                $query->orWhere('nvservicestatus.ceo_status', 2)
                ->orWhere('nvservicestatus.rv1_status', 2)
                ->orWhere('nvservicestatus.rv2_status', 2)
                ->orWhere('nvservicestatus.rv3_status', 2)
                ->orWhere('nvservicestatus.rv4_status', 2)
                ->orWhere('nvservicestatus.hod_status', 2)
                ->orWhere('nvservicestatus.ces_rew1_status', 2)
                ->orWhere('nvservicestatus.ces_rew2_status', 2)
                ->orWhere('nvservicestatus.ces_rew3_status', 2)
                ->orWhere('nvservicestatus.ces_rew4_status', 2)
                ->orWhere('nvservicestatus.cpmg_status', 2)
                ->orWhere('nvservicestatus.work_rew1_status', 2)
                ->orWhere('nvservicestatus.work_rew2_status', 2)
                ->orWhere('nvservicestatus.work_rew3_status', 2)
                ->orWhere('nvservicestatus.work_rew4_status', 2)
                ->orWhere('nvservicestatus.ces_status', 2)
                ->orWhere('nvservicestatus.work_rew1dep2_status', 2)
                ->orWhere('nvservicestatus.work_rew2dep2_status', 2)
                ->orWhere('nvservicestatus.work_rew3dep2_status', 2)
                ->orWhere('nvservicestatus.work_rew4dep2_status', 2)
                ->orWhere('nvservicestatus.cto_status', 2)
                ->orWhere('nvservicestatus.work_rew1dep3_status', 2)
                ->orWhere('nvservicestatus.work_rew2dep3_status', 2)
                ->orWhere('nvservicestatus.work_rew3dep3_status', 2)
                ->orWhere('nvservicestatus.work_rew4dep3_status', 2)
                ->orWhere('nvservicestatus.ceo_nominee_status', 2)
                ->orWhere('nvservicestatus.work_rew1dep4_status', 2)
                ->orWhere('nvservicestatus.work_rew2dep4_status', 2)
                ->orWhere('nvservicestatus.work_rew3dep4_status', 2)
                ->orWhere('nvservicestatus.work_rew4dep4_status', 2)
                ->orWhere('nvservicestatus.ceo_nominee2_status', 2)
                ->orWhere('nvservicestatus.groupcio_status', 2);
            })
            ->sum('tbl_service.total_buget');
           
   
            $approvedAmountBYPL = DB::table('tbl_material')
            ->join('nvservicestatus', 'tbl_material.id', '=', 'nvservicestatus.material_id')
            ->whereIn('tbl_material.nv_id', $BYPLnv)->where('nvservicestatus.ceo_status', 1)
            ->sum('tbl_material.total_budget_both')+DB::table('tbl_service')
            ->join('nvservicestatus', 'tbl_service.id', '=', 'nvservicestatus.service_id')
            ->whereIn('tbl_service.nv_id', $BYPLnv)->where('nvservicestatus.ceo_status', 1)
            ->sum('tbl_service.total_buget');
            //dd($approvedAmountBYPL);
             $fileDataBYPL =Nvsericestatus::
            select(
                DB::raw('MONTH(created_at) as month'),
                DB::raw('SUM(CASE WHEN ceo_status = "1" THEN 1 ELSE 0 END) as approved_count'),

                DB::raw('SUM(CASE WHEN (rv1_status = "2" OR rv2_status = "2" OR rv3_status = "2" OR rv4_status = "2" OR hod_status = "2" OR
                ces_rew1_status = "2" OR ces_rew2_status = "2" OR ces_rew3_status = "2" OR ces_rew4_status = "2" OR ces_status = "2" OR cpmg_status = "2" OR
                work_rew1_status = "2" OR work_rew2_status = "2" OR work_rew3_status = "2" OR work_rew4_status = "2" OR cto_status = "2" OR
                work_rew1dep2_status = "2" OR work_rew2dep2_status = "2" OR work_rew3dep2_status = "2" OR work_rew4dep2_status = "2" OR ceo_nominee_status = "2" OR
                work_rew1dep3_status = "2" OR work_rew2dep3_status = "2" OR work_rew3dep3_status = "2" OR work_rew4dep3_status = "2" OR ceo_nominee2_status = "2" OR
                work_rew1dep4_status = "2" OR work_rew2dep4_status = "2" OR work_rew3dep4_status = "2" OR work_rew4dep4_status = "2" OR
                ceo_status = "2" OR groupcio_status = "2")  THEN 1 ELSE 0 END) as rejected_count'),

                DB::raw('SUM(CASE WHEN (ceo_status = "0" AND
                rv1_status IN ("0", "1") AND rv2_status IN ("0", "1") AND rv3_status IN ("0", "1") AND rv4_status IN ("0", "1") AND hod_status IN ("0", "1") AND
                ces_rew1_status IN ("0", "1") AND ces_rew2_status IN ("0", "1") AND ces_rew3_status IN ("0", "1") AND ces_rew4_status IN ("0", "1") AND ces_status IN ("0", "1") AND cpmg_status IN ("0", "1") AND
                work_rew1_status IN ("0", "1") AND work_rew2_status IN ("0", "1") AND work_rew3_status IN ("0", "1") AND work_rew4_status IN ("0", "1") AND cto_status IN ("0", "1") AND
                work_rew1dep2_status IN ("0", "1") AND work_rew2dep2_status IN ("0", "1") AND work_rew3dep2_status IN ("0", "1") AND work_rew4dep2_status IN ("0", "1") AND ceo_nominee_status IN ("0", "1") AND
                work_rew1dep3_status IN ("0", "1") AND work_rew2dep3_status IN ("0", "1") AND work_rew3dep3_status IN ("0", "1") AND work_rew4dep3_status IN ("0", "1") AND ceo_nominee2_status IN ("0", "1") AND
                work_rew1dep4_status IN ("0", "1") AND work_rew2dep4_status IN ("0", "1") AND work_rew3dep4_status IN ("0", "1") AND work_rew4dep4_status IN ("0", "1") AND
                ceo_status IN ("0", "1") AND groupcio_status IN ("0", "1"))  THEN 1 ELSE 0 END) as pending_count'),
                // DB::raw('SUM(CASE WHEN ceo_status = "0" THEN 1 ELSE 0 END) as pending_count'),
                // DB::raw('SUM(CASE WHEN status = "pending" THEN 1 ELSE 0 END) as rejected_count')
            )
            ->whereIn('nv_id', $BYPLnv)->with(['service', 'material', 'user'])
            ->whereYear('created_at', Carbon::now()->year)
            ->groupBy('month')
            ->orderBy('month')
            ->get();
            // dd($fileDataBYPL);
        $BYPLlabels = [];
        $BYPLapprovedData = [];
        $BYPLrejectedData = [];
        $BYPLpendingData = [];
       
   
        foreach ($fileDataBYPL as $dataPointBYPL) {
            $monthBYPL = Carbon::createFromFormat('!m', $dataPointBYPL->month)->format('F');
   
            $BYPLlabels[] = $monthBYPL;
            $BYPLapprovedData[] = $dataPointBYPL->approved_count;
            $BYPLrejectedData[] = $dataPointBYPL->rejected_count;
            $BYPLpendingData[] = $dataPointBYPL->pending_count;
             
        }

   
    
        // dd($nv_sm_data) ;
        $userid = \Auth::user()->id;
        $log = DB::table('log_signature')->where('user_id',$userid)->select('created_at','signature_id')->get();
        
        return view("admin.dashboard", compact("approvedAmount","currentFinancialYear","nextFinancialYear","nextToNextFinancialYear", "rejectedAmount", "pendingAmount", "totalAmount","company", "company_id" , "ceonominee2Approval", "ceonominee1Approval", "nv_sm_data", "totalNV", "approvedNV", "rejectedNV", "pendingNV" , "hodApproval", "cpmgApproval", "btApproval", "ceoApproval"             ,'BRPLlabels','BRPLapprovedData','BRPLrejectedData','BRPLpendingData','BYPLlabels','BYPLapprovedData','BYPLrejectedData','BYPLpendingData','log','pendingAmountBYPL','pendingAmountBRPL','approvedAmountBYPL','approvedAmountBRPL','rejectedAmountBYPL','rejectedAmountBRPL'));
            }
        }
            
        }
    }

    public function addSignature(Request $request)

    {
        $user_id = \Auth::user()->id;
        if($request->hasfile('image'))

        {

            $file = $request->file('image');

            $filename = time().rand().'.'.$file->getClientOriginalName();

            $file->move('images/', $filename);

            $imgname = $filename ?? '';

        }
        // dd($imgname);
        
        $data = [
            'signature_id'      => $request->signature ?? '',
            'signature_status'  => 1,
            'image'             => $imgname ?? '',
            
        ];
        
        $create  = User::where('id',$request->user_id)->update($data);
        $data1 = [
            'signature_id'      => $request->signature ?? '',

            'signature_status'  => 1,

            'image'             => $imgname ?? '',

            

        ];

        

        // dd($data);

        $create  = User::where('id',$request->user_id)->update($data);

        $data1 = [

            'signature_id'      => $request->signature ?? '',

            'user_id'           => $user_id,

            'image'             => $imgname ?? '',

        ];

        $insert = DB::table('log_signature')->insert($data1);

        // dd( $create);

        if($create )

        {

            $UserData = User::where('id',$request->user_id)->first();

           // dd($UserData);

            return response()->json([

                "message"       => "Success",

                "signature_id"  => $UserData->signature_id ?? '',

                "code"          => 200

            ]);

        }

        

    }
    public function logSignature(Request $request){
        $user_id = \Auth::user()->id;
        $log = Signaturelog::where('user_id',$user_id)->exists();
    //    $log = Signaturelog::where('user_id',$user_id)->select('created_at','signature_id')->get();
       if($log)

       {
        $Userlog = Signaturelog::with('user')->select('signature_id','user_id','created_at','image')->where('user_id',$user_id)->orderBy('id', 'desc')->get();
            // $Userlog = Signaturelog::join('users','users.id','=','log_signature.user_id')->where('log_signature.user_id',$user_id)->select('users.name','log_signature.signature_id','log_signature.image','log_signature.created_at')->get();

            return response()->json([
                "message"       => "Success",
                'Userlog'       =>  $Userlog ?? '',
                "code"          => 200
            ]);
        }
    }


    
   

    
     public function show_list()
     {
      
        $user = \Auth::user();

      if($user->role_id == 1){

        $totalId = NeedValidation::pluck('id');
        
        $latestData = Nvsericestatus::whereIn("nv_id", $totalId)->get();
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
      }elseif($user->role_id == 9){
        $totalId = NeedValidation::where('user_id',$user->id)->pluck('id');
        
                    $latestData = Nvsericestatus::whereIn("nv_id", $totalId)->get();
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
      }elseif($user->role_id == 11){

            $employees = Employee::where("user_id", $user->id)->first();
            $employeesss = Employee::where("department_id", $employees->department_id)->get();
          
            
            $id0 = Workflow::where("id", 1)->first();
            $id1 = Workflow::skip(1)->first();
            $id2 = Workflow::skip(2)->first();
            $id3 = Workflow::skip(3)->first();
            $id4 = Workflow::skip(4)->first();
            $id5 = Workflow::skip(5)->first();
          
                $departmentIds = explode(',', $employees->department_id);
                $departments = Department::whereIn("id", $departmentIds)->get();
                // dd($departments);
                foreach ($departments as $department) {
                    $dep_id = $department->id;
                    $hod = $department->dep_hod;
                    $rv1 = $department->dep_rew1;
                    $rv2 = $department->dep_rew2;
                    $rv3 = $department->dep_rew3;
                    $rv4 = $department->dep_rew4;
                    $group_cio = $department->group_cio;

            if (!empty($rv1) && $rv1 == $user->id) {
                $departmentIds = explode(',', $user->department_id);
                $totalId = NeedValidation::whereIn('department_id', $departmentIds)->pluck('id');
                $latest = Nvsericestatus::whereIn("nv_id", $totalId)->where("rv1_status", 2)->get();
                   
                }elseif (!empty($rv2) && $rv2 == $user->id) {
                    $departmentIds = explode(',', $user->department_id);
                    $totalId = NeedValidation::whereIn('department_id', $departmentIds)->pluck('id');
                    $latest = Nvsericestatus::whereIn("nv_id", $totalId)->where("rv2_status", 2)->get();
                }                    
                elseif (!empty($rv3) && $rv3 == $user->id) {
                    $departmentIds = explode(',', $user->department_id);
                    $totalId = NeedValidation::whereIn('department_id', $departmentIds)->pluck('id');
                    $latest = Nvsericestatus::whereIn("nv_id", $totalId)->where("rv3_status", 2)->get();

                }elseif (!empty($rv4) && $rv4 == $user->id) {
                    $departmentIds = explode(',', $user->department_id);
                    $totalId = NeedValidation::whereIn('department_id', $departmentIds)->pluck('id');
                    $latest = Nvsericestatus::whereIn("nv_id", $totalId)->where("rv4_status", 2)->get();
                  
           
                } elseif(!empty($hod) && $hod == $user->id) {
                    $departmentIds = explode(',', $user->department_id);
                    $totalId = NeedValidation::whereIn('department_id', $departmentIds)->where("delete_draft",0)->pluck('id');
                    $latest = Nvsericestatus::whereIn("nv_id", $totalId)->where("hod_status", 2)->get();
                    
                }elseif (!empty($id0->work_rew1) && $id0->work_rew1 == $user->id) {
                 
                    $totalId = NeedValidation::pluck('id');
                    $latest = Nvsericestatus::whereIn("nv_id", $totalId)->where('ces_rew1_status', 2)->get();
    
                } elseif (!empty($id0->work_rew2) && $id0->work_rew2 == $user->id) {
                    
                    $totalId = NeedValidation::pluck('id');
                    $latest = Nvsericestatus::whereIn("nv_id", $totalId)->where('ces_rew2_status', 2)->get();
    
                } elseif (!empty($id0->work_rew3) && $id0->work_rew3 == $user->id) {
                    
                    $totalId = NeedValidation::pluck('id');;
                    $latest = Nvsericestatus::whereIn("nv_id", $totalId)->where('ces_rew3_status', 2)->get();
    
                } elseif (!empty($id0->work_rew4) && $id0->work_rew4 == $user->id) {
                    
                    $totalId = NeedValidation::pluck('id');
                    $latest = Nvsericestatus::whereIn("nv_id", $totalId)->where('ces_rew4_status', 2)->get();
    
                } elseif (!empty($id0->approver) && $id0->approver == $user->id) {

                    $totalId = NeedValidation::pluck('id');
                    $latest = Nvsericestatus::whereIn("nv_id", $totalId)->where('ces_status', 2)->get();
              
    
                } elseif (!empty($id1->work_rew1) && $id1->work_rew1 == $user->id) {
                  
                    $totalId = NeedValidation::pluck('id');
                    $latest = Nvsericestatus::whereIn("nv_id", $totalId)->where('work_rew1_status', 2)->get();
             
                } elseif (!empty($id1->work_rew2) && $id1->work_rew2 == $user->id) {
                   
    
                    $totalId = NeedValidation::pluck('id');
                    $latest = Nvsericestatus::whereIn("nv_id", $totalId)->where('work_rew2_status', 2)->get();
                  
                } elseif (!empty($id1->work_rew3) && $id1->work_rew3 == $user->id) {
                    
    
                    $totalId = NeedValidation::pluck('id');
                    $latest = Nvsericestatus::whereIn("nv_id", $totalId)->where('work_rew3_status', 2)->get();
                 
                } elseif (!empty($id1->work_rew4) && $id1->work_rew4 == $user->id) {
                   
    
                    $totalId = NeedValidation::pluck('id');
                    $latest = Nvsericestatus::whereIn("nv_id", $totalId)->where('work_rew4_status', 2)->get();
                   
                } elseif (!empty($id1->approver) && $id1->approver == $user->id) {
                   
                    $totalId = NeedValidation::pluck('id');
                    $latest = Nvsericestatus::whereIn("nv_id", $totalId)->where('approver_status', 2)->get();
                   
               
                  }elseif (!empty($id2->work_rew1) && $id2->work_rew1 == $user->id) {
                  
                    $totalId = NeedValidation::pluck('id');
                    $latest = Nvsericestatus::whereIn("nv_id", $totalId)->where('work_rew1dep2_status', 2)->get();
                  
                } elseif (!empty($id2->work_rew2) && $id2->work_rew2 == $user->id) {
                  
    
                    $totalId = NeedValidation::pluck('id');
                    $latest = Nvsericestatus::whereIn("nv_id", $totalId)->where('work_rew2dep2_status', 2)->get();
                   
                } elseif (!empty($id2->work_rew3) && $id2->work_rew3 == $user->id) {
                   
                    $totalId = NeedValidation::pluck('id');
                    $latest = Nvsericestatus::whereIn("nv_id", $totalId)->where('work_rew3dep2_status', 2)->get();
                   
                } elseif (!empty($id2->work_rew4) && $id2->work_rew4 == $user->id) {
                   
                    $totalId = NeedValidation::pluck('id');
                    $latest = Nvsericestatus::whereIn("nv_id", $totalId)->where('work_rew4dep2_status', 2)->get();
                
                } elseif (!empty($id2->approver) && $id2->approver == $user->id) {
                    
    
                    $totalId = NeedValidation::pluck('id');
                    $latest = Nvsericestatus::whereIn("nv_id", $totalId)->where('cto_status', 2)->get();
                  
                } elseif (!empty($id3->work_rew1) && $id3->work_rew1 == $user->id) {
                   
    
                    $totalId = NeedValidation::pluck('id');
                    $latest = Nvsericestatus::whereIn("nv_id", $totalId)->where('work_rew1dep3_status', 2)->get();
                   
                } elseif (!empty($id3->work_rew2) && $id3->work_rew2 == $user->id) {
                   
                    $totalId = NeedValidation::pluck('id');
                    $latest = Nvsericestatus::whereIn("nv_id", $totalId)->where('work_rew2dep3_status', 2)->get();
                  
                } elseif (!empty($id3->work_rew3) && $id3->work_rew3 == $user->id) {
                   
                    $totalId = NeedValidation::pluck('id');
                    $latest = Nvsericestatus::whereIn("nv_id", $totalId)->where('work_rew3dep3_status', 2)->get();
                  
                } elseif (!empty($id3->work_rew4) && $id3->work_rew4 == $user->id) {
                  
    
                    $totalId = NeedValidation::pluck('id');
                    $latest = Nvsericestatus::whereIn("nv_id", $totalId)->where('work_rew4dep3_status', 2)->get();
                    
                } elseif (!empty($id3->approver) && $id3->approver == $user->id) {
                 
    
                    $totalId = NeedValidation::pluck('id');
                    $latest = Nvsericestatus::whereIn("nv_id", $totalId)->where('approverdep3_status', 2)->get();
                 
                }elseif (!empty($group_cio) && $group_cio == $user->id) { 
                    
                    $departmentIds = explode(',', $user->department_id);
                    $totalId = NeedValidation::whereIn('department_id', $departmentIds)->pluck('id');
                    $latest = Nvsericestatus::whereIn("nv_id", $totalId)->where('groupcio_status', 2)->get();
                } elseif (!empty($id4->work_rew1) && $id4->work_rew1 == $user->id) {
                  
                    $totalId = NeedValidation::pluck('id');
                    $latest = Nvsericestatus::whereIn("nv_id", $totalId)->where('work_rew1dep4_status', 2)->get();
                   
                } elseif (!empty($id4->work_rew2) && $id4->work_rew2 == $user->id) {
                   
                    $totalId = NeedValidation::pluck('id');
                    $latest = Nvsericestatus::whereIn("nv_id", $totalId)->where('work_rew2dep4_status', 2)->get();
                   
                } elseif (!empty($id4->work_rew3) && $id4->work_rew3 == $user->id) {
                   
                    $totalId = NeedValidation::pluck('id');
                    $latest = Nvsericestatus::whereIn("nv_id", $totalId)->where('work_rew3dep4_status', 2)->get();
                    
                } elseif (!empty($id4->work_rew4) && $id4->work_rew4 == $user->id) {
                  
    
                    $totalId = NeedValidation::pluck('id');
                    $latest = Nvsericestatus::whereIn("nv_id", $totalId)->where('work_rew4dep4_status', 2)->get();
                   
                } elseif (!empty($id4->approver) && $id4->approver == $user->id) {
                  
    
                    $totalId = NeedValidation::pluck('id');
                    $latest = Nvsericestatus::whereIn("nv_id", $totalId)->where('approverdep4_status', 2)->get();
                   
                
                   
                   
                }elseif (!empty($id5->approver) && $id5->approver == $user->id) {
                  
                    $totalId = NeedValidation::pluck('id');
                    $latest = Nvsericestatus::whereIn("nv_id", $totalId)->where('approverdep5_status', 2)->get();
                  
                    
                }
            }

      }
       
     
        return view("admin.reject_list",compact("latest"));
     } 
          
     Public function export_NV_pdf(Request $request)

    {
        $fiscal_year = $request->fiscal_year;
        $company_id = $request->company_id;
        $user = \Auth::user();
        $employees = Employee::where("user_id", $user->id)->first();
        $currentDate = Carbon::now();

        if ($currentDate->month >= 4) {
            $financialYearStart = Carbon::create($currentDate->year, 4, 1);
        } else {
            $financialYearStart = Carbon::create($currentDate->year - 1, 4, 1);
        }
        $financialYearEnd = $financialYearStart->copy()->addYear()->subDay();
        $currentFinancialYear = $financialYearStart->format('Y') . '-' . $financialYearEnd->format('y');
        
        if ($user->role_id == 1) {
           
            if($company_id){
            $nv_ids = NeedValidation::where('company_id',$company_id)->where('fiscal_year',$currentFinancialYear)->where('delete_draft',0)->pluck("id");
            }elseif($fiscal_year){
            $nv_ids = NeedValidation::where('fiscal_year',$fiscal_year)->where('delete_draft',0)->pluck("id");
            }else{
            $nv_ids = NeedValidation::where('fiscal_year',$currentFinancialYear)->where("delete_draft", 0)->pluck("id");
            }
            $nv_sm_data = Nvsericestatus::whereIn('nv_id', $nv_ids)->with(['service', 'material', 'user'])->orderBy('id', 'asc')->get();
            // $nv_sm_data = $nv_sm_data->get();
            $customPaper = array(0, 0, 1240, 1748);
            $pdf = PDF::loadView('admin.dashboard_pdf',["nv_sm_data"=>$nv_sm_data])->setPaper('a4', 'landscape');
            return $pdf->download('NV_pdf.pdf');
            
        }elseif($user->role_id == 9){

                if($fiscal_year){
                    $nv = NeedValidation::whereHas('service')->select('id')->where('user_id', $user->id)->where('fiscal_year',$fiscal_year)->where("delete_draft", 0)->get();
    
                }else{
                    $nv = NeedValidation::whereHas('service')->select('id')->where('user_id', $user->id)->where('fiscal_year',$currentFinancialYear)->where("delete_draft", 0)->get();
    
                }
                $nv_ids = $nv->pluck('id');
                $nv_sm_data = Nvsericestatus::whereIn('nv_id', $nv_ids)->with(['service', 'material', 'user'])->orderBy('id', 'asc')->get();
                // $nv_sm_data = $nv_sm_data->get();
                $customPaper = array(0, 0, 1240, 1748);
                $pdf = PDF::loadView('admin.dashboard_pdf',["nv_sm_data"=>$nv_sm_data])->setPaper('a4', 'landscape');
                return $pdf->download('NV_pdf.pdf');
            
        }elseif(!empty($user->role_id == 11)) {
            $id0 = Workflow::where("id",1)->first();
            $id1 = Workflow::skip(1)->first();
            $id2 = Workflow::skip(2)->first();
            $id3 = Workflow::skip(3)->first();
            $id4 = Workflow::skip(4)->first();
            $id5 = Workflow::skip(5)->first();
           
            $employee = Employee::where('user_id', $user->id)->with('department')->first();	
            $allusers = Employee::where('department_id', $employee->department_id)->get();	
            $allNormalUsers = $allusers->where('role_id', 9)->pluck('user_id');	
            $employees = Employee::where("user_id", $user->id)->first();
            $departmentIds = explode(',', $employees->department_id);
                    $departments = Department::whereIn("id", $departmentIds)->get();
                    foreach ($departments as $dep) {
                        $dep_id = $dep->id;
                        $hod = $dep->dep_hod;
                        $dep_rew1 = $dep->dep_rew1;
                        $dep_rew2 = $dep->dep_rew2;
                        $dep_rew3 = $dep->dep_rew3;
                        $dep_rew4 = $dep->dep_rew4;
                        $group_cio = $dep->group_cio;

            if (!empty($dep_rew1) && $dep_rew1 == $user->id) {	

                if($fiscal_year){
                    $nv = NeedValidation::where('fiscal_year', $fiscal_year)->where(function ($query) use ($user, $allNormalUsers, $departmentIds, $currentFinancialYear) {
                        $query->where('user_id', $user->id)
                              ->orWhereIn('user_id', $allNormalUsers)
                              ->orWhereIn('department_id', $departmentIds);
                    })
                    ->whereHas('service')
                    ->select('id')
                    ->get();  
                }else{
                    $nv = NeedValidation::where('fiscal_year', $currentFinancialYear)->where(function ($query) use ($user, $allNormalUsers, $departmentIds, $currentFinancialYear) {
                        $query->where('user_id', $user->id)
                              ->orWhereIn('user_id', $allNormalUsers)
                              ->orWhereIn('department_id', $departmentIds);
                    })
                    ->whereHas('service')
                    ->select('id')
                    ->get();  
                }
                       
                
                $nv_ids = $nv->pluck('id');	
                $nv_sm_data = Nvsericestatus::whereIn('nv_id', $nv_ids)->where('draft',1)->with(['service', 'material', 'user'])->orderBy('id', 'asc')->get();
                
               	
            }elseif(!empty($dep_rew2) && $dep_rew2 == $user->id){
                if($fiscal_year){
                    $nv = NeedValidation::where('fiscal_year', $fiscal_year)->where(function ($query) use ($user, $allNormalUsers, $departmentIds, $currentFinancialYear) {
                        $query->where('user_id', $user->id)
                              ->orWhereIn('user_id', $allNormalUsers)
                              ->orWhereIn('department_id', $departmentIds);
                    })
                    ->whereHas('service') 
                    ->select('id')
                    ->get();
                }else{
                    $nv = NeedValidation::where('fiscal_year', $currentFinancialYear)->where(function ($query) use ($user, $allNormalUsers, $departmentIds, $currentFinancialYear) {
                        $query->where('user_id', $user->id)
                              ->orWhereIn('user_id', $allNormalUsers)
                              ->orWhereIn('department_id', $departmentIds);
                    })
                    ->whereHas('service') 
                    ->select('id')
                    ->get();
                }
               
        
               $nv_ids = $nv->pluck('id');
                if(!empty($dep_rew1)){
                $nv_sm_data = Nvsericestatus::whereIn('nv_id', $nv_ids)->with(['service', 'material', 'user'])->where('rv1_status',1)->orderBy('id', 'asc')->get();
                }else{
                $nv_sm_data = Nvsericestatus::whereIn('nv_id', $nv_ids)->with(['service', 'material', 'user'])->where('draft',1)->orderBy('id', 'asc')->get();
                }
            }elseif(!empty($dep_rew3) && $dep_rew3 == $user->id){
                if($fiscal_year){
                    $nv = NeedValidation::where('fiscal_year', $fiscal_year)->where(function ($query) use ($user, $allNormalUsers, $departmentIds, $currentFinancialYear) {
                        $query->where('user_id', $user->id)
                              ->orWhereIn('user_id', $allNormalUsers)
                              ->orWhereIn('department_id', $departmentIds);
                    })
                    ->whereHas('service') 
                    ->select('id')
                    ->get();
                }else{
                    $nv = NeedValidation::where('fiscal_year', $currentFinancialYear)->where(function ($query) use ($user, $allNormalUsers, $departmentIds, $currentFinancialYear) {
                        $query->where('user_id', $user->id)
                              ->orWhereIn('user_id', $allNormalUsers)
                              ->orWhereIn('department_id', $departmentIds);
                    })
                    ->whereHas('service')
                    ->select('id')
                    ->get();
                }
              
                $nv_ids = $nv->pluck('id');
                if(!empty($dep_rew2)){
                   $nv_sm_data = Nvsericestatus::whereIn('nv_id', $nv_ids)->with(['service', 'material', 'user'])
                    ->where('rv2_status',1)
                    ->orderBy('id', 'asc')->get();
                 
                }elseif(!empty($dep_rew1)){
                   $nv_sm_data = Nvsericestatus::whereIn('nv_id', $nv_ids)->with(['service', 'material', 'user'])
                    ->where('rv1_status',1)
                    ->orderBy('id', 'asc')->get();
                  
                
                }else{
                   $nv_sm_data = Nvsericestatus::whereIn('nv_id', $nv_ids)->with(['service', 'material', 'user'])
                    ->where('draft',1)
                    ->orderBy('id', 'asc')->get();
                  
                }
            }elseif(!empty($dep_rew4) && $dep_rew4 == $user->id){
                if($fiscal_year){
                    $nv = NeedValidation::where('fiscal_year', $fiscal_year)->where(function ($query) use ($user, $allNormalUsers, $departmentIds, $currentFinancialYear) {
                        $query->where('user_id', $user->id)
                              ->orWhereIn('user_id', $allNormalUsers)
                              ->orWhereIn('department_id', $departmentIds);
                    })
                    ->whereHas('service') 
                    ->select('id')
                    ->get();  
                }else{
                    $nv = NeedValidation::where('fiscal_year', $currentFinancialYear)->where(function ($query) use ($user, $allNormalUsers, $departmentIds, $currentFinancialYear) {
                        $query->where('user_id', $user->id)
                              ->orWhereIn('user_id', $allNormalUsers)
                              ->orWhereIn('department_id', $departmentIds);
                    })
                    ->whereHas('service') 
                    ->select('id')
                    ->get();  
                }
                   $nv_ids = $nv->pluck('id');
                if(!empty($dep_rew3)){
                   $nv_sm_data = Nvsericestatus::whereIn('nv_id', $nv_ids)->with(['service', 'material', 'user'])
                    ->where('rv3_status',1)
                    ->orderBy('id', 'asc')->get();
                
                }elseif(!empty($dep_rew2)){
                   $nv_sm_data = Nvsericestatus::whereIn('nv_id', $nv_ids)->with(['service', 'material', 'user'])
                    ->where('rv2_status',1)
                    ->orderBy('id', 'asc')->get();
                  
                }elseif(!empty($dep_rew1)){
                   $nv_sm_data = Nvsericestatus::whereIn('nv_id', $nv_ids)->with(['service', 'material', 'user'])
                    ->where('rv1_status',1)
                    ->orderBy('id', 'asc')->get();
                  
                }else{
                     $nv_sm_data = Nvsericestatus::whereIn('nv_id', $nv_ids)->with(['service', 'material', 'user'])
                    ->where('draft',1)
                    ->orderBy('id', 'asc')->get();
                   
                }
            }elseif(!empty($hod) && $hod == $user->id){
                $nv_sm_data = array();
                $nv_statuses = array();
                $nv_id = [];
        
               $depart = Department::whereIn('id', $departmentIds)->get();
        
               foreach ($depart as $depart) {
                $dep_rew4 = $depart->dep_rew4;
                $dep_rew3 = $depart->dep_rew3;
                $dep_rew2 = $depart->dep_rew2;
                $dep_rew1 = $depart->dep_rew1;
                $dep_hod = $depart->dep_hod;
                if(!empty($dep_hod)){
                if(!empty($dep_rew4)){
                    $nv_status = Nvsericestatus::where(function ($query) {
                        $query->where('rv4_status', 1);
                           
                    })
                        ->where(function ($query) {
                            $query
                                ->where('rv4_status', '!=', 2);
                        })
                        ->get();
                        if(count($nv_status) > 0) {
                            $nv_statuses[] = $nv_status;
                           
                            
                        }
               
              
            }elseif(!empty($dep_rew3)){
                $nv_status = Nvsericestatus::where(function ($query) {
                    $query->where('rv3_status', 1);
                       
                })
                    ->where(function ($query) {
                        $query
                            ->where('rv3_status', '!=', 2);
                    })
                    ->get();
                    if(count($nv_status) > 0) {
                        $nv_statuses[] = $nv_status;
                        
                        
                    }
              
            }elseif(!empty($dep_rew2)){
               // print_r("2");
               $nv_status = Nvsericestatus::where(function ($query) {
                $query->where('rv2_status', 1);
                  
            })
                ->where(function ($query) {
                    $query
                        ->where('rv2_status', '!=', 2);
                })
                ->get();
                if(count($nv_status) > 0) {
                    $nv_statuses[] = $nv_status;
                    
                    
                }
              
        
            }elseif(!empty($dep_rew1)){
                 // print_r("1");
                 $nv_status = Nvsericestatus::where(function ($query) {
                    $query->where('rv1_status', 1);
                })
                    ->where(function ($query) {
                        $query->where('rv1_status', '!=', 2);
                    })
                    ->get();  
                // print_r($nv_status);
                if(count($nv_status) > 0) {
                    $nv_statuses[] = $nv_status;
                    // print_r("here");
                    
                }
              
              
            }else{
                $nv_status = Nvsericestatus::join('needvalidations','needvalidations.id','=','nvservicestatus.nv_id')
                ->join('department','department.id','=','needvalidations.department_id')
                ->where('department.id',$depart->id)
                ->where('nvservicestatus.draft',1)
                ->get();
               
                if(count($nv_status) > 0) {
                    $nv_statuses[] = $nv_status;
                }
               
              
            }
        }
         
               }
               if(!empty($nv_statuses)) {
                foreach ($nv_statuses as $idx => $nv_status) {
                    foreach($nv_status as $data) {
                        array_push($nv_id, $data["nv_id"]);
                    }
                }
                if($fiscal_year){
                $nv1 = NeedValidation::where('fiscal_year', $fiscal_year)->where('delete_draft',0)
                ->whereIn('department_id', $departmentIds)->whereIn("id", $nv_id)->pluck('id');
                }else{
                $nv1 = NeedValidation::where('fiscal_year', $currentFinancialYear)->where('delete_draft',0)
                ->whereIn('department_id', $departmentIds)->whereIn("id", $nv_id)->pluck('id'); 
                }
                $nv_sm_data = Nvsericestatus::whereIn('nv_id', $nv1)->with(['service', 'material', 'user'])->orderBy('id', 'asc')->get();
              
              }
            //     if($fiscal_year){
            //         $nv = NeedValidation::where('fiscal_year', $fiscal_year)->where(function ($query) use ($user, $allNormalUsers, $departmentIds, $currentFinancialYear) {
            //             $query->where('user_id', $user->id)
            //                   ->orWhereIn('user_id', $allNormalUsers)
            //                   ->orWhereIn('department_id', $departmentIds);
            //         })
            //         ->whereHas('service') 
            //         ->select('id')
            //         ->get();
            //     }else{
            //         $nv = NeedValidation::where('fiscal_year', $currentFinancialYear)->where(function ($query) use ($user, $allNormalUsers, $departmentIds, $currentFinancialYear) {
            //             $query->where('user_id', $user->id)
            //                   ->orWhereIn('user_id', $allNormalUsers)
            //                   ->orWhereIn('department_id', $departmentIds);
            //         })
            //         ->whereHas('service') 
            //         ->select('id')
            //         ->get();
            //     }
               
            //    $nv_ids = $nv->pluck('id');
               
            //     if(!empty($dep_rew4)){
            //       $nv_sm_data = Nvsericestatus::whereIn('nv_id', $nv_ids)->with(['service', 'material', 'user'])
            //         ->where('rv4_status',1)
            //         ->orderBy('id', 'asc');
                 
            //     }elseif(!empty($dep_rew3)){
            //        $nv_sm_data = Nvsericestatus::whereIn('nv_id', $nv_ids)->with(['service', 'material', 'user'])
            //         ->where('rv3_status',1)
            //         ->orderBy('id', 'asc');
               
            //     }elseif(!empty($dep_rew2)){
            //        $nv_sm_data = Nvsericestatus::whereIn('nv_id', $nv_ids)->with(['service', 'material', 'user'])
            //         ->where('rv2_status',1)
            //         ->orderBy('id', 'asc');
                 
            //     }elseif(!empty($dep_rew1)){
            //        $nv_sm_data = Nvsericestatus::whereIn('nv_id', $nv_ids)->with(['service', 'material', 'user'])
            //         ->where('rv1_status',1)
            //         ->orderBy('id', 'asc');
                  
            //     }else{
            //          $nv_sm_data = Nvsericestatus::whereIn('nv_id', $nv_ids)->with(['service', 'material', 'user'])
            //         ->where('draft',1)
            //         ->orderBy('id', 'asc');
                   
                  
            //     }
            }elseif((!empty($id0->work_rew1) && $id0->work_rew1 == $user->id) || (!empty($id0->work_rew2) && $id0->work_rew2 == $user->id) ||
            (!empty($id0->work_rew3) && $id0->work_rew3 == $user->id) || (!empty($id0->work_rew4) && $id0->work_rew4 == $user->id)){

                if($fiscal_year){
                $nv = NeedValidation::whereHas('service')->select('id')->where('fiscal_year', $fiscal_year)
                ->get();
                }else{
                $nv = NeedValidation::whereHas('service')->select('id')->where('fiscal_year', $currentFinancialYear)
                ->get();
                }
                $nv_ids = $nv->pluck('id');

                $nv_sm_data = Nvsericestatus::whereIn('nv_id', $nv_ids)->with(['service', 'material', 'user'])
                ->where('derc_info',1)
                ->where('hod_status',1)
                ->orderBy('id', 'asc')->get();

            }elseif(!empty($id0->approver) && $id0->approver == $user->id){
                if($fiscal_year){
                    $nv = NeedValidation::whereHas('service')->select('id')->where('fiscal_year', $fiscal_year)
                   ->get();
                }else{
                    $nv = NeedValidation::whereHas('service')->select('id')->where('fiscal_year', $currentFinancialYear)
                   ->get();
                }
               $nv_ids = $nv->pluck('id');

             if(!empty($id0->work_rew1) || !empty($id0->work_rew2)|| !empty($id0->work_rew3)|| !empty($id0->work_rew4)){
                $nv_sm_data = Nvsericestatus::whereIn('nv_id', $nv_ids)->with(['service', 'material', 'user'])->where(function ($query) {
                    $query->orWhere('ces_rew1_status', 1)
                        ->orWhere('ces_rew2_status', 1)
                        ->orWhere('ces_rew3_status', 1)
                        ->orWhere('ces_rew4_status', 1);
                })
                    ->where(function ($query) {
                        $query->where('ces_rew1_status', '!=', 2)
                            ->where('ces_rew2_status', '!=', 2)
                            ->where('ces_rew3_status', '!=', 2)
                            ->where('ces_rew4_status', '!=', 2);
                    })
                    ->where(function ($query) {
                        $query->where('derc_info', 1);
                    })
                    ->orderBy('id', 'asc')->get();
                }elseif(empty($id0->work_rew1) && empty($id0->work_rew2) && empty($id0->work_rew3) && empty($id0->work_rew4)){
                    $nv_sm_data = Nvsericestatus::whereIn('nv_id', $nv_ids)->with(['service', 'material', 'user'])->where(function ($query) {
                        $query
                        ->where('hod_status', 1);
                    })
                    ->where(function ($query) {
                        $query ->where('hod_status', '!=', 2) ;
                    })
                    ->where(function ($query) {
                        $query->where('derc_info', 1);
                    })
                        ->orderBy('id', 'asc')->get();
                }
            }elseif((!empty($id1->work_rew1) && $id1->work_rew1 == $user->id) || (!empty($id1->work_rew2) && $id1->work_rew2 == $user->id) ||
            (!empty($id1->work_rew3) && $id1->work_rew3 == $user->id) || (!empty($id1->work_rew4) && $id1->work_rew4 == $user->id)){
                if($fiscal_year){
                    $nv = NeedValidation::whereHas('service')->select('id')->where('fiscal_year', $fiscal_year)
                   ->get();
                }else{
                   $nv = NeedValidation::whereHas('service')->select('id')->where('fiscal_year', $currentFinancialYear)
                   ->get();
                }
                $nv_ids = $nv->pluck('id');
            
                $nv_sm_data = Nvsericestatus::whereIn('nv_id', $nv_ids)
                ->with(['service', 'material', 'user'])
                ->where(function ($query) {
                    $query->where('ces_status', 1)
                        ->orWhere('hod_status', 1)
                        ->where('derc_info', 0);
                })
                ->orderBy('id', 'asc')->get();
            }elseif(!empty($id1->approver) && $id1->approver == $user->id){
                if($fiscal_year){
                    $nv = NeedValidation::whereHas('service')->select('id')->where('fiscal_year', $fiscal_year)
                   ->get();
                }else{
                    $nv = NeedValidation::whereHas('service')->select('id')->where('fiscal_year', $currentFinancialYear)
                   ->get();
                }                
                $nv_ids = $nv->pluck('id');
                if(!empty($id1->work_rew1) || !empty($id1->work_rew2)|| !empty($id1->work_rew3)|| !empty($id1->work_rew4)){
                    $nv_sm_data = Nvsericestatus::whereIn('nv_id', $nv_ids)
                    ->with(['service', 'material', 'user'])
                    ->where(function ($query) {
                        $query->where(function ($subQuery) {
                            $subQuery->orWhere('work_rew1_status', 1)
                                ->orWhere('work_rew2_status', 1)
                                ->orWhere('work_rew3_status', 1)
                                ->orWhere('work_rew4_status', 1);
                        })->where(function ($subQuery) {
                            $subQuery->where('work_rew1_status', '!=', 2)
                                ->where('work_rew2_status', '!=', 2)
                                ->where('work_rew3_status', '!=', 2)
                                ->where('work_rew4_status', '!=', 2);
                        })->whereIn('derc_info', [0, 1]);
                    })
                    ->orderBy('id', 'asc')->get();
                    }elseif(empty($id1->work_rew1) && empty($id1->work_rew2) && empty($id1->work_rew3) && empty($id1->work_rew4)){
                        $nv_sm_data = Nvsericestatus::whereIn('nv_id', $nv_ids)
                        ->with(['service', 'material', 'user'])
                        ->where(function ($query) {
                            $query->where('ces_status', 1)
                                ->orWhere('hod_status', 1)
                                ->where('derc_info', 0);
                        })
                        ->orderBy('id', 'asc')->get();
                    }
            }elseif((!empty($id2->work_rew1) && $id2->work_rew1 == $user->id) || (!empty($id2->work_rew2) && $id2->work_rew2 == $user->id) ||
            (!empty($id2->work_rew3) && $id2->work_rew3 == $user->id) || (!empty($id2->work_rew4) && $id2->work_rew4 == $user->id)){
                if($fiscal_year){
                    $nv = NeedValidation::whereHas('service')->select('id')->where('fiscal_year', $fiscal_year)
                   ->get();
                }else{
                    $nv = NeedValidation::whereHas('service')->select('id')->where('fiscal_year', $currentFinancialYear)
                   ->get();
                }
                $nv_ids = $nv->pluck('id');
                    $nv_sm_data = Nvsericestatus::whereIn('nv_id', $nv_ids)->with(['service', 'material', 'user'])->where(function ($query) {
                        $query->where('check_technology', 1);
                    })->where(function ($query) {
                        $query->where('approver_status', 1);
                    })
                ->orderBy('id', 'asc')->get();
            }elseif(!empty($id2->approver) && $id2->approver == $user->id){
                if($fiscal_year){
                    $nv = NeedValidation::whereHas('service')->select('id')->where('fiscal_year', $fiscal_year)
                    ->get();
                }else{
                   $nv = NeedValidation::whereHas('service')->select('id')->where('fiscal_year', $currentFinancialYear)
                  ->get();
                }
                $nv_ids = $nv->pluck('id');

                if(!empty($id2->work_rew1) || !empty($id2->work_rew2)|| !empty($id2->work_rew3)|| !empty($id2->work_rew4)){
                    $nv_sm_data = Nvsericestatus::whereIn('nv_id', $nv_ids)->with(['service', 'material', 'user'])
                    ->where(function ($query) {
                        $query->orWhere('work_rew1dep2_status', 1)
                            ->orWhere('work_rew2dep2_status', 1)
                            ->orWhere('work_rew3dep2_status', 1)
                            ->orWhere('work_rew4dep2_status', 1);
                    })
                        ->where(function ($query) {
                            $query->where('work_rew1dep2_status', '!=', 2)
                                ->where('work_rew2dep2_status', '!=', 2)
                                ->where('work_rew3dep2_status', '!=', 2)
                                ->where('work_rew4dep2_status', '!=', 2);
                        })
                        ->where(function ($query) {
                            $query->where('check_technology', 1);
                        })
                        ->orderBy('id', 'asc')->get();
                    }elseif(empty($id2->work_rew1) && empty($id2->work_rew2) && empty($id2->work_rew3) && empty($id2->work_rew4)){
                        $nv_sm_data = Nvsericestatus::whereIn('nv_id', $nv_ids)->with(['service', 'material', 'user'])
                    ->where(function ($query) {
                        $query->where('check_technology', 1);
                    })->where(function ($query) {
                        $query->where('approver_status', 1);
                    })
                            ->orderBy('id', 'asc')->get();
                    }
        
            }elseif((!empty($id3->work_rew1) && $id3->work_rew1 == $user->id) || (!empty($id3->work_rew2) && $id3->work_rew2 == $user->id) ||
            (!empty($id3->work_rew3) && $id3->work_rew3 == $user->id) || (!empty($id3->work_rew4) && $id3->work_rew4 == $user->id)){
                if($fiscal_year){
                    $nv = NeedValidation::whereHas('service')->select('id')->where('fiscal_year', $fiscal_year)
                    ->get();
                }else{
                    $nv = NeedValidation::whereHas('service')->select('id')->where('fiscal_year', $currentFinancialYear)
                    ->get();
                }
              
                $nv_ids = $nv->pluck('id');
                    $nv_sm_data = Nvsericestatus::whereIn('nv_id', $nv_ids)->with(['service', 'material', 'user'])
                    ->where(function ($query) {
                        $query->where('approverdep2_status', 1)
                            ->orWhere('approver_status', 1)
                            ->where('check_technology', 0);
                    })
                 ->orderBy('id', 'asc')->get();
            }elseif(!empty($id3->approver) && $id3->approver == $user->id){
                if($fiscal_year){
                    $nv = NeedValidation::whereHas('service')->select('id')->where('fiscal_year', $fiscal_year)
                    ->get();
                }else{
                    $nv = NeedValidation::whereHas('service')->select('id')->where('fiscal_year', $currentFinancialYear)
                    ->get();
                }
              
                $nv_ids = $nv->pluck('id');
                if(!empty($id3->work_rew1) || !empty($id3->work_rew2)|| !empty($id3->work_rew3)|| !empty($id3->work_rew4)){
                    $nv_sm_data = Nvsericestatus::whereIn('nv_id', $nv_ids)->with(['service', 'material', 'user'])
                    ->where(function ($query) {
                        $query->where(function ($subQuery) {
                            $subQuery->orWhere('work_rew1dep3_status', 1)
                                ->orWhere('work_rew2dep3_status', 1)
                                ->orWhere('work_rew3dep3_status', 1)
                                ->orWhere('work_rew4dep3_status', 1);
                        })->where(function ($subQuery) {
                            $subQuery->where('work_rew1dep3_status', '!=', 2)
                                ->orWhere('work_rew2dep3_status', '!=', 2)
                                ->orWhere('work_rew3dep3_status', '!=', 2)
                                ->orWhere('work_rew4dep3_status', '!=', 2);
                        })->whereIn('check_technology', [0, 1]);
                    })
                ->orderBy('id', 'asc')->get();
                    }elseif(empty($id3->work_rew1) && empty($id3->work_rew2) && empty($id3->work_rew3) && empty($id3->work_rew4)){
                        $nv_sm_data = Nvsericestatus::whereIn('nv_id', $nv_ids)->with(['service', 'material', 'user'])
                        ->where(function ($query) {
                            $query->where('approverdep2_status', 1)
                                ->orWhere('approver_status', 1)
                                ->where('check_technology', 0);
                        })
                    ->orderBy('id', 'asc')->get();
                    }
                }elseif(!empty($group_cio) && $group_cio == $user->id) {
                    $departmentIds = explode(',', $user->department_id);
                    if($fiscal_year){
                        $nv = NeedValidation::whereHas('service')->select('id')->whereIn('department_id', $departmentIds)->where('fiscal_year', $fiscal_year)
                        ->get();
                    }else{
                        $nv = NeedValidation::whereHas('service')->select('id')->whereIn('department_id', $departmentIds)->where('fiscal_year', $currentFinancialYear)
                    ->get();
                    }
                   
                    $nv_ids = $nv->pluck('id');
                
                     $nv_sm_data = Nvsericestatus::whereIn('nv_id', $nv_ids)->with(['service', 'material', 'user'])
                    ->where('approverdep3_status', 1)
                    ->orderBy('id', 'asc')->get();
            }elseif((!empty($id4->work_rew1) && $id4->work_rew1 == $user->id) || (!empty($id4->work_rew2) && $id4->work_rew2 == $user->id) ||
            (!empty($id4->work_rew3) && $id4->work_rew3 == $user->id) || (!empty($id4->work_rew4) && $id4->work_rew4 == $user->id)){
                $nvid = [];
                $nvstatus = Nvsericestatus::get();
                foreach ($nvstatus as $nvstatus) {
                    array_push($nvid, $nvstatus["nv_id"]);
                }
                $user_nv = NeedValidation::with("division", "service")
                    ->whereIn("id", $nvid)
                    ->orderBy("id", "desc")
                    ->first();
            if(!empty( $user_nv)){
                $employees = Employee::where("user_id", $user_nv->user_id)->first();
                $department = Department::where("id", $employees->department_id)->first();
                $group_cio = $department->group_cio;
            }
                if($fiscal_year){
                    $nv = NeedValidation::whereHas('service')->select('id')->where('fiscal_year', $fiscal_year)
                    ->get();
                }else{
                    $nv = NeedValidation::whereHas('service')->select('id')->where('fiscal_year', $currentFinancialYear)
                    ->get();
                }
              
                $nv_ids = $nv->pluck('id');
                if(!empty( $group_cio)){
                 $nv_sm_data = Nvsericestatus::whereIn('nv_id', $nv_ids)->with(['service', 'material', 'user'])
                 ->where('groupcio_status', 1)
                ->orderBy('id', 'asc')->get();
                }else{
                    $nv_sm_data = Nvsericestatus::whereIn('nv_id', $nv_ids)->with(['service', 'material', 'user'])
                    ->where('approverdep3_status', 1)
                   ->orderBy('id', 'asc')->get();
                }
            }elseif(!empty($id4->approver) && $id4->approver == $user->id){
                $nvid = [];
                $nvstatus = Nvsericestatus::get();
                foreach ($nvstatus as $nvstatus) {
                    array_push($nvid, $nvstatus["nv_id"]);
                }
                $user_nv = NeedValidation::with("division", "service")
                    ->whereIn("id", $nvid)
                    ->orderBy("id", "desc")
                    ->first();
            if(!empty( $user_nv)){
                $employees = Employee::where("user_id", $user_nv->user_id)->first();
                $department = Department::where("id", $employees->department_id)->first();
                $group_cio = $department->group_cio;
            }
                if($fiscal_year){
                    $nv = NeedValidation::whereHas('service')->select('id')->where('fiscal_year', $fiscal_year)
                ->get();
                }else{
                    $nv = NeedValidation::whereHas('service')->select('id')->where('fiscal_year', $currentFinancialYear)
                ->get();
                }
              
                $nv_ids = $nv->pluck('id');
                if(!empty($id4->work_rew1) || !empty($id4->work_rew2)|| !empty($id4->work_rew3)|| !empty($id4->work_rew4)){
                    if(!empty( $group_cio)){
                 $nv_sm_data = Nvsericestatus::whereIn('nv_id', $nv_ids)->with(['service', 'material', 'user'])
                 ->where('groupcio_status', 1)
                 ->where(function ($query) {
                    $query->orWhere('work_rew1dep4_status', 1)
                        ->orWhere('work_rew2dep4_status', 1)
                        ->orWhere('work_rew3dep4_status', 1)
                        ->orWhere('work_rew4dep4_status', 1);
                })
                    ->where(function ($query) {
                        $query->where('work_rew1dep4_status', '!=', 2)
                            ->where('work_rew2dep4_status', '!=', 2)
                            ->where('work_rew3dep4_status', '!=', 2)
                            ->where('work_rew4dep4_status', '!=', 2);
                    })
                
                ->orderBy('id', 'asc')->get();
                    }else{
                        $nv_sm_data = Nvsericestatus::whereIn('nv_id', $nv_ids)->with(['service', 'material', 'user'])
                 ->where('approverdep3_status', 1)
                 ->where(function ($query) {
                    $query->orWhere('work_rew1dep4_status', 1)
                        ->orWhere('work_rew2dep4_status', 1)
                        ->orWhere('work_rew3dep4_status', 1)
                        ->orWhere('work_rew4dep4_status', 1);
                })
                    ->where(function ($query) {
                        $query->where('work_rew1dep4_status', '!=', 2)
                            ->where('work_rew2dep4_status', '!=', 2)
                            ->where('work_rew3dep4_status', '!=', 2)
                            ->where('work_rew4dep4_status', '!=', 2);
                    })
                
                ->orderBy('id', 'asc')->get();
                    }
                }elseif(empty($id4->work_rew1) && empty($id4->work_rew2) && empty($id4->work_rew3) && empty($id4->work_rew4)){
                    if(!empty( $group_cio)){
                    $nv_sm_data = Nvsericestatus::whereIn('nv_id', $nv_ids)->with(['service', 'material', 'user'])
                    ->where('groupcio_status', 1)
                    
                       ->where(function ($query) {
                           $query->where('groupcio_status', '!=', 2) ;
                       })
                  
                   ->orderBy('id', 'asc')->get();
                    }else{
                        $nv_sm_data = Nvsericestatus::whereIn('nv_id', $nv_ids)->with(['service', 'material', 'user'])
                    ->where('approverdep3_status', 1)
                    
                       ->where(function ($query) {
                           $query->where('approverdep3_status', '!=', 2) ;
                       })
                  
                   ->orderBy('id', 'asc')->get();
                    }
                    }
           
            }elseif(!empty($id5->approver) && $id5->approver == $user->id){
       
                if($fiscal_year) {
                    $nv = NeedValidation::whereHas('service')->select('id')->where('fiscal_year', $fiscal_year)
                    ->get();

                 }else{
                    $nv = NeedValidation::whereHas('service')->select('id')->where('fiscal_year', $currentFinancialYear)
                    ->get();

                 }
             
                $nv_ids = $nv->pluck('id');
             
                    $nv_sm_data = Nvsericestatus::whereIn('nv_id', $nv_ids)->with(['service', 'material', 'user'])
                    ->where('approverdep4_status', 1)
                    ->orderBy('id', 'asc')->get();
              
            }
         
         }
        //  $nv_sm_data = $nv_sm_data->get();
         $customPaper = array(0, 0, 1240, 1748);
         $pdf = PDF::loadView('admin.dashboard_pdf',["nv_sm_data"=>$nv_sm_data])->setPaper('a4', 'landscape');
         return $pdf->download('NV_pdf.pdf');
        }
        
     
    }

    public function export_NV_excel(Request $request)
    {
        $fiscal_year = $request->fiscal_year;
        $company_id = $request->company_id;
        $user = \Auth::user();
        $employees = Employee::where("user_id", $user->id)->first();
        $currentDate = Carbon::now();

        if ($currentDate->month >= 4) {
            $financialYearStart = Carbon::create($currentDate->year, 4, 1);
        } else {
            $financialYearStart = Carbon::create($currentDate->year - 1, 4, 1);
        }
        $financialYearEnd = $financialYearStart->copy()->addYear()->subDay();
        $currentFinancialYear = $financialYearStart->format('Y') . '-' . $financialYearEnd->format('y');
        
        if ($user->role_id == 1) {
           
            if($company_id){
            $nv_ids = NeedValidation::where('company_id',$company_id)->where('fiscal_year',$currentFinancialYear)->where('delete_draft',0)->pluck("id");
            }elseif($fiscal_year){
            $nv_ids = NeedValidation::where('fiscal_year',$fiscal_year)->where('delete_draft',0)->pluck("id");
            }else{
            $nv_ids = NeedValidation::where('fiscal_year',$currentFinancialYear)->where("delete_draft", 0)->pluck("id");
            }
            $nv_sm_data = Nvsericestatus::whereIn('nv_id', $nv_ids)->with(['service', 'material', 'user'])->orderBy('id', 'asc')->get();

        }elseif($user->role_id == 9){

            if($fiscal_year){
                $nv = NeedValidation::whereHas('service')->select('id')->where('user_id', $user->id)->where('fiscal_year',$fiscal_year)->where("delete_draft", 0)->get();

            }else{
                $nv = NeedValidation::whereHas('service')->select('id')->where('user_id', $user->id)->where('fiscal_year',$currentFinancialYear)->where("delete_draft", 0)->get();

            }
            $nv_ids = $nv->pluck('id');
            $nv_sm_data = Nvsericestatus::whereIn('nv_id', $nv_ids)->with(['service', 'material', 'user'])->orderBy('id', 'asc')->get();
        }
        elseif(!empty($user->role_id == 11)) {
            $id0 = Workflow::where("id",1)->first();
            $id1 = Workflow::skip(1)->first();
            $id2 = Workflow::skip(2)->first();
            $id3 = Workflow::skip(3)->first();
            $id4 = Workflow::skip(4)->first();
            $id5 = Workflow::skip(5)->first();
           
            $employee = Employee::where('user_id', $user->id)->with('department')->first();	
            $allusers = Employee::where('department_id', $employee->department_id)->get();	
            $allNormalUsers = $allusers->where('role_id', 9)->pluck('user_id');	
            $employees = Employee::where("user_id", $user->id)->first();
            $departmentIds = explode(',', $employees->department_id);
                    $departments = Department::whereIn("id", $departmentIds)->get();
                    foreach ($departments as $dep) {
                        $dep_id = $dep->id;
                        $hod = $dep->dep_hod;
                        $dep_rew1 = $dep->dep_rew1;
                        $dep_rew2 = $dep->dep_rew2;
                        $dep_rew3 = $dep->dep_rew3;
                        $dep_rew4 = $dep->dep_rew4;
                        $group_cio = $dep->group_cio;

            if (!empty($dep_rew1) && $dep_rew1 == $user->id) {	

                if($fiscal_year){
                    $nv = NeedValidation::where('fiscal_year', $fiscal_year)->where(function ($query) use ($user, $allNormalUsers, $departmentIds, $currentFinancialYear) {
                        $query->where('user_id', $user->id)
                              ->orWhereIn('user_id', $allNormalUsers)
                              ->orWhereIn('department_id', $departmentIds);
                    })
                    ->whereHas('service')
                    ->select('id')
                    ->get();  
                }else{
                    $nv = NeedValidation::where('fiscal_year', $currentFinancialYear)->where(function ($query) use ($user, $allNormalUsers, $departmentIds, $currentFinancialYear) {
                        $query->where('user_id', $user->id)
                              ->orWhereIn('user_id', $allNormalUsers)
                              ->orWhereIn('department_id', $departmentIds);
                    })
                    ->whereHas('service')
                    ->select('id')
                    ->get();  
                }
                       
                
                $nv_ids = $nv->pluck('id');	
                $nv_sm_data = Nvsericestatus::whereIn('nv_id', $nv_ids)->where('draft',1)->with(['service', 'material', 'user'])->orderBy('id', 'asc')->get();
                
               	
            }elseif(!empty($dep_rew2) && $dep_rew2 == $user->id){
                if($fiscal_year){
                    $nv = NeedValidation::where('fiscal_year', $fiscal_year)->where(function ($query) use ($user, $allNormalUsers, $departmentIds, $currentFinancialYear) {
                        $query->where('user_id', $user->id)
                              ->orWhereIn('user_id', $allNormalUsers)
                              ->orWhereIn('department_id', $departmentIds);
                    })
                    ->whereHas('service') 
                    ->select('id')
                    ->get();
                }else{
                    $nv = NeedValidation::where('fiscal_year', $currentFinancialYear)->where(function ($query) use ($user, $allNormalUsers, $departmentIds, $currentFinancialYear) {
                        $query->where('user_id', $user->id)
                              ->orWhereIn('user_id', $allNormalUsers)
                              ->orWhereIn('department_id', $departmentIds);
                    })
                    ->whereHas('service') 
                    ->select('id')
                    ->get();
                }
               
        
               $nv_ids = $nv->pluck('id');
                if(!empty($dep_rew1)){
                $nv_sm_data = Nvsericestatus::whereIn('nv_id', $nv_ids)->with(['service', 'material', 'user'])->where('rv1_status',1)->orderBy('id', 'asc')->get();
                }else{
                $nv_sm_data = Nvsericestatus::whereIn('nv_id', $nv_ids)->with(['service', 'material', 'user'])->where('draft',1)->orderBy('id', 'asc')->get();
                }
            }elseif(!empty($dep_rew3) && $dep_rew3 == $user->id){
                if($fiscal_year){
                    $nv = NeedValidation::where('fiscal_year', $fiscal_year)->where(function ($query) use ($user, $allNormalUsers, $departmentIds, $currentFinancialYear) {
                        $query->where('user_id', $user->id)
                              ->orWhereIn('user_id', $allNormalUsers)
                              ->orWhereIn('department_id', $departmentIds);
                    })
                    ->whereHas('service') 
                    ->select('id')
                    ->get();
                }else{
                    $nv = NeedValidation::where('fiscal_year', $currentFinancialYear)->where(function ($query) use ($user, $allNormalUsers, $departmentIds, $currentFinancialYear) {
                        $query->where('user_id', $user->id)
                              ->orWhereIn('user_id', $allNormalUsers)
                              ->orWhereIn('department_id', $departmentIds);
                    })
                    ->whereHas('service')
                    ->select('id')
                    ->get();
                }
              
                $nv_ids = $nv->pluck('id');
                if(!empty($dep_rew2)){
                   $nv_sm_data = Nvsericestatus::whereIn('nv_id', $nv_ids)->with(['service', 'material', 'user'])
                    ->where('rv2_status',1)
                    ->orderBy('id', 'asc')->get();
                 
                }elseif(!empty($dep_rew1)){
                   $nv_sm_data = Nvsericestatus::whereIn('nv_id', $nv_ids)->with(['service', 'material', 'user'])
                    ->where('rv1_status',1)
                    ->orderBy('id', 'asc')->get();
                  
                
                }else{
                   $nv_sm_data = Nvsericestatus::whereIn('nv_id', $nv_ids)->with(['service', 'material', 'user'])
                    ->where('draft',1)
                    ->orderBy('id', 'asc')->get();
                  
                }
            }elseif(!empty($dep_rew4) && $dep_rew4 == $user->id){
                if($fiscal_year){
                    $nv = NeedValidation::where('fiscal_year', $fiscal_year)->where(function ($query) use ($user, $allNormalUsers, $departmentIds, $currentFinancialYear) {
                        $query->where('user_id', $user->id)
                              ->orWhereIn('user_id', $allNormalUsers)
                              ->orWhereIn('department_id', $departmentIds);
                    })
                    ->whereHas('service') 
                    ->select('id')
                    ->get();  
                }else{
                    $nv = NeedValidation::where('fiscal_year', $currentFinancialYear)->where(function ($query) use ($user, $allNormalUsers, $departmentIds, $currentFinancialYear) {
                        $query->where('user_id', $user->id)
                              ->orWhereIn('user_id', $allNormalUsers)
                              ->orWhereIn('department_id', $departmentIds);
                    })
                    ->whereHas('service') 
                    ->select('id')
                    ->get();  
                }
                   $nv_ids = $nv->pluck('id');
                if(!empty($dep_rew3)){
                   $nv_sm_data = Nvsericestatus::whereIn('nv_id', $nv_ids)->with(['service', 'material', 'user'])
                    ->where('rv3_status',1)
                    ->orderBy('id', 'asc')->get();
                
                }elseif(!empty($dep_rew2)){
                   $nv_sm_data = Nvsericestatus::whereIn('nv_id', $nv_ids)->with(['service', 'material', 'user'])
                    ->where('rv2_status',1)
                    ->orderBy('id', 'asc')->get();
                  
                }elseif(!empty($dep_rew1)){
                   $nv_sm_data = Nvsericestatus::whereIn('nv_id', $nv_ids)->with(['service', 'material', 'user'])
                    ->where('rv1_status',1)
                    ->orderBy('id', 'asc')->get();
                  
                }else{
                     $nv_sm_data = Nvsericestatus::whereIn('nv_id', $nv_ids)->with(['service', 'material', 'user'])
                    ->where('draft',1)
                    ->orderBy('id', 'asc')->get();
                   
                }
            }elseif(!empty($hod) && $hod == $user->id){
                $nv_sm_data = array();
                $nv_statuses = array();
                $nv_id = [];
        
               $depart = Department::whereIn('id', $departmentIds)->get();
        
               foreach ($depart as $depart) {
                $dep_rew4 = $depart->dep_rew4;
                $dep_rew3 = $depart->dep_rew3;
                $dep_rew2 = $depart->dep_rew2;
                $dep_rew1 = $depart->dep_rew1;
                $dep_hod = $depart->dep_hod;
                if(!empty($dep_hod)){
                if(!empty($dep_rew4)){
                    $nv_status = Nvsericestatus::where(function ($query) {
                        $query->where('rv4_status', 1);
                           
                    })
                        ->where(function ($query) {
                            $query
                                ->where('rv4_status', '!=', 2);
                        })
                        ->get();
                        if(count($nv_status) > 0) {
                            $nv_statuses[] = $nv_status;
                           
                            
                        }
               
              
            }elseif(!empty($dep_rew3)){
                $nv_status = Nvsericestatus::where(function ($query) {
                    $query->where('rv3_status', 1);
                       
                })
                    ->where(function ($query) {
                        $query
                            ->where('rv3_status', '!=', 2);
                    })
                    ->get();
                    if(count($nv_status) > 0) {
                        $nv_statuses[] = $nv_status;
                        
                        
                    }
              
            }elseif(!empty($dep_rew2)){
               // print_r("2");
               $nv_status = Nvsericestatus::where(function ($query) {
                $query->where('rv2_status', 1);
                  
            })
                ->where(function ($query) {
                    $query
                        ->where('rv2_status', '!=', 2);
                })
                ->get();
                if(count($nv_status) > 0) {
                    $nv_statuses[] = $nv_status;
                    
                    
                }
              
        
            }elseif(!empty($dep_rew1)){
                 // print_r("1");
                 $nv_status = Nvsericestatus::where(function ($query) {
                    $query->where('rv1_status', 1);
                })
                    ->where(function ($query) {
                        $query->where('rv1_status', '!=', 2);
                    })
                    ->get();  
                // print_r($nv_status);
                if(count($nv_status) > 0) {
                    $nv_statuses[] = $nv_status;
                    // print_r("here");
                    
                }
              
              
            }else{
                $nv_status = Nvsericestatus::join('needvalidations','needvalidations.id','=','nvservicestatus.nv_id')
                ->join('department','department.id','=','needvalidations.department_id')
                ->where('department.id',$depart->id)
                ->where('nvservicestatus.draft',1)
                ->get();
               
                if(count($nv_status) > 0) {
                    $nv_statuses[] = $nv_status;
                }
               
              
            }
        }
         
               }
               if(!empty($nv_statuses)) {
                foreach ($nv_statuses as $idx => $nv_status) {
                    foreach($nv_status as $data) {
                        array_push($nv_id, $data["nv_id"]);
                    }
                }
                if($fiscal_year){
                $nv1 = NeedValidation::where('fiscal_year', $fiscal_year)->where('delete_draft',0)
                ->whereIn('department_id', $departmentIds)->whereIn("id", $nv_id)->pluck('id');
                }else{
                $nv1 = NeedValidation::where('fiscal_year', $currentFinancialYear)->where('delete_draft',0)
                ->whereIn('department_id', $departmentIds)->whereIn("id", $nv_id)->pluck('id'); 
                }
                $nv_sm_data = Nvsericestatus::whereIn('nv_id', $nv1)->with(['service', 'material', 'user'])->orderBy('id', 'asc')->get();
              
              }
            }elseif((!empty($id0->work_rew1) && $id0->work_rew1 == $user->id) || (!empty($id0->work_rew2) && $id0->work_rew2 == $user->id) ||
            (!empty($id0->work_rew3) && $id0->work_rew3 == $user->id) || (!empty($id0->work_rew4) && $id0->work_rew4 == $user->id)){

                if($fiscal_year){
                $nv = NeedValidation::whereHas('service')->select('id')->where('fiscal_year', $fiscal_year)
                ->get();
                }else{
                $nv = NeedValidation::whereHas('service')->select('id')->where('fiscal_year', $currentFinancialYear)
                ->get();
                }
                $nv_ids = $nv->pluck('id');

                $nv_sm_data = Nvsericestatus::whereIn('nv_id', $nv_ids)->with(['service', 'material', 'user'])
                ->where('derc_info',1)
                ->where('hod_status',1)
                ->orderBy('id', 'asc')->get();

            }elseif(!empty($id0->approver) && $id0->approver == $user->id){
                if($fiscal_year){
                    $nv = NeedValidation::whereHas('service')->select('id')->where('fiscal_year', $fiscal_year)
                   ->get();
                }else{
                    $nv = NeedValidation::whereHas('service')->select('id')->where('fiscal_year', $currentFinancialYear)
                   ->get();
                }
               $nv_ids = $nv->pluck('id');

             if(!empty($id0->work_rew1) || !empty($id0->work_rew2)|| !empty($id0->work_rew3)|| !empty($id0->work_rew4)){
                $nv_sm_data = Nvsericestatus::whereIn('nv_id', $nv_ids)->with(['service', 'material', 'user'])->where(function ($query) {
                    $query->orWhere('ces_rew1_status', 1)
                        ->orWhere('ces_rew2_status', 1)
                        ->orWhere('ces_rew3_status', 1)
                        ->orWhere('ces_rew4_status', 1);
                })
                    ->where(function ($query) {
                        $query->where('ces_rew1_status', '!=', 2)
                            ->where('ces_rew2_status', '!=', 2)
                            ->where('ces_rew3_status', '!=', 2)
                            ->where('ces_rew4_status', '!=', 2);
                    })
                    ->where(function ($query) {
                        $query->where('derc_info', 1);
                    })
                    ->orderBy('id', 'asc')->get();
                }elseif(empty($id0->work_rew1) && empty($id0->work_rew2) && empty($id0->work_rew3) && empty($id0->work_rew4)){
                    $nv_sm_data = Nvsericestatus::whereIn('nv_id', $nv_ids)->with(['service', 'material', 'user'])->where(function ($query) {
                        $query
                        ->where('hod_status', 1);
                    })
                    ->where(function ($query) {
                        $query ->where('hod_status', '!=', 2) ;
                    })
                    ->where(function ($query) {
                        $query->where('derc_info', 1);
                    })
                        ->orderBy('id', 'asc')->get();
                }
            }elseif((!empty($id1->work_rew1) && $id1->work_rew1 == $user->id) || (!empty($id1->work_rew2) && $id1->work_rew2 == $user->id) ||
            (!empty($id1->work_rew3) && $id1->work_rew3 == $user->id) || (!empty($id1->work_rew4) && $id1->work_rew4 == $user->id)){
                if($fiscal_year){
                    $nv = NeedValidation::whereHas('service')->select('id')->where('fiscal_year', $fiscal_year)
                   ->get();
                }else{
                   $nv = NeedValidation::whereHas('service')->select('id')->where('fiscal_year', $currentFinancialYear)
                   ->get();
                }
                $nv_ids = $nv->pluck('id');
            
                $nv_sm_data = Nvsericestatus::whereIn('nv_id', $nv_ids)
                ->with(['service', 'material', 'user'])
                ->where(function ($query) {
                    $query->where('ces_status', 1)
                        ->orWhere('hod_status', 1)
                        ->where('derc_info', 0);
                })
                ->orderBy('id', 'asc')->get();
            }elseif(!empty($id1->approver) && $id1->approver == $user->id){
                if($fiscal_year){
                    $nv = NeedValidation::whereHas('service')->select('id')->where('fiscal_year', $fiscal_year)
                   ->get();
                }else{
                    $nv = NeedValidation::whereHas('service')->select('id')->where('fiscal_year', $currentFinancialYear)
                   ->get();
                }                
                $nv_ids = $nv->pluck('id');
                if(!empty($id1->work_rew1) || !empty($id1->work_rew2)|| !empty($id1->work_rew3)|| !empty($id1->work_rew4)){
                    $nv_sm_data = Nvsericestatus::whereIn('nv_id', $nv_ids)
                    ->with(['service', 'material', 'user'])
                    ->where(function ($query) {
                        $query->where(function ($subQuery) {
                            $subQuery->orWhere('work_rew1_status', 1)
                                ->orWhere('work_rew2_status', 1)
                                ->orWhere('work_rew3_status', 1)
                                ->orWhere('work_rew4_status', 1);
                        })->where(function ($subQuery) {
                            $subQuery->where('work_rew1_status', '!=', 2)
                                ->where('work_rew2_status', '!=', 2)
                                ->where('work_rew3_status', '!=', 2)
                                ->where('work_rew4_status', '!=', 2);
                        })->whereIn('derc_info', [0, 1]);
                    })
                    ->orderBy('id', 'asc')->get();
                    }elseif(empty($id1->work_rew1) && empty($id1->work_rew2) && empty($id1->work_rew3) && empty($id1->work_rew4)){
                        $nv_sm_data = Nvsericestatus::whereIn('nv_id', $nv_ids)
                        ->with(['service', 'material', 'user'])
                        ->where(function ($query) {
                            $query->where('ces_status', 1)
                                ->orWhere('hod_status', 1)
                                ->where('derc_info', 0);
                        })
                        ->orderBy('id', 'asc')->get();
                    }
            }elseif((!empty($id2->work_rew1) && $id2->work_rew1 == $user->id) || (!empty($id2->work_rew2) && $id2->work_rew2 == $user->id) ||
            (!empty($id2->work_rew3) && $id2->work_rew3 == $user->id) || (!empty($id2->work_rew4) && $id2->work_rew4 == $user->id)){
                if($fiscal_year){
                    $nv = NeedValidation::whereHas('service')->select('id')->where('fiscal_year', $fiscal_year)
                   ->get();
                }else{
                    $nv = NeedValidation::whereHas('service')->select('id')->where('fiscal_year', $currentFinancialYear)
                   ->get();
                }
                $nv_ids = $nv->pluck('id');
                    $nv_sm_data = Nvsericestatus::whereIn('nv_id', $nv_ids)->with(['service', 'material', 'user'])->where(function ($query) {
                        $query->where('check_technology', 1);
                    })->where(function ($query) {
                        $query->where('approver_status', 1);
                    })
                ->orderBy('id', 'asc')->get();
            }elseif(!empty($id2->approver) && $id2->approver == $user->id){
                if($fiscal_year){
                    $nv = NeedValidation::whereHas('service')->select('id')->where('fiscal_year', $fiscal_year)
                    ->get();
                }else{
                   $nv = NeedValidation::whereHas('service')->select('id')->where('fiscal_year', $currentFinancialYear)
                  ->get();
                }
                $nv_ids = $nv->pluck('id');

                if(!empty($id2->work_rew1) || !empty($id2->work_rew2)|| !empty($id2->work_rew3)|| !empty($id2->work_rew4)){
                    $nv_sm_data = Nvsericestatus::whereIn('nv_id', $nv_ids)->with(['service', 'material', 'user'])
                    ->where(function ($query) {
                        $query->orWhere('work_rew1dep2_status', 1)
                            ->orWhere('work_rew2dep2_status', 1)
                            ->orWhere('work_rew3dep2_status', 1)
                            ->orWhere('work_rew4dep2_status', 1);
                    })
                        ->where(function ($query) {
                            $query->where('work_rew1dep2_status', '!=', 2)
                                ->where('work_rew2dep2_status', '!=', 2)
                                ->where('work_rew3dep2_status', '!=', 2)
                                ->where('work_rew4dep2_status', '!=', 2);
                        })
                        ->where(function ($query) {
                            $query->where('check_technology', 1);
                        })
                        ->orderBy('id', 'asc')->get();
                    }elseif(empty($id2->work_rew1) && empty($id2->work_rew2) && empty($id2->work_rew3) && empty($id2->work_rew4)){
                        $nv_sm_data = Nvsericestatus::whereIn('nv_id', $nv_ids)->with(['service', 'material', 'user'])
                    ->where(function ($query) {
                        $query->where('check_technology', 1);
                    })->where(function ($query) {
                        $query->where('approver_status', 1);
                    })
                            ->orderBy('id', 'asc')->get();
                    }
        
            }elseif((!empty($id3->work_rew1) && $id3->work_rew1 == $user->id) || (!empty($id3->work_rew2) && $id3->work_rew2 == $user->id) ||
            (!empty($id3->work_rew3) && $id3->work_rew3 == $user->id) || (!empty($id3->work_rew4) && $id3->work_rew4 == $user->id)){
                if($fiscal_year){
                    $nv = NeedValidation::whereHas('service')->select('id')->where('fiscal_year', $fiscal_year)
                    ->get();
                }else{
                    $nv = NeedValidation::whereHas('service')->select('id')->where('fiscal_year', $currentFinancialYear)
                    ->get();
                }
              
                $nv_ids = $nv->pluck('id');
                    $nv_sm_data = Nvsericestatus::whereIn('nv_id', $nv_ids)->with(['service', 'material', 'user'])
                    ->where(function ($query) {
                        $query->where('approverdep2_status', 1)
                            ->orWhere('approver_status', 1)
                            ->where('check_technology', 0);
                    })
                 ->orderBy('id', 'asc')->get();
            }elseif(!empty($id3->approver) && $id3->approver == $user->id){
                if($fiscal_year){
                    $nv = NeedValidation::whereHas('service')->select('id')->where('fiscal_year', $fiscal_year)
                    ->get();
                }else{
                    $nv = NeedValidation::whereHas('service')->select('id')->where('fiscal_year', $currentFinancialYear)
                    ->get();
                }
              
                $nv_ids = $nv->pluck('id');
                if(!empty($id3->work_rew1) || !empty($id3->work_rew2)|| !empty($id3->work_rew3)|| !empty($id3->work_rew4)){
                    $nv_sm_data = Nvsericestatus::whereIn('nv_id', $nv_ids)->with(['service', 'material', 'user'])
                    ->where(function ($query) {
                        $query->where(function ($subQuery) {
                            $subQuery->orWhere('work_rew1dep3_status', 1)
                                ->orWhere('work_rew2dep3_status', 1)
                                ->orWhere('work_rew3dep3_status', 1)
                                ->orWhere('work_rew4dep3_status', 1);
                        })->where(function ($subQuery) {
                            $subQuery->where('work_rew1dep3_status', '!=', 2)
                                ->orWhere('work_rew2dep3_status', '!=', 2)
                                ->orWhere('work_rew3dep3_status', '!=', 2)
                                ->orWhere('work_rew4dep3_status', '!=', 2);
                        })->whereIn('check_technology', [0, 1]);
                    })
                ->orderBy('id', 'asc')->get();
                    }elseif(empty($id3->work_rew1) && empty($id3->work_rew2) && empty($id3->work_rew3) && empty($id3->work_rew4)){
                        $nv_sm_data = Nvsericestatus::whereIn('nv_id', $nv_ids)->with(['service', 'material', 'user'])
                        ->where(function ($query) {
                            $query->where('approverdep2_status', 1)
                                ->orWhere('approver_status', 1)
                                ->where('check_technology', 0);
                        })
                    ->orderBy('id', 'asc')->get();
                    }
                }elseif(!empty($group_cio) && $group_cio == $user->id) {
                    $departmentIds = explode(',', $user->department_id);
                    if($fiscal_year){
                        $nv = NeedValidation::whereHas('service')->select('id')->whereIn('department_id', $departmentIds)->where('fiscal_year', $fiscal_year)
                        ->get();
                    }else{
                        $nv = NeedValidation::whereHas('service')->select('id')->whereIn('department_id', $departmentIds)->where('fiscal_year', $currentFinancialYear)
                    ->get();
                    }
                   
                    $nv_ids = $nv->pluck('id');
                
                     $nv_sm_data = Nvsericestatus::whereIn('nv_id', $nv_ids)->with(['service', 'material', 'user'])
                    ->where('approverdep3_status', 1)
                    ->orderBy('id', 'asc')->get();
            }elseif((!empty($id4->work_rew1) && $id4->work_rew1 == $user->id) || (!empty($id4->work_rew2) && $id4->work_rew2 == $user->id) ||
            (!empty($id4->work_rew3) && $id4->work_rew3 == $user->id) || (!empty($id4->work_rew4) && $id4->work_rew4 == $user->id)){
                $nvid = [];
                $nvstatus = Nvsericestatus::get();
                foreach ($nvstatus as $nvstatus) {
                    array_push($nvid, $nvstatus["nv_id"]);
                }
                $user_nv = NeedValidation::with("division", "service")
                    ->whereIn("id", $nvid)
                    ->orderBy("id", "desc")
                    ->first();
            if(!empty( $user_nv)){
                $employees = Employee::where("user_id", $user_nv->user_id)->first();
                $department = Department::where("id", $employees->department_id)->first();
                $group_cio = $department->group_cio;
            }
                if($fiscal_year){
                    $nv = NeedValidation::whereHas('service')->select('id')->where('fiscal_year', $fiscal_year)
                    ->get();
                }else{
                    $nv = NeedValidation::whereHas('service')->select('id')->where('fiscal_year', $currentFinancialYear)
                    ->get();
                }
              
                $nv_ids = $nv->pluck('id');
                if(!empty( $group_cio)){
                 $nv_sm_data = Nvsericestatus::whereIn('nv_id', $nv_ids)->with(['service', 'material', 'user'])
                 ->where('groupcio_status', 1)
                ->orderBy('id', 'asc')->get();
                }else{
                    $nv_sm_data = Nvsericestatus::whereIn('nv_id', $nv_ids)->with(['service', 'material', 'user'])
                    ->where('approverdep3_status', 1)
                   ->orderBy('id', 'asc')->get();
                }
            }elseif(!empty($id4->approver) && $id4->approver == $user->id){
                $nvid = [];
                $nvstatus = Nvsericestatus::get();
                foreach ($nvstatus as $nvstatus) {
                    array_push($nvid, $nvstatus["nv_id"]);
                }
                $user_nv = NeedValidation::with("division", "service")
                    ->whereIn("id", $nvid)
                    ->orderBy("id", "desc")
                    ->first();
            if(!empty( $user_nv)){
                $employees = Employee::where("user_id", $user_nv->user_id)->first();
                $department = Department::where("id", $employees->department_id)->first();
                $group_cio = $department->group_cio;
            }
                if($fiscal_year){
                    $nv = NeedValidation::whereHas('service')->select('id')->where('fiscal_year', $fiscal_year)
                ->get();
                }else{
                    $nv = NeedValidation::whereHas('service')->select('id')->where('fiscal_year', $currentFinancialYear)
                ->get();
                }
              
                $nv_ids = $nv->pluck('id');
                if(!empty($id4->work_rew1) || !empty($id4->work_rew2)|| !empty($id4->work_rew3)|| !empty($id4->work_rew4)){
                    if(!empty( $group_cio)){
                 $nv_sm_data = Nvsericestatus::whereIn('nv_id', $nv_ids)->with(['service', 'material', 'user'])
                 ->where('groupcio_status', 1)
                 ->where(function ($query) {
                    $query->orWhere('work_rew1dep4_status', 1)
                        ->orWhere('work_rew2dep4_status', 1)
                        ->orWhere('work_rew3dep4_status', 1)
                        ->orWhere('work_rew4dep4_status', 1);
                })
                    ->where(function ($query) {
                        $query->where('work_rew1dep4_status', '!=', 2)
                            ->where('work_rew2dep4_status', '!=', 2)
                            ->where('work_rew3dep4_status', '!=', 2)
                            ->where('work_rew4dep4_status', '!=', 2);
                    })
                
                ->orderBy('id', 'asc')->get();
                    }else{
                        $nv_sm_data = Nvsericestatus::whereIn('nv_id', $nv_ids)->with(['service', 'material', 'user'])
                        ->where('approverdep3_status', 1)
                        ->where(function ($query) {
                           $query->orWhere('work_rew1dep4_status', 1)
                               ->orWhere('work_rew2dep4_status', 1)
                               ->orWhere('work_rew3dep4_status', 1)
                               ->orWhere('work_rew4dep4_status', 1);
                       })
                           ->where(function ($query) {
                               $query->where('work_rew1dep4_status', '!=', 2)
                                   ->where('work_rew2dep4_status', '!=', 2)
                                   ->where('work_rew3dep4_status', '!=', 2)
                                   ->where('work_rew4dep4_status', '!=', 2);
                           })
                       
                       ->orderBy('id', 'asc')->get();
                    }
                }elseif(empty($id4->work_rew1) && empty($id4->work_rew2) && empty($id4->work_rew3) && empty($id4->work_rew4)){
                    if(!empty( $group_cio)){
                    $nv_sm_data = Nvsericestatus::whereIn('nv_id', $nv_ids)->with(['service', 'material', 'user'])
                    ->where('groupcio_status', 1)
                    
                       ->where(function ($query) {
                           $query->where('groupcio_status', '!=', 2) ;
                       })
                  
                   ->orderBy('id', 'asc')->get();
                    }else{
                        $nv_sm_data = Nvsericestatus::whereIn('nv_id', $nv_ids)->with(['service', 'material', 'user'])
                        ->where('approverdep3_status', 1)
                        
                           ->where(function ($query) {
                               $query->where('approverdep3_status', '!=', 2) ;
                           })
                      
                       ->orderBy('id', 'asc')->get();
                    }
                    }
           
            }elseif(!empty($id5->approver) && $id5->approver == $user->id){
         
                if($fiscal_year) {
                    $nv = NeedValidation::whereHas('service')->select('id')->where('fiscal_year', $fiscal_year)
                    ->get();

                 }else{
                    $nv = NeedValidation::whereHas('service')->select('id')->where('fiscal_year', $currentFinancialYear)
                    ->get();

                 }
             
                $nv_ids = $nv->pluck('id');
             
                    $nv_sm_data = Nvsericestatus::whereIn('nv_id', $nv_ids)->with(['service', 'material', 'user'])
                    ->where('approverdep4_status', 1)
                    ->orderBy('id', 'asc')->get();
               
            }
         }
        }
    
        $output = '<html><head><style>';
        $output .= 'table {border-collapse: collapse; width: 100%;}';
        $output .= 'th, td {border: 1px solid black; padding: 8px; text-align: center;}';
        $output .= 'th {background-color: blue}';
        $output .= '</style></head><body>';
        $output .= '<table>';
        $output .= '<tr><th>S.no</th><th>Proposal Number</th><th>Department</th><th>NV Type</th><th>Initiated BY & Date</th><th>HOD</th><th>CES</th><th>CPMG</th><th>CEO Nominee1</th>
        <th>CEO Nominee2</th><th>Group Head</th><th>CTO</th><th>CEO</th></tr>';
        $i = 1;
        $no = 101;
   
            if (!empty($nv_sm_data)){
                foreach ($nv_sm_data as $nv_list){
                    $proposal_no = getProposalNumber($nv_list->nv_id);
                    $status_obj = getAllStatus($nv_list->nv_id);
                   
                    $inc_date = new DateTime($nv_list->created_at);
                   
                    $hod_app = new DateTime($nv_list->hod_timestamp);
                    $interval = $hod_app->diff($inc_date);
                    $days = $interval->days;
    
                    $ces_app = new DateTime($nv_list->ces_timestamp);
                    $interval = $ces_app->diff($hod_app);
                    $cesdays = $interval->days;
                   
                    $cpmg_app = new DateTime($nv_list->cpmg_timestamp);
                    $cpmginterval = $cpmg_app->diff($ces_app);
                    $cpmgdays = $cpmginterval->days;
                   
                    $cto_app = new DateTime($nv_list->cto_timestamp);
                    $ctointerval = $cto_app->diff($cpmg_app);
                    $ctodays = $ctointerval->days;
                   
                    $ceon_app = new DateTime($nv_list->ceo_nominee_timestamp);
                    $ceoninterval = $ceon_app->diff($cto_app);
                    $ceondays = $ceoninterval->days;
                   
                    $ceon2_app = new DateTime($nv_list->ceo_nominee2_timestamp);
                    $ceon2interval = $ceon_app->diff($ceon_app);
                    $ceon2days = $ceoninterval->days;
                   
                    $grouphead_app = new DateTime($nv_list->groupcio_timestamp);
                    $groupheadinterval = $ceon2_app->diff($ceon2_app);
                    $groupheaddays = $groupheadinterval->days;
    
                    $ceo_app = new DateTime($nv_list->ceo_timestamp);
                    $ceointerval = $ceo_app->diff($ceon2_app);
                    $ceodays = $ceointerval->days;
                   
                    $value = null;
                   
                    $n_id[] = $nv_list->nv_id;
                   
                    $result = [];
                   
                    foreach ($n_id as $key => $value) {
                        $count = count(array_keys($n_id, $value)); 
                   
                        if ($count > 1) {
                            for ($i = 0; $i <= $count; $i++) {
                                $result = $value . '-v' . $i;
                            }
                        } else {
                            $result = $value;
                        }
                       
                    }
                   $service= getServiceName($nv_list->nv_id);
                    $url = '/admin/nv_' . lcfirst($service) . '/create/' . $nv_list->nv_id.'/'.
                    $nv_list->company_id;
                    $i = $key + 1;
                    $output .= '<tr>';
                    $output .= '<td>' . $i . '</td>';

                    if($nv_list->service_id == null){
                        $proposalNo=  getDepartmentName($nv_list->material->dept_id);
                     }else{
                        $proposalNo= getDepartmentName($nv_list->service->dept_id);
                     }
                    $output .= '<td>' .'NV/'.''. ($proposal_no->budget_type).''. '/FY'.''. ($proposal_no->fiscal_year).''.'/'.''.$proposalNo.''.'/'.''.getServiceName($nv_list->nv_id).''.'/'.''. $result. '</td>';
                  
                     if ($nv_list->service_id == null){
                        $departmentName = getDepartmentName($nv_list->material->dept_id);
                     }else{
                        $departmentName = getDepartmentName($nv_list->service->dept_id);
                     }
                     $output .='<td>' .$departmentName. '</td>';
                     
                     if($nv_list->service_id == null && $nv_list->material->ser_rel_nv == 2){
                        $serviceName = getServiceName($nv_list->nv_id).'+Service';
                     }else{
                        $serviceName = getServiceName($nv_list->nv_id);
                     }
                      $output .='<td>' . $serviceName. '</td>';

                    $date = date('d-M-y', strtotime($nv_list->created_at)) ;  
                    $time = date('h:i A', strtotime($nv_list->created_at));      
                    if ($nv_list->service_id == null)
                    {
                         $output .='<td>' . getUserName($nv_list->material->user_id).'<br>'.$date.'<br>'.$time . '</td>';
                    }
                    else
                    {
                         $output .='<td>' . getUserName($nv_list->service->user_id).'<br>'.$date.'<br>'.$time . '</td>';
                    }

                    if ($nv_list->service_id == null)
                    {
                        
                        $gethodname = getHodName($nv_list->hod_id ?? null) ;
                        if ($nv_list->hod_status == 0)
                        {
                            if (!empty($nv_list->hod_timestamp))
                            {
                                $datehod = date('d-M-y', strtotime($nv_list->hod_timestamp));
                                $timehod = date('h:i A', strtotime($nv_list->hod_timestamp));
                            }
                        }
                        elseif($nv_list->hod_status == 1)
                        {
                            $datehod = date('d-M-y', strtotime($nv_list->hod_timestamp));
                            $timehod = date('h:i A', strtotime($nv_list->hod_timestamp));
                            $output .='<td>' . $gethodname.'<br>'. 'Approval Date:'.''. $datehod.'<br>'.'Approval Time:'.''.$timehod.'<br>'.'HOD System IP:'.''.$nv_list->hod_action_ip.'<br>'.'Pendency:'.''.$days. '</td>';
                        }
                        elseif($nv_list->hod_status == 2)
                        {
                            $datehod = date('d-M-y', strtotime($nv_list->hod_timestamp));
                            $timehod = date('h:i A', strtotime($nv_list->hod_timestamp));
                            $output .='<td>' . $gethodname.'<br>'. 'Rejection Date:'.''. $datehod.'<br>'.'Rejection Time:'.''.$timehod.'<br>'.'HOD System IP:'.''.$nv_list->hod_action_ip.'<br>'.'Pendency:'.''.$days. '</td>';
                        }
                    }
                    else
                    {
                        $gethodname = getHodName($nv_list->hod_id ?? null) ;
                        if ($nv_list->hod_status == 0)
                        {
                            if (!empty($nv_list->hod_timestamp))
                            {
                                $datehod = date('d-M-y', strtotime($nv_list->hod_timestamp));
                                $timehod = date('h:i A', strtotime($nv_list->hod_timestamp));
                            }
                        }
                        elseif($nv_list->hod_status == 1)
                        {
                            $datehod = date('d-M-y', strtotime($nv_list->hod_timestamp));
                            $timehod = date('h:i A', strtotime($nv_list->hod_timestamp));
                            $output .='<td>' . $gethodname.'<br>'. 'Approval Date:'.''. $datehod.'<br>'.'Approval Time:'.''.$timehod.'<br>'.'HOD System IP:'.''.$nv_list->hod_action_ip.'<br>'.'Pendency:'.''.$days. '</td>';
                        }
                        elseif($nv_list->hod_status == 2)
                        {
                            $datehod = date('d-M-y', strtotime($nv_list->hod_timestamp));
                            $timehod = date('h:i A', strtotime($nv_list->hod_timestamp));
                            $output .='<td>' . $gethodname.'<br>'. 'Rejection Date:'.''. $datehod.'<br>'.'Rejection Time:'.''.$timehod.'<br>'.'HOD System IP:'.''.$nv_list->hod_action_ip.'<br>'.'Pendency:'.''.$days. '</td>';
                        }
                    }

                    if ($nv_list->service_id == null)
                    {
                        $cesname = getCesName($nv_list->ces_id ?? null);
                        if ($nv_list->ces_status == 0)
                        {
                             if($nv_list->cpmg_status == 1 || $nv_list->cpmg_status == 2){
                                   $output .='<td>' .'N/A' . "\t" . '</td>';
                                }
                            if (!empty($nv_list->ces_timestamp))
                            {
                                $cesdate = date('d-M-y', strtotime($nv_list->ces_timestamp));
                                $cestime = date('h:i A', strtotime($nv_list->ces_timestamp));
                            }



                        }
                        elseif($nv_list->ces_status == 1)
                        {
                            $cesdate = date('d-M-y', strtotime($nv_list->ces_timestamp));
                            $cestime = date('h:i A', strtotime($nv_list->ces_timestamp));
                            $output .='<td>' .$cesname.'<br>'. 'Approval Date:'.''. $cesdate.'<br>'.'Approval Time:'.''.$cestime.'<br>'.'CES System IP:'.''.$nv_list->ces_action_ip.'<br>'.'Pendency:'.''.$cesdays. '</td>';
                        }
                        elseif($nv_list->ces_status == 2)
                        {
                            $cesdate = date('d-M-y', strtotime($nv_list->ces_timestamp));
                            $cestime = date('h:i A', strtotime($nv_list->ces_timestamp));
                            $output .='<td>' .$cesname.'<br>'. 'Rejection Date:'.''. $cesdate.'<br>'.'Rejection Time:'.''.$cestime.'<br>'.'CES System IP:'.''.$nv_list->ces_action_ip.'<br>'.'Pendency:'.''.$cesdays. '</td>';
                        }
                    }
                    else
                    {
                        $cesname = getCesName($nv_list->ces_id ?? null);
                        if ($nv_list->ces_status == 0)
                        {
                            if($nv_list->cpmg_status == 1 || $nv_list->cpmg_status == 2){
                                $output .='<td>' .'N/A' . "\t" . '</td>';
                            }
                            if (!empty($nv_list->ces_timestamp))
                            {
                                $cesdate = date('d-M-y', strtotime($nv_list->ces_timestamp));
                                $cestime = date('h:i A', strtotime($nv_list->ces_timestamp));
                            }
                        }
                        elseif($nv_list->ces_status == 1)
                        {
                            $cesdate = date('d-M-y', strtotime($nv_list->ces_timestamp));
                            $cestime = date('h:i A', strtotime($nv_list->ces_timestamp));
                            $output .='<td>' . $cesname.'<br>'. 'Approval Date:'.''. $cesdate.'<br>'.'Approval Time:'.''.$cestime.'<br>'.'CES System IP:'.''.$nv_list->ces_action_ip.'<br>'.'Pendency:'.''.$cesdays. '</td>';
                        }
                        elseif($nv_list->ces_status == 2)
                        {
                            $cesdate = date('d-M-y', strtotime($nv_list->ces_timestamp));
                            $cestime = date('h:i A', strtotime($nv_list->ces_timestamp));
                            $output .='<td>' . $cesname.'<br>'. 'Rejection Date:'.''. $cesdate.'<br>'.'Rejection Time:'.''.$cestime.'<br>'.'CES System IP:'.''.$nv_list->ces_action_ip.'<br>'.'Pendency:'.''.$cesdays. '</td>';
                        }
                    }

                    if ($nv_list->service_id == null)
                    {
                        $cpmgname = getCpmgName($nv_list->cpmg_id ?? null);
                        if ($nv_list->cpmg_status == 0)
                        {
                            if (!empty($nv_list->cpmg_timestamp))
                            {
                                $cpmgdate = date('d-M-y', strtotime($nv_list->cpmg_timestamp));
                                $cpmgtime = date('h:i A', strtotime($nv_list->cpmg_timestamp));
                            }
                        }
                        elseif($nv_list->cpmg_status == 1)
                        {
                            $cpmgdate = date('d-M-y', strtotime($nv_list->cpmg_timestamp));
                            $cpmgtime = date('h:i A', strtotime($nv_list->cpmg_timestamp));
                            $output .='<td>' . $cpmgname.'<br>'. 'Approval Date:'.''. $cpmgdate.'<br>'.'Approval Time:'.''.$cpmgtime.'<br>'.'CPMG System IP:'.''.$nv_list->cpmg_action_ip.'<br>'.'Pendency:'.''.$cpmgdays. '</td>';
                        }
                        elseif($nv_list->cpmg_status == 2)
                        {
                            $cpmgdate = date('d-M-y', strtotime($nv_list->cpmg_timestamp));
                            $cpmgtime = date('h:i A', strtotime($nv_list->cpmg_timestamp));
                            $output .='<td>' . $cpmgname.'<br>'. 'Rejection Date:'.''. $cpmgdate.'<br>'.'Rejection Time:'.''.$cpmgtime.'<br>'.'CPMG System IP:'.''.$nv_list->cpmg_action_ip.'<br>'.'Pendency:'.''.$cpmgdays. '</td>';
                        }
                    }  
                    else
                    {
                        $cpmgname = getCpmgName($nv_list->cpmg_id ?? null);
                        if ($nv_list->cpmg_status == 0)
                        {
                            if (!empty($nv_list->cpmg_timestamp))
                            {
                            
                                $cpmgdate = date('d-M-y', strtotime($nv_list->cpmg_timestamp));
                                $cpmgtime = date('h:i A', strtotime($nv_list->cpmg_timestamp));
                            }
                        }
                        elseif($nv_list->cpmg_status == 1)
                        {
                            $cpmgdate = date('d-M-y', strtotime($nv_list->cpmg_timestamp));
                            $cpmgtime = date('h:i A', strtotime($nv_list->cpmg_timestamp));
                            $output .='<td>' . $cpmgname.'<br>'. 'Approval Date:'.''. $cpmgdate.'<br>'.'Approval Time:'.''.$cpmgtime.'<br>'.'CPMG System IP:'.''.$nv_list->cpmg_action_ip.'<br>'.'Pendency:'.''.$cpmgdays. '</td>';
                        }
                        elseif($nv_list->cpmg_status == 2)
                        {
                            $cpmgdate = date('d-M-y', strtotime($nv_list->cpmg_timestamp));
                            $cpmgtime = date('h:i A', strtotime($nv_list->cpmg_timestamp));
                            $output .='<td>' . $cpmgname.'<br>'. 'Rejection Date:'.''. $cpmgdate.'<br>'.'Rejection Time:'.''.$cpmgtime.'<br>'.'CPMG System IP:'.''.$nv_list->cpmg_action_ip.'<br>'.'Pendency:'.''.$cpmgdays. '</td>';
                        }
                    }
                    if ($nv_list->service_id == null)
                    {
                        $ctoname = getCtoName($nv_list->cto_id ?? null);
                        if ($nv_list->cto_status == 0)
                        {
                            
                            if (!empty($nv_list->cto_timestamp))
                            {
                                $ctodate = date('d-M-y', strtotime($nv_list->cto_timestamp));
                                $ctotime = date('h:i A', strtotime($nv_list->cto_timestamp));
                            }
                        }
                        elseif($nv_list->cto_status == 1)
                        {
                            $ctodate = date('d-M-y', strtotime($nv_list->cto_timestamp));
                            $ctotime = date('h:i A', strtotime($nv_list->cto_timestamp));
                            $output .='<td>' . $ctoname.'<br>'. 'Approval Date:'.''. $ctodate.'<br>'.'Approval Time:'.''.$ctotime.'<br>'.'CEO Nominee1 System IP:'.''.$nv_list->cto_action_ip .'<br>'.'Pendency:'.''.$ctodays. '</td>';
                        }
                        elseif($nv_list->cto_status == 2)
                        {
                            $ctodate = date('d-M-y', strtotime($nv_list->cto_timestamp));
                            $ctotime = date('h:i A', strtotime($nv_list->cto_timestamp));
                            $output .='<td>' . $ctoname.'<br>'. 'Rejection Date:'.''. $ctodate.'<br>'.'Rejection Time:'.''.$ctotime.'<br>'.'CEO Nominee1 System IP:'.''.$nv_list->cto_action_ip.'<br>'.'Pendency:'.''.$ctodays. '</td>';
                        }
                    }
                    else
                    {
                        $ctoname = getCtoName($nv_list->cto_id ?? null);
                        if ($nv_list->cto_status == 0)
                        {
                               
                            if (!empty($nv_list->cto_timestamp))
                            {
                                $ctodate = date('d-M-y', strtotime($nv_list->cto_timestamp));
                                $ctotime = date('h:i A', strtotime($nv_list->cto_timestamp));
                            }
                        }
                        elseif($nv_list->cto_status == 1)
                        {
                            $ctodate = date('d-M-y', strtotime($nv_list->cto_timestamp));
                            $ctotime = date('h:i A', strtotime($nv_list->cto_timestamp));
                            $output .='<td>' . $ctoname.'<br>'. 'Approval Date:'.''. $ctodate.'<br>'.'Approval Time:'.''.$ctotime.'<br>'.'CEO Nominee1 System IP:'.''.$nv_list->cto_action_ip .'<br>'.'Pendency:'.''.$ctodays. '</td>';
                        }
                        elseif($nv_list->cto_status == 2)
                        {
                            $ctodate = date('d-M-y', strtotime($nv_list->cto_timestamp));
                            $ctotime = date('h:i A', strtotime($nv_list->cto_timestamp));
                            $output .='<td>' . $ctoname.'<br>'. 'Rejection Date:'.''. $ctodate.'<br>'.'Rejection Time:'.''.$ctotime.'<br>'.'CEO Nominee1 System IP:'.''.$nv_list->cto_action_ip.'<br>'.'Pendency:'.''.$ctodays. '</td>';
                        }
                    }
                    if ($nv_list->service_id == null)
                    {
                        $ceonom1 = getCeoNomineeName($nv_list->ceo_nominee_id ?? null);
                        if ($nv_list->ceo_nominee_status == 0)
                        {
                            if (!empty($nv_list->ceo_nominee_timestamp))
                            {
                                $ceonom1date = date('d-M-y', strtotime($nv_list->ceo_nominee_timestamp));
                                $ceonom1time = date('h:i A', strtotime($nv_list->ceo_nominee_timestamp));
                            }
                        }
                        elseif($nv_list->ceo_nominee_status == 1)
                        {
                            $ceonom1date = date('d-M-y', strtotime($nv_list->ceo_nominee_timestamp));
                            $ceonom1time = date('h:i A', strtotime($nv_list->ceo_nominee_timestamp));
                            $output .='<td>' . $ceonom1.'<br>'. 'Approval Date:'.''. $ceonom1date.'<br>'.'Approval Time:'.''.$ceonom1time.'<br>'.'CEO Nominee2 System IP:'.''.$nv_list->ceo_nomnee_action_ip .'<br>'.'Pendency:'.''.$ceondays. '</td>';
                        }
                        elseif($nv_list->ceo_nominee_status == 2)
                        {
                            $ceonom1date = date('d-M-y', strtotime($nv_list->ceo_nominee_timestamp));
                            $ceonom1time = date('h:i A', strtotime($nv_list->ceo_nominee_timestamp));
                            $output .='<td>' . $ceonom1.'<br>'. 'Rejection Date:'.''. $ceonom1date.'<br>'.'Rejection Time:'.''.$ceonom1time.'<br>'.'CEO Nominee2 System IP:'.''.$nv_list->ceo_nomnee_action_ip.'<br>'.'Pendency:'.''.$ceondays. '</td>';
                        }
                    }
                    else
                    {
                        $ceonom1 = getCeoNomineeName($nv_list->ceo_nominee_id ?? null);
                        if ($nv_list->ceo_nominee_status == 0)
                        {
                            if (!empty($nv_list->ceo_nominee_timestamp))
                            {
                                $ceonom1date = date('d-M-y', strtotime($nv_list->ceo_nominee_timestamp));
                                $ceonom1time = date('h:i A', strtotime($nv_list->ceo_nominee_timestamp));
                            }
                        }
                        elseif($nv_list->ceo_nominee_status == 1)
                        {
                            $ceonom1date = date('d-M-y', strtotime($nv_list->ceo_nominee_timestamp));
                            $ceonom1time = date('h:i A', strtotime($nv_list->ceo_nominee_timestamp));
                            $output .='<td>' . $ceonom1.'<br>'. 'Approval Date:'.''. $ceonom1date.'<br>'.'Approval Time:'.''.$ceonom1time.'<br>'.'CEO Nominee2 System IP:'.''.$nv_list->ceo_nomnee_action_ip .'<br>'.'Pendency:'.''.$ceondays. '</td>';
                        }
                        elseif($nv_list->ceo_nominee_status == 2)
                        {
                            $ceonom1date = date('d-M-y', strtotime($nv_list->ceo_nominee_timestamp));
                            $ceonom1time = date('h:i A', strtotime($nv_list->ceo_nominee_timestamp));
                            $output .='<td>' . $ceonom1.'<br>'. 'Rejection Date:'.''. $ceonom1date.'<br>'.'Rejection Time:'.''.$ceonom1time.'<br>'.'CEO Nominee2 System IP:'.''.$nv_list->ceo_nomnee_action_ip.'<br>'.'Pendency:'.''.$ceondays. '</td>';
                        }
                    }
                    if ($nv_list->service_id == null)
                    {
                        $groupcio =  getGroupHeadName($nv_list->groupcio_id ?? null);
                        if ($nv_list->groupcio_status == 0)
                        {
                            
                            if($nv_list->ceo_nominee2_status == 1 || $nv_list->ceo_nominee2_status == 2){
                                $output .='<td>' .'N/A' . "\t" . '</td>';
                            }
                            if (!empty($nv_list->groupcio_timestamp))
                            {
                                $groupciodate = date('d-M-y', strtotime($nv_list->groupcio_timestamp));
                                $groupciotime = date('h:i A', strtotime($nv_list->groupcio_timestamp));
                            }
                        }
                        elseif($nv_list->groupcio_status == 1)
                        {
                            $groupciodate = date('d-M-y', strtotime($nv_list->groupcio_timestamp));
                            $groupciotime = date('h:i A', strtotime($nv_list->groupcio_timestamp));
                            $output .='<td>' . $groupcio.'<br>'. 'Approval Date:'.''. $groupciodate.'<br>'.'Approval Time:'.''.$groupciotime.'<br>'.'Group Head System IP:'.''.$nv_list->groupcio_action_ip .'<br>'.'Pendency:'.''.$groupheaddays. '</td>';
                        }
                        elseif($nv_list->groupcio_status == 2)
                        {
                            $groupciodate = date('d-M-y', strtotime($nv_list->groupcio_timestamp));
                            $groupciotime = date('h:i A', strtotime($nv_list->groupcio_timestamp));
                            $output .='<td>' . $groupcio.'<br>'. 'Rejection Date:'.''. $groupciodate.'<br>'.'Rejection Time:'.''.$groupciotime.'<br>'.'Group Head System IP:'.''.$nv_list->groupcio_action_ip.'<br>'.'Pendency:'.''.$groupheaddays. '</td>';
                        }
                    }
                    else
                    {
                        $groupcio =  getGroupHeadName($nv_list->groupcio_id ?? null);
                        if ($nv_list->groupcio_status == 0)
                        {
                            if($nv_list->ceo_nominee2_status == 1 || $nv_list->ceo_nominee2_status == 2){
                                $output .='<td>' .'N/A' . "\t" . '</td>';
                            }
                            if (!empty($nv_list->groupcio_timestamp))
                            {
                                $groupciodate = date('d-M-y', strtotime($nv_list->groupcio_timestamp));
                                $groupciotime = date('h:i A', strtotime($nv_list->groupcio_timestamp));
                            }
                        }
                        elseif($nv_list->groupcio_status == 1)
                        {
                            $groupciodate = date('d-M-y', strtotime($nv_list->groupcio_timestamp));
                            $groupciotime = date('h:i A', strtotime($nv_list->groupcio_timestamp));
                            $output .='<td>' . $groupcio.'<br>'. 'Approval Date:'.''. $groupciodate.'<br>'.'Approval Time:'.''.$groupciotime.'<br>'.'Group Head System IP:'.''.$nv_list->groupcio_action_ip .'<br>'.'Pendency:'.''.$groupheaddays. '</td>';
                        }
                        elseif($nv_list->groupcio_status == 2)
                        {
                            $groupciodate = date('d-M-y', strtotime($nv_list->groupcio_timestamp));
                            $groupciotime = date('h:i A', strtotime($nv_list->groupcio_timestamp));
                            $output .='<td>' . $groupcio.'<br>'. 'Rejection Date:'.''. $groupciodate.'<br>'.'Rejection Time:'.''.$groupciotime.'<br>'.'Group Head System IP:'.''.$nv_list->groupcio_action_ip.'<br>'.'Pendency:'.''.$groupheaddays. '</td>';
                        }
                    }

                    if ($nv_list->service_id == null)
                    {
                        $ceonom2 =  getCeoNominee2Name($nv_list->ceo_nominee2_id ?? null);
                        if ($nv_list->ceo_nominee2_status == 0)
                        {
                            if (!empty($nv_list->ceo_nominee2_timestamp))
                            {
                                $ceonom2date = date('d-M-y', strtotime($nv_list->ceo_nominee2_timestamp));
                                $ceonom2time = date('h:i A', strtotime($nv_list->ceo_nominee2_timestamp));
                            }
                        }
                        elseif($nv_list->ceo_nominee2_status == 1)
                        {
                            $ceonom2date = date('d-M-y', strtotime($nv_list->ceo_nominee2_timestamp));
                            $ceonom2time = date('h:i A', strtotime($nv_list->ceo_nominee2_timestamp));
                            $output .='<td>' . $ceonom2.'<br>'. 'Approval Date:'.''. $ceonom2date.'<br>'.'Approval Time:'.''.$ceonom2time.'<br>'.'CTO System IP:'.''.$nv_list->ceo_nominee2_action_ip .'<br>'.'Pendency:'.''.$ceon2days. '</td>';
                        }
                        elseif($nv_list->ceo_nominee2_status == 2)
                        {
                            $ceonom2date = date('d-M-y', strtotime($nv_list->ceo_nominee2_timestamp));
                            $ceonom2time = date('h:i A', strtotime($nv_list->ceo_nominee2_timestamp));
                            $output .='<td>' . $ceonom2.'<br>'. 'Rejection Date:'.''. $ceonom2date.'<br>'.'Rejection Time:'.''.$ceonom2time.'<br>'.'CTO System IP:'.''.$nv_list->ceo_nominee2_action_ip.'<br>'.'Pendency:'.''.$ceon2days. '</td>';
                        }
                    }
                    else
                    {
                        $ceonom2 =  getCeoNominee2Name($nv_list->ceo_nominee2_id ?? null);
                        if ($nv_list->ceo_nominee2_status == 0)
                        {
                            if (!empty($nv_list->ceo_nominee2_timestamp))
                            {
                                $ceonom2date = date('d-M-y', strtotime($nv_list->ceo_nominee2_timestamp));
                                $ceonom2time = date('h:i A', strtotime($nv_list->ceo_nominee2_timestamp));
                            }
                        }
                        elseif($nv_list->ceo_nominee2_status == 1)
                        {
                            $ceonom2date = date('d-M-y', strtotime($nv_list->ceo_nominee2_timestamp));
                            $ceonom2time = date('h:i A', strtotime($nv_list->ceo_nominee2_timestamp));
                            $output .='<td>' . $ceonom2.'<br>'. 'Approval Date:'.''. $ceonom2date.'<br>'.'Approval Time:'.''.$ceonom2time.'<br>'.'CTO System IP:'.''.$nv_list->ceo_nominee2_action_ip .'<br>'.'Pendency:'.''.$ceon2days. '</td>';
                        }
                        elseif($nv_list->ceo_nominee2_status == 2)
                        {
                            $ceonom2date = date('d-M-y', strtotime($nv_list->ceo_nominee2_timestamp));
                            $ceonom2time = date('h:i A', strtotime($nv_list->ceo_nominee2_timestamp));
                            $output .='<td>' . $ceonom2.'<br>'. 'Rejection Date:'.''. $ceonom2date.'<br>'.'Rejection Time:'.''.$ceonom2time.'<br>'.'CTO System IP:'.''.$nv_list->ceo_nominee2_action_ip.'<br>'.'Pendency:'.''.$ceon2days. '</td>';
                        }
                    }

                  
                    if ($nv_list->service_id == null)
                    {
                        $ceoname = getCeoName($nv_list->ceo_id ?? null);
                        if ($nv_list->ceo_status == 0)
                        {
                            if (!empty($nv_list->ceo_timestamp))
                            {
                                $ceodate = date('d-M-y', strtotime($nv_list->ceo_timestamp));
                                $ceotime = date('h:i A', strtotime($nv_list->ceo_timestamp));
                            }
                        }
                        elseif($nv_list->ceo_status == 1)
                        {
                            $ceodate = date('d-M-y', strtotime($nv_list->ceo_timestamp));
                            $ceotime = date('h:i A', strtotime($nv_list->ceo_timestamp));
                            $output .='<td>' . $ceoname.'<br>'. 'Approval Date:'.''. $ceodate.'<br>'.'Approval Time:'.''.$ceotime.'<br>'.'CEO System IP:'.''.$nv_list->ceo_action_ip .'<br>'.'Pendency:'.''.$ceodays. '</td>';
                        }
                        elseif($nv_list->ceo_status == 2)
                        {
                            $ceodate = date('d-M-y', strtotime($nv_list->ceo_timestamp));
                            $ceotime = date('h:i A', strtotime($nv_list->ceo_timestamp));
                            $output .='<td>' . $ceoname.'<br>'. 'Rejection Date:'.''. $ceodate.'<br>'.'Rejection Time:'.''.$ceotime.'<br>'.'CEO System IP:'.''.$nv_list->ceo_action_ip.'<br>'.'Pendency:'.''.$ceodays. '</td>';
                        }
                    }
                    else
                    {
                        $ceoname = getCeoName($nv_list->ceo_id ?? null);
                        if ($nv_list->ceo_status == 0)
                        {
                            if (!empty($nv_list->ceo_timestamp))
                            {
                                $ceodate = date('d-M-y', strtotime($nv_list->ceo_timestamp));
                                $ceotime = date('h:i A', strtotime($nv_list->ceo_timestamp));
                            }
                        }
                        elseif($nv_list->ceo_status == 1)
                        {
                        
                            $ceodate = date('d-M-y', strtotime($nv_list->ceo_timestamp));
                            $ceotime = date('h:i A', strtotime($nv_list->ceo_timestamp));
                            $output .='<td>' . $ceoname.'<br>'. 'Approval Date:'.''. $ceodate.'<br>'.'Approval Time:'.''.$ceotime.'<br>'.'CEO System IP:'.''.$nv_list->ceo_action_ip .'<br>'.'Pendency:'.''.$ceodays. '</td>';
                        }
                        elseif($nv_list->ceo_status == 2)
                        {
                        
                            $ceodate = date('d-M-y', strtotime($nv_list->ceo_timestamp));
                            $ceotime = date('h:i A', strtotime($nv_list->ceo_timestamp));
                            $output .='<td>' . $ceoname.'<br>'. 'Rejection Date:'.''. $ceodate.'<br>'.'Rejection Time:'.''.$ceotime.'<br>'.'CEO System IP:'.''.$nv_list->ceo_action_ip.'<br>'.'Pendency:'.''.$ceodays. '</td>';
                        }
                    }
                    $output .= '</tr>';
                }
          
                  
            }
            $output .= '</table>';
            $output .= '</body></html>';
               
            header("Content-type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet");
            header("Content-Disposition: attachment; filename=NV_excel.xls");
            header("Pragma: no-cache");
            header("Expires: 0");
            echo $output;      
    }
            
}
