<?php
namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
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
use DB;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
class DashboardController extends Controller {
    public function __construct() {
        $this->middleware(function ($request, $next) {
            Session::put("active", "dashboard");
            return $next($request);
        });
    }
    public function dashboard(Request $request) {
        $user = \Auth::user();
        $user_id = [];
        $employees = Employee::where("user_id", $user->id)->first();
        $employeess = Employee::where("report_to", $user->id)->get();
        foreach ($employeess as $employee) {
            array_push($user_id, $employee["user_id"]);
        }
        $ceo_status = $request->approved;
        if (!empty($user->role_id == 1)) {
            // $ceo_status = $request->approved;
            // $totalNV = NeedValidation::count();
            $approvedNV = Nvsericestatus::where("ceo_status", 1)->count();
                       $totalNV=Nvsericestatus::count();
                       $rejectedNV = Nvsericestatus::where("hod_status", 2)
                       ->Orwhere("cto_status", 2)
                       ->Orwhere("cpmg_status", 2)
                       ->Orwhere("ceo_nominee_status", 2)
                       ->Orwhere("ceo_nominee2_status", 2)
                    //    ->Orwhere("ceo_nominee2_status", 2)
            ->Orwhere("ceo_status", 2)->count();
            // $pendingNV = Nvsericestatus::where("ceo_status",0)->count();
            $totalId = NeedValidation::pluck('id');
           
            $latestData = Nvsericestatus::whereIn("nv_id", $totalId)->get();
            $pendingNV = $latestData->filter(function ($data) {
                           return  in_array($data->rv1_status, [1]) ||
                               in_array($data->rv2_status, [0,1]) ||      
                               in_array($data->rv3_status, [0,1]) ||
                               in_array($data->rv4_status, [0,1]) ||
                               in_array($data->hod_status, [0,1]) &&
                               in_array($data->cpmg_status, [0, 1]) &&
                               in_array($data->cto_status, [0, 1]) &&
                               in_array($data->ceo_nominee_status, [0, 1]) &&
                               in_array($data->ceo_nominee2_status, [0, 1]) &&
                               in_array($data->ceo_status, [0]);
            })->count();
            //echo $pendingNV;
            // exit;
            $pendingNV = $pendingNV - $rejectedNV - $approvedNV;
            $totalNV = Nvsericestatus::whereIn("nv_id", $totalId)->where('ceo_status', 1)->count();
            $rejectedNV = $latestData->filter(function ($data) {
                return in_array($data->ceo_status, [2]) || $data->hod_status == 2 || $data->cpmg_status == 2 || $data->ces_status == 2 || $data->cto_status == 2 || $data->ceo_nominee_status == 2 || $data->ceo_nominee2_status == 2;
                //    $data->ceo_nominee2_status == 2 ;
                
            })->count();
            $revertedNV = $rejectedNV;
            // $pendingNV=$pendingNV-$approvedNV ;
            $hodApproval = Nvsericestatus::where("hod_status", 0)->count();
            $cpmgApproval = Nvsericestatus::where('hod_status', 1)->where('cpmg_status', 0)->count();
            $btApproval = Nvsericestatus::where("cpmg_status", 1)->where('cto_status', 0)->count();
            $ceonominee1Approval = Nvsericestatus::where('cto_status', 1)->where('ceo_nominee_status', 0)->count();
            $ceonominee2Approval = Nvsericestatus::where('ceo_nominee_status', 1)->where('ceo_nominee2_status', 0)->count();
            //    $ceonominee2A2pproval=Nvsericestatus::where('ceo_nominee2_status', 1)->where('ceo_nominee2_status', 0)->count();
            $ceoApproval = Nvsericestatus::where('ceo_nominee2_status', 1)->where('ceo_status', 0)->count();
            $dpnv = NVService::where('dept_id', '!=', 'Null')->count() + NVMaterial::where('dept_id', '!=', 'Null')->count();
            $dpbpinv = NVService::where('dept_id', 1)->count() + NVMaterial::where('dept_id', 1)->count();
            $dpceocellnv = NVService::where('dept_id', 2)->count() + NVMaterial::where('dept_id', 2)->count();
            $dpregnv = NVService::where('dept_id', 3)->count() + NVMaterial::where('dept_id', 3)->count();
            $dpomnv = NVService::where('dept_id', 4)->count() + NVMaterial::where('dept_id', 4)->count();
            $dpinfonv = NVService::where('dept_id', 5)->count() + NVMaterial::where('dept_id', 5)->count();
            $dpsafenv = NVService::where('dept_id', 7)->count() + NVMaterial::where('dept_id', 7)->count();
            $dpdsmnv = NVService::where('dept_id', 10)->count() + NVMaterial::where('dept_id', 10)->count();
            $dpbetnv = NVService::where('dept_id', 8)->count() + NVMaterial::where('dept_id', 8)->count();
            $nvIds = NeedValidation::pluck("id");
            $nv_sm_data = Nvsericestatus::whereIn('nv_id', $nvIds)->with(['service', 'material', 'user'])->orderBy('id', 'desc');
            if ($ceo_status) {
                $nv_sm_data->where('ceo_status', $ceo_status);
            }
            $nv_sm_data = $nv_sm_data->get();


            $BRPLnv = NeedValidation::where('company_id','6')->pluck("id");
            $fileDataBRPL =Nvsericestatus::
            select(
                DB::raw('MONTH(created_at) as month'),
                DB::raw('SUM(CASE WHEN ceo_status = "1" THEN 1 ELSE 0 END) as approved_count'),
                DB::raw('SUM(CASE WHEN ( hod_status = "2" OR cpmg_status = "2" OR cto_status = "2" OR ceo_nominee_status = "2" OR ceo_nominee2_status = "2" OR ceo_status = "2")  THEN 1 ELSE 0 END) as rejected_count'),
                DB::raw('SUM(CASE WHEN ceo_status = "0" THEN 1 ELSE 0 END) as pending_count'),
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

        $BYPLnv = NeedValidation::where('company_id','5')->pluck("id");
        // dd( $BYPLnv );
         $fileDataBYPL =Nvsericestatus::
        select(
            DB::raw('MONTH(created_at) as month'),
            DB::raw('SUM(CASE WHEN ceo_status = "1" THEN 1 ELSE 0 END) as approved_count'),
            DB::raw('SUM(CASE WHEN ( hod_status = "2" OR cpmg_status = "2" OR cto_status = "2" OR ceo_nominee_status = "2" OR ceo_nominee2_status = "2" OR ceo_status = "2")  THEN 1 ELSE 0 END) as rejected_count'),
            DB::raw('SUM(CASE WHEN ceo_status = "0" THEN 1 ELSE 0 END) as pending_count'),
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
         // dd( $dataPointBYPL->rejected_count);
    }
    
       return view("admin.dashboard", compact("ceoApproval", "ceonominee1Approval", "ceonominee2Approval", "nv_sm_data", "totalNV", "approvedNV", "rejectedNV", "pendingNV", "revertedNV", "hodApproval", "cpmgApproval", "btApproval", "dpnv", "dpbpinv", "dpceocellnv", "dpregnv", "dpomnv", "dpinfonv", "dpsafenv", "dpdsmnv", "dpbetnv",'BRPLlabels','BRPLapprovedData','BRPLrejectedData','BRPLpendingData','BYPLlabels','BYPLapprovedData','BYPLrejectedData','BYPLpendingData'));
        } else {
            $id1 = Workflow::where("id", 1)->first();
            $id2 = Workflow::skip(1)->first();
            $id3 = Workflow::skip(2)->first();
            $id4 = Workflow::skip(3)->first();
            $id5 = Workflow::skip(4)->first();
            // dd($employees);
            // Retrieve the employee and eager load the department relationship
            $employee = Employee::where('user_id', $user->id)->with('department')->first();
            $allusers = Employee::where('department_id', $employee->department_id)->get();
            $allNormalUsers = $allusers->where('role_id', 9)->pluck('user_id');
            // dd($allNormalUsers);
            // Access the department and its attributes using optional() to handle null values
            $department = optional($employee->department);
            $hod = $department->dep_hod??null;
            $dep_rew1 = $department->dep_rew1??null;
            $dep_rew2 = $department->dep_rew2??null;
            $dep_rew3 = $department->dep_rew3??null;
            $dep_rew4 = $department->dep_rew4??null;
            $Values = [$hod, $dep_rew1, $dep_rew2, $dep_rew3, $dep_rew4];
            // Access the department for reviewer 1
            if ($dep_rew1 == $user->id) {
                $Values = [$user->id, $dep_rew1];
                // $totalNV = NeedValidation::where("user_id", $user->id)->count();
                $totalId = NeedValidation::whereIn("user_id", $Values)->orWhereIn('user_id', $allNormalUsers)->pluck('id');
                // dd($totalId);
                $totalNV = Nvsericestatus::whereIn("nv_id", $totalId)->where('rv1_status', 1)->count();
            
            
              
                $latestData = Nvsericestatus::whereIn("nv_id", $totalId)->get();
                $approvedNV = $latestData->where('ceo_status', 1)->count();
                $rejectedNV = $latestData->filter(function ($data) {
                    return in_array($data->ceo_status, [2]) || 
                    // $data->rv1_status == 2 || 
                    $data->rv1_status == 2 ;
                    // $data->rv3_status == 2 ||
                    // $data->rv4_status == 2 ||
                    // $data->hod_status == 2 || 
                    // $data->cpmg_status == 2 || 
                    // $data->ces_status == 2 || 
                    // $data->cto_status == 2 || 
                    // $data->ceo_nominee_status == 2 || 
                    // $data->ceo_nominee2_status == 2 || 
                    // $data->ceo_nominee2_status == 2;
                })->count();
                // $pendingNV = $latestData->filter(function ($data) {
                //     return  in_array($data->rv1_status, [0,1]) &&
                //         in_array($data->rv2_status, [0]);
                //         in_array($data->rv3_status, [0]) ||
                //         in_array($data->rv4_status, [0]) ||
                //         in_array($data->hod_status, [0]) &&
                //         in_array($data->cpmg_status, [0]) &&
                //         in_array($data->ces_status, [0]) &&
                //         in_array($data->cto_status, [0]) &&
                //         in_array($data->ceo_nominee_status, [0]) &&
                //         in_array($data->ceo_status, [0]);
                // })->count();
                $pendingNV = Nvsericestatus::whereIn("nv_id", $totalId)->where('rv1_status', 0)->count();
                // $pendingNV=$pendingNV-$approvedNV-$rejectedNV;
                // $totalNV = Nvsericestatus::whereIn("nv_id", $totalId)->where('rv1_status', 0)->count();
                $revertedNV = $rejectedNV;
                $hodApproval = Nvsericestatus::where("hod_status", 0)->count();
                $cpmgApproval = Nvsericestatus::where('cto_status', 1)->where('cpmg_status', 0)->count();
                $btApproval = Nvsericestatus::where("hod_status", 1)->where('cto_status', 0)->count();
                // $ceonominee1Approval =  Nvsericestatus::where('cpmg_status', 1)->where('ceo_nominee_status', 0)->count();
                // $ceonominee2Approval=Nvsericestatus::where('ceo_nominee_status', 1)->where('ceo_nominee2_status', 0)->count();
                // $ceonominee2A2pproval=Nvsericestatus::where('ceo_nominee2_status', 1)->where('ceo_nominee2_status', 0)->count();
                $ceoApproval = Nvsericestatus::where('ceo_nominee2_status', 1)->where('ceo_status', 0)->count();
                $dpnv = NVService::where("user_id", $user->id)->where('dept_id', '!=', 'Null')->count() + NVMaterial::where("user_id", $user->id)->where('dept_id', '!=', 'Null')->count();
                $dpbpinv = NVService::where("user_id", $user->id)->where('dept_id', 1)->count() + NVMaterial::where("user_id", $user->id)->where('dept_id', 1)->count();
                $dpceocellnv = NVService::where("user_id", $user->id)->where('dept_id', 2)->count() + NVMaterial::where("user_id", $user->id)->where('dept_id', 2)->count();
                $dpregnv = NVService::where("user_id", $user->id)->where('dept_id', 3)->count() + NVMaterial::where("user_id", $user->id)->where('dept_id', 3)->count();
                $dpomnv = NVService::where("user_id", $user->id)->where('dept_id', 4)->count() + NVMaterial::where("user_id", $user->id)->where('dept_id', 4)->count();
                $dpinfonv = NVService::where("user_id", $user->id)->where('dept_id', 5)->count() + NVMaterial::where("user_id", $user->id)->where('dept_id', 5)->count();
                $dpsafenv = NVService::where("user_id", $user->id)->where('dept_id', 7)->count() + NVMaterial::where("user_id", $user->id)->where('dept_id', 7)->count();
                $dpdsmnv = NVService::where("user_id", $user->id)->where('dept_id', 10)->count() + NVMaterial::where("user_id", $user->id)->where('dept_id', 10)->count();
                $dpbetnv = NVService::where("user_id", $user->id)->where('dept_id', 8)->count() + NVMaterial::where("user_id", $user->id)->where('dept_id', 8)->count();
                $nvIds = NeedValidation::whereIn("user_id", $user_id)->pluck("id");
                $nv = NeedValidation::whereHas('service')->select('id')->where('user_id', $user->id)->orWhereIn('user_id', $allNormalUsers)->get();
                $nv_ids = $nv->pluck('id');
                $nv_sm_data = Nvsericestatus::whereIn('nv_id', $nv_ids)->with(['service', 'material', 'user'])->orderBy('id', 'desc');
                if ($ceo_status) {
                    $nv_sm_data->where('ceo_status', $ceo_status);
                }
                $nv_sm_data = $nv_sm_data->get();
                return view("admin.dashboard", compact("nv_sm_data", "totalNV", "approvedNV", "rejectedNV", "pendingNV", "revertedNV", "hodApproval", "cpmgApproval", "btApproval", "ceoApproval", "dpnv", "dpbpinv", "dpceocellnv", "dpregnv", "dpomnv", "dpinfonv", "dpsafenv", "dpdsmnv", "dpbetnv"));
            } elseif ($dep_rew2 == $user->id) {
                $Values = [$user->id, $dep_rew1];
                // $totalNV = NeedValidation::where("user_id", $user->id)->count();
                $totalId = NeedValidation::whereIn("user_id", $Values)->orWhereIn('user_id', $allNormalUsers)->pluck('id');
                $latestData = Nvsericestatus::whereIn("nv_id", $totalId)->get();
                $approvedNV = $latestData->where('ceo_status', 1)->count();
                $rejectedNV = $latestData->filter(function ($data) {
                    return in_array($data->ceo_status, [2]) || 
                    // $data->rv1_status == 2 || 
                    $data->rv2_status == 2 ;
                    // $data->rv3_status == 2 ||
                    // $data->rv4_status == 2 ||
                    // $data->hod_status == 2 || 
                    // $data->cpmg_status == 2 || 
                    // $data->ces_status == 2 || 
                    // $data->cto_status == 2 || 
                    // $data->ceo_nominee_status == 2 || 
                    // $data->ceo_nominee2_status == 2 || 
                    // $data->ceo_nominee2_status == 2;
                })->count();
                $pendingNV = $latestData->filter(function ($data) {
                    return  in_array($data->rv1_status, [1]) &&
                        in_array($data->rv2_status, [0]);
                        in_array($data->rv3_status, [0]) ||
                        in_array($data->rv4_status, [0]) ||
                        in_array($data->hod_status, [0]) &&
                        in_array($data->cpmg_status, [0]) &&
                        in_array($data->ces_status, [0]) &&
                        in_array($data->cto_status, [0]) &&
                        in_array($data->ceo_nominee_status, [0]) &&
                        in_array($data->ceo_status, [0]);
                })->count();
                //echo $pendingNV;
                // exit;
                // $pendingNV = Nvsericestatus::whereIn("nv_id", $totalId)->where('rv1_status', 1)->where('rv2_status', 0)->count();
                // $pendingNV=$pendingNV-$approvedNV-$rejectedNV;
                $totalNV = Nvsericestatus::whereIn("nv_id", $totalId)->where('rv2_status', 1)->count();
                $revertedNV = $rejectedNV;
                $hodApproval = Nvsericestatus::where("hod_status", 0)->count();
                $cpmgApproval = Nvsericestatus::where('cpmg_status', 0)->where('hod_status', 1)->count();
                $btApproval = Nvsericestatus::where("cpmg_status", 1)->where('cto_status', 0)->count();
                // $ceonominee1Approval =  Nvsericestatus::where('cpmg_status', 1)->where('ceo_nominee_status', 0)->count();
                // $ceonominee2Approval=Nvsericestatus::where('ceo_nominee_status', 1)->where('ceo_nominee2_status', 0)->count();
                // $ceonominee2A2pproval=Nvsericestatus::where('ceo_nominee2_status', 1)->where('ceo_nominee2_status', 0)->count();
                $ceoApproval = Nvsericestatus::where('ceo_nominee2_status', 1)->where('ceo_status', 0)->count();
                $dpnv = NVService::where("user_id", $user->id)->where('dept_id', '!=', 'Null')->count() + NVMaterial::where("user_id", $user->id)->where('dept_id', '!=', 'Null')->count();
                $dpbpinv = NVService::where("user_id", $user->id)->where('dept_id', 1)->count() + NVMaterial::where("user_id", $user->id)->where('dept_id', 1)->count();
                $dpceocellnv = NVService::where("user_id", $user->id)->where('dept_id', 2)->count() + NVMaterial::where("user_id", $user->id)->where('dept_id', 2)->count();
                $dpregnv = NVService::where("user_id", $user->id)->where('dept_id', 3)->count() + NVMaterial::where("user_id", $user->id)->where('dept_id', 3)->count();
                $dpomnv = NVService::where("user_id", $user->id)->where('dept_id', 4)->count() + NVMaterial::where("user_id", $user->id)->where('dept_id', 4)->count();
                $dpinfonv = NVService::where("user_id", $user->id)->where('dept_id', 5)->count() + NVMaterial::where("user_id", $user->id)->where('dept_id', 5)->count();
                $dpsafenv = NVService::where("user_id", $user->id)->where('dept_id', 7)->count() + NVMaterial::where("user_id", $user->id)->where('dept_id', 7)->count();
                $dpdsmnv = NVService::where("user_id", $user->id)->where('dept_id', 10)->count() + NVMaterial::where("user_id", $user->id)->where('dept_id', 10)->count();
                $dpbetnv = NVService::where("user_id", $user->id)->where('dept_id', 8)->count() + NVMaterial::where("user_id", $user->id)->where('dept_id', 8)->count();
                $nvIds = NeedValidation::whereIn("user_id", $Values)->pluck("id");
                $nv = NeedValidation::whereHas('service')->select('id')->whereIn('user_id', $Values)->orWhereIn('user_id', $allNormalUsers)->get();
                $nv_ids = $nv->pluck('id');
                $nv_sm_data = Nvsericestatus::whereIn('nv_id', $nv_ids)->with(['service', 'material', 'user'])
                ->where('rv1_status',1)
                ->orderBy('id', 'desc');
                if ($ceo_status) {
                    $nv_sm_data->where('ceo_status', $ceo_status);
                }
                $nv_sm_data = $nv_sm_data->get();
                // dd($nv_sm_data );
                return view("admin.dashboard", compact("nv_sm_data", "totalNV", "approvedNV", "rejectedNV", "pendingNV", "revertedNV", "hodApproval", "cpmgApproval", "btApproval", "ceoApproval", "dpnv", "dpbpinv", "dpceocellnv", "dpregnv", "dpomnv", "dpinfonv", "dpsafenv", "dpdsmnv", "dpbetnv"));
            } elseif ($dep_rew3 == $user->id) {
                $Values = [$user->id, $dep_rew1, $dep_rew2];
                // $totalNV = NeedValidation::where("user_id", $user->id)->count();
                $totalId = NeedValidation::whereIn("user_id", $Values)->orWhereIn('user_id', $allNormalUsers)->pluck('id');
                $latestData = Nvsericestatus::whereIn("nv_id", $totalId)->get();
                $approvedNV = $latestData->where('ceo_status', 1)->count();
                $rejectedNV = $latestData->filter(function ($data) {
                    return in_array($data->ceo_status, [2]) || 
                    $data->rv3_status == 2 ;
                    // $data->hod_status == 2 || 
                    // $data->cpmg_status == 2 || 
                    // $data->ces_status == 2 || 
                    // $data->cto_status == 2 || 
                    // $data->ceo_nominee_status == 2 || 
                    // $data->ceo_nominee2_status == 2 || 
                    // $data->ceo_nominee2_status == 2;
                })->count();
                $pendingNV = $latestData->filter(function ($data) {
                    return  in_array($data->rv1_status, [1]) &&
                        in_array($data->rv2_status, [1])&&
                        in_array($data->rv3_status, [0]) &&
                        in_array($data->rv4_status, [0]) &&
                        in_array($data->hod_status, [0]) &&
                        in_array($data->cpmg_status, [0]) &&
                        in_array($data->ces_status, [0]) &&
                        in_array($data->cto_status, [0]) &&
                        in_array($data->ceo_nominee_status, [0]) &&
                        in_array($data->ceo_status, [0]);
                })->count();
                // $pendingNV = Nvsericestatus::whereIn("nv_id", $totalId)->where('rv2_status', 1)->where('rv3_status', 0)->count();
                // $pendingNV=$pendingNV-$approvedNV-$rejectedNV;
                $totalNV = Nvsericestatus::whereIn("nv_id", $totalId)->where('rv3_status', 1)->count();
                $revertedNV = $rejectedNV;
                $hodApproval = Nvsericestatus::where("hod_status", 0)->count();
                $btApproval = Nvsericestatus::where("hod_status", 1)->where('cto_status', 0)->count();
                $cpmgApproval = Nvsericestatus::where('cto_status', 1)->where('cpmg_status', 0)->count();
                
                // $ceonominee1Approval =  Nvsericestatus::where('cpmg_status', 1)->where('ceo_nominee_status', 0)->count();
                // $ceonominee2Approval=Nvsericestatus::where('ceo_nominee_status', 1)->where('ceo_nominee2_status', 0)->count();
                // $ceonominee2A2pproval=Nvsericestatus::where('ceo_nominee2_status', 1)->where('ceo_nominee2_status', 0)->count();
                $ceoApproval = Nvsericestatus::where('ceo_nominee2_status', 1)->where('ceo_status', 0)->count();
                $dpnv = NVService::where("user_id", $user->id)->where('dept_id', '!=', 'Null')->count() + NVMaterial::where("user_id", $user->id)->where('dept_id', '!=', 'Null')->count();
                $dpbpinv = NVService::where("user_id", $user->id)->where('dept_id', 1)->count() + NVMaterial::where("user_id", $user->id)->where('dept_id', 1)->count();
                $dpceocellnv = NVService::where("user_id", $user->id)->where('dept_id', 2)->count() + NVMaterial::where("user_id", $user->id)->where('dept_id', 2)->count();
                $dpregnv = NVService::where("user_id", $user->id)->where('dept_id', 3)->count() + NVMaterial::where("user_id", $user->id)->where('dept_id', 3)->count();
                $dpomnv = NVService::where("user_id", $user->id)->where('dept_id', 4)->count() + NVMaterial::where("user_id", $user->id)->where('dept_id', 4)->count();
                $dpinfonv = NVService::where("user_id", $user->id)->where('dept_id', 5)->count() + NVMaterial::where("user_id", $user->id)->where('dept_id', 5)->count();
                $dpsafenv = NVService::where("user_id", $user->id)->where('dept_id', 7)->count() + NVMaterial::where("user_id", $user->id)->where('dept_id', 7)->count();
                $dpdsmnv = NVService::where("user_id", $user->id)->where('dept_id', 10)->count() + NVMaterial::where("user_id", $user->id)->where('dept_id', 10)->count();
                $dpbetnv = NVService::where("user_id", $user->id)->where('dept_id', 8)->count() + NVMaterial::where("user_id", $user->id)->where('dept_id', 8)->count();
                $nvIds = NeedValidation::whereIn("user_id", $Values)->pluck("id");
                $nv = NeedValidation::whereHas('service')->select('id')->whereIn('user_id', $Values)->orWhereIn('user_id', $allNormalUsers)->get();
                $nv_ids = $nv->pluck('id');
                $nv_sm_data = Nvsericestatus::whereIn('nv_id', $nv_ids)->with(['service', 'material', 'user'])
                ->where('rv2_status',1)
                ->orderBy('id', 'desc');
                if ($ceo_status) {
                    $nv_sm_data->where('ceo_status', $ceo_status);
                }
                $nv_sm_data = $nv_sm_data->get();
                // dd($nv_sm_data );
                return view("admin.dashboard", compact("nv_sm_data", "totalNV", "approvedNV", "rejectedNV", "pendingNV", "revertedNV", "hodApproval", "cpmgApproval", "btApproval", "ceoApproval", "dpnv", "dpbpinv", "dpceocellnv", "dpregnv", "dpomnv", "dpinfonv", "dpsafenv", "dpdsmnv", "dpbetnv"));
            } elseif ($dep_rew4 == $user->id) {
                $Values = [$user->id, $dep_rew1, $dep_rew2, $dep_rew3];
                // $totalNV = NeedValidation::where("user_id", $user->id)->count();
                $totalId = NeedValidation::whereIn("user_id", $Values)->orWhereIn('user_id', $allNormalUsers)->pluck('id');
                $latestData = Nvsericestatus::whereIn("nv_id", $totalId)->get();
                $approvedNV = $latestData->where('ceo_status', 1)->count();
                $rejectedNV = $latestData->filter(function ($data) {
                    return in_array($data->ceo_status, [2]) || 
                    $data->rv4_status == 2 ;
                    // $data->hod_status == 2 || 
                    // $data->cpmg_status == 2 || 
                    // $data->ces_status == 2 || 
                    // $data->cto_status == 2 || 
                    // $data->ceo_nominee_status == 2 || 
                    // $data->ceo_nominee2_status == 2 || 
                    // $data->ceo_nominee2_status == 2;
                })->count();
                 $pendingNV = $latestData->filter(function ($data) {
                    return  in_array($data->rv1_status, [1]) &&
                        in_array($data->rv2_status, [1])&&
                        in_array($data->rv3_status, [1]) &&
                        in_array($data->rv4_status, [0]) &&
                        in_array($data->hod_status, [0]) &&
                        in_array($data->cpmg_status, [0]) &&
                        in_array($data->ces_status, [0]) &&
                        in_array($data->cto_status, [0]) &&
                        in_array($data->ceo_nominee_status, [0]) &&
                        in_array($data->ceo_status, [0]);
                })->count();
                // $pendingNV = Nvsericestatus::whereIn("nv_id", $totalId)->where('rv1_status', 1)->where('rv2_status', 1)->where('rv3_status', 1)->where('rv4_status', 0)->count();
                // $pendingNV=$pendingNV-$approvedNV-$rejectedNV;
                $totalNV = Nvsericestatus::whereIn("nv_id", $totalId)->where('rv4_status', 1)->count();
                $revertedNV = $rejectedNV;
                $hodApproval = Nvsericestatus::where("hod_status", 0)->count();
                $cpmgApproval = Nvsericestatus::where('cto_status', 1)->where('cpmg_status', 0)->count();
                $btApproval = Nvsericestatus::where("hod_status", 1)->where('cto_status', 0)->count();
                // $ceonominee1Approval =  Nvsericestatus::where('cpmg_status', 1)->where('ceo_nominee_status', 0)->count();
                // $ceonominee2Approval=Nvsericestatus::where('ceo_nominee_status', 1)->where('ceo_nominee2_status', 0)->count();
                // $ceonominee2A2pproval=Nvsericestatus::where('ceo_nominee2_status', 1)->where('ceo_nominee2_status', 0)->count();
                $ceoApproval = Nvsericestatus::where('ceo_nominee2_status', 1)->where('ceo_status', 0)->count();
                $dpnv = NVService::where("user_id", $user->id)->where('dept_id', '!=', 'Null')->count() + NVMaterial::where("user_id", $user->id)->where('dept_id', '!=', 'Null')->count();
                $dpbpinv = NVService::where("user_id", $user->id)->where('dept_id', 1)->count() + NVMaterial::where("user_id", $user->id)->where('dept_id', 1)->count();
                $dpceocellnv = NVService::where("user_id", $user->id)->where('dept_id', 2)->count() + NVMaterial::where("user_id", $user->id)->where('dept_id', 2)->count();
                $dpregnv = NVService::where("user_id", $user->id)->where('dept_id', 3)->count() + NVMaterial::where("user_id", $user->id)->where('dept_id', 3)->count();
                $dpomnv = NVService::where("user_id", $user->id)->where('dept_id', 4)->count() + NVMaterial::where("user_id", $user->id)->where('dept_id', 4)->count();
                $dpinfonv = NVService::where("user_id", $user->id)->where('dept_id', 5)->count() + NVMaterial::where("user_id", $user->id)->where('dept_id', 5)->count();
                $dpsafenv = NVService::where("user_id", $user->id)->where('dept_id', 7)->count() + NVMaterial::where("user_id", $user->id)->where('dept_id', 7)->count();
                $dpdsmnv = NVService::where("user_id", $user->id)->where('dept_id', 10)->count() + NVMaterial::where("user_id", $user->id)->where('dept_id', 10)->count();
                $dpbetnv = NVService::where("user_id", $user->id)->where('dept_id', 8)->count() + NVMaterial::where("user_id", $user->id)->where('dept_id', 8)->count();
                $nvIds = NeedValidation::whereIn("user_id", $Values)->pluck("id");
                $nv = NeedValidation::whereHas('service')->select('id')->whereIn('user_id', $Values)->orWhereIn('user_id', $allNormalUsers)->get();
                $nv_ids = $nv->pluck('id');
                $nv_sm_data = Nvsericestatus::whereIn('nv_id', $nv_ids)->with(['service', 'material', 'user'])
                ->where('rv3_status',1)
                ->orderBy('id', 'desc');
                if ($ceo_status) {
                    $nv_sm_data->where('ceo_status', $ceo_status);
                }
                $nv_sm_data = $nv_sm_data->get();
                return view("admin.dashboard", compact("nv_sm_data", "totalNV", "approvedNV", "rejectedNV", "pendingNV", "revertedNV", "hodApproval", "cpmgApproval", "btApproval", "ceoApproval", "dpnv", "dpbpinv", "dpceocellnv", "dpregnv", "dpomnv", "dpinfonv", "dpsafenv", "dpdsmnv", "dpbetnv"));
            } elseif ($hod == $user->id) {
                $Values = [$user->id, $dep_rew1, $dep_rew2, $dep_rew3, $dep_rew4];
                // $totalNV = NeedValidation::where("user_id", $user->id)->count();
                $totalId = NeedValidation::whereIn("user_id", $Values)->orWhereIn('user_id', $allNormalUsers)->pluck('id');
                // dd( $totalId);
                $latestData = Nvsericestatus::whereIn("nv_id", $totalId)->get();
                $approvedNV = $latestData->where('ceo_status', 1)->count();
                $rejectedNV = $latestData->filter(function ($data) {
                    return in_array($data->ceo_status, [2]) || 
                    $data->hod_status == 2 ;
                    // $data->hod_status == 2 || 
                    // $data->cpmg_status == 2 || 
                    // $data->ces_status == 2 || 
                    // $data->cto_status == 2 || 
                    // $data->ceo_nominee_status == 2 || 
                    // $data->ceo_nominee2_status == 2 || 
                    // $data->ceo_nominee2_status == 2;
                })->count();
                $pendingNV = $latestData->filter(function ($data) {
                    return  in_array($data->rv1_status, [1]) &&
                        in_array($data->rv2_status, [0,1])&&
                        in_array($data->rv3_status, [0,1]) &&
                        in_array($data->rv4_status, [0,1]) &&          
                        in_array($data->hod_status, [0]) &&
                        in_array($data->cpmg_status, [0]) &&
                        in_array($data->ces_status, [0]) &&
                        in_array($data->cto_status, [0]) &&
                        in_array($data->ceo_nominee_status, [0]) &&
                        in_array($data->ceo_status, [0]);
                })->count();
                // $pendingNV = Nvsericestatus::whereIn("nv_id", $totalId)->where('hod_status', 0)->count();
                $totalNV = Nvsericestatus::whereIn("nv_id", $totalId)->where('hod_status', 1)->count();
                $revertedNV = $rejectedNV;
                $hodApproval = Nvsericestatus::where("hod_status", 0)->count();
                $cpmgApproval = Nvsericestatus::where('cto_status', 1)->where('cpmg_status', 0)->count();
                $btApproval = Nvsericestatus::where("hod_status", 1)->where('cto_status', 0)->count();
                // $ceonominee1Approval =  Nvsericestatus::where('cpmg_status', 1)->where('ceo_nominee_status', 0)->count();
                // $ceonominee2Approval=Nvsericestatus::where('ceo_nominee_status', 1)->where('ceo_nominee2_status', 0)->count();
                // $ceonominee2A2pproval=Nvsericestatus::where('ceo_nominee2_status', 1)->where('ceo_nominee2_status', 0)->count();
                $ceoApproval = Nvsericestatus::where('ceo_nominee2_status', 1)->where('ceo_status', 0)->count();
                $dpnv = NVService::where("user_id", $user->id)->where('dept_id', '!=', 'Null')->count() + NVMaterial::where("user_id", $user->id)->where('dept_id', '!=', 'Null')->count();
                $dpbpinv = NVService::where("user_id", $user->id)->where('dept_id', 1)->count() + NVMaterial::where("user_id", $user->id)->where('dept_id', 1)->count();
                $dpceocellnv = NVService::where("user_id", $user->id)->where('dept_id', 2)->count() + NVMaterial::where("user_id", $user->id)->where('dept_id', 2)->count();
                $dpregnv = NVService::where("user_id", $user->id)->where('dept_id', 3)->count() + NVMaterial::where("user_id", $user->id)->where('dept_id', 3)->count();
                $dpomnv = NVService::where("user_id", $user->id)->where('dept_id', 4)->count() + NVMaterial::where("user_id", $user->id)->where('dept_id', 4)->count();
                $dpinfonv = NVService::where("user_id", $user->id)->where('dept_id', 5)->count() + NVMaterial::where("user_id", $user->id)->where('dept_id', 5)->count();
                $dpsafenv = NVService::where("user_id", $user->id)->where('dept_id', 7)->count() + NVMaterial::where("user_id", $user->id)->where('dept_id', 7)->count();
                $dpdsmnv = NVService::where("user_id", $user->id)->where('dept_id', 10)->count() + NVMaterial::where("user_id", $user->id)->where('dept_id', 10)->count();
                $dpbetnv = NVService::where("user_id", $user->id)->where('dept_id', 8)->count() + NVMaterial::where("user_id", $user->id)->where('dept_id', 8)->count();
                $nvIds = NeedValidation::whereIn("user_id", $Values)->pluck("id");
                $nv = NeedValidation::whereHas('service')->select('id')->whereIn('user_id', $Values)->orWhereIn('user_id', $allNormalUsers)->get();
                $nv_ids = $nv->pluck('id');
                $nv_sm_data = Nvsericestatus::whereIn('nv_id', $nv_ids)->with(['service', 'material', 'user'])->orderBy('id', 'desc');
                if ($ceo_status) {
                    $nv_sm_data->where('ceo_status', $ceo_status);
                }
                $nv_sm_data = $nv_sm_data->get();
                // dd($nv_sm_data );
                return view("admin.dashboard", compact("nv_sm_data", "totalNV", "approvedNV", "rejectedNV", "pendingNV", "revertedNV", "hodApproval", "cpmgApproval", "btApproval", "ceoApproval", "dpnv", "dpbpinv", "dpceocellnv", "dpregnv", "dpomnv", "dpinfonv", "dpsafenv", "dpdsmnv", "dpbetnv"));
            } elseif ($id1->work_rew1 == $user->id) {
                $totalNV = NeedValidation::where("user_id", $user->id)->count();
                $totalId = NeedValidation::where("user_id", $user->id)->pluck('id');
                // dd($totalId);
                $latestData = Nvsericestatus::whereIn("nv_id", $totalId)->get();
                $approvedNV = $latestData->where('ceo_status', 1)->count();
                $rejectedNV = $latestData->filter(function ($data) {
                    return in_array($data->ceo_status, [2]) || $data->hod_status == 2 || $data->cpmg_status == 2 || $data->ces_status == 2 || $data->cto_status == 2 || $data->ceo_nominee_status == 2 || $data->ceo_nominee2_status == 2;
                })->count();
                // $pendingNV = $latestData->filter(function ($data) {
                // return in_array($data->hod_status, [0, 1]) &&
                // in_array($data->cpmg_status, [0, 1]) &&
                // in_array($data->ces_status, [0, 1]) &&
                // in_array($data->cto_status, [0, 1]) &&
                // in_array($data->ceo_nominee_status, [0, 1]) &&
                // in_array($data->ceo_status, [0, 1]);
                // })->count();
                $pendingNV = Nvsericestatus::whereIn("nv_id", $totalId)->where('rv1_status', 0)->count();
                // $pendingNV=$pendingNV-$approvedNV-$rejectedNV;
                $totalNV = Nvsericestatus::whereIn("nv_id", $totalId)->where('rv1_status', 1)->count();
                $revertedNV = $rejectedNV;
                $hodApproval = Nvsericestatus::where("hod_status", 0)->count();
                $cpmgApproval = Nvsericestatus::where('cto_status', 1)->where('cpmg_status', 0)->count();
                $btApproval = Nvsericestatus::where("hod_status", 1)->where('cto_status', 0)->count();
                // $ceonominee1Approval =  Nvsericestatus::where('cpmg_status', 1)->where('ceo_nominee_status', 0)->count();
                // $ceonominee2Approval=Nvsericestatus::where('ceo_nominee_status', 1)->where('ceo_nominee2_status', 0)->count();
                // $ceonominee2A2pproval=Nvsericestatus::where('ceo_nominee2_status', 1)->where('ceo_nominee2_status', 0)->count();
                $ceoApproval = Nvsericestatus::where('ceo_nominee2_status', 1)->where('ceo_status', 0)->count();
                $dpnv = NVService::where("user_id", $user->id)->where('dept_id', '!=', 'Null')->count() + NVMaterial::where("user_id", $user->id)->where('dept_id', '!=', 'Null')->count();
                $dpbpinv = NVService::where("user_id", $user->id)->where('dept_id', 1)->count() + NVMaterial::where("user_id", $user->id)->where('dept_id', 1)->count();
                $dpceocellnv = NVService::where("user_id", $user->id)->where('dept_id', 2)->count() + NVMaterial::where("user_id", $user->id)->where('dept_id', 2)->count();
                $dpregnv = NVService::where("user_id", $user->id)->where('dept_id', 3)->count() + NVMaterial::where("user_id", $user->id)->where('dept_id', 3)->count();
                $dpomnv = NVService::where("user_id", $user->id)->where('dept_id', 4)->count() + NVMaterial::where("user_id", $user->id)->where('dept_id', 4)->count();
                $dpinfonv = NVService::where("user_id", $user->id)->where('dept_id', 5)->count() + NVMaterial::where("user_id", $user->id)->where('dept_id', 5)->count();
                $dpsafenv = NVService::where("user_id", $user->id)->where('dept_id', 7)->count() + NVMaterial::where("user_id", $user->id)->where('dept_id', 7)->count();
                $dpdsmnv = NVService::where("user_id", $user->id)->where('dept_id', 10)->count() + NVMaterial::where("user_id", $user->id)->where('dept_id', 10)->count();
                $dpbetnv = NVService::where("user_id", $user->id)->where('dept_id', 8)->count() + NVMaterial::where("user_id", $user->id)->where('dept_id', 8)->count();
                $nvIds = NeedValidation::whereIn("user_id", $Values)->pluck("id");
                $nv = NeedValidation::whereHas('service')->select('id')->whereIn('user_id', $Values)->orWhereIn('user_id', $allNormalUsers)->get();
                $nv_ids = $nv->pluck('id');
                $nv_sm_data = Nvsericestatus::whereIn('nv_id', $nv_ids)->with(['service', 'material', 'user'])
                ->where('rv1_status',1)
                ->orderBy('id', 'desc');
                if ($ceo_status) {
                    $nv_sm_data->where('ceo_status', $ceo_status);
                }
                $nv_sm_data = $nv_sm_data->get();
                // dd($nv_sm_data );
                return view("admin.dashboard", compact("nv_sm_data", "totalNV", "approvedNV", "rejectedNV", "pendingNV", "revertedNV", "hodApproval", "cpmgApproval", "btApproval", "ceoApproval", "dpnv", "dpbpinv", "dpceocellnv", "dpregnv", "dpomnv", "dpinfonv", "dpsafenv", "dpdsmnv", "dpbetnv"));
            } elseif ($id1->work_rew1 == $user->id) {
               
                $Values = [$user->id, $hod, $id1  ];
                
                // $totalNV = NeedValidation::where("user_id", $user->id)->count();
                $totalId = NeedValidation::pluck('id');
                // dd($totalId);
                $totalNV = Nvsericestatus::whereIn("nv_id", $totalId)->where('work_rew1_status', 1)->count();
                $rejectedNV = Nvsericestatus::whereIn("nv_id", $totalId)->where('work_rew1_status', 2)->count();
                // dd($totalNV);
                $latestData = Nvsericestatus::whereIn("nv_id", $totalId)->get();
                $approvedNV = $latestData->where('ceo_status', 1)->count();
                // $rejectedNV = $latestData->filter(function ($data) {
                //     return in_array($data->ceo_status, [2]) || 
                //     $data->work_rew1_status == 2 ;
                //     $data->hod_status == 2 || 
                //     $data->cpmg_status == 2 || 
                //     $data->ces_status == 2 || 
                //     $data->cto_status == 2 || 
                //     $data->ceo_nominee_status == 2 || 
                //     $data->ceo_nominee2_status == 2 || 
                //     $data->ceo_nominee2_status == 2;
                // })->count();
                $pendingNV = $latestData->filter(function ($data) {
                return in_array($data->hod_status, [ 1]) &&
                in_array($data->work_rew1_status, [0]);
                
                // in_array($data->ces_status, [0]) &&
                // in_array($data->cto_status, [0]) &&
                // in_array($data->ceo_nominee_status, [0]) &&
                // in_array($data->ceo_status, [0]);
                })->count();
                // $pendingNV = Nvsericestatus::whereIn("nv_id", $totalId)->where('work_rew1_status', 0)->count();
                // $pendingNV=$pendingNV-$approvedNV-$rejectedNV;
                // $totalNV = Nvsericestatus::whereIn("nv_id", $totalId)->where('rv1_status', 1)->count();
                $revertedNV = $rejectedNV;
                $hodApproval = Nvsericestatus::where("hod_status", 0)->count();
                $cpmgApproval = Nvsericestatus::where('cto_status', 1)->where('cpmg_status', 0)->count();
                $btApproval = Nvsericestatus::where("hod_status", 1)->where('cto_status', 0)->count();
                // $ceonominee1Approval =  Nvsericestatus::where('cpmg_status', 1)->where('ceo_nominee_status', 0)->count();
                // $ceonominee2Approval=Nvsericestatus::where('ceo_nominee_status', 1)->where('ceo_nominee2_status', 0)->count();
                // $ceonominee2A2pproval=Nvsericestatus::where('ceo_nominee2_status', 1)->where('ceo_nominee2_status', 0)->count();
                $ceoApproval = Nvsericestatus::where('ceo_nominee2_status', 1)->where('ceo_status', 0)->count();
                $dpnv = NVService::where("user_id", $user->id)->where('dept_id', '!=', 'Null')->count() + NVMaterial::where("user_id", $user->id)->where('dept_id', '!=', 'Null')->count();
                $dpbpinv = NVService::where("user_id", $user->id)->where('dept_id', 1)->count() + NVMaterial::where("user_id", $user->id)->where('dept_id', 1)->count();
                $dpceocellnv = NVService::where("user_id", $user->id)->where('dept_id', 2)->count() + NVMaterial::where("user_id", $user->id)->where('dept_id', 2)->count();
                $dpregnv = NVService::where("user_id", $user->id)->where('dept_id', 3)->count() + NVMaterial::where("user_id", $user->id)->where('dept_id', 3)->count();
                $dpomnv = NVService::where("user_id", $user->id)->where('dept_id', 4)->count() + NVMaterial::where("user_id", $user->id)->where('dept_id', 4)->count();
                $dpinfonv = NVService::where("user_id", $user->id)->where('dept_id', 5)->count() + NVMaterial::where("user_id", $user->id)->where('dept_id', 5)->count();
                $dpsafenv = NVService::where("user_id", $user->id)->where('dept_id', 7)->count() + NVMaterial::where("user_id", $user->id)->where('dept_id', 7)->count();
                $dpdsmnv = NVService::where("user_id", $user->id)->where('dept_id', 10)->count() + NVMaterial::where("user_id", $user->id)->where('dept_id', 10)->count();
                $dpbetnv = NVService::where("user_id", $user->id)->where('dept_id', 8)->count() + NVMaterial::where("user_id", $user->id)->where('dept_id', 8)->count();
                $nvIds = NeedValidation::where("user_id", $user->id)->pluck("id");
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
                $nv_sm_data = Nvsericestatus::whereIn('nv_id', $nv_ids)->with(['service', 'material', 'user'])
                ->where('hod_status',1)
                ->orderBy('id', 'desc');
                if ($ceo_status) {
                    $nv_sm_data->where('ceo_status', $ceo_status);
                }
                $nv_sm_data = $nv_sm_data->get();
                return view("admin.dashboard", compact("nv_sm_data", "totalNV", "approvedNV", "rejectedNV", "pendingNV", "revertedNV", "hodApproval", "cpmgApproval", "btApproval", "ceoApproval", "dpnv", "dpbpinv", "dpceocellnv", "dpregnv", "dpomnv", "dpinfonv", "dpsafenv", "dpdsmnv", "dpbetnv"));
            } elseif ($id1->work_rew3 == $user->id) {
                $Values = [$user->id, $id1->work_rew1, $id1->work_rew2];
                $totalNV = NeedValidation::where("user_id", $user->id)->count();
                $totalId = NeedValidation::where("user_id", $user->id)->pluck('id');
                // dd($totalId);
                $latestData = Nvsericestatus::whereIn("nv_id", $totalId)->get();
                $approvedNV = $latestData->where('ceo_status', 1)->count();
                $rejectedNV = $latestData->filter(function ($data) {
                    return in_array($data->ceo_status, [2]) || $data->hod_status == 2 || $data->cpmg_status == 2 || $data->ces_status == 2 || $data->cto_status == 2 || $data->ceo_nominee_status == 2 || $data->ceo_nominee2_status == 2 ;
                })->count();
                // $pendingNV = $latestData->filter(function ($data) {
                // return in_array($data->hod_status, [0, 1]) &&
                // in_array($data->cpmg_status, [0, 1]) &&
                // in_array($data->ces_status, [0, 1]) &&
                // in_array($data->cto_status, [0, 1]) &&
                // in_array($data->ceo_nominee_status, [0, 1]) &&
                // in_array($data->ceo_status, [0, 1]);
                // })->count();
                $pendingNV = Nvsericestatus::whereIn("nv_id", $totalId)->where('rv1_status', 0)->count();
                // $pendingNV=$pendingNV-$approvedNV-$rejectedNV;
                $totalNV = Nvsericestatus::whereIn("nv_id", $totalId)->where('rv1_status', 1)->count();
                $revertedNV = $rejectedNV;
                $hodApproval = Nvsericestatus::where("hod_status", 0)->count();
                $cpmgApproval = Nvsericestatus::where('cto_status', 1)->where('cpmg_status', 0)->count();
                $btApproval = Nvsericestatus::where("hod_status", 1)->where('cto_status', 0)->count();
                // $ceonominee1Approval =  Nvsericestatus::where('cpmg_status', 1)->where('ceo_nominee_status', 0)->count();
                // $ceonominee2Approval=Nvsericestatus::where('ceo_nominee_status', 1)->where('ceo_nominee2_status', 0)->count();
                // $ceonominee2A2pproval=Nvsericestatus::where('ceo_nominee2_status', 1)->where('ceo_nominee2_status', 0)->count();
                $ceoApproval = Nvsericestatus::where('ceo_nominee2_status', 1)->where('ceo_status', 0)->count();
                $dpnv = NVService::where("user_id", $user->id)->where('dept_id', '!=', 'Null')->count() + NVMaterial::where("user_id", $user->id)->where('dept_id', '!=', 'Null')->count();
                $dpbpinv = NVService::where("user_id", $user->id)->where('dept_id', 1)->count() + NVMaterial::where("user_id", $user->id)->where('dept_id', 1)->count();
                $dpceocellnv = NVService::where("user_id", $user->id)->where('dept_id', 2)->count() + NVMaterial::where("user_id", $user->id)->where('dept_id', 2)->count();
                $dpregnv = NVService::where("user_id", $user->id)->where('dept_id', 3)->count() + NVMaterial::where("user_id", $user->id)->where('dept_id', 3)->count();
                $dpomnv = NVService::where("user_id", $user->id)->where('dept_id', 4)->count() + NVMaterial::where("user_id", $user->id)->where('dept_id', 4)->count();
                $dpinfonv = NVService::where("user_id", $user->id)->where('dept_id', 5)->count() + NVMaterial::where("user_id", $user->id)->where('dept_id', 5)->count();
                $dpsafenv = NVService::where("user_id", $user->id)->where('dept_id', 7)->count() + NVMaterial::where("user_id", $user->id)->where('dept_id', 7)->count();
                $dpdsmnv = NVService::where("user_id", $user->id)->where('dept_id', 10)->count() + NVMaterial::where("user_id", $user->id)->where('dept_id', 10)->count();
                $dpbetnv = NVService::where("user_id", $user->id)->where('dept_id', 8)->count() + NVMaterial::where("user_id", $user->id)->where('dept_id', 8)->count();
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
                $nv_sm_data = Nvsericestatus::whereIn('nv_id', $nv_ids)->with(['service', 'material', 'user'])
                ->where('hod_status',1)
                ->orderBy('id', 'desc');
                if ($ceo_status) {
                    $nv_sm_data->where('ceo_status', $ceo_status);
                }
                $nv_sm_data = $nv_sm_data->get();
                return view("admin.dashboard", compact("nv_sm_data", "totalNV", "approvedNV", "rejectedNV", "pendingNV", "revertedNV", "hodApproval", "cpmgApproval", "btApproval", "ceoApproval", "dpnv", "dpbpinv", "dpceocellnv", "dpregnv", "dpomnv", "dpinfonv", "dpsafenv", "dpdsmnv", "dpbetnv"));
            } elseif ($id1->work_rew4 == $user->id) {
                $Values = [$user->id, $id1->work_rew1, $id1->work_rew2, $id1->work_rew3];
                $totalNV = NeedValidation::where("user_id", $user->id)->count();
                $totalId = NeedValidation::where("user_id", $user->id)->pluck('id');
                // dd($totalId);
                $latestData = Nvsericestatus::whereIn("nv_id", $totalId)->get();
                $approvedNV = $latestData->where('ceo_status', 1)->count();
                $rejectedNV = $latestData->filter(function ($data) {
                    return in_array($data->ceo_status, [2]) || $data->hod_status == 2 || $data->cpmg_status == 2 || $data->ces_status == 2 || $data->cto_status == 2 || $data->ceo_nominee_status == 2 || $data->ceo_nominee2_status == 2;
                })->count();
                // $pendingNV = $latestData->filter(function ($data) {
                // return in_array($data->hod_status, [0, 1]) &&
                // in_array($data->cpmg_status, [0, 1]) &&
                // in_array($data->ces_status, [0, 1]) &&
                // in_array($data->cto_status, [0, 1]) &&
                // in_array($data->ceo_nominee_status, [0, 1]) &&
                // in_array($data->ceo_status, [0, 1]);
                // })->count();
                $pendingNV = Nvsericestatus::whereIn("nv_id", $totalId)->where('rv1_status', 0)->count();
                // $pendingNV=$pendingNV-$approvedNV-$rejectedNV;
                $totalNV = Nvsericestatus::whereIn("nv_id", $totalId)->where('rv1_status', 1)->count();
                $revertedNV = $rejectedNV;
                $hodApproval = Nvsericestatus::where("hod_status", 0)->count();
                $cpmgApproval = Nvsericestatus::where('cto_status', 1)->where('cpmg_status', 0)->count();
                $btApproval = Nvsericestatus::where("hod_status", 1)->where('cto_status', 0)->count();
                // $ceonominee1Approval =  Nvsericestatus::where('cpmg_status', 1)->where('ceo_nominee_status', 0)->count();
                // $ceonominee2Approval=Nvsericestatus::where('ceo_nominee_status', 1)->where('ceo_nominee2_status', 0)->count();
                // $ceonominee2A2pproval=Nvsericestatus::where('ceo_nominee2_status', 1)->where('ceo_nominee2_status', 0)->count();
                $ceoApproval = Nvsericestatus::where('ceo_nominee2_status', 1)->where('ceo_status', 0)->count();
                $dpnv = NVService::where("user_id", $user->id)->where('dept_id', '!=', 'Null')->count() + NVMaterial::where("user_id", $user->id)->where('dept_id', '!=', 'Null')->count();
                $dpbpinv = NVService::where("user_id", $user->id)->where('dept_id', 1)->count() + NVMaterial::where("user_id", $user->id)->where('dept_id', 1)->count();
                $dpceocellnv = NVService::where("user_id", $user->id)->where('dept_id', 2)->count() + NVMaterial::where("user_id", $user->id)->where('dept_id', 2)->count();
                $dpregnv = NVService::where("user_id", $user->id)->where('dept_id', 3)->count() + NVMaterial::where("user_id", $user->id)->where('dept_id', 3)->count();
                $dpomnv = NVService::where("user_id", $user->id)->where('dept_id', 4)->count() + NVMaterial::where("user_id", $user->id)->where('dept_id', 4)->count();
                $dpinfonv = NVService::where("user_id", $user->id)->where('dept_id', 5)->count() + NVMaterial::where("user_id", $user->id)->where('dept_id', 5)->count();
                $dpsafenv = NVService::where("user_id", $user->id)->where('dept_id', 7)->count() + NVMaterial::where("user_id", $user->id)->where('dept_id', 7)->count();
                $dpdsmnv = NVService::where("user_id", $user->id)->where('dept_id', 10)->count() + NVMaterial::where("user_id", $user->id)->where('dept_id', 10)->count();
                $dpbetnv = NVService::where("user_id", $user->id)->where('dept_id', 8)->count() + NVMaterial::where("user_id", $user->id)->where('dept_id', 8)->count();
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
                $nv_sm_data = Nvsericestatus::whereIn('nv_id', $nv_ids)->with(['service', 'material', 'user'])
                ->where('hod_status',1)
                ->orderBy('id', 'desc');
                if ($ceo_status) {
                    $nv_sm_data->where('ceo_status', $ceo_status);
                }
                $nv_sm_data = $nv_sm_data->get();
                return view("admin.dashboard", compact("nv_sm_data", "totalNV", "approvedNV", "rejectedNV", "pendingNV", "revertedNV", "hodApproval", "cpmgApproval", "btApproval", "ceoApproval", "dpnv", "dpbpinv", "dpceocellnv", "dpregnv", "dpomnv", "dpinfonv", "dpsafenv", "dpdsmnv", "dpbetnv"));
            } elseif ($id1->approver == $user->id) {
                $Values = [$user->id, $id1->work_rew1, $id1->work_rew2, $id1->work_rew3, $id1->work_rew4];
                $totalNV = NeedValidation::where("user_id", $user->id)->count();
                $totalId = NeedValidation::where("user_id", $user->id)->pluck('id');
                // dd($totalId);
                $latestData = Nvsericestatus::whereIn("nv_id", $totalId)->get();
                $approvedNV = $latestData->where('ceo_status', 1)->count();
                $rejectedNV = $latestData->filter(function ($data) {
                    return in_array($data->ceo_status, [2]) || $data->hod_status == 2 || $data->cpmg_status == 2 ||
                    // $data->ces_status == 2 ||
                    $data->cto_status == 2 || $data->ceo_nominee_status == 2 || $data->ceo_nominee2_status == 2;
                    // $data->ceo_nominee2_status == 2 ;
                    
                })->count();
                // $pendingNV = $latestData->filter(function ($data) {
                // return in_array($data->hod_status, [0, 1]) &&
                // in_array($data->cpmg_status, [0, 1]) &&
                // in_array($data->ces_status, [0, 1]) &&
                // in_array($data->cto_status, [0, 1]) &&
                // in_array($data->ceo_nominee_status, [0, 1]) &&
                // in_array($data->ceo_status, [0, 1]);
                // })->count();
                $pendingNV = Nvsericestatus::whereIn("nv_id", $totalId)->where('rv1_status', 0)->count();
                // $pendingNV=$pendingNV-$approvedNV-$rejectedNV;
                $totalNV = Nvsericestatus::whereIn("nv_id", $totalId)->where('rv1_status', 1)->count();
                $revertedNV = $rejectedNV;
                $hodApproval = Nvsericestatus::where("hod_status", 0)->count();
                $cpmgApproval = Nvsericestatus::where('cto_status', 1)->where('cpmg_status', 0)->count();
                $btApproval = Nvsericestatus::where("hod_status", 1)->where('cto_status', 0)->count();
                // $ceonominee1Approval =  Nvsericestatus::where('cpmg_status', 1)->where('ceo_nominee_status', 0)->count();
                // $ceonominee2Approval=Nvsericestatus::where('ceo_nominee_status', 1)->where('ceo_nominee2_status', 0)->count();
                // $ceonominee2A2pproval=Nvsericestatus::where('ceo_nominee2_status', 1)->where('ceo_nominee2_status', 0)->count();
                $ceoApproval = '';
                // Nvsericestatus::where('ceo_nominee2_status', 1)->where('ceo_status', 0)->count();
                $dpnv = NVService::where("user_id", $user->id)->where('dept_id', '!=', 'Null')->count() + NVMaterial::where("user_id", $user->id)->where('dept_id', '!=', 'Null')->count();
                $dpbpinv = NVService::where("user_id", $user->id)->where('dept_id', 1)->count() + NVMaterial::where("user_id", $user->id)->where('dept_id', 1)->count();
                $dpceocellnv = NVService::where("user_id", $user->id)->where('dept_id', 2)->count() + NVMaterial::where("user_id", $user->id)->where('dept_id', 2)->count();
                $dpregnv = NVService::where("user_id", $user->id)->where('dept_id', 3)->count() + NVMaterial::where("user_id", $user->id)->where('dept_id', 3)->count();
                $dpomnv = NVService::where("user_id", $user->id)->where('dept_id', 4)->count() + NVMaterial::where("user_id", $user->id)->where('dept_id', 4)->count();
                $dpinfonv = NVService::where("user_id", $user->id)->where('dept_id', 5)->count() + NVMaterial::where("user_id", $user->id)->where('dept_id', 5)->count();
                $dpsafenv = NVService::where("user_id", $user->id)->where('dept_id', 7)->count() + NVMaterial::where("user_id", $user->id)->where('dept_id', 7)->count();
                $dpdsmnv = NVService::where("user_id", $user->id)->where('dept_id', 10)->count() + NVMaterial::where("user_id", $user->id)->where('dept_id', 10)->count();
                $dpbetnv = NVService::where("user_id", $user->id)->where('dept_id', 8)->count() + NVMaterial::where("user_id", $user->id)->where('dept_id', 8)->count();
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
                $nv_sm_data = Nvsericestatus::whereIn('nv_id', $nv_ids)->with(['service', 'material', 'user'])
                ->where('hod_status',1)
                ->orderBy('id', 'desc');
                if ($ceo_status) {
                    $nv_sm_data->where('ceo_status', $ceo_status);
                }
                $nv_sm_data = $nv_sm_data->get();
                return view("admin.dashboard", compact("nv_sm_data", "totalNV", "approvedNV", "rejectedNV", "pendingNV", "revertedNV", "hodApproval", "cpmgApproval", "btApproval", "ceoApproval", "dpnv", "dpbpinv", "dpceocellnv", "dpregnv", "dpomnv", "dpinfonv", "dpsafenv", "dpdsmnv", "dpbetnv"));
            } elseif ($id2->work_rew1 == $user->id) {
                $totalNV = NeedValidation::where("user_id", $user->id)->count();
                $totalId = NeedValidation::where("user_id", $user->id)->pluck('id');
                // dd($totalId);
                $latestData = Nvsericestatus::whereIn("nv_id", $totalId)->get();
                $approvedNV = $latestData->where('ceo_status', 1)->count();
                $rejectedNV = $latestData->filter(function ($data) {
                    return in_array($data->ceo_status, [2]) || $data->hod_status == 2 || $data->cpmg_status == 2 || $data->ces_status == 2 || $data->cto_status == 2 || $data->ceo_nominee_status == 2 || $data->ceo_nominee2_status == 2 ;
                })->count();
                // $pendingNV = $latestData->filter(function ($data) {
                // return in_array($data->hod_status, [0, 1]) &&
                // in_array($data->cpmg_status, [0, 1]) &&
                // in_array($data->ces_status, [0, 1]) &&
                // in_array($data->cto_status, [0, 1]) &&
                // in_array($data->ceo_nominee_status, [0, 1]) &&
                // in_array($data->ceo_status, [0, 1]);
                // })->count();
                $pendingNV = Nvsericestatus::whereIn("nv_id", $totalId)->where('rv1_status', 0)->count();
                // $pendingNV=$pendingNV-$approvedNV-$rejectedNV;
                $totalNV = Nvsericestatus::whereIn("nv_id", $totalId)->where('rv1_status', 1)->count();
                $revertedNV = $rejectedNV;
                $hodApproval = Nvsericestatus::where("hod_status", 0)->count();
                $cpmgApproval = Nvsericestatus::where('cto_status', 1)->where('cpmg_status', 0)->count();
                $btApproval = Nvsericestatus::where("hod_status", 1)->where('cto_status', 0)->count();
                // $ceonominee1Approval =  Nvsericestatus::where('cpmg_status', 1)->where('ceo_nominee_status', 0)->count();
                // $ceonominee2Approval=Nvsericestatus::where('ceo_nominee_status', 1)->where('ceo_nominee2_status', 0)->count();
                // $ceonominee2A2pproval=Nvsericestatus::where('ceo_nominee2_status', 1)->where('ceo_nominee2_status', 0)->count();
                $ceoApproval = Nvsericestatus::where('ceo_nominee2_status', 1)->where('ceo_status', 0)->count();
                $dpnv = NVService::where("user_id", $user->id)->where('dept_id', '!=', 'Null')->count() + NVMaterial::where("user_id", $user->id)->where('dept_id', '!=', 'Null')->count();
                $dpbpinv = NVService::where("user_id", $user->id)->where('dept_id', 1)->count() + NVMaterial::where("user_id", $user->id)->where('dept_id', 1)->count();
                $dpceocellnv = NVService::where("user_id", $user->id)->where('dept_id', 2)->count() + NVMaterial::where("user_id", $user->id)->where('dept_id', 2)->count();
                $dpregnv = NVService::where("user_id", $user->id)->where('dept_id', 3)->count() + NVMaterial::where("user_id", $user->id)->where('dept_id', 3)->count();
                $dpomnv = NVService::where("user_id", $user->id)->where('dept_id', 4)->count() + NVMaterial::where("user_id", $user->id)->where('dept_id', 4)->count();
                $dpinfonv = NVService::where("user_id", $user->id)->where('dept_id', 5)->count() + NVMaterial::where("user_id", $user->id)->where('dept_id', 5)->count();
                $dpsafenv = NVService::where("user_id", $user->id)->where('dept_id', 7)->count() + NVMaterial::where("user_id", $user->id)->where('dept_id', 7)->count();
                $dpdsmnv = NVService::where("user_id", $user->id)->where('dept_id', 10)->count() + NVMaterial::where("user_id", $user->id)->where('dept_id', 10)->count();
                $dpbetnv = NVService::where("user_id", $user->id)->where('dept_id', 8)->count() + NVMaterial::where("user_id", $user->id)->where('dept_id', 8)->count();
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
                $nv_sm_data = Nvsericestatus::whereIn('nv_id', $nv_ids)->with(['service', 'material', 'user'])
                ->where('check_technology',1)
                ->where('cpmg_status',1)
                ->orderBy('id', 'desc');
                if ($ceo_status) {
                    $nv_sm_data->where('ceo_status', $ceo_status);
                }
                $nv_sm_data = $nv_sm_data->get();
                return view("admin.dashboard", compact("nv_sm_data", "totalNV", "approvedNV", "rejectedNV", "pendingNV", "revertedNV", "hodApproval", "cpmgApproval", "btApproval", "ceoApproval", "dpnv", "dpbpinv", "dpceocellnv", "dpregnv", "dpomnv", "dpinfonv", "dpsafenv", "dpdsmnv", "dpbetnv"));
            } elseif ($id2->work_rew2 == $user->id) {
                $Values = [$user->id, $id2->work_rew1];
                $totalNV = NeedValidation::where("user_id", $user->id)->count();
                $totalId = NeedValidation::where("user_id", $user->id)->pluck('id');
                // dd($totalId);
                $latestData = Nvsericestatus::whereIn("nv_id", $totalId)->get();
                $approvedNV = $latestData->where('ceo_status', 1)->count();
                $rejectedNV = $latestData->filter(function ($data) {
                    return in_array($data->ceo_status, [2]) || $data->hod_status == 2 || $data->cpmg_status == 2 || $data->ces_status == 2 || $data->cto_status == 2 || $data->ceo_nominee_status == 2 || $data->ceo_nominee2_status == 2 ;
                })->count();
                // $pendingNV = $latestData->filter(function ($data) {
                // return in_array($data->hod_status, [0, 1]) &&
                // in_array($data->cpmg_status, [0, 1]) &&
                // in_array($data->ces_status, [0, 1]) &&
                // in_array($data->cto_status, [0, 1]) &&
                // in_array($data->ceo_nominee_status, [0, 1]) &&
                // in_array($data->ceo_status, [0, 1]);
                // })->count();
                $pendingNV = Nvsericestatus::whereIn("nv_id", $totalId)->where('rv1_status', 0)->count();
                // $pendingNV=$pendingNV-$approvedNV-$rejectedNV;
                $totalNV = Nvsericestatus::whereIn("nv_id", $totalId)->where('rv1_status', 1)->count();
                $revertedNV = $rejectedNV;
                $hodApproval = Nvsericestatus::where("hod_status", 0)->count();
                $cpmgApproval = Nvsericestatus::where('cto_status', 1)->where('cpmg_status', 0)->count();
                $btApproval = Nvsericestatus::where("hod_status", 1)->where('cto_status', 0)->count();
                // $ceonominee1Approval =  Nvsericestatus::where('cpmg_status', 1)->where('ceo_nominee_status', 0)->count();
                // $ceonominee2Approval=Nvsericestatus::where('ceo_nominee_status', 1)->where('ceo_nominee2_status', 0)->count();
                // $ceonominee2A2pproval=Nvsericestatus::where('ceo_nominee2_status', 1)->where('ceo_nominee2_status', 0)->count();
                $ceoApproval = Nvsericestatus::where('ceo_nominee2_status', 1)->where('ceo_status', 0)->count();
                $dpnv = NVService::where("user_id", $user->id)->where('dept_id', '!=', 'Null')->count() + NVMaterial::where("user_id", $user->id)->where('dept_id', '!=', 'Null')->count();
                $dpbpinv = NVService::where("user_id", $user->id)->where('dept_id', 1)->count() + NVMaterial::where("user_id", $user->id)->where('dept_id', 1)->count();
                $dpceocellnv = NVService::where("user_id", $user->id)->where('dept_id', 2)->count() + NVMaterial::where("user_id", $user->id)->where('dept_id', 2)->count();
                $dpregnv = NVService::where("user_id", $user->id)->where('dept_id', 3)->count() + NVMaterial::where("user_id", $user->id)->where('dept_id', 3)->count();
                $dpomnv = NVService::where("user_id", $user->id)->where('dept_id', 4)->count() + NVMaterial::where("user_id", $user->id)->where('dept_id', 4)->count();
                $dpinfonv = NVService::where("user_id", $user->id)->where('dept_id', 5)->count() + NVMaterial::where("user_id", $user->id)->where('dept_id', 5)->count();
                $dpsafenv = NVService::where("user_id", $user->id)->where('dept_id', 7)->count() + NVMaterial::where("user_id", $user->id)->where('dept_id', 7)->count();
                $dpdsmnv = NVService::where("user_id", $user->id)->where('dept_id', 10)->count() + NVMaterial::where("user_id", $user->id)->where('dept_id', 10)->count();
                $dpbetnv = NVService::where("user_id", $user->id)->where('dept_id', 8)->count() + NVMaterial::where("user_id", $user->id)->where('dept_id', 8)->count();
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
                $nv_sm_data = Nvsericestatus::whereIn('nv_id', $nv_ids)->with(['service', 'material', 'user'])
                ->where('cpmg_status',1)
                ->orderBy('id', 'desc');
                if ($ceo_status) {
                    $nv_sm_data->where('ceo_status', $ceo_status);
                }
                $nv_sm_data = $nv_sm_data->get();
                return view("admin.dashboard", compact("nv_sm_data", "totalNV", "approvedNV", "rejectedNV", "pendingNV", "revertedNV", "hodApproval", "cpmgApproval", "btApproval", "ceoApproval", "dpnv", "dpbpinv", "dpceocellnv", "dpregnv", "dpomnv", "dpinfonv", "dpsafenv", "dpdsmnv", "dpbetnv"));
            } elseif ($id2->work_rew3 == $user->id) {
                $Values = [$user->id, $id2->work_rew1, $id2->work_rew2];
                $totalNV = NeedValidation::where("user_id", $user->id)->count();
                $totalId = NeedValidation::where("user_id", $user->id)->pluck('id');
                // dd($totalId);
                $latestData = Nvsericestatus::whereIn("nv_id", $totalId)->get();
                $approvedNV = $latestData->where('ceo_status', 1)->count();
                $rejectedNV = $latestData->filter(function ($data) {
                    return in_array($data->ceo_status, [2]) || $data->hod_status == 2 || $data->cpmg_status == 2 || $data->ces_status == 2 || $data->cto_status == 2 || $data->ceo_nominee_status == 2 || $data->ceo_nominee2_status == 2 ;
                })->count();
                // $pendingNV = $latestData->filter(function ($data) {
                // return in_array($data->hod_status, [0, 1]) &&
                // in_array($data->cpmg_status, [0, 1]) &&
                // in_array($data->ces_status, [0, 1]) &&
                // in_array($data->cto_status, [0, 1]) &&
                // in_array($data->ceo_nominee_status, [0, 1]) &&
                // in_array($data->ceo_status, [0, 1]);
                // })->count();
                $pendingNV = Nvsericestatus::whereIn("nv_id", $totalId)->where('rv1_status', 0)->count();
                // $pendingNV=$pendingNV-$approvedNV-$rejectedNV;
                $totalNV = Nvsericestatus::whereIn("nv_id", $totalId)->where('rv1_status', 1)->count();
                $revertedNV = $rejectedNV;
                $hodApproval = Nvsericestatus::where("hod_status", 0)->count();
                $cpmgApproval = Nvsericestatus::where('cto_status', 1)->where('cpmg_status', 0)->count();
                $btApproval = Nvsericestatus::where("hod_status", 1)->where('cto_status', 0)->count();
                // $ceonominee1Approval =  Nvsericestatus::where('cpmg_status', 1)->where('ceo_nominee_status', 0)->count();
                // $ceonominee2Approval=Nvsericestatus::where('ceo_nominee_status', 1)->where('ceo_nominee2_status', 0)->count();
                // $ceonominee2A2pproval=Nvsericestatus::where('ceo_nominee2_status', 1)->where('ceo_nominee2_status', 0)->count();
                $ceoApproval = Nvsericestatus::where('ceo_nominee2_status', 1)->where('ceo_status', 0)->count();
                $dpnv = NVService::where("user_id", $user->id)->where('dept_id', '!=', 'Null')->count() + NVMaterial::where("user_id", $user->id)->where('dept_id', '!=', 'Null')->count();
                $dpbpinv = NVService::where("user_id", $user->id)->where('dept_id', 1)->count() + NVMaterial::where("user_id", $user->id)->where('dept_id', 1)->count();
                $dpceocellnv = NVService::where("user_id", $user->id)->where('dept_id', 2)->count() + NVMaterial::where("user_id", $user->id)->where('dept_id', 2)->count();
                $dpregnv = NVService::where("user_id", $user->id)->where('dept_id', 3)->count() + NVMaterial::where("user_id", $user->id)->where('dept_id', 3)->count();
                $dpomnv = NVService::where("user_id", $user->id)->where('dept_id', 4)->count() + NVMaterial::where("user_id", $user->id)->where('dept_id', 4)->count();
                $dpinfonv = NVService::where("user_id", $user->id)->where('dept_id', 5)->count() + NVMaterial::where("user_id", $user->id)->where('dept_id', 5)->count();
                $dpsafenv = NVService::where("user_id", $user->id)->where('dept_id', 7)->count() + NVMaterial::where("user_id", $user->id)->where('dept_id', 7)->count();
                $dpdsmnv = NVService::where("user_id", $user->id)->where('dept_id', 10)->count() + NVMaterial::where("user_id", $user->id)->where('dept_id', 10)->count();
                $dpbetnv = NVService::where("user_id", $user->id)->where('dept_id', 8)->count() + NVMaterial::where("user_id", $user->id)->where('dept_id', 8)->count();
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
                $nv_sm_data = Nvsericestatus::whereIn('nv_id', $nv_ids)->with(['service', 'material', 'user'])
                ->where('cpmg_status',1)
                ->orderBy('id', 'desc');
                if ($ceo_status) {
                    $nv_sm_data->where('ceo_status', $ceo_status);
                }
                $nv_sm_data = $nv_sm_data->get();
                return view("admin.dashboard", compact("nv_sm_data", "totalNV", "approvedNV", "rejectedNV", "pendingNV", "revertedNV", "hodApproval", "cpmgApproval", "btApproval", "ceoApproval", "dpnv", "dpbpinv", "dpceocellnv", "dpregnv", "dpomnv", "dpinfonv", "dpsafenv", "dpdsmnv", "dpbetnv"));
            } elseif ($id2->work_rew4 == $user->id) {
                $Values = [$user->id, $id2->work_rew1, $id2->work_rew2, $id2->work_rew3];
                $totalNV = NeedValidation::where("user_id", $user->id)->count();
                $totalId = NeedValidation::where("user_id", $user->id)->pluck('id');
                // dd($totalId);
                $latestData = Nvsericestatus::whereIn("nv_id", $totalId)->get();
                $approvedNV = $latestData->where('ceo_status', 1)->count();
                $rejectedNV = $latestData->filter(function ($data) {
                    return in_array($data->ceo_status, [2]) || $data->hod_status == 2 || $data->cpmg_status == 2 || $data->ces_status == 2 || $data->cto_status == 2 || $data->ceo_nominee_status == 2 || $data->ceo_nominee2_status == 2;
                })->count();
                // $pendingNV = $latestData->filter(function ($data) {
                // return in_array($data->hod_status, [0, 1]) &&
                // in_array($data->cpmg_status, [0, 1]) &&
                // in_array($data->ces_status, [0, 1]) &&
                // in_array($data->cto_status, [0, 1]) &&
                // in_array($data->ceo_nominee_status, [0, 1]) &&
                // in_array($data->ceo_status, [0, 1]);
                // })->count();
                $pendingNV = Nvsericestatus::whereIn("nv_id", $totalId)->where('rv1_status', 0)->count();
                // $pendingNV=$pendingNV-$approvedNV-$rejectedNV;
                $totalNV = Nvsericestatus::whereIn("nv_id", $totalId)->where('rv1_status', 1)->count();
                $revertedNV = $rejectedNV;
                $hodApproval = Nvsericestatus::where("hod_status", 0)->count();
                $cpmgApproval = Nvsericestatus::where('cto_status', 1)->where('cpmg_status', 0)->count();
                $btApproval = Nvsericestatus::where("hod_status", 1)->where('cto_status', 0)->count();
                // $ceonominee1Approval =  Nvsericestatus::where('cpmg_status', 1)->where('ceo_nominee_status', 0)->count();
                // $ceonominee2Approval=Nvsericestatus::where('ceo_nominee_status', 1)->where('ceo_nominee2_status', 0)->count();
                // $ceonominee2A2pproval=Nvsericestatus::where('ceo_nominee2_status', 1)->where('ceo_nominee2_status', 0)->count();
                $ceoApproval = Nvsericestatus::where('ceo_nominee2_status', 1)->where('ceo_status', 0)->count();
                $dpnv = NVService::where("user_id", $user->id)->where('dept_id', '!=', 'Null')->count() + NVMaterial::where("user_id", $user->id)->where('dept_id', '!=', 'Null')->count();
                $dpbpinv = NVService::where("user_id", $user->id)->where('dept_id', 1)->count() + NVMaterial::where("user_id", $user->id)->where('dept_id', 1)->count();
                $dpceocellnv = NVService::where("user_id", $user->id)->where('dept_id', 2)->count() + NVMaterial::where("user_id", $user->id)->where('dept_id', 2)->count();
                $dpregnv = NVService::where("user_id", $user->id)->where('dept_id', 3)->count() + NVMaterial::where("user_id", $user->id)->where('dept_id', 3)->count();
                $dpomnv = NVService::where("user_id", $user->id)->where('dept_id', 4)->count() + NVMaterial::where("user_id", $user->id)->where('dept_id', 4)->count();
                $dpinfonv = NVService::where("user_id", $user->id)->where('dept_id', 5)->count() + NVMaterial::where("user_id", $user->id)->where('dept_id', 5)->count();
                $dpsafenv = NVService::where("user_id", $user->id)->where('dept_id', 7)->count() + NVMaterial::where("user_id", $user->id)->where('dept_id', 7)->count();
                $dpdsmnv = NVService::where("user_id", $user->id)->where('dept_id', 10)->count() + NVMaterial::where("user_id", $user->id)->where('dept_id', 10)->count();
                $dpbetnv = NVService::where("user_id", $user->id)->where('dept_id', 8)->count() + NVMaterial::where("user_id", $user->id)->where('dept_id', 8)->count();
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
                $nv_sm_data = Nvsericestatus::whereIn('nv_id', $nv_ids)->with(['service', 'material', 'user'])
                // ->where('hod_status',1)
                ->orderBy('id', 'desc');
                if ($ceo_status) {
                    $nv_sm_data->where('ceo_status', $ceo_status);
                }
                $nv_sm_data = $nv_sm_data->get();
                return view("admin.dashboard", compact("nv_sm_data", "totalNV", "approvedNV", "rejectedNV", "pendingNV", "revertedNV", "hodApproval", "cpmgApproval", "btApproval", "ceoApproval", "dpnv", "dpbpinv", "dpceocellnv", "dpregnv", "dpomnv", "dpinfonv", "dpsafenv", "dpdsmnv", "dpbetnv"));
            } elseif ($id2->approver == $user->id) {
                $Values = [$user->id, $id2->work_rew1, $id2->work_rew2, $id2->work_rew3, $id2->work_rew4];
                $totalNV = NeedValidation::where("user_id", $user->id)->count();
                $totalId = NeedValidation::where("user_id", $user->id)->pluck('id');
                // dd($totalId);
                $latestData = Nvsericestatus::whereIn("nv_id", $totalId)->get();
                $approvedNV = $latestData->where('ceo_status', 1)->count();
                $rejectedNV = $latestData->filter(function ($data) {
                    return in_array($data->ceo_status, [2]) || $data->hod_status == 2 || $data->cpmg_status == 2 ||
                    // $data->ces_status == 2 ||
                    $data->cto_status == 2 || $data->ceo_nominee_status == 2 || $data->ceo_nominee2_status == 2;
                    // $data->ceo_nominee2_status == 2 ;
                    
                })->count();
                // $pendingNV = $latestData->filter(function ($data) {
                // return in_array($data->hod_status, [0, 1]) &&
                // in_array($data->cpmg_status, [0, 1]) &&
                // in_array($data->ces_status, [0, 1]) &&
                // in_array($data->cto_status, [0, 1]) &&
                // in_array($data->ceo_nominee_status, [0, 1]) &&
                // in_array($data->ceo_status, [0, 1]);
                // })->count();
                $pendingNV = Nvsericestatus::whereIn("nv_id", $totalId)->where('rv1_status', 0)->count();
                // $pendingNV=$pendingNV-$approvedNV-$rejectedNV;
                $totalNV = Nvsericestatus::whereIn("nv_id", $totalId)->where('rv1_status', 1)->count();
                $revertedNV = $rejectedNV;
                $hodApproval = Nvsericestatus::where("hod_status", 0)->count();
                $cpmgApproval = Nvsericestatus::where('cto_status', 1)->where('cpmg_status', 0)->count();
                $btApproval = Nvsericestatus::where("hod_status", 1)->where('cto_status', 0)->count();
                // $ceonominee1Approval =  Nvsericestatus::where('cpmg_status', 1)->where('ceo_nominee_status', 0)->count();
                // $ceonominee2Approval=Nvsericestatus::where('ceo_nominee_status', 1)->where('ceo_nominee2_status', 0)->count();
                // $ceonominee2A2pproval=Nvsericestatus::where('ceo_nominee2_status', 1)->where('ceo_nominee2_status', 0)->count();
                $ceoApproval = '';
                // Nvsericestatus::where('ceo_nominee2_status', 1)->where('ceo_status', 0)->count();
                $dpnv = NVService::where("user_id", $user->id)->where('dept_id', '!=', 'Null')->count() + NVMaterial::where("user_id", $user->id)->where('dept_id', '!=', 'Null')->count();
                $dpbpinv = NVService::where("user_id", $user->id)->where('dept_id', 1)->count() + NVMaterial::where("user_id", $user->id)->where('dept_id', 1)->count();
                $dpceocellnv = NVService::where("user_id", $user->id)->where('dept_id', 2)->count() + NVMaterial::where("user_id", $user->id)->where('dept_id', 2)->count();
                $dpregnv = NVService::where("user_id", $user->id)->where('dept_id', 3)->count() + NVMaterial::where("user_id", $user->id)->where('dept_id', 3)->count();
                $dpomnv = NVService::where("user_id", $user->id)->where('dept_id', 4)->count() + NVMaterial::where("user_id", $user->id)->where('dept_id', 4)->count();
                $dpinfonv = NVService::where("user_id", $user->id)->where('dept_id', 5)->count() + NVMaterial::where("user_id", $user->id)->where('dept_id', 5)->count();
                $dpsafenv = NVService::where("user_id", $user->id)->where('dept_id', 7)->count() + NVMaterial::where("user_id", $user->id)->where('dept_id', 7)->count();
                $dpdsmnv = NVService::where("user_id", $user->id)->where('dept_id', 10)->count() + NVMaterial::where("user_id", $user->id)->where('dept_id', 10)->count();
                $dpbetnv = NVService::where("user_id", $user->id)->where('dept_id', 8)->count() + NVMaterial::where("user_id", $user->id)->where('dept_id', 8)->count();
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
                $nv_sm_data = Nvsericestatus::whereIn('nv_id', $nv_ids)->with(['service', 'material', 'user'])
                ->where('cpmg_status',1)
                ->orderBy('id', 'desc');
                if ($ceo_status) {
                    $nv_sm_data->where('ceo_status', $ceo_status);
                }
                $nv_sm_data = $nv_sm_data->get();
                return view("admin.dashboard", compact("nv_sm_data", "totalNV", "approvedNV", "rejectedNV", "pendingNV", "revertedNV", "hodApproval", "cpmgApproval", "btApproval", "ceoApproval", "dpnv", "dpbpinv", "dpceocellnv", "dpregnv", "dpomnv", "dpinfonv", "dpsafenv", "dpdsmnv", "dpbetnv"));
            } elseif ($id3->work_rew1 == $user->id) {
                $totalNV = NeedValidation::where("user_id", $user->id)->count();
                $totalId = NeedValidation::where("user_id", $user->id)->pluck('id');
                // dd($totalId);
                $latestData = Nvsericestatus::whereIn("nv_id", $totalId)->get();
                $approvedNV = $latestData->where('ceo_status', 1)->count();
                $rejectedNV = $latestData->filter(function ($data) {
                    return in_array($data->ceo_status, [2]) || $data->hod_status == 2 || $data->cpmg_status == 2 || $data->ces_status == 2 || $data->cto_status == 2 || $data->ceo_nominee_status == 2 || $data->ceo_nominee2_status == 2 ;
                })->count();
                // $pendingNV = $latestData->filter(function ($data) {
                // return in_array($data->hod_status, [0, 1]) &&
                // in_array($data->cpmg_status, [0, 1]) &&
                // in_array($data->ces_status, [0, 1]) &&
                // in_array($data->cto_status, [0, 1]) &&
                // in_array($data->ceo_nominee_status, [0, 1]) &&
                // in_array($data->ceo_status, [0, 1]);
                // })->count();
                $pendingNV = Nvsericestatus::whereIn("nv_id", $totalId)->where('rv1_status', 0)->count();
                // $pendingNV=$pendingNV-$approvedNV-$rejectedNV;
                $totalNV = Nvsericestatus::whereIn("nv_id", $totalId)->where('rv1_status', 1)->count();
                $revertedNV = $rejectedNV;
                $hodApproval = Nvsericestatus::where("hod_status", 0)->count();
                $cpmgApproval = Nvsericestatus::where('cto_status', 1)->where('cpmg_status', 0)->count();
                $btApproval = Nvsericestatus::where("hod_status", 1)->where('cto_status', 0)->count();
                // $ceonominee1Approval =  Nvsericestatus::where('cpmg_status', 1)->where('ceo_nominee_status', 0)->count();
                // $ceonominee2Approval=Nvsericestatus::where('ceo_nominee_status', 1)->where('ceo_nominee2_status', 0)->count();
                // $ceonominee2A2pproval=Nvsericestatus::where('ceo_nominee2_status', 1)->where('ceo_nominee2_status', 0)->count();
                $ceoApproval = Nvsericestatus::where('ceo_nominee2_status', 1)->where('ceo_status', 0)->count();
                $dpnv = NVService::where("user_id", $user->id)->where('dept_id', '!=', 'Null')->count() + NVMaterial::where("user_id", $user->id)->where('dept_id', '!=', 'Null')->count();
                $dpbpinv = NVService::where("user_id", $user->id)->where('dept_id', 1)->count() + NVMaterial::where("user_id", $user->id)->where('dept_id', 1)->count();
                $dpceocellnv = NVService::where("user_id", $user->id)->where('dept_id', 2)->count() + NVMaterial::where("user_id", $user->id)->where('dept_id', 2)->count();
                $dpregnv = NVService::where("user_id", $user->id)->where('dept_id', 3)->count() + NVMaterial::where("user_id", $user->id)->where('dept_id', 3)->count();
                $dpomnv = NVService::where("user_id", $user->id)->where('dept_id', 4)->count() + NVMaterial::where("user_id", $user->id)->where('dept_id', 4)->count();
                $dpinfonv = NVService::where("user_id", $user->id)->where('dept_id', 5)->count() + NVMaterial::where("user_id", $user->id)->where('dept_id', 5)->count();
                $dpsafenv = NVService::where("user_id", $user->id)->where('dept_id', 7)->count() + NVMaterial::where("user_id", $user->id)->where('dept_id', 7)->count();
                $dpdsmnv = NVService::where("user_id", $user->id)->where('dept_id', 10)->count() + NVMaterial::where("user_id", $user->id)->where('dept_id', 10)->count();
                $dpbetnv = NVService::where("user_id", $user->id)->where('dept_id', 8)->count() + NVMaterial::where("user_id", $user->id)->where('dept_id', 8)->count();
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
                $nv_sm_data = Nvsericestatus::whereIn('nv_id', $nv_ids)->with(['service', 'material', 'user'])
                ->where('check_technology',0)
                ->where('cto_status',1)
                ->orWhere('cpmg_status',1)
             
                ->orderBy('id', 'desc');
                if ($ceo_status) {
                    $nv_sm_data->where('ceo_status', $ceo_status);
                }
                $nv_sm_data = $nv_sm_data->get();
             
                return view("admin.dashboard", compact("nv_sm_data", "totalNV", "approvedNV", "rejectedNV", "pendingNV", "revertedNV", "hodApproval", "cpmgApproval", "btApproval", "ceoApproval", "dpnv", "dpbpinv", "dpceocellnv", "dpregnv", "dpomnv", "dpinfonv", "dpsafenv", "dpdsmnv", "dpbetnv"));
            } elseif ($id3->work_rew2 == $user->id) {
                $Values = [$user->id, $id3->work_rew1];
                $totalNV = NeedValidation::where("user_id", $user->id)->count();
                $totalId = NeedValidation::where("user_id", $user->id)->pluck('id');
                // dd($totalId);
                $latestData = Nvsericestatus::whereIn("nv_id", $totalId)->get();
                $approvedNV = $latestData->where('ceo_status', 1)->count();
                $rejectedNV = $latestData->filter(function ($data) {
                    return in_array($data->ceo_status, [2]) || $data->hod_status == 2 || $data->cpmg_status == 2 || $data->ces_status == 2 || $data->cto_status == 2 || $data->ceo_nominee_status == 2 || $data->ceo_nominee2_status == 2 ;
                })->count();
                // $pendingNV = $latestData->filter(function ($data) {
                // return in_array($data->hod_status, [0, 1]) &&
                // in_array($data->cpmg_status, [0, 1]) &&
                // in_array($data->ces_status, [0, 1]) &&
                // in_array($data->cto_status, [0, 1]) &&
                // in_array($data->ceo_nominee_status, [0, 1]) &&
                // in_array($data->ceo_status, [0, 1]);
                // })->count();
                $pendingNV = Nvsericestatus::whereIn("nv_id", $totalId)->where('rv1_status', 0)->count();
                // $pendingNV=$pendingNV-$approvedNV-$rejectedNV;
                $totalNV = Nvsericestatus::whereIn("nv_id", $totalId)->where('rv1_status', 1)->count();
                $revertedNV = $rejectedNV;
                $hodApproval = Nvsericestatus::where("hod_status", 0)->count();
                $cpmgApproval = Nvsericestatus::where('cto_status', 1)->where('cpmg_status', 0)->count();
                $btApproval = Nvsericestatus::where("hod_status", 1)->where('cto_status', 0)->count();
                // $ceonominee1Approval =  Nvsericestatus::where('cpmg_status', 1)->where('ceo_nominee_status', 0)->count();
                // $ceonominee2Approval=Nvsericestatus::where('ceo_nominee_status', 1)->where('ceo_nominee2_status', 0)->count();
                // $ceonominee2A2pproval=Nvsericestatus::where('ceo_nominee2_status', 1)->where('ceo_nominee2_status', 0)->count();
                $ceoApproval = Nvsericestatus::where('ceo_nominee2_status', 1)->where('ceo_status', 0)->count();
                $dpnv = NVService::where("user_id", $user->id)->where('dept_id', '!=', 'Null')->count() + NVMaterial::where("user_id", $user->id)->where('dept_id', '!=', 'Null')->count();
                $dpbpinv = NVService::where("user_id", $user->id)->where('dept_id', 1)->count() + NVMaterial::where("user_id", $user->id)->where('dept_id', 1)->count();
                $dpceocellnv = NVService::where("user_id", $user->id)->where('dept_id', 2)->count() + NVMaterial::where("user_id", $user->id)->where('dept_id', 2)->count();
                $dpregnv = NVService::where("user_id", $user->id)->where('dept_id', 3)->count() + NVMaterial::where("user_id", $user->id)->where('dept_id', 3)->count();
                $dpomnv = NVService::where("user_id", $user->id)->where('dept_id', 4)->count() + NVMaterial::where("user_id", $user->id)->where('dept_id', 4)->count();
                $dpinfonv = NVService::where("user_id", $user->id)->where('dept_id', 5)->count() + NVMaterial::where("user_id", $user->id)->where('dept_id', 5)->count();
                $dpsafenv = NVService::where("user_id", $user->id)->where('dept_id', 7)->count() + NVMaterial::where("user_id", $user->id)->where('dept_id', 7)->count();
                $dpdsmnv = NVService::where("user_id", $user->id)->where('dept_id', 10)->count() + NVMaterial::where("user_id", $user->id)->where('dept_id', 10)->count();
                $dpbetnv = NVService::where("user_id", $user->id)->where('dept_id', 8)->count() + NVMaterial::where("user_id", $user->id)->where('dept_id', 8)->count();
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
                $nv_sm_data = Nvsericestatus::whereIn('nv_id', $nv_ids)->with(['service', 'material', 'user'])
                ->where('cto_status',1)
                ->orderBy('id', 'desc');
                if ($ceo_status) {
                    $nv_sm_data->where('ceo_status', $ceo_status);
                }
                $nv_sm_data = $nv_sm_data->get();
                return view("admin.dashboard", compact("nv_sm_data", "totalNV", "approvedNV", "rejectedNV", "pendingNV", "revertedNV", "hodApproval", "cpmgApproval", "btApproval", "ceoApproval", "dpnv", "dpbpinv", "dpceocellnv", "dpregnv", "dpomnv", "dpinfonv", "dpsafenv", "dpdsmnv", "dpbetnv"));
            } elseif ($id3->work_rew3 == $user->id) {
                $Values = [$user->id, $id3->work_rew1, $id3->work_rew2];
                $totalNV = NeedValidation::where("user_id", $user->id)->count();
                $totalId = NeedValidation::where("user_id", $user->id)->pluck('id');
                // dd($totalId);
                $latestData = Nvsericestatus::whereIn("nv_id", $totalId)->get();
                $approvedNV = $latestData->where('ceo_status', 1)->count();
                $rejectedNV = $latestData->filter(function ($data) {
                    return in_array($data->ceo_status, [2]) || $data->hod_status == 2 || $data->cpmg_status == 2 || $data->ces_status == 2 || $data->cto_status == 2 || $data->ceo_nominee_status == 2 || $data->ceo_nominee2_status == 2 ;
                })->count();
                // $pendingNV = $latestData->filter(function ($data) {
                // return in_array($data->hod_status, [0, 1]) &&
                // in_array($data->cpmg_status, [0, 1]) &&
                // in_array($data->ces_status, [0, 1]) &&
                // in_array($data->cto_status, [0, 1]) &&
                // in_array($data->ceo_nominee_status, [0, 1]) &&
                // in_array($data->ceo_status, [0, 1]);
                // })->count();
                $pendingNV = Nvsericestatus::whereIn("nv_id", $totalId)->where('rv1_status', 0)->count();
                // $pendingNV=$pendingNV-$approvedNV-$rejectedNV;
                $totalNV = Nvsericestatus::whereIn("nv_id", $totalId)->where('rv1_status', 1)->count();
                $revertedNV = $rejectedNV;
                $hodApproval = Nvsericestatus::where("hod_status", 0)->count();
                $cpmgApproval = Nvsericestatus::where('cto_status', 1)->where('cpmg_status', 0)->count();
                $btApproval = Nvsericestatus::where("hod_status", 1)->where('cto_status', 0)->count();
                // $ceonominee1Approval =  Nvsericestatus::where('cpmg_status', 1)->where('ceo_nominee_status', 0)->count();
                // $ceonominee2Approval=Nvsericestatus::where('ceo_nominee_status', 1)->where('ceo_nominee2_status', 0)->count();
                // $ceonominee2A2pproval=Nvsericestatus::where('ceo_nominee2_status', 1)->where('ceo_nominee2_status', 0)->count();
                $ceoApproval = Nvsericestatus::where('ceo_nominee2_status', 1)->where('ceo_status', 0)->count();
                $dpnv = NVService::where("user_id", $user->id)->where('dept_id', '!=', 'Null')->count() + NVMaterial::where("user_id", $user->id)->where('dept_id', '!=', 'Null')->count();
                $dpbpinv = NVService::where("user_id", $user->id)->where('dept_id', 1)->count() + NVMaterial::where("user_id", $user->id)->where('dept_id', 1)->count();
                $dpceocellnv = NVService::where("user_id", $user->id)->where('dept_id', 2)->count() + NVMaterial::where("user_id", $user->id)->where('dept_id', 2)->count();
                $dpregnv = NVService::where("user_id", $user->id)->where('dept_id', 3)->count() + NVMaterial::where("user_id", $user->id)->where('dept_id', 3)->count();
                $dpomnv = NVService::where("user_id", $user->id)->where('dept_id', 4)->count() + NVMaterial::where("user_id", $user->id)->where('dept_id', 4)->count();
                $dpinfonv = NVService::where("user_id", $user->id)->where('dept_id', 5)->count() + NVMaterial::where("user_id", $user->id)->where('dept_id', 5)->count();
                $dpsafenv = NVService::where("user_id", $user->id)->where('dept_id', 7)->count() + NVMaterial::where("user_id", $user->id)->where('dept_id', 7)->count();
                $dpdsmnv = NVService::where("user_id", $user->id)->where('dept_id', 10)->count() + NVMaterial::where("user_id", $user->id)->where('dept_id', 10)->count();
                $dpbetnv = NVService::where("user_id", $user->id)->where('dept_id', 8)->count() + NVMaterial::where("user_id", $user->id)->where('dept_id', 8)->count();
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
                $nv_sm_data = Nvsericestatus::whereIn('nv_id', $nv_ids)->with(['service', 'material', 'user'])
                ->where('cto_status',1)
                ->orderBy('id', 'desc');
                if ($ceo_status) {
                    $nv_sm_data->where('ceo_status', $ceo_status);
                }
                $nv_sm_data = $nv_sm_data->get();
                return view("admin.dashboard", compact("nv_sm_data", "totalNV", "approvedNV", "rejectedNV", "pendingNV", "revertedNV", "hodApproval", "cpmgApproval", "btApproval", "ceoApproval", "dpnv", "dpbpinv", "dpceocellnv", "dpregnv", "dpomnv", "dpinfonv", "dpsafenv", "dpdsmnv", "dpbetnv"));
            } elseif ($id3->work_rew4 == $user->id) {
                $Values = [$user->id, $id3->work_rew1, $id3->work_rew2, $id3->work_rew3];
                $totalNV = NeedValidation::where("user_id", $user->id)->count();
                $totalId = NeedValidation::where("user_id", $user->id)->pluck('id');
                // dd($totalId);
                $latestData = Nvsericestatus::whereIn("nv_id", $totalId)->get();
                $approvedNV = $latestData->where('ceo_status', 1)->count();
                $rejectedNV = $latestData->filter(function ($data) {
                    return in_array($data->ceo_status, [2]) || $data->hod_status == 2 || $data->cpmg_status == 2 || $data->ces_status == 2 || $data->cto_status == 2 || $data->ceo_nominee_status == 2 || $data->ceo_nominee2_status == 2;
                })->count();
                // $pendingNV = $latestData->filter(function ($data) {
                // return in_array($data->hod_status, [0, 1]) &&
                // in_array($data->cpmg_status, [0, 1]) &&
                // in_array($data->ces_status, [0, 1]) &&
                // in_array($data->cto_status, [0, 1]) &&
                // in_array($data->ceo_nominee_status, [0, 1]) &&
                // in_array($data->ceo_status, [0, 1]);
                // })->count();
                $pendingNV = Nvsericestatus::whereIn("nv_id", $totalId)->where('rv1_status', 0)->count();
                // $pendingNV=$pendingNV-$approvedNV-$rejectedNV;
                $totalNV = Nvsericestatus::whereIn("nv_id", $totalId)->where('rv1_status', 1)->count();
                $revertedNV = $rejectedNV;
                $hodApproval = Nvsericestatus::where("hod_status", 0)->count();
                $cpmgApproval = Nvsericestatus::where('cto_status', 1)->where('cpmg_status', 0)->count();
                $btApproval = Nvsericestatus::where("hod_status", 1)->where('cto_status', 0)->count();
                // $ceonominee1Approval =  Nvsericestatus::where('cpmg_status', 1)->where('ceo_nominee_status', 0)->count();
                // $ceonominee2Approval=Nvsericestatus::where('ceo_nominee_status', 1)->where('ceo_nominee2_status', 0)->count();
                // $ceonominee2A2pproval=Nvsericestatus::where('ceo_nominee2_status', 1)->where('ceo_nominee2_status', 0)->count();
                $ceoApproval = Nvsericestatus::where('ceo_nominee2_status', 1)->where('ceo_status', 0)->count();
                $dpnv = NVService::where("user_id", $user->id)->where('dept_id', '!=', 'Null')->count() + NVMaterial::where("user_id", $user->id)->where('dept_id', '!=', 'Null')->count();
                $dpbpinv = NVService::where("user_id", $user->id)->where('dept_id', 1)->count() + NVMaterial::where("user_id", $user->id)->where('dept_id', 1)->count();
                $dpceocellnv = NVService::where("user_id", $user->id)->where('dept_id', 2)->count() + NVMaterial::where("user_id", $user->id)->where('dept_id', 2)->count();
                $dpregnv = NVService::where("user_id", $user->id)->where('dept_id', 3)->count() + NVMaterial::where("user_id", $user->id)->where('dept_id', 3)->count();
                $dpomnv = NVService::where("user_id", $user->id)->where('dept_id', 4)->count() + NVMaterial::where("user_id", $user->id)->where('dept_id', 4)->count();
                $dpinfonv = NVService::where("user_id", $user->id)->where('dept_id', 5)->count() + NVMaterial::where("user_id", $user->id)->where('dept_id', 5)->count();
                $dpsafenv = NVService::where("user_id", $user->id)->where('dept_id', 7)->count() + NVMaterial::where("user_id", $user->id)->where('dept_id', 7)->count();
                $dpdsmnv = NVService::where("user_id", $user->id)->where('dept_id', 10)->count() + NVMaterial::where("user_id", $user->id)->where('dept_id', 10)->count();
                $dpbetnv = NVService::where("user_id", $user->id)->where('dept_id', 8)->count() + NVMaterial::where("user_id", $user->id)->where('dept_id', 8)->count();
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
                $nv_sm_data = Nvsericestatus::whereIn('nv_id', $nv_ids)->with(['service', 'material', 'user'])
                ->where('cto_status',1)
                ->orderBy('id', 'desc');
                if ($ceo_status) {
                    $nv_sm_data->where('ceo_status', $ceo_status);
                }
                $nv_sm_data = $nv_sm_data->get();
                return view("admin.dashboard", compact("nv_sm_data", "totalNV", "approvedNV", "rejectedNV", "pendingNV", "revertedNV", "hodApproval", "cpmgApproval", "btApproval", "ceoApproval", "dpnv", "dpbpinv", "dpceocellnv", "dpregnv", "dpomnv", "dpinfonv", "dpsafenv", "dpdsmnv", "dpbetnv"));
            } elseif ($id3->approver == $user->id) {
                $Values = [$user->id, $id3->work_rew1, $id3->work_rew2, $id3->work_rew3, $id3->work_rew4];
                $totalNV = NeedValidation::where("user_id", $user->id)->count();
                $totalId = NeedValidation::where("user_id", $user->id)->pluck('id');
                // dd($totalId);
                $latestData = Nvsericestatus::whereIn("nv_id", $totalId)->get();
                $approvedNV = $latestData->where('ceo_status', 1)->count();
                $rejectedNV = $latestData->filter(function ($data) {
                    return in_array($data->ceo_status, [2]) || $data->hod_status == 2 || $data->cpmg_status == 2 ||
                    // $data->ces_status == 2 ||
                    $data->cto_status == 2 || $data->ceo_nominee_status == 2 || $data->ceo_nominee2_status == 2;
                    // $data->ceo_nominee2_status == 2 ;
                    
                })->count();
                // $pendingNV = $latestData->filter(function ($data) {
                // return in_array($data->hod_status, [0, 1]) &&
                // in_array($data->cpmg_status, [0, 1]) &&
                // in_array($data->ces_status, [0, 1]) &&
                // in_array($data->cto_status, [0, 1]) &&
                // in_array($data->ceo_nominee_status, [0, 1]) &&
                // in_array($data->ceo_status, [0, 1]);
                // })->count();
                $pendingNV = Nvsericestatus::whereIn("nv_id", $totalId)->where('rv1_status', 0)->count();
                // $pendingNV=$pendingNV-$approvedNV-$rejectedNV;
                $totalNV = Nvsericestatus::whereIn("nv_id", $totalId)->where('rv1_status', 1)->count();
                $revertedNV = $rejectedNV;
                $hodApproval = Nvsericestatus::where("hod_status", 0)->count();
                $cpmgApproval = Nvsericestatus::where('cto_status', 1)->where('cpmg_status', 0)->count();
                $btApproval = Nvsericestatus::where("hod_status", 1)->where('cto_status', 0)->count();
                // $ceonominee1Approval =  Nvsericestatus::where('cpmg_status', 1)->where('ceo_nominee_status', 0)->count();
                // $ceonominee2Approval=Nvsericestatus::where('ceo_nominee_status', 1)->where('ceo_nominee2_status', 0)->count();
                // $ceonominee2A2pproval=Nvsericestatus::where('ceo_nominee2_status', 1)->where('ceo_nominee2_status', 0)->count();
                $ceoApproval = '';
                // Nvsericestatus::where('ceo_nominee2_status', 1)->where('ceo_status', 0)->count();
                $dpnv = NVService::where("user_id", $user->id)->where('dept_id', '!=', 'Null')->count() + NVMaterial::where("user_id", $user->id)->where('dept_id', '!=', 'Null')->count();
                $dpbpinv = NVService::where("user_id", $user->id)->where('dept_id', 1)->count() + NVMaterial::where("user_id", $user->id)->where('dept_id', 1)->count();
                $dpceocellnv = NVService::where("user_id", $user->id)->where('dept_id', 2)->count() + NVMaterial::where("user_id", $user->id)->where('dept_id', 2)->count();
                $dpregnv = NVService::where("user_id", $user->id)->where('dept_id', 3)->count() + NVMaterial::where("user_id", $user->id)->where('dept_id', 3)->count();
                $dpomnv = NVService::where("user_id", $user->id)->where('dept_id', 4)->count() + NVMaterial::where("user_id", $user->id)->where('dept_id', 4)->count();
                $dpinfonv = NVService::where("user_id", $user->id)->where('dept_id', 5)->count() + NVMaterial::where("user_id", $user->id)->where('dept_id', 5)->count();
                $dpsafenv = NVService::where("user_id", $user->id)->where('dept_id', 7)->count() + NVMaterial::where("user_id", $user->id)->where('dept_id', 7)->count();
                $dpdsmnv = NVService::where("user_id", $user->id)->where('dept_id', 10)->count() + NVMaterial::where("user_id", $user->id)->where('dept_id', 10)->count();
                $dpbetnv = NVService::where("user_id", $user->id)->where('dept_id', 8)->count() + NVMaterial::where("user_id", $user->id)->where('dept_id', 8)->count();
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
                $nv_sm_data = Nvsericestatus::whereIn('nv_id', $nv_ids)->with(['service', 'material', 'user'])
                ->where('cto_status',1)
                ->orderBy('id', 'desc');
                if ($ceo_status) {
                    $nv_sm_data->where('ceo_status', $ceo_status);
                }
                $nv_sm_data = $nv_sm_data->get();
                return view("admin.dashboard", compact("nv_sm_data", "totalNV", "approvedNV", "rejectedNV", "pendingNV", "revertedNV", "hodApproval", "cpmgApproval", "btApproval", "ceoApproval", "dpnv", "dpbpinv", "dpceocellnv", "dpregnv", "dpomnv", "dpinfonv", "dpsafenv", "dpdsmnv", "dpbetnv"));
            } elseif ($id4->work_rew1 == $user->id) {
                $totalNV = NeedValidation::where("user_id", $user->id)->count();
                $totalId = NeedValidation::where("user_id", $user->id)->pluck('id');
                // dd($totalId);
                $latestData = Nvsericestatus::whereIn("nv_id", $totalId)->get();
                $approvedNV = $latestData->where('ceo_status', 1)->count();
                $rejectedNV = $latestData->filter(function ($data) {
                    return in_array($data->ceo_status, [2]) || $data->hod_status == 2 || $data->cpmg_status == 2 || $data->ces_status == 2 || $data->cto_status == 2 || $data->ceo_nominee_status == 2 || $data->ceo_nominee2_status == 2 ;
                })->count();
                // $pendingNV = $latestData->filter(function ($data) {
                // return in_array($data->hod_status, [0, 1]) &&
                // in_array($data->cpmg_status, [0, 1]) &&
                // in_array($data->ces_status, [0, 1]) &&
                // in_array($data->cto_status, [0, 1]) &&
                // in_array($data->ceo_nominee_status, [0, 1]) &&
                // in_array($data->ceo_status, [0, 1]);
                // })->count();
                $pendingNV = Nvsericestatus::whereIn("nv_id", $totalId)->where('rv1_status', 0)->count();
                // $pendingNV=$pendingNV-$approvedNV-$rejectedNV;
                $totalNV = Nvsericestatus::whereIn("nv_id", $totalId)->where('rv1_status', 1)->count();
                $revertedNV = $rejectedNV;
                $hodApproval = Nvsericestatus::where("hod_status", 0)->count();
                $cpmgApproval = Nvsericestatus::where('cto_status', 1)->where('cpmg_status', 0)->count();
                $btApproval = Nvsericestatus::where("hod_status", 1)->where('cto_status', 0)->count();
                // $ceonominee1Approval =  Nvsericestatus::where('cpmg_status', 1)->where('ceo_nominee_status', 0)->count();
                // $ceonominee2Approval=Nvsericestatus::where('ceo_nominee_status', 1)->where('ceo_nominee2_status', 0)->count();
                // $ceonominee2A2pproval=Nvsericestatus::where('ceo_nominee2_status', 1)->where('ceo_nominee2_status', 0)->count();
                $ceoApproval = Nvsericestatus::where('ceo_nominee2_status', 1)->where('ceo_status', 0)->count();
                $dpnv = NVService::where("user_id", $user->id)->where('dept_id', '!=', 'Null')->count() + NVMaterial::where("user_id", $user->id)->where('dept_id', '!=', 'Null')->count();
                $dpbpinv = NVService::where("user_id", $user->id)->where('dept_id', 1)->count() + NVMaterial::where("user_id", $user->id)->where('dept_id', 1)->count();
                $dpceocellnv = NVService::where("user_id", $user->id)->where('dept_id', 2)->count() + NVMaterial::where("user_id", $user->id)->where('dept_id', 2)->count();
                $dpregnv = NVService::where("user_id", $user->id)->where('dept_id', 3)->count() + NVMaterial::where("user_id", $user->id)->where('dept_id', 3)->count();
                $dpomnv = NVService::where("user_id", $user->id)->where('dept_id', 4)->count() + NVMaterial::where("user_id", $user->id)->where('dept_id', 4)->count();
                $dpinfonv = NVService::where("user_id", $user->id)->where('dept_id', 5)->count() + NVMaterial::where("user_id", $user->id)->where('dept_id', 5)->count();
                $dpsafenv = NVService::where("user_id", $user->id)->where('dept_id', 7)->count() + NVMaterial::where("user_id", $user->id)->where('dept_id', 7)->count();
                $dpdsmnv = NVService::where("user_id", $user->id)->where('dept_id', 10)->count() + NVMaterial::where("user_id", $user->id)->where('dept_id', 10)->count();
                $dpbetnv = NVService::where("user_id", $user->id)->where('dept_id', 8)->count() + NVMaterial::where("user_id", $user->id)->where('dept_id', 8)->count();
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
                $nv_sm_data = Nvsericestatus::whereIn('nv_id', $nv_ids)->with(['service', 'material', 'user'])
                ->where('ceo_nominee_status',1)
                ->orderBy('id', 'desc');
                if ($ceo_status) {
                    $nv_sm_data->where('ceo_status', $ceo_status);
                }
                $nv_sm_data = $nv_sm_data->get();
                return view("admin.dashboard", compact("nv_sm_data", "totalNV", "approvedNV", "rejectedNV", "pendingNV", "revertedNV", "hodApproval", "cpmgApproval", "btApproval", "ceoApproval", "dpnv", "dpbpinv", "dpceocellnv", "dpregnv", "dpomnv", "dpinfonv", "dpsafenv", "dpdsmnv", "dpbetnv"));
            } elseif ($id4->work_rew2 == $user->id) {
                $Values = [$user->id, $id4->work_rew1];
                $totalNV = NeedValidation::where("user_id", $user->id)->count();
                $totalId = NeedValidation::where("user_id", $user->id)->pluck('id');
                // dd($totalId);
                $latestData = Nvsericestatus::whereIn("nv_id", $totalId)->get();
                $approvedNV = $latestData->where('ceo_status', 1)->count();
                $rejectedNV = $latestData->filter(function ($data) {
                    return in_array($data->ceo_status, [2]) || $data->hod_status == 2 || $data->cpmg_status == 2 || $data->ces_status == 2 || $data->cto_status == 2 || $data->ceo_nominee_status == 2 || $data->ceo_nominee2_status == 2 ;
                })->count();
                // $pendingNV = $latestData->filter(function ($data) {
                // return in_array($data->hod_status, [0, 1]) &&
                // in_array($data->cpmg_status, [0, 1]) &&
                // in_array($data->ces_status, [0, 1]) &&
                // in_array($data->cto_status, [0, 1]) &&
                // in_array($data->ceo_nominee_status, [0, 1]) &&
                // in_array($data->ceo_status, [0, 1]);
                // })->count();
                $pendingNV = Nvsericestatus::whereIn("nv_id", $totalId)->where('rv1_status', 0)->count();
                // $pendingNV=$pendingNV-$approvedNV-$rejectedNV;
                $totalNV = Nvsericestatus::whereIn("nv_id", $totalId)->where('rv1_status', 1)->count();
                $revertedNV = $rejectedNV;
                $hodApproval = Nvsericestatus::where("hod_status", 0)->count();
                $cpmgApproval = Nvsericestatus::where('cto_status', 1)->where('cpmg_status', 0)->count();
                $btApproval = Nvsericestatus::where("hod_status", 1)->where('cto_status', 0)->count();
                // $ceonominee1Approval =  Nvsericestatus::where('cpmg_status', 1)->where('ceo_nominee_status', 0)->count();
                // $ceonominee2Approval=Nvsericestatus::where('ceo_nominee_status', 1)->where('ceo_nominee2_status', 0)->count();
                // $ceonominee2A2pproval=Nvsericestatus::where('ceo_nominee2_status', 1)->where('ceo_nominee2_status', 0)->count();
                $ceoApproval = Nvsericestatus::where('ceo_nominee2_status', 1)->where('ceo_status', 0)->count();
                $dpnv = NVService::where("user_id", $user->id)->where('dept_id', '!=', 'Null')->count() + NVMaterial::where("user_id", $user->id)->where('dept_id', '!=', 'Null')->count();
                $dpbpinv = NVService::where("user_id", $user->id)->where('dept_id', 1)->count() + NVMaterial::where("user_id", $user->id)->where('dept_id', 1)->count();
                $dpceocellnv = NVService::where("user_id", $user->id)->where('dept_id', 2)->count() + NVMaterial::where("user_id", $user->id)->where('dept_id', 2)->count();
                $dpregnv = NVService::where("user_id", $user->id)->where('dept_id', 3)->count() + NVMaterial::where("user_id", $user->id)->where('dept_id', 3)->count();
                $dpomnv = NVService::where("user_id", $user->id)->where('dept_id', 4)->count() + NVMaterial::where("user_id", $user->id)->where('dept_id', 4)->count();
                $dpinfonv = NVService::where("user_id", $user->id)->where('dept_id', 5)->count() + NVMaterial::where("user_id", $user->id)->where('dept_id', 5)->count();
                $dpsafenv = NVService::where("user_id", $user->id)->where('dept_id', 7)->count() + NVMaterial::where("user_id", $user->id)->where('dept_id', 7)->count();
                $dpdsmnv = NVService::where("user_id", $user->id)->where('dept_id', 10)->count() + NVMaterial::where("user_id", $user->id)->where('dept_id', 10)->count();
                $dpbetnv = NVService::where("user_id", $user->id)->where('dept_id', 8)->count() + NVMaterial::where("user_id", $user->id)->where('dept_id', 8)->count();
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
                $nv_sm_data = Nvsericestatus::whereIn('nv_id', $nv_ids)->with(['service', 'material', 'user'])
                ->where('ceo_nominee_status',1)
                ->orderBy('id', 'desc');
                if ($ceo_status) {
                    $nv_sm_data->where('ceo_status', $ceo_status);
                }
                $nv_sm_data = $nv_sm_data->get();
                return view("admin.dashboard", compact("nv_sm_data", "totalNV", "approvedNV", "rejectedNV", "pendingNV", "revertedNV", "hodApproval", "cpmgApproval", "btApproval", "ceoApproval", "dpnv", "dpbpinv", "dpceocellnv", "dpregnv", "dpomnv", "dpinfonv", "dpsafenv", "dpdsmnv", "dpbetnv"));
            } elseif ($id4->work_rew3 == $user->id) {
                $Values = [$user->id, $id4->work_rew1, $id4->work_rew2];
                $totalNV = NeedValidation::where("user_id", $user->id)->count();
                $totalId = NeedValidation::where("user_id", $user->id)->pluck('id');
                // dd($totalId);
                $latestData = Nvsericestatus::whereIn("nv_id", $totalId)->get();
                $approvedNV = $latestData->where('ceo_status', 1)->count();
                $rejectedNV = $latestData->filter(function ($data) {
                    return in_array($data->ceo_status, [2]) || $data->hod_status == 2 || $data->cpmg_status == 2 || $data->ces_status == 2 || $data->cto_status == 2 || $data->ceo_nominee_status == 2 || $data->ceo_nominee2_status == 2 ;
                })->count();
                // $pendingNV = $latestData->filter(function ($data) {
                // return in_array($data->hod_status, [0, 1]) &&
                // in_array($data->cpmg_status, [0, 1]) &&
                // in_array($data->ces_status, [0, 1]) &&
                // in_array($data->cto_status, [0, 1]) &&
                // in_array($data->ceo_nominee_status, [0, 1]) &&
                // in_array($data->ceo_status, [0, 1]);
                // })->count();
                $pendingNV = Nvsericestatus::whereIn("nv_id", $totalId)->where('rv1_status', 0)->count();
                // $pendingNV=$pendingNV-$approvedNV-$rejectedNV;
                $totalNV = Nvsericestatus::whereIn("nv_id", $totalId)->where('rv1_status', 1)->count();
                $revertedNV = $rejectedNV;
                $hodApproval = Nvsericestatus::where("hod_status", 0)->count();
                $cpmgApproval = Nvsericestatus::where('cto_status', 1)->where('cpmg_status', 0)->count();
                $btApproval = Nvsericestatus::where("hod_status", 1)->where('cto_status', 0)->count();
                // $ceonominee1Approval =  Nvsericestatus::where('cpmg_status', 1)->where('ceo_nominee_status', 0)->count();
                // $ceonominee2Approval=Nvsericestatus::where('ceo_nominee_status', 1)->where('ceo_nominee2_status', 0)->count();
                // $ceonominee2A2pproval=Nvsericestatus::where('ceo_nominee2_status', 1)->where('ceo_nominee2_status', 0)->count();
                $ceoApproval = Nvsericestatus::where('ceo_nominee2_status', 1)->where('ceo_status', 0)->count();
                $dpnv = NVService::where("user_id", $user->id)->where('dept_id', '!=', 'Null')->count() + NVMaterial::where("user_id", $user->id)->where('dept_id', '!=', 'Null')->count();
                $dpbpinv = NVService::where("user_id", $user->id)->where('dept_id', 1)->count() + NVMaterial::where("user_id", $user->id)->where('dept_id', 1)->count();
                $dpceocellnv = NVService::where("user_id", $user->id)->where('dept_id', 2)->count() + NVMaterial::where("user_id", $user->id)->where('dept_id', 2)->count();
                $dpregnv = NVService::where("user_id", $user->id)->where('dept_id', 3)->count() + NVMaterial::where("user_id", $user->id)->where('dept_id', 3)->count();
                $dpomnv = NVService::where("user_id", $user->id)->where('dept_id', 4)->count() + NVMaterial::where("user_id", $user->id)->where('dept_id', 4)->count();
                $dpinfonv = NVService::where("user_id", $user->id)->where('dept_id', 5)->count() + NVMaterial::where("user_id", $user->id)->where('dept_id', 5)->count();
                $dpsafenv = NVService::where("user_id", $user->id)->where('dept_id', 7)->count() + NVMaterial::where("user_id", $user->id)->where('dept_id', 7)->count();
                $dpdsmnv = NVService::where("user_id", $user->id)->where('dept_id', 10)->count() + NVMaterial::where("user_id", $user->id)->where('dept_id', 10)->count();
                $dpbetnv = NVService::where("user_id", $user->id)->where('dept_id', 8)->count() + NVMaterial::where("user_id", $user->id)->where('dept_id', 8)->count();
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
                $nv_sm_data = Nvsericestatus::whereIn('nv_id', $nv_ids)->with(['service', 'material', 'user'])
                ->where('ceo_nominee_status',1)
                ->orderBy('id', 'desc');
                if ($ceo_status) {
                    $nv_sm_data->where('ceo_status', $ceo_status);
                }
                $nv_sm_data = $nv_sm_data->get();
                return view("admin.dashboard", compact("nv_sm_data", "totalNV", "approvedNV", "rejectedNV", "pendingNV", "revertedNV", "hodApproval", "cpmgApproval", "btApproval", "ceoApproval", "dpnv", "dpbpinv", "dpceocellnv", "dpregnv", "dpomnv", "dpinfonv", "dpsafenv", "dpdsmnv", "dpbetnv"));
            } elseif ($id4->work_rew4 == $user->id) {
                $Values = [$user->id, $id4->work_rew1, $id4->work_rew2, $id4->work_rew3];
                $totalNV = NeedValidation::where("user_id", $user->id)->count();
                $totalId = NeedValidation::where("user_id", $user->id)->pluck('id');
                // dd($totalId);
                $latestData = Nvsericestatus::whereIn("nv_id", $totalId)->get();
                $approvedNV = $latestData->where('ceo_status', 1)->count();
                $rejectedNV = $latestData->filter(function ($data) {
                    return in_array($data->ceo_status, [2]) || $data->hod_status == 2 || $data->cpmg_status == 2 || $data->ces_status == 2 || $data->cto_status == 2 || $data->ceo_nominee_status == 2 || $data->ceo_nominee2_status == 2;
                })->count();
                // $pendingNV = $latestData->filter(function ($data) {
                // return in_array($data->hod_status, [0, 1]) &&
                // in_array($data->cpmg_status, [0, 1]) &&
                // in_array($data->ces_status, [0, 1]) &&
                // in_array($data->cto_status, [0, 1]) &&
                // in_array($data->ceo_nominee_status, [0, 1]) &&
                // in_array($data->ceo_status, [0, 1]);
                // })->count();
                $pendingNV = Nvsericestatus::whereIn("nv_id", $totalId)->where('rv1_status', 0)->count();
                // $pendingNV=$pendingNV-$approvedNV-$rejectedNV;
                $totalNV = Nvsericestatus::whereIn("nv_id", $totalId)->where('rv1_status', 1)->count();
                $revertedNV = $rejectedNV;
                $hodApproval = Nvsericestatus::where("hod_status", 0)->count();
                $cpmgApproval = Nvsericestatus::where('cto_status', 1)->where('cpmg_status', 0)->count();
                $btApproval = Nvsericestatus::where("hod_status", 1)->where('cto_status', 0)->count();
                // $ceonominee1Approval =  Nvsericestatus::where('cpmg_status', 1)->where('ceo_nominee_status', 0)->count();
                // $ceonominee2Approval=Nvsericestatus::where('ceo_nominee_status', 1)->where('ceo_nominee2_status', 0)->count();
                // $ceonominee2A2pproval=Nvsericestatus::where('ceo_nominee2_status', 1)->where('ceo_nominee2_status', 0)->count();
                $ceoApproval = Nvsericestatus::where('ceo_nominee2_status', 1)->where('ceo_status', 0)->count();
                $dpnv = NVService::where("user_id", $user->id)->where('dept_id', '!=', 'Null')->count() + NVMaterial::where("user_id", $user->id)->where('dept_id', '!=', 'Null')->count();
                $dpbpinv = NVService::where("user_id", $user->id)->where('dept_id', 1)->count() + NVMaterial::where("user_id", $user->id)->where('dept_id', 1)->count();
                $dpceocellnv = NVService::where("user_id", $user->id)->where('dept_id', 2)->count() + NVMaterial::where("user_id", $user->id)->where('dept_id', 2)->count();
                $dpregnv = NVService::where("user_id", $user->id)->where('dept_id', 3)->count() + NVMaterial::where("user_id", $user->id)->where('dept_id', 3)->count();
                $dpomnv = NVService::where("user_id", $user->id)->where('dept_id', 4)->count() + NVMaterial::where("user_id", $user->id)->where('dept_id', 4)->count();
                $dpinfonv = NVService::where("user_id", $user->id)->where('dept_id', 5)->count() + NVMaterial::where("user_id", $user->id)->where('dept_id', 5)->count();
                $dpsafenv = NVService::where("user_id", $user->id)->where('dept_id', 7)->count() + NVMaterial::where("user_id", $user->id)->where('dept_id', 7)->count();
                $dpdsmnv = NVService::where("user_id", $user->id)->where('dept_id', 10)->count() + NVMaterial::where("user_id", $user->id)->where('dept_id', 10)->count();
                $dpbetnv = NVService::where("user_id", $user->id)->where('dept_id', 8)->count() + NVMaterial::where("user_id", $user->id)->where('dept_id', 8)->count();
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
                $nv_sm_data = Nvsericestatus::whereIn('nv_id', $nv_ids)->with(['service', 'material', 'user'])
                ->where('ceo_nominee_status',1)
                ->orderBy('id', 'desc');
                if ($ceo_status) {
                    $nv_sm_data->where('ceo_status', $ceo_status);
                }
                $nv_sm_data = $nv_sm_data->get();
                return view("admin.dashboard", compact("nv_sm_data", "totalNV", "approvedNV", "rejectedNV", "pendingNV", "revertedNV", "hodApproval", "cpmgApproval", "btApproval", "ceoApproval", "dpnv", "dpbpinv", "dpceocellnv", "dpregnv", "dpomnv", "dpinfonv", "dpsafenv", "dpdsmnv", "dpbetnv"));
            } elseif ($id4->approver == $user->id) {
                $Values = [$user->id, $id4->work_rew1, $id4->work_rew2, $id4->work_rew3, $id4->work_rew4];
                $totalNV = NeedValidation::where("user_id", $user->id)->count();
                $totalId = NeedValidation::where("user_id", $user->id)->pluck('id');
                // dd($totalId);
                $latestData = Nvsericestatus::whereIn("nv_id", $totalId)->get();
                $approvedNV = $latestData->where('ceo_status', 1)->count();
                $rejectedNV = $latestData->filter(function ($data) {
                    return in_array($data->ceo_status, [2]) || $data->hod_status == 2 || $data->cpmg_status == 2 ||
                    // $data->ces_status == 2 ||
                    $data->cto_status == 2 || $data->ceo_nominee_status == 2 || $data->ceo_nominee2_status == 2;
                    // $data->ceo_nominee2_status == 2 ;
                    
                })->count();
                // $pendingNV = $latestData->filter(function ($data) {
                // return in_array($data->hod_status, [0, 1]) &&
                // in_array($data->cpmg_status, [0, 1]) &&
                // in_array($data->ces_status, [0, 1]) &&
                // in_array($data->cto_status, [0, 1]) &&
                // in_array($data->ceo_nominee_status, [0, 1]) &&
                // in_array($data->ceo_status, [0, 1]);
                // })->count();
                $pendingNV = Nvsericestatus::whereIn("nv_id", $totalId)->where('rv1_status', 0)->count();
                // $pendingNV=$pendingNV-$approvedNV-$rejectedNV;
                $totalNV = Nvsericestatus::whereIn("nv_id", $totalId)->where('rv1_status', 1)->count();
                $revertedNV = $rejectedNV;
                $hodApproval = Nvsericestatus::where("hod_status", 0)->count();
                $cpmgApproval = Nvsericestatus::where('cto_status', 1)->where('cpmg_status', 0)->count();
                $btApproval = Nvsericestatus::where("hod_status", 1)->where('cto_status', 0)->count();
                // $ceonominee1Approval =  Nvsericestatus::where('cpmg_status', 1)->where('ceo_nominee_status', 0)->count();
                // $ceonominee2Approval=Nvsericestatus::where('ceo_nominee_status', 1)->where('ceo_nominee2_status', 0)->count();
                // $ceonominee2A2pproval=Nvsericestatus::where('ceo_nominee2_status', 1)->where('ceo_nominee2_status', 0)->count();
                $ceoApproval = '';
                // Nvsericestatus::where('ceo_nominee2_status', 1)->where('ceo_status', 0)->count();
                $dpnv = NVService::where("user_id", $user->id)->where('dept_id', '!=', 'Null')->count() + NVMaterial::where("user_id", $user->id)->where('dept_id', '!=', 'Null')->count();
                $dpbpinv = NVService::where("user_id", $user->id)->where('dept_id', 1)->count() + NVMaterial::where("user_id", $user->id)->where('dept_id', 1)->count();
                $dpceocellnv = NVService::where("user_id", $user->id)->where('dept_id', 2)->count() + NVMaterial::where("user_id", $user->id)->where('dept_id', 2)->count();
                $dpregnv = NVService::where("user_id", $user->id)->where('dept_id', 3)->count() + NVMaterial::where("user_id", $user->id)->where('dept_id', 3)->count();
                $dpomnv = NVService::where("user_id", $user->id)->where('dept_id', 4)->count() + NVMaterial::where("user_id", $user->id)->where('dept_id', 4)->count();
                $dpinfonv = NVService::where("user_id", $user->id)->where('dept_id', 5)->count() + NVMaterial::where("user_id", $user->id)->where('dept_id', 5)->count();
                $dpsafenv = NVService::where("user_id", $user->id)->where('dept_id', 7)->count() + NVMaterial::where("user_id", $user->id)->where('dept_id', 7)->count();
                $dpdsmnv = NVService::where("user_id", $user->id)->where('dept_id', 10)->count() + NVMaterial::where("user_id", $user->id)->where('dept_id', 10)->count();
                $dpbetnv = NVService::where("user_id", $user->id)->where('dept_id', 8)->count() + NVMaterial::where("user_id", $user->id)->where('dept_id', 8)->count();
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
                $nv_sm_data = Nvsericestatus::whereIn('nv_id', $nv_ids)->with(['service', 'material', 'user'])
                ->where('ceo_nominee_status',1)
                ->orderBy('id', 'desc');
                if ($ceo_status) {
                    $nv_sm_data->where('ceo_status', $ceo_status);
                }
                $nv_sm_data = $nv_sm_data->get();
                return view("admin.dashboard", compact("nv_sm_data", "totalNV", "approvedNV", "rejectedNV", "pendingNV", "revertedNV", "hodApproval", "cpmgApproval", "btApproval", "ceoApproval", "dpnv", "dpbpinv", "dpceocellnv", "dpregnv", "dpomnv", "dpinfonv", "dpsafenv", "dpdsmnv", "dpbetnv"));
            } elseif ($id5->work_rew1 == $user->id) {
                $totalNV = NeedValidation::where("user_id", $user->id)->count();
                $totalId = NeedValidation::where("user_id", $user->id)->pluck('id');
                // dd($totalId);
                $latestData = Nvsericestatus::whereIn("nv_id", $totalId)->get();
                $approvedNV = $latestData->where('ceo_status', 1)->count();
                $rejectedNV = $latestData->filter(function ($data) {
                    return in_array($data->ceo_status, [2]) || $data->hod_status == 2 || $data->cpmg_status == 2 || $data->ces_status == 2 || $data->cto_status == 2 || $data->ceo_nominee_status == 2 || $data->ceo_nominee2_status == 2 ;
                })->count();
                // $pendingNV = $latestData->filter(function ($data) {
                // return in_array($data->hod_status, [0, 1]) &&
                // in_array($data->cpmg_status, [0, 1]) &&
                // in_array($data->ces_status, [0, 1]) &&
                // in_array($data->cto_status, [0, 1]) &&
                // in_array($data->ceo_nominee_status, [0, 1]) &&
                // in_array($data->ceo_status, [0, 1]);
                // })->count();
                $pendingNV = Nvsericestatus::whereIn("nv_id", $totalId)->where('rv1_status', 0)->count();
                // $pendingNV=$pendingNV-$approvedNV-$rejectedNV;
                $totalNV = Nvsericestatus::whereIn("nv_id", $totalId)->where('rv1_status', 1)->count();
                $revertedNV = $rejectedNV;
                $hodApproval = Nvsericestatus::where("hod_status", 0)->count();
                $cpmgApproval = Nvsericestatus::where('cto_status', 1)->where('cpmg_status', 0)->count();
                $btApproval = Nvsericestatus::where("hod_status", 1)->where('cto_status', 0)->count();
                // $ceonominee1Approval =  Nvsericestatus::where('cpmg_status', 1)->where('ceo_nominee_status', 0)->count();
                // $ceonominee2Approval=Nvsericestatus::where('ceo_nominee_status', 1)->where('ceo_nominee2_status', 0)->count();
                // $ceonominee2A2pproval=Nvsericestatus::where('ceo_nominee2_status', 1)->where('ceo_nominee2_status', 0)->count();
                $ceoApproval = Nvsericestatus::where('ceo_nominee2_status', 1)->where('ceo_status', 0)->count();
                $dpnv = NVService::where("user_id", $user->id)->where('dept_id', '!=', 'Null')->count() + NVMaterial::where("user_id", $user->id)->where('dept_id', '!=', 'Null')->count();
                $dpbpinv = NVService::where("user_id", $user->id)->where('dept_id', 1)->count() + NVMaterial::where("user_id", $user->id)->where('dept_id', 1)->count();
                $dpceocellnv = NVService::where("user_id", $user->id)->where('dept_id', 2)->count() + NVMaterial::where("user_id", $user->id)->where('dept_id', 2)->count();
                $dpregnv = NVService::where("user_id", $user->id)->where('dept_id', 3)->count() + NVMaterial::where("user_id", $user->id)->where('dept_id', 3)->count();
                $dpomnv = NVService::where("user_id", $user->id)->where('dept_id', 4)->count() + NVMaterial::where("user_id", $user->id)->where('dept_id', 4)->count();
                $dpinfonv = NVService::where("user_id", $user->id)->where('dept_id', 5)->count() + NVMaterial::where("user_id", $user->id)->where('dept_id', 5)->count();
                $dpsafenv = NVService::where("user_id", $user->id)->where('dept_id', 7)->count() + NVMaterial::where("user_id", $user->id)->where('dept_id', 7)->count();
                $dpdsmnv = NVService::where("user_id", $user->id)->where('dept_id', 10)->count() + NVMaterial::where("user_id", $user->id)->where('dept_id', 10)->count();
                $dpbetnv = NVService::where("user_id", $user->id)->where('dept_id', 8)->count() + NVMaterial::where("user_id", $user->id)->where('dept_id', 8)->count();
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
                $nv_sm_data = Nvsericestatus::whereIn('nv_id', $nv_ids)->with(['service', 'material', 'user'])
                ->where('ceo_nominee2_status',1)
                ->orderBy('id', 'desc');
                if ($ceo_status) {
                    $nv_sm_data->where('ceo_status', $ceo_status);
                }
                $nv_sm_data = $nv_sm_data->get();
                return view("admin.dashboard", compact("nv_sm_data", "totalNV", "approvedNV", "rejectedNV", "pendingNV", "revertedNV", "hodApproval", "cpmgApproval", "btApproval", "ceoApproval", "dpnv", "dpbpinv", "dpceocellnv", "dpregnv", "dpomnv", "dpinfonv", "dpsafenv", "dpdsmnv", "dpbetnv"));
            } elseif ($id5->work_rew2 == $user->id) {
                $Values = [$user->id, $id5->work_rew1];
                $totalNV = NeedValidation::where("user_id", $user->id)->count();
                $totalId = NeedValidation::where("user_id", $user->id)->pluck('id');
                // dd($totalId);
                $latestData = Nvsericestatus::whereIn("nv_id", $totalId)->get();
                $approvedNV = $latestData->where('ceo_status', 1)->count();
                $rejectedNV = $latestData->filter(function ($data) {
                    return in_array($data->ceo_status, [2]) || $data->hod_status == 2 || $data->cpmg_status == 2 || $data->ces_status == 2 || $data->cto_status == 2 || $data->ceo_nominee_status == 2 || $data->ceo_nominee2_status == 2 ;
                })->count();
                // $pendingNV = $latestData->filter(function ($data) {
                // return in_array($data->hod_status, [0, 1]) &&
                // in_array($data->cpmg_status, [0, 1]) &&
                // in_array($data->ces_status, [0, 1]) &&
                // in_array($data->cto_status, [0, 1]) &&
                // in_array($data->ceo_nominee_status, [0, 1]) &&
                // in_array($data->ceo_status, [0, 1]);
                // })->count();
                $pendingNV = Nvsericestatus::whereIn("nv_id", $totalId)->where('rv1_status', 0)->count();
                // $pendingNV=$pendingNV-$approvedNV-$rejectedNV;
                $totalNV = Nvsericestatus::whereIn("nv_id", $totalId)->where('rv1_status', 1)->count();
                $revertedNV = $rejectedNV;
                $hodApproval = Nvsericestatus::where("hod_status", 0)->count();
                $cpmgApproval = Nvsericestatus::where('cto_status', 1)->where('cpmg_status', 0)->count();
                $btApproval = Nvsericestatus::where("hod_status", 1)->where('cto_status', 0)->count();
                // $ceonominee1Approval =  Nvsericestatus::where('cpmg_status', 1)->where('ceo_nominee_status', 0)->count();
                // $ceonominee2Approval=Nvsericestatus::where('ceo_nominee_status', 1)->where('ceo_nominee2_status', 0)->count();
                // $ceonominee2A2pproval=Nvsericestatus::where('ceo_nominee2_status', 1)->where('ceo_nominee2_status', 0)->count();
                $ceoApproval = Nvsericestatus::where('ceo_nominee2_status', 1)->where('ceo_status', 0)->count();
                $dpnv = NVService::where("user_id", $user->id)->where('dept_id', '!=', 'Null')->count() + NVMaterial::where("user_id", $user->id)->where('dept_id', '!=', 'Null')->count();
                $dpbpinv = NVService::where("user_id", $user->id)->where('dept_id', 1)->count() + NVMaterial::where("user_id", $user->id)->where('dept_id', 1)->count();
                $dpceocellnv = NVService::where("user_id", $user->id)->where('dept_id', 2)->count() + NVMaterial::where("user_id", $user->id)->where('dept_id', 2)->count();
                $dpregnv = NVService::where("user_id", $user->id)->where('dept_id', 3)->count() + NVMaterial::where("user_id", $user->id)->where('dept_id', 3)->count();
                $dpomnv = NVService::where("user_id", $user->id)->where('dept_id', 4)->count() + NVMaterial::where("user_id", $user->id)->where('dept_id', 4)->count();
                $dpinfonv = NVService::where("user_id", $user->id)->where('dept_id', 5)->count() + NVMaterial::where("user_id", $user->id)->where('dept_id', 5)->count();
                $dpsafenv = NVService::where("user_id", $user->id)->where('dept_id', 7)->count() + NVMaterial::where("user_id", $user->id)->where('dept_id', 7)->count();
                $dpdsmnv = NVService::where("user_id", $user->id)->where('dept_id', 10)->count() + NVMaterial::where("user_id", $user->id)->where('dept_id', 10)->count();
                $dpbetnv = NVService::where("user_id", $user->id)->where('dept_id', 8)->count() + NVMaterial::where("user_id", $user->id)->where('dept_id', 8)->count();
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
                $nv_sm_data = Nvsericestatus::whereIn('nv_id', $nv_ids)->with(['service', 'material', 'user'])
                ->where('ceo_nominee2_status',1)
                ->orderBy('id', 'desc');
                if ($ceo_status) {
                    $nv_sm_data->where('ceo_status', $ceo_status);
                }
                $nv_sm_data = $nv_sm_data->get();
                return view("admin.dashboard", compact("nv_sm_data", "totalNV", "approvedNV", "rejectedNV", "pendingNV", "revertedNV", "hodApproval", "cpmgApproval", "btApproval", "ceoApproval", "dpnv", "dpbpinv", "dpceocellnv", "dpregnv", "dpomnv", "dpinfonv", "dpsafenv", "dpdsmnv", "dpbetnv"));
            } elseif ($id5->work_rew3 == $user->id) {
                $Values = [$user->id, $id5->work_rew1, $id5->work_rew2];
                $totalNV = NeedValidation::where("user_id", $user->id)->count();
                $totalId = NeedValidation::where("user_id", $user->id)->pluck('id');
                // dd($totalId);
                $latestData = Nvsericestatus::whereIn("nv_id", $totalId)->get();
                $approvedNV = $latestData->where('ceo_status', 1)->count();
                $rejectedNV = $latestData->filter(function ($data) {
                    return in_array($data->ceo_status, [2]) || $data->hod_status == 2 || $data->cpmg_status == 2 || $data->ces_status == 2 || $data->cto_status == 2 || $data->ceo_nominee_status == 2 || $data->ceo_nominee2_status == 2 ;
                })->count();
                // $pendingNV = $latestData->filter(function ($data) {
                // return in_array($data->hod_status, [0, 1]) &&
                // in_array($data->cpmg_status, [0, 1]) &&
                // in_array($data->ces_status, [0, 1]) &&
                // in_array($data->cto_status, [0, 1]) &&
                // in_array($data->ceo_nominee_status, [0, 1]) &&
                // in_array($data->ceo_status, [0, 1]);
                // })->count();
                $pendingNV = Nvsericestatus::whereIn("nv_id", $totalId)->where('rv1_status', 0)->count();
                // $pendingNV=$pendingNV-$approvedNV-$rejectedNV;
                $totalNV = Nvsericestatus::whereIn("nv_id", $totalId)->where('rv1_status', 1)->count();
                $revertedNV = $rejectedNV;
                $hodApproval = Nvsericestatus::where("hod_status", 0)->count();
                $cpmgApproval = Nvsericestatus::where('cto_status', 1)->where('cpmg_status', 0)->count();
                $btApproval = Nvsericestatus::where("hod_status", 1)->where('cto_status', 0)->count();
                // $ceonominee1Approval =  Nvsericestatus::where('cpmg_status', 1)->where('ceo_nominee_status', 0)->count();
                // $ceonominee2Approval=Nvsericestatus::where('ceo_nominee_status', 1)->where('ceo_nominee2_status', 0)->count();
                // $ceonominee2A2pproval=Nvsericestatus::where('ceo_nominee2_status', 1)->where('ceo_nominee2_status', 0)->count();
                $ceoApproval = Nvsericestatus::where('ceo_nominee2_status', 1)->where('ceo_status', 0)->count();
                $dpnv = NVService::where("user_id", $user->id)->where('dept_id', '!=', 'Null')->count() + NVMaterial::where("user_id", $user->id)->where('dept_id', '!=', 'Null')->count();
                $dpbpinv = NVService::where("user_id", $user->id)->where('dept_id', 1)->count() + NVMaterial::where("user_id", $user->id)->where('dept_id', 1)->count();
                $dpceocellnv = NVService::where("user_id", $user->id)->where('dept_id', 2)->count() + NVMaterial::where("user_id", $user->id)->where('dept_id', 2)->count();
                $dpregnv = NVService::where("user_id", $user->id)->where('dept_id', 3)->count() + NVMaterial::where("user_id", $user->id)->where('dept_id', 3)->count();
                $dpomnv = NVService::where("user_id", $user->id)->where('dept_id', 4)->count() + NVMaterial::where("user_id", $user->id)->where('dept_id', 4)->count();
                $dpinfonv = NVService::where("user_id", $user->id)->where('dept_id', 5)->count() + NVMaterial::where("user_id", $user->id)->where('dept_id', 5)->count();
                $dpsafenv = NVService::where("user_id", $user->id)->where('dept_id', 7)->count() + NVMaterial::where("user_id", $user->id)->where('dept_id', 7)->count();
                $dpdsmnv = NVService::where("user_id", $user->id)->where('dept_id', 10)->count() + NVMaterial::where("user_id", $user->id)->where('dept_id', 10)->count();
                $dpbetnv = NVService::where("user_id", $user->id)->where('dept_id', 8)->count() + NVMaterial::where("user_id", $user->id)->where('dept_id', 8)->count();
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
                $nv_sm_data = Nvsericestatus::whereIn('nv_id', $nv_ids)->with(['service', 'material', 'user'])
                ->where('ceo_nominee2_status',1)
                ->orderBy('id', 'desc');
                if ($ceo_status) {
                    $nv_sm_data->where('ceo_status', $ceo_status);
                }
                $nv_sm_data = $nv_sm_data->get();

                $BRPLnv = NeedValidation::where('company_id','6')->pluck("id");
                $fileDataBRPL =Nvsericestatus::
                select(
                    DB::raw('MONTH(created_at) as month'),
                    DB::raw('SUM(CASE WHEN ceo_status = "1" THEN 1 ELSE 0 END) as approved_count'),
                    DB::raw('SUM(CASE WHEN ( ceo_status = "2" OR hod_status == "2" OR cpmg_status == "2" OR  cto_status == "2" OR ceo_nominee_status == "2" OR ceo_nominee2_status == 2)  THEN 1 ELSE 0 END ) as rejected_count'),
                    DB::raw('SUM(CASE WHEN (rv1_status = "0" ) THEN 1 ELSE 0 END) as pending_count'),
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
            // dd( $BYPLnv );
             $fileDataBYPL =Nvsericestatus::
            select(
                DB::raw('MONTH(created_at) as month'),
                DB::raw('SUM(CASE WHEN ceo_status = "1" THEN 1 ELSE 0 END) as approved_count'),
                DB::raw('SUM(CASE WHEN ( ceo_status = "2" OR hod_status == "2" OR cpmg_status == "2" OR  cto_status == "2" OR ceo_nominee_status == "2" OR ceo_nominee2_status == 2)  THEN 1 ELSE 0 END) as rejected_count'),
                DB::raw('SUM(CASE WHEN (rv1_status = "0" )  THEN 1 ELSE 0 END) as pending_count'),
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
                return view("admin.dashboard", compact("nv_sm_data", "totalNV", "approvedNV", "rejectedNV", "pendingNV", "revertedNV", "hodApproval", "cpmgApproval", "btApproval", "ceoApproval", "dpnv", "dpbpinv", "dpceocellnv", "dpregnv", "dpomnv", "dpinfonv", "dpsafenv", "dpdsmnv", "dpbetnv"));
            } elseif ($id5->work_rew4 == $user->id) {
                $Values = [$user->id, $id5->work_rew1, $id5->work_rew2, $id5->work_rew3];
                $totalNV = NeedValidation::where("user_id", $user->id)->count();
                $totalId = NeedValidation::where("user_id", $user->id)->pluck('id');
                // dd($totalId);
                $latestData = Nvsericestatus::whereIn("nv_id", $totalId)->get();
                $approvedNV = $latestData->where('ceo_status', 1)->count();
                $rejectedNV = $latestData->filter(function ($data) {
                    return in_array($data->ceo_status, [2]) || $data->hod_status == 2 || $data->cpmg_status == 2 || $data->ces_status == 2 || $data->cto_status == 2 || $data->ceo_nominee_status == 2 || $data->ceo_nominee2_status == 2;
                })->count();
                // $pendingNV = $latestData->filter(function ($data) {
                // return in_array($data->hod_status, [0, 1]) &&
                // in_array($data->cpmg_status, [0, 1]) &&
                // in_array($data->ces_status, [0, 1]) &&
                // in_array($data->cto_status, [0, 1]) &&
                // in_array($data->ceo_nominee_status, [0, 1]) &&
                // in_array($data->ceo_status, [0, 1]);
                // })->count();
                $pendingNV = Nvsericestatus::whereIn("nv_id", $totalId)->where('rv1_status', 0)->count();
                // $pendingNV=$pendingNV-$approvedNV-$rejectedNV;
                $totalNV = Nvsericestatus::whereIn("nv_id", $totalId)->where('rv1_status', 1)->count();
                $revertedNV = $rejectedNV;
                $hodApproval = Nvsericestatus::where("hod_status", 0)->count();
                $cpmgApproval = Nvsericestatus::where('cto_status', 1)->where('cpmg_status', 0)->count();
                $btApproval = Nvsericestatus::where("hod_status", 1)->where('cto_status', 0)->count();
                // $ceonominee1Approval =  Nvsericestatus::where('cpmg_status', 1)->where('ceo_nominee_status', 0)->count();
                // $ceonominee2Approval=Nvsericestatus::where('ceo_nominee_status', 1)->where('ceo_nominee2_status', 0)->count();
                // $ceonominee2A2pproval=Nvsericestatus::where('ceo_nominee2_status', 1)->where('ceo_nominee2_status', 0)->count();
                $ceoApproval = Nvsericestatus::where('ceo_nominee2_status', 1)->where('ceo_status', 0)->count();
                $dpnv = NVService::where("user_id", $user->id)->where('dept_id', '!=', 'Null')->count() + NVMaterial::where("user_id", $user->id)->where('dept_id', '!=', 'Null')->count();
                $dpbpinv = NVService::where("user_id", $user->id)->where('dept_id', 1)->count() + NVMaterial::where("user_id", $user->id)->where('dept_id', 1)->count();
                $dpceocellnv = NVService::where("user_id", $user->id)->where('dept_id', 2)->count() + NVMaterial::where("user_id", $user->id)->where('dept_id', 2)->count();
                $dpregnv = NVService::where("user_id", $user->id)->where('dept_id', 3)->count() + NVMaterial::where("user_id", $user->id)->where('dept_id', 3)->count();
                $dpomnv = NVService::where("user_id", $user->id)->where('dept_id', 4)->count() + NVMaterial::where("user_id", $user->id)->where('dept_id', 4)->count();
                $dpinfonv = NVService::where("user_id", $user->id)->where('dept_id', 5)->count() + NVMaterial::where("user_id", $user->id)->where('dept_id', 5)->count();
                $dpsafenv = NVService::where("user_id", $user->id)->where('dept_id', 7)->count() + NVMaterial::where("user_id", $user->id)->where('dept_id', 7)->count();
                $dpdsmnv = NVService::where("user_id", $user->id)->where('dept_id', 10)->count() + NVMaterial::where("user_id", $user->id)->where('dept_id', 10)->count();
                $dpbetnv = NVService::where("user_id", $user->id)->where('dept_id', 8)->count() + NVMaterial::where("user_id", $user->id)->where('dept_id', 8)->count();
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
                $nv_sm_data = Nvsericestatus::whereIn('nv_id', $nv_ids)->with(['service', 'material', 'user'])
                ->where('ceo_nominee2_status',1)
                ->orderBy('id', 'desc');
                if ($ceo_status) {
                    $nv_sm_data->where('ceo_status', $ceo_status);
                }
                $nv_sm_data = $nv_sm_data->get();
                $BRPLnv = NeedValidation::where('company_id','6')->pluck("id");
                $fileDataBRPL =Nvsericestatus::
                select(
                    DB::raw('MONTH(created_at) as month'),
                    DB::raw('SUM(CASE WHEN ceo_status = "1" THEN 1 ELSE 0 END) as approved_count'),
                    DB::raw('SUM(CASE WHEN ( ceo_status = "2" OR hod_status == "2" OR cpmg_status == "2" OR  cto_status == "2" OR ceo_nominee_status == "2" OR ceo_nominee2_status == 2)  THEN 1 ELSE 0 END ) as rejected_count'),
                    DB::raw('SUM(CASE WHEN (rv1_status = "0" ) THEN 1 ELSE 0 END) as pending_count'),
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
            // dd( $BYPLnv );
             $fileDataBYPL =Nvsericestatus::
            select(
                DB::raw('MONTH(created_at) as month'),
                DB::raw('SUM(CASE WHEN ceo_status = "1" THEN 1 ELSE 0 END) as approved_count'),
                DB::raw('SUM(CASE WHEN ( ceo_status = "2" OR hod_status == "2" OR cpmg_status == "2" OR  cto_status == "2" OR ceo_nominee_status == "2" OR ceo_nominee2_status == 2)  THEN 1 ELSE 0 END) as rejected_count'),
                DB::raw('SUM(CASE WHEN (rv1_status = "0" )  THEN 1 ELSE 0 END) as pending_count'),
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
                return view("admin.dashboard", compact("nv_sm_data", "totalNV", "approvedNV", "rejectedNV", "pendingNV", "revertedNV", "hodApproval", "cpmgApproval", "btApproval", "ceoApproval", "dpnv", "dpbpinv", "dpceocellnv", "dpregnv", "dpomnv", "dpinfonv", "dpsafenv", "dpdsmnv", "dpbetnv",'BRPLlabels','BRPLapprovedData','BRPLrejectedData','BRPLpendingData','BYPLlabels','BYPLapprovedData','BYPLrejectedData','BYPLpendingData'));
            } elseif ($id5->approver == $user->id) {
                $Values = [$user->id, $id5->work_rew1, $id5->work_rew2, $id5->work_rew3, $id5->work_rew4];
              $totalId = NeedValidation::pluck('id');
                // dd($totalId);
                $totalNV = Nvsericestatus::whereIn("nv_id", $totalId)->where('approverdep5_status', 1)->count();
                $rejectedNV = Nvsericestatus::whereIn("nv_id", $totalId)->where('approverdep5_status', 2)->count();
                // dd($totalId);
                $latestData = Nvsericestatus::whereIn("nv_id", $totalId)->get();
                $approvedNV = $latestData->where('ceo_status', 1)->count();
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
                    in_array($data->approverdep5_status, [0]);
                    
                  
                   
                   
                    // in_array($data->ces_status, [0]) &&
                    // in_array($data->cto_status, [0]) &&
                    // in_array($data->ceo_nominee_status, [0]) &&
                    // in_array($data->ceo_status, [0]);
                    })->count();
                $revertedNV = $rejectedNV;
                $hodApproval = Nvsericestatus::where("hod_status", 0)->count();
                $cpmgApproval = Nvsericestatus::where('cto_status', 1)->where('cpmg_status', 0)->count();
                $btApproval = Nvsericestatus::where("hod_status", 1)->where('cto_status', 0)->count();
                // $ceonominee1Approval =  Nvsericestatus::where('cpmg_status', 1)->where('ceo_nominee_status', 0)->count();
                // $ceonominee2Approval=Nvsericestatus::where('ceo_nominee_status', 1)->where('ceo_nominee2_status', 0)->count();
                // $ceonominee2A2pproval=Nvsericestatus::where('ceo_nominee2_status', 1)->where('ceo_nominee2_status', 0)->count();
                $ceoApproval = '';
                // Nvsericestatus::where('ceo_nominee2_status', 1)->where('ceo_status', 0)->count();
                $dpnv = NVService::where("user_id", $user->id)->where('dept_id', '!=', 'Null')->count() + NVMaterial::where("user_id", $user->id)->where('dept_id', '!=', 'Null')->count();
                $dpbpinv = NVService::where("user_id", $user->id)->where('dept_id', 1)->count() + NVMaterial::where("user_id", $user->id)->where('dept_id', 1)->count();
                $dpceocellnv = NVService::where("user_id", $user->id)->where('dept_id', 2)->count() + NVMaterial::where("user_id", $user->id)->where('dept_id', 2)->count();
                $dpregnv = NVService::where("user_id", $user->id)->where('dept_id', 3)->count() + NVMaterial::where("user_id", $user->id)->where('dept_id', 3)->count();
                $dpomnv = NVService::where("user_id", $user->id)->where('dept_id', 4)->count() + NVMaterial::where("user_id", $user->id)->where('dept_id', 4)->count();
                $dpinfonv = NVService::where("user_id", $user->id)->where('dept_id', 5)->count() + NVMaterial::where("user_id", $user->id)->where('dept_id', 5)->count();
                $dpsafenv = NVService::where("user_id", $user->id)->where('dept_id', 7)->count() + NVMaterial::where("user_id", $user->id)->where('dept_id', 7)->count();
                $dpdsmnv = NVService::where("user_id", $user->id)->where('dept_id', 10)->count() + NVMaterial::where("user_id", $user->id)->where('dept_id', 10)->count();
                $dpbetnv = NVService::where("user_id", $user->id)->where('dept_id', 8)->count() + NVMaterial::where("user_id", $user->id)->where('dept_id', 8)->count();
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
                $nv_sm_data = Nvsericestatus::whereIn('nv_id', $nv_ids)->with(['service', 'material', 'user'])
                ->where('ceo_nominee2_status',1)
                ->orderBy('id', 'desc');
                if ($ceo_status) {
                    $nv_sm_data->where('ceo_status', $ceo_status);
                }
                $nv_sm_data = $nv_sm_data->get();
                $BRPLnv = NeedValidation::where('company_id','6')->pluck("id");
                $fileDataBRPL =Nvsericestatus::
                select(
                    DB::raw('MONTH(created_at) as month'),
                    DB::raw('SUM(CASE WHEN ceo_status = "1" THEN 1 ELSE 0 END) as approved_count'),
                    DB::raw('SUM(CASE WHEN ( ceo_status = "2")  THEN 1 ELSE 0 END ) as rejected_count'),
                    DB::raw('SUM(CASE WHEN (ceo_nominee2_status = "1" AND work_rew1dep5_status = "1" AND ceo_status = "0" ) THEN 1 ELSE 0 END) as pending_count'),
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
            // dd( $BYPLnv );
             $fileDataBYPL =Nvsericestatus::
            select(
                DB::raw('MONTH(created_at) as month'),
                DB::raw('SUM(CASE WHEN ceo_status = "1" THEN 1 ELSE 0 END) as approved_count'),
                DB::raw('SUM(CASE WHEN (ceo_status = "2")  THEN 1 ELSE 0 END) as rejected_count'),
                DB::raw('SUM(CASE WHEN ( ceo_nominee2_status = "1" AND work_rew1dep5_status = "1" AND ceo_status = "0") THEN 1 ELSE 0 END) as pending_count'),
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
                return view("admin.dashboard", compact("nv_sm_data", "totalNV", "approvedNV", "rejectedNV", "pendingNV", "revertedNV", "hodApproval", "cpmgApproval", "btApproval", "ceoApproval", "dpnv", "dpbpinv", "dpceocellnv", "dpregnv", "dpomnv", "dpinfonv", "dpsafenv", "dpdsmnv", "dpbetnv",'BRPLlabels','BRPLapprovedData','BRPLrejectedData','BRPLpendingData','BYPLlabels','BYPLapprovedData','BYPLrejectedData','BYPLpendingData'));
            } 
             else {
                // $totalNV = NeedValidation::where("user_id", $user->id)->count();
                $totalId = NeedValidation::where("user_id", $user->id)->pluck('id');
                $latestData = Nvsericestatus::whereIn("nv_id", $totalId)->get();
                $approvedNV = $latestData->where('ceo_status', 1)->count();
                $rejectedNV = $latestData->filter(function ($data) {
                    return in_array($data->ceo_status, [2]) || $data->hod_status == 2 || $data->cpmg_status == 2 || $data->ces_status == 2 || $data->cto_status == 2 || $data->ceo_nominee_status == 2 || $data->ceo_nominee2_status == 2;
                })->count();
                $pendingNV = $latestData->filter(function ($data) {
                    return in_array($data->rv1_status, [1]) 
                    || in_array($data->rv2_status, [0, 1]) 
                    || in_array($data->rv3_status, [0, 1]) 
                    || in_array($data->rv4_status, [0, 1]) 
                    || in_array($data->hod_status, [0, 1]) 
                    && in_array($data->cpmg_status, [0, 1]) 
                    && in_array($data->ces_status, [0, 1]) 
                    && in_array($data->cto_status, [0, 1]) 
                    && in_array($data->ceo_nominee_status, [0, 1]) 
                    && in_array($data->ceo_status, [0]);
                })->count();
                // $pendingNV=$pendingNV-$approvedNV-$rejectedNV ;
                $totalNV = Nvsericestatus::whereIn("nv_id", $totalId)->where('ceo_status', 1)->count();
                $revertedNV = $rejectedNV;
                $hodApproval = Nvsericestatus::where("hod_status", 0)->count();
                $cpmgApproval = Nvsericestatus::where('cto_status', 1)->where('cpmg_status', 0)->count();
                $btApproval = Nvsericestatus::where("hod_status", 1)->where('cto_status', 0)->count();
                $ceonominee1Approval = Nvsericestatus::where('cpmg_status', 1)->where('ceo_nominee_status', 0)->count();
                $ceonominee2Approval = Nvsericestatus::where('ceo_nominee_status', 1)->where('ceo_nominee2_status', 0)->count();
                // $ceonominee2A2pproval=Nvsericestatus::where('ceo_nominee2_status', 1)->where('ceo_nominee2_status', 0)->count();
                $ceoApproval = Nvsericestatus::where('ceo_nominee2_status', 1)->where('ceo_status', 0)->count();
                $dpnv = NVService::where("user_id", $user->id)->where('dept_id', '!=', 'Null')->count() + NVMaterial::where("user_id", $user->id)->where('dept_id', '!=', 'Null')->count();
                $dpbpinv = NVService::where("user_id", $user->id)->where('dept_id', 1)->count() + NVMaterial::where("user_id", $user->id)->where('dept_id', 1)->count();
                $dpceocellnv = NVService::where("user_id", $user->id)->where('dept_id', 2)->count() + NVMaterial::where("user_id", $user->id)->where('dept_id', 2)->count();
                $dpregnv = NVService::where("user_id", $user->id)->where('dept_id', 3)->count() + NVMaterial::where("user_id", $user->id)->where('dept_id', 3)->count();
                $dpomnv = NVService::where("user_id", $user->id)->where('dept_id', 4)->count() + NVMaterial::where("user_id", $user->id)->where('dept_id', 4)->count();
                $dpinfonv = NVService::where("user_id", $user->id)->where('dept_id', 5)->count() + NVMaterial::where("user_id", $user->id)->where('dept_id', 5)->count();
                $dpsafenv = NVService::where("user_id", $user->id)->where('dept_id', 7)->count() + NVMaterial::where("user_id", $user->id)->where('dept_id', 7)->count();
                $dpdsmnv = NVService::where("user_id", $user->id)->where('dept_id', 10)->count() + NVMaterial::where("user_id", $user->id)->where('dept_id', 10)->count();
                $dpbetnv = NVService::where("user_id", $user->id)->where('dept_id', 8)->count() + NVMaterial::where("user_id", $user->id)->where('dept_id', 8)->count();
                $nvIds = NeedValidation::whereIn("user_id", $user_id)->pluck("id");
                $nv = NeedValidation::whereHas('service')->select('id')->where('user_id', $user->id)->get();
                $nv_ids = $nv->pluck('id');
                $nv_sm_data = Nvsericestatus::whereIn('nv_id', $nv_ids)->with(['service', 'material', 'user'])->orderBy('id', 'desc');
                if ($ceo_status) {
                    $nv_sm_data->where('ceo_status', $ceo_status);
                }
                $nv_sm_data = $nv_sm_data->get();

                $BRPLnv = NeedValidation::where('company_id','6')->where('user_id', $user->id)->pluck("id");
                $fileDataBRPL =Nvsericestatus::
                select(
                    DB::raw('MONTH(created_at) as month'),
                    DB::raw('SUM(CASE WHEN ceo_status = "1" THEN 1 ELSE 0 END) as approved_count'),
                    DB::raw('SUM(CASE WHEN ( hod_status = "2" OR cpmg_status = "2" OR cto_status = "2" OR ceo_nominee_status = "2" OR ceo_nominee2_status = "2" OR ceo_status = "2")  THEN 1 ELSE 0 END) as rejected_count'),
                    DB::raw('SUM(CASE WHEN ceo_status = "0" THEN 1 ELSE 0 END) as pending_count'),
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
    
            $BYPLnv = NeedValidation::where('company_id','5')->where('user_id', $user->id)->pluck("id");
            // dd( $BYPLnv );
             $fileDataBYPL =Nvsericestatus::
            select(
                DB::raw('MONTH(created_at) as month'),
                DB::raw('SUM(CASE WHEN ceo_status = "1" THEN 1 ELSE 0 END) as approved_count'),
                DB::raw('SUM(CASE WHEN ( hod_status = "2" OR cpmg_status = "2" OR cto_status = "2" OR ceo_nominee_status = "2" OR ceo_nominee2_status = "2" OR ceo_status = "2")  THEN 1 ELSE 0 END) as rejected_count'),
                DB::raw('SUM(CASE WHEN ceo_status = "0" THEN 1 ELSE 0 END) as pending_count'),
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
             // dd( $dataPointBYPL->rejected_count);
        }
        
                return view("admin.dashboard", compact("ceonominee2Approval", "ceonominee1Approval", "nv_sm_data", "totalNV", "approvedNV", "rejectedNV", "pendingNV", "revertedNV", "hodApproval", "cpmgApproval", "btApproval", "ceoApproval", "dpnv", "dpbpinv", "dpceocellnv", "dpregnv", "dpomnv", "dpinfonv", "dpsafenv", "dpdsmnv", "dpbetnv",'BRPLlabels','BRPLapprovedData','BRPLrejectedData','BRPLpendingData','BYPLlabels','BYPLapprovedData','BYPLrejectedData','BYPLpendingData'));
            }
            // dd($nv_sm_data) ;
            
        }
    }
}
